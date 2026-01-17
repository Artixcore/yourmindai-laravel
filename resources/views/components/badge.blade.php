@props(['variant' => 'primary', 'size' => 'md', 'type' => null])

@php
    $variants = [
        'primary' => 'bg-teal-100 text-teal-800',
        'secondary' => 'bg-indigo-100 text-indigo-800',
        'success' => 'bg-emerald-100 text-emerald-800',
        'warning' => 'bg-amber-100 text-amber-800',
        'danger' => 'bg-red-100 text-red-800',
        'info' => 'bg-blue-100 text-blue-800',
        'default' => 'bg-stone-100 text-stone-800',
    ];
    
    $sizes = [
        'sm' => 'px-2 py-0.5 text-xs',
        'md' => 'px-3 py-1 text-sm',
        'lg' => 'px-4 py-1.5 text-base',
    ];
    
    $selectedVariant = $type ?? $variant;
    $classes = 'inline-flex items-center font-medium rounded-full ' . ($variants[$selectedVariant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
