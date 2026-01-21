<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the dashboard.
     */
    public function index()
    {
        $user = auth()->user();
        $role = $user->role;

        // Redirect patients to their dedicated dashboard
        if ($role === 'PATIENT' || strtolower($role) === 'patient') {
            return redirect()->route('patient.dashboard');
        }

        return view('dashboard.index', compact('role'));
    }
}
