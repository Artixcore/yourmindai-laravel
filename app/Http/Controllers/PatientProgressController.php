<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PatientProfile;
use App\Models\Task;
use App\Models\PatientJournalEntry;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientProgressController extends Controller
{
    /**
     * Check if called from client route
     */
    private function isClientRoute()
    {
        return request()->routeIs('client.*');
    }

    /**
     * Get appropriate dashboard route
     */
    private function getDashboardRoute()
    {
        return $this->isClientRoute() ? 'client.dashboard' : 'patient.dashboard';
    }

    /**
     * Get progress data for authenticated patient
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $patientProfile = PatientProfile::where('user_id', $user->id)->first();
        $patient = Patient::where('email', $user->email)->first();
        // Need at least one: Task/Goal use patient_profiles.id; Journal and Session use patients.id
        if (!$patientProfile && !$patient) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error' => 'Patient profile not found.',
                ], 404);
            }
            return redirect()->route($this->getDashboardRoute())
                ->with('error', 'Patient profile not found.');
        }

        $patientProfileId = $patientProfile?->id;   // for Task (patient_profiles.id)
        $patientId = $patient?->id;                // for PatientJournalEntry and Session (patients.id)

        // Get mood trends from journal entries (last 30 days) – uses patients.id
        $moodTrends = $patientId
            ? PatientJournalEntry::where('patient_id', $patientId)
                ->where('entry_date', '>=', now()->subDays(30))
                ->whereNotNull('mood_score')
                ->orderBy('entry_date', 'asc')
                ->get(['entry_date', 'mood_score'])
                ->map(function ($entry) {
                    return [
                        'date' => $entry->entry_date->format('Y-m-d'),
                        'mood' => $entry->mood_score,
                    ];
                })
            : collect();

        // Task completion rates – Task uses patient_profiles.id
        $totalTasks = $patientProfileId ? Task::where('patient_id', $patientProfileId)->where('visible_to_patient', true)->count() : 0;
        $completedTasks = $patientProfileId ? Task::where('patient_id', $patientProfileId)->where('visible_to_patient', true)->where('status', 'completed')->count() : 0;
        $taskCompletionRate = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;

        // Session attendance – Session uses patients.id
        $totalSessions = $patientId ? Session::where('patient_id', $patientId)->count() : 0;

        $stats = [
            'mood_trends' => $moodTrends,
            'task_completion_rate' => round($taskCompletionRate, 2),
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'total_sessions' => $totalSessions,
            'average_mood_score' => $patientId
                ? PatientJournalEntry::where('patient_id', $patientId)
                    ->where('entry_date', '>=', now()->subDays(30))
                    ->whereNotNull('mood_score')
                    ->avg('mood_score')
                : null,
        ];
        
        // Return JSON for API requests
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        }
        
        // Return appropriate view based on route
        $view = $this->isClientRoute() ? 'client.progress.index' : 'patient.progress.index';
        return view($view, compact('stats'));
    }
}
