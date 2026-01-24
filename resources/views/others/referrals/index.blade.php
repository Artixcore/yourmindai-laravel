@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h2 class="fw-bold mb-1">Incoming Referrals</h2>
        <p class="text-muted mb-0">Manage referrals sent to you for specialist consultation</p>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-clipboard-data text-primary" style="font-size: 2rem;"></i>
                    <h3 class="mt-2 mb-0">{{ $stats['total'] }}</h3>
                    <p class="text-muted small mb-0">Total Referrals</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-clock text-warning" style="font-size: 2rem;"></i>
                    <h3 class="mt-2 mb-0">{{ $stats['pending'] }}</h3>
                    <p class="text-muted small mb-0">Pending Response</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-arrow-repeat text-primary" style="font-size: 2rem;"></i>
                    <h3 class="mt-2 mb-0">{{ $stats['active'] }}</h3>
                    <p class="text-muted small mb-0">Active Cases</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                    <h3 class="mt-2 mb-0">{{ $stats['completed'] }}</h3>
                    <p class="text-muted small mb-0">Completed</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Referrals List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0">All Referrals</h5>
        </div>
        <div class="card-body">
            @if($referrals->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3 mb-0">No referrals yet</p>
                    <p class="text-muted small">Referrals from other clinicians will appear here.</p>
                </div>
            @else
                @foreach($referrals as $referral)
                    <div class="card mb-3 border-start border-{{ $referral->getStatusBadgeColor() }} border-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="fw-bold mb-1">
                                                Patient: {{ $referral->patient->user->name }}
                                            </h6>
                                            <small class="text-muted">
                                                Referred by: {{ $referral->referredBy->name }}
                                            </small>
                                        </div>
                                        <span class="badge bg-{{ $referral->getStatusBadgeColor() }}">
                                            {{ $referral->getStatusLabel() }}
                                        </span>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <span class="badge bg-secondary">{{ ucwords(str_replace('_', ' ', $referral->specialty_needed)) }}</span>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <small class="text-muted">Reason:</small>
                                        <p class="small mb-0">{{ Str::limit($referral->reason, 150) }}</p>
                                    </div>
                                    
                                    <small class="text-muted">
                                        <i class="bi bi-calendar me-1"></i>
                                        {{ $referral->referred_at->format('M d, Y') }} ({{ $referral->referred_at->diffForHumans() }})
                                    </small>
                                </div>
                                
                                <div class="col-md-4 d-flex align-items-center justify-content-end">
                                    <a href="{{ route('others.referrals.show', $referral) }}" class="btn btn-outline-primary">
                                        <i class="bi bi-eye"></i> View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        @if($referrals->hasPages())
            <div class="card-footer bg-white border-0">
                {{ $referrals->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
