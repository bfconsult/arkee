<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $projects = $user ? $user->projects()->get() : collect();

        $currentProjectId = session('current_project_id');

        // Auto-select (or re-select) a project if none is set, *or* the one in
        // session no longer belongs to this user - e.g. they were removed from
        // it, or it was deleted, since the id was stashed. Controllers that read
        // session('current_project_id') directly (not everything goes through
        // $currentProject below) trust it to be a project they can actually
        // access; leaving a stale id in place crashes anything downstream that
        // assumes Project::find() on it returns something. The session itself
        // also only lasts SESSION_LIFETIME minutes, so an idle session left
        // backgrounded longer than that gets a fresh empty session on its next
        // request. Prefer the user's own last explicit selection (persisted on
        // the User model, so it survives that reset) over just picking the
        // first one, as long as they still belong to it.
        if (!($currentProjectId && $projects->contains('id', $currentProjectId)) && $projects->count() > 0) {
            $currentProjectId = ($user->current_project_id && $projects->contains('id', $user->current_project_id))
                ? $user->current_project_id
                : $projects->first()->id;

            session(['current_project_id' => $currentProjectId]);
            $user->update(['current_project_id' => $currentProjectId]);
        } elseif ($currentProjectId && !$projects->contains('id', $currentProjectId)) {
            // No projects left to fall back to either - clear it out so
            // nothing downstream mistakes a stale id for a valid selection.
            $currentProjectId = null;
            session(['current_project_id' => null]);
        }

        $currentProject = $user && $currentProjectId
            ? $user->projects()->find($currentProjectId)
            : null;

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
            ],
            'projects' => $projects,
            'currentProject' => $currentProject,
            'currentUserRole' => $user && $currentProject ? $user->roleOn($currentProject) : null,
            'flash' => [
                'error' => session('error'),
                'success' => session('success'),
            ],
        ];
    }
}
