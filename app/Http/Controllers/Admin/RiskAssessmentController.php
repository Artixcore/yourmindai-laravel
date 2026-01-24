<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RiskAssessment;
use App\Models\PatientProfile;
use App\Models\User;
use Carbon\Carbon;

class RiskAssessmentController extends Controller
{
    public function index(Request $request)
    {
        $query = RiskAssessment::with('patient.user', 'assessedBy');

        // Apply filters
        if ($request->filled('risk_level')) {
            $query->where('risk_level', $request->risk_level);
        }

        if ($request->filled('doctor_id')) {
            $query->where('assessed_by', $request->doctor_id);
        }

        if ($request->filled('date_from')) {
            $query->where('assessment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('assessment_date', '<=', $request->date_to);
        }

        if ($request->filled('alert_status')) {
            if ($request->alert_status == 'sent') {
                $query->where('alert_sent', true);
            } elseif ($request->alert_status == 'not_sent') {
                $query->where('alert_sent', false);
            }
        }

        $assessments = $query->orderBy('assessment_date', 'desc')->paginate(20);

        // Get doctors for filter dropdown
        $doctors = User::where('role', 'doctor')->get();

        // Calculate statistics
        $stats = [
            'total' => RiskAssessment::count(),
            'high_risk' => RiskAssessment::whereIn('risk_level', ['high', 'critical'])->count(),
            'alerts_sent' => RiskAssessment::where('alert_sent', true)->count(),
            'this_month' => RiskAssessment::whereBetween('assessment_date', [now()->startOfMonth(), now()->endOfMonth()])->count(),
        ];

        return view('admin.risk-assessments.index', compact('assessments', 'doctors', 'stats'));
    }

    public function show(Request $request, RiskAssessment $assessment)
    {
        $assessment->load('patient.user', 'assessedBy', 'feedback', 'practiceProgressions');

        // Get previous assessment for comparison
        $previousAssessment = RiskAssessment::where('patient_id', $assessment->patient_id)
            ->where('assessment_date', '<', $assessment->assessment_date)
            ->orderBy('assessment_date', 'desc')
            ->first();

        // Get next assessment for comparison
        $nextAssessment = RiskAssessment::where('patient_id', $assessment->patient_id)
            ->where('assessment_date', '>', $assessment->assessment_date)
            ->orderBy('assessment_date', 'asc')
            ->first();

        return view('admin.risk-assessments.show', compact('assessment', 'previousAssessment', 'nextAssessment'));
    }

    public function analytics(Request $request)
    {
        // Risk level distribution
        $riskDistribution = RiskAssessment::selectRaw('risk_level, COUNT(*) as count')
            ->groupBy('risk_level')
            ->get()
            ->pluck('count', 'risk_level')
            ->toArray();

        // Assessments over time (last 12 months)
        $assessmentsByMonth = RiskAssessment::selectRaw('YEAR(assessment_date) as year, MONTH(assessment_date) as month, COUNT(*) as count')
            ->where('assessment_date', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // High-risk cases by doctor
        $highRiskByDoctor = RiskAssessment::whereIn('risk_level', ['high', 'critical'])
            ->with('assessedBy')
            ->selectRaw('assessed_by, COUNT(*) as count')
            ->groupBy('assessed_by')
            ->orderByDesc('count')
            ->take(10)
            ->get();

        // Risk factors frequency
        $riskFactorsFrequency = [];
        $assessments = RiskAssessment::whereNotNull('risk_factors')->get();
        foreach ($assessments as $assessment) {
            if (is_array($assessment->risk_factors)) {
                foreach ($assessment->risk_factors as $factor) {
                    if (!isset($riskFactorsFrequency[$factor])) {
                        $riskFactorsFrequency[$factor] = 0;
                    }
                    $riskFactorsFrequency[$factor]++;
                }
            }
        }
        arsort($riskFactorsFrequency);

        // Monthly high-risk trend
        $highRiskTrend = RiskAssessment::whereIn('risk_level', ['high', 'critical'])
            ->selectRaw('YEAR(assessment_date) as year, MONTH(assessment_date) as month, COUNT(*) as count')
            ->where('assessment_date', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $stats = [
            'total_assessments' => RiskAssessment::count(),
            'high_risk_cases' => RiskAssessment::whereIn('risk_level', ['high', 'critical'])->count(),
            'alerts_sent' => RiskAssessment::where('alert_sent', true)->count(),
            'avg_per_patient' => round(RiskAssessment::count() / max(PatientProfile::count(), 1), 1),
            'this_week' => RiskAssessment::where('assessment_date', '>=', now()->startOfWeek())->count(),
            'this_month' => RiskAssessment::whereBetween('assessment_date', [now()->startOfMonth(), now()->endOfMonth()])->count(),
        ];

        return view('admin.risk-assessments.analytics', compact(
            'stats',
            'riskDistribution',
            'assessmentsByMonth',
            'highRiskByDoctor',
            'riskFactorsFrequency',
            'highRiskTrend'
        ));
    }

    public function highRisk(Request $request)
    {
        // Get all high and critical risk assessments
        $highRiskAssessments = RiskAssessment::whereIn('risk_level', ['high', 'critical'])
            ->with('patient.user', 'assessedBy')
            ->orderBy('assessment_date', 'desc')
            ->paginate(15);

        // Get patients with recent high-risk assessments (within last 30 days)
        $recentHighRisk = RiskAssessment::whereIn('risk_level', ['high', 'critical'])
            ->where('assessment_date', '>=', now()->subDays(30))
            ->with('patient.user', 'assessedBy')
            ->orderBy('assessment_date', 'desc')
            ->get()
            ->groupBy('patient_id');

        $stats = [
            'total_high_risk' => RiskAssessment::whereIn('risk_level', ['high', 'critical'])->count(),
            'critical' => RiskAssessment::where('risk_level', 'critical')->count(),
            'high' => RiskAssessment::where('risk_level', 'high')->count(),
            'recent' => RiskAssessment::whereIn('risk_level', ['high', 'critical'])
                ->where('assessment_date', '>=', now()->subDays(7))
                ->count(),
        ];

        return view('admin.risk-assessments.high-risk', compact('highRiskAssessments', 'recentHighRisk', 'stats'));
    }
}
