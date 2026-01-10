<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Illuminate\Support\Facades\Auth;

class HandleInertiaRequests extends Middleware
{
   
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                // lazy-loaded so it only runs when the prop is accessed
                'user' => fn () => $this->resolveUser($request),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ]);
    }

    protected function resolveUser(Request $request)
    {
        // Prefer the authenticated user
        $u = $request->user();
        if ($u) {
            // Attempt to safely access a related credential if it exists.
            // Using null-safe operator to avoid exceptions when relation missing.
            $cred = $u->credential;


            return [
            // User id for the user identity
            'id' => $u->user_id ?? $u->id ?? null,
            'user_id' => $u->user_id ?? $u->id ?? null,
            // Names
            'name' => trim(($u->first_name ?? '') . ' ' . ($u->last_name ?? '')) ?: ($u->name ?? null),
            // Profile picture
            'profile_pic' => $u->profile_pic ?? null,
            // Contact fields
            'primary_contact' => $cred->contact_number ?? null,
            'secondary_contact' => $cred->secondary_contact_number ?? null,
            // User Roles
            'fk_role_id' => $u->fk_role_id ?? null,
            // Any small helper flags useful to the frontend (booleans, minimal data)
            'is_admin' => isset($u->fk_role_id) ? ($u->fk_role_id === 1) : null,
            ];
        }


        // Fallback to session-stored 'logged_user'
        $sessionUser = $request->session()->get('logged_user');
        if ($sessionUser) {
            return [
            'id' => $sessionUser['id'] ?? null,
            'user_id' => $sessionUser['id'] ?? $sessionUser['user_id'] ?? null,
            'name' => $sessionUser['name'] ?? null,
            'profile_pic' => $sessionUser['profile_pic'] ?? null,
            'primary_contact' => $sessionUser['contact_number'] ?? $sessionUser['primary_contact'] ?? null,
            'secondary_contact' => $sessionUser['secondary_number'] ?? $sessionUser['secondary_contact'] ?? null,
            'fk_role_id' => $sessionUser['fk_role_id'] ?? null,
            'is_admin' => isset($sessionUser['fk_role_id']) ? ($sessionUser['fk_role_id'] === 1) : null,
            ];
        }


        return null;
    }
}
