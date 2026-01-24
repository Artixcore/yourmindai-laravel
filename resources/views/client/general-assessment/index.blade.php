@extends('client.layout')

@section('title', 'General Assessments - Your Mind Aid')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">General Assessments</h4>
    <p class="text-muted mb-0 small">Assessments assigned by your therapist</p>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-4">
        <div class="stats-card bg-warning bg-opacity-10">
            <div class="number text-warning">{{ $stats['pending'] }}</div>
            <div class="label">Pending</div>
        </div>
    </div>
    <div class="col-4">
        <div class="stats-card bg-primary bg-opacity-10">
            <div class="number text-primary">{{ $stats['in_progress'] }}</div>
            <div class="label">In Progress</div>
        </div>
    </div>
    <div class="col-4">
        <div class="stats-card bg-success bg-opacity-10">
            <div class="number text-success">{{ $stats['completed'] }}</div>
            <div class="label">Completed</div>
        </div>
    </div>
</div>

<!-- Assessments List -->
@if($assessments->isNotEmpty())
    @foreach($assessments as $assessment)
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <h6 class="mb-0 fw-semibold">{{ $assessment->title }}</h6>
                        @if($assessment->status === 'completed')
                            <span class="badge bg-success small">Completed</span>
                        @elseif($assessment->status === 'in_progress')
                            <span class="badge bg-primary small">In Progress</span>
                        @else
                            <span class="badge bg-warning small">Pending</span>
                        @endif
                    </div>
                    
                    @if($assessment->description)
                        <p class="text-muted small mb-2">{{ $assessment->description }}</p>
                    @endif
                    
                    <div class="d-flex gap-3 small text-muted">
                        <span>
                            <i class="bi bi-person-badge me-1"></i>
                            Assigned by: {{ $assessment->assignedByDoctor->name }}
                        </span>
                        <span>
                            <i class="bi bi-calendar me-1"></i>
                            {{ $assessment->assigned_at->format('M d, Y') }}
                        </span>
                        @if($assessment->status === 'completed' && $assessment->completed_at)
                            <span class="text-success">
                                <i class="bi bi-check-circle me-1"></i>
                                Completed: {{ $assessment->completed_at->format('M d, Y') }}
                            </span>
                        @endif
                    </div>
                    
                    @if($assessment->questions->count() > 0)
                        <p class="small text-muted mt-2 mb-0">
                            <i class="bi bi-list-ul me-1"></i>
                            {{ $assessment->questions->count() }} questions
                        </p>
                    @endif
                </div>
                
                <div class="flex-shrink-0 ms-3">
                    @if($assessment->status === 'completed')
                        <a href="{{ route('client.general-assessment.result', $assessment->id) }}" 
                           class="btn btn-sm btn-outline-success">
                            <i class="bi bi-eye me-1"></i>View Results
                        </a>
                    @else
                        <a href="{{ route('client.general-assessment.show', $assessment->id) }}" 
                           class="btn btn-sm btn-primary">
                            @if($assessment->status === 'in_progress')
                                <i class="bi bi-arrow-repeat me-1"></i>Continue
                            @else
                                <i class="bi bi-play-fill me-1"></i>Start
                            @endif
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-clipboard-data text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-3 mb-0">No assessments assigned yet</p>
            <p class="text-muted small">Your therapist will assign assessments when needed.</p>
        </div>
    </div>
@endif
@endsection
