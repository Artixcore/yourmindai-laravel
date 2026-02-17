@extends('layouts.app')

@section('title', 'Task Management')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <h1 class="h3 mb-1 fw-semibold">Task Management</h1>
        <p class="text-muted mb-0">Assign and manage tasks for patients</p>
    </div>
    <a href="{{ route('tasks.create') }}" class="btn btn-primary">New Task</a>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body text-center"><p class="text-muted small mb-1">Total</p><h4 class="fw-bold mb-0">{{ $stats['total'] }}</h4></div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body text-center"><p class="text-muted small mb-1">Pending</p><h4 class="fw-bold text-warning mb-0">{{ $stats['pending'] }}</h4></div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body text-center"><p class="text-muted small mb-1">Completed</p><h4 class="fw-bold text-success mb-0">{{ $stats['completed'] }}</h4></div></div></div>
    <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body text-center"><p class="text-muted small mb-1">Overdue</p><h4 class="fw-bold text-danger mb-0">{{ $stats['overdue'] }}</h4></div></div></div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        @if($tasks->isEmpty())
        <div class="text-center py-5">
            <p class="text-muted mb-0">No tasks yet.</p>
            <a href="{{ route('tasks.create') }}" class="btn btn-primary mt-3">Create Task</a>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Task</th>
                        <th>Patient</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $t)
                    <tr>
                        <td>{{ $t->title }}</td>
                        <td>{{ $t->patient->user->name ?? $t->patient->full_name ?? '—' }}</td>
                        <td>{{ $t->due_date ? $t->due_date->format('M d, Y') : '—' }}</td>
                        <td><span class="badge bg-{{ $t->status === 'completed' ? 'success' : 'warning' }}">{{ ucfirst($t->status) }}</span></td>
                        <td class="text-end"><a href="{{ route('tasks.show', $t) }}" class="btn btn-sm btn-outline-primary">View</a></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $tasks->withQueryString()->links() }}
        @endif
    </div>
</div>
@endsection
