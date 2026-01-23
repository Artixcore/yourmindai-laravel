@extends('layouts.app')

@section('title', 'Create Psychometric Scale')

@section('content')
<!-- Page Header -->
<div class="mb-4">
    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('admin.dashboard')],
        ['label' => 'Psychometric Scales', 'url' => route('psychometric-scales.index')],
        ['label' => 'Create']
    ]" />
    <h1 class="h3 mb-1 fw-semibold">Create Psychometric Scale</h1>
    <p class="text-muted mb-0">Define a new assessment scale with questions and scoring rules</p>
</div>

<form method="POST" action="{{ route('psychometric-scales.store') }}" id="scaleForm">
    @csrf
    
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0">
            <h5 class="mb-0 fw-semibold">Basic Information</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label">Scale Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" value="{{ old('name') }}" required placeholder="e.g., PHQ-9, GAD-7">
                </div>
                
                <div class="col-12 col-md-6">
                    <label class="form-label">Category</label>
                    <input type="text" class="form-control" name="category" value="{{ old('category') }}" placeholder="e.g., Depression, Anxiety">
                </div>
                
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3" placeholder="Brief description of the scale">{{ old('description') }}</textarea>
                </div>
                
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Active (available for use)
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-semibold">Questions</h5>
            <button type="button" class="btn btn-sm btn-primary" onclick="addQuestion()">
                <i class="bi bi-plus-circle me-1"></i>Add Question
            </button>
        </div>
        <div class="card-body">
            <div id="questionsContainer">
                <!-- Questions will be added here dynamically -->
            </div>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0">
            <h5 class="mb-0 fw-semibold">Scoring Rules</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Scoring Type</label>
                <select class="form-select" name="scoring_rules[type]" id="scoringType" required>
                    <option value="sum">Simple Sum</option>
                    <option value="weighted">Weighted</option>
                </select>
            </div>
            <div id="scoringRulesContainer">
                <!-- Scoring rules will be configured here -->
            </div>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0">
            <h5 class="mb-0 fw-semibold">Interpretation Rules</h5>
        </div>
        <div class="card-body">
            <div id="interpretationRulesContainer">
                <div class="interpretation-rule-item mb-3 p-3 border rounded">
                    <div class="row g-3">
                        <div class="col-12 col-md-4">
                            <label class="form-label">Min Score</label>
                            <input type="number" class="form-control" name="interpretation_rules[0][min]" value="0" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">Max Score</label>
                            <input type="number" class="form-control" name="interpretation_rules[0][max]" value="100" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label">Interpretation</label>
                            <input type="text" class="form-control" name="interpretation_rules[0][interpretation]" placeholder="e.g., Mild, Moderate, Severe" required>
                        </div>
                    </div>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addInterpretationRule()">
                <i class="bi bi-plus-circle me-1"></i>Add Rule
            </button>
        </div>
    </div>
    
    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('psychometric-scales.index') }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Create Scale</button>
    </div>
</form>

@push('scripts')
<script>
'use strict';

// Initialize counters
let questionCount = 0;
let interpretationRuleCount = 1;

// Make functions globally accessible immediately
window.addQuestion = function() {
    try {
        const container = document.getElementById('questionsContainer');
        if (!container) {
            console.error('Questions container not found');
            return;
        }
        
        const questionDiv = document.createElement('div');
        questionDiv.className = 'question-item mb-3 p-3 border rounded';
        questionDiv.innerHTML = `
            <div class="d-flex justify-content-between align-items-start mb-2">
                <strong>Question ${questionCount + 1}</strong>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.question-item').remove()">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            <div class="mb-3">
                <label class="form-label">Question Text <span class="text-danger">*</span></label>
                <textarea class="form-control" name="questions[${questionCount}][text]" rows="2" required placeholder="Enter question text"></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Question Type <span class="text-danger">*</span></label>
                <select class="form-select" name="questions[${questionCount}][type]" required onchange="updateQuestionOptions(this, ${questionCount})">
                    <option value="likert">Likert Scale</option>
                    <option value="scale">Numeric Scale</option>
                    <option value="multiple_choice">Multiple Choice</option>
                    <option value="text">Text Input</option>
                    <option value="textarea">Text Area</option>
                    <option value="number">Number Input</option>
                </select>
            </div>
            <div class="question-options-${questionCount}">
                <!-- Options will be added here based on type -->
            </div>
            <input type="hidden" name="questions[${questionCount}][id]" value="${questionCount}">
        `;
        container.appendChild(questionDiv);
        questionCount++;
    } catch (e) {
        console.error('Error adding question:', e);
    }
};

window.updateQuestionOptions = function(select, index) {
    try {
        if (!select) return;
        
        const type = select.value;
        const optionsContainer = document.querySelector(`.question-options-${index}`);
        if (!optionsContainer) return;
        
        if (type === 'likert' || type === 'scale') {
            optionsContainer.innerHTML = `
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label">Min Value</label>
                        <input type="number" class="form-control" name="questions[${index}][min]" value="0">
                    </div>
                    <div class="col-6">
                        <label class="form-label">Max Value</label>
                        <input type="number" class="form-control" name="questions[${index}][max]" value="10">
                    </div>
                </div>
            `;
        } else if (type === 'multiple_choice') {
            optionsContainer.innerHTML = `
                <label class="form-label">Options (one per line)</label>
                <textarea class="form-control" name="questions[${index}][options_text]" rows="4" placeholder="Option 1&#10;Option 2&#10;Option 3"></textarea>
            `;
        } else {
            optionsContainer.innerHTML = '';
        }
    } catch (e) {
        console.error('Error updating question options:', e);
    }
};

window.addInterpretationRule = function() {
    try {
        const container = document.getElementById('interpretationRulesContainer');
        if (!container) {
            console.error('Interpretation rules container not found');
            return;
        }
        
        const ruleDiv = document.createElement('div');
        ruleDiv.className = 'interpretation-rule-item mb-3 p-3 border rounded';
        ruleDiv.innerHTML = `
            <div class="d-flex justify-content-between align-items-start mb-2">
                <strong>Rule ${interpretationRuleCount + 1}</strong>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.interpretation-rule-item').remove()">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <label class="form-label">Min Score</label>
                    <input type="number" class="form-control" name="interpretation_rules[${interpretationRuleCount}][min]" required>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">Max Score</label>
                    <input type="number" class="form-control" name="interpretation_rules[${interpretationRuleCount}][max]" required>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label">Interpretation</label>
                    <input type="text" class="form-control" name="interpretation_rules[${interpretationRuleCount}][interpretation]" required>
                </div>
            </div>
        `;
        container.appendChild(ruleDiv);
        interpretationRuleCount++;
    } catch (e) {
        console.error('Error adding interpretation rule:', e);
    }
};

// Initialize scoring rules on form submit
document.addEventListener('DOMContentLoaded', function() {
    // Add first question on page load
    if (typeof window.addQuestion === 'function') {
        window.addQuestion();
    }
    
    // Ensure scoring rules are set before form submission
    const form = document.getElementById('scaleForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const scoringType = document.getElementById('scoringType');
            // Just ensure the select has a value - no need to create hidden input
            // The select element already has name="scoring_rules[type]" and will submit correctly
            if (scoringType && !scoringType.value) {
                scoringType.value = 'sum';
            }
        });
    }
});
</script>
@endpush
@endsection
