<?php

namespace App\Http\Controllers;

use App\Models\FurnitureScheduleLine;
use App\Models\Item;
use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;

class FurnitureScheduleLineController extends Controller
{
    public function index(Project $project)
    {
        return Inertia::render('ScheduleLines/Index', [
            'project' => $project,
            'lines' => $project->furnitureScheduleLines()->with('item')->get(),
        ]);
    }

    public function create(Project $project)
    {
        return Inertia::render('ScheduleLines/Form', [
            'project' => $project,
            'line' => null,
            ...$this->options(),
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
            ...$this->options(),
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

    private function options(): array
    {
        return [
            'items' => Item::with('itemCategory')->orderBy('catalogue_no')->get(['id', 'catalogue_no', 'item_category_id']),
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
}
