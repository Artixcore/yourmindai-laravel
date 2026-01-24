<?php

namespace App\Http\Controllers\Writer;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Services\ArticleRevenueService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $revenueService;

    public function __construct(ArticleRevenueService $revenueService)
    {
        $this->revenueService = $revenueService;
    }

    public function index()
    {
        $userId = auth()->id();
        
        // Article statistics
        $stats = [
            'total_articles' => Article::where('user_id', $userId)->count(),
            'published_articles' => Article::where('user_id', $userId)->where('status', 'published')->count(),
            'pending_articles' => Article::where('user_id', $userId)->where('status', 'pending_review')->count(),
            'draft_articles' => Article::where('user_id', $userId)->where('status', 'draft')->count(),
            'total_views' => Article::where('user_id', $userId)->sum('views_count'),
            'total_likes' => Article::where('user_id', $userId)
                ->withCount('likes')
                ->get()
                ->sum('likes_count'),
        ];
        
        // Earnings data
        $earningsData = $this->revenueService->getAuthorEarnings($userId);
        $stats['total_earnings'] = $earningsData['total_earnings'];
        $stats['pending_earnings'] = $earningsData['pending_earnings'];
        $stats['paid_earnings'] = $earningsData['paid_earnings'];
        
        // Recent articles
        $recentArticles = Article::where('user_id', $userId)
            ->with(['categories', 'tags'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        // Top performing articles
        $topArticles = Article::where('user_id', $userId)
            ->where('status', 'published')
            ->orderBy('views_count', 'desc')
            ->take(5)
            ->get();

        return view('writer.dashboard', compact('stats', 'recentArticles', 'topArticles'));
    }
}
