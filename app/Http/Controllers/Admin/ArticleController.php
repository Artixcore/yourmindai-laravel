<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\User;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::with(['user', 'categories', 'tags'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Filter by author
        if ($request->has('author_id') && $request->author_id) {
            $query->where('user_id', $request->author_id);
        }

        // Filter by category
        if ($request->has('category_id') && $request->category_id) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('article_categories.id', $request->category_id);
            });
        }

        $articles = $query->paginate(20);

        // Get filters data
        $authors = User::whereHas('articles')->orderBy('name')->get();
        $categories = ArticleCategory::orderBy('name')->get();

        // Statistics
        $stats = [
            'total' => Article::count(),
            'published' => Article::where('status', 'published')->count(),
            'pending' => Article::where('status', 'pending_review')->count(),
            'draft' => Article::where('status', 'draft')->count(),
            'rejected' => Article::where('status', 'rejected')->count(),
        ];

        return view('admin.articles.index', compact('articles', 'authors', 'categories', 'stats'));
    }

    public function show(Article $article)
    {
        $article->load(['user', 'categories', 'tags', 'comments', 'likes']);
        
        $stats = [
            'views' => $article->views_count,
            'likes' => $article->likes()->count(),
            'comments' => $article->comments()->approved()->count(),
        ];

        return view('admin.articles.show', compact('article', 'stats'));
    }

    public function approve(Request $request, Article $article)
    {
        if ($article->status !== 'pending_review') {
            return back()->with('error', 'Only pending articles can be approved.');
        }

        $article->update([
            'status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Article approved successfully!');
    }

    public function reject(Request $request, Article $article)
    {
        $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        $article->update([
            'status' => 'rejected',
            'rejected_reason' => $request->reason,
        ]);

        return back()->with('success', 'Article rejected.');
    }

    public function publish(Article $article)
    {
        if (!in_array($article->status, ['approved', 'published'])) {
            return back()->with('error', 'Only approved articles can be published.');
        }

        $article->update([
            'status' => 'published',
            'published_at' => $article->published_at ?? now(),
        ]);

        return back()->with('success', 'Article published successfully!');
    }

    public function unpublish(Article $article)
    {
        if ($article->status !== 'published') {
            return back()->with('error', 'Only published articles can be unpublished.');
        }

        $article->update(['status' => 'approved']);

        return back()->with('success', 'Article unpublished.');
    }

    public function feature(Article $article)
    {
        if ($article->status !== 'published') {
            return back()->with('error', 'Only published articles can be featured.');
        }

        $maxOrder = Article::where('is_featured', true)->max('featured_order') ?? 0;

        $article->update([
            'is_featured' => true,
            'featured_order' => $maxOrder + 1,
        ]);

        return back()->with('success', 'Article featured successfully!');
    }

    public function unfeature(Article $article)
    {
        $article->update([
            'is_featured' => false,
            'featured_order' => null,
        ]);

        return back()->with('success', 'Article removed from featured.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'articles' => 'required|array',
            'articles.*' => 'exists:articles,id',
        ]);

        try {
            foreach ($request->articles as $order => $articleId) {
                Article::where('id', $articleId)->update(['featured_order' => $order + 1]);
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
