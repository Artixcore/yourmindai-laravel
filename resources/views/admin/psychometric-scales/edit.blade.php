@extends('layouts.app')

@section('title', 'Edit Psychometric Scale')

@section('content')
<div class="mb-4">
    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('admin.dashboard')],
        ['label' => 'Psychometric Scales', 'url' => route('psychometric-scales.index')],
        ['label' => 'Edit']
    ]" />
    <h1 class="h3 mb-1 fw-semibold">Edit Psychometric Scale</h1>
    <p class="text-muted mb-0">Update scale information and questions</p>
</div>

<form method="POST" action="{{ route('psychometric-scales.update', $psychometricScale) }}">
    @csrf
    @method('PUT')
    
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0">
            <h5 class="mb-0 fw-semibold">Basic Information</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12 col-md-6">
                    <label class="form-label">Scale Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="name" value="{{ old('name', $psychometricScale->name) }}" required>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label">Category</label>
                    <input type="text" class="form-control" name="category" value="{{ old('category', $psychometricScale->category) }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="3">{{ old('description', $psychometricScale->description) }}</textarea>
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $psychometricScale->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0">
            <h5 class="mb-0 fw-semibold">Questions</h5>
        </div>
        <div class="card-body">
            <div id="questionsContainer">
                @if($psychometricScale->questions)
                    @foreach($psychometricScale->questions as $index => $question)
                    <div class="question-item mb-3 p-3 border rounded">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <strong>Question {{ $index + 1 }}</strong>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.question-item').remove()">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Question Text <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="questions[{{ $index }}][text]" rows="2" required>{{ $question['text'] ?? '' }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Question Type <span class="text-danger">*</span></label>
                            <select class="form-select" name="questions[{{ $index }}][type]" required>
                                <option value="likert" {{ ($question['type'] ?? '') == 'likert' ? 'selected' : '' }}>Likert Scale</option>
                                <option value="scale" {{ ($question['type'] ?? '') == 'scale' ? 'selected' : '' }}>Numeric Scale</option>
                                <option value="multiple_choice" {{ ($question['type'] ?? '') == 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                                <option value="text" {{ ($question['type'] ?? '') == 'text' ? 'selected' : '' }}>Text Input</option>
                                <option value="textarea" {{ ($question['type'] ?? '') == 'textarea' ? 'selected' : '' }}>Text Area</option>
                                <option value="number" {{ ($question['type'] ?? '') == 'number' ? 'selected' : '' }}>Number Input</option>
                            </select>
                        </div>
                        @if(($question['type'] ?? '') === 'likert' || ($question['type'] ?? '') === 'scale')
                        <div class="row g-3">
                            <div class="col-6">
                                <label class="form-label">Min Value</label>
                                <input type="number" class="form-control" name="questions[{{ $index }}][min]" value="{{ $question['min'] ?? 0 }}">
                            </div>
                            <div class="col-6">
                                <label class="form-label">Max Value</label>
                                <input type="number" class="form-control" name="questions[{{ $index }}][max]" value="{{ $question['max'] ?? 10 }}">
                            </div>
                        </div>
                        @endif
                        <input type="hidden" name="questions[{{ $index }}][id]" value="{{ $question['id'] ?? $index }}">
                    </div>
                    @endforeach
                @endif
            </div>
            <button type="button" class="btn btn-sm btn-primary" onclick="addQuestion()">
                <i class="bi bi-plus-circle me-1"></i>Add Question
            </button>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0">
            <h5 class="mb-0 fw-semibold">Scoring Rules</h5>
        </div>
        <div class="card-body">
            <div class="mb-3">
                <label class="form-label">Scoring Type</label>
                <select class="form-select" name="scoring_rules[type]" required>
                    <option value="sum" {{ ($psychometricScale->scoring_rules['type'] ?? 'sum') == 'sum' ? 'selected' : '' }}>Simple Sum</option>
                    <option value="weighted" {{ ($psychometricScale->scoring_rules['type'] ?? '') == 'weighted' ? 'selected' : '' }}>Weighted</option>
                </select>
            </div>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0">
            <h5 class="mb-0 fw-semibold">Interpretation Rules</h5>
        </div>
        <div class="card-body">
            <div id="interpretationRulesContainer">
                @if($psychometricScale->interpretation_rules)
                    @foreach($psychometricScale->interpretation_rules as $index => $rule)
                    <div class="interpretation-rule-item mb-3 p-3 border rounded">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <strong>Rule {{ $index + 1 }}</strong>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.interpretation-rule-item').remove()">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                        <div class="row g-3">
                            <div class="col-12 col-md-4">
                                <label class="form-label">Min Score</label>
                                <input type="number" class="form-control" name="interpretation_rules[{{ $index }}][min]" value="{{ $rule['min'] ?? 0 }}" required>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">Max Score</label>
                                <input type="number" class="form-control" name="interpretation_rules[{{ $index }}][max]" value="{{ $rule['max'] ?? 100 }}" required>
                            </div>
                            <div class="col-12 col-md-4">
                                <label class="form-label">Interpretation</label>
                                <input type="text" class="form-control" name="interpretation_rules[{{ $index }}][interpretation]" value="{{ $rule['interpretation'] ?? '' }}" required>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addInterpretationRule()">
                <i class="bi bi-plus-circle me-1"></i>Add Rule
            </button>
        </div>
    </div>
    
    <div class="d-flex justify-content-end gap-2">
        <a href="{{ route('psychometric-scales.index') }}" class="btn btn-outline-secondary">Cancel</a>
        <button type="submit" class="btn btn-primary">Update Scale</button>
    </div>
</form>

@push('scripts')
<script>
(function() {
    'use strict';
    
    let questionCount = {{ count($psychometricScale->questions ?? []) }};
    let interpretationRuleCount = {{ count($psychometricScale->interpretation_rules ?? []) }};
    
    function addQuestion() {
        try {
            const container = document.getElementById('questionsContainer');
            if (!container) return;
            
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
                    <textarea class="form-control" name="questions[${questionCount}][text]" rows="2" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Question Type <span class="text-danger">*</span></label>
                    <select class="form-select" name="questions[${questionCount}][type]" required>
                        <option value="likert">Likert Scale</option>
                        <option value="scale">Numeric Scale</option>
                        <option value="multiple_choice">Multiple Choice</option>
                        <option value="text">Text Input</option>
                        <option value="textarea">Text Area</option>
                        <option value="number">Number Input</option>
                    </select>
                </div>
                <input type="hidden" name="questions[${questionCount}][id]" value="${questionCount}">
            `;
            container.appendChild(questionDiv);
            questionCount++;
        } catch (e) {
            // Silently fail
        }
    }
    
    function addInterpretationRule() {
        try {
            const container = document.getElementById('interpretationRulesContainer');
            if (!container) return;
            
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
            // Silently fail
        }
    }
    
    // Make functions globally accessible for inline handlers
    window.addQuestion = addQuestion;
    window.addInterpretationRule = addInterpretationRule;
})();
</script>
@endpush
@endsection
