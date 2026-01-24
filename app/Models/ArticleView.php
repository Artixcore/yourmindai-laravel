<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ArticleView extends Model
{
    protected $fillable = [
        'article_id',
        'user_id',
        'ip_address',
        'user_agent',
        'viewed_at',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function recordView($article, $user = null, $ipAddress = null, $userAgent = null)
    {
        // Prevent duplicate views from same IP within 1 hour
        $recentView = static::where('article_id', $article->id)
            ->where('ip_address', $ipAddress)
            ->where('viewed_at', '>', now()->subHour())
            ->first();

        if ($recentView) {
            return false;
        }

        static::create([
            'article_id' => $article->id,
            'user_id' => $user ? $user->id : null,
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent,
            'viewed_at' => now(),
        ]);

        $article->incrementViews();
        return true;
    }
}
