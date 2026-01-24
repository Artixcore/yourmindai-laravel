<?php

namespace App\Http\Controllers\Writer;

use App\Http\Controllers\Controller;
use App\Models\ArticleEarning;
use App\Services\ArticleRevenueService;
use Illuminate\Http\Request;

class EarningsController extends Controller
{
    protected $revenueService;

    public function __construct(ArticleRevenueService $revenueService)
    {
        $this->revenueService = $revenueService;
    }

    public function index()
    {
        $userId = auth()->id();
        
        // Get earnings summary
        $earningsData = $this->revenueService->getAuthorEarnings($userId);
        
        // Get earnings history
        $earnings = ArticleEarning::where('user_id', $userId)
            ->with('article')
            ->orderBy('period_start', 'desc')
            ->paginate(20);
        
        return view('writer.earnings.index', compact('earningsData', 'earnings'));
    }

    public function show($period)
    {
        $userId = auth()->id();
        
        // Parse period (format: YYYY-MM)
        [$year, $month] = explode('-', $period);
        $periodStart = \Carbon\Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $periodEnd = $periodStart->copy()->endOfMonth();
        
        // Get earnings for this period
        $earnings = ArticleEarning::where('user_id', $userId)
            ->where('period_start', $periodStart)
            ->where('period_end', $periodEnd)
            ->with('article')
            ->get();
        
        $totalEarnings = $earnings->sum('earnings_amount');
        $totalViews = $earnings->sum('views_count');
        
        return view('writer.earnings.show', compact(
            'earnings',
            'totalEarnings',
            'totalViews',
            'periodStart',
            'periodEnd'
        ));
    }
}
