@extends('client.layout')

@section('title', 'Assessment Results - Your Mind Aid')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">{{ $assessment->scale->name ?? 'Assessment' }}</h4>
    <p class="text-muted mb-0 small">Completed on {{ $assessment->completed_at->format('M d, Y') }}</p>
</div>

<div class="card mb-3">
    <div class="card-header bg-success bg-opacity-10 border-0">
        <h6 class="mb-0 fw-semibold text-success">
            <i class="bi bi-check-circle me-2"></i>
            Assessment Completed
        </h6>
    </div>
    <div class="card-body">
        @if($assessment->total_score !== null)
        <div class="text-center mb-4">
            <div class="display-4 fw-bold text-primary mb-2">{{ $assessment->total_score }}</div>
            <div class="text-muted">Total Score</div>
            @if($assessment->scale->scoring_rules && isset($assessment->scale->scoring_rules['max_score']))
            <div class="small text-muted">
                out of {{ $assessment->scale->scoring_rules['max_score'] }}
            </div>
            @endif
        </div>
        @endif
        
        @if($assessment->interpretation)
        <div class="alert alert-info">
            <h6 class="fw-semibold mb-2">Interpretation</h6>
            <p class="mb-0">{{ $assessment->interpretation }}</p>
        </div>
        @endif
        
        @if($assessment->sub_scores && count($assessment->sub_scores) > 0)
        <div class="mb-3">
            <h6 class="fw-semibold mb-3">Sub-Scores</h6>
            @foreach($assessment->sub_scores as $subScoreName => $subScoreValue)
            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                <span>{{ $subScoreName }}</span>
                <strong>{{ $subScoreValue }}</strong>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

@if($assessment->assignedByDoctor)
<div class="card mb-3">
    <div class="card-header bg-white border-0">
        <h6 class="mb-0 fw-semibold">Assigned By</h6>
    </div>
    <div class="card-body">
        <div class="d-flex align-items-center">
            <i class="bi bi-person-circle fs-3 text-muted me-3"></i>
            <div>
                <strong>{{ $assessment->assignedByDoctor->name ?? 'Doctor' }}</strong>
                <div class="small text-muted">
                    Assigned on {{ $assessment->assigned_at ? $assessment->assigned_at->format('M d, Y') : 'Recently' }}
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<div class="d-grid gap-2">
    <a href="{{ route('client.assessments.index') }}" class="btn btn-outline-primary">
        <i class="bi bi-arrow-left me-2"></i>
        Back to Assessments
    </a>
</div>
@endsection
