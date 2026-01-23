@extends('layouts.app')

@section('title', 'Assessment Details')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('admin.dashboard')],
            ['label' => 'Patients', 'url' => route('admin.patients.index')],
            ['label' => $patient->name, 'url' => route('admin.patients.show', $patient)],
            ['label' => 'Assessments', 'url' => route('admin.patients.psychometric.index', $patient)],
            ['label' => 'Details']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">Assessment Details</h1>
        <p class="text-muted mb-0">{{ $assessment->scale->name ?? 'Assessment' }} - {{ $patient->name }}</p>
    </div>
</div>

<!-- Assessment Info -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0">
        <h5 class="mb-0 fw-semibold">Assessment Information</h5>
    </div>
    <div class="card-body">
        <div class="row g-3">
            <div class="col-12 col-md-6">
                <label class="form-label small text-muted">Scale Name</label>
                <p class="fw-medium mb-0">{{ $assessment->scale->name ?? 'Unknown Scale' }}</p>
            </div>
            
            <div class="col-12 col-md-6">
                <label class="form-label small text-muted">Status</label>
                <div>
                    @if($assessment->status === 'completed')
                    <span class="badge bg-success">Completed</span>
                    @elseif($assessment->status === 'in_progress')
                    <span class="badge bg-warning">In Progress</span>
                    @else
                    <span class="badge bg-secondary">Pending</span>
                    @endif
                </div>
            </div>
            
            @if($assessment->scale && $assessment->scale->category)
            <div class="col-12 col-md-6">
                <label class="form-label small text-muted">Category</label>
                <p class="fw-medium mb-0">{{ $assessment->scale->category }}</p>
            </div>
            @endif
            
            @if($assessment->assignedByDoctor)
            <div class="col-12 col-md-6">
                <label class="form-label small text-muted">Assigned By</label>
                <p class="fw-medium mb-0">{{ $assessment->assignedByDoctor->name ?? $assessment->assignedByDoctor->email ?? 'Doctor' }}</p>
            </div>
            @endif
            
            <div class="col-12 col-md-6">
                <label class="form-label small text-muted">Assigned Date</label>
                <p class="fw-medium mb-0">{{ $assessment->assigned_at ? $assessment->assigned_at->format('M d, Y H:i') : 'Not set' }}</p>
            </div>
            
            @if($assessment->completed_at)
            <div class="col-12 col-md-6">
                <label class="form-label small text-muted">Completed Date</label>
                <p class="fw-medium mb-0">{{ $assessment->completed_at->format('M d, Y H:i') }}</p>
            </div>
            @endif
            
            @if($assessment->total_score !== null)
            <div class="col-12 col-md-6">
                <label class="form-label small text-muted">Total Score</label>
                <p class="fw-medium mb-0"><strong>{{ $assessment->total_score }}</strong></p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Results -->
@if($assessment->status === 'completed')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0">
        <h5 class="mb-0 fw-semibold">Assessment Results</h5>
    </div>
    <div class="card-body">
        @if($assessment->total_score !== null)
        <div class="mb-4">
            <h6 class="fw-semibold mb-2">Total Score</h6>
            <div class="display-4 fw-bold text-primary">{{ $assessment->total_score }}</div>
        </div>
        @endif
        
        @if($assessment->interpretation)
        <div class="mb-4">
            <h6 class="fw-semibold mb-2">Interpretation</h6>
            <div class="p-3 bg-light rounded">
                <p class="mb-0">{{ $assessment->interpretation }}</p>
            </div>
        </div>
        @endif
        
        @if($assessment->sub_scores && is_array($assessment->sub_scores) && count($assessment->sub_scores) > 0)
        <div class="mb-4">
            <h6 class="fw-semibold mb-2">Sub-Scores</h6>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th class="text-end">Score</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assessment->sub_scores as $category => $score)
                        <tr>
                            <td>{{ $category }}</td>
                            <td class="text-end fw-bold">{{ $score }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
        
        @if($assessment->responses && is_array($assessment->responses) && count($assessment->responses) > 0)
        <div>
            <h6 class="fw-semibold mb-2">Responses</h6>
            <div class="table-responsive">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Question</th>
                            <th class="text-end">Response</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assessment->responses as $questionId => $response)
                        <tr>
                            <td>Question {{ $questionId }}</td>
                            <td class="text-end">{{ is_array($response) ? json_encode($response) : $response }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>
@else
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body text-center py-5">
        <i class="bi bi-clock-history display-4 text-muted mb-3"></i>
        <h5 class="mb-2">Assessment Not Completed</h5>
        <p class="text-muted mb-0">This assessment is {{ $assessment->status === 'pending' ? 'pending' : 'in progress' }}. Results will appear here once the patient completes it.</p>
    </div>
</div>
@endif

<div class="mt-4">
    <a href="{{ route('admin.patients.psychometric.index', $patient) }}" class="btn btn-outline-secondary me-2">
        <i class="bi bi-arrow-left me-2"></i>Back to Assessments
    </a>
    <a href="{{ route('admin.patients.show', $patient) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Patient
    </a>
</div>
@endsection
