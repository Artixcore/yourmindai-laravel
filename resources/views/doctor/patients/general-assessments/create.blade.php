@extends('layouts.app')

@section('title', 'Create General Assessment - Your Mind Aid')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Patients</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('patients.show', $patient) }}">{{ $patientProfile->user->name ?? $patientProfile->full_name ?? $patient->name ?? 'Patient' }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('patients.general-assessments.index', $patient) }}">General Assessments</a></li>
                    <li class="breadcrumb-item active">Create New</li>
                </ol>
            </nav>
            
            <h2 class="mb-1">Create General Assessment</h2>
            <p class="text-muted">Create custom assessment for {{ $patientProfile->user->name ?? $patientProfile->full_name ?? $patient->name ?? 'Patient' }}</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('patients.general-assessments.store', $patient) }}" method="POST" id="assessmentForm">
                        @csrf
                        
                        <!-- Assessment Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label fw-semibold">
                                Assessment Title <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="title" id="title" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   value="{{ old('title') }}" 
                                   placeholder="e.g., Initial Clinical Interview" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea name="description" id="description" rows="3" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      placeholder="Purpose and context of this assessment">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Questions Section -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="mb-0">Assessment Questions</h5>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="addQuestionBtn">
                                    <i class="bi bi-plus-lg me-1"></i>Add Question
                                </button>
                            </div>

                            <div id="questionsContainer">
                                <!-- Questions will be added here dynamically -->
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-2"></i>Create & Assign Assessment
                            </button>
                            <a href="{{ route('patients.general-assessments.index', $patient) }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Help Sidebar -->
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3">
                        <i class="bi bi-lightbulb me-2"></i>Assessment Guidelines
                    </h6>
                    <ul class="small mb-0">
                        <li>Start with open-ended questions to encourage detailed responses</li>
                        <li>Use scale questions (1-10) for measuring intensity</li>
                        <li>Include yes/no questions for quick screening</li>
                        <li>Organize questions logically (demographics, symptoms, history)</li>
                        <li>Mark critical questions as required</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let questionIndex = 0;

document.getElementById('addQuestionBtn').addEventListener('click', function() {
    addQuestion();
});

function addQuestion() {
    const container = document.getElementById('questionsContainer');
    const questionDiv = document.createElement('div');
    questionDiv.className = 'card mb-3 question-card';
    questionDiv.dataset.index = questionIndex;
    
    questionDiv.innerHTML = `
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <h6 class="mb-0">Question ${questionIndex + 1}</h6>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeQuestion(${questionIndex})">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Question Text <span class="text-danger">*</span></label>
                <input type="text" name="questions[${questionIndex}][question_text]" 
                       class="form-control" placeholder="Enter your question" required>
            </div>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Question Type <span class="text-danger">*</span></label>
                    <select name="questions[${questionIndex}][question_type]" class="form-select" required>
                        <option value="text">Text Response</option>
                        <option value="scale">Scale (1-10)</option>
                        <option value="yes_no">Yes/No</option>
                        <option value="multiple_choice">Multiple Choice</option>
                    </select>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Required?</label>
                    <div class="form-check form-switch">
                        <input type="checkbox" name="questions[${questionIndex}][is_required]" 
                               class="form-check-input" value="1">
                        <label class="form-check-label">Required</label>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.appendChild(questionDiv);
    questionIndex++;
}

function removeQuestion(index) {
    const questionCard = document.querySelector(`[data-index="${index}"]`);
    if (questionCard) {
        questionCard.remove();
    }
}

// Add first question on page load
document.addEventListener('DOMContentLoaded', function() {
    addQuestion();
});
</script>
@endsection
