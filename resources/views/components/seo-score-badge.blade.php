@props(['score', 'label' => 'SEO Score'])

@php
    $score = $score ?? 0;
    $colorClass = 'bg-secondary bg-opacity-25 text-secondary-emphasis';
    $barColor = 'bg-secondary';
    
    if ($score >= 80) {
        $colorClass = 'bg-success bg-opacity-25 text-success-emphasis';
        $barColor = 'bg-success';
        $grade = 'A';
    } elseif ($score >= 60) {
        $colorClass = 'bg-info bg-opacity-25 text-info-emphasis';
        $barColor = 'bg-info';
        $grade = 'B';
    } elseif ($score >= 40) {
        $colorClass = 'bg-warning bg-opacity-25 text-warning-emphasis';
        $barColor = 'bg-warning';
        $grade = 'C';
    } else {
        $colorClass = 'bg-danger bg-opacity-25 text-danger-emphasis';
        $barColor = 'bg-danger';
        $grade = 'D';
    }
@endphp

<div {{ $attributes->merge(['class' => 'd-inline-flex align-items-center gap-2']) }}>
    <span class="small text-secondary">{{ $label }}:</span>
    <div class="d-flex align-items-center gap-2">
        <div class="progress" style="width: 6rem; height: 0.5rem;">
            <div class="progress-bar {{ $barColor }}" role="progressbar" style="width: {{ $score }}%" aria-valuenow="{{ $score }}" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <span class="badge {{ $colorClass }}">
            {{ $score }}/100 ({{ $grade }})
        </span>
    </div>
</div>
