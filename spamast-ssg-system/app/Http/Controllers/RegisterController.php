<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function index()
    {
        return view('register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:255',
            'email'          => 'required|email:rfc|max:255|unique:users,email', // Changed to email:rfc and users table
            'password'       => 'required|string|min:8|confirmed',
        ]);

        // Find or create the Student role
        $studentRole = Role::firstOrCreate(['name' => 'Student']);

        User::create([
            'name'           => $validated['name'],
            'first_name'     => explode(' ', $validated['name'])[0], // Basic split
            'last_name'      => count(explode(' ', $validated['name'])) > 1 ? explode(' ', $validated['name'])[1] : '',
            'email'          => $validated['email'],
            'password'       => Hash::make($validated['password']),
            'role_id'        => $studentRole->id,
            'is_active'      => false, // Requires Admin approval
            'is_admin'       => false,
        ]);

        return redirect()->route('login')->with('success', 'Registration successful! Your account is awaiting Admin approval.');
    }
}