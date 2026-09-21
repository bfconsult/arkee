<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\ComponentController;
use App\Http\Controllers\DeliveryLocationController;
use App\Http\Controllers\FinishController;
use App\Http\Controllers\FurnitureScheduleLineController;
use App\Http\Controllers\ItemCategoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MaterialController;
use App\Http\Controllers\PackagingTypeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\QuoteController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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

    Route::resource('clients', ClientController::class)->except('show');
    Route::resource('suppliers', SupplierController::class)->except('show');
    Route::resource('delivery-locations', DeliveryLocationController::class)->except('show');
    Route::resource('packaging-types', PackagingTypeController::class)->except('show');
    Route::resource('item-categories', ItemCategoryController::class)->except('show');
    Route::resource('materials', MaterialController::class);
    Route::resource('materials.finishes', FinishController::class)->except('show');
    Route::resource('items', ItemController::class);
    Route::resource('items.components', ComponentController::class)->except('show');
    Route::get('quotes', [QuoteController::class, 'index'])->name('quotes.index');
    Route::resource('projects', ProjectController::class);
    Route::resource('projects.quotes', QuoteController::class)->except(['index', 'show']);
    Route::resource('quotes.schedule-lines', FurnitureScheduleLineController::class)->except('show');
    Route::resource('projects.purchase-orders', PurchaseOrderController::class)->except('show');

    Route::middleware('admin')->group(function () {
        Route::patch('/users/{user}/approve', [UserController::class, 'approve'])->name('users.approve');
        Route::resource('users', UserController::class)->except('show');
    });
});

require __DIR__.'/auth.php';
