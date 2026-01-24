@extends('layouts.app')

@section('title', 'Writer Dashboard')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h2 fw-bold">Writer Dashboard</h1>
        <p class="text-muted">Manage your articles and track your earnings</p>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted small">Total Articles</h6>
                    <h2 class="mb-0">{{ $stats['total_articles'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted small">Published</h6>
                    <h2 class="mb-0 text-success">{{ $stats['published_articles'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted small">Total Views</h6>
                    <h2 class="mb-0 text-primary">{{ number_format($stats['total_views']) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted small">Total Earnings</h6>
                    <h2 class="mb-0 text-success">${{ number_format($stats['total_earnings'], 2) }}</h2>
                    <small class="text-muted">Pending: ${{ number_format($stats['pending_earnings'], 2) }}</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Recent Articles -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Recent Articles</h5>
                    <a href="{{ route('writer.articles.create') }}" class="btn btn-sm btn-primary">New Article</a>
                </div>
                <div class="card-body p-0">
                    @if ($recentArticles->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Title</th>
                                        <th>Status</th>
                                        <th>Views</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recentArticles as $article)
                                        <tr>
                                            <td>{{ Str::limit($article->title, 50) }}</td>
                                            <td><x-article-status-badge :status="$article->status" /></td>
                                            <td>{{ number_format($article->views_count) }}</td>
                                            <td>{{ $article->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <a href="{{ route('writer.articles.edit', $article) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <p class="text-muted">No articles yet. Create your first article!</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Top Performing Articles -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Top Performing</h5>
                </div>
                <div class="card-body">
                    @if ($topArticles->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach ($topArticles as $article)
                                <div class="list-group-item px-0">
                                    <h6 class="mb-1">{{ Str::limit($article->title, 40) }}</h6>
                                    <small class="text-muted">{{ number_format($article->views_count) }} views</small>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted small text-center">No published articles yet</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
