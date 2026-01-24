<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\MoodLog;
use App\Models\SleepLog;
use App\Models\ExerciseLog;
use App\Models\PatientProfile;
use Carbon\Carbon;

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
            ->with('patient.user')
            ->get();
    }
}
