@extends('layouts.app')

@section('title', 'Edit Article')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('writer.articles.index') }}" class="text-decoration-none">← Back to Articles</a>
        <h1 class="h2 fw-bold mt-2">Edit Article</h1>
    </div>

    <form method="POST" action="{{ route('writer.articles.update', $article) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <label class="form-label">Title *</label>
                        <input type="text" name="title" id="title" class="form-control form-control-lg" 
                               value="{{ old('title', $article->title) }}" required>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <label class="form-label">Slug</label>
                        <input type="text" name="slug" id="slug" class="form-control" 
                               value="{{ old('slug', $article->slug) }}">
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <label class="form-label">Content *</label>
                        <x-article-editor name="content" :value="old('content', $article->content)" :required="true" />
                        <div class="mt-2 d-flex gap-3 text-sm text-muted">
                            <span>Words: <span id="word-count">0</span></span>
                            <span>Reading Time: <span id="reading-time">0</span> min</span>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <label class="form-label">Excerpt</label>
                        <textarea name="excerpt" class="form-control" rows="3">{{ old('excerpt', $article->excerpt) }}</textarea>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white"><h6 class="mb-0">SEO Settings</h6></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" class="form-control" 
                                   value="{{ old('meta_title', $article->meta_title) }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Meta Description</label>
                            <textarea name="meta_description" class="form-control" rows="2">{{ old('meta_description', $article->meta_description) }}</textarea>
                        </div>
                        <div>
                            <label class="form-label">Keywords</label>
                            <input type="text" name="meta_keywords" class="form-control" 
                                   value="{{ old('meta_keywords', $article->meta_keywords) }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white"><h6 class="mb-0">Featured Image</h6></div>
                    <div class="card-body">
                        @if ($article->featured_image)
                            <img src="{{ asset('storage/' . $article->featured_image) }}" class="img-fluid rounded mb-2">
                        @endif
                        <input type="file" name="featured_image" class="form-control" accept="image/*">
                        <small class="text-muted">Max 2MB</small>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white"><h6 class="mb-0">Categories</h6></div>
                    <div class="card-body">
                        @foreach ($categories as $category)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="categories[]" 
                                       value="{{ $category->id }}" id="cat-{{ $category->id }}"
                                       {{ $article->categories->contains($category->id) ? 'checked' : '' }}>
                                <label class="form-check-label" for="cat-{{ $category->id }}">
                                    {{ $category->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white"><h6 class="mb-0">Tags</h6></div>
                    <div class="card-body">
                        <input type="text" name="tags" class="form-control" 
                               value="{{ old('tags', $article->tags->pluck('name')->implode(', ')) }}">
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Update Article</button>
                        @if ($article->status === 'draft')
                            <button type="submit" name="submit_for_review" value="1" class="btn btn-success">
                                Submit for Review
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js"></script>
<script src="{{ asset('js/tinymce-config.js') }}"></script>
@endpush
@endsection
