<?php

namespace App\Http\Controllers;

use App\Models\PatientProfile;
use App\Models\Patient;
use App\Models\Session;
use App\Models\PatientResource;
use App\Models\Task;
use App\Models\PatientPoints;
use App\Models\Appointment;
use App\Models\PsychometricAssessment;
use App\Models\PatientDevice;
use App\Models\ContingencyPlan;
use Illuminate\Http\Request;

class ClientDashboardController extends Controller
{
    /**
     * Show client dashboard (for webview app)
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
            return redirect()->route('client.login')->with('error', 'Patient profile not found.');
        }
        
        // Get patient ID (handle both models)
        $patientId = $patientProfile ? $patientProfile->id : $patient->id;
        $isPatientProfile = (bool)$patientProfile;
        
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
            ->where('date', '>=', now())
            ->orderBy('date', 'asc')
            ->take(3)
            ->get();
        
        // Get pending psychometric assessments
        $pendingAssessments = PsychometricAssessment::where($isPatientProfile ? 'patient_profile_id' : 'patient_id', $patientId)
            ->where('status', 'pending')
            ->with('scale')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();
        
        // Get active devices
        $devices = PatientDevice::where($isPatientProfile ? 'patient_profile_id' : 'patient_id', $patientId)
            ->where('is_active', true)
            ->orderBy('last_active_at', 'desc')
            ->get();
        
        // Get active contingency plans
        $contingencyPlans = ContingencyPlan::where($isPatientProfile ? 'patient_profile_id' : 'patient_id', $patientId)
            ->where('status', 'active')
            ->with('createdByDoctor')
            ->orderBy('created_at', 'desc')
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
            'active_devices' => $devices->count(),
            'pending_assessments' => $pendingAssessments->count(),
            'active_contingency_plans' => $contingencyPlans->count(),
        ];
        
        return view('client.dashboard', compact(
            'patientData',
            'sessions',
            'resources',
            'tasks',
            'stats',
            'upcomingAppointments',
            'pendingAssessments',
            'devices',
            'contingencyPlans',
            'isPatientProfile'
        ));
    }
}
