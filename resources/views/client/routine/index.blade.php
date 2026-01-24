@extends('client.layout')

@section('title', 'Daily Routine - Your Mind Aid')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Daily Routine</h4>
    <p class="text-muted mb-0 small">Your daily wellness checklist</p>
</div>

@if($routine)
    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-4">
            <div class="stats-card">
                <div class="number">{{ $stats['completion_today'] }}/{{ $stats['total_items'] }}</div>
                <div class="label">Completed Today</div>
            </div>
        </div>
        <div class="col-4">
            <div class="stats-card">
                <div class="number">{{ round(($stats['completion_today'] / max($stats['total_items'], 1)) * 100) }}%</div>
                <div class="label">Progress</div>
            </div>
        </div>
        <div class="col-4">
            <div class="stats-card">
                <div class="number text-success">{{ $stats['current_streak'] }}</div>
                <div class="label">Day Streak</div>
            </div>
        </div>
    </div>

    <!-- Routine Info -->
    <div class="card mb-3">
        <div class="card-body">
            <h5 class="mb-1">{{ $routine->title }}</h5>
            @if($routine->description)
                <p class="text-muted small mb-0">{{ $routine->description }}</p>
            @endif
        </div>
    </div>

    <!-- Routine Items by Time of Day -->
    @foreach(['morning' => 'Morning', 'afternoon' => 'Afternoon', 'evening' => 'Evening', 'night' => 'Night', 'anytime' => 'Anytime'] as $timeOfDay => $label)
        @php
            $items = $routine->items->where('time_of_day', $timeOfDay);
        @endphp
        @if($items->isNotEmpty())
        <div class="card mb-3">
            <div class="card-header bg-white border-0">
                <h6 class="mb-0 fw-semibold">
                    <i class="bi bi-{{ getTimeIcon($timeOfDay) }} me-2"></i>
                    {{ $label }}
                </h6>
            </div>
            <div class="card-body">
                @foreach($items as $item)
                    @php
                        $log = $todayLogs[$item->id] ?? null;
                        $isCompleted = $log && $log->is_completed;
                        $isSkipped = $log && $log->is_skipped;
                    @endphp
                    <div class="d-flex align-items-start mb-3 pb-3 @if(!$loop->last) border-bottom @endif">
                        <div class="flex-shrink-0 me-3">
                            <div class="form-check">
                                <input class="form-check-input routine-checkbox" type="checkbox" 
                                       id="item_{{ $item->id }}"
                                       data-item-id="{{ $item->id }}"
                                       {{ $isCompleted ? 'checked' : '' }}
                                       {{ $isSkipped ? 'disabled' : '' }}
                                       onchange="toggleItem({{ $item->id }}, this.checked)">
                            </div>
                        </div>
                        <div class="flex-grow-1">
                            <label class="form-check-label" for="item_{{ $item->id }}">
                                <div class="fw-semibold {{ $isCompleted ? 'text-decoration-line-through text-success' : '' }}">
                                    {{ $item->title }}
                                    @if($item->is_required)
                                        <span class="badge bg-danger ms-1">Required</span>
                                    @endif
                                </div>
                                @if($item->description)
                                    <small class="text-muted d-block">{{ $item->description }}</small>
                                @endif
                                @if($item->scheduled_time)
                                    <small class="text-muted">
                                        <i class="bi bi-clock me-1"></i>{{ date('g:i A', strtotime($item->scheduled_time)) }}
                                    </small>
                                @endif
                                @if($item->estimated_minutes)
                                    <small class="text-muted ms-2">
                                        <i class="bi bi-hourglass me-1"></i>{{ $item->estimated_minutes }} min
                                    </small>
                                @endif
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    @endforeach
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-calendar-check text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-3 mb-0">No routine assigned yet</p>
            <p class="text-muted small">Your therapist will create a personalized daily routine for you.</p>
        </div>
    </div>
@endif
@endsection

@php
function getTimeIcon($timeOfDay) {
    return match($timeOfDay) {
        'morning' => 'sunrise',
        'afternoon' => 'sun',
        'evening' => 'sunset',
        'night' => 'moon-stars',
        'anytime' => 'clock',
        default => 'circle',
    };
}
@endphp

<script>
function toggleItem(itemId, isCompleted) {
    fetch(`{{ url('/client/routine') }}/${itemId}/log`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            is_completed: isCompleted,
            is_skipped: false,
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload to update stats
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to log routine item');
    });
}
</script>

<style>
.stats-card {
    background: white;
    border-radius: 12px;
    padding: 1rem;
    text-align: center;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}
.stats-card .number {
    font-size: 1.5rem;
    font-weight: bold;
    color: #333;
}
.stats-card .label {
    font-size: 0.75rem;
    color: #666;
    text-transform: uppercase;
}
.routine-checkbox {
    width: 1.5rem;
    height: 1.5rem;
}
</style>
