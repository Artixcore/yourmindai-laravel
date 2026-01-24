<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PatientProfile;
use App\Models\User;
use App\Models\Feedback;
use App\Models\PracticeProgression;
use App\Models\SessionReport;
use App\Models\HomeworkAssignment;
use App\Models\MoodLog;
use App\Models\SleepLog;
use App\Models\ExerciseLog;
use App\Models\RiskAssessment;
use App\Models\PsychometricAssessment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PatientTrackingController extends Controller
{
    public function overview(Request $request, PatientProfile $patient)
    {
        $user = $request->user();
        
        if (!$this->canAccessPatient($user, $patient)) {
            abort(403, 'Unauthorized access to patient data');
        }

        // Get date range (default last 30 days)
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Overall statistics
        $stats = [
            'homework' => [
                'assigned' => HomeworkAssignment::where('patient_id', $patient->id)->count(),
                'completed' => HomeworkAssignment::where('patient_id', $patient->id)
                    ->where('status', 'completed')->count(),
            ],
            'feedback' => [
                'total' => Feedback::where('patient_id', $patient->id)
                    ->whereBetween('feedback_date', [$startDate, $endDate])->count(),
                'avg_rating' => round(Feedback::where('patient_id', $patient->id)
                    ->whereBetween('feedback_date', [$startDate, $endDate])
                    ->whereNotNull('rating')->avg('rating'), 2),
            ],
            'practice_progression' => [
                'total' => PracticeProgression::where('patient_id', $patient->id)
                    ->whereBetween('progress_date', [$startDate, $endDate])->count(),
                'avg_progress' => round(PracticeProgression::where('patient_id', $patient->id)
                    ->whereBetween('progress_date', [$startDate, $endDate])
                    ->avg('progress_percentage'), 2),
            ],
            'mood_logs' => MoodLog::where('patient_id', $patient->id)
                ->whereBetween('log_date', [$startDate, $endDate])->count(),
            'sleep_logs' => SleepLog::where('patient_id', $patient->id)
                ->whereBetween('sleep_date', [$startDate, $endDate])->count(),
            'exercise_logs' => ExerciseLog::where('patient_id', $patient->id)
                ->whereBetween('exercise_date', [$startDate, $endDate])->count(),
            'session_reports' => SessionReport::where('patient_id', $patient->id)
                ->whereBetween('created_at', [$startDate, $endDate])->count(),
            'risk_assessments' => [
                'total' => RiskAssessment::where('patient_id', $patient->id)->count(),
                'latest' => RiskAssessment::where('patient_id', $patient->id)
                    ->latest('assessment_date')->first(),
            ],
            'psychometric' => [
                'assigned' => PsychometricAssessment::where('patient_profile_id', $patient->id)->count(),
                'completed' => PsychometricAssessment::where('patient_profile_id', $patient->id)
                    ->where('status', 'completed')->count(),
            ],
        ];

        // Recent activity summary (last 7 days)
        $recentActivity = $this->getRecentActivitySummary($patient->id, 7);

        // Compliance alerts
        $alerts = $this->getComplianceAlerts($patient);

        return view('doctor.patients.tracking.overview', compact(
            'patient',
            'stats',
            'recentActivity',
            'alerts',
            'startDate',
            'endDate'
        ));
    }

    public function timeline(Request $request, PatientProfile $patient)
    {
        $user = $request->user();
        
        if (!$this->canAccessPatient($user, $patient)) {
            abort(403, 'Unauthorized access to patient data');
        }

        $limit = $request->input('limit', 50);
        $activityType = $request->input('type'); // Filter by activity type

        $timeline = $this->getActivityTimeline($patient->id, $limit, $activityType);

        return view('doctor.patients.tracking.timeline', compact('patient', 'timeline', 'limit', 'activityType'));
    }

    public function compliance(Request $request, PatientProfile $patient)
    {
        $user = $request->user();
        
        if (!$this->canAccessPatient($user, $patient)) {
            abort(403, 'Unauthorized access to patient data');
        }

        // Date range (default last 30 days)
        $startDate = $request->input('start_date', Carbon::now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Homework compliance
        $homeworkStats = [
            'assigned' => HomeworkAssignment::where('patient_id', $patient->id)
                ->whereBetween('start_date', [$startDate, $endDate])->count(),
            'completed' => HomeworkAssignment::where('patient_id', $patient->id)
                ->whereBetween('start_date', [$startDate, $endDate])
                ->where('status', 'completed')->count(),
            'in_progress' => HomeworkAssignment::where('patient_id', $patient->id)
                ->whereBetween('start_date', [$startDate, $endDate])
                ->where('status', 'in_progress')->count(),
            'overdue' => HomeworkAssignment::where('patient_id', $patient->id)
                ->where('end_date', '<', now())
                ->where('status', '!=', 'completed')->count(),
        ];
        $homeworkStats['compliance_rate'] = $homeworkStats['assigned'] > 0 
            ? round(($homeworkStats['completed'] / $homeworkStats['assigned']) * 100, 2) 
            : 0;

        // Daily tracking compliance
        $daysInRange = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;
        
        $moodLogDays = MoodLog::where('patient_id', $patient->id)
            ->whereBetween('log_date', [$startDate, $endDate])
            ->distinct('log_date')
            ->count('log_date');
        
        $sleepLogDays = SleepLog::where('patient_id', $patient->id)
            ->whereBetween('sleep_date', [$startDate, $endDate])
            ->distinct('sleep_date')
            ->count('sleep_date');
        
        $exerciseLogDays = ExerciseLog::where('patient_id', $patient->id)
            ->whereBetween('exercise_date', [$startDate, $endDate])
            ->distinct('exercise_date')
            ->count('exercise_date');

        $trackingCompliance = [
            'mood' => [
                'days_logged' => $moodLogDays,
                'rate' => round(($moodLogDays / $daysInRange) * 100, 2),
            ],
            'sleep' => [
                'days_logged' => $sleepLogDays,
                'rate' => round(($sleepLogDays / $daysInRange) * 100, 2),
            ],
            'exercise' => [
                'days_logged' => $exerciseLogDays,
                'rate' => round(($exerciseLogDays / $daysInRange) * 100, 2),
            ],
        ];

        // Practice progression compliance
        $practiceStats = [
            'total' => PracticeProgression::where('patient_id', $patient->id)
                ->whereBetween('progress_date', [$startDate, $endDate])->count(),
            'completed' => PracticeProgression::where('patient_id', $patient->id)
                ->whereBetween('progress_date', [$startDate, $endDate])
                ->where('status', 'completed')->count(),
        ];
        $practiceStats['compliance_rate'] = $practiceStats['total'] > 0 
            ? round(($practiceStats['completed'] / $practiceStats['total']) * 100, 2) 
            : 0;

        // Weekly compliance trends
        $weeklyTrends = $this->getWeeklyComplianceTrends($patient->id, $startDate, $endDate);

        return view('doctor.patients.tracking.compliance', compact(
            'patient',
            'homeworkStats',
            'trackingCompliance',
            'practiceStats',
            'weeklyTrends',
            'startDate',
            'endDate',
            'daysInRange'
        ));
    }

    protected function canAccessPatient(User $user, PatientProfile $patient): bool
    {
        if ($user->role === 'admin') {
            return true;
        }
        
        if ($user->role === 'doctor') {
            return $patient->doctor_id === $user->id || 
                   $user->assignedDoctors()->where('doctor_id', $patient->doctor_id)->exists();
        }
        
        return false;
    }

    protected function getActivityTimeline($patientId, $limit = 50, $type = null)
    {
        $activities = collect();

        // Only get activities of specified type if filter is set
        if (!$type || $type == 'feedback') {
            $feedbacks = Feedback::where('patient_id', $patientId)
                ->latest('feedback_date')
                ->limit($limit)
                ->get()
                ->map(function($item) {
                    return [
                        'type' => 'feedback',
                        'date' => $item->feedback_date,
                        'data' => $item,
                        'icon' => 'message-circle',
                        'color' => 'blue',
                    ];
                });
            $activities = $activities->merge($feedbacks);
        }

        if (!$type || $type == 'homework') {
            $homework = HomeworkAssignment::where('patient_id', $patientId)
                ->latest('created_at')
                ->limit($limit)
                ->get()
                ->map(function($item) {
                    return [
                        'type' => 'homework',
                        'date' => $item->created_at,
                        'data' => $item,
                        'icon' => 'clipboard',
                        'color' => 'purple',
                    ];
                });
            $activities = $activities->merge($homework);
        }

        if (!$type || $type == 'mood') {
            $moodLogs = MoodLog::where('patient_id', $patientId)
                ->latest('log_date')
                ->limit($limit)
                ->get()
                ->map(function($item) {
                    return [
                        'type' => 'mood',
                        'date' => $item->log_date,
                        'data' => $item,
                        'icon' => 'smile',
                        'color' => 'yellow',
                    ];
                });
            $activities = $activities->merge($moodLogs);
        }

        if (!$type || $type == 'practice') {
            $progressions = PracticeProgression::where('patient_id', $patientId)
                ->latest('progress_date')
                ->limit($limit)
                ->get()
                ->map(function($item) {
                    return [
                        'type' => 'practice_progression',
                        'date' => $item->progress_date,
                        'data' => $item,
                        'icon' => 'trending-up',
                        'color' => 'green',
                    ];
                });
            $activities = $activities->merge($progressions);
        }

        return $activities->sortByDesc('date')->take($limit)->values();
    }

    protected function getRecentActivitySummary($patientId, $days = 7)
    {
        $startDate = Carbon::now()->subDays($days);

        return [
            'feedback_count' => Feedback::where('patient_id', $patientId)
                ->where('feedback_date', '>=', $startDate)->count(),
            'homework_completed' => HomeworkAssignment::where('patient_id', $patientId)
                ->where('status', 'completed')
                ->where('updated_at', '>=', $startDate)->count(),
            'mood_logs' => MoodLog::where('patient_id', $patientId)
                ->where('log_date', '>=', $startDate)->count(),
            'practice_progressions' => PracticeProgression::where('patient_id', $patientId)
                ->where('progress_date', '>=', $startDate)->count(),
        ];
    }

    protected function getComplianceAlerts($patient)
    {
        $alerts = [];

        // Check for overdue homework
        $overdueHomework = HomeworkAssignment::where('patient_id', $patient->id)
            ->where('end_date', '<', now())
            ->where('status', '!=', 'completed')
            ->count();
        
        if ($overdueHomework > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "{$overdueHomework} overdue homework assignment(s)",
                'action_url' => route('patients.homework.index', $patient->id),
            ];
        }

        // Check for no mood logs in last 7 days
        $recentMoodLogs = MoodLog::where('patient_id', $patient->id)
            ->where('log_date', '>=', Carbon::now()->subDays(7))
            ->count();
        
        if ($recentMoodLogs == 0) {
            $alerts[] = [
                'type' => 'info',
                'message' => 'No mood logs in the past 7 days',
                'action_url' => null,
            ];
        }

        // Check for pending psychometric assessments
        $pendingAssessments = PsychometricAssessment::where('patient_profile_id', $patient->id)
            ->where('status', 'pending')
            ->where('assigned_at', '<', Carbon::now()->subDays(7))
            ->count();
        
        if ($pendingAssessments > 0) {
            $alerts[] = [
                'type' => 'info',
                'message' => "{$pendingAssessments} psychometric assessment(s) pending for over 7 days",
                'action_url' => route('patients.psychometric.index', $patient->id),
            ];
        }

        return $alerts;
    }

    protected function getWeeklyComplianceTrends($patientId, $startDate, $endDate)
    {
        // Get homework completion by week
        $homeworkByWeek = HomeworkAssignment::where('patient_id', $patientId)
            ->whereBetween('start_date', [$startDate, $endDate])
            ->select(
                DB::raw('WEEK(start_date) as week'),
                DB::raw('YEAR(start_date) as year'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed')
            )
            ->groupBy('year', 'week')
            ->orderBy('year', 'asc')
            ->orderBy('week', 'asc')
            ->get();

        return $homeworkByWeek;
    }
}
