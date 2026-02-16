<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MoodLog;
use App\Models\SleepLog;
use App\Models\ExerciseLog;
use App\Models\Patient;
use App\Models\PatientProfile;
use App\Models\User;
use Carbon\Carbon;

class TrackingLogController extends Controller
{
    private function resolvePatientProfile(Patient $patient): PatientProfile
    {
        $profile = $patient->resolvePatientProfile();
        if (!$profile) {
            abort(404, 'Patient profile not found for this patient.');
        }
        return $profile;
    }

    /**
     * Show overview of all tracking logs for patient.
     */
    public function index(Request $request, Patient $patient)
    {
        $patientProfile = $this->resolvePatientProfile($patient);
        
        // Check authorization
        if (!$this->canAccessPatient($request->user(), $patientProfile)) {
            abort(403);
        }

        // Get date range (default last 30 days)
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $stats = [
            'mood_entries' => MoodLog::where('patient_id', $patientProfile->id)
                ->whereBetween('log_date', [$startDate, $endDate])
                ->count(),
            'sleep_entries' => SleepLog::where('patient_id', $patientProfile->id)
                ->whereBetween('sleep_date', [$startDate, $endDate])
                ->count(),
            'exercise_entries' => ExerciseLog::where('patient_id', $patientProfile->id)
                ->whereBetween('exercise_date', [$startDate, $endDate])
                ->count(),
            'avg_mood_rating' => MoodLog::where('patient_id', $patientProfile->id)
                ->whereBetween('log_date', [$startDate, $endDate])
                ->avg('mood_rating'),
            'avg_sleep_hours' => SleepLog::where('patient_id', $patientProfile->id)
                ->whereBetween('sleep_date', [$startDate, $endDate])
                ->avg('hours_slept'),
            'total_exercise_minutes' => ExerciseLog::where('patient_id', $patientProfile->id)
                ->whereBetween('exercise_date', [$startDate, $endDate])
                ->sum('duration_minutes'),
        ];

        return view('doctor.patients.tracking.overview', compact('patient', 'patientProfile', 'stats', 'startDate', 'endDate'));
    }

    /**
     * Show mood tracking logs.
     */
    public function mood(Request $request, Patient $patient)
    {
        $patientProfile = $this->resolvePatientProfile($patient);
        
        // Check authorization
        if (!$this->canAccessPatient($request->user(), $patientProfile)) {
            abort(403);
        }

        $moodLogs = MoodLog::where('patient_id', $patientProfile->id)
            ->with(['feedback', 'practiceProgressions'])
            ->orderBy('log_date', 'desc')
            ->orderBy('log_time', 'desc')
            ->paginate(30);

        return view('doctor.patients.tracking.mood', compact('patient', 'patientProfile', 'moodLogs'));
    }

    /**
     * Show sleep tracking logs.
     */
    public function sleep(Request $request, Patient $patient)
    {
        $patientProfile = $this->resolvePatientProfile($patient);
        
        // Check authorization
        if (!$this->canAccessPatient($request->user(), $patientProfile)) {
            abort(403);
        }

        $sleepLogs = SleepLog::where('patient_id', $patientProfile->id)
            ->with(['feedback', 'practiceProgressions'])
            ->orderBy('sleep_date', 'desc')
            ->paginate(30);

        return view('doctor.patients.tracking.sleep', compact('patient', 'patientProfile', 'sleepLogs'));
    }

    /**
     * Show exercise tracking logs.
     */
    public function exercise(Request $request, Patient $patient)
    {
        $patientProfile = $this->resolvePatientProfile($patient);
        
        // Check authorization
        if (!$this->canAccessPatient($request->user(), $patientProfile)) {
            abort(403);
        }

        $exerciseLogs = ExerciseLog::where('patient_id', $patientProfile->id)
            ->with(['feedback', 'practiceProgressions'])
            ->orderBy('exercise_date', 'desc')
            ->paginate(30);

        return view('doctor.patients.tracking.exercise', compact('patient', 'patientProfile', 'exerciseLogs'));
    }

    /**
     * Check if doctor can access this patient.
     */
    private function canAccessPatient($user, $patient)
    {
        return $user->isAdmin() || $user->id === $patient->doctor_id;
    }
}
