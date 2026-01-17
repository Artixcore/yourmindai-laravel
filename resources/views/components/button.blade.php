@props(['variant' => 'primary', 'size' => 'md', 'type' => 'button'])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-lg transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed';
    
    $variants = [
        'primary' => 'bg-teal-700 text-white hover:bg-teal-800 focus:ring-teal-500',
        'secondary' => 'bg-indigo-700 text-white hover:bg-indigo-800 focus:ring-indigo-500',
        'outline' => 'border-2 border-teal-700 text-teal-700 hover:bg-teal-50 focus:ring-teal-500',
        'ghost' => 'text-teal-700 hover:bg-teal-50 focus:ring-teal-500',
    ];
    
    $sizes = [
        'sm' => 'px-3 py-1.5 text-sm',
        'md' => 'px-4 py-2 text-base',
        'lg' => 'px-6 py-3 text-lg',
    ];
    
    $classes = $baseClasses . ' ' . ($variants[$variant] ?? $variants['primary']) . ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</button>
