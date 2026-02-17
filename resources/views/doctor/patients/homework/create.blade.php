@extends('layouts.app')

@section('title', 'Assign Homework - Your Mind Aid')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Patients</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('patients.show', $patient) }}">{{ $patientProfile->user->name ?? $patientProfile->full_name ?? $patient->name ?? 'Patient' }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('patients.homework.index', $patient) }}">Homework</a></li>
                    <li class="breadcrumb-item active">Assign New</li>
                </ol>
            </nav>
            
            <h2 class="mb-1">Assign Homework/Technique</h2>
            <p class="text-muted">Assign therapy technique to {{ $patientProfile->user->name ?? $patientProfile->full_name ?? $patient->name ?? 'Patient' }}</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('patients.homework.store', $patient) }}" method="POST">
                        @csrf
                        
                        <!-- Homework Type -->
                        <div class="mb-3">
                            <label for="homework_type" class="form-label fw-semibold">
                                Technique Type <span class="text-danger">*</span>
                            </label>
                            <select name="homework_type" id="homework_type" class="form-select @error('homework_type') is-invalid @enderror" required>
                                <option value="">Select technique type...</option>
                                @foreach($homeworkTypes as $value => $label)
                                    <option value="{{ $value }}" {{ old('homework_type') == $value ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('homework_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Title -->
                        <div class="mb-3">
                            <label for="title" class="form-label fw-semibold">
                                Assignment Title <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="title" id="title" 
                                   class="form-control @error('title') is-invalid @enderror" 
                                   value="{{ old('title') }}" 
                                   placeholder="e.g., Daily Mood Journaling" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea name="description" id="description" rows="3" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      placeholder="Brief description of this assignment">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Instructions -->
                        <div class="mb-3">
                            <label for="instructions" class="form-label fw-semibold">Instructions for Patient</label>
                            <textarea name="instructions" id="instructions" rows="4" 
                                      class="form-control @error('instructions') is-invalid @enderror" 
                                      placeholder="Detailed instructions on how to complete this assignment">{{ old('instructions') }}</textarea>
                            @error('instructions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Frequency -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="frequency" class="form-label fw-semibold">
                                    Frequency <span class="text-danger">*</span>
                                </label>
                                <select name="frequency" id="frequency" class="form-select @error('frequency') is-invalid @enderror" required>
                                    <option value="daily" {{ old('frequency') == 'daily' ? 'selected' : '' }}>Daily</option>
                                    <option value="weekly" {{ old('frequency') == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                    <option value="as_needed" {{ old('frequency') == 'as_needed' ? 'selected' : '' }}>As Needed</option>
                                </select>
                                @error('frequency')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="session_id" class="form-label fw-semibold">Link to Session</label>
                                <select name="session_id" id="session_id" class="form-select">
                                    <option value="">Not linked to session</option>
                                    @foreach($sessions as $session)
                                        <option value="{{ $session->id }}" {{ old('session_id') == $session->id ? 'selected' : '' }}>
                                            Session #{{ $session->id }} - {{ $session->created_at->format('M d, Y') }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Date Range -->
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label fw-semibold">
                                    Start Date <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="start_date" id="start_date" 
                                       class="form-control @error('start_date') is-invalid @enderror" 
                                       value="{{ old('start_date', date('Y-m-d')) }}" required>
                                @error('start_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="end_date" class="form-label fw-semibold">End Date</label>
                                <input type="date" name="end_date" id="end_date" 
                                       class="form-control @error('end_date') is-invalid @enderror" 
                                       value="{{ old('end_date') }}">
                                <small class="text-muted">Leave empty for ongoing assignment</small>
                                @error('end_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Contingency Points (shown when type is contingency) -->
                        <div class="mb-3" id="contingency-points-group" style="display: {{ old('homework_type') === 'contingency' ? 'block' : 'none' }};">
                            <label class="form-label fw-semibold">Contingency Scoring Points</label>
                            <p class="small text-muted mb-2">Set points for each completion choice. Leave blank to use defaults (+10, +5, -10).</p>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label small">Self action</label>
                                    <input type="number" name="contingency_self_action_points" class="form-control form-control-sm" 
                                           value="{{ old('contingency_self_action_points', 10) }}" min="-100" max="100" placeholder="10">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Others helped</label>
                                    <input type="number" name="contingency_others_help_points" class="form-control form-control-sm" 
                                           value="{{ old('contingency_others_help_points', 5) }}" min="-100" max="100" placeholder="5">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small">Not working</label>
                                    <input type="number" name="contingency_not_working_points" class="form-control form-control-sm" 
                                           value="{{ old('contingency_not_working_points', -10) }}" min="-100" max="100" placeholder="-10">
                                </div>
                            </div>
                        </div>

                        <!-- Feedback Requirements -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Feedback Requirements</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="requires_parent_feedback" 
                                       id="requires_parent_feedback" value="1" {{ old('requires_parent_feedback') ? 'checked' : '' }}>
                                <label class="form-check-label" for="requires_parent_feedback">
                                    Requires Parent Feedback
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="requires_others_feedback" 
                                       id="requires_others_feedback" value="1" {{ old('requires_others_feedback') ? 'checked' : '' }}>
                                <label class="form-check-label" for="requires_others_feedback">
                                    Requires Others/Expert Feedback
                                </label>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-lg me-2"></i>Assign Homework
                            </button>
                            <a href="{{ route('patients.homework.index', $patient) }}" class="btn btn-outline-secondary">
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
                        <i class="bi bi-info-circle me-2"></i>Assignment Tips
                    </h6>
                    <ul class="small mb-0">
                        <li>Choose the appropriate technique type for the patient's needs</li>
                        <li>Provide clear, specific instructions</li>
                        <li>Set realistic frequencies (daily for habit-building, weekly for check-ins)</li>
                        <li>Enable parent feedback for minors or family-involved therapy</li>
                        <li>Link to session for better tracking</li>
                    </ul>
                </div>
            </div>

            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="fw-semibold mb-3">Technique Types</h6>
                    <ul class="small mb-0">
                        <li><strong>Psychotherapy:</strong> CBT, DBT exercises</li>
                        <li><strong>Mood Tracking:</strong> Daily mood logging</li>
                        <li><strong>Sleep Tracking:</strong> Sleep pattern monitoring</li>
                        <li><strong>Exercise:</strong> Physical activity goals</li>
                        <li><strong>Risk Tracking:</strong> Safety monitoring</li>
                        <li><strong>Contingency:</strong> Crisis plan practice</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    var typeSelect = document.getElementById('homework_type');
    var pointsGroup = document.getElementById('contingency-points-group');
    if (typeSelect && pointsGroup) {
        function toggleContingencyPoints() {
            pointsGroup.style.display = typeSelect.value === 'contingency' ? 'block' : 'none';
        }
        typeSelect.addEventListener('change', toggleContingencyPoints);
    }
});
</script>
@endpush
@endsection
