<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\User;
use App\Services\PatientTransferService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AdminPatientController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::with('doctor');
        
        // Filters
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('created_from')) {
            $query->where('created_at', '>=', $request->created_from);
        }
        
        if ($request->filled('created_to')) {
            $query->where('created_at', '<=', $request->created_to);
        }
        
        $patients = $query->latest()->paginate(20);
        $doctors = User::where('role', 'doctor')->where('status', 'active')->get();
        
        return view('admin.patients.index', compact('patients', 'doctors'));
    }
    
    public function show(Patient $patient)
    {
        $patient->load(['doctor', 'sessions.days', 'resources']);
        
        $sessionsSummary = [
            'total' => $patient->sessions()->count(),
            'active' => $patient->sessions()->where('status', 'active')->count(),
            'closed' => $patient->sessions()->where('status', 'closed')->count(),
        ];
        
        $recentActivity = $patient->sessions()
            ->with('days')
            ->latest()
            ->limit(5)
            ->get();

        $doctors = User::where('role', 'doctor')->where('status', 'active')->orderBy('name')->get();
        
        return view('admin.patients.show', compact('patient', 'sessionsSummary', 'recentActivity', 'doctors'));
    }

    /**
     * Transfer patient to another doctor. Admin only.
     */
    public function transfer(Request $request, Patient $patient)
    {
        $validated = $request->validate([
            'new_doctor_id' => 'required|exists:users,id',
            'reason' => 'nullable|string|max:1000',
        ]);

        $actor = $request->user();
        if ($actor->role !== 'admin') {
            abort(403, 'Only administrators can transfer patients.');
        }

        try {
            app(PatientTransferService::class)->transfer(
                $patient,
                (int) $validated['new_doctor_id'],
                $actor,
                $validated['reason'] ?? null
            );
        } catch (\InvalidArgumentException $e) {
            throw ValidationException::withMessages(['new_doctor_id' => $e->getMessage()]);
        }

        return redirect()
            ->route('admin.patients.show', $patient)
            ->with('success', 'Patient has been transferred to the selected doctor.');
    }
}
