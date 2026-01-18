@props(['variant' => 'primary', 'size' => 'md', 'type' => null])

@php
    $variants = [
        'primary' => 'bg-teal-100 text-teal-800',
        'secondary' => 'bg-indigo-100 text-indigo-800',
        'success' => 'bg-success bg-opacity-25 text-success',
        'warning' => 'bg-warning bg-opacity-25 text-warning',
        'danger' => 'bg-danger bg-opacity-25 text-danger',
        'info' => 'bg-info bg-opacity-25 text-info',
        'default' => 'bg-secondary bg-opacity-25 text-secondary',
    ];
    
    $sizes = [
        'sm' => 'badge-sm',
        'md' => '',
        'lg' => 'badge-lg',
    ];
    
    $selectedVariant = $type ?? $variant;
    $classes = 'badge rounded-pill ' . ($variants[$selectedVariant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>

<style>
    .badge-sm {
        padding: 0.125rem 0.5rem;
        font-size: 0.75rem;
    }
    .badge-lg {
        padding: 0.375rem 1rem;
        font-size: 1rem;
    }
</style>
