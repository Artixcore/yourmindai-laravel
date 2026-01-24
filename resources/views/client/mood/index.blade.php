@extends('client.layout')

@section('title', 'Mood Tracking - Your Mind Aid')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Mood Tracking</h4>
    <p class="text-muted mb-0 small">Track your daily mood and emotional patterns</p>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-4">
        <div class="stats-card">
            <div class="number">{{ $stats['total_entries'] }}</div>
            <div class="label">Total Entries</div>
        </div>
    </div>
    <div class="col-4">
        <div class="stats-card">
            <div class="number">{{ $stats['avg_mood'] }}</div>
            <div class="label">Avg Mood</div>
        </div>
    </div>
    <div class="col-4">
        <div class="stats-card">
            <div class="number text-success">{{ $stats['current_streak'] }}</div>
            <div class="label">Day Streak</div>
        </div>
    </div>
</div>

<!-- Log Today's Mood -->
<div class="card mb-3">
    <div class="card-header bg-white border-0">
        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-emoji-smile me-2"></i>
            How are you feeling today?
        </h6>
    </div>
    <div class="card-body">
        <form action="{{ route('client.mood.store') }}" method="POST">
            @csrf
            <input type="hidden" name="log_date" value="{{ date('Y-m-d') }}">
            <input type="hidden" name="log_time" value="{{ date('H:i') }}">
            @if($moodHomework)
                <input type="hidden" name="homework_assignment_id" value="{{ $moodHomework->id }}">
            @endif

            <!-- Mood Rating -->
            <div class="mb-3">
                <label class="form-label fw-semibold">Mood Rating <span class="text-danger">*</span></label>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    @for($i = 1; $i <= 10; $i++)
                        <button type="button" class="btn btn-outline-primary mood-btn" data-mood="{{ $i }}" 
                                onclick="selectMood({{ $i }})">
                            {{ $i }}
                        </button>
                    @endfor
                </div>
                <input type="hidden" name="mood_rating" id="mood_rating" required>
                <div class="text-center" id="moodEmoji" style="font-size: 3rem;"></div>
            </div>

            <!-- Notes -->
            <div class="mb-3">
                <label for="notes" class="form-label">Notes (Optional)</label>
                <textarea name="notes" id="notes" rows="3" class="form-control" 
                          placeholder="What's on your mind? Any triggers or events that affected your mood?"></textarea>
            </div>

            <button type="submit" class="btn btn-primary w-100" id="submitBtn" disabled>
                <i class="bi bi-check-lg me-2"></i>Log Mood
            </button>
        </form>
    </div>
</div>

<!-- Mood History -->
@if($moodLogs->isNotEmpty())
<div class="card">
    <div class="card-header bg-white border-0">
        <h6 class="mb-0 fw-semibold">
            <i class="bi bi-clock-history me-2"></i>
            Mood History (Last 30 Days)
        </h6>
    </div>
    <div class="card-body">
        @foreach($moodLogs as $log)
        <div class="d-flex justify-content-between align-items-start mb-3 pb-3 @if(!$loop->last) border-bottom @endif">
            <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2 mb-1">
                    <span style="font-size: 1.5rem;">{{ $log->mood_emoji }}</span>
                    <span class="fw-semibold">{{ \Carbon\Carbon::parse($log->log_date)->format('M d, Y') }}</span>
                    <span class="badge bg-primary">{{ $log->mood_rating }}/10</span>
                </div>
                @if($log->notes)
                    <p class="text-muted small mb-0">{{ $log->notes }}</p>
                @endif
                
                <!-- Feedback Count -->
                @php
                    $feedbackCount = $log->feedback()->count();
                @endphp
                @if($feedbackCount > 0)
                    <div class="mt-2">
                        <small class="text-muted">
                            <i class="bi bi-chat-left me-1"></i>{{ $feedbackCount }} feedback
                        </small>
                    </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@else
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-emoji-neutral text-muted" style="font-size: 3rem;"></i>
        <p class="text-muted mt-3 mb-0">No mood entries yet</p>
        <p class="text-muted small">Start tracking your mood to see patterns over time.</p>
    </div>
</div>
@endif
@endsection

<script>
function selectMood(rating) {
    // Remove active class from all buttons
    document.querySelectorAll('.mood-btn').forEach(btn => {
        btn.classList.remove('active');
        btn.classList.remove('btn-primary');
        btn.classList.add('btn-outline-primary');
    });
    
    // Add active class to selected button
    const selectedBtn = document.querySelector(`[data-mood="${rating}"]`);
    selectedBtn.classList.add('active');
    selectedBtn.classList.add('btn-primary');
    selectedBtn.classList.remove('btn-outline-primary');
    
    // Set hidden input value
    document.getElementById('mood_rating').value = rating;
    
    // Update emoji
    const emojis = ['😢', '😢', '😕', '😕', '😐', '😐', '🙂', '🙂', '😊', '😊'];
    document.getElementById('moodEmoji').textContent = emojis[rating - 1];
    
    // Enable submit button
    document.getElementById('submitBtn').disabled = false;
}
</script>

<style>
.mood-btn {
    width: 40px;
    height: 40px;
    padding: 0;
    font-weight: bold;
}
.mood-btn.active {
    transform: scale(1.2);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}
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
</style>
@endsection
