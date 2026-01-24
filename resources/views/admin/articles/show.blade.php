@extends('layouts.app')

@section('title', 'Article Details')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('admin.articles.index') }}" class="text-decoration-none">← Back</a>
        <h1 class="h2 fw-bold mt-2">Article Details</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Article Content -->
            <div class="card border-0 shadow-sm mb-3">
                @if ($article->featured_image)
                    <img src="{{ asset('storage/' . $article->featured_image) }}" class="card-img-top">
                @endif
                <div class="card-body">
                    <div class="d-flex gap-2 mb-3">
                        <x-article-status-badge :status="$article->status" />
                        @if ($article->is_featured)
                            <span class="badge bg-warning">Featured</span>
                        @endif
                    </div>
                    <h2>{{ $article->title }}</h2>
                    <p class="text-muted">By {{ $article->user->name }} • {{ $article->created_at->format('M d, Y') }}</p>
                    <div class="mt-4">{!! $article->content !!}</div>
                </div>
            </div>

            <!-- SEO Info -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white"><h6 class="mb-0">SEO Analysis</h6></div>
                <div class="card-body">
                    <div class="mb-3">
                        <x-seo-score-badge :score="$article->seo_score" label="SEO Score" />
                    </div>
                    <div class="mb-3">
                        <x-seo-score-badge :score="$article->readability_score" label="Readability" />
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Actions -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white"><h6 class="mb-0">Actions</h6></div>
                <div class="card-body d-grid gap-2">
                    @if ($article->status === 'pending_review')
                        <form method="POST" action="{{ route('admin.articles.approve', $article) }}">
                            @csrf
                            <button type="submit" class="btn btn-success w-100">Approve</button>
                        </form>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">Reject</button>
                    @endif
                    
                    @if ($article->status === 'approved')
                        <form method="POST" action="{{ route('admin.articles.publish', $article) }}">
                            @csrf
                            <button type="submit" class="btn btn-primary w-100">Publish</button>
                        </form>
                    @endif
                    
                    @if ($article->status === 'published')
                        @if (!$article->is_featured)
                            <form method="POST" action="{{ route('admin.articles.feature', $article) }}">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100">Feature</button>
                            </form>
                        @else
                            <form method="POST" action="{{ route('admin.articles.unfeature', $article) }}">
                                @csrf
                                <button type="submit" class="btn btn-outline-warning w-100">Unfeature</button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>

            <!-- Stats -->
            <div class="card border-0 shadow-sm mb-3">
                <div class="card-header bg-white"><h6 class="mb-0">Statistics</h6></div>
                <div class="card-body">
                    <p><strong>Views:</strong> {{ number_format($stats['views']) }}</p>
                    <p><strong>Likes:</strong> {{ number_format($stats['likes']) }}</p>
                    <p><strong>Comments:</strong> {{ number_format($stats['comments']) }}</p>
                    <p class="mb-0"><strong>Reading Time:</strong> {{ $article->reading_time }} min</p>
                </div>
            </div>

            <!-- Categories & Tags -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white"><h6 class="mb-0">Categories & Tags</h6></div>
                <div class="card-body">
                    <p class="small mb-2"><strong>Categories:</strong></p>
                    <div class="mb-3">
                        @foreach ($article->categories as $category)
                            <span class="badge bg-secondary">{{ $category->name }}</span>
                        @endforeach
                    </div>
                    <p class="small mb-2"><strong>Tags:</strong></p>
                    <div>
                        @foreach ($article->tags as $tag)
                            <span class="badge bg-light text-dark">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.articles.reject', $article) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Reject Article</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Reason for rejection *</label>
                    <textarea name="reason" class="form-control" rows="4" required></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Reject</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
