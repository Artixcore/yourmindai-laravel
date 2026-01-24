@props([
    'rating' => 0,
    'max' => 5,
    'name' => 'rating',
    'readonly' => false,
    'size' => 'md'
])

@php
    $sizeClasses = [
        'sm' => 'w-4 h-4',
        'md' => 'w-6 h-6',
        'lg' => 'w-8 h-8',
        'xl' => 'w-10 h-10'
    ];
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<div class="star-rating-container" data-readonly="{{ $readonly ? 'true' : 'false' }}">
    <div class="flex items-center gap-1">
        @for ($i = 1; $i <= $max; $i++)
            <button 
                type="button"
                class="star-button {{ $sizeClass }} {{ $readonly ? 'cursor-default' : 'cursor-pointer hover:scale-110 transition-transform' }}"
                data-rating="{{ $i }}"
                data-name="{{ $name }}"
                {{ $readonly ? 'disabled' : '' }}
            >
                @if ($i <= $rating)
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="text-yellow-400">
                        <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" />
                    </svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="text-gray-300">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />
                    </svg>
                @endif
            </button>
        @endfor
    </div>
    
    @unless ($readonly)
        <input type="hidden" name="{{ $name }}" id="{{ $name }}" value="{{ $rating }}" class="star-rating-input">
    @endunless
</div>

@unless ($readonly)
<script>
document.addEventListener('DOMContentLoaded', function() {
    const containers = document.querySelectorAll('.star-rating-container[data-readonly="false"]');
    
    containers.forEach(container => {
        const stars = container.querySelectorAll('.star-button');
        const input = container.querySelector('.star-rating-input');
        
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = parseInt(this.dataset.rating);
                const name = this.dataset.name;
                
                // Update input value
                input.value = rating;
                
                // Update star display
                updateStars(stars, rating);
            });
            
            // Hover effect
            star.addEventListener('mouseenter', function() {
                const rating = parseInt(this.dataset.rating);
                updateStars(stars, rating);
            });
        });
        
        // Reset on mouse leave
        container.addEventListener('mouseleave', function() {
            const currentRating = parseInt(input.value) || 0;
            updateStars(stars, currentRating);
        });
    });
    
    function updateStars(stars, rating) {
        stars.forEach((star, index) => {
            const svg = star.querySelector('svg');
            if (index < rating) {
                // Filled star
                svg.innerHTML = '<path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.007 5.404.433c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.433 2.082-5.006z" clip-rule="evenodd" />';
                svg.setAttribute('fill', 'currentColor');
                svg.classList.remove('text-gray-300');
                svg.classList.add('text-yellow-400');
            } else {
                // Empty star
                svg.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.563.563 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z" />';
                svg.setAttribute('fill', 'none');
                svg.classList.remove('text-yellow-400');
                svg.classList.add('text-gray-300');
            }
        });
    }
});
</script>
@endunless
