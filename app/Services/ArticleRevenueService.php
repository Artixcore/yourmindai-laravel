<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ArticleEarning;
use App\Models\ArticleView;
use Carbon\Carbon;

class ArticleRevenueService
{
    /**
     * Calculate earnings for an article in a given period
     */
    public function calculateEarnings(Article $article, Carbon $periodStart, Carbon $periodEnd): float
    {
        // Get views count for the period
        $viewsCount = ArticleView::where('article_id', $article->id)
            ->whereBetween('viewed_at', [$periodStart, $periodEnd])
            ->count();
        
        if ($viewsCount === 0) {
            return 0;
        }
        
        // Base rate per 1000 views
        $baseRate = config('articles.revenue.base_rate_per_thousand', 0.50);
        $baseEarnings = ($viewsCount / 1000) * $baseRate;
        
        // Apply quality multipliers
        $multiplier = 1.0;
        
        // SEO score multiplier
        if ($article->seo_score >= config('articles.revenue.quality_multipliers.seo_threshold', 80)) {
            $multiplier *= config('articles.revenue.quality_multipliers.seo_multiplier', 1.5);
        }
        
        // Readability multiplier
        if ($article->readability_score >= config('articles.revenue.quality_multipliers.readability_threshold', 70)) {
            $multiplier *= config('articles.revenue.quality_multipliers.readability_multiplier', 1.2);
        }
        
        // Featured multiplier
        if ($article->is_featured) {
            $multiplier *= config('articles.revenue.quality_multipliers.featured_multiplier', 2.0);
        }
        
        // Likes multiplier
        $likesCount = $article->likes()->count();
        if ($likesCount >= config('articles.revenue.quality_multipliers.likes_threshold', 50)) {
            $multiplier *= config('articles.revenue.quality_multipliers.likes_multiplier', 1.3);
        }
        
        $totalEarnings = $baseEarnings * $multiplier;
        
        return round($totalEarnings, 2);
    }

    /**
     * Distribute earnings for all articles in a period
     */
    public function distributeEarnings(Carbon $periodStart, Carbon $periodEnd)
    {
        $articles = Article::where('status', 'published')
            ->where('published_at', '<=', $periodEnd)
            ->get();
        
        foreach ($articles as $article) {
            $earnings = $this->calculateEarnings($article, $periodStart, $periodEnd);
            
            if ($earnings > 0) {
                ArticleEarning::create([
                    'article_id' => $article->id,
                    'user_id' => $article->user_id,
                    'views_count' => ArticleView::where('article_id', $article->id)
                        ->whereBetween('viewed_at', [$periodStart, $periodEnd])
                        ->count(),
                    'earnings_amount' => $earnings,
                    'period_start' => $periodStart,
                    'period_end' => $periodEnd,
                    'status' => 'calculated',
                ]);
            }
        }
    }

    /**
     * Get total earnings for an author
     */
    public function getAuthorEarnings(int $userId): array
    {
        $earnings = ArticleEarning::where('user_id', $userId)->get();
        
        return [
            'total_earnings' => $earnings->sum('earnings_amount'),
            'paid_earnings' => $earnings->where('status', 'paid')->sum('earnings_amount'),
            'pending_earnings' => $earnings->where('status', 'calculated')->sum('earnings_amount'),
            'total_views' => $earnings->sum('views_count'),
            'earnings_by_period' => $earnings->groupBy(function ($item) {
                return $item->period_start->format('Y-m');
            })->map(function ($group) {
                return [
                    'amount' => $group->sum('earnings_amount'),
                    'views' => $group->sum('views_count'),
                    'status' => $group->first()->status,
                ];
            }),
        ];
    }

    /**
     * Get article performance metrics
     */
    public function getArticlePerformance(Article $article): array
    {
        return [
            'total_views' => $article->views_count,
            'total_likes' => $article->likes()->count(),
            'total_comments' => $article->comments()->approved()->count(),
            'total_earnings' => $article->earnings()->sum('earnings_amount'),
            'avg_daily_views' => $article->published_at 
                ? $article->views_count / max(1, $article->published_at->diffInDays(now()))
                : 0,
        ];
    }
}
