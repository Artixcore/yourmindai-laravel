@extends('layouts.app')

@section('title', 'Contingency Plan Details')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('dashboard')],
            ['label' => 'Patients', 'url' => route('patients.index')],
            ['label' => $patient->name, 'url' => route('patients.show', $patient)],
            ['label' => 'Contingency Plans', 'url' => route('patients.contingency-plans.index', $patient)],
            ['label' => $contingencyPlan->title]
        ]" />
        <h1 class="h3 mb-1 fw-semibold">{{ $contingencyPlan->title }}</h1>
        <p class="text-muted mb-0">Emergency plan details and activation history</p>
    </div>
    <div class="btn-group">
        <a href="{{ route('patients.contingency-plans.edit', ['patient' => $patient, 'contingencyPlan' => $contingencyPlan]) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-2"></i>Edit
        </a>
        @if($contingencyPlan->isActive())
        <form method="POST" action="{{ route('patients.contingency.activate', ['patient' => $patient, 'contingencyPlan' => $contingencyPlan]) }}" class="d-inline">
            @csrf
            <input type="hidden" name="trigger_reason" value="Manually activated by healthcare provider">
            <button type="submit" class="btn btn-warning" onclick="return confirm('Are you sure you want to manually activate this plan?');">
                <i class="bi bi-exclamation-triangle me-2"></i>Activate
            </button>
        </form>
        @endif
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-8">
        <!-- Plan Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0 fw-semibold">Plan Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <p class="small text-muted mb-1">Status</p>
                        <p class="mb-0">
                            @if($contingencyPlan->status === 'active')
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-6">
                        <p class="small text-muted mb-1">Created Date</p>
                        <p class="fw-semibold mb-0">{{ $contingencyPlan->created_at->format('M d, Y') }}</p>
                    </div>
                    <div class="col-6">
                        <p class="small text-muted mb-1">Created By</p>
                        <p class="fw-semibold mb-0">{{ $contingencyPlan->createdByDoctor->name ?? 'Unknown' }}</p>
                    </div>
                    @if($contingencyPlan->activated_at)
                    <div class="col-6">
                        <p class="small text-muted mb-1">Last Activated</p>
                        <p class="fw-semibold mb-0">{{ $contingencyPlan->activated_at->format('M d, Y h:i A') }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        
        <!-- Trigger Conditions -->
        @if($contingencyPlan->trigger_conditions && count($contingencyPlan->trigger_conditions) > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0 fw-semibold">Trigger Conditions</h5>
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    @foreach($contingencyPlan->trigger_conditions as $condition)
                    <li class="mb-2">{{ is_string($condition) ? $condition : ($condition['description'] ?? 'Condition') }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
        @endif
        
        <!-- Actions -->
        @if($contingencyPlan->actions && count($contingencyPlan->actions) > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0 fw-semibold">Actions</h5>
            </div>
            <div class="card-body">
                <ol class="mb-0">
                    @foreach($contingencyPlan->actions as $action)
                    <li class="mb-2">{{ is_string($action) ? $action : ($action['description'] ?? 'Action') }}</li>
                    @endforeach
                </ol>
            </div>
        </div>
        @endif
        
        <!-- Emergency Contacts -->
        @if($contingencyPlan->emergency_contacts && count($contingencyPlan->emergency_contacts) > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0 fw-semibold">Emergency Contacts</h5>
            </div>
            <div class="card-body">
                @foreach($contingencyPlan->emergency_contacts as $contact)
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
                                <i class="bi bi-telephone me-1"></i>{{ $contact['phone'] }}
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        
        <!-- Activation History -->
        @if($contingencyPlan->activations && $contingencyPlan->activations->count() > 0)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0 fw-semibold">Activation History</h5>
            </div>
            <div class="card-body">
                @foreach($contingencyPlan->activations->sortByDesc('activated_at') as $activation)
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
                    <div class="small mb-2">
                        <strong>Reason:</strong> {{ $activation->trigger_reason }}
                    </div>
                    @endif
                    @if($activation->resolved_at)
                    <div class="small text-success">
                        <i class="bi bi-check-circle me-1"></i>
                        Resolved on {{ $activation->resolved_at->format('M d, Y') }}
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
    
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0 fw-semibold">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('patients.contingency-plans.edit', ['patient' => $patient, 'contingencyPlan' => $contingencyPlan]) }}" class="btn btn-outline-primary">
                        <i class="bi bi-pencil me-2"></i>Edit Plan
                    </a>
                    @if($contingencyPlan->isActive())
                    <form method="POST" action="{{ route('patients.contingency.activate', ['patient' => $patient, 'contingencyPlan' => $contingencyPlan]) }}" class="d-inline">
                        @csrf
                        <input type="hidden" name="trigger_reason" value="Manually activated by healthcare provider">
                        <button type="submit" class="btn btn-outline-warning w-100" onclick="return confirm('Are you sure you want to manually activate this plan?');">
                            <i class="bi bi-exclamation-triangle me-2"></i>Manually Activate
                        </button>
                    </form>
                    @endif
                    <form method="POST" action="{{ route('patients.contingency-plans.destroy', ['patient' => $patient, 'contingencyPlan' => $contingencyPlan]) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this plan?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-trash me-2"></i>Delete Plan
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('patients.contingency-plans.index', $patient) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Plans
    </a>
</div>
@endsection
