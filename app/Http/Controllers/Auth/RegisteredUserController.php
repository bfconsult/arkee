<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('Auth/Register', [
            'email' => $request->query('email'),
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // The very first account on a fresh install has no admin to approve
        // it, so it bootstraps itself as the Administrator. Every account
        // after that is inactive until an admin approves it from /users.
        $isFirstUser = User::count() === 0;

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $isFirstUser ? User::ROLE_ADMIN : User::ROLE_PM,
            'active' => $isFirstUser,
        ]);

        event(new Registered($user));

        if (! $isFirstUser) {
            return redirect()->route('login')->with(
                'status',
                'Thanks for registering! An administrator needs to approve your account before you can sign in.'
            );
        }

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
