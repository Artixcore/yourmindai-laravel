@extends('layouts.app')

@section('title', 'Homework Details - Your Mind Aid')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Patients</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('patients.show', $patient) }}">{{ $patientProfile->user->name ?? $patientProfile->full_name ?? $patient->name ?? 'Patient' }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('patients.homework.index', $patient) }}">Homework</a></li>
                    <li class="breadcrumb-item active">{{ Str::limit($homework->title, 30) }}</li>
                </ol>
            </nav>

            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">{{ $homework->title }}</h2>
                    <p class="text-muted mb-0">
                        {{ ucfirst(str_replace('_', ' ', $homework->homework_type)) }}
                        @if($homework->session)
                            | Session #{{ $homework->session->id }}
                        @endif
                    </p>
                </div>
                <a href="{{ route('patients.homework.index', $patient) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Back to Homework
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Assignment Details -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Assignment Details</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <small class="text-muted">Status</small>
                            <div>
                                @if($homework->status === 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($homework->status === 'in_progress')
                                    <span class="badge bg-primary">In Progress</span>
                                @else
                                    <span class="badge bg-warning">{{ ucfirst($homework->status) }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Frequency</small>
                            <div class="fw-semibold">{{ ucfirst($homework->frequency) }}</div>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted">Date Range</small>
                            <div>
                                {{ \Carbon\Carbon::parse($homework->start_date)->format('M d, Y') }}
                                @if($homework->end_date)
                                    - {{ \Carbon\Carbon::parse($homework->end_date)->format('M d, Y') }}
                                @endif
                            </div>
                        </div>
                    </div>
                    @if($homework->description)
                        <div class="mb-2">
                            <small class="text-muted">Description</small>
                            <p class="mb-0">{{ $homework->description }}</p>
                        </div>
                    @endif
                    @if($homework->instructions)
                        <div>
                            <small class="text-muted">Instructions</small>
                            <div>{{ $homework->instructions }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Completions with Review -->
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Completion History</h5>
                </div>
                <div class="card-body">
                    @forelse($homework->completions()->orderBy('completion_date', 'desc')->get() as $completion)
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <strong>{{ $completion->completion_date->format('M d, Y') }}</strong>
                                    @if($completion->completion_time)
                                        <span class="text-muted">at {{ \Carbon\Carbon::parse($completion->completion_time)->format('g:i A') }}</span>
                                    @endif
                                    <div class="mt-1">
                                        @if($completion->is_completed)
                                            <span class="badge bg-success">Completed {{ $completion->completion_percentage }}%</span>
                                        @else
                                            <span class="badge bg-warning">In Progress {{ $completion->completion_percentage }}%</span>
                                        @endif
                                        @if($completion->scoring_choice)
                                            <span class="badge bg-info">
                                                {{ ucfirst(str_replace('_', ' ', $completion->scoring_choice)) }}
                                                ({{ $completion->score_value > 0 ? '+' : '' }}{{ $completion->score_value }})
                                            </span>
                                        @endif
                                        @if($completion->reviewed_at)
                                            <span class="badge bg-secondary">
                                                <i class="bi bi-check-circle me-1"></i>Reviewed
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                @if(!$completion->reviewed_at)
                                    <form action="{{ route('patients.homework.completions.review', [$patient, $homework, $completion]) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="btn btn-sm btn-primary">Mark Reviewed</button>
                                    </form>
                                @endif
                            </div>
                            @if($completion->patient_notes)
                                <p class="mt-2 mb-0 small">{{ $completion->patient_notes }}</p>
                            @endif
                            @if($completion->reviewed_at && $completion->reviewer)
                                <small class="text-muted">Reviewed by {{ $completion->reviewer->name }} on {{ $completion->reviewed_at->format('M d, Y') }}</small>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted mb-0">No completions yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Actions</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('patients.homework.index', $patient) }}" class="btn btn-outline-primary w-100 mb-2">
                        <i class="bi bi-arrow-left me-2"></i>Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
