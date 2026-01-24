@props(['score', 'label' => 'SEO Score'])

@php
    $score = $score ?? 0;
    $colorClass = 'bg-gray-100 text-gray-800';
    $barColor = 'bg-gray-400';
    
    if ($score >= 80) {
        $colorClass = 'bg-green-100 text-green-800';
        $barColor = 'bg-green-500';
        $grade = 'A';
    } elseif ($score >= 60) {
        $colorClass = 'bg-blue-100 text-blue-800';
        $barColor = 'bg-blue-500';
        $grade = 'B';
    } elseif ($score >= 40) {
        $colorClass = 'bg-yellow-100 text-yellow-800';
        $barColor = 'bg-yellow-500';
        $grade = 'C';
    } else {
        $colorClass = 'bg-red-100 text-red-800';
        $barColor = 'bg-red-500';
        $grade = 'D';
    }
@endphp

<div {{ $attributes->merge(['class' => 'inline-flex items-center gap-2']) }}>
    <span class="text-sm text-gray-600">{{ $label }}:</span>
    <div class="flex items-center gap-2">
        <div class="w-24 h-2 bg-gray-200 rounded-full overflow-hidden">
            <div class="{{ $barColor }} h-full transition-all duration-300" style="width: {{ $score }}%"></div>
        </div>
        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $colorClass }}">
            {{ $score }}/100 ({{ $grade }})
        </span>
    </div>
</div>
