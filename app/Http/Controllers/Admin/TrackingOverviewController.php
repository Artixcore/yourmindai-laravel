<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MoodLog;
use App\Models\SleepLog;
use App\Models\ExerciseLog;
use App\Models\PatientProfile;
use App\Models\Feedback;
use App\Models\PracticeProgression;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TrackingOverviewController extends Controller
{
    /**
     * Show system-wide tracking overview.
     */
    public function index(Request $request)
    {
        // Date range
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        $stats = [
            'mood_logs' => [
                'total' => MoodLog::whereBetween('log_date', [$startDate, $endDate])->count(),
                'avg_rating' => MoodLog::whereBetween('log_date', [$startDate, $endDate])->avg('mood_rating'),
                'patients_logging' => MoodLog::whereBetween('log_date', [$startDate, $endDate])
                    ->distinct('patient_id')->count('patient_id'),
            ],
            'sleep_logs' => [
                'total' => SleepLog::whereBetween('sleep_date', [$startDate, $endDate])->count(),
                'avg_hours' => SleepLog::whereBetween('sleep_date', [$startDate, $endDate])->avg('hours_slept'),
                'avg_quality' => SleepLog::whereBetween('sleep_date', [$startDate, $endDate])->avg('sleep_quality'),
                'patients_logging' => SleepLog::whereBetween('sleep_date', [$startDate, $endDate])
                    ->distinct('patient_id')->count('patient_id'),
            ],
            'exercise_logs' => [
                'total' => ExerciseLog::whereBetween('exercise_date', [$startDate, $endDate])->count(),
                'total_minutes' => ExerciseLog::whereBetween('exercise_date', [$startDate, $endDate])
                    ->sum('duration_minutes'),
                'avg_duration' => ExerciseLog::whereBetween('exercise_date', [$startDate, $endDate])
                    ->avg('duration_minutes'),
                'patients_logging' => ExerciseLog::whereBetween('exercise_date', [$startDate, $endDate])
                    ->distinct('patient_id')->count('patient_id'),
            ],
        ];

        // Top logging patients
        $topLoggers = [
            'mood' => $this->getTopLoggers('mood', $startDate, $endDate, 10),
            'sleep' => $this->getTopLoggers('sleep', $startDate, $endDate, 10),
            'exercise' => $this->getTopLoggers('exercise', $startDate, $endDate, 10),
        ];

        return view('admin.tracking.overview', compact('stats', 'topLoggers', 'startDate', 'endDate'));
    }

    /**
     * Show mood logs system-wide.
     */
    public function mood(Request $request)
    {
        $moodLogs = MoodLog::with(['patient.user'])
            ->orderBy('log_date', 'desc')
            ->orderBy('log_time', 'desc')
            ->paginate(100);

        return view('admin.tracking.mood', compact('moodLogs'));
    }

    /**
     * Show sleep logs system-wide.
     */
    public function sleep(Request $request)
    {
        $sleepLogs = SleepLog::with(['patient.user'])
            ->orderBy('sleep_date', 'desc')
            ->paginate(100);

        return view('admin.tracking.sleep', compact('sleepLogs'));
    }

    /**
     * Show exercise logs system-wide.
     */
    public function exercise(Request $request)
    {
        $exerciseLogs = ExerciseLog::with(['patient.user'])
            ->orderBy('exercise_date', 'desc')
            ->paginate(100);

        return view('admin.tracking.exercise', compact('exerciseLogs'));
    }

    /**
     * Comprehensive view of all tracking across all patients.
     */
    public function allTracking(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        
        // Get all tracking activities
        $allActivities = collect();
        
        // Add mood logs
        $moodLogs = MoodLog::whereBetween('log_date', [$startDate, $endDate])
            ->with('patient.user')
            ->get()
            ->map(function($log) {
                return [
                    'type' => 'mood',
                    'date' => $log->log_date,
                    'patient_id' => $log->patient_id,
                    'patient_name' => $log->patient->user->full_name ?? 'N/A',
                    'data' => $log,
                ];
            });
        $allActivities = $allActivities->merge($moodLogs);
        
        // Add sleep logs
        $sleepLogs = SleepLog::whereBetween('sleep_date', [$startDate, $endDate])
            ->with('patient.user')
            ->get()
            ->map(function($log) {
                return [
                    'type' => 'sleep',
                    'date' => $log->sleep_date,
                    'patient_id' => $log->patient_id,
                    'patient_name' => $log->patient->user->full_name ?? 'N/A',
                    'data' => $log,
                ];
            });
        $allActivities = $allActivities->merge($sleepLogs);
        
        // Add exercise logs
        $exerciseLogs = ExerciseLog::whereBetween('exercise_date', [$startDate, $endDate])
            ->with('patient.user')
            ->get()
            ->map(function($log) {
                return [
                    'type' => 'exercise',
                    'date' => $log->exercise_date,
                    'patient_id' => $log->patient_id,
                    'patient_name' => $log->patient->user->full_name ?? 'N/A',
                    'data' => $log,
                ];
            });
        $allActivities = $allActivities->merge($exerciseLogs);
        
        // Sort by date descending
        $allActivities = $allActivities->sortByDesc('date')->take(200);
        
        return view('admin.tracking.all', compact('allActivities', 'startDate', 'endDate'));
    }

    /**
     * Filter tracking by specific type.
     */
    public function trackingByType(Request $request)
    {
        $type = $request->input('type', 'mood'); // mood, sleep, exercise
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        
        $logs = match($type) {
            'mood' => MoodLog::whereBetween('log_date', [$startDate, $endDate])
                ->with('patient.user')
                ->orderBy('log_date', 'desc')
                ->paginate(50),
            'sleep' => SleepLog::whereBetween('sleep_date', [$startDate, $endDate])
                ->with('patient.user')
                ->orderBy('sleep_date', 'desc')
                ->paginate(50),
            'exercise' => ExerciseLog::whereBetween('exercise_date', [$startDate, $endDate])
                ->with('patient.user')
                ->orderBy('exercise_date', 'desc')
                ->paginate(50),
            default => collect(),
        };
        
        return view('admin.tracking.by-type', compact('logs', 'type', 'startDate', 'endDate'));
    }

    /**
     * System-wide compliance metrics.
     */
    public function complianceReport(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        $daysInRange = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
        
        // Total active patients
        $totalPatients = PatientProfile::count();
        
        // Tracking compliance rates
        $complianceMetrics = [
            'mood' => [
                'patients_logging' => MoodLog::whereBetween('log_date', [$startDate, $endDate])
                    ->distinct('patient_id')->count(),
                'total_logs' => MoodLog::whereBetween('log_date', [$startDate, $endDate])->count(),
                'avg_logs_per_patient' => 0,
                'compliance_rate' => 0,
            ],
            'sleep' => [
                'patients_logging' => SleepLog::whereBetween('sleep_date', [$startDate, $endDate])
                    ->distinct('patient_id')->count(),
                'total_logs' => SleepLog::whereBetween('sleep_date', [$startDate, $endDate])->count(),
                'avg_logs_per_patient' => 0,
                'compliance_rate' => 0,
            ],
            'exercise' => [
                'patients_logging' => ExerciseLog::whereBetween('exercise_date', [$startDate, $endDate])
                    ->distinct('patient_id')->count(),
                'total_logs' => ExerciseLog::whereBetween('exercise_date', [$startDate, $endDate])->count(),
                'avg_logs_per_patient' => 0,
                'compliance_rate' => 0,
            ],
        ];
        
        // Calculate averages and rates
        foreach ($complianceMetrics as $type => &$metrics) {
            if ($metrics['patients_logging'] > 0) {
                $metrics['avg_logs_per_patient'] = round($metrics['total_logs'] / $metrics['patients_logging'], 2);
                $metrics['compliance_rate'] = round(($metrics['patients_logging'] / $totalPatients) * 100, 2);
            }
        }
        
        // Practice progression compliance
        $progressionMetrics = [
            'total' => PracticeProgression::whereBetween('progress_date', [$startDate, $endDate])->count(),
            'completed' => PracticeProgression::whereBetween('progress_date', [$startDate, $endDate])
                ->where('status', 'completed')->count(),
            'compliance_rate' => 0,
        ];
        if ($progressionMetrics['total'] > 0) {
            $progressionMetrics['compliance_rate'] = round(($progressionMetrics['completed'] / $progressionMetrics['total']) * 100, 2);
        }
        
        // Feedback engagement
        $feedbackMetrics = [
            'total' => Feedback::whereBetween('feedback_date', [$startDate, $endDate])->count(),
            'by_source' => Feedback::whereBetween('feedback_date', [$startDate, $endDate])
                ->select('source', DB::raw('COUNT(*) as count'))
                ->groupBy('source')
                ->pluck('count', 'source')
                ->toArray(),
        ];
        
        return view('admin.tracking.compliance', compact(
            'complianceMetrics',
            'progressionMetrics',
            'feedbackMetrics',
            'totalPatients',
            'daysInRange',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Compare tracking patterns across patients.
     */
    public function patientComparison(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));
        
        // Get patient tracking stats
        $patientStats = PatientProfile::with('user')
            ->get()
            ->map(function($patient) use ($startDate, $endDate) {
                return [
                    'patient' => $patient,
                    'mood_logs' => MoodLog::where('patient_id', $patient->id)
                        ->whereBetween('log_date', [$startDate, $endDate])->count(),
                    'sleep_logs' => SleepLog::where('patient_id', $patient->id)
                        ->whereBetween('sleep_date', [$startDate, $endDate])->count(),
                    'exercise_logs' => ExerciseLog::where('patient_id', $patient->id)
                        ->whereBetween('exercise_date', [$startDate, $endDate])->count(),
                    'total_logs' => 0,
                ];
            })
            ->map(function($stats) {
                $stats['total_logs'] = $stats['mood_logs'] + $stats['sleep_logs'] + $stats['exercise_logs'];
                return $stats;
            })
            ->sortByDesc('total_logs')
            ->take(50);
        
        return view('admin.tracking.patient-comparison', compact('patientStats', 'startDate', 'endDate'));
    }

    /**
     * Get top loggers for a specific type.
     */
    private function getTopLoggers($type, $startDate, $endDate, $limit = 10)
    {
        $model = match($type) {
            'mood' => MoodLog::class,
            'sleep' => SleepLog::class,
            'exercise' => ExerciseLog::class,
        };

        $dateColumn = match($type) {
            'mood' => 'log_date',
            'sleep' => 'sleep_date',
            'exercise' => 'exercise_date',
        };

        return $model::whereBetween($dateColumn, [$startDate, $endDate])
            ->selectRaw('patient_id, COUNT(*) as log_count')
            ->groupBy('patient_id')
            ->orderByDesc('log_count')
            ->take($limit)
            ->get();
    }
}
