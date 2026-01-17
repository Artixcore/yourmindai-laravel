<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Session;
use App\Models\SessionDay;
use App\Models\User;
use App\Models\PatientResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    public function index()
    {
        // KPIs
        $totalDoctors = User::where('role', 'doctor')->where('status', 'active')->count();
        $totalAssistants = User::where('role', 'assistant')->where('status', 'active')->count();
        $totalPatients = Patient::where('status', 'active')->count();
        $activeSessions = Session::where('status', 'active')->count();
        $closedSessions = Session::where('status', 'closed')->count();
        
        // This week/month
        $sessionsThisWeek = Session::where('created_at', '>=', now()->startOfWeek())->count();
        $sessionsThisMonth = Session::where('created_at', '>=', now()->startOfMonth())->count();
        $resourcesThisWeek = PatientResource::where('created_at', '>=', now()->startOfWeek())->count();
        $resourcesThisMonth = PatientResource::where('created_at', '>=', now()->startOfMonth())->count();
        
        // Sessions over time (last 12 months)
        $sessionsOverTime = Session::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('COUNT(*) as count')
        )
        ->where('created_at', '>=', now()->subMonths(12))
        ->groupBy('month')
        ->orderBy('month')
        ->get();
        
        // Per-doctor caseload
        $doctorCaseloads = User::where('role', 'doctor')
            ->withCount('patients')
            ->orderBy('patients_count', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($doctor) {
                return [
                    'name' => $doctor->full_name ?? $doctor->name ?? 'Unknown',
                    'count' => $doctor->patients_count,
                ];
            });
        
        // Attention flags (keyword-based)
        $attentionFlags = SessionDay::whereNotNull('alerts')
            ->where(function ($q) {
                $q->where('alerts', 'like', '%self-harm%')
                  ->orWhere('alerts', 'like', '%panic%')
                  ->orWhere('alerts', 'like', '%relapse%');
            })
            ->count();
        
        return view('admin.analytics.index', compact(
            'totalDoctors',
            'totalAssistants',
            'totalPatients',
            'activeSessions',
            'closedSessions',
            'sessionsThisWeek',
            'sessionsThisMonth',
            'resourcesThisWeek',
            'resourcesThisMonth',
            'sessionsOverTime',
            'doctorCaseloads',
            'attentionFlags'
        ));
    }
}
