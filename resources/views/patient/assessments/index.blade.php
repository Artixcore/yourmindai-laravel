@extends('layouts.app')

@section('title', 'My Assessments - Your Mind Aid')

@section('content')
<div class="container-fluid" style="max-width: 1200px;">
    <div class="mb-4">
        <h1 class="h3 fw-bold text-stone-900 mb-2">Assessments</h1>
        <p class="text-stone-600 mb-0">View all your assessments</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($assessments->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body p-5 text-center">
                <i class="bi bi-clipboard-check fs-1 text-stone-300 mb-3"></i>
                <p class="text-stone-500 mb-0">No assessments yet.</p>
                <p class="small text-stone-400 mt-2 mb-0">Your doctor will create assessments for you.</p>
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach($assessments as $assessment)
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-start justify-content-between">
                                <div class="flex-grow-1">
                                    <h5 class="fw-semibold text-stone-900 mb-2">
                                        <a href="{{ route('patient.assessments.show', $assessment->id) }}" class="text-decoration-none text-stone-900">
                                            {{ $assessment->assessment_type ?? 'Assessment #' . $assessment->id }}
                                        </a>
                                    </h5>
                                    @if($assessment->status)
                                        <x-badge :variant="$assessment->status === 'completed' ? 'success' : 'warning'">
                                            {{ ucfirst($assessment->status) }}
                                        </x-badge>
                                    @endif
                                    <div class="d-flex align-items-center gap-3 small text-stone-500 mt-2">
                                        <span><i class="bi bi-calendar me-1"></i>{{ $assessment->created_at->format('M d, Y') }}</span>
                                        @if($assessment->completed_at)
                                            <span><i class="bi bi-check-circle me-1"></i>Completed {{ $assessment->completed_at->format('M d, Y') }}</span>
                                        @elseif($assessment->assigned_at)
                                            <span><i class="bi bi-clock me-1"></i>Assigned {{ $assessment->assigned_at->format('M d, Y') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('patient.assessments.show', $assessment->id) }}" class="btn btn-primary btn-sm">
                                        View
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
