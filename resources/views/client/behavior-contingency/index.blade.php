@extends('client.layout')

@section('title', 'Contingency Plan - Your Mind Aid')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Contingency Plan</h4>
    <p class="text-muted mb-0 small">Your behavior plans with daily check-ins</p>
</div>

@if(!$plans || $plans->isEmpty())
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-clipboard-check display-1 text-muted mb-3"></i>
        <p class="text-muted mb-0">No contingency plans assigned yet.</p>
        <p class="text-muted small">Your healthcare provider will create a plan for you when needed.</p>
    </div>
</div>
@else
@foreach($plans as $plan)
<div class="card mb-3">
    <div class="card-header bg-primary bg-opacity-10 border-0">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold">
                <i class="bi bi-clipboard-check me-2"></i>
                {{ $plan->title }}
            </h6>
            @if($plan->isActive())
            <span class="badge bg-success">Active</span>
            @else
            <span class="badge bg-secondary">Archived</span>
            @endif
        </div>
    </div>
    <div class="card-body">
        <p class="small text-muted mb-2">
            {{ $plan->starts_at->format('M d, Y') }}
            @if($plan->ends_at)
            – {{ $plan->ends_at->format('M d, Y') }}
            @endif
        </p>
        <p class="small mb-0">{{ $plan->items->count() }} behavior(s) to track</p>
        <div class="d-grid gap-2 mt-3">
            <a href="{{ route('client.contingency-plans.show', $plan) }}" class="btn btn-primary">
                <i class="bi bi-eye me-2"></i>
                View Details & Check-in
            </a>
            <a href="{{ route('client.contingency-plans.history', $plan) }}" class="btn btn-outline-secondary">
                <i class="bi bi-clock-history me-2"></i>
                View History
            </a>
        </div>
    </div>
</div>
@endforeach
@endif
@endsection
