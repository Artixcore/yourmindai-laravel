@extends('client.layout')

@section('title', 'Assessment - Your Mind Aid')

@section('content')
@if($assessment->status === 'completed')
    @include('client.assessments.result')
@else
<div class="mb-4">
    <h4 class="fw-bold mb-1">{{ $assessment->scale->name ?? 'Assessment' }}</h4>
    @if($assessment->scale && $assessment->scale->description)
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
                @if($assessment->scale && $assessment->scale->questions && is_array($assessment->scale->questions) && count($assessment->scale->questions) > 0)
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
<script>
(function() {
    'use strict';
    
    /**
     * Psychometric Assessment Form Handler
     * Handles dynamic form rendering, validation, and draft saving
     */
    
    let formInitialized = false;
    
    /**
     * Debounce function
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    /**
     * Update Likert scale value display
     */
    function updateLikertValue(input, questionId) {
        try {
            const valueElement = document.getElementById('value-' + questionId);
            if (valueElement && input) {
                valueElement.textContent = input.value;
                updateProgress();
            }
        } catch (e) {
            // Silently fail
        }
    }
    
    /**
     * Update progress bar and text
     */
    function updateProgress() {
        try {
            const form = document.getElementById('assessmentForm');
            if (!form) return;
            
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
            
            const progressBar = document.getElementById('progressBar');
            const progressText = document.getElementById('progressText');
            
            if (progressBar) {
                progressBar.style.width = percentage + '%';
            }
            if (progressText) {
                progressText.textContent = answered + ' of ' + totalQuestions + ' questions answered';
            }
        } catch (e) {
            // Silently fail
        }
    }
    
    /**
     * Save draft responses to localStorage
     */
    function saveDraft(assessmentId) {
        try {
            const form = document.getElementById('assessmentForm');
            if (!form || !assessmentId) return;
            
            const formData = new FormData(form);
            const responses = {};
            
            for (let [key, value] of formData.entries()) {
                if (key.startsWith('responses[')) {
                    responses[key] = value;
                }
            }
            
            localStorage.setItem(`assessment_draft_${assessmentId}`, JSON.stringify(responses));
        } catch (e) {
            // Silently fail if localStorage is not available
        }
    }
    
    /**
     * Load draft responses from localStorage
     */
    function loadDraft(assessmentId) {
        try {
            if (!assessmentId) return;
            
            const draftData = localStorage.getItem(`assessment_draft_${assessmentId}`);
            if (!draftData) return;
            
            const responses = JSON.parse(draftData);
            const form = document.getElementById('assessmentForm');
            if (!form) return;
            
            for (let [key, value] of Object.entries(responses)) {
                const input = form.querySelector(`[name="${key}"]`);
                if (input) {
                    if (input.type === 'radio') {
                        const radio = form.querySelector(`[name="${key}"][value="${value}"]`);
                        if (radio) radio.checked = true;
                    } else {
                        input.value = value;
                    }
                }
            }
            
            // Update progress after loading draft
            updateProgress();
        } catch (e) {
            // Silently fail - corrupted or invalid draft data
        }
    }
    
    /**
     * Clear draft responses
     */
    function clearDraft(assessmentId) {
        try {
            if (assessmentId) {
                localStorage.removeItem(`assessment_draft_${assessmentId}`);
            }
        } catch (e) {
            // Silently fail
        }
    }
    
    /**
     * Validate assessment form before submission
     */
    function validateAssessmentForm() {
        try {
            const form = document.getElementById('assessmentForm');
            if (!form) return false;
            
            const questions = form.querySelectorAll('.question-item');
            let allAnswered = true;
            
            questions.forEach((question) => {
                const inputs = question.querySelectorAll('input[required], textarea[required]');
                let questionAnswered = false;
                
                inputs.forEach(input => {
                    if (input.type === 'radio') {
                        if (input.checked) questionAnswered = true;
                    } else if (input.type === 'checkbox') {
                        if (input.checked) questionAnswered = true;
                    } else {
                        if (input.value.trim() !== '') questionAnswered = true;
                    }
                });
                
                if (!questionAnswered) {
                    allAnswered = false;
                    question.classList.add('border-danger');
                } else {
                    question.classList.remove('border-danger');
                }
            });
            
            return allAnswered;
        } catch (e) {
            return false;
        }
    }
    
    /**
     * Initialize assessment form
     */
    function initAssessmentForm() {
        if (formInitialized) return;
        
        try {
            const form = document.getElementById('assessmentForm');
            if (!form) return;
            
            // Get assessment ID from form action
            const actionMatch = form.getAttribute('action');
            const assessmentId = actionMatch ? actionMatch.match(/\/(\d+)\/complete/)?.[1] : null;
            
            if (assessmentId) {
                // Load saved draft
                loadDraft(assessmentId);
                
                // Save draft on input (debounced)
                form.addEventListener('input', debounce(() => {
                    saveDraft(assessmentId);
                }, 1000));
                
                // Clear draft on successful submission
                form.addEventListener('submit', function(e) {
                    if (validateAssessmentForm()) {
                        clearDraft(assessmentId);
                    } else {
                        e.preventDefault();
                        // Show error message using Bootstrap alert
                        const errorAlert = document.createElement('div');
                        errorAlert.className = 'alert alert-danger alert-dismissible fade show';
                        errorAlert.innerHTML = `
                            <strong>Please complete all questions:</strong> You must answer all questions before submitting the assessment.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        `;
                        const form = document.getElementById('assessmentForm');
                        if (form) {
                            form.insertBefore(errorAlert, form.firstChild);
                            // Scroll to top to show error
                            errorAlert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        }
                    }
                });
            }
            
            // Update progress on input and change
            form.addEventListener('input', updateProgress);
            form.addEventListener('change', updateProgress);
            
            // Initial progress update
            updateProgress();
            
            formInitialized = true;
        } catch (e) {
            // Silently fail
        }
    }
    
    // Make updateLikertValue globally accessible for inline oninput handlers
    window.updateLikertValue = updateLikertValue;
    
    // Initialize on page load
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initAssessmentForm);
    } else {
        initAssessmentForm();
    }
})();
</script>
@endpush
