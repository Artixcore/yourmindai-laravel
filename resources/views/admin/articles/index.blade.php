@extends('layouts.app')

@section('title', 'Manage Articles')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h2 fw-bold">Article Management</h1>
        <p class="text-muted">Review, approve, and publish articles</p>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md">
            <div class="card border-0 shadow-sm"><div class="card-body"><small>Total</small><h4>{{ $stats['total'] }}</h4></div></div>
        </div>
        <div class="col-md">
            <div class="card border-0 shadow-sm"><div class="card-body"><small>Published</small><h4 class="text-success">{{ $stats['published'] }}</h4></div></div>
        </div>
        <div class="col-md">
            <div class="card border-0 shadow-sm"><div class="card-body"><small>Pending</small><h4 class="text-warning">{{ $stats['pending'] }}</h4></div></div>
        </div>
        <div class="col-md">
            <div class="card border-0 shadow-sm"><div class="card-body"><small>Draft</small><h4 class="text-secondary">{{ $stats['draft'] }}</h4></div></div>
        </div>
        <div class="col-md">
            <div class="card border-0 shadow-sm"><div class="card-body"><small>Rejected</small><h4 class="text-danger">{{ $stats['rejected'] }}</h4></div></div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="pending_review" {{ request('status') === 'pending_review' ? 'selected' : '' }}>Pending Review</option>
                        <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="author_id" class="form-select">
                        <option value="">All Authors</option>
                        @foreach ($authors as $author)
                            <option value="{{ $author->id }}" {{ request('author_id') == $author->id ? 'selected' : '' }}>
                                {{ $author->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="category_id" class="form-select">
                        <option value="">All Categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                    <a href="{{ route('admin.articles.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Articles -->
    @if ($articles->count() > 0)
        <div class="row g-3">
            @foreach ($articles as $article)
                <div class="col-12">
                    <x-article-card :article="$article" :showAuthor="true" :compact="true">
                        <div class="border-top pt-3 mt-3 d-flex gap-2">
                            <a href="{{ route('admin.articles.show', $article) }}" class="btn btn-sm btn-outline-primary">View</a>
                            @if ($article->status === 'pending_review')
                                <form method="POST" action="{{ route('admin.articles.approve', $article) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success">Approve</button>
                                </form>
                            @endif
                            @if ($article->status === 'approved')
                                <form method="POST" action="{{ route('admin.articles.publish', $article) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-primary">Publish</button>
                                </form>
                            @endif
                        </div>
                    </x-article-card>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $articles->links() }}</div>
    @else
        <div class="card"><div class="card-body text-center py-5"><p class="text-muted mb-0">No articles found</p></div></div>
    @endif
</div>
@endsection
