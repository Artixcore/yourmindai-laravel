@extends('layouts.app')

@section('title', 'Tasks')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('patient.dashboard')],
            ['label' => 'Tasks']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">My Tasks</h1>
        <p class="text-muted mb-0">Complete tasks assigned by your healthcare provider</p>
    </div>
</div>

<!-- Points Display -->
@if($totalPoints > 0)
<div class="card border-0 shadow-sm mb-4 bg-gradient-primary text-white">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <p class="mb-1 opacity-75">Total Points</p>
                <h2 class="mb-0 fw-bold">{{ number_format($totalPoints) }}</h2>
            </div>
            <div class="bg-white bg-opacity-20 rounded-circle p-3">
                <i class="bi bi-trophy-fill" style="font-size: 2rem;"></i>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <p class="text-muted small mb-1">Total Tasks</p>
                <h4 class="fw-bold mb-0">{{ $stats['total'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <p class="text-muted small mb-1">Pending</p>
                <h4 class="fw-bold text-warning mb-0">{{ $stats['pending'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center">
                <p class="text-muted small mb-1">Completed</p>
                <h4 class="fw-bold text-success mb-0">{{ $stats['completed'] }}</h4>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <div class="btn-group" role="group">
            <a href="{{ route('patient.tasks.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-primary' : 'btn-outline-primary' }}">
                All
            </a>
            <a href="{{ route('patient.tasks.index', ['status' => 'pending']) }}" class="btn btn-sm {{ request('status') === 'pending' ? 'btn-primary' : 'btn-outline-primary' }}">
                Pending
            </a>
            <a href="{{ route('patient.tasks.index', ['status' => 'completed']) }}" class="btn btn-sm {{ request('status') === 'completed' ? 'btn-primary' : 'btn-outline-primary' }}">
                Completed
            </a>
        </div>
    </div>
</div>

<!-- Tasks List -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0">
        <h5 class="mb-0 fw-semibold">Tasks</h5>
    </div>
    <div class="card-body p-0">
        @if($tasks->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-check-square text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 mb-2">No tasks found</h5>
            <p class="text-muted mb-0">
                @if(request('status') === 'completed')
                    You haven't completed any tasks yet.
                @elseif(request('status') === 'pending')
                    You have no pending tasks. Great job!
                @else
                    You don't have any tasks assigned yet.
                @endif
            </p>
        </div>
        @else
        <div class="list-group list-group-flush">
            @foreach($tasks as $task)
            <div class="list-group-item border-0 border-bottom">
                <div class="d-flex align-items-start justify-content-between">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <h6 class="mb-0 fw-semibold">{{ $task->title }}</h6>
                            @if($task->status === 'completed')
                            <span class="badge bg-success">Completed</span>
                            @else
                            <span class="badge bg-warning">Pending</span>
                            @endif
                            @if($task->points > 0)
                            <span class="badge bg-info">
                                <i class="bi bi-star-fill me-1"></i>{{ $task->points }} pts
                            </span>
                            @endif
                        </div>
                        
                        @if($task->description)
                        <p class="text-muted small mb-2">{{ \Illuminate\Support\Str::limit($task->description, 150) }}</p>
                        @endif
                        
                        <div class="d-flex align-items-center gap-3 small text-muted">
                            @if($task->due_date)
                            <span>
                                <i class="bi bi-calendar3 me-1"></i>
                                Due: {{ $task->due_date->format('M d, Y') }}
                                @if($task->due_date->isPast() && $task->status !== 'completed')
                                <span class="text-danger">(Overdue)</span>
                                @endif
                            </span>
                            @endif
                            
                            @if($task->assignedByDoctor)
                            <span>
                                <i class="bi bi-person me-1"></i>
                                Assigned by: {{ $task->assignedByDoctor->name ?? 'Doctor' }}
                            </span>
                            @endif
                            
                            @if($task->completed_at)
                            <span>
                                <i class="bi bi-check-circle me-1"></i>
                                Completed: {{ $task->completed_at->format('M d, Y') }}
                            </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="ms-3">
                        @if($task->status !== 'completed')
                        <form action="{{ route('patient.tasks.complete', $task) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-success">
                                <i class="bi bi-check-circle me-1"></i>Complete
                            </button>
                        </form>
                        @endif
                        <a href="{{ route('patient.tasks.show', $task) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i> View
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
