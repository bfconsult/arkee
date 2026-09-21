<?php

namespace App\Http\Controllers;

use App\Models\Component;
use App\Models\FurnitureScheduleLine;
use App\Models\Item;
use App\Models\Material;
use App\Models\Quote;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FurnitureScheduleLineController extends Controller
{
    public function index(Quote $quote)
    {
        $lines = $quote->furnitureScheduleLines()
            ->with(['item.components', 'componentFinishes'])
            ->get()
            ->each(fn (FurnitureScheduleLine $line) => $line->needs_finishes = $line->needsFinishes());

        return Inertia::render('ScheduleLines/Index', [
            'quote' => $quote->load('project'),
            'lines' => $lines,
        ]);
    }

    public function view(Quote $quote)
    {
        $lines = $quote->furnitureScheduleLines()
            ->with([
                'item.itemCategory',
                'item.packagingType',
                'item.supplier',
                'item.attachments',
                'item.components.material.supplier',
                'item.components.supplier',
                'componentFinishes.component',
                'componentFinishes.material',
                'componentFinishes.finish',
            ])
            ->get();

        $groups = $lines
            ->groupBy(fn (FurnitureScheduleLine $line) => $line->item->itemCategory->name ?? 'Uncategorised')
            ->sortKeys()
            ->map(fn ($groupLines, $category) => [
                'category' => $category,
                'lines' => $groupLines->values(),
            ])
            ->values();

        return Inertia::render('ScheduleLines/View', [
            'quote' => $quote->load('project'),
            'groups' => $groups,
        ]);
    }

    public function create(Quote $quote)
    {
        return Inertia::render('ScheduleLines/Form', [
            'quote' => $quote->load('project'),
            'line' => null,
            ...$this->options(),
        ]);
    }

    public function store(Request $request, Quote $quote)
    {
        $line = $quote->furnitureScheduleLines()->create($this->validated($request));
        $this->syncComponentFinishes($line, $request);

        return redirect()->route('quotes.schedule-lines.index', $quote)->with('success', 'Schedule line added.');
    }

    public function edit(Quote $quote, FurnitureScheduleLine $scheduleLine)
    {
        return Inertia::render('ScheduleLines/Form', [
            'quote' => $quote->load('project'),
            'line' => $scheduleLine->load('componentFinishes'),
            ...$this->options(),
        ]);
    }

    public function update(Request $request, Quote $quote, FurnitureScheduleLine $scheduleLine)
    {
        $scheduleLine->update($this->validated($request));
        $this->syncComponentFinishes($scheduleLine, $request);

        return redirect()->route('quotes.schedule-lines.index', $quote)->with('success', 'Schedule line updated.');
    }

    public function destroy(Quote $quote, FurnitureScheduleLine $scheduleLine)
    {
        $scheduleLine->delete();

        return redirect()->route('quotes.schedule-lines.index', $quote)->with('success', 'Schedule line deleted.');
    }

    private function options(): array
    {
        return [
            'items' => Item::with([
                'itemCategory',
                'components' => fn ($query) => $query->select(['id', 'item_id', 'name', 'is_fabric', 'material_id']),
                'components.material:id,name',
                'components.material.finishes:id,material_id,name',
            ])->orderBy('catalogue_no')->get(['id', 'catalogue_no', 'item_category_id']),
            // Only Fabric-flagged materials are offered for a Fabric
            // Component - a regular Component's Material is already fixed.
            'materials' => Material::where('is_fabric', true)
                ->with('finishes:id,material_id,name')
                ->orderBy('name')
                ->get(['id', 'name']),
        ];
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'item_id' => 'required|exists:items,id',
            'fabric_notes' => 'nullable|string',
            'quantity' => 'nullable|integer|min:0',
            'price_override' => 'nullable|numeric|min:0',
            'markup_target_pct' => 'nullable|numeric|min:0',
            'required_by' => 'nullable|date',
            'include_on_po' => 'boolean',
            'internal_cost_manual' => 'nullable|numeric|min:0',
        ]);

        // The quantity column is NOT NULL with a default of 1 - drop a blank
        // value instead of passing an explicit null, so the DB default applies.
        if (is_null($data['quantity'] ?? null)) {
            unset($data['quantity']);
        }

        return $data;
    }

    /**
     * Replace this line's Component Finish selections with the ones just
     * submitted - simplest correct approach given there are only ever a
     * handful of rows per line. For a Fabric component the submitted
     * Material is used (and must actually be Fabric-flagged); for a
     * regular component the Material is never user-chosen here - it's
     * always the component's own fixed material_id. A row is only kept
     * if a Material ends up resolved.
     */
    private function syncComponentFinishes(FurnitureScheduleLine $line, Request $request): void
    {
        $rows = $request->validate([
            'component_finishes' => 'array',
            'component_finishes.*.component_id' => 'required|exists:components,id',
            'component_finishes.*.material_id' => 'nullable|exists:materials,id',
            'component_finishes.*.finish_id' => 'nullable|exists:finishes,id',
        ])['component_finishes'] ?? [];

        $line->componentFinishes()->delete();

        if (empty($rows)) {
            return;
        }

        $components = Component::whereIn('id', collect($rows)->pluck('component_id'))->get()->keyBy('id');
        $materials = Material::whereIn('id', collect($rows)->pluck('material_id')->filter())->get()->keyBy('id');

        foreach ($rows as $row) {
            $component = $components->get($row['component_id']);

            if (! $component) {
                continue;
            }

            if ($component->is_fabric) {
                $material = $materials->get($row['material_id'] ?? null);
                $materialId = $material && $material->is_fabric ? $material->id : null;
            } else {
                $materialId = $component->material_id;
            }

            if (empty($materialId)) {
                continue;
            }

            $line->componentFinishes()->create([
                'component_id' => $component->id,
                'material_id' => $materialId,
                'finish_id' => $row['finish_id'] ?? null,
            ]);
        }
    }
}
