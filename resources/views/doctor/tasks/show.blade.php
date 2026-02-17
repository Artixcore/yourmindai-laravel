@extends('layouts.app')

@section('title', 'Task: ' . $task->title)

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('tasks.index') }}" class="text-decoration-none text-primary mb-3 d-inline-flex align-items-center">
            <i class="bi bi-arrow-left me-1"></i> Back to Tasks
        </a>
        <h1 class="h2 fw-bold text-stone-900 mb-0">{{ $task->title }}</h1>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <p class="text-muted small mb-2">Patient: {{ optional($task->patient->user)->name ?? optional($task->patient)->full_name ?? '—' }}</p>
            <p class="text-muted small mb-2">Due: {{ $task->due_date ? $task->due_date->format('M d, Y') : '—' }}</p>
            <p class="text-muted small mb-3">Status: <span class="badge bg-{{ $task->status === 'completed' ? 'success' : 'warning' }}">{{ ucfirst($task->status) }}</span></p>
            @if($task->description)
            <hr>
            <p class="mb-0">{{ $task->description }}</p>
            @endif
        </div>
    </div>

    @if($task->status !== 'completed')
    <div class="d-flex gap-2">
        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-primary">Edit</a>
        <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this task?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-outline-danger">Delete</button>
        </form>
    </div>
    @endif
</div>
@endsection
