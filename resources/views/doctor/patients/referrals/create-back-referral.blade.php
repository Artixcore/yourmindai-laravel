@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Create Back-Referral</h2>
            <p class="text-muted mb-0">Return patient to primary care with consultation results</p>
        </div>
        <a href="{{ route('doctor.patients.referrals.show', [$referral->patient_id, $referral]) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Referral
        </a>
    </div>

    <form action="{{ route('doctor.referrals.back-referral.store', $referral) }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4 border-start border-success border-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0">Original Referral Information</h5>
                    </div>
                    <div class="card-body bg-light">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <small class="text-muted">Referred By:</small>
                                <p class="mb-0 fw-bold">{{ $referral->referredBy->name }}</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <small class="text-muted">Patient:</small>
                                <p class="mb-0 fw-bold">{{ $referral->patient->user->name }}</p>
                            </div>
                            <div class="col-12">
                                <small class="text-muted">Original Reason:</small>
                                <p class="mb-0">{{ $referral->reason }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0">Back-Referral Details</h5>
                    </div>
                    <div class="card-body">
                        <!-- Consultation Summary -->
                        <div class="mb-3">
                            <label for="reason" class="form-label">Consultation Summary <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('reason') is-invalid @enderror" 
                                      id="reason" 
                                      name="reason" 
                                      rows="4" 
                                      required
                                      placeholder="Summarize the consultation findings and assessment...">{{ old('reason') }}</textarea>
                            <small class="text-muted">Describe what was done during the consultation and key findings</small>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Clinical Findings -->
                        <div class="mb-3">
                            <label for="patient_history_summary" class="form-label">Clinical Findings & Assessment</label>
                            <textarea class="form-control @error('patient_history_summary') is-invalid @enderror" 
                                      id="patient_history_summary" 
                                      name="patient_history_summary" 
                                      rows="5"
                                      placeholder="Document diagnostic findings, test results, clinical observations...">{{ old('patient_history_summary') }}</textarea>
                            @error('patient_history_summary')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Recommendations for Ongoing Care -->
                        <div class="mb-3">
                            <label for="recommendations" class="form-label">Recommendations for Ongoing Care <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('recommendations') is-invalid @enderror" 
                                      id="recommendations" 
                                      name="recommendations" 
                                      rows="6"
                                      required
                                      placeholder="Provide detailed recommendations for continued treatment, medications, follow-up schedule, warning signs to watch for...">{{ old('recommendations') }}</textarea>
                            <small class="text-muted">Include treatment plan, medications, follow-up requirements, and any precautions</small>
                            @error('recommendations')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Report Upload -->
                        <div class="mb-3">
                            <label for="report_file" class="form-label">Attach Consultation Report</label>
                            <input type="file" 
                                   class="form-control @error('report_file') is-invalid @enderror" 
                                   id="report_file" 
                                   name="report_file"
                                   accept=".pdf,.doc,.docx">
                            <small class="text-muted">Include assessment results, test reports, or detailed consultation notes (Max: 10MB)</small>
                            @error('report_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4 bg-success bg-opacity-10">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3 text-success">
                            <i class="bi bi-info-circle me-2"></i>Back-Referral Guidelines
                        </h6>
                        
                        <div class="mb-3">
                            <strong>Include in Back-Referral:</strong>
                            <ul class="small mb-0 mt-1">
                                <li>Comprehensive consultation summary</li>
                                <li>Diagnosis and assessment findings</li>
                                <li>Treatment provided during consultation</li>
                                <li>Clear recommendations for ongoing care</li>
                                <li>Medication adjustments if any</li>
                                <li>Follow-up requirements</li>
                                <li>Warning signs for primary doctor</li>
                            </ul>
                        </div>

                        <div class="alert alert-success mb-0">
                            <small>
                                <i class="bi bi-check-circle me-1"></i>
                                <strong>Note:</strong> The original referral will be marked as completed when you send this back-referral.
                            </small>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <button type="submit" class="btn btn-success w-100 mb-2">
                            <i class="bi bi-arrow-left-circle me-2"></i>Send Back-Referral
                        </button>
                        <a href="{{ route('doctor.patients.referrals.show', [$referral->patient_id, $referral]) }}" class="btn btn-outline-secondary w-100">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
