@extends('client.layout')

@section('title', 'Assessment - Your Mind Aid')

@section('content')
@if($assessment->status === 'completed')
    @include('client.assessments.result')
@else
<div class="mb-4">
    <h4 class="fw-bold mb-1">{{ $assessment->scale->name ?? 'Assessment' }}</h4>
    @if($assessment->scale->description)
    <p class="text-muted mb-0 small">{{ $assessment->scale->description }}</p>
    @endif
</div>

<form method="POST" action="{{ route('client.assessments.complete', $assessment->id) }}" id="assessmentForm">
    @csrf
    
    <div class="card mb-3">
        <div class="card-body">
            <div class="mb-3">
                <div class="progress" style="height: 8px;">
                    <div class="progress-bar" role="progressbar" id="progressBar" style="width: 0%"></div>
                </div>
                <small class="text-muted mt-1 d-block" id="progressText">0 of {{ count($assessment->scale->questions ?? []) }} questions answered</small>
            </div>
            
            <div id="questionsContainer">
                @if($assessment->scale && $assessment->scale->questions)
                    @foreach($assessment->scale->questions as $index => $question)
                    <div class="question-item mb-4 pb-4 border-bottom" data-question-id="{{ $question['id'] ?? $index }}">
                        <h6 class="fw-semibold mb-3">
                            {{ $index + 1 }}. {{ $question['text'] ?? 'Question ' . ($index + 1) }}
                        </h6>
                        
                        @if(isset($question['type']))
                            @if($question['type'] === 'likert' || $question['type'] === 'scale')
                                <!-- Likert Scale -->
                                <div class="likert-scale">
                                    @php
                                        $min = $question['min'] ?? 0;
                                        $max = $question['max'] ?? 10;
                                        $labels = $question['labels'] ?? [];
                                    @endphp
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        @if(!empty($labels) && isset($labels[0]))
                                        <small class="text-muted">{{ $labels[0] }}</small>
                                        @else
                                        <small class="text-muted">Not at all</small>
                                        @endif
                                        @if(!empty($labels) && isset($labels[count($labels)-1]))
                                        <small class="text-muted">{{ $labels[count($labels)-1] }}</small>
                                        @else
                                        <small class="text-muted">Extremely</small>
                                        @endif
                                    </div>
                                    <input 
                                        type="range" 
                                        class="form-range" 
                                        name="responses[{{ $question['id'] ?? $index }}]" 
                                        min="{{ $min }}" 
                                        max="{{ $max }}" 
                                        value="{{ floor(($min + $max) / 2) }}"
                                        required
                                        oninput="updateLikertValue(this, {{ $question['id'] ?? $index }})"
                                    >
                                    <div class="text-center mt-2">
                                        <span class="fw-bold text-primary" id="value-{{ $question['id'] ?? $index }}">{{ floor(($min + $max) / 2) }}</span>
                                    </div>
                                </div>
                            @elseif($question['type'] === 'multiple_choice')
                                <!-- Multiple Choice -->
                                <div class="multiple-choice">
                                    @if(isset($question['options']))
                                        @foreach($question['options'] as $optionIndex => $option)
                                        <div class="form-check mb-2">
                                            <input 
                                                class="form-check-input" 
                                                type="radio" 
                                                name="responses[{{ $question['id'] ?? $index }}]" 
                                                id="q{{ $question['id'] ?? $index }}_opt{{ $optionIndex }}"
                                                value="{{ $option['value'] ?? $optionIndex }}"
                                                required
                                            >
                                            <label class="form-check-label" for="q{{ $question['id'] ?? $index }}_opt{{ $optionIndex }}">
                                                {{ $option['text'] ?? $option }}
                                            </label>
                                        </div>
                                        @endforeach
                                    @endif
                                </div>
                            @elseif($question['type'] === 'text' || $question['type'] === 'textarea')
                                <!-- Text Input -->
                                <textarea 
                                    class="form-control" 
                                    name="responses[{{ $question['id'] ?? $index }}]" 
                                    rows="3"
                                    placeholder="Your answer..."
                                    required
                                ></textarea>
                            @else
                                <!-- Default: Number Input -->
                                <input 
                                    type="number" 
                                    class="form-control" 
                                    name="responses[{{ $question['id'] ?? $index }}]" 
                                    min="{{ $question['min'] ?? 0 }}"
                                    max="{{ $question['max'] ?? 100 }}"
                                    required
                                >
                            @endif
                        @else
                            <!-- Default: Number Input if no type specified -->
                            <input 
                                type="number" 
                                class="form-control" 
                                name="responses[{{ $question['id'] ?? $index }}]" 
                                min="0"
                                max="10"
                                required
                            >
                        @endif
                    </div>
                    @endforeach
                @else
                <div class="alert alert-warning">
                    No questions available for this assessment.
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="d-grid gap-2">
        <button type="submit" class="btn btn-primary btn-lg">
            <i class="bi bi-check-circle me-2"></i>
            Complete Assessment
        </button>
        <a href="{{ route('client.assessments.index') }}" class="btn btn-outline-secondary">
            Cancel
        </a>
    </div>
</form>
@endif
@endsection

@push('scripts')
<script src="{{ asset('js/psychometric-assessment.js') }}"></script>
<script>
    function updateLikertValue(input, questionId) {
        document.getElementById('value-' + questionId).textContent = input.value;
        updateProgress();
    }
    
    function updateProgress() {
        const form = document.getElementById('assessmentForm');
        const formData = new FormData(form);
        const responses = {};
        
        for (let [key, value] of formData.entries()) {
            if (key.startsWith('responses[')) {
                responses[key] = value;
            }
        }
        
        const totalQuestions = document.querySelectorAll('.question-item').length;
        const answered = Object.keys(responses).length;
        const percentage = totalQuestions > 0 ? (answered / totalQuestions) * 100 : 0;
        
        document.getElementById('progressBar').style.width = percentage + '%';
        document.getElementById('progressText').textContent = answered + ' of ' + totalQuestions + ' questions answered';
    }
    
    // Update progress on input
    document.getElementById('assessmentForm').addEventListener('input', updateProgress);
    document.getElementById('assessmentForm').addEventListener('change', updateProgress);
    
    // Initial progress update
    updateProgress();
</script>
@endpush
