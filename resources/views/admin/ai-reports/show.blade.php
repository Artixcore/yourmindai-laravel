@extends('layouts.app')

@section('title', 'AI Report Details')

@section('content')
<div class="container-fluid" style="max-width: 1024px;">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <x-breadcrumb :items="[
                ['label' => 'Home', 'url' => route('admin.dashboard')],
                ['label' => 'AI Reports', 'url' => route('admin.ai-reports.index')],
                ['label' => 'Report #' . $aiReport->id]
            ]" />
            <h1 class="h3 mb-1 fw-semibold">AI Report Details</h1>
            <p class="text-muted mb-0">Report #{{ $aiReport->id }}</p>
        </div>
        <div class="d-flex gap-2">
            @if($aiReport->status !== 'running' && $aiReport->status !== 'queued')
                <form action="{{ route('admin.ai-reports.regenerate', $aiReport) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-clockwise me-2"></i>Regenerate
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.ai-reports.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Reports
            </a>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div 
            x-data="{ show: true }"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="alert alert-success alert-dismissible fade show mb-4"
            x-init="setTimeout(() => show = false, 5000)"
        >
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Report Metadata -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title mb-0 fw-semibold">Report Information</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-muted">Scope</label>
                            <div>
                                <x-badge :variant="$aiReport->scope === 'patient' ? 'success' : ($aiReport->scope === 'doctor' ? 'primary' : ($aiReport->scope === 'clinic' ? 'info' : 'default'))">
                                    {{ ucfirst($aiReport->scope) }}
                                </x-badge>
                            </div>
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-muted">Status</label>
                            <div>
                                <x-badge :variant="$aiReport->status === 'completed' ? 'success' : ($aiReport->status === 'failed' ? 'danger' : ($aiReport->status === 'running' ? 'warning' : 'default'))">
                                    {{ ucfirst($aiReport->status) }}
                                </x-badge>
                            </div>
                        </div>
                        
                        @if($aiReport->patient)
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-muted">Patient</label>
                            <p class="fw-medium mb-0">
                                <a href="{{ route('admin.patients.show', $aiReport->patient) }}" class="text-decoration-none">
                                    {{ $aiReport->patient->name }}
                                </a>
                            </p>
                        </div>
                        @endif
                        
                        @if($aiReport->doctor)
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-muted">Doctor</label>
                            <p class="fw-medium mb-0">{{ $aiReport->doctor->name ?? $aiReport->doctor->email }}</p>
                        </div>
                        @endif
                        
                        @if($aiReport->date_from && $aiReport->date_to)
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-muted">Date Range</label>
                            <p class="fw-medium mb-0">
                                {{ $aiReport->date_from->format('M d, Y') }} - {{ $aiReport->date_to->format('M d, Y') }}
                            </p>
                        </div>
                        @endif
                        
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-muted">Model</label>
                            <p class="fw-medium mb-0">{{ $aiReport->model ?? 'N/A' }}</p>
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-muted">Requested By</label>
                            <p class="fw-medium mb-0">
                                {{ $aiReport->requestedBy ? ($aiReport->requestedBy->name ?? $aiReport->requestedBy->email) : 'N/A' }}
                            </p>
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-muted">Created</label>
                            <p class="fw-medium mb-0">{{ $aiReport->created_at->format('M d, Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Content -->
    @if($aiReport->status === 'completed' && $aiReport->result_summary)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-bottom py-3">
            <h5 class="card-title mb-0 fw-semibold">Report Summary</h5>
        </div>
        <div class="card-body p-4">
            <div style="white-space: pre-wrap;">{{ $aiReport->result_summary }}</div>
        </div>
    </div>
    @elseif($aiReport->status === 'failed' && $aiReport->error_message)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-bottom py-3">
            <h5 class="card-title mb-0 fw-semibold text-danger">Error</h5>
        </div>
        <div class="card-body p-4">
            <div class="text-danger">{{ $aiReport->error_message }}</div>
        </div>
    </div>
    @elseif($aiReport->status === 'running' || $aiReport->status === 'queued')
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body text-center py-5">
            <div class="spinner-border text-primary mb-3" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <h5 class="mb-2">Report is {{ $aiReport->status === 'running' ? 'being generated' : 'queued' }}</h5>
            <p class="text-muted mb-0">Please check back in a few moments.</p>
        </div>
    </div>
    @else
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body text-center py-5">
            <i class="bi bi-file-earmark-text text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 mb-2">No report content available</h5>
            <p class="text-muted mb-0">This report does not have any content yet.</p>
        </div>
    </div>
    @endif
</div>
@endsection
