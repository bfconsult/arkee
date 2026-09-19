<?php

use App\Models\Project;
use App\Models\Role;
use App\Models\User;

test('the get-started page renders for a user with no projects', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('projects.create'))
        ->assertInertia(fn ($page) => $page->component('Projects/GetStarted'));
});

test('a user who already has a project is sent past the get-started page', function () {
    $user = User::factory()->create();
    $project = Project::create(['name' => 'Valle Pacis', 'address' => '1 Test Rd']);
    Role::create(['user_id' => $user->id, 'project_id' => $project->id, 'type' => Role::ADMIN]);

    $this->actingAs($user)
        ->get(route('projects.create'))
        ->assertRedirect(route('dashboard'));
});

test('creating a project from the get-started flow works the same as the nav Add Project action', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('projects.store'))
        ->assertRedirect();

    expect($user->projects()->count())->toBe(1);
    expect($user->fresh()->roleOn($user->projects()->first()))->toBe(Role::ADMIN);
});

test('a new project defaults its email to the creating user\'s own email', function () {
    $user = User::factory()->create(['email' => 'me@example.com']);

    $this->actingAs($user)->post(route('projects.store'));

    expect($user->projects()->first()->email)->toBe('me@example.com');
});

test('completing onboarding by saving a new project redirects to the dashboard with a success flash', function () {
    $user = User::factory()->create();
    $project = Project::create(['name' => 'New Project', 'address' => '']);
    Role::create(['user_id' => $user->id, 'project_id' => $project->id, 'type' => Role::ADMIN]);

    $this->actingAs($user)
        ->patch(route('projects.update', $project), [
            'name' => 'Valle Pacis',
            'address' => '1 Test Rd',
        ])
        ->assertRedirect(route('dashboard'))
        ->assertSessionHas('success');
});

test('editing an already-established project still redirects to the project page', function () {
    $user = User::factory()->create();
    $project = Project::create(['name' => 'Valle Pacis', 'address' => '1 Test Rd']);
    Role::create(['user_id' => $user->id, 'project_id' => $project->id, 'type' => Role::ADMIN]);

    $this->actingAs($user)
        ->patch(route('projects.update', $project), [
            'name' => 'Valle Pacis Updated',
            'address' => '1 Test Rd',
        ])
        ->assertRedirect(route('projects.show', $project))
        ->assertSessionMissing('success');
});
