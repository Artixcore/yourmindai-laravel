@extends('layouts.app')

@section('title', 'Psychometric Assessments')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('admin.dashboard')],
            ['label' => 'Patients', 'url' => route('admin.patients.index')],
            ['label' => $patient->name, 'url' => route('admin.patients.show', $patient)],
            ['label' => 'Psychometric Assessments']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">Psychometric Assessments</h1>
        <p class="text-muted mb-0">Manage assessments for {{ $patient->name }}</p>
    </div>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Assign New Assessment -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0">
        <h5 class="mb-0 fw-semibold">Assign New Assessment</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.patients.psychometric.assign', $patient) }}">
            @csrf
            <div class="row g-3">
                <div class="col-12 col-md-8">
                    <label class="form-label">Select Scale <span class="text-danger">*</span></label>
                    <select class="form-select" name="scale_id" required>
                        <option value="">Choose a scale...</option>
                        @foreach($availableScales as $scale)
                        <option value="{{ $scale->id }}">{{ $scale->name }}@if($scale->category) - {{ $scale->category }}@endif</option>
                        @endforeach
                    </select>
                    @error('scale_id')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                <div class="col-12 col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-plus-circle me-2"></i>Assign Assessment
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Assessments List -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0">
        <h5 class="mb-0 fw-semibold">Assigned Assessments</h5>
    </div>
    <div class="card-body p-0">
        @if($assessments->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-clipboard-check text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 mb-2">No assessments assigned</h5>
            <p class="text-muted mb-0">Assign a psychometric assessment to this patient to get started.</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 ps-4">Scale Name</th>
                        <th class="border-0">Category</th>
                        <th class="border-0">Status</th>
                        <th class="border-0">Score</th>
                        <th class="border-0">Assigned By</th>
                        <th class="border-0">Assigned Date</th>
                        <th class="border-0">Completed Date</th>
                        <th class="border-0 text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($assessments as $assessment)
                    <tr>
                        <td class="ps-4">
                            <strong>{{ $assessment->scale->name ?? 'Unknown Scale' }}</strong>
                        </td>
                        <td>
                            @if($assessment->scale && $assessment->scale->category)
                            <span class="badge bg-secondary">{{ $assessment->scale->category }}</span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($assessment->status === 'completed')
                            <span class="badge bg-success">Completed</span>
                            @elseif($assessment->status === 'in_progress')
                            <span class="badge bg-warning">In Progress</span>
                            @else
                            <span class="badge bg-secondary">Pending</span>
                            @endif
                        </td>
                        <td>
                            @if($assessment->total_score !== null)
                            <strong>{{ $assessment->total_score }}</strong>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($assessment->assignedByDoctor)
                            {{ $assessment->assignedByDoctor->name ?? $assessment->assignedByDoctor->email ?? 'Doctor' }}
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            {{ $assessment->assigned_at ? $assessment->assigned_at->format('M d, Y') : '-' }}
                        </td>
                        <td>
                            @if($assessment->completed_at)
                            {{ $assessment->completed_at->format('M d, Y') }}
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.patients.psychometric.show', ['patient' => $patient, 'assessment' => $assessment]) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

<div class="mt-4">
    <a href="{{ route('admin.patients.show', $patient) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Patient
    </a>
</div>
@endsection
