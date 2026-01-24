<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SessionReport;
use App\Models\PatientProfile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SessionReportController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', SessionReport::class);
        
        $query = SessionReport::with(['patient.user', 'createdByDoctor', 'session']);

        // Apply filters
        if ($request->filled('doctor_id')) {
            $query->where('created_by', $request->doctor_id);
        }

        if ($request->filled('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to);
        }

        if ($request->filled('finalized')) {
            if ($request->finalized == 'yes') {
                $query->whereNotNull('finalized_at');
            } else {
                $query->whereNull('finalized_at');
            }
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('summary', 'like', '%' . $request->search . '%')
                  ->orWhere('progress_notes', 'like', '%' . $request->search . '%');
            });
        }

        $reports = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get doctors for filter dropdown
        $doctors = User::where('role', 'doctor')->get();

        // Get patients for filter dropdown
        $patients = PatientProfile::with('user')->get();

        // Calculate statistics
        $stats = [
            'total' => SessionReport::count(),
            'finalized' => SessionReport::whereNotNull('finalized_at')->count(),
            'draft' => SessionReport::whereNull('finalized_at')->count(),
            'by_status' => [
                'draft' => SessionReport::where('status', 'draft')->count(),
                'completed' => SessionReport::where('status', 'completed')->count(),
                'reviewed' => SessionReport::where('status', 'reviewed')->count(),
            ],
            'this_week' => SessionReport::where('created_at', '>=', Carbon::now()->startOfWeek())->count(),
            'this_month' => SessionReport::whereMonth('created_at', Carbon::now()->month)->count(),
            'finalization_rate' => $this->calculateFinalizationRate(),
        ];

        return view('admin.session-reports.index', compact('reports', 'doctors', 'patients', 'stats'));
    }

    public function show($id)
    {
        $report = SessionReport::with([
            'patient.user',
            'createdByDoctor',
            'session'
        ])->findOrFail($id);
        
        $this->authorize('view', $report);

        // Get related reports for the same patient
        $relatedReports = SessionReport::where('patient_id', $report->patient_id)
            ->where('id', '!=', $report->id)
            ->with('createdByDoctor')
            ->latest('created_at')
            ->limit(5)
            ->get();

        return view('admin.session-reports.show', compact('report', 'relatedReports'));
    }

    public function analytics(Request $request)
    {
        // Date range filter
        $startDate = $request->input('start_date', Carbon::now()->subMonths(3)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Overall statistics
        $totalReports = SessionReport::whereBetween('created_at', [$startDate, $endDate])->count();
        $finalizedReports = SessionReport::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('finalized_at')
            ->count();
        $finalizationRate = $totalReports > 0 ? round(($finalizedReports / $totalReports) * 100, 2) : 0;

        // Reports per doctor
        $reportsByDoctor = SessionReport::whereBetween('created_at', [$startDate, $endDate])
            ->select('created_by', DB::raw('COUNT(*) as total'), DB::raw('SUM(CASE WHEN finalized_at IS NOT NULL THEN 1 ELSE 0 END) as finalized'))
            ->with('createdByDoctor')
            ->groupBy('created_by')
            ->orderBy('total', 'desc')
            ->get();

        // Weekly trends
        $weeklyTrends = SessionReport::whereBetween('created_at', [$startDate, $endDate])
            ->select(
                DB::raw('WEEK(created_at) as week'),
                DB::raw('YEAR(created_at) as year'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN finalized_at IS NOT NULL THEN 1 ELSE 0 END) as finalized')
            )
            ->groupBy('year', 'week')
            ->orderBy('year', 'asc')
            ->orderBy('week', 'asc')
            ->get();

        // Status distribution
        $statusDistribution = SessionReport::whereBetween('created_at', [$startDate, $endDate])
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->get();

        // Sharing statistics
        $sharingStats = [
            'shared_with_patient' => SessionReport::whereBetween('created_at', [$startDate, $endDate])
                ->where('shared_with_patient', true)->count(),
            'shared_with_parents' => SessionReport::whereBetween('created_at', [$startDate, $endDate])
                ->where('shared_with_parents', true)->count(),
            'shared_with_others' => SessionReport::whereBetween('created_at', [$startDate, $endDate])
                ->where('shared_with_others', true)->count(),
        ];

        // Average time to finalize (in days)
        $avgTimeToFinalize = SessionReport::whereBetween('created_at', [$startDate, $endDate])
            ->whereNotNull('finalized_at')
            ->selectRaw('AVG(DATEDIFF(finalized_at, created_at)) as avg_days')
            ->value('avg_days');
        $avgTimeToFinalize = round($avgTimeToFinalize ?? 0, 1);

        return view('admin.session-reports.analytics', compact(
            'totalReports',
            'finalizedReports',
            'finalizationRate',
            'reportsByDoctor',
            'weeklyTrends',
            'statusDistribution',
            'sharingStats',
            'avgTimeToFinalize',
            'startDate',
            'endDate'
        ));
    }

    private function calculateFinalizationRate()
    {
        $total = SessionReport::count();
        if ($total == 0) return 0;

        $finalized = SessionReport::whereNotNull('finalized_at')->count();
        
        return round(($finalized / $total) * 100, 2);
    }
}
