@extends('layouts.app')

@section('title', 'Homework Overview - Admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Homework & Techniques Overview</h2>
                    <p class="text-muted mb-0">Monitor all homework assignments across the platform</p>
                </div>
                <a href="{{ route('admin.homework.analytics') }}" class="btn btn-outline-primary">
                    <i class="bi bi-graph-up me-2"></i>View Analytics
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Total Assignments</h6>
                    <h2 class="mb-0">{{ $stats['total'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm bg-warning bg-opacity-10">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Assigned</h6>
                    <h2 class="mb-0 text-warning">{{ $stats['assigned'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm bg-primary bg-opacity-10">
                <div class="card-body">
                    <h6 class="text-muted mb-1">In Progress</h6>
                    <h2 class="mb-0 text-primary">{{ $stats['in_progress'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm bg-success bg-opacity-10">
                <div class="card-body">
                    <h6 class="text-muted mb-1">Completed</h6>
                    <h2 class="mb-0 text-success">{{ $stats['completed'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- By Type Statistics -->
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-white">
            <h5 class="mb-0">Assignments by Technique Type</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @foreach($stats['by_type'] as $type => $count)
                    <div class="col-md-4 col-lg-3">
                        <div class="d-flex align-items-center p-3 bg-light rounded">
                            <i class="bi bi-{{ getIcon($type) }} me-3 text-primary" style="font-size: 1.5rem;"></i>
                            <div>
                                <div class="fw-semibold">{{ ucfirst(str_replace('_', ' ', $type)) }}</div>
                                <div class="text-muted">{{ $count }} assignments</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Technique Type</label>
                    <select name="homework_type" class="form-select">
                        <option value="">All Types</option>
                        <option value="psychotherapy">Psychotherapy</option>
                        <option value="lifestyle_modification">Lifestyle Modification</option>
                        <option value="sleep_tracking">Sleep Tracking</option>
                        <option value="mood_tracking">Mood Tracking</option>
                        <option value="personal_journal">Personal Journal</option>
                        <option value="risk_tracking">Risk Tracking</option>
                        <option value="contingency">Contingency</option>
                        <option value="exercise">Exercise</option>
                        <option value="parent_role">Parent's Role</option>
                        <option value="others_role">Others' Role</option>
                        <option value="self_help_tools">Self-Help Tools</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">All Statuses</option>
                        <option value="assigned">Assigned</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">Apply</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Homework Table -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Title</th>
                            <th>Patient</th>
                            <th>Doctor</th>
                            <th>Frequency</th>
                            <th>Status</th>
                            <th>Completions</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($homework as $assignment)
                        <tr>
                            <td>{{ $assignment->id }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ ucfirst(str_replace('_', ' ', $assignment->homework_type)) }}</span>
                            </td>
                            <td>
                                <strong>{{ $assignment->title }}</strong>
                            </td>
                            <td>{{ $assignment->patient->user->name ?? 'N/A' }}</td>
                            <td>{{ $assignment->assignedByDoctor->name ?? 'N/A' }}</td>
                            <td>{{ ucfirst($assignment->frequency) }}</td>
                            <td>
                                @if($assignment->status === 'completed')
                                    <span class="badge bg-success">Completed</span>
                                @elseif($assignment->status === 'in_progress')
                                    <span class="badge bg-primary">In Progress</span>
                                @elseif($assignment->status === 'assigned')
                                    <span class="badge bg-warning">Assigned</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($assignment->status) }}</span>
                                @endif
                            </td>
                            <td>{{ $assignment->completions->count() }}</td>
                            <td>
                                <a href="{{ route('admin.homework.show', $assignment->id) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">No homework assignments found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $homework->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@php
function getIcon($type) {
    return match($type) {
        'psychotherapy' => 'chat-heart',
        'lifestyle_modification' => 'heart-pulse',
        'sleep_tracking' => 'moon-stars',
        'mood_tracking' => 'emoji-smile',
        'personal_journal' => 'journal-text',
        'risk_tracking' => 'shield-exclamation',
        'contingency' => 'shield-check',
        'exercise' => 'bicycle',
        'parent_role' => 'people',
        'others_role' => 'person-badge',
        'self_help_tools' => 'tools',
        default => 'clipboard-check',
    };
}
@endphp
