@props(['padding' => 'p-6', 'shadow' => 'shadow-lg'])

<div {{ $attributes->merge(['class' => "bg-white rounded-xl {$shadow} {$padding} transition-all duration-200 hover:shadow-xl"]) }}>
    {{ $slot }}
</div>
