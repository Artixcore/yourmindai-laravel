<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArticleEarning;
use App\Models\User;
use App\Services\ArticleRevenueService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ArticleEarningsController extends Controller
{
    protected $revenueService;

    public function __construct(ArticleRevenueService $revenueService)
    {
        $this->revenueService = $revenueService;
    }

    public function index(Request $request)
    {
        $query = ArticleEarning::with(['user', 'article'])
            ->orderBy('period_start', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by author
        if ($request->has('author_id') && $request->author_id) {
            $query->where('user_id', $request->author_id);
        }

        $earnings = $query->paginate(20);

        // Get authors with earnings
        $authors = User::whereHas('articleEarnings')->orderBy('name')->get();

        // Statistics
        $stats = [
            'total_earnings' => ArticleEarning::sum('earnings_amount'),
            'paid_earnings' => ArticleEarning::where('status', 'paid')->sum('earnings_amount'),
            'pending_earnings' => ArticleEarning::whereIn('status', ['pending', 'calculated'])->sum('earnings_amount'),
            'total_authors' => User::whereHas('articleEarnings')->count(),
        ];

        return view('admin.article-earnings.index', compact('earnings', 'authors', 'stats'));
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'period_start' => 'required|date',
            'period_end' => 'required|date|after:period_start',
        ]);

        try {
            $periodStart = Carbon::parse($request->period_start);
            $periodEnd = Carbon::parse($request->period_end);

            $this->revenueService->distributeEarnings($periodStart, $periodEnd);

            return back()->with('success', 'Earnings calculated successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to calculate earnings: ' . $e->getMessage());
        }
    }

    public function markAsPaid(ArticleEarning $earning)
    {
        $earning->markAsPaid();
        return back()->with('success', 'Earnings marked as paid!');
    }
}
