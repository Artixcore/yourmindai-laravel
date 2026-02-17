@extends('layouts.app')

@section('title', 'Tasks - ' . (optional($patient->user)->name ?? optional($patient)->full_name ?? 'Patient'))

@section('content')
<div class="mb-4">
    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Patients', 'url' => route('patients.index')],
        ['label' => optional($patient->user)->name ?? optional($patient)->full_name ?? 'Patient', 'url' => route('patients.show', $patient)],
        ['label' => 'Tasks']
    ]" />
    <h1 class="h3 mb-1 fw-semibold">Tasks</h1>
    <p class="text-muted mb-0">Manage tasks for {{ optional($patient->user)->name ?? optional($patient)->full_name ?? 'patient' }}</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <p class="text-muted small mb-1">Total</p>
                <h4 class="fw-bold mb-0">{{ $stats['total'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <p class="text-muted small mb-1">Pending</p>
                <h4 class="fw-bold text-warning mb-0">{{ $stats['pending'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <p class="text-muted small mb-1">Completed</p>
                <h4 class="fw-bold text-success mb-0">{{ $stats['completed'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <p class="text-muted small mb-1">Overdue</p>
                <h4 class="fw-bold text-danger mb-0">{{ $stats['overdue'] }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <a href="{{ route('tasks.create', ['patient_id' => $patient->id]) }}" class="btn btn-primary mb-3">Add Task</a>
        @if($tasks->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-check-square text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-3 mb-0">No tasks for this patient.</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $t)
                    <tr>
                        <td>{{ $t->title }}</td>
                        <td>{{ $t->due_date ? $t->due_date->format('M d, Y') : '—' }}</td>
                        <td><span class="badge bg-{{ $t->status === 'completed' ? 'success' : 'warning' }}">{{ ucfirst($t->status) }}</span></td>
                        <td class="text-end">
                            <a href="{{ route('tasks.show', $t) }}" class="btn btn-sm btn-outline-primary">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
