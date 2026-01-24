<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ArticleTag;
use App\Models\ArticleLike;
use App\Models\ArticleComment;
use App\Models\ArticleView;
use App\Services\ArticleSEOService;
use Illuminate\Http\Request;

class ArticlePublicController extends Controller
{
    protected $seoService;

    public function __construct(ArticleSEOService $seoService)
    {
        $this->seoService = $seoService;
    }

    public function index(Request $request)
    {
        $query = Article::published()
            ->with(['user', 'categories', 'tags'])
            ->orderBy('published_at', 'desc');

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('excerpt', 'LIKE', "%{$search}%")
                  ->orWhere('content', 'LIKE', "%{$search}%");
            });
        }

        $articles = $query->paginate(12);
        $categories = ArticleCategory::active()->ordered()->withCount('articles')->get();

        return view('articles.index', compact('articles', 'categories'));
    }

    public function show(string $slug)
    {
        $article = Article::where('slug', $slug)
            ->published()
            ->with(['user', 'categories', 'tags', 'comments' => function ($q) {
                $q->approved()->topLevel()->with(['user', 'replies' => function ($rq) {
                    $rq->approved()->with('user');
                }]);
            }])
            ->firstOrFail();

        // Record view
        ArticleView::recordView(
            $article,
            auth()->user(),
            request()->ip(),
            request()->userAgent()
        );

        // Check if user has liked
        $userHasLiked = false;
        if (auth()->check()) {
            $userHasLiked = ArticleLike::where('article_id', $article->id)
                ->where('user_id', auth()->id())
                ->exists();
        } else {
            $userHasLiked = ArticleLike::where('article_id', $article->id)
                ->where('ip_address', request()->ip())
                ->exists();
        }

        // Related articles
        $relatedArticles = Article::published()
            ->where('id', '!=', $article->id)
            ->whereHas('categories', function ($q) use ($article) {
                $q->whereIn('article_categories.id', $article->categories->pluck('id'));
            })
            ->orderBy('views_count', 'desc')
            ->take(3)
            ->get();

        // SEO meta tags
        $metaTags = $this->seoService->generateMetaTags($article);
        $schemaMarkup = $this->seoService->generateSchemaMarkup($article);

        return view('articles.show', compact('article', 'userHasLiked', 'relatedArticles', 'metaTags', 'schemaMarkup'));
    }

    public function category(string $slug)
    {
        $category = ArticleCategory::where('slug', $slug)->active()->firstOrFail();
        
        $articles = $category->articles()
            ->published()
            ->with(['user', 'categories', 'tags'])
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        $categories = ArticleCategory::active()->ordered()->withCount('articles')->get();

        return view('articles.category', compact('category', 'articles', 'categories'));
    }

    public function tag(string $slug)
    {
        $tag = ArticleTag::where('slug', $slug)->firstOrFail();
        
        $articles = $tag->articles()
            ->published()
            ->with(['user', 'categories', 'tags'])
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('articles.tag', compact('tag', 'articles'));
    }

    public function search(Request $request)
    {
        $search = $request->input('q', '');
        
        $articles = Article::published()
            ->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('excerpt', 'LIKE', "%{$search}%")
                  ->orWhere('content', 'LIKE', "%{$search}%");
            })
            ->with(['user', 'categories'])
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('articles.search', compact('articles', 'search'));
    }

    public function like(Article $article)
    {
        if ($article->status !== 'published') {
            return response()->json(['error' => 'Article not found'], 404);
        }

        $userId = auth()->id();
        $ipAddress = request()->ip();

        // Check if already liked
        $existingLike = ArticleLike::where('article_id', $article->id)
            ->where(function ($q) use ($userId, $ipAddress) {
                if ($userId) {
                    $q->where('user_id', $userId);
                } else {
                    $q->where('ip_address', $ipAddress);
                }
            })
            ->first();

        if ($existingLike) {
            // Unlike
            $existingLike->delete();
            $liked = false;
        } else {
            // Like
            ArticleLike::create([
                'article_id' => $article->id,
                'user_id' => $userId,
                'ip_address' => $ipAddress,
            ]);
            $liked = true;
        }

        return response()->json([
            'success' => true,
            'liked' => $liked,
            'likes_count' => $article->likes()->count(),
        ]);
    }

    public function comment(Request $request, Article $article)
    {
        if ($article->status !== 'published') {
            return back()->with('error', 'Cannot comment on this article.');
        }

        $rules = [
            'comment' => 'required|string|max:2000',
            'parent_id' => 'nullable|exists:article_comments,id',
        ];

        if (!auth()->check()) {
            $rules['name'] = 'required|string|max:100';
            $rules['email'] = 'required|email|max:150';
        }

        $request->validate($rules);

        ArticleComment::create([
            'article_id' => $article->id,
            'user_id' => auth()->id(),
            'parent_id' => $request->parent_id,
            'name' => $request->name,
            'email' => $request->email,
            'comment' => $request->comment,
            'status' => 'pending',
        ]);

        return back()->with('success', 'Your comment has been submitted and is awaiting moderation.');
    }
}
