<?php

namespace App\Http\Controllers;

use App\Models\Session;
use App\Models\Patient;
use Illuminate\Http\Request;

class DoctorSessionController extends Controller
{
    /**
     * Display a listing of all sessions for the doctor's patients.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Get all sessions for the doctor's patients
        $query = Session::with(['patient', 'doctor', 'days'])
            ->whereHas('patient', function($q) use ($user) {
                $q->where('doctor_id', $user->id);
            });
        
        // Filter by patient if provided
        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }
        
        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by date range if provided
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }
        
        $sessions = $query->orderBy('created_at', 'desc')->paginate(20);
        
        // Get all patients for filter dropdown
        $patients = Patient::where('doctor_id', $user->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
        
        return view('doctor.sessions.index', compact('sessions', 'patients'));
    }
}
