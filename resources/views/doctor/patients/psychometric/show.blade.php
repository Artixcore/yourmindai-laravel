@extends('layouts.app')

@section('title', 'Assessment Results')

@section('content')
<div class="mb-4">
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('dashboard')],
            ['label' => 'Patients', 'url' => route('patients.index')],
            ['label' => $patient->name, 'url' => route('patients.show', $patient)],
            ['label' => 'Assessments', 'url' => route('patients.psychometric.index', $patient)],
            ['label' => $assessment->scale->name ?? 'Assessment']
        ]" />
    <h1 class="h3 mb-1 fw-semibold">{{ $assessment->scale->name ?? 'Assessment' }}</h1>
    <p class="text-muted mb-0">Assessment results and interpretation</p>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-8">
        <!-- Assessment Details -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0 fw-semibold">Assessment Details</h5>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <p class="small text-muted mb-1">Status</p>
                        <p class="mb-0">
                            @if($assessment->status === 'completed')
                            <span class="badge bg-success">Completed</span>
                            @elseif($assessment->status === 'in_progress')
                            <span class="badge bg-warning">In Progress</span>
                            @else
                            <span class="badge bg-secondary">Pending</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-6">
                        <p class="small text-muted mb-1">Assigned Date</p>
                        <p class="fw-semibold mb-0">{{ $assessment->assigned_at ? $assessment->assigned_at->format('M d, Y') : 'N/A' }}</p>
                    </div>
                    @if($assessment->completed_at)
                    <div class="col-6">
                        <p class="small text-muted mb-1">Completed Date</p>
                        <p class="fw-semibold mb-0">{{ $assessment->completed_at->format('M d, Y') }}</p>
                    </div>
                    @endif
                    <div class="col-6">
                        <p class="small text-muted mb-1">Assigned By</p>
                        <p class="fw-semibold mb-0">{{ $assessment->assignedByDoctor->name ?? 'Unknown' }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        @if($assessment->status === 'completed')
        <!-- Results -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0 fw-semibold">Results</h5>
            </div>
            <div class="card-body">
                @if($assessment->total_score !== null)
                <div class="text-center mb-4">
                    <div class="display-4 fw-bold text-primary mb-2">{{ $assessment->total_score }}</div>
                    <div class="text-muted">Total Score</div>
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
        
        <!-- Responses -->
        @if($assessment->responses && $assessment->scale)
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0 fw-semibold">Patient Responses</h5>
            </div>
            <div class="card-body">
                @foreach($assessment->scale->questions as $index => $question)
                    @php
                        $questionId = $question['id'] ?? $index;
                        $response = $assessment->responses[$questionId] ?? null;
                    @endphp
                    @if($response !== null)
                    <div class="mb-3 pb-3 border-bottom">
                        <h6 class="fw-semibold mb-2">{{ $index + 1 }}. {{ $question['text'] ?? 'Question' }}</h6>
                        <p class="mb-0">
                            <strong>Response:</strong> {{ $response }}
                        </p>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
        @endif
        @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-hourglass-split text-muted" style="font-size: 3rem;"></i>
                <h5 class="mt-3 mb-2">Assessment Not Completed</h5>
                <p class="text-muted mb-0">The patient has not completed this assessment yet.</p>
            </div>
        </div>
        @endif
    </div>
    
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0 fw-semibold">Patient Information</h5>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <strong>Name:</strong><br>
                    {{ $patient->name }}
                </p>
                <p class="mb-0">
                    <strong>Email:</strong><br>
                    {{ $patient->email }}
                </p>
                @if($patientProfile)
                <p class="mb-0 mt-2">
                    <strong>Patient Number:</strong><br>
                    {{ $patientProfile->patient_number ?? 'N/A' }}
                </p>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('patients.psychometric.index', $patient) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Assessments
    </a>
</div>
@endsection
