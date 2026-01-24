<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ArticleCategoryController extends Controller
{
    public function index()
    {
        $categories = ArticleCategory::withCount('articles')->ordered()->get();
        return view('admin.article-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.article-categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:100|unique:article_categories,slug',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
        ]);

        $category = new ArticleCategory($request->only(['name', 'slug', 'description', 'icon']));
        
        if (!$category->slug) {
            $category->slug = Str::slug($category->name);
        }
        
        $category->order = ArticleCategory::max('order') + 1;
        $category->save();

        return redirect()->route('admin.article-categories.index')
            ->with('success', 'Category created successfully!');
    }

    public function edit(ArticleCategory $articleCategory)
    {
        return view('admin.article-categories.edit', compact('articleCategory'));
    }

    public function update(Request $request, ArticleCategory $articleCategory)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:100|unique:article_categories,slug,' . $articleCategory->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'is_active' => 'boolean',
        ]);

        $articleCategory->update($request->only(['name', 'slug', 'description', 'icon', 'is_active']));

        return redirect()->route('admin.article-categories.index')
            ->with('success', 'Category updated successfully!');
    }

    public function destroy(ArticleCategory $articleCategory)
    {
        if ($articleCategory->articles()->count() > 0) {
            return back()->with('error', 'Cannot delete category with articles. Remove articles first.');
        }

        $articleCategory->delete();

        return redirect()->route('admin.article-categories.index')
            ->with('success', 'Category deleted successfully!');
    }
}
