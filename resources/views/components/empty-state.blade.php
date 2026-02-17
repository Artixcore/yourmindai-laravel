@props(['message' => 'No data available.', 'icon' => 'bi-inbox'])
<div class="text-center py-5">
    <i class="bi {{ $icon }} text-muted" style="font-size: 3rem;"></i>
    <h5 class="mt-3 mb-2">{{ $message }}</h5>
    {{ $slot ?? '' }}
</div>
