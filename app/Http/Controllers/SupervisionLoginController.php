<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class SupervisionLoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check() && strtolower((string) Auth::user()->role) === 'supervision') {
            return redirect()->route('supervision.dashboard');
        }
        return view('supervision.login');
    }

    public function login(Request $request)
    {
        $request->validate(['email' => 'required|string', 'password' => 'required|string']);

        $user = User::where('email', $request->email)->orWhere('username', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password_hash ?? $user->password)) {
            return back()->withErrors(['email' => 'The provided credentials do not match our records.'])->onlyInput('email');
        }

        if (strtolower((string) $user->role) !== 'supervision') {
            return back()->withErrors(['email' => 'You do not have access to the supervision portal.'])->onlyInput('email');
        }

        if (isset($user->status) && $user->status !== 'active') {
            return back()->withErrors(['email' => 'Your account is inactive.'])->onlyInput('email');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        return redirect()->intended(route('supervision.dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('supervision.login')->with('success', 'You have been logged out.');
    }
}
