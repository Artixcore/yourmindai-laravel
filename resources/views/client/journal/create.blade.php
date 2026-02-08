@extends('client.layout')

@section('title', 'New Journal Entry - Your Mind Aid')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">New Journal Entry</h4>
    <p class="text-muted mb-0 small">Record your mood and thoughts</p>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('client.journal.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label small">How are you feeling? (1–10)</label>
                <div class="d-flex align-items-center gap-2">
                    <input type="range" class="form-range flex-grow-1" name="mood_score" min="1" max="10" value="5" id="moodSlider">
                    <span class="fw-bold" id="moodValue">5</span>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label small">Notes</label>
                <textarea class="form-control" name="notes" rows="4" placeholder="How are you feeling? What's on your mind?"></textarea>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('client.journal.index') }}" class="btn btn-outline-secondary btn-sm">Cancel</a>
                <button type="submit" class="btn btn-primary btn-sm">Save entry</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('moodSlider').addEventListener('input', function(e) {
    document.getElementById('moodValue').textContent = e.target.value;
});
</script>
@endpush
@endsection
