@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Referral Details</h2>
            <p class="text-muted mb-0">Patient: {{ $patient->user->name }}</p>
        </div>
        <div>
            <a href="{{ route('doctor.patients.referrals.index', $patient) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back to List
            </a>
            @if($referral->referred_to === auth()->id() && in_array($referral->status, ['accepted', 'in_progress']))
                <a href="{{ route('doctor.referrals.back-referral.create', $referral) }}" class="btn btn-success">
                    <i class="bi bi-arrow-left-circle"></i> Create Back-Referral
                </a>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-{{ $referral->getTypeIcon() }} me-2"></i>
                            {{ $referral->getTypeLabel() }}
                        </h5>
                        <span class="badge bg-{{ $referral->getStatusBadgeColor() }}" style="font-size: 1rem;">
                            <i class="bi bi-{{ $referral->getStatusIcon() }} me-1"></i>
                            {{ $referral->getStatusLabel() }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Referred By</h6>
                            <p class="mb-0">
                                <i class="bi bi-person me-2"></i>
                                {{ $referral->referredBy->name ?? 'Unknown' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Referred To</h6>
                            <p class="mb-0">
                                <i class="bi bi-person-badge me-2"></i>
                                {{ $referral->referredTo->name ?? 'Not assigned' }}
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
                            <p class="mb-0">{{ $referral->reason }}</p>
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
                            <h6 class="text-muted mb-2">Recommendations</h6>
                            <div class="p-3 bg-light rounded border-start border-primary border-4">
                                <p class="mb-0" style="white-space: pre-line;">{{ $referral->recommendations }}</p>
                            </div>
                        </div>
                    @endif

                    @if($referral->response_notes)
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">Response Notes</h6>
                            <div class="p-3 bg-success bg-opacity-10 rounded border-start border-success border-4">
                                <p class="mb-0" style="white-space: pre-line;">{{ $referral->response_notes }}</p>
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
                </div>
            </div>

            @if($referral->isForwardReferral() && $referral->backReferrals->isNotEmpty())
                <div class="card border-0 shadow-sm mb-4 border-start border-success border-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0">
                            <i class="bi bi-arrow-left-circle me-2 text-success"></i>Back-Referrals
                        </h5>
                    </div>
                    <div class="card-body">
                        @foreach($referral->backReferrals as $backRef)
                            <div class="card mb-3 bg-light">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between mb-2">
                                        <div>
                                            <h6 class="mb-1">From: {{ $backRef->referredBy->name }}</h6>
                                            <small class="text-muted">{{ $backRef->referred_at->format('M d, Y') }}</small>
                                        </div>
                                        <span class="badge bg-{{ $backRef->getStatusBadgeColor() }}">
                                            {{ $backRef->getStatusLabel() }}
                                        </span>
                                    </div>
                                    <p class="small mb-2">{{ Str::limit($backRef->reason, 150) }}</p>
                                    <a href="{{ route('doctor.patients.referrals.show', [$patient, $backRef]) }}" class="btn btn-sm btn-outline-success">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($referral->isBackReferral() && $referral->originalReferral)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0">
                            <i class="bi bi-arrow-right-circle me-2"></i>Original Referral
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="card bg-light">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <div>
                                        <h6 class="mb-1">From: {{ $referral->originalReferral->referredBy->name }}</h6>
                                        <small class="text-muted">{{ $referral->originalReferral->referred_at->format('M d, Y') }}</small>
                                    </div>
                                    <span class="badge bg-{{ $referral->originalReferral->getStatusBadgeColor() }}">
                                        {{ $referral->originalReferral->getStatusLabel() }}
                                    </span>
                                </div>
                                <p class="small mb-2">{{ Str::limit($referral->originalReferral->reason, 150) }}</p>
                                <a href="{{ route('doctor.patients.referrals.show', [$patient, $referral->originalReferral]) }}" class="btn btn-sm btn-outline-primary">
                                    View Details
                                </a>
                            </div>
                        </div>
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

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">Patient Info</h5>
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
                    <div class="mt-3">
                        <a href="{{ route('doctor.patients.show', $patient) }}" class="btn btn-outline-primary w-100">
                            <i class="bi bi-person"></i> View Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
