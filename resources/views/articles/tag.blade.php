@extends('layouts.guest')

@section('title', 'Articles tagged: ' . $tag->name)

@section('content')
<div class="container py-5">
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('articles.public.index') }}">Articles</a></li>
                <li class="breadcrumb-item active">Tag: {{ $tag->name }}</li>
            </ol>
        </nav>
        <h1 class="display-5 fw-bold">{{ $tag->name }}</h1>
    </div>

    @if ($articles->count() > 0)
        <div class="row g-4">
            @foreach ($articles as $article)
                <div class="col-md-4">
                    <a href="{{ route('articles.public.show', $article->slug) }}" class="text-decoration-none">
                        <x-article-card :article="$article" />
                    </a>
                </div>
            @endforeach
        </div>
        <div class="mt-4">{{ $articles->links() }}</div>
    @else
        <div class="card"><div class="card-body text-center py-5"><p class="text-muted mb-0">No articles with this tag</p></div></div>
    @endif
</div>
@endsection
