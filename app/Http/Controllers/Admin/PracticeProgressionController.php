<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PracticeProgression;
use App\Models\PatientProfile;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PracticeProgressionController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', PracticeProgression::class);
        
        $query = PracticeProgression::with(['patient.user', 'monitoredByUser', 'progressionable']);

        // Apply filters
        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

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

        $progressions = $query->orderBy('progress_date', 'desc')->paginate(20);

        // Get patients for filter dropdown
        $patients = PatientProfile::with('user')->get();

        // Get unique progressionable types
        $progressionableTypes = PracticeProgression::select('progressionable_type')
            ->distinct()
            ->pluck('progressionable_type');

        // Calculate statistics
        $stats = [
            'total' => PracticeProgression::count(),
            'by_status' => [
                'not_started' => PracticeProgression::where('status', 'not_started')->count(),
                'in_progress' => PracticeProgression::where('status', 'in_progress')->count(),
                'completed' => PracticeProgression::where('status', 'completed')->count(),
                'skipped' => PracticeProgression::where('status', 'skipped')->count(),
            ],
            'avg_progress' => round(PracticeProgression::avg('progress_percentage'), 2),
            'completion_rate' => $this->calculateCompletionRate(),
            'this_week' => PracticeProgression::where('progress_date', '>=', Carbon::now()->startOfWeek())->count(),
        ];

        return view('admin.practice-progressions.index', compact('progressions', 'patients', 'progressionableTypes', 'stats'));
    }

    public function show($id)
    {
        $progression = PracticeProgression::with([
            'patient.user',
            'monitoredByUser',
            'progressionable'
        ])->findOrFail($id);
        
        $this->authorize('view', $progression);

        // Get progression history for the same progressionable item
        $history = PracticeProgression::where('progressionable_type', $progression->progressionable_type)
            ->where('progressionable_id', $progression->progressionable_id)
            ->where('patient_id', $progression->patient_id)
            ->orderBy('progress_date', 'asc')
            ->get();

        return view('admin.practice-progressions.show', compact('progression', 'history'));
    }

    public function analytics(Request $request)
    {
        // Date range filter
        $startDate = $request->input('start_date', Carbon::now()->subMonths(3)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Overall statistics
        $totalProgressions = PracticeProgression::whereBetween('progress_date', [$startDate, $endDate])->count();
        $avgProgress = round(PracticeProgression::whereBetween('progress_date', [$startDate, $endDate])->avg('progress_percentage'), 2);
        $completionRate = $this->calculateCompletionRate($startDate, $endDate);

        // Progress trends by week
        $weeklyTrends = PracticeProgression::whereBetween('progress_date', [$startDate, $endDate])
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
        $statusDistribution = PracticeProgression::whereBetween('progress_date', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        // Top performing patients (highest avg progress)
        $topPatients = PracticeProgression::whereBetween('progress_date', [$startDate, $endDate])
            ->select('patient_id', DB::raw('AVG(progress_percentage) as avg_progress'), DB::raw('COUNT(*) as total'))
            ->with('patient.user')
            ->groupBy('patient_id')
            ->orderBy('avg_progress', 'desc')
            ->limit(10)
            ->get();

        // Compliance by monitoring source
        $monitoringSourceStats = PracticeProgression::whereBetween('progress_date', [$startDate, $endDate])
            ->select('monitored_by', DB::raw('COUNT(*) as count'), DB::raw('AVG(progress_percentage) as avg_progress'))
            ->groupBy('monitored_by')
            ->get();

        return view('admin.practice-progressions.analytics', compact(
            'totalProgressions',
            'avgProgress',
            'completionRate',
            'weeklyTrends',
            'statusDistribution',
            'topPatients',
            'monitoringSourceStats',
            'startDate',
            'endDate'
        ));
    }

    public function export(Request $request)
    {
        $query = PracticeProgression::with(['patient.user', 'monitoredByUser', 'progressionable']);

        // Apply same filters as index
        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('progress_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('progress_date', '<=', $request->date_to);
        }

        $progressions = $query->orderBy('progress_date', 'desc')->get();

        // Generate CSV
        $filename = 'practice_progressions_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($progressions) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'ID',
                'Date',
                'Patient',
                'Progressionable Type',
                'Status',
                'Progress %',
                'Monitored By',
                'Monitor User',
                'Notes'
            ]);

            // Add data rows
            foreach ($progressions as $progression) {
                fputcsv($file, [
                    $progression->id,
                    $progression->progress_date->format('Y-m-d'),
                    $progression->patient->user->full_name ?? 'N/A',
                    class_basename($progression->progressionable_type),
                    $progression->status,
                    $progression->progress_percentage,
                    $progression->monitored_by,
                    $progression->monitoredByUser->full_name ?? 'N/A',
                    $progression->notes ?? ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function calculateCompletionRate($startDate = null, $endDate = null)
    {
        $query = PracticeProgression::query();
        
        if ($startDate && $endDate) {
            $query->whereBetween('progress_date', [$startDate, $endDate]);
        }

        $total = $query->count();
        if ($total == 0) return 0;

        $completed = $query->where('status', 'completed')->count();
        
        return round(($completed / $total) * 100, 2);
    }
}
