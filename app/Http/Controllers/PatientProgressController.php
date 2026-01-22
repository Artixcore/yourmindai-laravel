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
     * Get progress data for authenticated patient
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $patientProfile = PatientProfile::where('user_id', $user->id)->first();
        $patient = Patient::where('email', $user->email)->first();
        $patientData = $patientProfile ?? $patient;
        
        if (!$patientData) {
            // Check if this is an API request
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error' => 'Patient profile not found.',
                ], 404);
            }
            
            return redirect()->route('patient.dashboard')
                ->with('error', 'Patient profile not found.');
        }
        
        $patientId = $patientProfile ? $patientProfile->id : $patient->id;
        
        // Get mood trends from journal entries (last 30 days)
        $moodTrends = PatientJournalEntry::where('patient_id', $patientId)
            ->where('entry_date', '>=', now()->subDays(30))
            ->whereNotNull('mood_score')
            ->orderBy('entry_date', 'asc')
            ->get(['entry_date', 'mood_score'])
            ->map(function ($entry) {
                return [
                    'date' => $entry->entry_date->format('Y-m-d'),
                    'mood' => $entry->mood_score,
                ];
            });
        
        // Get task completion rates
        $totalTasks = Task::where('patient_id', $patientId)
            ->where('visible_to_patient', true)
            ->count();
        
        $completedTasks = Task::where('patient_id', $patientId)
            ->where('visible_to_patient', true)
            ->where('status', 'completed')
            ->count();
        
        $taskCompletionRate = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;
        
        // Get session attendance
        $totalSessions = Session::where('patient_id', $patientId)->count();
        
        // Get stats
        $stats = [
            'mood_trends' => $moodTrends,
            'task_completion_rate' => round($taskCompletionRate, 2),
            'total_tasks' => $totalTasks,
            'completed_tasks' => $completedTasks,
            'total_sessions' => $totalSessions,
            'average_mood_score' => PatientJournalEntry::where('patient_id', $patientId)
                ->where('entry_date', '>=', now()->subDays(30))
                ->whereNotNull('mood_score')
                ->avg('mood_score'),
        ];
        
        // Return JSON for API requests
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => $stats,
            ]);
        }
        
        // Return view for web requests
        return view('patient.progress.index', compact('stats'));
    }
}
