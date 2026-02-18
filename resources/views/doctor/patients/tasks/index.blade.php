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
    <div class="col-6 col-md-6 col-lg">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <p class="text-muted small mb-1">Total Tasks</p>
                <h4 class="fw-bold mb-0">{{ $stats['total'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-6 col-lg">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <p class="text-muted small mb-1">Pending</p>
                <h4 class="fw-bold text-warning mb-0">{{ $stats['pending'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-6 col-lg">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <p class="text-muted small mb-1">Completed</p>
                <h4 class="fw-bold text-success mb-0">{{ $stats['completed'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-6 col-lg">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <p class="text-muted small mb-1">Overdue</p>
                <h4 class="fw-bold text-danger mb-0">{{ $stats['overdue'] }}</h4>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-6 col-lg">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <p class="text-muted small mb-1">Patient Points</p>
                <h4 class="fw-bold mb-0 {{ ($stats['total_points'] ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ $patient->user_id ? ($stats['total_points'] ?? 0) : 'N/A' }}
                </h4>
                <small class="text-muted">Tasks + contingency</small>
                @if($patient->user_id)
                    <div class="mt-2">
                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#adjustPointsModal">
                            Adjust Points
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($patient->user_id)
<!-- Adjust Points Modal -->
<div class="modal fade" id="adjustPointsModal" tabindex="-1" aria-labelledby="adjustPointsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('patients.points.adjust', $patient) }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="adjustPointsModalLabel">Adjust Patient Points</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="points_delta" class="form-label">Points <span class="text-muted">(-100 to +100)</span></label>
                        <input type="number" class="form-control @error('points_delta') is-invalid @enderror" id="points_delta" name="points_delta" min="-100" max="100" value="{{ old('points_delta', 0) }}" required>
                        <small class="text-muted">Positive = add points, Negative = subtract points</small>
                        @error('points_delta')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="category" class="form-label">Category</label>
                        <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                            <option value="contingency" {{ old('category') === 'contingency' ? 'selected' : '' }}>Contingency</option>
                            <option value="task" {{ old('category') === 'task' ? 'selected' : '' }}>Task</option>
                            <option value="other" {{ old('category') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="reason" class="form-label">Reason <span class="text-muted">(optional)</span></label>
                        <input type="text" class="form-control @error('reason') is-invalid @enderror" id="reason" name="reason" maxlength="255" value="{{ old('reason') }}" placeholder="e.g. Contingency compliance, missed session">
                        @error('reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Apply Adjustment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

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
                        <th>Points</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tasks as $t)
                    <tr>
                        <td>{{ $t->title }}</td>
                        <td>{{ $t->due_date ? $t->due_date->format('M d, Y') : '—' }}</td>
                        <td>
                            @if($t->points != 0)
                                <span class="badge bg-{{ $t->points > 0 ? 'success' : 'danger' }}">
                                    {{ $t->points > 0 ? '+' : '' }}{{ $t->points }}
                                </span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
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
