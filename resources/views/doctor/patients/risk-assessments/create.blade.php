@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">New Risk Assessment</h2>
            <p class="text-muted mb-0">Patient: {{ $patient->user->name }}</p>
        </div>
        <a href="{{ route('doctor.patients.risk-assessments.index', $patient) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Assessments
        </a>
    </div>

    <form action="{{ route('doctor.patients.risk-assessments.store', $patient) }}" method="POST">
        @csrf
        
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0">Assessment Details</h5>
                    </div>
                    <div class="card-body">
                        <!-- Assessment Date -->
                        <div class="mb-3">
                            <label for="assessment_date" class="form-label">Assessment Date <span class="text-danger">*</span></label>
                            <input type="date" 
                                   class="form-control @error('assessment_date') is-invalid @enderror" 
                                   id="assessment_date" 
                                   name="assessment_date" 
                                   value="{{ old('assessment_date', date('Y-m-d')) }}" 
                                   required>
                            @error('assessment_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Risk Level -->
                        <div class="mb-3">
                            <label class="form-label">Risk Level <span class="text-danger">*</span></label>
                            <div class="d-flex gap-2 flex-wrap">
                                <div class="form-check form-check-inline p-3 border rounded flex-fill" style="min-width: 150px;">
                                    <input class="form-check-input" type="radio" name="risk_level" id="risk_none" value="none" {{ old('risk_level') == 'none' ? 'checked' : '' }} required>
                                    <label class="form-check-label w-100" for="risk_none">
                                        <i class="bi bi-shield-check text-success me-1"></i>
                                        <strong>None</strong><br>
                                        <small class="text-muted">No risk identified</small>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline p-3 border rounded flex-fill" style="min-width: 150px;">
                                    <input class="form-check-input" type="radio" name="risk_level" id="risk_low" value="low" {{ old('risk_level') == 'low' ? 'checked' : '' }} required>
                                    <label class="form-check-label w-100" for="risk_low">
                                        <i class="bi bi-shield text-info me-1"></i>
                                        <strong>Low</strong><br>
                                        <small class="text-muted">Minimal concern</small>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline p-3 border rounded flex-fill" style="min-width: 150px;">
                                    <input class="form-check-input" type="radio" name="risk_level" id="risk_moderate" value="moderate" {{ old('risk_level') == 'moderate' ? 'checked' : '' }} required>
                                    <label class="form-check-label w-100" for="risk_moderate">
                                        <i class="bi bi-shield-exclamation text-warning me-1"></i>
                                        <strong>Moderate</strong><br>
                                        <small class="text-muted">Needs monitoring</small>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline p-3 border rounded flex-fill" style="min-width: 150px;">
                                    <input class="form-check-input" type="radio" name="risk_level" id="risk_high" value="high" {{ old('risk_level') == 'high' ? 'checked' : '' }} required>
                                    <label class="form-check-label w-100" for="risk_high">
                                        <i class="bi bi-exclamation-triangle text-danger me-1"></i>
                                        <strong>High</strong><br>
                                        <small class="text-muted">Immediate attention</small>
                                    </label>
                                </div>
                                <div class="form-check form-check-inline p-3 border rounded flex-fill" style="min-width: 150px;">
                                    <input class="form-check-input" type="radio" name="risk_level" id="risk_critical" value="critical" {{ old('risk_level') == 'critical' ? 'checked' : '' }} required>
                                    <label class="form-check-label w-100" for="risk_critical">
                                        <i class="bi bi-exclamation-octagon text-dark me-1"></i>
                                        <strong>Critical</strong><br>
                                        <small class="text-muted">Crisis intervention</small>
                                    </label>
                                </div>
                            </div>
                            @error('risk_level')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Risk Factors -->
                        <div class="mb-3">
                            <label class="form-label">Risk Factors (select all that apply)</label>
                            <div class="row g-2">
                                @foreach($riskFactors as $key => $label)
                                    <div class="col-md-6">
                                        <div class="form-check p-2 border rounded">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   name="risk_factors[]" 
                                                   value="{{ $key }}" 
                                                   id="factor_{{ $key }}"
                                                   {{ in_array($key, old('risk_factors', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="factor_{{ $key }}">
                                                {{ $label }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Assessment Notes -->
                        <div class="mb-3">
                            <label for="assessment_notes" class="form-label">Assessment Notes</label>
                            <textarea class="form-control @error('assessment_notes') is-invalid @enderror" 
                                      id="assessment_notes" 
                                      name="assessment_notes" 
                                      rows="4"
                                      placeholder="Document your clinical observations, patient statements, behavioral indicators...">{{ old('assessment_notes') }}</textarea>
                            @error('assessment_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Intervention Plan -->
                        <div class="mb-3">
                            <label for="intervention_plan" class="form-label">Intervention Plan</label>
                            <textarea class="form-control @error('intervention_plan') is-invalid @enderror" 
                                      id="intervention_plan" 
                                      name="intervention_plan" 
                                      rows="4"
                                      placeholder="Outline safety measures, treatment modifications, follow-up schedule, emergency contacts...">{{ old('intervention_plan') }}</textarea>
                            @error('intervention_plan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Help Card -->
                <div class="card border-0 shadow-sm mb-4 bg-light">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">
                            <i class="bi bi-info-circle me-2"></i>Risk Level Guidelines
                        </h6>
                        
                        <div class="mb-3">
                            <strong class="text-success">None:</strong>
                            <small class="d-block text-muted">No current risk factors or concerns identified.</small>
                        </div>
                        
                        <div class="mb-3">
                            <strong class="text-info">Low:</strong>
                            <small class="d-block text-muted">Minimal risk with protective factors in place.</small>
                        </div>
                        
                        <div class="mb-3">
                            <strong class="text-warning">Moderate:</strong>
                            <small class="d-block text-muted">Some risk factors present, requires monitoring and intervention.</small>
                        </div>
                        
                        <div class="mb-3">
                            <strong class="text-danger">High:</strong>
                            <small class="d-block text-muted">Significant risk requiring immediate attention and intervention.</small>
                        </div>
                        
                        <div>
                            <strong class="text-dark">Critical:</strong>
                            <small class="d-block text-muted">Imminent danger, crisis intervention needed immediately.</small>
                        </div>

                        <hr class="my-3">

                        <div class="alert alert-warning mb-0">
                            <small>
                                <i class="bi bi-bell me-1"></i>
                                <strong>Note:</strong> High and Critical risk levels will automatically trigger alert notifications.
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-save me-2"></i>Save Assessment
                        </button>
                        <a href="{{ route('doctor.patients.risk-assessments.index', $patient) }}" 
                           class="btn btn-outline-secondary w-100">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
