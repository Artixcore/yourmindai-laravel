<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PracticeProgression;
use App\Models\PatientProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PracticeProgressionController extends Controller
{
    public function index(Request $request, PatientProfile $patient)
    {
        $user = $request->user();
        
        if (!$this->canAccessPatient($user, $patient)) {
            abort(403, 'Unauthorized access to patient data');
        }

        $query = PracticeProgression::where('patient_id', $patient->id)
            ->with(['monitoredByUser', 'progressionable']);

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('monitored_by')) {
            $query->where('monitored_by', $request->monitored_by);
        }

        if ($request->filled('date_from')) {
            $query->where('progress_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('progress_date', '<=', $request->date_to);
        }

        if ($request->filled('progressionable_type')) {
            $query->where('progressionable_type', $request->progressionable_type);
        }

        $progressions = $query->orderBy('progress_date', 'desc')->paginate(15);

        // Get unique progressionable types for this patient
        $progressionableTypes = PracticeProgression::where('patient_id', $patient->id)
            ->select('progressionable_type')
            ->distinct()
            ->pluck('progressionable_type');

        // Calculate statistics
        $stats = [
            'total' => PracticeProgression::where('patient_id', $patient->id)->count(),
            'by_status' => [
                'not_started' => PracticeProgression::where('patient_id', $patient->id)->where('status', 'not_started')->count(),
                'in_progress' => PracticeProgression::where('patient_id', $patient->id)->where('status', 'in_progress')->count(),
                'completed' => PracticeProgression::where('patient_id', $patient->id)->where('status', 'completed')->count(),
                'skipped' => PracticeProgression::where('patient_id', $patient->id)->where('status', 'skipped')->count(),
            ],
            'avg_progress' => round(PracticeProgression::where('patient_id', $patient->id)->avg('progress_percentage'), 2),
            'completion_rate' => $this->calculateCompletionRate($patient->id),
        ];

        return view('doctor.patients.practice-progressions.index', compact('patient', 'progressions', 'progressionableTypes', 'stats'));
    }

    public function show(Request $request, PatientProfile $patient, PracticeProgression $progression)
    {
        $user = $request->user();
        
        if (!$this->canAccessPatient($user, $patient)) {
            abort(403, 'Unauthorized access to patient data');
        }

        // Verify progression belongs to this patient
        if ($progression->patient_id != $patient->id) {
            abort(404);
        }

        $progression->load(['monitoredByUser', 'progressionable']);

        // Get progression history for the same progressionable item
        $history = PracticeProgression::where('progressionable_type', $progression->progressionable_type)
            ->where('progressionable_id', $progression->progressionable_id)
            ->where('patient_id', $patient->id)
            ->orderBy('progress_date', 'asc')
            ->get();

        return view('doctor.patients.practice-progressions.show', compact('patient', 'progression', 'history'));
    }

    public function analytics(Request $request, PatientProfile $patient)
    {
        $user = $request->user();
        
        if (!$this->canAccessPatient($user, $patient)) {
            abort(403, 'Unauthorized access to patient data');
        }

        // Date range filter
        $startDate = $request->input('start_date', Carbon::now()->subMonths(3)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Overall statistics
        $totalProgressions = PracticeProgression::where('patient_id', $patient->id)
            ->whereBetween('progress_date', [$startDate, $endDate])
            ->count();
        
        $avgProgress = round(PracticeProgression::where('patient_id', $patient->id)
            ->whereBetween('progress_date', [$startDate, $endDate])
            ->avg('progress_percentage'), 2);
        
        $completionRate = $this->calculateCompletionRate($patient->id, $startDate, $endDate);

        // Weekly trends
        $weeklyTrends = PracticeProgression::where('patient_id', $patient->id)
            ->whereBetween('progress_date', [$startDate, $endDate])
            ->select(
                DB::raw('WEEK(progress_date) as week'),
                DB::raw('YEAR(progress_date) as year'),
                DB::raw('AVG(progress_percentage) as avg_progress'),
                DB::raw('COUNT(*) as total')
            )
            ->groupBy('year', 'week')
            ->orderBy('year', 'asc')
            ->orderBy('week', 'asc')
            ->get();

        // Status distribution
        $statusDistribution = PracticeProgression::where('patient_id', $patient->id)
            ->whereBetween('progress_date', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        // Progress by type
        $progressByType = PracticeProgression::where('patient_id', $patient->id)
            ->whereBetween('progress_date', [$startDate, $endDate])
            ->select(
                'progressionable_type',
                DB::raw('COUNT(*) as total'),
                DB::raw('AVG(progress_percentage) as avg_progress')
            )
            ->groupBy('progressionable_type')
            ->get();

        // Compliance by monitoring source
        $monitoringSourceStats = PracticeProgression::where('patient_id', $patient->id)
            ->whereBetween('progress_date', [$startDate, $endDate])
            ->select('monitored_by', DB::raw('COUNT(*) as count'), DB::raw('AVG(progress_percentage) as avg_progress'))
            ->groupBy('monitored_by')
            ->get();

        // Identify struggling areas (lowest avg progress)
        $strugglingAreas = PracticeProgression::where('patient_id', $patient->id)
            ->whereBetween('progress_date', [$startDate, $endDate])
            ->select(
                'progressionable_type',
                'progressionable_id',
                DB::raw('AVG(progress_percentage) as avg_progress'),
                DB::raw('COUNT(*) as attempts')
            )
            ->groupBy('progressionable_type', 'progressionable_id')
            ->orderBy('avg_progress', 'asc')
            ->limit(5)
            ->get();

        return view('doctor.patients.practice-progressions.analytics', compact(
            'patient',
            'totalProgressions',
            'avgProgress',
            'completionRate',
            'weeklyTrends',
            'statusDistribution',
            'progressByType',
            'monitoringSourceStats',
            'strugglingAreas',
            'startDate',
            'endDate'
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

    private function calculateCompletionRate($patientId, $startDate = null, $endDate = null)
    {
        $query = PracticeProgression::where('patient_id', $patientId);
        
        if ($startDate && $endDate) {
            $query->whereBetween('progress_date', [$startDate, $endDate]);
        }

        $total = $query->count();
        if ($total == 0) return 0;

        $completed = $query->where('status', 'completed')->count();
        
        return round(($completed / $total) * 100, 2);
    }
}
