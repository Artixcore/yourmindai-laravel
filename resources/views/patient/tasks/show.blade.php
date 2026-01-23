@extends('layouts.app')

@section('title', 'Task Details')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('patient.dashboard')],
            ['label' => 'Tasks', 'url' => route('patient.tasks.index')],
            ['label' => 'Details']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">Task Details</h1>
        <p class="text-muted mb-0">{{ $task->title }}</p>
    </div>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Task Details -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0">
        <div class="d-flex align-items-center justify-content-between">
            <h5 class="mb-0 fw-semibold">Task Information</h5>
            <div>
                @if($task->status === 'completed')
                <span class="badge bg-success">Completed</span>
                @else
                <span class="badge bg-warning">Pending</span>
                @endif
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row g-3 mb-3">
            <div class="col-12">
                <label class="form-label small text-muted">Title</label>
                <h4 class="fw-semibold mb-0">{{ $task->title }}</h4>
            </div>
            
            @if($task->description)
            <div class="col-12">
                <label class="form-label small text-muted">Description</label>
                <div class="p-3 bg-light rounded">
                    <p class="mb-0">{{ $task->description }}</p>
                </div>
            </div>
            @endif
            
            <div class="col-12 col-md-6">
                <label class="form-label small text-muted">Status</label>
                <div>
                    @if($task->status === 'completed')
                    <span class="badge bg-success">Completed</span>
                    @else
                    <span class="badge bg-warning">Pending</span>
                    @endif
                </div>
            </div>
            
            @if($task->points > 0)
            <div class="col-12 col-md-6">
                <label class="form-label small text-muted">Points</label>
                <div>
                    <span class="badge bg-info">
                        <i class="bi bi-star-fill me-1"></i>{{ $task->points }} points
                    </span>
                </div>
            </div>
            @endif
            
            @if($task->due_date)
            <div class="col-12 col-md-6">
                <label class="form-label small text-muted">Due Date</label>
                <p class="fw-medium mb-0">
                    {{ $task->due_date->format('M d, Y') }}
                    @if($task->due_date->isPast() && $task->status !== 'completed')
                    <span class="text-danger">(Overdue)</span>
                    @endif
                </p>
            </div>
            @endif
            
            @if($task->assignedByDoctor)
            <div class="col-12 col-md-6">
                <label class="form-label small text-muted">Assigned By</label>
                <p class="fw-medium mb-0">{{ $task->assignedByDoctor->name ?? $task->assignedByDoctor->email ?? 'Doctor' }}</p>
            </div>
            @endif
            
            @if($task->completed_at)
            <div class="col-12 col-md-6">
                <label class="form-label small text-muted">Completed Date</label>
                <p class="fw-medium mb-0">{{ $task->completed_at->format('M d, Y H:i') }}</p>
            </div>
            @endif
            
            <div class="col-12 col-md-6">
                <label class="form-label small text-muted">Created</label>
                <p class="fw-medium mb-0">{{ $task->created_at->format('M d, Y') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Actions -->
@if($task->status !== 'completed')
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h6 class="fw-semibold mb-3">Complete Task</h6>
        <p class="text-muted mb-3">Mark this task as complete to earn {{ $task->points > 0 ? $task->points . ' points' : 'credit' }}.</p>
        <form action="{{ route('patient.tasks.complete', $task) }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-success">
                <i class="bi bi-check-circle me-2"></i>Mark as Complete
            </button>
        </form>
    </div>
</div>
@else
<div class="alert alert-success">
    <i class="bi bi-check-circle-fill me-2"></i>
    <strong>Task Completed!</strong> You completed this task on {{ $task->completed_at->format('M d, Y') }}.
    @if($task->points > 0)
    <br>
    <small>You earned {{ $task->points }} points for completing this task.</small>
    @endif
</div>
@endif

<div class="mt-4">
    <a href="{{ route('patient.tasks.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Tasks
    </a>
</div>
@endsection
