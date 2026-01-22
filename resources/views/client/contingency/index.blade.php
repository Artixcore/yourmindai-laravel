@extends('client.layout')

@section('title', 'Emergency Plans - Your Mind Aid')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Emergency Plans</h4>
    <p class="text-muted mb-0 small">Your contingency plans for emergency situations</p>
</div>

@if($contingencyPlans->isEmpty())
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-shield-exclamation display-1 text-muted mb-3"></i>
        <p class="text-muted mb-0">No emergency plans available.</p>
        <p class="text-muted small">Your healthcare provider will create plans for you when needed.</p>
    </div>
</div>
@else
@foreach($contingencyPlans as $plan)
<div class="card mb-3 border-danger">
    <div class="card-header bg-danger bg-opacity-10 border-0">
        <h6 class="mb-0 fw-bold text-danger">
            <i class="bi bi-shield-exclamation me-2"></i>
            {{ $plan->title }}
        </h6>
    </div>
    <div class="card-body">
        @if($plan->trigger_conditions && count($plan->trigger_conditions) > 0)
        <div class="mb-3">
            <h6 class="small fw-semibold mb-2">Trigger Conditions:</h6>
            <ul class="small mb-0">
                @foreach($plan->trigger_conditions as $condition)
                <li>{{ is_string($condition) ? $condition : ($condition['description'] ?? 'Condition') }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        
        @if($plan->emergency_contacts && count($plan->emergency_contacts) > 0)
        <div class="mb-3">
            <h6 class="small fw-semibold mb-2">Emergency Contacts:</h6>
            @foreach($plan->emergency_contacts as $contact)
            <div class="small mb-1">
                <strong>{{ $contact['name'] ?? 'Contact' }}</strong>
                @if(isset($contact['phone']))
                <a href="tel:{{ $contact['phone'] }}" class="ms-2">
                    <i class="bi bi-telephone"></i> {{ $contact['phone'] }}
                </a>
                @endif
            </div>
            @endforeach
        </div>
        @endif
        
        <div class="d-grid gap-2">
            <a href="{{ route('client.contingency.show', $plan->id) }}" class="btn btn-danger">
                <i class="bi bi-eye me-2"></i>
                View Details
            </a>
        </div>
    </div>
</div>
@endforeach
@endif
@endsection
