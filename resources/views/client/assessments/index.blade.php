@extends('client.layout')

@section('title', 'Assessments - Your Mind Aid')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">My Assessments</h4>
    <p class="text-muted mb-0 small">Complete your assigned assessments</p>
</div>

<!-- Pending Assessments -->
@if($pendingAssessments->isNotEmpty())
<div class="card mb-3">
    <div class="card-header bg-warning bg-opacity-10 border-0">
        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-exclamation-circle me-2"></i>
            Pending Assessments ({{ $pendingAssessments->count() }})
        </h6>
    </div>
    <div class="card-body">
        @foreach($pendingAssessments as $assessment)
        <div class="card border-warning mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="fw-bold mb-1">{{ $assessment->scale->name ?? 'Assessment' }}</h6>
                        @if($assessment->scale && $assessment->scale->description)
                        <p class="small text-muted mb-2">{{ Str::limit($assessment->scale->description, 100) }}</p>
                        @endif
                        <div class="small text-muted">
                            <i class="bi bi-calendar me-1"></i>
                            Assigned: {{ $assessment->assigned_at ? $assessment->assigned_at->format('M d, Y') : 'Recently' }}
                        </div>
                        @if($assessment->assignedByDoctor)
                        <div class="small text-muted">
                            <i class="bi bi-person me-1"></i>
                            By: {{ $assessment->assignedByDoctor->name ?? 'Doctor' }}
                        </div>
                        @endif
                    </div>
                    <a href="{{ route('client.assessments.show', $assessment->id) }}" class="btn btn-warning">
                        Start
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Completed Assessments -->
@if($completedAssessments->isNotEmpty())
<div class="card">
    <div class="card-header bg-white border-0">
        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-check-circle me-2 text-success"></i>
            Completed Assessments ({{ $completedAssessments->count() }})
        </h6>
    </div>
    <div class="card-body">
        @foreach($completedAssessments as $assessment)
        <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom">
            <div>
                <h6 class="fw-semibold mb-1">{{ $assessment->scale->name ?? 'Assessment' }}</h6>
                @if($assessment->total_score !== null)
                <div class="small">
                    <strong>Score:</strong> {{ $assessment->total_score }}
                </div>
                @endif
                <div class="small text-muted">
                    Completed: {{ $assessment->completed_at->format('M d, Y') }}
                </div>
            </div>
            <a href="{{ route('client.assessments.show', $assessment->id) }}" class="btn btn-sm btn-outline-primary">
                View Results
            </a>
        </div>
        @endforeach
    </div>
</div>
@endif

@if((!$pendingAssessments || $pendingAssessments->isEmpty()) && (!$completedAssessments || $completedAssessments->isEmpty()))
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-clipboard-check display-1 text-muted mb-3"></i>
        <p class="text-muted mb-0">No assessments assigned yet.</p>
    </div>
</div>
@endif
@endsection
