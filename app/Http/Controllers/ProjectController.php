<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProjectController extends Controller
{
    /**
     * Landing spot for a user with no projects at all - was previously
     * profile.edit (Account Settings), which has nothing on it about
     * projects and left brand-new users with no obvious next step. A user
     * who already has at least one project has no reason to be here (they
     * already have a working nav/project picker), so send them on instead.
     */
    public function create()
    {
        if (Auth::user()->projects()->exists()) {
            return redirect()->route('dashboard');
        }

        return Inertia::render('Projects/GetStarted');
    }

    /**
     * Creates a project with a placeholder name and no other details, gives
     * the creator an admin role on it, and switches the session to it -
     * there's no form here, so the real name/address get filled in on the
     * Edit page this redirects to instead. Email defaults to the creator's
     * own address (most users just re-type it anyway) - editable later.
     */
    public function store()
    {
        $project = Project::create([
            'name' => 'New Project',
            'address' => '',
            'email' => Auth::user()->email,
        ]);

        Auth::user()->roles()->create([
            'project_id' => $project->id,
            'type' => Role::ADMIN,
        ]);

        session(['current_project_id' => $project->id]);
        Auth::user()->update(['current_project_id' => $project->id]);

        return redirect()->route('projects.edit', $project);
    }

    public function show(Project $project)
    {
        abort_unless(Auth::user()->roleOn($project), 403);

        $currentRole = Auth::user()->roleOn($project);

        return Inertia::render('Projects/Show', [
            'project' => $project,
            'currentRole' => $currentRole,
            'canLeave' => $currentRole ? $this->canLeave($project, $currentRole) : false,
        ]);
    }

    /**
     * A member can leave freely unless doing so would strip the project of
     * its last admin - they're pointed at deleting the project instead.
     */
    private function canLeave(Project $project, string $roleType): bool
    {
        if ($roleType !== Role::ADMIN) {
            return true;
        }

        return $project->roles()->where('type', Role::ADMIN)->count() > 1;
    }

    public function leave(Project $project)
    {
        $user = Auth::user();
        $role = Role::where('user_id', $user->id)->where('project_id', $project->id)->firstOrFail();

        if (!$this->canLeave($project, $role->type)) {
            return back()->with('error', 'You are the last admin on this project - delete the project instead of leaving.');
        }

        $role->delete();

        if ((int) session('current_project_id') === $project->id) {
            session()->forget('current_project_id');
        }
        if ($user->current_project_id === $project->id) {
            $user->update(['current_project_id' => null]);
        }

        return redirect()->route('profile.edit')->with('success', "You have left {$project->name}.");
    }

    public function edit(Project $project)
    {
        abort_unless(Auth::user()->roleOn($project) === Role::ADMIN, 403);

        return Inertia::render('Projects/Edit', [
            'project' => $project,
            // Still has the placeholder details store() creates it with -
            // i.e. the admin hasn't been through this form yet, regardless
            // of how many times they've navigated away and back since it
            // was created.
            'isNewProject' => $project->name === 'New Project' && $project->address === '',
        ]);
    }

    public function update(Request $request, Project $project)
    {
        abort_unless(Auth::user()->roleOn($project) === Role::ADMIN, 403);

        // Same placeholder check as edit()'s isNewProject - captured before
        // the update so we can tell "onboarding just completed" apart from
        // an ordinary edit of an already-real project.
        $wasNewProject = $project->name === 'New Project' && $project->address === '';

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
        ]);

        $project->update($validated);

        return $wasNewProject
            ? redirect()->route('dashboard')->with('success', "You've successfully created your first project!")
            : redirect()->route('projects.show', $project);
    }

    public function destroy(Project $project)
    {
        abort_unless(Auth::user()->roleOn($project) === Role::ADMIN, 403);

        if ((int) session('current_project_id') === $project->id) {
            session()->forget('current_project_id');
        }

        $project->delete();

        return redirect()->route('profile.edit');
    }
}
