@extends('layouts.app')

@section('title', 'New Journal Entry - Your Mind Aid')

@section('content')
<div class="container-fluid" style="max-width: 800px;">
    <div class="mb-4">
        <h1 class="h3 fw-bold text-stone-900 mb-2">New Journal Entry</h1>
        <p class="text-stone-600 mb-0">Record your mood and thoughts</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('patient.journal.store') }}">
                @csrf

                <div class="mb-4">
                    <label class="form-label fw-medium text-stone-700">How are you feeling today?</label>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="small text-stone-500">Very Low</span>
                        <input type="range" class="form-range" name="mood_score" min="1" max="10" value="5" id="moodSlider">
                        <span class="small text-stone-500">Very Good</span>
                    </div>
                    <div class="text-center mt-2">
                        <span class="h4 fw-bold text-patient-primary" id="moodValue">5</span>
                        <span class="text-stone-500">/10</span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-medium text-stone-700">Notes</label>
                    <textarea class="form-control" name="notes" rows="8" placeholder="How are you feeling? What's on your mind?"></textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('patient.journal.index') }}" class="btn btn-outline-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>
                        Save Entry
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    const slider = document.getElementById('moodSlider');
    const value = document.getElementById('moodValue');
    slider.addEventListener('input', (e) => {
        value.textContent = e.target.value;
    });
</script>
@endpush
@endsection
