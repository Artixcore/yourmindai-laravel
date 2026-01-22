@extends('layouts.app')

@section('title', 'Assessment Details - Your Mind Aid')

@section('content')
<div class="container-fluid" style="max-width: 1200px;">
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h1 class="h3 fw-bold text-stone-900 mb-2">{{ $assessment->assessment_type ?? 'Assessment #' . $assessment->id }}</h1>
            <p class="text-stone-600 mb-0">Assessment Details</p>
        </div>
        <a href="{{ route('patient.assessments.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Assessments
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Assessment Information -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-bottom py-3">
            <h5 class="card-title mb-0 fw-semibold">Assessment Information</h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label small text-muted">Assessment Type</label>
                    <p class="fw-medium mb-0">{{ $assessment->assessment_type ?? 'N/A' }}</p>
                </div>
                
                <div class="col-12 col-md-6">
                    <label class="form-label small text-muted">Status</label>
                    <div>
                        <x-badge :variant="$assessment->status === 'completed' ? 'success' : ($assessment->status === 'pending' ? 'warning' : 'default')">
                            {{ ucfirst($assessment->status ?? 'pending') }}
                        </x-badge>
                    </div>
                </div>
                
                @if($assessment->assigned_at)
                <div class="col-12 col-md-6">
                    <label class="form-label small text-muted">Assigned</label>
                    <p class="fw-medium mb-0">{{ $assessment->assigned_at->format('M d, Y') }}</p>
                </div>
                @endif
                
                @if($assessment->completed_at)
                <div class="col-12 col-md-6">
                    <label class="form-label small text-muted">Completed</label>
                    <p class="fw-medium mb-0">{{ $assessment->completed_at->format('M d, Y') }}</p>
                </div>
                @endif
                
                <div class="col-12 col-md-6">
                    <label class="form-label small text-muted">Created</label>
                    <p class="fw-medium mb-0">{{ $assessment->created_at->format('M d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Assessment Results -->
    @php
        $results = $assessment->results ?? collect();
    @endphp
    @if($results->count() > 0)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-bottom py-3">
            <h5 class="card-title mb-0 fw-semibold">Results</h5>
        </div>
        <div class="card-body p-4">
            <div class="d-flex flex-column gap-3">
                @foreach($results as $result)
                    <div class="border rounded p-3">
                        @if($result->score !== null)
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h6 class="fw-semibold mb-0">Score</h6>
                                <span class="badge bg-primary">{{ $result->score }}</span>
                            </div>
                        @endif
                        @if($result->interpretation)
                            <div class="mb-2">
                                <label class="form-label small text-muted">Interpretation</label>
                                <p class="mb-0" style="white-space: pre-wrap;">{{ $result->interpretation }}</p>
                            </div>
                        @endif
                        @if($result->substance_type)
                            <div class="mb-2">
                                <label class="form-label small text-muted">Substance Type</label>
                                <p class="mb-0">{{ $result->substance_type }}</p>
                            </div>
                        @endif
                        @if($result->sub_scores && is_array($result->sub_scores))
                            <div class="mb-2">
                                <label class="form-label small text-muted">Sub Scores</label>
                                <pre class="bg-light p-2 rounded small mb-0">{{ json_encode($result->sub_scores, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        @endif
                        @if($result->responses && is_array($result->responses))
                            <div class="mb-2">
                                <label class="form-label small text-muted">Responses</label>
                                <pre class="bg-light p-2 rounded small mb-0">{{ json_encode($result->responses, JSON_PRETTY_PRINT) }}</pre>
                            </div>
                        @endif
                        @if($result->completed_at)
                            <div class="text-muted small">
                                Completed: {{ $result->completed_at->format('M d, Y H:i') }}
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @else
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body text-center py-5">
            <i class="bi bi-clipboard-check text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 mb-2">No results available</h5>
            <p class="text-muted mb-0">This assessment does not have any results yet.</p>
        </div>
    </div>
    @endif
</div>
@endsection
