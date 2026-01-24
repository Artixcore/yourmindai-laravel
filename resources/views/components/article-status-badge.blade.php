@props(['status'])

@php
    $colors = [
        'draft' => 'bg-gray-100 text-gray-800',
        'pending_review' => 'bg-yellow-100 text-yellow-800',
        'approved' => 'bg-blue-100 text-blue-800',
        'published' => 'bg-green-100 text-green-800',
        'rejected' => 'bg-red-100 text-red-800',
    ];
    
    $labels = [
        'draft' => 'Draft',
        'pending_review' => 'Pending Review',
        'approved' => 'Approved',
        'published' => 'Published',
        'rejected' => 'Rejected',
    ];
    
    $colorClass = $colors[$status] ?? 'bg-gray-100 text-gray-800';
    $label = $labels[$status] ?? ucfirst($status);
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$colorClass}"]) }}>
    {{ $label }}
</span>
