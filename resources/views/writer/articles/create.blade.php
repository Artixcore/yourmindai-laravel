@extends('layouts.app')

@section('title', 'Create Article')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('writer.articles.index') }}" class="text-decoration-none">← Back to Articles</a>
        <h1 class="h2 fw-bold mt-2">Create New Article</h1>
    </div>

    <form method="POST" action="{{ route('writer.articles.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-lg-8">
                <!-- Title -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <label class="form-label">Title *</label>
                        <input type="text" name="title" id="title" class="form-control form-control-lg" value="{{ old('title') }}" required>
                    </div>
                </div>

                <!-- Slug -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <label class="form-label">Slug (URL)</label>
                        <input type="text" name="slug" id="slug" class="form-control" value="{{ old('slug') }}">
                        <small class="text-muted">Leave blank to auto-generate from title</small>
                    </div>
                </div>

                <!-- Content Editor -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <label class="form-label">Content *</label>
                        <x-article-editor name="content" :value="old('content', '')" :required="true" />
                        <div class="mt-2 d-flex gap-3 text-sm text-muted">
                            <span>Words: <span id="word-count">0</span></span>
                            <span>Reading Time: <span id="reading-time">0</span> min</span>
                        </div>
                    </div>
                </div>

                <!-- Excerpt -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body">
                        <label class="form-label">Excerpt</label>
                        <textarea name="excerpt" class="form-control" rows="3">{{ old('excerpt') }}</textarea>
                        <small class="text-muted">Brief summary of the article</small>
                    </div>
                </div>

                <!-- SEO Fields -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white"><h6 class="mb-0">SEO Settings</h6></div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Meta Title</label>
                            <input type="text" name="meta_title" class="form-control" value="{{ old('meta_title') }}" maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Meta Description</label>
                            <textarea name="meta_description" class="form-control" rows="2" maxlength="300">{{ old('meta_description') }}</textarea>
                        </div>
                        <div>
                            <label class="form-label">Keywords (comma-separated)</label>
                            <input type="text" name="meta_keywords" class="form-control" value="{{ old('meta_keywords') }}">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Featured Image -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white"><h6 class="mb-0">Featured Image</h6></div>
                    <div class="card-body">
                        <input type="file" name="featured_image" class="form-control" accept="image/*">
                        <small class="text-muted">Max 2MB. JPG, PNG, or GIF</small>
                    </div>
                </div>

                <!-- Categories -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white"><h6 class="mb-0">Categories</h6></div>
                    <div class="card-body">
                        @foreach ($categories as $category)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="categories[]" 
                                       value="{{ $category->id }}" id="cat-{{ $category->id }}">
                                <label class="form-check-label" for="cat-{{ $category->id }}">
                                    {{ $category->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Tags -->
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white"><h6 class="mb-0">Tags</h6></div>
                    <div class="card-body">
                        <input type="text" name="tags" class="form-control" value="{{ old('tags') }}" placeholder="anxiety, therapy, wellness">
                        <small class="text-muted">Comma-separated tags</small>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body d-grid gap-2">
                        <button type="submit" name="submit_for_review" value="1" class="btn btn-primary">
                            Submit for Review
                        </button>
                        <button type="submit" class="btn btn-outline-secondary">
                            Save as Draft
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/tinymce@6/tinymce.min.js"></script>
<script src="https://storaeall.s3.us-east-1.amazonaws.com/public/js/tinymce-config.js"></script>
@endpush
@endsection
