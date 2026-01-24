@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Risk Assessments - {{ $patient->user->name }}</h2>
            <p class="text-muted mb-0">Track and monitor patient risk levels over time</p>
        </div>
        <div>
            <a href="{{ route('doctor.patients.show', $patient) }}" class="btn btn-outline-secondary me-2">
                <i class="bi bi-arrow-left"></i> Back to Patient
            </a>
            <a href="{{ route('doctor.patients.risk-assessments.create', $patient) }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> New Assessment
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-clipboard-data text-primary" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Total Assessments</h6>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-exclamation-triangle text-danger" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">High Risk Cases</h6>
                            <h3 class="mb-0 text-danger">{{ $stats['high_risk'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-shield-{{ $stats['latest_level'] === 'none' ? 'check' : 'exclamation' }} text-{{ $stats['latest_level'] === 'none' ? 'success' : 'warning' }}" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Latest Level</h6>
                            <h3 class="mb-0">
                                <span class="badge bg-{{ (new \App\Models\RiskAssessment(['risk_level' => $stats['latest_level']]))->getRiskBadgeColor() }}">
                                    {{ ucfirst($stats['latest_level']) }}
                                </span>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <i class="bi bi-graph-up text-info" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Average Level</h6>
                            <h3 class="mb-0">
                                <span class="badge bg-{{ (new \App\Models\RiskAssessment(['risk_level' => $stats['avg_level']]))->getRiskBadgeColor() }}">
                                    {{ ucfirst($stats['avg_level']) }}
                                </span>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assessments List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0">Assessment History</h5>
        </div>
        <div class="card-body p-0">
            @if($assessments->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-clipboard-x text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3 mb-0">No risk assessments yet</p>
                    <p class="text-muted small">Create the first assessment to start tracking risk levels.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Date</th>
                                <th>Risk Level</th>
                                <th>Risk Factors</th>
                                <th>Assessed By</th>
                                <th>Alert Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assessments as $assessment)
                                <tr>
                                    <td>
                                        <i class="bi bi-calendar3 me-1 text-muted"></i>
                                        {{ $assessment->assessment_date->format('M d, Y') }}
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $assessment->getRiskBadgeColor() }}">
                                            <i class="bi bi-{{ $assessment->getRiskIcon() }} me-1"></i>
                                            {{ $assessment->getRiskLevelLabel() }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($assessment->risk_factors && count($assessment->risk_factors) > 0)
                                            <span class="badge bg-secondary">{{ count($assessment->risk_factors) }} factors</span>
                                        @else
                                            <span class="text-muted">None specified</span>
                                        @endif
                                    </td>
                                    <td>
                                        <i class="bi bi-person me-1 text-muted"></i>
                                        {{ $assessment->assessedBy->name ?? 'Unknown' }}
                                    </td>
                                    <td>
                                        @if($assessment->alert_sent)
                                            <span class="badge bg-warning">
                                                <i class="bi bi-bell me-1"></i>
                                                Alert Sent
                                            </span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('doctor.patients.risk-assessments.show', [$patient, $assessment]) }}" 
                                           class="btn btn-sm btn-outline-primary">
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
        @if($assessments->hasPages())
            <div class="card-footer bg-white border-0">
                {{ $assessments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
