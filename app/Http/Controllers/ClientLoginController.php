<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ClientLoginController extends Controller
{
    /**
     * Show the client login form.
     */
    public function showLoginForm()
    {
        // Redirect if already authenticated as PATIENT
        if (Auth::check() && Auth::user()->role === 'PATIENT') {
            return redirect()->route('patient.dashboard');
        }

        return view('client.login');
    }

    /**
     * Handle a client login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        // Find user by email or username
        $user = User::where('email', $request->email)
            ->orWhere('username', $request->email)
            ->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        // Check password
        $password = $user->password_hash ?? $user->password;
        if (!Hash::check($request->password, $password)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        // Only allow PATIENT role users
        if ($user->role !== 'PATIENT') {
            return back()->withErrors([
                'email' => 'You do not have access to this portal. Please use the staff login.',
            ])->onlyInput('email');
        }

        // Check if user is active
        if ($user->status !== 'active') {
            return back()->withErrors([
                'email' => 'Your account is inactive. Please contact your healthcare provider.',
            ])->onlyInput('email');
        }

        // Log in the user
        Auth::login($user, $request->boolean('remember'));

        $request->session()->regenerate();

        return redirect()->intended(route('patient.dashboard'));
    }

    /**
     * Handle a client logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('client.login')->with('success', 'You have been logged out successfully.');
    }
}
