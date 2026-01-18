@props(['variant' => 'primary', 'size' => 'md', 'type' => 'button'])

@php
    $variants = [
        'primary' => 'btn-primary',
        'secondary' => 'btn-secondary',
        'outline' => 'btn-outline-primary',
        'ghost' => 'btn-link text-teal-700',
    ];
    
    $sizes = [
        'sm' => 'btn-sm',
        'md' => '',
        'lg' => 'btn-lg',
    ];
    
    $classes = 'btn ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
    
    if ($variant === 'ghost') {
        $classes .= ' hover-bg-teal-50';
    }
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
