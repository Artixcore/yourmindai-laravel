@extends('client.layout')

@section('title', 'Emergency Plan Details - Your Mind Aid')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">{{ $plan->title }}</h4>
    <p class="text-muted mb-0 small">Created on {{ $plan->created_at->format('M d, Y') }}</p>
</div>

<!-- Plan Details -->
<div class="card mb-3">
    <div class="card-header bg-danger bg-opacity-10 border-0">
        <h6 class="mb-0 fw-semibold text-danger">
            <i class="bi bi-shield-exclamation me-2"></i>
            Plan Information
        </h6>
    </div>
    <div class="card-body">
        @if($plan->trigger_conditions && count($plan->trigger_conditions) > 0)
        <div class="mb-4">
            <h6 class="fw-semibold mb-3">When to Activate:</h6>
            <ul class="mb-0">
                @foreach($plan->trigger_conditions as $condition)
                <li class="mb-2">{{ is_string($condition) ? $condition : ($condition['description'] ?? 'Condition') }}</li>
                @endforeach
            </ul>
        </div>
        @endif
        
        @if($plan->actions && count($plan->actions) > 0)
        <div class="mb-4">
            <h6 class="fw-semibold mb-3">What Will Happen:</h6>
            <ol class="mb-0">
                @foreach($plan->actions as $action)
                <li class="mb-2">{{ is_string($action) ? $action : ($action['description'] ?? 'Action') }}</li>
                @endforeach
            </ol>
        </div>
        @endif
        
        @if($plan->emergency_contacts && count($plan->emergency_contacts) > 0)
        <div>
            <h6 class="fw-semibold mb-3">Emergency Contacts:</h6>
            @foreach($plan->emergency_contacts as $contact)
            <div class="card bg-light mb-2">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $contact['name'] ?? 'Contact' }}</strong>
                            @if(isset($contact['relationship']))
                            <div class="small text-muted">{{ $contact['relationship'] }}</div>
                            @endif
                        </div>
                        @if(isset($contact['phone']))
                        <a href="tel:{{ $contact['phone'] }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-telephone me-1"></i>
                            Call
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

<!-- Activation History -->
@if($plan->activations && $plan->activations->count() > 0)
<div class="card mb-3">
    <div class="card-header bg-white border-0">
        <h6 class="mb-0 fw-semibold">Activation History</h6>
    </div>
    <div class="card-body">
        @foreach($plan->activations->take(5) as $activation)
        <div class="border-bottom pb-3 mb-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <strong>Activated</strong>
                    <div class="small text-muted">
                        {{ $activation->activated_at->format('M d, Y h:i A') }}
                    </div>
                </div>
                <span class="badge bg-warning">Triggered by {{ ucfirst($activation->triggered_by) }}</span>
            </div>
            @if($activation->trigger_reason)
            <div class="small">
                <strong>Reason:</strong> {{ $activation->trigger_reason }}
            </div>
            @endif
            @if($activation->resolved_at)
            <div class="small text-success mt-2">
                <i class="bi bi-check-circle me-1"></i>
                Resolved on {{ $activation->resolved_at->format('M d, Y') }}
            </div>
            @endif
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Activate Button -->
@if($plan->isActive())
<div class="card border-danger">
    <div class="card-body text-center">
        <h6 class="fw-bold text-danger mb-3">Need to Activate This Plan?</h6>
        <p class="small text-muted mb-3">Only activate if you're experiencing the trigger conditions listed above.</p>
        <button type="button" class="btn btn-danger btn-lg" data-bs-toggle="modal" data-bs-target="#activateModal">
            <i class="bi bi-exclamation-triangle me-2"></i>
            Activate Emergency Plan
        </button>
    </div>
</div>
@endif

<!-- Activation Modal -->
<div class="modal fade" id="activateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Activate Emergency Plan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('client.contingency.activate', $plan->id) }}">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Warning:</strong> Activating this plan will notify your healthcare provider and emergency contacts.
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Why are you activating this plan?</label>
                        <textarea 
                            class="form-control" 
                            name="trigger_reason" 
                            rows="4" 
                            required
                            placeholder="Describe your current situation..."
                        ></textarea>
                        <small class="text-muted">Please provide details about why you need to activate this emergency plan.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Activate Plan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('client.contingency.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>
        Back to Plans
    </a>
</div>
@endsection
