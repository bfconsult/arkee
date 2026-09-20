<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Inertia\Inertia;

class QuoteController extends Controller
{
    /**
     * A Quote is a Project's furniture schedule - this is a read-only,
     * status-grouped view over the same Projects managed under Data.
     */
    public function index()
    {
        $projects = Project::with('client')->orderByDesc('date')->get();

        $statuses = [
            Project::STATUS_QUOTE,
            Project::STATUS_COMPLETE,
            Project::STATUS_APPROVED,
            Project::STATUS_CANCELLED,
        ];

        return Inertia::render('Quotes/Index', [
            'quotesByStatus' => collect($statuses)->mapWithKeys(
                fn ($status) => [$status => $projects->where('status', $status)->values()]
            ),
        ]);
    }
}
