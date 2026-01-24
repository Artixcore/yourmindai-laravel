<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Article extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'status',
        'published_at',
        'views_count',
        'reading_time',
        'is_featured',
        'featured_order',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'seo_score',
        'readability_score',
        'rejected_reason',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'approved_at' => 'datetime',
        'is_featured' => 'boolean',
        'views_count' => 'integer',
        'reading_time' => 'integer',
        'seo_score' => 'integer',
        'readability_score' => 'integer',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(ArticleCategory::class, 'article_category_pivot', 'article_id', 'category_id')->withTimestamps();
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(ArticleTag::class, 'article_tag_pivot', 'article_id', 'tag_id')->withTimestamps();
    }

    public function comments(): HasMany
    {
        return $this->hasMany(ArticleComment::class);
    }

    public function likes(): HasMany
    {
        return $this->hasMany(ArticleLike::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(ArticleView::class);
    }

    public function earnings(): HasMany
    {
        return $this->hasMany(ArticleEarning::class);
    }

    public function media(): HasMany
    {
        return $this->hasMany(ArticleMedia::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending_review');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true)->orderBy('featured_order');
    }

    public function scopeByAuthor($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Methods
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function calculateReadingTime()
    {
        $wordCount = str_word_count(strip_tags($this->content));
        $this->reading_time = ceil($wordCount / 200); // Average reading speed: 200 words/min
        // Note: Does not save automatically - caller must save the model
    }

    public function generateSlug()
    {
        $slug = Str::slug($this->title);
        $originalSlug = $slug;
        
        // Check if the exact slug exists (excluding current article if updating)
        $query = static::where('slug', $slug);
        if ($this->exists) {
            $query->where('id', '!=', $this->id);
        }
        
        if ($query->exists()) {
            // Find the next available slug
            $count = 1;
            do {
                $slug = $originalSlug . '-' . $count;
                $query = static::where('slug', $slug);
                if ($this->exists) {
                    $query->where('id', '!=', $this->id);
                }
                $count++;
            } while ($query->exists());
        }
        
        $this->slug = $slug;
    }

    public function updateSEOScore()
    {
        // Will be implemented by ArticleSEOService
        // This is a placeholder
    }

    // Accessors
    public function getFormattedPublishedDateAttribute()
    {
        return $this->published_at ? $this->published_at->format('M d, Y') : null;
    }

    public function getAuthorNameAttribute()
    {
        return $this->user->name ?? 'Unknown';
    }

    public function getLikesCountAttribute()
    {
        return $this->likes()->count();
    }

    public function getExcerptPreviewAttribute()
    {
        return $this->excerpt ?? Str::limit(strip_tags($this->content), 150);
    }
}
