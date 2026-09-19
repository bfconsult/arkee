<?php

use App\Models\Invitation;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

test('re-inviting the same email supersedes the previous invitation token', function () {
    Mail::fake();

    $admin = User::factory()->create();
    $project = Project::create(['name' => 'Valle Pacis', 'address' => '1 Test Rd']);
    Role::create(['user_id' => $admin->id, 'project_id' => $project->id, 'type' => Role::ADMIN]);

    $oldInvitation = Invitation::create([
        'project_id' => $project->id,
        'invited_by' => $admin->id,
        'email' => 'wez1139@gmail.com',
        'role' => Role::WORKER,
    ]);
    $oldToken = $oldInvitation->token;

    $this->actingAs($admin)
        ->withSession(['current_project_id' => $project->id])
        ->post(route('invitations.store'), [
            'email' => 'wez1139@gmail.com',
            'role' => 'worker',
        ])
        ->assertSessionHasNoErrors();

    expect(Invitation::where('project_id', $project->id)
        ->where('email', 'wez1139@gmail.com')
        ->whereNull('accepted_at')
        ->count())->toBe(1);

    // The old link a user might still have open must no longer resolve.
    $this->get(route('invitations.accept', $oldToken))->assertNotFound();
});

test('removing a team member deletes their pending invitation', function () {
    $admin = User::factory()->create();
    $worker = User::factory()->create(['email' => 'wez1139@gmail.com']);
    $project = Project::create(['name' => 'Valle Pacis', 'address' => '1 Test Rd']);
    Role::create(['user_id' => $admin->id, 'project_id' => $project->id, 'type' => Role::ADMIN]);
    $workerRole = Role::create(['user_id' => $worker->id, 'project_id' => $project->id, 'type' => Role::WORKER]);

    $invitation = Invitation::create([
        'project_id' => $project->id,
        'invited_by' => $admin->id,
        'email' => 'wez1139@gmail.com',
        'role' => Role::WORKER,
    ]);

    $this->actingAs($admin)
        ->withSession(['current_project_id' => $project->id])
        ->delete(route('invitations.destroy-role', $workerRole))
        ->assertSessionHasNoErrors();

    expect(Invitation::find($invitation->id))->toBeNull();
});

test('an unclaimed member with a pending invitation still shows in the team list', function () {
    Mail::fake();

    $admin = User::factory()->create();
    $project = Project::create(['name' => 'Valle Pacis', 'address' => '1 Test Rd']);
    Role::create(['user_id' => $admin->id, 'project_id' => $project->id, 'type' => Role::ADMIN]);

    $this->actingAs($admin)
        ->withSession(['current_project_id' => $project->id])
        ->post(route('invitations.store-member'), ['name' => 'Casey Contractor', 'role' => 'worker'])
        ->assertSessionHasNoErrors();
    $member = User::where('name', 'Casey Contractor')->firstOrFail();
    $role = Role::where('user_id', $member->id)->firstOrFail();

    $this->actingAs($admin)
        ->withSession(['current_project_id' => $project->id])
        ->post(route('invitations.invite-member', $role->id), ['email' => 'casey@example.com'])
        ->assertSessionHasNoErrors();

    $this->actingAs($admin)
        ->withSession(['current_project_id' => $project->id])
        ->get(route('invitations.index'))
        ->assertInertia(fn ($page) => $page
            ->component('Invitations/Index')
            ->has('roles', 2)
            ->where('roles.1.user.id', $member->id));
});

test('removing an unclaimed member deletes their invitation but keeps their user record', function () {
    $admin = User::factory()->create();
    $project = Project::create(['name' => 'Valle Pacis', 'address' => '1 Test Rd']);
    Role::create(['user_id' => $admin->id, 'project_id' => $project->id, 'type' => Role::ADMIN]);

    $this->actingAs($admin)
        ->withSession(['current_project_id' => $project->id])
        ->post(route('invitations.store-member'), ['name' => 'Casey Contractor', 'role' => 'worker'])
        ->assertSessionHasNoErrors();
    $member = User::where('name', 'Casey Contractor')->firstOrFail();
    $role = Role::where('user_id', $member->id)->firstOrFail();

    $invitation = Invitation::create([
        'project_id' => $project->id,
        'invited_by' => $admin->id,
        'user_id' => $member->id,
        'email' => 'casey@example.com',
        'role' => Role::WORKER,
    ]);

    $this->actingAs($admin)
        ->withSession(['current_project_id' => $project->id])
        ->delete(route('invitations.destroy-role', $role))
        ->assertSessionHasNoErrors();

    expect(Invitation::find($invitation->id))->toBeNull();
    expect(User::find($member->id))->not->toBeNull();
});
