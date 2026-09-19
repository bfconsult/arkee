<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        return Inertia::render('Users/Index', [
            'users' => User::orderBy('active')->orderBy('name')->get(),
        ]);
    }

    public function create()
    {
        return Inertia::render('Users/Form', [
            'targetUser' => null,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request, isNew: true);
        $data['password'] = Hash::make($data['password']);
        // An admin creating an account directly is itself the approval step.
        $data['active'] = $data['active'] ?? true;

        User::create($data);

        return redirect()->route('users.index')->with('success', 'User added.');
    }

    public function edit(User $user)
    {
        return Inertia::render('Users/Form', [
            'targetUser' => $user,
        ]);
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validated($request, isNew: false, editing: $user);

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User updated.');
    }

    public function approve(User $user)
    {
        $user->update(['active' => true]);

        return redirect()->route('users.index')->with('success', "{$user->name} approved.");
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->is($request->user())) {
            return redirect()->route('users.index')->with('error', "You can't delete your own account.");
        }

        if ($user->role === User::ROLE_ADMIN && User::where('role', User::ROLE_ADMIN)->count() <= 1) {
            return redirect()->route('users.index')->with('error', "You can't delete the last administrator.");
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'User deleted.');
    }

    private function validated(Request $request, bool $isNew, ?User $editing = null): array
    {
        $passwordRules = $isNew
            ? ['required', 'confirmed', Rules\Password::defaults()]
            : ['nullable', 'confirmed', Rules\Password::defaults()];

        return $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique('users', 'email')->ignore($editing?->id),
            ],
            'role' => 'required|in:'.implode(',', User::ROLES),
            'active' => 'boolean',
            'password' => $passwordRules,
        ]);
    }
}
