@props(['padding' => 'p-4 p-md-5', 'shadow' => 'shadow'])

<div {{ $attributes->merge(['class' => "card bg-white rounded-xl {$shadow}"]) }}>
    <div class="card-body {$padding}">
        {{ $slot }}
    </div>
</div>
