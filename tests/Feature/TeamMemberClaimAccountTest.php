<?php

use App\Models\Invitation;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

function addUnclaimedMember(User $admin, Project $project, string $name = 'Casey Contractor'): User
{
    test()->actingAs($admin)
        ->withSession(['current_project_id' => $project->id])
        ->post(route('invitations.store-member'), ['name' => $name, 'role' => 'worker'])
        ->assertSessionHasNoErrors();

    return User::where('name', $name)->firstOrFail();
}

test('inviting an already-added member persists their email and links the invitation to their user', function () {
    Mail::fake();

    $admin = User::factory()->create();
    $project = Project::create(['name' => 'Valle Pacis', 'address' => '1 Test Rd']);
    Role::create(['user_id' => $admin->id, 'project_id' => $project->id, 'type' => Role::ADMIN]);

    $member = addUnclaimedMember($admin, $project);
    $role = Role::where('user_id', $member->id)->firstOrFail();

    $this->actingAs($admin)
        ->withSession(['current_project_id' => $project->id])
        ->post(route('invitations.invite-member', $role->id), [
            'email' => 'casey@example.com',
        ])
        ->assertSessionHasNoErrors();

    expect($member->fresh()->email)->toBe('casey@example.com');

    $invitation = Invitation::where('project_id', $project->id)->whereNull('accepted_at')->firstOrFail();
    expect($invitation->user_id)->toBe($member->id);
    expect($invitation->email)->toBe('casey@example.com');
});

test('the accept page renders the Claim form for an unclaimed member, not the normal Accept form', function () {
    $admin = User::factory()->create();
    $project = Project::create(['name' => 'Valle Pacis', 'address' => '1 Test Rd']);
    Role::create(['user_id' => $admin->id, 'project_id' => $project->id, 'type' => Role::ADMIN]);
    $member = addUnclaimedMember($admin, $project);

    $invitation = Invitation::create([
        'project_id' => $project->id,
        'invited_by' => $admin->id,
        'user_id' => $member->id,
        'email' => 'casey@example.com',
        'role' => Role::WORKER,
    ]);

    $this->get(route('invitations.accept', $invitation->token))
        ->assertInertia(fn ($page) => $page->component('Invitations/Claim'));

    // A normal stranger-by-email invitation still gets the plain Accept page.
    $strangerInvitation = Invitation::create([
        'project_id' => $project->id,
        'invited_by' => $admin->id,
        'email' => 'stranger@example.com',
        'role' => Role::WORKER,
    ]);

    $this->get(route('invitations.accept', $strangerInvitation->token))
        ->assertInertia(fn ($page) => $page->component('Invitations/Accept'));
});

test('claiming sets a password and claimed_at, logs the user in, and reuses the existing role', function () {
    $admin = User::factory()->create();
    $project = Project::create(['name' => 'Valle Pacis', 'address' => '1 Test Rd']);
    Role::create(['user_id' => $admin->id, 'project_id' => $project->id, 'type' => Role::ADMIN]);
    $member = addUnclaimedMember($admin, $project);
    $originalRole = Role::where('user_id', $member->id)->firstOrFail();

    $invitation = Invitation::create([
        'project_id' => $project->id,
        'invited_by' => $admin->id,
        'user_id' => $member->id,
        'email' => 'casey@example.com',
        'role' => Role::WORKER,
    ]);

    $this->post(route('invitations.claim', $invitation->token), [
        'name' => 'Casey Contractor',
        'password' => 'a-brand-new-password',
        'password_confirmation' => 'a-brand-new-password',
    ])->assertSessionHasNoErrors();

    $member->refresh();
    expect($member->email)->toBe('casey@example.com');
    expect($member->claimed_at)->not->toBeNull();
    expect($member->isClaimed())->toBeTrue();
    $this->assertAuthenticatedAs($member);

    expect(Role::where('user_id', $member->id)->where('project_id', $project->id)->count())->toBe(1);
    expect(Role::find($originalRole->id))->not->toBeNull();

    expect(Invitation::find($invitation->id)->fresh()->accepted_at)->not->toBeNull();
});

test('a logged-out invitee whose account already exists is sent to login, not register', function () {
    $admin = User::factory()->create();
    $project = Project::create(['name' => 'Valle Pacis', 'address' => '1 Test Rd']);
    Role::create(['user_id' => $admin->id, 'project_id' => $project->id, 'type' => Role::ADMIN]);

    $existing = User::factory()->create(['email' => 'already-here@example.com']);

    $invitation = Invitation::create([
        'project_id' => $project->id,
        'invited_by' => $admin->id,
        'email' => 'already-here@example.com',
        'role' => Role::WORKER,
    ]);

    $this->post(route('invitations.process', $invitation->token))
        ->assertRedirect(route('login'));
});
