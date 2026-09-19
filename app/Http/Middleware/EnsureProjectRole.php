<?php

namespace App\Http\Middleware;

use App\Models\Project;
use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProjectRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $projectId = session('current_project_id');

        if (!$projectId) {
            return redirect()->route('profile.edit')
                ->with('error', 'Please select a project first.');
        }

        $project = Project::find($projectId);

        if (!$project) {
            session()->forget('current_project_id');

            return redirect()->route('profile.edit')
                ->with('error', 'Project not found.');
        }

        $userRole = $request->user()->roleOn($project);

        if (!in_array($userRole, $roles)) {
            abort(403, 'You do not have permission to perform this action.');
        }

        return $next($request);
    }
}