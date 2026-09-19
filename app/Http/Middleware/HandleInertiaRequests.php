<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $properties = $user ? $user->properties()->get() : collect();

        $currentPropertyId = session('current_property_id');

        // Auto-select (or re-select) a property if none is set, *or* the one in
        // session no longer belongs to this user - e.g. they were removed from
        // it, or it was deleted, since the id was stashed. Controllers that read
        // session('current_property_id') directly (not everything goes through
        // $currentProperty below) trust it to be a property they can actually
        // access; leaving a stale id in place crashes anything downstream that
        // assumes Property::find() on it returns something. The session itself
        // also only lasts SESSION_LIFETIME minutes, so an idle session left
        // backgrounded longer than that gets a fresh empty session on its next
        // request. Prefer the user's own last explicit selection (persisted on
        // the User model, so it survives that reset) over just picking the
        // first one, as long as they still belong to it.
        if (!($currentPropertyId && $properties->contains('id', $currentPropertyId)) && $properties->count() > 0) {
            $currentPropertyId = ($user->current_property_id && $properties->contains('id', $user->current_property_id))
                ? $user->current_property_id
                : $properties->first()->id;

            session(['current_property_id' => $currentPropertyId]);
            $user->update(['current_property_id' => $currentPropertyId]);
        } elseif ($currentPropertyId && !$properties->contains('id', $currentPropertyId)) {
            // No properties left to fall back to either - clear it out so
            // nothing downstream mistakes a stale id for a valid selection.
            $currentPropertyId = null;
            session(['current_property_id' => null]);
        }

        $currentProperty = $user && $currentPropertyId
            ? $user->properties()->find($currentPropertyId)
            : null;

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
            ],
            'properties' => $properties,
            'currentProperty' => $currentProperty,
            'currentUserRole' => $user && $currentProperty ? $user->roleOn($currentProperty) : null,
            'flash' => [
                'error' => session('error'),
                'success' => session('success'),
            ],
        ];
    }
}
