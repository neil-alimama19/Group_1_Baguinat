<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        // 1. Validate the input
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'confirmed', 'min:8'],
            // Photo is optional: must be an image of type jpg, jpeg, png or webp, max 2048 KB (2 MB)
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        // 2. Update basic info
        $user->first_name = $validated['first_name'];
        $user->middle_name = $validated['middle_name'] ?? null;
        $user->last_name = $validated['last_name'];
        $user->email = $validated['email'];

        // 3. Update password if provided
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        // 4. Save the new photo, if one was uploaded
        if ($request->hasFile('photo')) {
            // Delete the old file from the public disk first
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }

            // Store in storage/app/public/avatars and save only the path (e.g. avatars/abc123.jpg)
            $user->photo = $request->file('photo')->store('avatars', 'public');
        }

        // Note: We never update role_id, is_active, or is_admin here.
        $user->save();

        // 5. Redirect back with success message
        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully!');
    }
}