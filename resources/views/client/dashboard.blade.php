@extends('client.layout')

@section('title', 'Dashboard - Your Mind Aid')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Welcome back!</h4>
    <p class="text-muted mb-0 small">Here's your therapy overview</p>
</div>

<!-- Quick Stats -->
<div class="row g-3 mb-4">
    <div class="col-6">
        <div class="stats-card">
            <div class="number">{{ $stats['total_sessions'] }}</div>
            <div class="label">Sessions</div>
        </div>
    </div>
    <div class="col-6">
        <div class="stats-card">
            <div class="number">{{ $stats['pending_assessments'] }}</div>
            <div class="label">Pending Assessments</div>
        </div>
    </div>
    <div class="col-6">
        <div class="stats-card">
            <div class="number">{{ $stats['active_devices'] }}</div>
            <div class="label">Active Devices</div>
        </div>
    </div>
    <div class="col-6">
        <div class="stats-card">
            <div class="number">{{ $stats['total_points'] }}</div>
            <div class="label">Points</div>
        </div>
    </div>
</div>

<!-- Recent Sessions -->
@if($sessions->isNotEmpty())
<div class="card mb-3">
    <div class="card-header bg-white border-0">
        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-calendar-check me-2 text-primary"></i>
            Recent Sessions
        </h6>
    </div>
    <div class="card-body">
        @foreach($sessions->take(3) as $session)
        <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom">
            <div>
                <h6 class="mb-1 fw-semibold">{{ $session->title ?? 'Session #' . $session->id }}</h6>
                <small class="text-muted">
                    <i class="bi bi-calendar3 me-1"></i>
                    {{ $session->created_at->format('M d, Y') }}
                </small>
            </div>
            <a href="{{ route('client.sessions.show', $session->id) }}" class="btn btn-sm btn-outline-primary">
                View
            </a>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Pending Assessments -->
<div class="card mb-3">
    <div class="card-header bg-white border-0">
        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-clipboard-check me-2 text-warning"></i>
            Pending Assessments
        </h6>
    </div>
    <div class="card-body">
        @if($pendingAssessments->isNotEmpty())
            @foreach($pendingAssessments as $assessment)
            <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom">
                <div>
                    <h6 class="mb-1 fw-semibold">{{ $assessment->scale->name ?? 'Assessment' }}</h6>
                    <small class="text-muted">
                        Assigned: {{ $assessment->assigned_at ? $assessment->assigned_at->format('M d, Y') : 'Recently' }}
                    </small>
                </div>
                <a href="{{ route('client.assessments.show', $assessment->id) }}" class="btn btn-sm btn-warning">
                    Complete
                </a>
            </div>
            @endforeach
            <a href="{{ route('client.assessments.index') }}" class="btn btn-sm btn-outline-primary w-100">
                View All Assessments
            </a>
        @else
            <div class="text-center py-3">
                <i class="bi bi-clipboard-check text-muted" style="font-size: 2rem;"></i>
                <p class="text-muted mb-2 mt-2">No pending assessments</p>
                <p class="text-muted small mb-0">Your healthcare provider will assign assessments when needed.</p>
            </div>
            <a href="{{ route('client.assessments.index') }}" class="btn btn-sm btn-outline-primary w-100 mt-2">
                View All Assessments
            </a>
        @endif
    </div>
</div>

<!-- Active Devices -->
@if($devices->isNotEmpty())
<div class="card mb-3">
    <div class="card-header bg-white border-0">
        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-phone me-2 text-success"></i>
            Active Devices
        </h6>
    </div>
    <div class="card-body">
        @foreach($devices->take(3) as $device)
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div>
                <strong>{{ $device->device_name }}</strong>
                <br>
                <small class="text-muted">
                    {{ ucfirst($device->device_type) }} • {{ $device->os_type ?? 'Unknown OS' }}
                </small>
            </div>
            @if($device->last_active_at)
            <small class="text-muted">
                {{ $device->last_active_at->diffForHumans() }}
            </small>
            @endif
        </div>
        @endforeach
        <a href="{{ route('client.devices.index') }}" class="btn btn-sm btn-outline-primary w-100 mt-2">
            Manage Devices
        </a>
    </div>
</div>
@endif

<!-- Active Contingency Plans -->
<div class="card mb-3">
    <div class="card-header bg-white border-0">
        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-shield-exclamation me-2 text-danger"></i>
            Emergency Plans
        </h6>
    </div>
    <div class="card-body">
        @if($contingencyPlans->isNotEmpty())
            @foreach($contingencyPlans as $plan)
            <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom">
                <div>
                    <h6 class="mb-1 fw-semibold">{{ $plan->title }}</h6>
                    <small class="text-muted">
                        Created: {{ $plan->created_at->format('M d, Y') }}
                    </small>
                </div>
                <a href="{{ route('client.contingency.show', $plan->id) }}" class="btn btn-sm btn-outline-danger">
                    View
                </a>
            </div>
            @endforeach
            <a href="{{ route('client.contingency.index') }}" class="btn btn-sm btn-outline-primary w-100">
                View All Plans
            </a>
        @else
            <div class="text-center py-3">
                <i class="bi bi-shield-exclamation text-muted" style="font-size: 2rem;"></i>
                <p class="text-muted mb-2 mt-2">No emergency plans available</p>
                <p class="text-muted small mb-0">Your healthcare provider will create plans for you when needed.</p>
            </div>
            <a href="{{ route('client.contingency.index') }}" class="btn btn-sm btn-outline-primary w-100 mt-2">
                View All Plans
            </a>
        @endif
    </div>
</div>

<!-- Quick Actions -->
<div class="card">
    <div class="card-header bg-white border-0">
        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-lightning-fill me-2 text-primary"></i>
            Quick Actions
        </h6>
    </div>
    <div class="card-body">
        <div class="d-grid gap-2">
            <a href="{{ route('client.assessments.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-clipboard-check me-2"></i>
                Complete Assessment
            </a>
            <a href="{{ route('client.devices.index') }}" class="btn btn-outline-primary">
                <i class="bi bi-phone me-2"></i>
                Manage Devices
            </a>
            <a href="{{ route('client.contingency.index') }}" class="btn btn-outline-danger">
                <i class="bi bi-shield-exclamation me-2"></i>
                Emergency Plans
            </a>
        </div>
    </div>
</div>
@endsection
