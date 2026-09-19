<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\InvitationController;

Route::get('/', function () {
    return Inertia::render('Welcome');
});

Route::get('/privacy-policy', function () {
    return Inertia::render('Legal');
})->name('privacy-policy');

Route::get('/contact', function () {
    return Inertia::render('Contact');
})->name('contact');

// Served via a route (not a static public/ file) so the icon URLs below can
// resolve through asset(), which points at the CDN on Vapor — a plain public/
// file would 404 there, since Vapor only auto-redirects favicon.ico/robots.txt.
Route::get('manifest.webmanifest', function () {
    return response()->json([
        'name' => config('app.name'),
        'short_name' => config('app.name'),
        'start_url' => '/dashboard',
        'scope' => '/',
        'display' => 'standalone',
        'orientation' => 'portrait',
        'background_color' => '#ffffff',
        'theme_color' => '#ffffff',
        'icons' => [
            ['src' => asset('icon-192.png'), 'sizes' => '192x192', 'type' => 'image/png'],
            ['src' => asset('icon-512.png'), 'sizes' => '512x512', 'type' => 'image/png'],
        ],
    ])->header('Content-Type', 'application/manifest+json');
})->name('manifest');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::delete('/profile/avatar', [ProfileController::class, 'destroyAvatar'])->name('profile.avatar.destroy');
});

// Admin only routes
Route::middleware(['auth', 'property.role:admin'])->group(function () {
    Route::resource('properties', PropertyController::class)->only(['edit', 'update', 'destroy']);
});

// All authenticated users with a property
Route::middleware(['auth'])->group(function () {
    Route::get('properties/create', [PropertyController::class, 'create'])->name('properties.create');
    Route::post('properties', [PropertyController::class, 'store'])->name('properties.store');
    Route::get('properties/{property}', [PropertyController::class, 'show'])->name('properties.show');
    Route::delete('properties/{property}/leave', [PropertyController::class, 'leave'])->name('properties.leave');
    Route::post('select-property', function (\Illuminate\Http\Request $request) {
        $user = \Illuminate\Support\Facades\Auth::user();
        $request->validate(['property_id' => ['required', \Illuminate\Validation\Rule::exists('properties', 'id')]]);
        abort_unless($user->properties()->where('properties.id', $request->property_id)->exists(), 403);
        session(['current_property_id' => $request->property_id]);
        $user->update(['current_property_id' => $request->property_id]);
        return back();
    })->name('property.select');
});

// Invitation accept flow (no auth required to view, but process requires auth)
Route::get('invitations/{token}', [InvitationController::class, 'accept'])->name('invitations.accept');
Route::post('invitations/{token}', [InvitationController::class, 'process'])->name('invitations.process');
// Claiming a team member who was already added directly (no auth required -
// this IS how they get their first session) - see InvitationController::claim.
Route::post('invitations/{token}/claim', [InvitationController::class, 'claim'])->name('invitations.claim');

// Admin and Manager can manage invitations
Route::middleware(['auth', 'property.role:admin,manager'])->group(function () {
    Route::get('team', [InvitationController::class, 'index'])->name('invitations.index');
    Route::post('team/invite', [InvitationController::class, 'store'])->name('invitations.store');
    Route::post('team/members', [InvitationController::class, 'storeMember'])->name('invitations.store-member');
    Route::post('team/roles/{role}/invite', [InvitationController::class, 'inviteMember'])->name('invitations.invite-member');
    Route::patch('team/roles/{role}', [InvitationController::class, 'updateRole'])->name('invitations.update-role');
    Route::delete('team/roles/{role}', [InvitationController::class, 'destroyRole'])->name('invitations.destroy-role');
    Route::delete('team/invitations/{invitation}', [InvitationController::class, 'destroyInvitation'])->name('invitations.destroy-invitation');
});

require __DIR__.'/auth.php';
