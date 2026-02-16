@extends('client.layout')

@section('title', $homework->title . ' - Your Mind Aid')

@section('content')
<div class="mb-4">
    <a href="{{ route('client.homework.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
        <i class="bi bi-arrow-left me-1"></i>Back to Homework
    </a>
    <h4 class="fw-bold mb-1">{{ $homework->title }}</h4>
    <p class="text-muted mb-0 small">
        <i class="bi bi-{{ getIcon($homework->homework_type) }} me-1"></i>
        {{ ucfirst(str_replace('_', ' ', $homework->homework_type)) }}
    </p>
</div>

<!-- Assignment Details -->
<div class="card mb-3">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-6">
                <small class="text-muted">Frequency</small>
                <div class="fw-semibold">{{ ucfirst($homework->frequency) }}</div>
            </div>
            <div class="col-6">
                <small class="text-muted">Status</small>
                <div>
                    @if($homework->status === 'completed')
                        <span class="badge bg-success">Completed</span>
                    @elseif($homework->status === 'in_progress')
                        <span class="badge bg-primary">In Progress</span>
                    @else
                        <span class="badge bg-warning">Assigned</span>
                    @endif
                </div>
            </div>
            <div class="col-6">
                <small class="text-muted">Start Date</small>
                <div class="fw-semibold">{{ \Carbon\Carbon::parse($homework->start_date)->format('M d, Y') }}</div>
            </div>
            @if($homework->end_date)
            <div class="col-6">
                <small class="text-muted">End Date</small>
                <div class="fw-semibold">{{ \Carbon\Carbon::parse($homework->end_date)->format('M d, Y') }}</div>
            </div>
            @endif
        </div>

        @if($homework->description)
        <div class="mt-3 pt-3 border-top">
            <small class="text-muted">Description</small>
            <p class="mb-0">{{ $homework->description }}</p>
        </div>
        @endif

        @if($homework->instructions)
        <div class="mt-3 pt-3 border-top">
            <small class="text-muted">Instructions</small>
            <div class="mt-2">{{ $homework->instructions }}</div>
        </div>
        @endif

        @if($homework->media && $homework->media->isNotEmpty())
        <div class="mt-3 pt-3 border-top">
            <small class="text-muted">Media Resources</small>
            <ul class="list-unstyled mt-2 mb-0">
                @foreach($homework->media as $media)
                <li class="mb-1">
                    <a href="{{ $media->url }}" target="_blank" rel="noopener" class="text-primary">
                        <i class="bi bi-{{ $media->type === 'video' ? 'play-circle' : ($media->type === 'audio' ? 'music-note' : 'link-45deg') }} me-1"></i>
                        {{ $media->title ?: $media->url }}
                    </a>
                </li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>
</div>

<!-- Today's Progress -->
@if(!$todayCompletion || !$todayCompletion->is_completed)
<div class="card mb-3 border-primary">
    <div class="card-body">
        <h6 class="fw-semibold mb-3">
            <i class="bi bi-calendar-check me-2"></i>
            Mark Today's Progress
        </h6>

        <form action="{{ route('client.homework.complete', $homework->id) }}" method="POST">
            @csrf
            
            <!-- Homework Done? Yes/No -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Homework done today?</label>
                <div class="d-flex gap-4">
                    <div class="form-check">
                        <input type="radio" name="homework_done" id="homework_done_yes" value="yes" class="form-check-input" {{ old('homework_done', 'yes') === 'yes' ? 'checked' : '' }} onchange="togglePercentage(this)">
                        <label for="homework_done_yes" class="form-check-label">Yes</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" name="homework_done" id="homework_done_no" value="no" class="form-check-input" {{ old('homework_done') === 'no' ? 'checked' : '' }} onchange="togglePercentage(this)">
                        <label for="homework_done_no" class="form-check-label">No</label>
                    </div>
                </div>
            </div>

            <!-- Completion Percentage -->
            <div class="mb-3" id="percentageGroup">
                <label for="completion_percentage" class="form-label">How much did you complete today?</label>
                <input type="range" class="form-range" name="completion_percentage" id="completion_percentage" 
                       min="0" max="100" step="10" value="100" oninput="updatePercentageLabel(this.value)">
                <div class="d-flex justify-content-between">
                    <small class="text-muted">0%</small>
                    <small id="percentageLabel" class="fw-semibold">100%</small>
                    <small class="text-muted">100%</small>
                </div>
            </div>

            <!-- Contingency Scoring (optional) -->
            <div class="mb-3">
                <label class="form-label fw-semibold">How did you complete this? (Optional)</label>
                <div class="d-flex flex-column gap-2">
                    <div class="form-check">
                        <input type="radio" name="scoring_choice" id="scoring_self" value="self_action" class="form-check-input" {{ old('scoring_choice') === 'self_action' ? 'checked' : '' }}>
                        <label for="scoring_self" class="form-check-label">Self action (+10)</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" name="scoring_choice" id="scoring_others" value="others_help" class="form-check-input" {{ old('scoring_choice') === 'others_help' ? 'checked' : '' }}>
                        <label for="scoring_others" class="form-check-label">Others helped (+5)</label>
                    </div>
                    <div class="form-check">
                        <input type="radio" name="scoring_choice" id="scoring_not" value="not_working" class="form-check-input" {{ old('scoring_choice') === 'not_working' ? 'checked' : '' }}>
                        <label for="scoring_not" class="form-check-label">Not working (-10)</label>
                    </div>
                </div>
            </div>

            <!-- Comment / Feedback -->
            <div class="mb-3">
                <label for="patient_notes" class="form-label">Comment / Feedback (Optional)</label>
                <textarea name="patient_notes" id="patient_notes" rows="3" class="form-control" placeholder="How did it go? Any challenges or observations? Feedback for your doctor?">{{ old('patient_notes') }}</textarea>
            </div>

            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-check-circle me-2"></i>Submit Today's Progress
            </button>
        </form>
    </div>
</div>
@else
<div class="alert alert-success">
    <i class="bi bi-check-circle me-2"></i>
    You've already completed this assignment today! Great job!
</div>
@endif

<!-- Progress History -->
@if($homework->practiceProgressions()->count() > 0)
<div class="card mb-3">
    <div class="card-header bg-white border-0">
        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-graph-up me-2"></i>
            Your Progress History
        </h6>
    </div>
    <div class="card-body">
        @foreach($homework->practiceProgressions()->latest('progress_date')->take(10)->get() as $progression)
        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
            <div>
                <div class="fw-semibold">{{ \Carbon\Carbon::parse($progression->progress_date)->format('M d, Y') }}</div>
                <small class="text-muted">Monitored by: {{ ucfirst($progression->monitored_by) }}</small>
            </div>
            <div>
                <span class="badge bg-primary">{{ $progression->progress_percentage }}%</span>
                <span class="badge bg-{{ $progression->status === 'completed' ? 'success' : 'warning' }}">
                    {{ ucfirst($progression->status) }}
                </span>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Feedback -->
@if($homework->feedback()->count() > 0)
<div class="card">
    <div class="card-header bg-white border-0">
        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-chat-left-text me-2"></i>
            Feedback
        </h6>
    </div>
    <div class="card-body">
        @foreach($homework->feedback()->latest()->get() as $feedback)
        <div class="mb-3 pb-3 border-bottom">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <span class="badge bg-{{ $feedback->source === 'parent' ? 'success' : ($feedback->source === 'self' ? 'info' : 'purple') }}">
                        {{ ucfirst($feedback->source) }}
                    </span>
                    <small class="text-muted ms-2">{{ $feedback->feedback_date->format('M d, Y') }}</small>
                </div>
                @if($feedback->rating)
                    <span class="badge bg-warning">{{ $feedback->rating }}/5</span>
                @endif
            </div>
            @if($feedback->feedback_text)
                <p class="mb-0">{{ $feedback->feedback_text }}</p>
            @endif
        </div>
        @endforeach
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

<script>
function updatePercentageLabel(value) {
    document.getElementById('percentageLabel').textContent = value + '%';
}
function togglePercentage(radio) {
    var group = document.getElementById('percentageGroup');
    var slider = document.getElementById('completion_percentage');
    if (radio.value === 'yes') {
        group.style.display = 'block';
        slider.value = 100;
        updatePercentageLabel(100);
    } else {
        group.style.display = 'block';
        slider.value = 0;
        updatePercentageLabel(0);
    }
}
</script>

<style>
.bg-purple {
    background-color: #8b5cf6 !important;
    color: white !important;
}
</style>
