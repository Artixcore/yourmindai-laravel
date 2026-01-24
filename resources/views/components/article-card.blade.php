@props([
    'article',
    'showAuthor' => true,
    'showExcerpt' => true,
    'showStats' => true,
    'compact' => false
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition']) }}>
    @if ($article->featured_image)
        <div class="aspect-video w-full overflow-hidden">
            <img src="{{ asset('storage/' . $article->featured_image) }}" 
                 alt="{{ $article->title }}" 
                 class="w-full h-full object-cover">
        </div>
    @endif
    
    <div class="p-4">
        <div class="flex items-center gap-2 mb-2">
            <x-article-status-badge :status="$article->status" />
            
            @if ($article->is_featured)
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">
                    Featured
                </span>
            @endif
            
            @if ($article->categories->count() > 0)
                @foreach ($article->categories->take(2) as $category)
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-purple-100 text-purple-800">
                        {{ $category->name }}
                    </span>
                @endforeach
            @endif
        </div>
        
        <h3 class="text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
            {{ $article->title }}
        </h3>
        
        @if ($showExcerpt && !$compact)
            <p class="text-gray-600 text-sm mb-3 line-clamp-3">
                {{ $article->excerpt_preview }}
            </p>
        @endif
        
        <div class="flex items-center justify-between text-sm text-gray-500">
            <div class="flex items-center gap-4">
                @if ($showAuthor)
                    <span class="font-medium text-gray-700">{{ $article->author_name }}</span>
                @endif
                
                @if ($showStats)
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        {{ $article->views_count }}
                    </span>
                    
                    <span class="flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        {{ $article->likes->count() }}
                    </span>
                @endif
            </div>
            
            <span>{{ $article->reading_time }} min read</span>
        </div>
        
        @if ($article->published_at)
            <div class="text-xs text-gray-400 mt-2">
                {{ $article->published_at->diffForHumans() }}
            </div>
        @endif
    </div>
    
    {{ $slot }}
</div>
