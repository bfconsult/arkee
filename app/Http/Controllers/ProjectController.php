<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Project;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function index()
    {
        return Inertia::render('Projects/Index', [
            'projects' => Project::with(['client', 'pmUser'])->withCount('quotes')->orderBy('project_descriptor')->get(),
        ]);
    }

    public function show(Project $project)
    {
        $project->load([
            'client',
            'pmUser',
            'quotes' => fn ($query) => $query->orderByDesc('date'),
            'quotes.furnitureScheduleLines.item.components',
            'quotes.furnitureScheduleLines.componentFinishes',
            'purchaseOrders.supplier',
        ]);

        $project->quotes->each(function (Quote $quote) {
            $quote->furnitureScheduleLines->each(
                fn ($line) => $line->needs_finishes = $line->needsFinishes()
            );
            $quote->needs_finishes = $quote->furnitureScheduleLines->contains(fn ($line) => $line->needs_finishes);
        });

        return Inertia::render('Projects/Show', [
            'project' => $project,
        ]);
    }

    public function create()
    {
        return Inertia::render('Projects/Form', [
            'project' => null,
            ...$this->options(),
        ]);
    }

    public function store(Request $request)
    {
        $project = Project::create($this->validated($request));

        return redirect()->route('projects.show', $project)->with('success', 'Project added.');
    }

    public function edit(Project $project)
    {
        return Inertia::render('Projects/Form', [
            'project' => $project,
            ...$this->options(),
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $project->update($this->validated($request));

        return redirect()->route('projects.index')->with('success', 'Project updated.');
    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project deleted.');
    }

    private function options(): array
    {
        return [
            'clients' => Client::orderBy('company_name')->get(['id', 'company_name']),
            'users' => User::orderBy('name')->get(['id', 'name']),
        ];
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'client_id' => 'required|exists:clients,id',
            'pm_user_id' => 'required|exists:users,id',
            'project_descriptor' => 'nullable|string|max:255',
            'site_name' => 'nullable|string|max:255',
            'site_address' => 'nullable|string|max:255',
            'site_contact_name' => 'nullable|string|max:255',
        ]);
    }
}
