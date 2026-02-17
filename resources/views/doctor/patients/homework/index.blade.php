@extends('layouts.app')

@section('title', 'Patient Homework - Your Mind Aid')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Patients</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('patients.show', $patient) }}">{{ $patientProfile->user->name ?? $patientProfile->full_name ?? $patient->name ?? 'Patient' }}</a></li>
                    <li class="breadcrumb-item active">Homework & Techniques</li>
                </ol>
            </nav>
            
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Homework & Techniques</h2>
                    <p class="text-muted mb-0">Manage therapy assignments for {{ $patientProfile->user->name ?? $patientProfile->full_name ?? $patient->name ?? 'Patient' }}</p>
                </div>
                <a href="{{ route('patients.homework.create', $patient) }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-2"></i>Assign New Homework
                </a>
            </div>
        </div>
    </div>

    @if($homework->isNotEmpty())
        @foreach($homework->groupBy('homework_type') as $type => $assignments)
        <div class="card shadow-sm mb-3">
            <div class="card-header bg-white">
                <h5 class="mb-0">
                    <i class="bi bi-{{ getIcon($type) }} me-2"></i>
                    {{ ucfirst(str_replace('_', ' ', $type)) }}
                    <span class="badge bg-secondary ms-2">{{ $assignments->count() }}</span>
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Assignment</th>
                                <th>Frequency</th>
                                <th>Date Range</th>
                                <th>Status</th>
                                <th>Completion</th>
                                <th>Feedback</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assignments as $assignment)
                            <tr>
                                <td>
                                    <strong>{{ $assignment->title }}</strong>
                                    @if($assignment->session)
                                        <br><small class="text-muted">Session #{{ $assignment->session->id }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark">{{ ucfirst($assignment->frequency) }}</span>
                                </td>
                                <td>
                                    <small>
                                        {{ \Carbon\Carbon::parse($assignment->start_date)->format('M d, Y') }}
                                        @if($assignment->end_date)
                                            <br>to {{ \Carbon\Carbon::parse($assignment->end_date)->format('M d, Y') }}
                                        @endif
                                    </small>
                                </td>
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
                                <td>
                                    @php
                                        $completionCount = $assignment->completions->where('is_completed', true)->count();
                                        $totalExpected = $assignment->frequency === 'daily' 
                                            ? \Carbon\Carbon::parse($assignment->start_date)->diffInDays(now()) 
                                            : 0;
                                    @endphp
                                    <small>
                                        {{ $completionCount }} entries
                                        @if($totalExpected > 0)
                                            <br>{{ round(($completionCount / $totalExpected) * 100) }}% complete
                                        @endif
                                    </small>
                                </td>
                                <td>
                                    @php
                                        $selfCount = $assignment->feedback->where('source', 'self')->count();
                                        $parentCount = $assignment->feedback->where('source', 'parent')->count();
                                        $othersCount = $assignment->feedback->where('source', 'others')->count();
                                    @endphp
                                    <div class="d-flex gap-1">
                                        @if($selfCount > 0)
                                            <span class="badge bg-info" title="Self feedback">
                                                S: {{ $selfCount }}
                                            </span>
                                        @endif
                                        @if($parentCount > 0)
                                            <span class="badge bg-success" title="Parent feedback">
                                                P: {{ $parentCount }}
                                            </span>
                                        @endif
                                        @if($othersCount > 0)
                                            <span class="badge bg-purple" title="Others feedback">
                                                O: {{ $othersCount }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ route('patients.homework.show', [$patient, $assignment]) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endforeach
    @else
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-clipboard-check text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-3 mb-2">No homework assigned yet</p>
                <p class="text-muted small mb-4">Start by assigning therapy techniques to this patient.</p>
                <a href="{{ route('patients.homework.create', $patient) }}" class="btn btn-primary">
                    <i class="bi bi-plus-lg me-2"></i>Assign First Homework
                </a>
            </div>
        </div>
    @endif
</div>

<style>
.bg-purple {
    background-color: #8b5cf6 !important;
    color: white !important;
}
</style>
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
