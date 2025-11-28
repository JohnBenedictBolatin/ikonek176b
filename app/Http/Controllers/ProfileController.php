<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\RoleCategory;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        $user->load('roleCategory');

        $roleCategories = RoleCategory::select('role_id as id', 'name')->orderBy('name')->get();

        // Render the exact component path used in your Route::inertia
        return Inertia::render('User/Resident/R_Profile', [
            'roleCategories' => $roleCategories,
            // don't pass top-level 'user' — rely on shared auth.user
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'profile_description' => ['nullable', 'string', 'max:2000'],
            'fk_role_id' => ['nullable', 'integer', 'exists:role_categories,role_id'],
        ]);

        $user->profile_description = $data['profile_description'] ?? $user->profile_description;
        if (array_key_exists('fk_role_id', $data) && $data['fk_role_id'] !== null) {
            $user->fk_role_id = $data['fk_role_id'];
        }
        $user->save();

        // Redirect to the canonical profile page so the next Inertia response includes updated auth.user
        return redirect()->route('profile.edit')->with('message', 'Profile updated successfully.');
    }

}
