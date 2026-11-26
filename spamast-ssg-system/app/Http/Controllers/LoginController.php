<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function index()
    {
        return view('login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'remember' => ['nullable', 'boolean'],
        ]);

        if (Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ], $request->boolean('remember'))) {

            $user = $request->user();

            // Inactive accounts are logged out right away
            if (! $user->is_active) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'Your account is inactive or awaiting Admin approval',
                ])->onlyInput('email');
            }

            $request->session()->regenerate();

            // intended(): go to the page the user first asked for, otherwise to the role dashboard
            return redirect()->intended(route($user->dashboardRoute()))
                ->with('success', 'Welcome back!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        // End the session and create a new CSRF token so the old ones cannot be reused
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Logout goes to the login page (guide table 3.8.1); login.blade.php shows the success message
        return redirect()->route('login')->with('success', 'You have been logged out.');
    }
}