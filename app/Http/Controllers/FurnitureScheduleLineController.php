<?php

namespace App\Http\Controllers;

use App\Models\DeliveryLocation;
use App\Models\Finish;
use App\Models\FurnitureScheduleLine;
use App\Models\Item;
use App\Models\Project;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FurnitureScheduleLineController extends Controller
{
    public function index(Project $project)
    {
        return Inertia::render('ScheduleLines/Index', [
            'project' => $project,
            'lines' => $project->furnitureScheduleLines()->with(['item', 'parentLine'])->get(),
        ]);
    }

    public function create(Project $project)
    {
        return Inertia::render('ScheduleLines/Form', [
            'project' => $project,
            'line' => null,
            ...$this->options($project),
        ]);
    }

    public function store(Request $request, Project $project)
    {
        $project->furnitureScheduleLines()->create($this->validated($request));

        return redirect()->route('projects.schedule-lines.index', $project)->with('success', 'Schedule line added.');
    }

    public function edit(Project $project, FurnitureScheduleLine $scheduleLine)
    {
        return Inertia::render('ScheduleLines/Form', [
            'project' => $project,
            'line' => $scheduleLine,
            ...$this->options($project, $scheduleLine),
        ]);
    }

    public function update(Request $request, Project $project, FurnitureScheduleLine $scheduleLine)
    {
        $scheduleLine->update($this->validated($request));

        return redirect()->route('projects.schedule-lines.index', $project)->with('success', 'Schedule line updated.');
    }

    public function destroy(Project $project, FurnitureScheduleLine $scheduleLine)
    {
        $scheduleLine->delete();

        return redirect()->route('projects.schedule-lines.index', $project)->with('success', 'Schedule line deleted.');
    }

    private function options(Project $project, ?FurnitureScheduleLine $editing = null): array
    {
        return [
            'items' => Item::with('itemCategory')->orderBy('catalogue_no')->get(['id', 'catalogue_no', 'item_category_id']),
            'suppliers' => Supplier::orderBy('name')->get(['id', 'name']),
            'finishes' => Finish::orderBy('name')->get(['id', 'name']),
            'deliveryLocations' => DeliveryLocation::orderBy('name')->get(['id', 'name']),
            'parentLineOptions' => $project->furnitureScheduleLines()
                ->where('row_type', FurnitureScheduleLine::ROW_TYPE_PARENT)
                ->when($editing, fn ($q) => $q->whereKeyNot($editing->id))
                ->get(['id', 'quantity', 'item_id']),
        ];
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'item_id' => 'required|exists:items,id',
            'row_type' => 'required|in:parent,sub',
            'parent_line_id' => 'nullable|exists:furniture_schedule_lines,id',
            'fabric_supplier_id' => 'nullable|exists:suppliers,id',
            'fabric_notes' => 'nullable|string',
            'quantity' => 'nullable|integer|min:0',
            'fabric_price_pm' => 'nullable|numeric|min:0',
            'price_override' => 'nullable|numeric|min:0',
            'markup_target_pct' => 'nullable|numeric|min:0',
            'required_by' => 'nullable|date',
            'delivery_location_id' => 'nullable|exists:delivery_locations,id',
            'include_on_po' => 'boolean',
            'finish_id' => 'nullable|exists:finishes,id',
            'internal_cost_manual' => 'nullable|numeric|min:0',
        ]);

        // The quantity column is NOT NULL with a default of 1 - drop a blank
        // value instead of passing an explicit null, so the DB default applies.
        if (is_null($data['quantity'] ?? null)) {
            unset($data['quantity']);
        }

        return $data;
    }
}
