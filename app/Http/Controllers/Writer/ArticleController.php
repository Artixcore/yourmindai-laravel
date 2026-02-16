<?php

namespace App\Http\Controllers\Writer;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\ArticleTag;
use App\Services\ArticleSEOService;
use App\Services\ArticleMediaService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    protected $seoService;
    protected $mediaService;

    public function __construct(ArticleSEOService $seoService, ArticleMediaService $mediaService)
    {
        $this->seoService = $seoService;
        $this->mediaService = $mediaService;
    }

    public function index()
    {
        $articles = Article::where('user_id', auth()->id())
            ->with(['categories', 'tags'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        $stats = [
            'total' => Article::where('user_id', auth()->id())->count(),
            'published' => Article::where('user_id', auth()->id())->where('status', 'published')->count(),
            'pending' => Article::where('user_id', auth()->id())->where('status', 'pending_review')->count(),
            'draft' => Article::where('user_id', auth()->id())->where('status', 'draft')->count(),
        ];

        return view('writer.articles.index', compact('articles', 'stats'));
    }

    public function create()
    {
        $categories = ArticleCategory::active()->ordered()->get();
        $tags = ArticleTag::orderBy('name')->get();
        
        return view('writer.articles.create', compact('categories', 'tags'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:articles,slug',
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:article_categories,id',
            'tags' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $article = new Article($request->only([
                'title', 'slug', 'excerpt', 'content',
                'meta_title', 'meta_description', 'meta_keywords'
            ]));
            
            $article->user_id = auth()->id();
            
            // Generate slug if not provided
            if (!$article->slug) {
                $article->generateSlug();
            }
            
            // Handle featured image upload
            if ($request->hasFile('featured_image')) {
                $result = $this->mediaService->uploadImage($request->file('featured_image'), null, auth()->id());
                $article->featured_image = str_replace(asset('storage/'), '', $result['location']);
            }
            
            // Calculate reading time
            $article->calculateReadingTime();
            
            // Calculate SEO scores
            $seoAnalysis = $this->seoService->analyzeSEO($article);
            $article->seo_score = $seoAnalysis['seo_score'];
            $article->readability_score = $seoAnalysis['readability_score'];
            
            // Set status
            $article->status = $request->has('submit_for_review') ? 'pending_review' : 'draft';
            
            $article->save();
            
            // Attach categories
            if ($request->has('categories')) {
                $article->categories()->sync($request->categories);
            }
            
            // Handle tags
            if ($request->has('tags') && $request->tags) {
                $this->attachTags($article, $request->tags);
            }
            
            DB::commit();

            $message = $article->status === 'pending_review' 
                ? 'Article submitted for review!' 
                : 'Article saved as draft!';

            return redirect()->route('writer.articles.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to save article: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Article $article)
    {
        $this->authorize('update', $article);
        
        $categories = ArticleCategory::active()->ordered()->get();
        $tags = ArticleTag::orderBy('name')->get();
        
        $article->load(['categories', 'tags']);

        return view('writer.articles.edit', compact('article', 'categories', 'tags'));
    }

    public function update(Request $request, Article $article)
    {
        $this->authorize('update', $article);
        
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:articles,slug,' . $article->id,
            'excerpt' => 'nullable|string',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
            'categories' => 'nullable|array',
            'tags' => 'nullable|string',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string',
            'meta_keywords' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $article->fill($request->only([
                'title', 'slug', 'excerpt', 'content',
                'meta_title', 'meta_description', 'meta_keywords'
            ]));
            
            // Generate slug if changed
            if (!$article->slug || $article->isDirty('title')) {
                $article->generateSlug();
            }
            
            // Handle featured image
            if ($request->hasFile('featured_image')) {
                $result = $this->mediaService->uploadImage($request->file('featured_image'), $article, auth()->id());
                $article->featured_image = str_replace(asset('storage/'), '', $result['location']);
            }
            
            $article->calculateReadingTime();
            
            // Recalculate SEO scores
            $seoAnalysis = $this->seoService->analyzeSEO($article);
            $article->seo_score = $seoAnalysis['seo_score'];
            $article->readability_score = $seoAnalysis['readability_score'];
            
            $article->save();
            
            // Sync categories
            if ($request->has('categories')) {
                $article->categories()->sync($request->categories);
            }
            
            // Handle tags
            if ($request->has('tags')) {
                $this->attachTags($article, $request->tags);
            }
            
            DB::commit();

            return redirect()->route('writer.articles.index')->with('success', 'Article updated successfully!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update article: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Article $article)
    {
        $this->authorize('delete', $article);
        
        try {
            // Delete associated media
            if ($article->featured_image) {
                Storage::disk('public')->delete($article->featured_image);
            }
            
            $article->delete();
            
            return redirect()->route('writer.articles.index')->with('success', 'Article deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete article.');
        }
    }

    public function submitForReview(Article $article)
    {
        $this->authorize('update', $article);
        
        if (!in_array($article->status, ['draft', 'rejected'])) {
            return back()->with('error', 'Only draft or rejected articles can be submitted for review.');
        }
        
        $article->update(['status' => 'pending_review']);
        
        return redirect()->route('writer.articles.index')
            ->with('success', 'Article submitted for review!');
    }

    public function uploadImage(Request $request)
    {
        $request->validate([
            'file' => 'required|image|max:2048',
        ]);

        try {
            $result = $this->mediaService->uploadImage($request->file('file'), null, auth()->id());
            return response()->json($result);
        } catch (\Exception $e) {
            Log::error('Article image upload failed', [
                'user_id' => auth()->id(),
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return ApiResponse::error('An error occurred while processing your request.', 400);
        }
    }

    public function preview(Article $article)
    {
        $this->authorize('view', $article);
        
        $article->load(['categories', 'tags', 'user']);
        
        return view('writer.articles.preview', compact('article'));
    }

    private function attachTags(Article $article, string $tagsString)
    {
        $tagNames = array_filter(array_map('trim', explode(',', $tagsString)));
        $tagIds = [];
        
        foreach ($tagNames as $tagName) {
            $tag = ArticleTag::firstOrCreate(
                ['slug' => Str::slug($tagName)],
                ['name' => $tagName]
            );
            $tagIds[] = $tag->id;
        }
        
        $article->tags()->sync($tagIds);
    }
}
