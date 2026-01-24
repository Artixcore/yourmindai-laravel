@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">New Referral</h2>
            <p class="text-muted mb-0">Patient: {{ $patient->user->name }}</p>
        </div>
        <a href="{{ route('doctor.patients.referrals.index', $patient) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Referrals
        </a>
    </div>

    <form action="{{ route('doctor.patients.referrals.store', $patient) }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0">Referral Information</h5>
                    </div>
                    <div class="card-body">
                        <!-- Specialist Selection -->
                        <div class="mb-3">
                            <label for="referred_to" class="form-label">Refer To (Optional)</label>
                            <select class="form-select @error('referred_to') is-invalid @enderror" 
                                    id="referred_to" 
                                    name="referred_to">
                                <option value="">-- Select Specialist (Optional) --</option>
                                @foreach($specialists as $specialist)
                                    <option value="{{ $specialist->id }}" {{ old('referred_to') == $specialist->id ? 'selected' : '' }}>
                                        {{ $specialist->name }}{{ $specialist->specialty ? ' - ' . $specialist->specialty : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">Leave empty if referring to a specific specialty without a specific doctor</small>
                            @error('referred_to')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Specialty Needed -->
                        <div class="mb-3">
                            <label for="specialty_needed" class="form-label">Specialty Needed <span class="text-danger">*</span></label>
                            <select class="form-select @error('specialty_needed') is-invalid @enderror" 
                                    id="specialty_needed" 
                                    name="specialty_needed" 
                                    required>
                                <option value="">-- Select Specialty --</option>
                                @foreach($specialties as $key => $label)
                                    <option value="{{ $key }}" {{ old('specialty_needed') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('specialty_needed')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Reason for Referral -->
                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason for Referral <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('reason') is-invalid @enderror" 
                                      id="reason" 
                                      name="reason" 
                                      rows="4" 
                                      required
                                      placeholder="Describe why this patient needs specialist consultation...">{{ old('reason') }}</textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Patient History Summary -->
                        <div class="mb-3">
                            <label for="patient_history_summary" class="form-label">Patient History Summary</label>
                            <textarea class="form-control @error('patient_history_summary') is-invalid @enderror" 
                                      id="patient_history_summary" 
                                      name="patient_history_summary" 
                                      rows="5"
                                      placeholder="Provide relevant medical history, diagnosis, current treatment...">{{ old('patient_history_summary') }}</textarea>
                            @error('patient_history_summary')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Recommendations -->
                        <div class="mb-3">
                            <label for="recommendations" class="form-label">Recommendations</label>
                            <textarea class="form-control @error('recommendations') is-invalid @enderror" 
                                      id="recommendations" 
                                      name="recommendations" 
                                      rows="4"
                                      placeholder="Suggest specific assessments, treatments, or areas of focus...">{{ old('recommendations') }}</textarea>
                            @error('recommendations')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Report File Upload -->
                        <div class="mb-3">
                            <label for="report_file" class="form-label">Attach Report (Optional)</label>
                            <input type="file" 
                                   class="form-control @error('report_file') is-invalid @enderror" 
                                   id="report_file" 
                                   name="report_file"
                                   accept=".pdf,.doc,.docx">
                            <small class="text-muted">Accepted formats: PDF, DOC, DOCX (Max: 10MB)</small>
                            @error('report_file')
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
                            <i class="bi bi-info-circle me-2"></i>Referral Guidelines
                        </h6>
                        
                        <div class="mb-3">
                            <strong>When to Refer:</strong>
                            <ul class="small mb-0 mt-1">
                                <li>Specialized expertise needed</li>
                                <li>Complex cases requiring consultation</li>
                                <li>Second opinion requested</li>
                                <li>Crisis intervention needed</li>
                            </ul>
                        </div>

                        <div>
                            <strong>Include in Referral:</strong>
                            <ul class="small mb-0 mt-1">
                                <li>Clear reason for referral</li>
                                <li>Relevant medical history</li>
                                <li>Current medications</li>
                                <li>Assessment results</li>
                                <li>Treatment goals</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Patient Information -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h6 class="mb-0">Patient Information</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <small class="text-muted">Name:</small>
                            <p class="mb-0">{{ $patient->user->name }}</p>
                        </div>
                        <div class="mb-2">
                            <small class="text-muted">Email:</small>
                            <p class="mb-0">{{ $patient->user->email }}</p>
                        </div>
                        @if($patient->phone)
                            <div class="mb-2">
                                <small class="text-muted">Phone:</small>
                                <p class="mb-0">{{ $patient->phone }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Actions -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="bi bi-send me-2"></i>Send Referral
                        </button>
                        <a href="{{ route('doctor.patients.referrals.index', $patient) }}" class="btn btn-outline-secondary w-100">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
