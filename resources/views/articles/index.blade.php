@extends('layouts.guest')

@section('title', 'Articles')

@section('content')
<div class="container py-5">
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="display-5 fw-bold">Articles</h1>
            <p class="lead text-muted">Explore our collection of mental health and wellness articles</p>
        </div>
        <div class="col-md-4">
            <form method="GET" action="{{ route('articles.public.search') }}">
                <div class="input-group">
                    <input type="text" name="q" class="form-control" placeholder="Search articles..." value="{{ request('q') }}">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white"><h6 class="mb-0">Categories</h6></div>
                <div class="list-group list-group-flush">
                    @foreach ($categories as $category)
                        <a href="{{ route('articles.public.category', $category->slug) }}" 
                           class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                            <span>{{ $category->name }}</span>
                            <span class="badge bg-primary rounded-pill">{{ $category->articles_count }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Articles Grid -->
        <div class="col-md-9">
            @if ($articles->count() > 0)
                <div class="row g-4">
                    @foreach ($articles as $article)
                        <div class="col-md-6">
                            <a href="{{ route('articles.public.show', $article->slug) }}" class="text-decoration-none">
                                <x-article-card :article="$article" :showAuthor="true" :showExcerpt="true" />
                            </a>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">{{ $articles->links() }}</div>
            @else
                <div class="card"><div class="card-body text-center py-5"><p class="text-muted mb-0">No articles found</p></div></div>
            @endif
        </div>
    </div>
</div>
@endsection
