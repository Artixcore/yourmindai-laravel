@extends('layouts.guest')

@section('title', $article->meta_title ?? $article->title)

@push('meta')
    @foreach ($metaTags as $property => $content)
        @if ($content)
            <meta property="{{ $property }}" content="{{ $content }}">
        @endif
    @endforeach
    <script type="application/ld+json">{!! json_encode($schemaMarkup) !!}</script>
@endpush

@section('content')
<article class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Article Header -->
            <div class="mb-4">
                <h1 class="display-4 fw-bold mb-3">{{ $article->title }}</h1>
                
                <div class="d-flex align-items-center gap-3 text-muted mb-3">
                    <span class="d-flex align-items-center gap-2">
                        @if ($article->user->writer_avatar)
                            <img src="{{ asset('storage/' . $article->user->writer_avatar) }}" 
                                 class="rounded-circle" style="width: 40px; height: 40px;">
                        @endif
                        <strong>{{ $article->user->name }}</strong>
                    </span>
                    <span>•</span>
                    <span>{{ $article->published_at->format('M d, Y') }}</span>
                    <span>•</span>
                    <span>{{ $article->reading_time }} min read</span>
                </div>

                <!-- Categories & Tags -->
                <div class="mb-3">
                    @foreach ($article->categories as $category)
                        <a href="{{ route('articles.public.category', $category->slug) }}" 
                           class="badge bg-primary text-decoration-none">{{ $category->name }}</a>
                    @endforeach
                    @foreach ($article->tags as $tag)
                        <a href="{{ route('articles.public.tag', $tag->slug) }}" 
                           class="badge bg-light text-dark text-decoration-none">{{ $tag->name }}</a>
                    @endforeach
                </div>
            </div>

            <!-- Featured Image -->
            @if ($article->featured_image)
                <img src="{{ asset('storage/' . $article->featured_image) }}" 
                     alt="{{ $article->title }}" 
                     class="img-fluid rounded mb-4 w-100">
            @endif

            <!-- Article Content -->
            <div class="article-content mb-5">
                {!! $article->content !!}
            </div>

            <!-- Interaction Bar -->
            <div class="d-flex gap-3 align-items-center py-3 border-top border-bottom mb-4">
                <button type="button" class="btn btn-outline-primary like-button" data-article-id="{{ $article->id }}">
                    <svg style="width: 20px; height: 20px;" fill="{{ ($userHasLiked ?? false) ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                    <span class="likes-count">{{ $article->likes->count() }}</span>
                </button>
                
                <!-- Share Buttons -->
                <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('articles.public.show', $article->slug)) }}&text={{ urlencode($article->title) }}" 
                   target="_blank" class="btn btn-outline-info">
                    Share on Twitter
                </a>
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('articles.public.show', $article->slug)) }}" 
                   target="_blank" class="btn btn-outline-primary">
                    Share on Facebook
                </a>
            </div>

            <!-- Comments Section -->
            <div class="mb-5">
                <h3 class="mb-4">Comments ({{ $article->comments->count() }})</h3>
                
                <!-- Comment Form -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <form method="POST" action="{{ route('articles.public.comment', $article) }}">
                            @csrf
                            @guest
                                <div class="row g-3 mb-3">
                                    <div class="col-md-6">
                                        <input type="text" name="name" class="form-control" placeholder="Your Name *" required>
                                    </div>
                                    <div class="col-md-6">
                                        <input type="email" name="email" class="form-control" placeholder="Your Email *" required>
                                    </div>
                                </div>
                            @endguest
                            <textarea name="comment" class="form-control mb-3" rows="4" placeholder="Share your thoughts..." required></textarea>
                            <button type="submit" class="btn btn-primary">Post Comment</button>
                        </form>
                    </div>
                </div>

                <!-- Comments List -->
                @foreach ($article->comments as $comment)
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <strong>{{ $comment->commenter_name }}</strong>
                                <small class="text-muted">{{ $comment->created_at->diffForHumans() }}</small>
                            </div>
                            <p class="mb-0">{{ $comment->comment }}</p>
                            
                            @if ($comment->replies->count() > 0)
                                <div class="mt-3 ms-4">
                                    @foreach ($comment->replies as $reply)
                                        <div class="card bg-light mb-2">
                                            <div class="card-body py-2">
                                                <small><strong>{{ $reply->commenter_name }}</strong></small>
                                                <p class="small mb-0">{{ $reply->comment }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Related Articles -->
            @if ($relatedArticles->count() > 0)
                <div class="mb-5">
                    <h3 class="mb-4">Related Articles</h3>
                    <div class="row g-3">
                        @foreach ($relatedArticles as $related)
                            <div class="col-md-4">
                                <a href="{{ route('articles.public.show', $related->slug) }}" class="text-decoration-none">
                                    <x-article-card :article="$related" :compact="true" />
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</article>

@push('scripts')
<script>
document.querySelector('.like-button')?.addEventListener('click', function() {
    const articleId = this.dataset.articleId;
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
    fetch(`/articles/${articleId}/like`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken || ''
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            this.querySelector('.likes-count').textContent = data.likes_count;
            const svg = this.querySelector('svg');
            svg.setAttribute('fill', data.liked ? 'currentColor' : 'none');
        }
    });
});
</script>
@endpush
@endsection
