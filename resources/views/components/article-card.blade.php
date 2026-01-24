@props([
    'article',
    'showAuthor' => true,
    'showExcerpt' => true,
    'showStats' => true,
    'compact' => false
])

<div {{ $attributes->merge(['class' => 'card border shadow-sm h-100']) }}>
    @if ($article->featured_image)
        <div class="ratio ratio-16x9">
            <img src="{{ asset('storage/' . $article->featured_image) }}" 
                 alt="{{ $article->title }}" 
                 class="card-img-top object-fit-cover">
        </div>
    @endif
    
    <div class="card-body">
        <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
            <x-article-status-badge :status="$article->status" />
            
            @if ($article->is_featured)
                <span class="badge bg-warning bg-opacity-25 text-warning-emphasis">
                    Featured
                </span>
            @endif
            
            @if ($article->categories->count() > 0)
                @foreach ($article->categories->take(2) as $category)
                    <span class="badge bg-primary bg-opacity-25 text-primary-emphasis">
                        {{ $category->name }}
                    </span>
                @endforeach
            @endif
        </div>
        
        <h3 class="card-title fs-5 fw-semibold mb-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
            {{ $article->title }}
        </h3>
        
        @if ($showExcerpt && !$compact)
            <p class="card-text text-secondary small mb-3" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                {{ $article->excerpt_preview }}
            </p>
        @endif
        
        <div class="d-flex align-items-center justify-content-between small text-muted">
            <div class="d-flex align-items-center gap-3">
                @if ($showAuthor)
                    <span class="fw-medium text-dark">{{ $article->author_name }}</span>
                @endif
                
                @if ($showStats)
                    <span class="d-flex align-items-center gap-1">
                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        {{ $article->views_count }}
                    </span>
                    
                    <span class="d-flex align-items-center gap-1">
                        <svg style="width: 1rem; height: 1rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        {{ $article->likes->count() }}
                    </span>
                @endif
            </div>
            
            <span>{{ $article->reading_time }} min read</span>
        </div>
        
        @if ($article->published_at)
            <div class="small text-muted mt-2">
                {{ $article->published_at->diffForHumans() }}
            </div>
        @endif
    </div>
    
    {{ $slot }}
</div>
