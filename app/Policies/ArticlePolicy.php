<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ArticlePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        // Anyone can view published articles
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Article $article): bool
    {
        // Published articles are public
        if ($article->status === 'published') {
            return true;
        }
        
        // Require authentication for non-published articles
        if (!$user) {
            return false;
        }
        
        // Author can view their own articles
        if ($article->user_id === $user->id) {
            return true;
        }
        
        // Admins can view all articles
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Writers, doctors, and admins can create articles
        return $user->canWriteArticles();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Article $article): bool
    {
        // Admins can always update
        if ($user->isAdmin()) {
            return true;
        }
        
        // Author can update their own article if it's draft or rejected
        if ($article->user_id === $user->id) {
            return in_array($article->status, ['draft', 'rejected']);
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Article $article): bool
    {
        // Admins can always delete
        if ($user->isAdmin()) {
            return true;
        }
        
        // Author can delete their own draft articles
        if ($article->user_id === $user->id && $article->status === 'draft') {
            return true;
        }
        
        return false;
    }

    /**
     * Determine whether the user can approve the model.
     */
    public function approve(User $user, Article $article): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can publish the model.
     */
    public function publish(User $user, Article $article): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can feature the model.
     */
    public function feature(User $user, Article $article): bool
    {
        return $user->isAdmin();
    }
}
