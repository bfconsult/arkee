<?php

use App\Models\Property;
use App\Models\Role;
use App\Models\User;

test('a session pointing at a deleted property is repaired instead of crashing', function () {
    $user = User::factory()->create();
    $property = Property::create(['name' => 'Valle Pacis', 'address' => '1 Test Rd']);
    Role::create(['user_id' => $user->id, 'property_id' => $property->id, 'type' => Role::ADMIN]);

    // A property this session still points at, but which no longer exists -
    // e.g. it was deleted after being selected as current.
    $response = $this->actingAs($user)
        ->withSession(['current_property_id' => 999999])
        ->get(route('dashboard'));

    $response->assertOk();

    // The session should have been repaired to the user's one real property,
    // not left pointing at the deleted one.
    $this->assertSame($property->id, session('current_property_id'));
});

test('a session pointing at a property the user no longer belongs to is repaired', function () {
    $user = User::factory()->create();
    $property = Property::create(['name' => 'Valle Pacis', 'address' => '1 Test Rd']);
    $otherProperty = Property::create(['name' => 'Other Farm', 'address' => '2 Test Rd']);
    Role::create(['user_id' => $user->id, 'property_id' => $property->id, 'type' => Role::ADMIN]);
    // otherProperty exists, but this user was never given a role on it.

    $response = $this->actingAs($user)
        ->withSession(['current_property_id' => $otherProperty->id])
        ->get(route('dashboard'));

    $response->assertOk();
    $this->assertSame($property->id, session('current_property_id'));
});

test('a valid current_property_id in session is left untouched', function () {
    $user = User::factory()->create();
    $property = Property::create(['name' => 'Valle Pacis', 'address' => '1 Test Rd']);
    $otherProperty = Property::create(['name' => 'Other Farm', 'address' => '2 Test Rd']);
    Role::create(['user_id' => $user->id, 'property_id' => $property->id, 'type' => Role::ADMIN]);
    Role::create(['user_id' => $user->id, 'property_id' => $otherProperty->id, 'type' => Role::ADMIN]);

    $this->actingAs($user)
        ->withSession(['current_property_id' => $property->id])
        ->get(route('dashboard'))
        ->assertOk();

    $this->assertSame($property->id, session('current_property_id'));
});

test('an approver whose stale session property was deleted does not crash either', function () {
    $user = User::factory()->create();
    $property = Property::create(['name' => 'Valle Pacis', 'address' => '1 Test Rd']);
    Role::create(['user_id' => $user->id, 'property_id' => $property->id, 'type' => Role::APPROVER]);

    $this->actingAs($user)
        ->withSession(['current_property_id' => 999999])
        ->get(route('dashboard'))
        ->assertOk();
});
