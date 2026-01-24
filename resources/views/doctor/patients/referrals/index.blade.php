@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Referrals - {{ $patient->user->name }}</h2>
            <p class="text-muted mb-0">Manage patient referrals and consultations</p>
        </div>
        <div>
            <a href="{{ route('doctor.patients.show', $patient) }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left"></i> Back to Patient
            </a>
            <a href="{{ route('doctor.patients.referrals.create', $patient) }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> New Referral
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Sent Referrals -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-arrow-right-circle me-2 text-primary"></i>Sent Referrals
                    </h5>
                </div>
                <div class="card-body">
                    @if($sentReferrals->isEmpty())
                        <div class="text-center py-4">
                            <i class="bi bi-inbox text-muted" style="font-size: 2.5rem;"></i>
                            <p class="text-muted mt-2 mb-0">No referrals sent yet</p>
                        </div>
                    @else
                        @foreach($sentReferrals as $referral)
                            <div class="card mb-3 border-start border-primary border-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="fw-bold mb-1">
                                                <i class="bi bi-{{ $referral->getTypeIcon() }} me-1"></i>
                                                {{ $referral->getTypeLabel() }}
                                            </h6>
                                            <small class="text-muted">
                                                To: {{ $referral->referredTo->name ?? 'Not assigned' }}
                                            </small>
                                        </div>
                                        <span class="badge bg-{{ $referral->getStatusBadgeColor() }}">
                                            {{ $referral->getStatusLabel() }}
                                        </span>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Specialty:</small>
                                        <span class="badge bg-secondary">{{ ucwords(str_replace('_', ' ', $referral->specialty_needed)) }}</span>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <small class="text-muted">Reason:</small>
                                        <p class="small mb-0">{{ Str::limit($referral->reason, 100) }}</p>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">{{ $referral->referred_at->diffForHumans() }}</small>
                                        <a href="{{ route('doctor.patients.referrals.show', [$patient, $referral]) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <!-- Received Referrals (Back Referrals) -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">
                        <i class="bi bi-arrow-left-circle me-2 text-success"></i>Received Referrals
                    </h5>
                </div>
                <div class="card-body">
                    @if($receivedReferrals->isEmpty())
                        <div class="text-center py-4">
                            <i class="bi bi-inbox text-muted" style="font-size: 2.5rem;"></i>
                            <p class="text-muted mt-2 mb-0">No referrals received yet</p>
                        </div>
                    @else
                        @foreach($receivedReferrals as $referral)
                            <div class="card mb-3 border-start border-success border-3">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="fw-bold mb-1">
                                                <i class="bi bi-{{ $referral->getTypeIcon() }} me-1"></i>
                                                {{ $referral->getTypeLabel() }}
                                            </h6>
                                            <small class="text-muted">
                                                From: {{ $referral->referredBy->name ?? 'Unknown' }}
                                            </small>
                                        </div>
                                        <span class="badge bg-{{ $referral->getStatusBadgeColor() }}">
                                            {{ $referral->getStatusLabel() }}
                                        </span>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Specialty:</small>
                                        <span class="badge bg-secondary">{{ ucwords(str_replace('_', ' ', $referral->specialty_needed)) }}</span>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <small class="text-muted">Reason:</small>
                                        <p class="small mb-0">{{ Str::limit($referral->reason, 100) }}</p>
                                    </div>
                                    
                                    @if($referral->response_notes)
                                        <div class="mb-2">
                                            <small class="text-success">
                                                <i class="bi bi-check-circle me-1"></i>
                                                Response provided
                                            </small>
                                        </div>
                                    @endif
                                    
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">{{ $referral->referred_at->diffForHumans() }}</small>
                                        <a href="{{ route('doctor.patients.referrals.show', [$patient, $referral]) }}" class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
