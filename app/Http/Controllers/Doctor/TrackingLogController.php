<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MoodLog;
use App\Models\SleepLog;
use App\Models\ExerciseLog;
use App\Models\PatientProfile;
use App\Models\User;
use Carbon\Carbon;

class TrackingLogController extends Controller
{
    /**
     * Show overview of all tracking logs for patient.
     */
    public function index(Request $request, $patientId)
    {
        $patient = PatientProfile::with('user')->findOrFail($patientId);
        
        // Check authorization
        if (!$this->canAccessPatient($request->user(), $patient)) {
            abort(403);
        }

        // Get date range (default last 30 days)
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $stats = [
            'mood_entries' => MoodLog::where('patient_id', $patientId)
                ->whereBetween('log_date', [$startDate, $endDate])
                ->count(),
            'sleep_entries' => SleepLog::where('patient_id', $patientId)
                ->whereBetween('sleep_date', [$startDate, $endDate])
                ->count(),
            'exercise_entries' => ExerciseLog::where('patient_id', $patientId)
                ->whereBetween('exercise_date', [$startDate, $endDate])
                ->count(),
            'avg_mood_rating' => MoodLog::where('patient_id', $patientId)
                ->whereBetween('log_date', [$startDate, $endDate])
                ->avg('mood_rating'),
            'avg_sleep_hours' => SleepLog::where('patient_id', $patientId)
                ->whereBetween('sleep_date', [$startDate, $endDate])
                ->avg('hours_slept'),
            'total_exercise_minutes' => ExerciseLog::where('patient_id', $patientId)
                ->whereBetween('exercise_date', [$startDate, $endDate])
                ->sum('duration_minutes'),
        ];

        return view('doctor.patients.tracking.overview', compact('patient', 'stats', 'startDate', 'endDate'));
    }

    /**
     * Show mood tracking logs.
     */
    public function mood(Request $request, $patientId)
    {
        $patient = PatientProfile::with('user')->findOrFail($patientId);
        
        // Check authorization
        if (!$this->canAccessPatient($request->user(), $patient)) {
            abort(403);
        }

        $moodLogs = MoodLog::where('patient_id', $patientId)
            ->with(['feedback', 'practiceProgressions'])
            ->orderBy('log_date', 'desc')
            ->orderBy('log_time', 'desc')
            ->paginate(30);

        return view('doctor.patients.tracking.mood', compact('patient', 'moodLogs'));
    }

    /**
     * Show sleep tracking logs.
     */
    public function sleep(Request $request, $patientId)
    {
        $patient = PatientProfile::with('user')->findOrFail($patientId);
        
        // Check authorization
        if (!$this->canAccessPatient($request->user(), $patient)) {
            abort(403);
        }

        $sleepLogs = SleepLog::where('patient_id', $patientId)
            ->with(['feedback', 'practiceProgressions'])
            ->orderBy('sleep_date', 'desc')
            ->paginate(30);

        return view('doctor.patients.tracking.sleep', compact('patient', 'sleepLogs'));
    }

    /**
     * Show exercise tracking logs.
     */
    public function exercise(Request $request, $patientId)
    {
        $patient = PatientProfile::with('user')->findOrFail($patientId);
        
        // Check authorization
        if (!$this->canAccessPatient($request->user(), $patient)) {
            abort(403);
        }

        $exerciseLogs = ExerciseLog::where('patient_id', $patientId)
            ->with(['feedback', 'practiceProgressions'])
            ->orderBy('exercise_date', 'desc')
            ->paginate(30);

        return view('doctor.patients.tracking.exercise', compact('patient', 'exerciseLogs'));
    }

    /**
     * Check if doctor can access this patient.
     */
    private function canAccessPatient($user, $patient)
    {
        return $user->isAdmin() || $user->id === $patient->doctor_id;
    }
}
