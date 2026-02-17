<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class ParentLoginController extends Controller
{
    /**
     * Show the parent login form.
     */
    public function showLoginForm()
    {
        if (Auth::check() && strtolower((string) Auth::user()->role) === 'parent') {
            return redirect()->route('parent.dashboard');
        }

        return view('parent.login');
    }

    /**
     * Handle a parent login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)
            ->orWhere('username', $request->email)
            ->first();

        if (!$user) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        $password = $user->password_hash ?? $user->password;
        if (!Hash::check($request->password, $password)) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ])->onlyInput('email');
        }

        if (strtolower((string) $user->role) !== 'parent') {
            return back()->withErrors([
                'email' => 'You do not have access to the parent portal. Please use the correct login.',
            ])->onlyInput('email');
        }

        if (isset($user->status) && $user->status !== 'active') {
            return back()->withErrors([
                'email' => 'Your account is inactive. Please contact your healthcare provider.',
            ])->onlyInput('email');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('parent.dashboard'));
    }

    /**
     * Handle parent logout (redirects to parent login).
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('parent.login')->with('success', 'You have been logged out successfully.');
    }
}
