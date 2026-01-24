@extends('layouts.app')

@section('title', 'My Articles')

@section('content')
<div class="container-fluid">
    <div class="mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h2 fw-bold">My Articles</h1>
            <p class="text-muted">Manage your articles and submissions</p>
        </div>
        <a href="{{ route('writer.articles.create') }}" class="btn btn-primary">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Article
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body"><small class="text-muted">Total</small><h4>{{ $stats['total'] }}</h4></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body"><small class="text-muted">Published</small><h4 class="text-success">{{ $stats['published'] }}</h4></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body"><small class="text-muted">Pending</small><h4 class="text-warning">{{ $stats['pending'] }}</h4></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body"><small class="text-muted">Draft</small><h4 class="text-secondary">{{ $stats['draft'] }}</h4></div>
            </div>
        </div>
    </div>

    <!-- Articles List -->
    @if ($articles->count() > 0)
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Title</th>
                            <th>Categories</th>
                            <th>Status</th>
                            <th>Views</th>
                            <th>SEO</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($articles as $article)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if ($article->featured_image)
                                            <img src="{{ asset('storage/' . $article->featured_image) }}" 
                                                 alt="{{ $article->title }}" 
                                                 class="rounded" 
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                        @endif
                                        <span>{{ Str::limit($article->title, 50) }}</span>
                                    </div>
                                </td>
                                <td>
                                    @foreach ($article->categories->take(2) as $category)
                                        <span class="badge bg-secondary small">{{ $category->name }}</span>
                                    @endforeach
                                </td>
                                <td><x-article-status-badge :status="$article->status" /></td>
                                <td>{{ number_format($article->views_count) }}</td>
                                <td><x-seo-score-badge :score="$article->seo_score" label="" /></td>
                                <td>{{ $article->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('writer.articles.edit', $article) }}" class="btn btn-outline-primary">Edit</a>
                                        @if ($article->status === 'draft')
                                            <form method="POST" action="{{ route('writer.articles.submit', $article) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-success">Submit</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">{{ $articles->links() }}</div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <p class="text-muted mb-3">You haven't created any articles yet</p>
                <a href="{{ route('writer.articles.create') }}" class="btn btn-primary">Create Your First Article</a>
            </div>
        </div>
    @endif
</div>
@endsection
