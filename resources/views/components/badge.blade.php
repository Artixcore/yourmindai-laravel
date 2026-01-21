@props(['variant' => 'primary', 'size' => 'md'])

@php
    // Bootstrap 5.7 bg-*-subtle pattern
    $variants = [
        'primary' => 'bg-primary-subtle text-primary-emphasis border border-primary-subtle',
        'secondary' => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle',
        'success' => 'bg-success-subtle text-success-emphasis border border-success-subtle',
        'warning' => 'bg-warning-subtle text-warning-emphasis border border-warning-subtle',
        'danger' => 'bg-danger-subtle text-danger-emphasis border border-danger-subtle',
        'info' => 'bg-info-subtle text-info-emphasis border border-info-subtle',
        'default' => 'bg-secondary-subtle text-secondary-emphasis border border-secondary-subtle',
    ];
    
    $sizes = [
        'sm' => 'badge-sm',
        'md' => '',
        'lg' => 'badge-lg',
    ];
    
    $classes = 'badge ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
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
