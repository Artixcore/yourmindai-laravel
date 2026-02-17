<div id="completion-{{ $completion->id }}" class="border-bottom pb-3 mb-3">
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <strong>{{ $completion->completion_date->format('M d, Y') }}</strong>
            @if($completion->completion_time)
                <span class="text-muted">at {{ \Carbon\Carbon::parse($completion->completion_time)->format('g:i A') }}</span>
            @endif
            <div class="mt-1">
                @if($completion->is_completed)
                    <span class="badge bg-success">Completed {{ $completion->completion_percentage }}%</span>
                @else
                    <span class="badge bg-warning">In Progress {{ $completion->completion_percentage }}%</span>
                @endif
                @if($completion->scoring_choice)
                    <span class="badge bg-info">
                        {{ ucfirst(str_replace('_', ' ', $completion->scoring_choice)) }}
                        ({{ $completion->score_value > 0 ? '+' : '' }}{{ $completion->score_value }})
                    </span>
                @endif
                @if($completion->reviewed_at)
                    <span class="badge bg-secondary">
                        <i class="bi bi-check-circle me-1"></i>Reviewed
                    </span>
                @endif
            </div>
        </div>
        @if(!$completion->reviewed_at)
            <form action="{{ route('patients.homework.completions.review', [$patient, $homework, $completion]) }}" method="POST" class="d-inline ajax-form" data-target="#completion-{{ $completion->id }}">
                @csrf
                @method('PUT')
                <button type="submit" class="btn btn-sm btn-primary" data-loading-text="Saving...">Mark Reviewed</button>
            </form>
        @endif
    </div>
    @if($completion->patient_notes)
        <p class="mt-2 mb-0 small">{{ $completion->patient_notes }}</p>
    @endif
    @if($completion->reviewed_at && $completion->reviewer)
        <small class="text-muted">Reviewed by {{ $completion->reviewer->name }} on {{ $completion->reviewed_at->format('M d, Y') }}</small>
    @endif
</div>
