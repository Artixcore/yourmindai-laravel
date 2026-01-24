<?php

namespace App\Services;

use App\Models\Article;
use Illuminate\Support\Str;

class ArticleSEOService
{
    /**
     * Analyze article and calculate SEO score
     */
    public function analyzeSEO(Article $article): array
    {
        $score = $this->calculateSEOScore($article);
        $readability = $this->calculateReadability($article);
        
        return [
            'seo_score' => $score,
            'readability_score' => $readability,
            'recommendations' => $this->getRecommendations($article, $score),
        ];
    }

    /**
     * Calculate SEO score (0-100)
     */
    public function calculateSEOScore(Article $article): int
    {
        $score = 0;
        
        // Title length (5-70 chars): 15 points
        $titleLength = strlen($article->title);
        if ($titleLength >= 5 && $titleLength <= 70) {
            $score += 15;
        } elseif ($titleLength > 0) {
            $score += max(0, 15 - abs($titleLength - 40) / 5);
        }
        
        // Meta description (120-160 chars): 15 points
        if ($article->meta_description) {
            $descLength = strlen($article->meta_description);
            if ($descLength >= 120 && $descLength <= 160) {
                $score += 15;
            } elseif ($descLength > 0) {
                $score += max(0, 15 - abs($descLength - 140) / 5);
            }
        }
        
        // Content length (>1000 words): 15 points
        $wordCount = str_word_count(strip_tags($article->content));
        if ($wordCount >= 1000) {
            $score += 15;
        } elseif ($wordCount >= 500) {
            $score += ($wordCount / 1000) * 15;
        }
        
        // Images with alt text: 10 points
        preg_match_all('/<img[^>]+alt=["\']([^"\']*)["\'][^>]*>/i', $article->content, $matches);
        $imagesWithAlt = count($matches[0]);
        preg_match_all('/<img[^>]*>/i', $article->content, $allImages);
        $totalImages = count($allImages[0]);
        
        if ($totalImages > 0 && $imagesWithAlt === $totalImages) {
            $score += 10;
        } elseif ($totalImages > 0) {
            $score += ($imagesWithAlt / $totalImages) * 10;
        }
        
        // Internal links: 10 points
        preg_match_all('/<a[^>]+href=["\']\/[^"\']*["\'][^>]*>/i', $article->content, $internalLinks);
        if (count($internalLinks[0]) >= 2) {
            $score += 10;
        } elseif (count($internalLinks[0]) > 0) {
            $score += (count($internalLinks[0]) / 2) * 10;
        }
        
        // External links: 10 points
        preg_match_all('/<a[^>]+href=["\']https?:\/\/[^"\']*["\'][^>]*>/i', $article->content, $externalLinks);
        if (count($externalLinks[0]) >= 2) {
            $score += 10;
        } elseif (count($externalLinks[0]) > 0) {
            $score += (count($externalLinks[0]) / 2) * 10;
        }
        
        // Reading time (5-15 min): 15 points
        $readingTime = ceil($wordCount / 200);
        if ($readingTime >= 5 && $readingTime <= 15) {
            $score += 15;
        } elseif ($readingTime > 0) {
            $score += max(0, 15 - abs($readingTime - 10));
        }
        
        // Keyword density: 10 points (simplified check)
        if ($article->meta_keywords) {
            $score += 10;
        }
        
        return min(100, max(0, round($score)));
    }

    /**
     * Calculate readability score (Flesch Reading Ease)
     */
    public function calculateReadability(Article $article): int
    {
        $text = strip_tags($article->content);
        $text = preg_replace('/\s+/', ' ', $text);
        
        $sentenceCount = preg_match_all('/[.!?]+/', $text);
        $wordCount = str_word_count($text);
        $syllableCount = $this->countSyllables($text);
        
        if ($sentenceCount === 0 || $wordCount === 0) {
            return 0;
        }
        
        // Flesch Reading Ease formula
        $score = 206.835 - 1.015 * ($wordCount / $sentenceCount) - 84.6 * ($syllableCount / $wordCount);
        
        // Normalize to 0-100
        return min(100, max(0, round($score)));
    }

    /**
     * Count syllables in text (simplified)
     */
    private function countSyllables(string $text): int
    {
        $words = str_word_count(strtolower($text), 1);
        $syllables = 0;
        
        foreach ($words as $word) {
            $syllables += $this->syllablesInWord($word);
        }
        
        return $syllables;
    }

    /**
     * Count syllables in a single word
     */
    private function syllablesInWord(string $word): int
    {
        $word = strtolower($word);
        $vowels = ['a', 'e', 'i', 'o', 'u', 'y'];
        $syllableCount = 0;
        $previousWasVowel = false;
        
        for ($i = 0; $i < strlen($word); $i++) {
            $isVowel = in_array($word[$i], $vowels);
            
            if ($isVowel && !$previousWasVowel) {
                $syllableCount++;
            }
            
            $previousWasVowel = $isVowel;
        }
        
        // Handle silent 'e'
        if (substr($word, -1) === 'e') {
            $syllableCount--;
        }
        
        return max(1, $syllableCount);
    }

    /**
     * Get SEO recommendations
     */
    private function getRecommendations(Article $article, int $score): array
    {
        $recommendations = [];
        
        if (strlen($article->title) < 5 || strlen($article->title) > 70) {
            $recommendations[] = 'Title should be between 5-70 characters';
        }
        
        if (!$article->meta_description || strlen($article->meta_description) < 120) {
            $recommendations[] = 'Add a meta description (120-160 characters)';
        }
        
        $wordCount = str_word_count(strip_tags($article->content));
        if ($wordCount < 1000) {
            $recommendations[] = 'Increase content length to at least 1000 words';
        }
        
        if (!$article->featured_image) {
            $recommendations[] = 'Add a featured image';
        }
        
        return $recommendations;
    }

    /**
     * Generate meta tags for article
     */
    public function generateMetaTags(Article $article): array
    {
        return [
            'title' => $article->meta_title ?? $article->title,
            'description' => $article->meta_description ?? $article->excerpt_preview,
            'og:title' => $article->meta_title ?? $article->title,
            'og:description' => $article->meta_description ?? $article->excerpt_preview,
            'og:image' => $article->featured_image ? asset('storage/' . $article->featured_image) : null,
            'og:type' => 'article',
            'og:url' => route('articles.show', $article->slug),
            'twitter:card' => 'summary_large_image',
            'twitter:title' => $article->meta_title ?? $article->title,
            'twitter:description' => $article->meta_description ?? $article->excerpt_preview,
            'twitter:image' => $article->featured_image ? asset('storage/' . $article->featured_image) : null,
        ];
    }

    /**
     * Generate Schema.org JSON-LD markup
     */
    public function generateSchemaMarkup(Article $article): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => $article->title,
            'description' => $article->excerpt_preview,
            'image' => $article->featured_image ? asset('storage/' . $article->featured_image) : null,
            'author' => [
                '@type' => 'Person',
                'name' => $article->user?->name ?? 'Unknown',
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('app.name'),
                'logo' => [
                    '@type' => 'ImageObject',
                    'url' => asset('favicon.ico'),
                ],
            ],
            'datePublished' => $article->published_at ? $article->published_at->toIso8601String() : null,
            'dateModified' => $article->updated_at->toIso8601String(),
        ];
    }
}
