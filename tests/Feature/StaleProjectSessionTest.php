<?php

use App\Models\Project;
use App\Models\Role;
use App\Models\User;

test('a session pointing at a deleted project is repaired instead of crashing', function () {
    $user = User::factory()->create();
    $project = Project::create(['name' => 'Valle Pacis', 'address' => '1 Test Rd']);
    Role::create(['user_id' => $user->id, 'project_id' => $project->id, 'type' => Role::ADMIN]);

    // A project this session still points at, but which no longer exists -
    // e.g. it was deleted after being selected as current.
    $response = $this->actingAs($user)
        ->withSession(['current_project_id' => 999999])
        ->get(route('dashboard'));

    $response->assertOk();

    // The session should have been repaired to the user's one real project,
    // not left pointing at the deleted one.
    $this->assertSame($project->id, session('current_project_id'));
});

test('a session pointing at a project the user no longer belongs to is repaired', function () {
    $user = User::factory()->create();
    $project = Project::create(['name' => 'Valle Pacis', 'address' => '1 Test Rd']);
    $otherProject = Project::create(['name' => 'Other Farm', 'address' => '2 Test Rd']);
    Role::create(['user_id' => $user->id, 'project_id' => $project->id, 'type' => Role::ADMIN]);
    // otherProject exists, but this user was never given a role on it.

    $response = $this->actingAs($user)
        ->withSession(['current_project_id' => $otherProject->id])
        ->get(route('dashboard'));

    $response->assertOk();
    $this->assertSame($project->id, session('current_project_id'));
});

test('a valid current_project_id in session is left untouched', function () {
    $user = User::factory()->create();
    $project = Project::create(['name' => 'Valle Pacis', 'address' => '1 Test Rd']);
    $otherProject = Project::create(['name' => 'Other Farm', 'address' => '2 Test Rd']);
    Role::create(['user_id' => $user->id, 'project_id' => $project->id, 'type' => Role::ADMIN]);
    Role::create(['user_id' => $user->id, 'project_id' => $otherProject->id, 'type' => Role::ADMIN]);

    $this->actingAs($user)
        ->withSession(['current_project_id' => $project->id])
        ->get(route('dashboard'))
        ->assertOk();

    $this->assertSame($project->id, session('current_project_id'));
});

test('an approver whose stale session project was deleted does not crash either', function () {
    $user = User::factory()->create();
    $project = Project::create(['name' => 'Valle Pacis', 'address' => '1 Test Rd']);
    Role::create(['user_id' => $user->id, 'project_id' => $project->id, 'type' => Role::APPROVER]);

    $this->actingAs($user)
        ->withSession(['current_project_id' => 999999])
        ->get(route('dashboard'))
        ->assertOk();
});
