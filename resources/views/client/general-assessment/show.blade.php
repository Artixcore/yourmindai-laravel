@extends('client.layout')

@section('title', $assessment->title . ' - Your Mind Aid')

@section('content')
<div class="mb-4">
    <a href="{{ route('client.general-assessment.index') }}" class="btn btn-sm btn-outline-secondary mb-2">
        <i class="bi bi-arrow-left me-1"></i>Back to Assessments
    </a>
    <h4 class="fw-bold mb-1">{{ $assessment->title }}</h4>
    @if($assessment->description)
        <p class="text-muted mb-0 small">{{ $assessment->description }}</p>
    @endif
</div>

<!-- Assessment Info -->
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <small class="text-muted">Assigned by</small>
                <div class="fw-semibold">{{ $assessment->assignedByDoctor->name }}</div>
            </div>
            <div>
                <small class="text-muted">Assigned on</small>
                <div class="fw-semibold">{{ $assessment->assigned_at->format('M d, Y') }}</div>
            </div>
            <div>
                <small class="text-muted">Progress</small>
                <div>
                    @php
                        $answeredCount = $assessment->responses->count();
                        $totalQuestions = $assessment->questions->count();
                        $progress = $totalQuestions > 0 ? round(($answeredCount / $totalQuestions) * 100) : 0;
                    @endphp
                    <span class="badge bg-{{ $progress === 100 ? 'success' : 'primary' }}">
                        {{ $answeredCount }}/{{ $totalQuestions }} answered
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assessment Form -->
<form action="{{ route('client.general-assessment.submit', $assessment->id) }}" method="POST">
    @csrf

    @foreach($assessment->questions as $question)
    <div class="card mb-3">
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label fw-semibold">
                    Question {{ $loop->iteration }}
                    @if($question->is_required)
                        <span class="text-danger">*</span>
                    @endif
                </label>
                <p class="mb-0">{{ $question->question_text }}</p>
            </div>

            @php
                $existingResponse = $assessment->responses->where('question_id', $question->id)->first();
            @endphp

            @if($question->question_type === 'text')
                <textarea name="responses[{{ $question->id }}]" rows="3" 
                          class="form-control" 
                          placeholder="Type your response here..."
                          {{ $question->is_required ? 'required' : '' }}>{{ $existingResponse->response_text ?? '' }}</textarea>
            
            @elseif($question->question_type === 'scale')
                <div class="d-flex justify-content-between align-items-center mb-2">
                    @for($i = 1; $i <= 10; $i++)
                        <button type="button" class="btn btn-outline-primary scale-btn" 
                                data-question="{{ $question->id }}" 
                                data-value="{{ $i }}"
                                onclick="selectScale({{ $question->id }}, {{ $i }})"
                                {{ ($existingResponse && $existingResponse->response_score == $i) ? 'class=active' : '' }}>
                            {{ $i }}
                        </button>
                    @endfor
                </div>
                <input type="hidden" name="responses[{{ $question->id }}]" 
                       id="scale_{{ $question->id }}" 
                       value="{{ $existingResponse->response_score ?? '' }}"
                       {{ $question->is_required ? 'required' : '' }}>
                <div class="text-center">
                    <small class="text-muted">1 = Not at all, 10 = Extremely</small>
                </div>
            
            @elseif($question->question_type === 'yes_no')
                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-outline-success" 
                            onclick="selectYesNo({{ $question->id }}, 'yes')">
                        Yes
                    </button>
                    <button type="button" class="btn btn-outline-danger" 
                            onclick="selectYesNo({{ $question->id }}, 'no')">
                        No
                    </button>
                </div>
                <input type="hidden" name="responses[{{ $question->id }}]" 
                       id="yesno_{{ $question->id }}"
                       value="{{ $existingResponse->response_text ?? '' }}"
                       {{ $question->is_required ? 'required' : '' }}>
            
            @elseif($question->question_type === 'multiple_choice')
                @if($question->options)
                    @foreach($question->options as $option)
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" 
                                   name="responses[{{ $question->id }}]" 
                                   id="option_{{ $question->id }}_{{ $loop->index }}"
                                   value="{{ $option }}"
                                   {{ ($existingResponse && $existingResponse->response_text === $option) ? 'checked' : '' }}
                                   {{ $question->is_required ? 'required' : '' }}>
                            <label class="form-check-label" for="option_{{ $question->id }}_{{ $loop->index }}">
                                {{ $option }}
                            </label>
                        </div>
                    @endforeach
                @endif
            @endif
        </div>
    </div>
    @endforeach

    <!-- Submit Button -->
    <div class="card border-success">
        <div class="card-body">
            <button type="submit" class="btn btn-success w-100">
                <i class="bi bi-check-circle me-2"></i>Complete Assessment
            </button>
        </div>
    </div>
</form>
@endsection

<script>
function selectScale(questionId, value) {
    // Remove active from all buttons for this question
    document.querySelectorAll(`[data-question="${questionId}"]`).forEach(btn => {
        btn.classList.remove('active', 'btn-primary');
        btn.classList.add('btn-outline-primary');
    });
    
    // Activate selected button
    const selectedBtn = document.querySelector(`[data-question="${questionId}"][data-value="${value}"]`);
    selectedBtn.classList.add('active', 'btn-primary');
    selectedBtn.classList.remove('btn-outline-primary');
    
    // Set hidden input
    document.getElementById(`scale_${questionId}`).value = value;
}

function selectYesNo(questionId, value) {
    document.getElementById(`yesno_${questionId}`).value = value;
    
    // Visual feedback
    const container = document.getElementById(`yesno_${questionId}`).previousElementSibling;
    const buttons = container.querySelectorAll('button');
    buttons.forEach(btn => {
        btn.classList.remove('btn-success', 'btn-danger');
        btn.classList.add(btn.textContent.trim().toLowerCase() === value ? 
            (value === 'yes' ? 'btn-success' : 'btn-danger') : 
            (value === 'yes' ? 'btn-outline-danger' : 'btn-outline-success'));
    });
}
</script>

<style>
.scale-btn {
    width: 35px;
    height: 35px;
    padding: 0;
    font-size: 0.875rem;
}
.scale-btn.active {
    transform: scale(1.1);
}
</style>
