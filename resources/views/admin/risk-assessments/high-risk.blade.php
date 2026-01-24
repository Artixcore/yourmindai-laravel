@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1 text-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>High-Risk Alert Board
            </h2>
            <p class="text-muted mb-0">Monitor critical and high-risk patients requiring immediate attention</p>
        </div>
        <a href="{{ route('admin.risk-assessments.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <!-- Alert Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-danger border-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-exclamation-octagon text-danger" style="font-size: 2.5rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total High-Risk</h6>
                            <h2 class="mb-0 text-danger">{{ $stats['total_high_risk'] }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-dark border-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-exclamation-circle text-dark" style="font-size: 2.5rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Critical Level</h6>
                            <h2 class="mb-0">{{ $stats['critical'] }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-warning border-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-shield-exclamation text-warning" style="font-size: 2.5rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">High Level</h6>
                            <h2 class="mb-0">{{ $stats['high'] }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm border-start border-primary border-4">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-clock-history text-primary" style="font-size: 2.5rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Recent (7 days)</h6>
                            <h2 class="mb-0">{{ $stats['recent'] }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent High-Risk Patients (Last 30 Days) -->
    @if($recentHighRisk->isNotEmpty())
        <div class="card border-0 shadow-sm mb-4 border-start border-danger border-4">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0 text-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i>Recent High-Risk Cases (Last 30 Days)
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($recentHighRisk as $patientId => $assessments)
                        @php
                            $latestAssessment = $assessments->first();
                        @endphp
                        <div class="col-md-6">
                            <div class="card border-0 bg-light">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="fw-bold mb-1">
                                                <i class="bi bi-person me-1"></i>
                                                {{ $latestAssessment->patient->user->name ?? 'Unknown' }}
                                            </h6>
                                            <small class="text-muted">
                                                Doctor: {{ $latestAssessment->assessedBy->name ?? 'Unknown' }}
                                            </small>
                                        </div>
                                        <span class="badge bg-{{ $latestAssessment->getRiskBadgeColor() }}">
                                            {{ $latestAssessment->getRiskLevelLabel() }}
                                        </span>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <small class="text-muted d-block">Latest Assessment:</small>
                                        <small>{{ $latestAssessment->assessment_date->format('M d, Y') }}</small>
                                        @if($latestAssessment->alert_sent)
                                            <span class="badge bg-warning ms-2">Alert Sent</span>
                                        @endif
                                    </div>

                                    @if($latestAssessment->intervention_plan)
                                        <div class="mb-2">
                                            <small class="text-success">
                                                <i class="bi bi-check-circle me-1"></i>
                                                Intervention plan in place
                                            </small>
                                        </div>
                                    @endif

                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="text-muted">
                                            {{ $assessments->count() }} assessment(s) in last 30 days
                                        </small>
                                        <a href="{{ route('admin.risk-assessments.show', $latestAssessment) }}" 
                                           class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- All High-Risk Assessments -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0">All High-Risk Assessments</h5>
        </div>
        <div class="card-body p-0">
            @if($highRiskAssessments->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-shield-check text-success" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3 mb-0">No high-risk assessments found</p>
                    <p class="text-muted small">All patients are currently at low or moderate risk levels.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Patient</th>
                                <th>Date</th>
                                <th>Risk Level</th>
                                <th>Risk Factors</th>
                                <th>Assessed By</th>
                                <th>Alert</th>
                                <th>Intervention</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($highRiskAssessments as $assessment)
                                <tr class="{{ $assessment->risk_level == 'critical' ? 'table-danger' : '' }}">
                                    <td>
                                        <i class="bi bi-person me-1"></i>
                                        {{ $assessment->patient->user->name ?? 'Unknown' }}
                                    </td>
                                    <td>
                                        {{ $assessment->assessment_date->format('M d, Y') }}
                                        <br>
                                        <small class="text-muted">{{ $assessment->assessment_date->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $assessment->getRiskBadgeColor() }}">
                                            <i class="bi bi-{{ $assessment->getRiskIcon() }} me-1"></i>
                                            {{ $assessment->getRiskLevelLabel() }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($assessment->risk_factors && count($assessment->risk_factors) > 0)
                                            <span class="badge bg-secondary">{{ count($assessment->risk_factors) }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>{{ $assessment->assessedBy->name ?? 'Unknown' }}</td>
                                    <td>
                                        @if($assessment->alert_sent)
                                            <span class="badge bg-warning">
                                                <i class="bi bi-bell"></i> Sent
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($assessment->intervention_plan)
                                            <i class="bi bi-check-circle text-success" title="Plan in place"></i>
                                        @else
                                            <i class="bi bi-x-circle text-danger" title="No plan"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.risk-assessments.show', $assessment) }}" 
                                           class="btn btn-sm btn-outline-danger">
                                            <i class="bi bi-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
        @if($highRiskAssessments->hasPages())
            <div class="card-footer bg-white border-0">
                {{ $highRiskAssessments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
