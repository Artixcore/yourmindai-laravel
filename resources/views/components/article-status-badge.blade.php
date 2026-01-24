@props(['status'])

@php
    $colors = [
        'draft' => 'bg-secondary bg-opacity-25 text-secondary-emphasis',
        'pending_review' => 'bg-warning bg-opacity-25 text-warning-emphasis',
        'approved' => 'bg-info bg-opacity-25 text-info-emphasis',
        'published' => 'bg-success bg-opacity-25 text-success-emphasis',
        'rejected' => 'bg-danger bg-opacity-25 text-danger-emphasis',
    ];
    
    $labels = [
        'draft' => 'Draft',
        'pending_review' => 'Pending Review',
        'approved' => 'Approved',
        'published' => 'Published',
        'rejected' => 'Rejected',
    ];
    
    $colorClass = $colors[$status] ?? 'bg-secondary bg-opacity-25 text-secondary-emphasis';
    $label = $labels[$status] ?? ucfirst($status);
@endphp

<span {{ $attributes->merge(['class' => "badge rounded-pill {$colorClass}"]) }}>
    {{ $label }}
</span>
