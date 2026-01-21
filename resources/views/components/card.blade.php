@props(['padding' => 'p-4', 'shadow' => 'shadow-sm'])

<div {{ $attributes->merge(['class' => "card border-0 {$shadow}"]) }}>
    <div class="card-body {{ $padding }}">
        {{ $slot }}
    </div>
</div>
