@extends('client.layout')

@section('title', 'Techniques & Homework - Your Mind Aid')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Techniques & Homework</h4>
    <p class="text-muted mb-0 small">Your therapy assignments and practice tracking</p>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-4">
        <div class="stats-card">
            <div class="number">{{ $stats['total_assigned'] }}</div>
            <div class="label">Active Assignments</div>
        </div>
    </div>
    <div class="col-4">
        <div class="stats-card">
            <div class="number text-success">{{ $stats['completed_today'] }}</div>
            <div class="label">Completed Today</div>
        </div>
    </div>
    <div class="col-4">
        <div class="stats-card">
            <div class="number text-primary">{{ $stats['completion_percentage'] }}%</div>
            <div class="label">Overall Progress</div>
        </div>
    </div>
</div>

<!-- Homework Types -->
@if($homework->isNotEmpty())
    @foreach($homework as $type => $assignments)
    <div class="card mb-3">
        <div class="card-header bg-white border-0">
            <h6 class="mb-0 fw-semibold">
                <i class="bi bi-{{ $this->getIcon($type) }} me-2"></i>
                {{ ucfirst(str_replace('_', ' ', $type)) }}
            </h6>
        </div>
        <div class="card-body">
            @foreach($assignments as $assignment)
            <div class="d-flex justify-content-between align-items-start mb-3 pb-3 @if(!$loop->last) border-bottom @endif">
                <div class="flex-grow-1">
                    <h6 class="mb-1 fw-semibold">{{ $assignment->title }}</h6>
                    <p class="text-muted small mb-2">{{ Str::limit($assignment->description, 80) }}</p>
                    
                    <!-- Progression Indicators -->
                    @php
                        $latestProgression = $assignment->latestProgression();
                    @endphp
                    @if($latestProgression)
                    <div class="d-flex align-items-center gap-3 small">
                        <span class="text-primary">
                            <i class="bi bi-graph-up me-1"></i>
                            {{ $latestProgression->progress_percentage }}% Progress
                        </span>
                        <span class="text-muted">
                            Monitored by: {{ ucfirst($latestProgression->monitored_by) }}
                        </span>
                    </div>
                    @endif
                    
                    <!-- Feedback Indicators -->
                    <div class="d-flex gap-2 mt-2">
                        @if($assignment->requires_parent_feedback && $assignment->parentFeedback()->count() > 0)
                            <span class="badge bg-success small">
                                <i class="bi bi-check-circle me-1"></i>Parent Feedback: {{ $assignment->parentFeedback()->count() }}
                            </span>
                        @endif
                        @if($assignment->selfFeedback()->count() > 0)
                            <span class="badge bg-info small">
                                <i class="bi bi-check-circle me-1"></i>Self Feedback: {{ $assignment->selfFeedback()->count() }}
                            </span>
                        @endif
                    </div>
                </div>
                <div class="flex-shrink-0 ms-3">
                    <a href="{{ route('client.homework.show', $assignment->id) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-clipboard-check text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-3 mb-0">No homework assignments yet</p>
            <p class="text-muted small">Your therapist will assign techniques and homework during your sessions.</p>
        </div>
    </div>
@endif
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
