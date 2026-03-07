@extends('layouts.guest')

@section('title', 'Articles')

@section('content')
<section class="public-section px-3 px-md-4 px-lg-5 bg-gradient-guest">
    <div class="container-fluid">
        <div class="articles-page-header text-center mb-4" data-aos="fade-up">
            <h1 class="h1 public-section-title">Articles</h1>
            <p class="public-section-lead text-stone-600 mx-auto mb-4">Explore our collection of mental health and wellness articles</p>
            <form method="GET" action="{{ route('articles.public.search') }}" class="d-flex justify-content-center flex-wrap gap-2">
                <input type="text" name="q" class="form-control articles-search-input" placeholder="Search articles..." value="{{ request('q') }}" style="max-width: 320px;">
                <button type="submit" class="btn btn-gradient-primary">Search</button>
            </form>
        </div>

        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 mb-4">
                <div class="card card-psychological shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="mb-0 fw-semibold text-psychological-primary">Categories</h6>
                    </div>
                    <div class="list-group list-group-flush articles-category-list">
                        @foreach ($categories as $category)
                            <a href="{{ route('articles.public.category', $category->slug) }}"
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center border-0">
                                <span>{{ $category->name }}</span>
                                <span class="badge rounded-pill" style="background: linear-gradient(135deg, var(--color-teal-600), var(--color-soft-blue-500));">{{ $category->articles_count }}</span>
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
                            <div class="col-md-6" data-aos="fade-up">
                                <a href="{{ route('articles.public.show', $article->slug) }}" class="text-decoration-none">
                                    <x-article-card :article="$article" :showAuthor="true" :showExcerpt="true" class="card-psychological h-100 shadow-sm" />
                                </a>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-4">{{ $articles->links() }}</div>
                @else
                    <div class="card card-psychological shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="bi bi-journal-text display-4 text-muted mb-3"></i>
                            <p class="text-muted mb-0">No articles found</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endsection
