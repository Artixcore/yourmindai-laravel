@extends('layouts.app')

@section('title', 'General Assessments - ' . ($patientProfile->user->name ?? $patientProfile->full_name ?? $patient->name ?? 'Patient'))

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Patients</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('patients.show', $patient) }}">{{ $patientProfile->user->name ?? $patientProfile->full_name ?? $patient->name ?? 'Patient' }}</a></li>
                    <li class="breadcrumb-item active">General Assessments</li>
                </ol>
            </nav>
            
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">General Assessments</h2>
                    <p class="text-muted mb-0">Manage custom assessments for {{ $patientProfile->user->name ?? $patientProfile->full_name ?? $patient->name ?? 'Patient' }}</p>
                </div>
                <a href="{{ route('patients.general-assessments.create', $patient) }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-2"></i>Create Assessment
                </a>
            </div>
        </div>
    </div>

    @if($assessments->isNotEmpty())
        @foreach($assessments as $assessment)
        <div class="card shadow-sm mb-3">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <h5 class="mb-0">{{ $assessment->title }}</h5>
                            @if($assessment->status === 'completed')
                                <span class="badge bg-success">Completed</span>
                            @elseif($assessment->status === 'in_progress')
                                <span class="badge bg-primary">In Progress</span>
                            @else
                                <span class="badge bg-warning">Pending</span>
                            @endif
                        </div>
                        
                        @if($assessment->description)
                            <p class="text-muted mb-2">{{ $assessment->description }}</p>
                        @endif
                        
                        <div class="d-flex gap-4 small text-muted">
                            <span>
                                <i class="bi bi-calendar me-1"></i>
                                Assigned: {{ $assessment->assigned_at->format('M d, Y') }}
                            </span>
                            <span>
                                <i class="bi bi-person-badge me-1"></i>
                                By: {{ $assessment->assignedByDoctor->name }}
                            </span>
                            <span>
                                <i class="bi bi-list-ul me-1"></i>
                                {{ $assessment->questions->count() }} questions
                            </span>
                            <span>
                                <i class="bi bi-chat-left me-1"></i>
                                {{ $assessment->responses->count() }} responses
                            </span>
                        </div>

                        @if($assessment->status === 'completed' && $assessment->completed_at)
                            <div class="small text-success mt-2">
                                <i class="bi bi-check-circle me-1"></i>
                                Completed on {{ $assessment->completed_at->format('M d, Y \a\t g:i A') }}
                            </div>
                        @endif

                        <!-- Feedback Summary -->
                        @php
                            $feedbackCount = $assessment->feedback()->count();
                        @endphp
                        @if($feedbackCount > 0)
                            <div class="mt-2">
                                <span class="badge bg-info">
                                    <i class="bi bi-chat-left me-1"></i>{{ $feedbackCount }} feedback
                                </span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="flex-shrink-0 ms-3">
                        <a href="{{ route('patients.general-assessments.show', [$patient, $assessment->id]) }}" 
                           class="btn btn-outline-primary">
                            <i class="bi bi-eye me-1"></i>View Details
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    @else
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-clipboard-data text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-3 mb-2">No general assessments created yet</p>
                <p class="text-muted small mb-4">Create custom assessments to evaluate this patient's progress and needs.</p>
                <a href="{{ route('patients.general-assessments.create', $patient) }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-2"></i>Create First Assessment
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
