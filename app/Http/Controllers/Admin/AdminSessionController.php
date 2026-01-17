<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Http\Request;

class AdminSessionController extends Controller
{
    public function index(Request $request)
    {
        $query = Session::with(['doctor', 'patient', 'days']);
        
        // Filters
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }
        
        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }
        
        $sessions = $query->latest()->paginate(20);
        $doctors = User::where('role', 'doctor')->where('status', 'active')->get();
        $patients = Patient::where('status', 'active')->get();
        
        return view('admin.sessions.index', compact('sessions', 'doctors', 'patients'));
    }
    
    public function show(Session $session)
    {
        $session->load(['doctor', 'patient', 'days']);
        
        return view('admin.sessions.show', compact('session'));
    }
}
