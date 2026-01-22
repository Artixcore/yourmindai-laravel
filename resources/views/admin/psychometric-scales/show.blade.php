@extends('layouts.app')

@section('title', 'Psychometric Scale Details')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('admin.dashboard')],
            ['label' => 'Psychometric Scales', 'url' => route('psychometric-scales.index')],
            ['label' => $psychometricScale->name]
        ]" />
        <h1 class="h3 mb-1 fw-semibold">{{ $psychometricScale->name }}</h1>
        <p class="text-muted mb-0">{{ $psychometricScale->description ?? 'No description' }}</p>
    </div>
    <div class="btn-group">
        <a href="{{ route('psychometric-scales.edit', $psychometricScale) }}" class="btn btn-primary">
            <i class="bi bi-pencil me-2"></i>Edit
        </a>
        <form method="POST" action="{{ route('psychometric-scales.destroy', $psychometricScale) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this scale?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger">
                <i class="bi bi-trash me-2"></i>Delete
            </button>
        </form>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-8">
        <!-- Scale Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0 fw-semibold">Scale Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <p class="small text-muted mb-1">Category</p>
                        <p class="fw-semibold mb-0">{{ $psychometricScale->category ?? 'N/A' }}</p>
                    </div>
                    <div class="col-6">
                        <p class="small text-muted mb-1">Status</p>
                        <p class="mb-0">
                            @if($psychometricScale->is_active)
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-6">
                        <p class="small text-muted mb-1">Created By</p>
                        <p class="fw-semibold mb-0">{{ $psychometricScale->createdByDoctor->name ?? 'System' }}</p>
                    </div>
                    <div class="col-6">
                        <p class="small text-muted mb-1">Created Date</p>
                        <p class="fw-semibold mb-0">{{ $psychometricScale->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Questions -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0 fw-semibold">Questions ({{ count($psychometricScale->questions ?? []) }})</h5>
            </div>
            <div class="card-body">
                @if($psychometricScale->questions && count($psychometricScale->questions) > 0)
                    @foreach($psychometricScale->questions as $index => $question)
                    <div class="mb-4 pb-4 border-bottom">
                        <h6 class="fw-semibold mb-2">{{ $index + 1 }}. {{ $question['text'] ?? 'Question' }}</h6>
                        <div class="small text-muted">
                            <span class="badge bg-secondary">{{ ucfirst($question['type'] ?? 'unknown') }}</span>
                            @if(isset($question['min']) && isset($question['max']))
                            <span class="ms-2">Range: {{ $question['min'] }} - {{ $question['max'] }}</span>
                            @endif
                        </div>
                    </div>
                    @endforeach
                @else
                <p class="text-muted mb-0">No questions defined.</p>
                @endif
            </div>
        </div>
        
        <!-- Interpretation Rules -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0 fw-semibold">Interpretation Rules</h5>
            </div>
            <div class="card-body">
                @if($psychometricScale->interpretation_rules && count($psychometricScale->interpretation_rules) > 0)
                    @foreach($psychometricScale->interpretation_rules as $rule)
                    <div class="mb-3 p-3 border rounded">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <strong>Score Range: {{ $rule['min'] ?? 0 }} - {{ $rule['max'] ?? 100 }}</strong>
                                <p class="mb-0 mt-1">{{ $rule['interpretation'] ?? 'No interpretation' }}</p>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                <p class="text-muted mb-0">No interpretation rules defined.</p>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-12 col-lg-4">
        <!-- Statistics -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0 fw-semibold">Statistics</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <p class="small text-muted mb-1">Total Assessments</p>
                    <h4 class="mb-0">{{ $psychometricScale->assessments->count() }}</h4>
                </div>
                <div class="mb-3">
                    <p class="small text-muted mb-1">Completed</p>
                    <h4 class="mb-0">{{ $psychometricScale->assessments->where('status', 'completed')->count() }}</h4>
                </div>
                <div>
                    <p class="small text-muted mb-1">Pending</p>
                    <h4 class="mb-0">{{ $psychometricScale->assessments->where('status', 'pending')->count() }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
