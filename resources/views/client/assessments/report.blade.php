@extends('client.layout')

@section('title', 'Assessment Report - Your Mind Aid')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">{{ $assessment->scale->name ?? 'Assessment' }} Report</h4>
    <p class="text-muted mb-0 small">Completed on {{ $assessment->completed_at?->format('M d, Y') }}</p>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('info'))
    <div class="alert alert-info alert-dismissible fade show">
        {{ session('info') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($report)
<div class="card mb-3">
    <div class="card-header bg-white border-0">
        <h6 class="mb-0 fw-semibold">Report Summary</h6>
        @if($report->generated_at)
            <small class="text-muted">Generated {{ $report->generated_at->format('M d, Y g:i A') }}</small>
        @endif
    </div>
    <div class="card-body">
        <pre class="mb-0 bg-light p-3 rounded" style="white-space: pre-wrap; font-family: inherit;">{{ $report->summary }}</pre>
    </div>
</div>
@else
<div class="card mb-3">
    <div class="card-body text-center py-5">
        <i class="bi bi-file-earmark-text text-muted" style="font-size: 3rem;"></i>
        <p class="text-muted mt-3 mb-3">No report generated yet.</p>
        <form action="{{ route('client.assessments.report.generate', $assessment) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-file-earmark-plus me-2"></i>Generate Report
            </button>
        </form>
    </div>
</div>
@endif

<div class="d-grid gap-2">
    @if($report)
    <form action="{{ route('client.assessments.report.generate', $assessment) }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-outline-primary w-100">
            <i class="bi bi-arrow-clockwise me-2"></i>Regenerate Report
        </button>
    </form>
    @endif
    <a href="{{ route('client.assessments.show', $assessment) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Results
    </a>
    <a href="{{ route('client.assessments.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-list me-2"></i>All Assessments
    </a>
</div>
@endsection
