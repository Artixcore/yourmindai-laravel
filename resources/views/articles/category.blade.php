@extends('layouts.guest')

@section('title', $category->name . ' Articles')

@section('content')
<div class="container py-5">
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('articles.public.index') }}">Articles</a></li>
                <li class="breadcrumb-item active">{{ $category->name }}</li>
            </ol>
        </nav>
        <h1 class="display-5 fw-bold">{{ $category->name }}</h1>
        @if ($category->description)
            <p class="lead text-muted">{{ $category->description }}</p>
        @endif
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white"><h6 class="mb-0">All Categories</h6></div>
                <div class="list-group list-group-flush">
                    @foreach ($categories as $cat)
                        <a href="{{ route('articles.public.category', $cat->slug) }}" 
                           class="list-group-item list-group-item-action {{ $cat->id === $category->id ? 'active' : '' }}">
                            {{ $cat->name }}
                            <span class="badge bg-secondary rounded-pill">{{ $cat->articles_count }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-md-9">
            @if ($articles->count() > 0)
                <div class="row g-4">
                    @foreach ($articles as $article)
                        <div class="col-md-6">
                            <a href="{{ route('articles.public.show', $article->slug) }}" class="text-decoration-none">
                                <x-article-card :article="$article" />
                            </a>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">{{ $articles->links() }}</div>
            @else
                <div class="card"><div class="card-body text-center py-5"><p class="text-muted mb-0">No articles in this category yet</p></div></div>
            @endif
        </div>
    </div>
</div>
@endsection
