@extends('client.layout')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <h2 class="fw-bold mb-1">Risk Assessments</h2>
        <p class="text-muted mb-0">View your risk assessment history and intervention plans</p>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-clipboard-data text-primary" style="font-size: 2rem;"></i>
                    <h3 class="mt-2 mb-0">{{ $stats['total'] }}</h3>
                    <p class="text-muted small mb-0">Total Assessments</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-shield-{{ $stats['latest_level'] === 'none' ? 'check' : 'exclamation' }} text-{{ $stats['latest_level'] === 'none' ? 'success' : 'warning' }}" style="font-size: 2rem;"></i>
                    <h3 class="mt-2 mb-0">
                        <span class="badge bg-{{ (new \App\Models\RiskAssessment(['risk_level' => $stats['latest_level']]))->getRiskBadgeColor() }}">
                            {{ ucfirst($stats['latest_level']) }}
                        </span>
                    </h3>
                    <p class="text-muted small mb-0">Current Risk Level</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <i class="bi bi-{{ $stats['has_intervention'] ? 'check-circle text-success' : 'x-circle text-muted' }}" style="font-size: 2rem;"></i>
                    <h3 class="mt-2 mb-0">{{ $stats['has_intervention'] ? 'Yes' : 'No' }}</h3>
                    <p class="text-muted small mb-0">Intervention Plan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Assessments Timeline -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0">Assessment History</h5>
        </div>
        <div class="card-body">
            @if($assessments->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-clipboard-x text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3 mb-0">No risk assessments yet</p>
                    <p class="text-muted small">Your therapist will create assessments during your sessions.</p>
                </div>
            @else
                <div class="timeline">
                    @foreach($assessments as $assessment)
                        <div class="timeline-item mb-4">
                            <div class="row">
                                <div class="col-auto">
                                    <div class="timeline-marker bg-{{ $assessment->getRiskBadgeColor() }}">
                                        <i class="bi bi-{{ $assessment->getRiskIcon() }}"></i>
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="card border-0 bg-light">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <h6 class="fw-bold mb-1">
                                                        Assessment - {{ $assessment->assessment_date->format('M d, Y') }}
                                                    </h6>
                                                    <p class="text-muted small mb-0">
                                                        By {{ $assessment->assessedBy->name ?? 'Unknown' }}
                                                    </p>
                                                </div>
                                                <span class="badge bg-{{ $assessment->getRiskBadgeColor() }}">
                                                    {{ $assessment->getRiskLevelLabel() }}
                                                </span>
                                            </div>
                                            
                                            @if($assessment->risk_factors && count($assessment->risk_factors) > 0)
                                                <div class="mb-2">
                                                    <small class="text-muted d-block mb-1">Risk Factors:</small>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach(array_slice($assessment->risk_factors, 0, 3) as $factor)
                                                            <span class="badge bg-secondary">{{ ucwords(str_replace('_', ' ', $factor)) }}</span>
                                                        @endforeach
                                                        @if(count($assessment->risk_factors) > 3)
                                                            <span class="badge bg-secondary">+{{ count($assessment->risk_factors) - 3 }} more</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                            
                                            @if($assessment->intervention_plan)
                                                <div class="mb-2">
                                                    <small class="text-muted d-block mb-1">Intervention Plan Available</small>
                                                    <i class="bi bi-check-circle text-success me-1"></i>
                                                    <small>Your therapist has created a safety plan for you</small>
                                                </div>
                                            @endif
                                            
                                            <div class="mt-3">
                                                <a href="{{ route('client.risk-assessments.show', $assessment) }}" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-eye"></i> View Details
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($assessments->hasPages())
                    <div class="mt-4">
                        {{ $assessments->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<style>
.timeline-marker {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}
.timeline-item:not(:last-child) .timeline-marker::after {
    content: '';
    position: absolute;
    top: 40px;
    left: 19px;
    width: 2px;
    height: calc(100% + 10px);
    background: #dee2e6;
}
</style>
@endsection
