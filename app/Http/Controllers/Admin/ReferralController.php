<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Referral;
use App\Models\User;

class ReferralController extends Controller
{
    public function index(Request $request)
    {
        $query = Referral::with('patient.user', 'referredBy', 'referredTo');

        // Apply filters
        if ($request->filled('referral_type')) {
            $query->where('referral_type', $request->referral_type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('specialty')) {
            $query->where('specialty_needed', $request->specialty);
        }

        if ($request->filled('referred_by')) {
            $query->where('referred_by', $request->referred_by);
        }

        if ($request->filled('date_from')) {
            $query->where('referred_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->where('referred_at', '<=', $request->date_to);
        }

        $referrals = $query->orderBy('referred_at', 'desc')->paginate(20);

        // Get doctors for filter dropdown
        $doctors = User::where('role', 'doctor')->get();

        // Get unique specialties
        $specialties = Referral::select('specialty_needed')
            ->distinct()
            ->pluck('specialty_needed')
            ->filter()
            ->sort()
            ->values();

        // Calculate statistics
        $stats = [
            'total' => Referral::count(),
            'pending' => Referral::where('status', 'pending')->count(),
            'active' => Referral::whereIn('status', ['accepted', 'in_progress'])->count(),
            'completed' => Referral::where('status', 'completed')->count(),
            'declined' => Referral::where('status', 'declined')->count(),
        ];

        return view('admin.referrals.index', compact('referrals', 'doctors', 'specialties', 'stats'));
    }

    public function show(Request $request, Referral $referral)
    {
        $referral->load('patient.user', 'referredBy', 'referredTo', 'originalReferral', 'backReferrals');

        return view('admin.referrals.show', compact('referral'));
    }

    public function analytics(Request $request)
    {
        // Referral distribution by type
        $typeDistribution = Referral::selectRaw('referral_type, COUNT(*) as count')
            ->groupBy('referral_type')
            ->get()
            ->pluck('count', 'referral_type')
            ->toArray();

        // Referral distribution by status
        $statusDistribution = Referral::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status')
            ->toArray();

        // Referrals by specialty
        $specialtyDistribution = Referral::selectRaw('specialty_needed, COUNT(*) as count')
            ->groupBy('specialty_needed')
            ->orderByDesc('count')
            ->take(10)
            ->get();

        // Referrals over time (last 12 months)
        $referralsByMonth = Referral::selectRaw('YEAR(referred_at) as year, MONTH(referred_at) as month, COUNT(*) as count')
            ->where('referred_at', '>=', now()->subMonths(12))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Top referring doctors
        $topReferrers = Referral::with('referredBy')
            ->selectRaw('referred_by, COUNT(*) as count')
            ->groupBy('referred_by')
            ->orderByDesc('count')
            ->take(10)
            ->get();

        // Top receiving doctors/specialists
        $topReceivers = Referral::with('referredTo')
            ->whereNotNull('referred_to')
            ->selectRaw('referred_to, COUNT(*) as count')
            ->groupBy('referred_to')
            ->orderByDesc('count')
            ->take(10)
            ->get();

        // Success rate (accepted + completed vs declined)
        $successfulReferrals = Referral::whereIn('status', ['accepted', 'in_progress', 'completed'])->count();
        $declinedReferrals = Referral::where('status', 'declined')->count();
        $totalWithResponse = $successfulReferrals + $declinedReferrals;
        $successRate = $totalWithResponse > 0 ? round(($successfulReferrals / $totalWithResponse) * 100, 1) : 0;

        $stats = [
            'total_referrals' => Referral::count(),
            'forward_referrals' => Referral::where('referral_type', 'forward')->count(),
            'back_referrals' => Referral::where('referral_type', 'back')->count(),
            'success_rate' => $successRate,
            'avg_response_time' => $this->calculateAverageResponseTime(),
            'this_week' => Referral::where('referred_at', '>=', now()->startOfWeek())->count(),
            'this_month' => Referral::whereBetween('referred_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
        ];

        return view('admin.referrals.analytics', compact(
            'stats',
            'typeDistribution',
            'statusDistribution',
            'specialtyDistribution',
            'referralsByMonth',
            'topReferrers',
            'topReceivers'
        ));
    }

    private function calculateAverageResponseTime()
    {
        $referralsWithResponse = Referral::whereNotNull('responded_at')->get();
        
        if ($referralsWithResponse->isEmpty()) {
            return 'N/A';
        }

        $totalHours = 0;
        foreach ($referralsWithResponse as $referral) {
            $totalHours += $referral->referred_at->diffInHours($referral->responded_at);
        }

        $avgHours = round($totalHours / $referralsWithResponse->count(), 1);
        
        if ($avgHours < 24) {
            return $avgHours . ' hours';
        } else {
            return round($avgHours / 24, 1) . ' days';
        }
    }
}
