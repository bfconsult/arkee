<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Quote;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QuoteController extends Controller
{
    /**
     * Every Quote across every Project, grouped by status - the Task-level
     * overview. Creating/editing a Quote itself happens from its Project.
     */
    public function index()
    {
        $quotes = Quote::with('project.client')->orderByDesc('date')->get();

        $statuses = [
            Quote::STATUS_QUOTE,
            Quote::STATUS_COMPLETE,
            Quote::STATUS_APPROVED,
            Quote::STATUS_CANCELLED,
        ];

        return Inertia::render('Quotes/Index', [
            'quotesByStatus' => collect($statuses)->mapWithKeys(
                fn ($status) => [$status => $quotes->where('status', $status)->values()]
            ),
        ]);
    }

    public function create(Project $project)
    {
        return Inertia::render('Quotes/Form', [
            'project' => $project,
            'quote' => null,
        ]);
    }

    public function store(Request $request, Project $project)
    {
        $project->quotes()->create($this->validated($request));

        return redirect()->route('projects.show', $project)->with('success', 'Quote added.');
    }

    public function edit(Project $project, Quote $quote)
    {
        return Inertia::render('Quotes/Form', [
            'project' => $project,
            'quote' => $quote,
        ]);
    }

    public function update(Request $request, Project $project, Quote $quote)
    {
        $quote->update($this->validated($request));

        return redirect()->route('projects.show', $project)->with('success', 'Quote updated.');
    }

    public function destroy(Project $project, Quote $quote)
    {
        $quote->delete();

        return redirect()->route('projects.show', $project)->with('success', 'Quote deleted.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'quote_number' => 'nullable|string|max:255',
            'version' => 'nullable|integer|min:1',
            'date' => 'nullable|date',
            'status' => 'required|in:quote,complete,approved,cancelled',
        ]);
    }
}
