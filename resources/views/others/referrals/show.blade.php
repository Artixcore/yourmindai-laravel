@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Referral Details</h2>
            <p class="text-muted mb-0">Specialist Consultation Request</p>
        </div>
        <a href="{{ route('others.referrals.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Referral Information</h5>
                        <span class="badge bg-{{ $referral->getStatusBadgeColor() }}" style="font-size: 1rem;">
                            <i class="bi bi-{{ $referral->getStatusIcon() }} me-1"></i>
                            {{ $referral->getStatusLabel() }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Patient</h6>
                            <p class="mb-0 fw-bold">
                                <i class="bi bi-person me-2"></i>
                                {{ $referral->patient->user->name }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Referring Clinician</h6>
                            <p class="mb-0">
                                <i class="bi bi-person-badge me-2"></i>
                                {{ $referral->referredBy->name }}
                            </p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Specialty Needed</h6>
                            <span class="badge bg-secondary">{{ ucwords(str_replace('_', ' ', $referral->specialty_needed)) }}</span>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Referral Date</h6>
                            <p class="mb-0">{{ $referral->referred_at->format('F d, Y') }}</p>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-muted mb-2">Reason for Referral</h6>
                        <div class="p-3 bg-light rounded">
                            <p class="mb-0" style="white-space: pre-line;">{{ $referral->reason }}</p>
                        </div>
                    </div>

                    @if($referral->patient_history_summary)
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">Patient History Summary</h6>
                            <div class="p-3 bg-light rounded">
                                <p class="mb-0" style="white-space: pre-line;">{{ $referral->patient_history_summary }}</p>
                            </div>
                        </div>
                    @endif

                    @if($referral->recommendations)
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">Recommendations from Referring Clinician</h6>
                            <div class="p-3 bg-light rounded border-start border-primary border-4">
                                <p class="mb-0" style="white-space: pre-line;">{{ $referral->recommendations }}</p>
                            </div>
                        </div>
                    @endif

                    @if($referral->report_file_path)
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">Attached Report</h6>
                            <a href="{{ Storage::url($referral->report_file_path) }}" target="_blank" class="btn btn-outline-primary">
                                <i class="bi bi-file-earmark-pdf me-2"></i>Download Report
                            </a>
                        </div>
                    @endif

                    @if($referral->response_notes)
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">Your Response</h6>
                            <div class="p-3 bg-success bg-opacity-10 rounded border-start border-success border-4">
                                <p class="mb-0" style="white-space: pre-line;">{{ $referral->response_notes }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Response Form -->
            @if($referral->status === 'pending')
                <div class="card border-0 shadow-sm mb-4 border-start border-warning border-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0">
                            <i class="bi bi-reply me-2"></i>Respond to Referral
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('others.referrals.respond', $referral) }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label class="form-label">Action <span class="text-danger">*</span></label>
                                <div class="d-flex gap-2">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="action" id="action_accept" value="accept" required>
                                        <label class="form-check-label" for="action_accept">
                                            <i class="bi bi-check-circle text-success"></i> Accept Referral
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="action" id="action_decline" value="decline" required>
                                        <label class="form-check-label" for="action_decline">
                                            <i class="bi bi-x-circle text-danger"></i> Decline Referral
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="response_notes" class="form-label">Response Notes <span class="text-danger">*</span></label>
                                <textarea class="form-control" 
                                          id="response_notes" 
                                          name="response_notes" 
                                          rows="5" 
                                          required
                                          placeholder="Provide your response, availability, or reason for declining..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send"></i> Submit Response
                            </button>
                        </form>
                    </div>
                </div>
            @endif

            <!-- Update Progress Form -->
            @if(in_array($referral->status, ['accepted', 'in_progress']))
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0">
                            <i class="bi bi-arrow-repeat me-2"></i>Update Consultation Status
                        </h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('others.referrals.update', $referral) }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="in_progress" {{ $referral->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="completed">Completed</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="response_notes" class="form-label">Progress Notes <span class="text-danger">*</span></label>
                                <textarea class="form-control" 
                                          id="response_notes" 
                                          name="response_notes" 
                                          rows="5" 
                                          required
                                          placeholder="Document consultation progress, findings, or completion summary..."></textarea>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Update Status
                            </button>
                        </form>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Referred</small>
                        <p class="mb-0">{{ $referral->referred_at->format('M d, Y g:i A') }}</p>
                    </div>
                    @if($referral->responded_at)
                        <div class="mb-3">
                            <small class="text-muted">Responded</small>
                            <p class="mb-0">{{ $referral->responded_at->format('M d, Y g:i A') }}</p>
                        </div>
                    @endif
                    @if($referral->completed_at)
                        <div>
                            <small class="text-muted">Completed</small>
                            <p class="mb-0">{{ $referral->completed_at->format('M d, Y g:i A') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            @if($referral->status === 'pending')
                <div class="card border-0 shadow-sm mb-4 bg-warning bg-opacity-10">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3 text-warning">
                            <i class="bi bi-clock me-2"></i>Awaiting Your Response
                        </h6>
                        <p class="small mb-0">
                            Please review this referral and provide your response to the referring clinician.
                        </p>
                    </div>
                </div>
            @endif

            @if(in_array($referral->status, ['accepted', 'in_progress']))
                <div class="card border-0 shadow-sm mb-4 bg-info bg-opacity-10">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3 text-info">
                            <i class="bi bi-info-circle me-2"></i>Active Consultation
                        </h6>
                        <p class="small mb-0">
                            Remember to update the consultation status and provide notes on your findings. 
                            When completed, you can create a back-referral from the doctor's panel.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
