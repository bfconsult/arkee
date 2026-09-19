<?php

use App\Models\User;

test('the first user to register bootstraps as an active administrator', function () {
    $response = $this->post('/register', [
        'name' => 'First Admin',
        'email' => 'first@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $user = User::sole();
    expect($user->role)->toBe(User::ROLE_ADMIN);
    expect($user->active)->toBeTrue();

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('a second registrant is inactive and cannot sign in until approved', function () {
    User::factory()->create(['role' => User::ROLE_ADMIN]);

    $response = $this->post('/register', [
        'name' => 'Pending Person',
        'email' => 'pending@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $pending = User::where('email', 'pending@example.com')->sole();
    expect($pending->active)->toBeFalse();

    $this->assertGuest();
    $response->assertRedirect(route('login'));

    $login = $this->post('/login', [
        'email' => 'pending@example.com',
        'password' => 'password',
    ]);
    $this->assertGuest();
    $login->assertSessionHasErrors('email');
});

test('an admin can approve a pending user, after which they can log in', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $pending = User::factory()->create(['role' => User::ROLE_PM, 'active' => false]);

    $this->actingAs($admin)
        ->patch(route('users.approve', $pending))
        ->assertRedirect(route('users.index'));

    expect($pending->fresh()->active)->toBeTrue();

    $this->post('/login', [
        'email' => $pending->email,
        'password' => 'password',
    ]);
    $this->assertAuthenticated();
});

test('a non-admin cannot access the users management screen', function () {
    $user = User::factory()->create(['role' => User::ROLE_PM]);

    $this->actingAs($user)
        ->get(route('users.index'))
        ->assertForbidden();
});

test('an admin can create a user directly, active immediately', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $this->actingAs($admin)
        ->post(route('users.store'), [
            'name' => 'Directly Added',
            'email' => 'direct@example.com',
            'role' => User::ROLE_READ_ONLY,
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
        ->assertRedirect(route('users.index'));

    $created = User::where('email', 'direct@example.com')->sole();
    expect($created->active)->toBeTrue();
    expect($created->role)->toBe(User::ROLE_READ_ONLY);
});

test('an admin cannot delete their own account', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $this->actingAs($admin)
        ->delete(route('users.destroy', $admin))
        ->assertRedirect(route('users.index'));

    expect(User::find($admin->id))->not->toBeNull();
});

test('an admin can delete another admin as long as one remains', function () {
    $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
    $otherAdmin = User::factory()->create(['role' => User::ROLE_ADMIN]);

    $this->actingAs($admin)
        ->delete(route('users.destroy', $otherAdmin))
        ->assertRedirect(route('users.index'));

    expect(User::find($otherAdmin->id))->toBeNull();
    expect(User::where('role', User::ROLE_ADMIN)->count())->toBe(1);
});
