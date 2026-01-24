@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Risk Assessment Details</h2>
            <p class="text-muted mb-0">Patient: {{ $assessment->patient->user->name ?? 'Unknown' }}</p>
        </div>
        <a href="{{ route('admin.risk-assessments.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Assessment Overview -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">Assessment Overview</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Patient</h6>
                            <p class="mb-0">
                                <i class="bi bi-person me-2 text-muted"></i>
                                {{ $assessment->patient->user->name ?? 'Unknown' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Assessment Date</h6>
                            <p class="mb-0">
                                <i class="bi bi-calendar3 me-2 text-muted"></i>
                                {{ $assessment->assessment_date->format('F d, Y') }}
                            </p>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Assessed By</h6>
                            <p class="mb-0">
                                <i class="bi bi-person-badge me-2 text-muted"></i>
                                {{ $assessment->assessedBy->name ?? 'Unknown' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Risk Level</h6>
                            <span class="badge bg-{{ $assessment->getRiskBadgeColor() }} px-3 py-2" style="font-size: 1rem;">
                                <i class="bi bi-{{ $assessment->getRiskIcon() }} me-1"></i>
                                {{ $assessment->getRiskLevelLabel() }}
                            </span>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-2">Alert Status</h6>
                            @if($assessment->alert_sent)
                                <div>
                                    <span class="badge bg-warning px-3 py-2">
                                        <i class="bi bi-bell me-1"></i>
                                        Alert Sent
                                    </span>
                                    <small class="d-block text-muted mt-1">
                                        {{ $assessment->alert_sent_at->format('M d, Y g:i A') }}
                                    </small>
                                </div>
                            @else
                                <span class="text-muted">No alert sent</span>
                            @endif
                        </div>
                    </div>

                    @if($assessment->risk_factors && count($assessment->risk_factors) > 0)
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">Identified Risk Factors</h6>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach($assessment->risk_factors as $factor)
                                    <span class="badge bg-secondary">{{ ucwords(str_replace('_', ' ', $factor)) }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($assessment->assessment_notes)
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">Assessment Notes</h6>
                            <div class="p-3 bg-light rounded">
                                <p class="mb-0">{{ $assessment->assessment_notes }}</p>
                            </div>
                        </div>
                    @endif

                    @if($assessment->intervention_plan)
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">Intervention Plan</h6>
                            <div class="p-3 bg-light rounded border-start border-primary border-4">
                                <p class="mb-0">{{ $assessment->intervention_plan }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Assessment History Comparison -->
            @if($previousAssessment || $nextAssessment)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0">Assessment History</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if($previousAssessment)
                                <div class="col-md-4">
                                    <div class="text-center p-3 border rounded">
                                        <small class="text-muted d-block mb-2">Previous</small>
                                        <small class="d-block mb-2">{{ $previousAssessment->assessment_date->format('M d, Y') }}</small>
                                        <span class="badge bg-{{ $previousAssessment->getRiskBadgeColor() }}">
                                            {{ $previousAssessment->getRiskLevelLabel() }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                            <div class="col-md-4">
                                <div class="text-center p-3 border rounded bg-light">
                                    <small class="text-muted d-block mb-2"><strong>Current</strong></small>
                                    <small class="d-block mb-2">{{ $assessment->assessment_date->format('M d, Y') }}</small>
                                    <span class="badge bg-{{ $assessment->getRiskBadgeColor() }}" style="font-size: 1rem;">
                                        {{ $assessment->getRiskLevelLabel() }}
                                    </span>
                                </div>
                            </div>
                            @if($nextAssessment)
                                <div class="col-md-4">
                                    <div class="text-center p-3 border rounded">
                                        <small class="text-muted d-block mb-2">Next</small>
                                        <small class="d-block mb-2">{{ $nextAssessment->assessment_date->format('M d, Y') }}</small>
                                        <span class="badge bg-{{ $nextAssessment->getRiskBadgeColor() }}">
                                            {{ $nextAssessment->getRiskLevelLabel() }}
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        @if($previousAssessment)
                            @php
                                $riskLevels = ['none' => 0, 'low' => 1, 'moderate' => 2, 'high' => 3, 'critical' => 4];
                                $prevLevel = $riskLevels[$previousAssessment->risk_level];
                                $currLevel = $riskLevels[$assessment->risk_level];
                            @endphp
                            <div class="text-center mt-3">
                                @if($currLevel < $prevLevel)
                                    <span class="badge bg-success">
                                        <i class="bi bi-arrow-down"></i> Risk Decreased
                                    </span>
                                @elseif($currLevel > $prevLevel)
                                    <span class="badge bg-danger">
                                        <i class="bi bi-arrow-up"></i> Risk Increased
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-arrow-left-right"></i> No Change
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Timeline -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">Timeline</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted">Created</small>
                        <p class="mb-0">{{ $assessment->created_at->format('M d, Y g:i A') }}</p>
                    </div>
                    @if($assessment->updated_at != $assessment->created_at)
                        <div>
                            <small class="text-muted">Last Updated</small>
                            <p class="mb-0">{{ $assessment->updated_at->format('M d, Y g:i A') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Feedback Section -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">Feedback</h5>
                </div>
                <div class="card-body">
                    @if($assessment->feedback->isEmpty())
                        <p class="text-muted small mb-0">No feedback yet</p>
                    @else
                        @foreach($assessment->feedback->take(5) as $feedback)
                            <div class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between mb-1">
                                    <strong class="small">{{ ucfirst($feedback->feedback_source) }}</strong>
                                    <small class="text-muted">{{ $feedback->created_at->diffForHumans() }}</small>
                                </div>
                                @if($feedback->rating)
                                    <div class="mb-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star{{ $i <= $feedback->rating ? '-fill text-warning' : '' }} small"></i>
                                        @endfor
                                    </div>
                                @endif
                                <p class="small mb-0">{{ $feedback->feedback_text }}</p>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">Quick Actions</h5>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.patients.show', $assessment->patient_id) }}" class="btn btn-outline-primary w-100 mb-2">
                        <i class="bi bi-person"></i> View Patient Profile
                    </a>
                    <a href="{{ route('admin.risk-assessments.index', ['doctor_id' => $assessment->assessed_by]) }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-person-badge"></i> View Doctor's Assessments
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
