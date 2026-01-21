<?php

namespace App\Http\Controllers;

use App\Models\PatientProfile;
use App\Models\Patient;
use App\Models\Session;
use App\Models\PatientResource;
use App\Models\Task;
use App\Models\PatientPoints;
use App\Models\Appointment;
use App\Models\Assessment;
use Illuminate\Http\Request;

class PatientDashboardController extends Controller
{
    /**
     * Show patient dashboard
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Try to get patient profile (for JWT-based patients)
        $patientProfile = PatientProfile::where('user_id', $user->id)->with('doctor')->first();
        
        // Try to get patient model (for Sanctum-based patients)
        $patient = Patient::where('email', $user->email)->with('doctor')->first();
        
        // Use whichever exists
        $patientData = $patientProfile ?? $patient;
        
        if (!$patientData) {
            return redirect()->route('dashboard')->with('error', 'Patient profile not found.');
        }
        
        // Get patient ID (handle both models)
        $patientId = $patientProfile ? $patientProfile->id : $patient->id;
        
        // Get sessions
        $sessions = Session::where('patient_id', $patientId)
            ->with(['doctor', 'days'])
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Get resources
        $resources = PatientResource::where('patient_id', $patientId)
            ->with(['session', 'sessionDay'])
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();
        
        // Get tasks
        $tasks = Task::where('patient_id', $patientId)
            ->where('visible_to_patient', true)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Get points
        $points = PatientPoints::where('user_id', $user->id)->first();
        $totalPoints = $points ? $points->total_points : 0;
        
        // Get upcoming appointments
        $upcomingAppointments = Appointment::where('patient_id', $patientId)
            ->where('appointment_date', '>=', now())
            ->orderBy('appointment_date', 'asc')
            ->take(3)
            ->get();
        
        // Get pending assessments
        $pendingAssessments = Assessment::where('patient_id', $patientId)
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
        
        // Calculate stats
        $stats = [
            'total_sessions' => Session::where('patient_id', $patientId)->count(),
            'completed_tasks' => Task::where('patient_id', $patientId)
                ->where('status', 'completed')
                ->where('visible_to_patient', true)
                ->count(),
            'total_resources' => PatientResource::where('patient_id', $patientId)->count(),
            'total_points' => $totalPoints,
        ];
        
        return view('patient.dashboard', compact(
            'patientData',
            'sessions',
            'resources',
            'tasks',
            'stats',
            'upcomingAppointments',
            'pendingAssessments',
            'points'
        ));
    }

    /**
     * Update patient profile
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'phone' => 'sometimes|nullable|string|max:20',
            'current_password' => 'required_with:password',
            'password' => 'sometimes|nullable|min:8|confirmed',
        ]);

        // Update user
        if ($request->has('name')) {
            $user->name = $request->name;
        }
        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }
        
        // Update password if provided
        if ($request->filled('password')) {
            if (!\Hash::check($request->current_password, $user->password_hash ?? $user->password)) {
                return back()->withErrors(['current_password' => 'Current password is incorrect.']);
            }
            $user->password_hash = \Hash::make($request->password);
        }
        
        $user->save();
        
        return redirect()->route('patient.profile')->with('success', 'Profile updated successfully.');
    }
}
