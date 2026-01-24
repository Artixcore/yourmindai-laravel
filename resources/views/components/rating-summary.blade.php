@props([
    'averageRating' => 0,
    'totalReviews' => 0,
    'ratingDistribution' => [],
    'showDistribution' => true
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg shadow-sm border border-gray-200 p-6']) }}>
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <div class="text-center">
                <div class="text-4xl font-bold text-gray-900">{{ number_format($averageRating, 1) }}</div>
                <x-star-rating :rating="$averageRating" :readonly="true" size="md" class="mt-2" />
                <div class="text-sm text-gray-600 mt-1">{{ $totalReviews }} {{ Str::plural('review', $totalReviews) }}</div>
            </div>
        </div>
    </div>
    
    @if ($showDistribution && $totalReviews > 0)
        <div class="space-y-2">
            @for ($i = 5; $i >= 1; $i--)
                @php
                    $count = $ratingDistribution[$i] ?? 0;
                    $percentage = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                @endphp
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-1 w-12">
                        <span class="text-sm font-medium text-gray-700">{{ $i }}</span>
                        <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="bg-gray-200 rounded-full h-2 overflow-hidden">
                            <div class="bg-yellow-400 h-full rounded-full transition-all duration-300" 
                                 style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                    <div class="w-12 text-right">
                        <span class="text-sm text-gray-600">{{ $count }}</span>
                    </div>
                </div>
            @endfor
        </div>
    @endif
    
    {{ $slot }}
</div>
