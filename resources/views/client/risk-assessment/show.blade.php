@extends('client.layout')

@section('content')
<div class="container py-4">
    <div class="mb-4">
        <a href="{{ route('client.risk-assessments.index') }}" class="btn btn-outline-secondary btn-sm mb-3">
            <i class="bi bi-arrow-left"></i> Back to Assessments
        </a>
        <h2 class="fw-bold mb-1">Risk Assessment Details</h2>
        <p class="text-muted mb-0">{{ $assessment->assessment_date->format('F d, Y') }}</p>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Risk Level Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center py-4">
                    <i class="bi bi-{{ $assessment->getRiskIcon() }} text-{{ $assessment->getRiskBadgeColor() }}" style="font-size: 4rem;"></i>
                    <h3 class="mt-3 mb-2">
                        <span class="badge bg-{{ $assessment->getRiskBadgeColor() }}" style="font-size: 1.5rem;">
                            {{ $assessment->getRiskLevelLabel() }} Risk
                        </span>
                    </h3>
                    <p class="text-muted mb-0">Assessment Date: {{ $assessment->assessment_date->format('F d, Y') }}</p>
                </div>
            </div>

            <!-- Risk Factors -->
            @if($assessment->risk_factors && count($assessment->risk_factors) > 0)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0">
                            <i class="bi bi-exclamation-triangle me-2"></i>Identified Risk Factors
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            @foreach($assessment->risk_factors as $factor)
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center p-2 bg-light rounded">
                                        <i class="bi bi-dot text-danger me-2" style="font-size: 1.5rem;"></i>
                                        <span>{{ ucwords(str_replace('_', ' ', $factor)) }}</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Assessment Notes -->
            @if($assessment->assessment_notes)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0">
                            <i class="bi bi-clipboard-check me-2"></i>Assessment Notes
                        </h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-0">{{ $assessment->assessment_notes }}</p>
                    </div>
                </div>
            @endif

            <!-- Intervention Plan -->
            @if($assessment->intervention_plan)
                <div class="card border-0 shadow-sm mb-4 border-start border-primary border-4">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0">
                            <i class="bi bi-shield-check me-2 text-primary"></i>Your Safety & Intervention Plan
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Important:</strong> Please review this plan carefully and follow the recommendations provided by your therapist.
                        </div>
                        <div class="p-3 bg-light rounded">
                            <p class="mb-0" style="white-space: pre-line;">{{ $assessment->intervention_plan }}</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Emergency Resources -->
            @if($assessment->isHighRisk())
                <div class="card border-0 shadow-sm mb-4 bg-danger text-white">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">
                            <i class="bi bi-telephone-fill me-2"></i>Emergency Resources
                        </h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Crisis Hotline</strong>
                                <p class="mb-0">Call: 988 (24/7)</p>
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Emergency Services</strong>
                                <p class="mb-0">Call: 911</p>
                            </div>
                            <div class="col-12">
                                <small>If you are in immediate danger or having thoughts of harming yourself or others, please seek help immediately.</small>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Assessment Info -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">Assessment Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Assessed By</small>
                        <p class="mb-0">
                            <i class="bi bi-person me-1"></i>
                            {{ $assessment->assessedBy->name ?? 'Unknown' }}
                        </p>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Assessment Date</small>
                        <p class="mb-0">
                            <i class="bi bi-calendar3 me-1"></i>
                            {{ $assessment->assessment_date->format('M d, Y') }}
                        </p>
                    </div>
                    @if($assessment->alert_sent)
                        <div class="mb-3">
                            <small class="text-muted d-block mb-1">Alert Status</small>
                            <span class="badge bg-warning">
                                <i class="bi bi-bell me-1"></i>
                                Alert Sent
                            </span>
                            <small class="d-block text-muted mt-1">
                                {{ $assessment->alert_sent_at->format('M d, Y g:i A') }}
                            </small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Help Card -->
            <div class="card border-0 shadow-sm mb-4 bg-light">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">
                        <i class="bi bi-question-circle me-2"></i>Need Help?
                    </h6>
                    <p class="small mb-3">If you have questions about your assessment or intervention plan, please contact your therapist.</p>
                    <a href="{{ route('client.sessions.index') }}" class="btn btn-sm btn-primary w-100 mb-2">
                        <i class="bi bi-calendar me-1"></i> View Sessions
                    </a>
                    <a href="{{ route('patient.messages.index') }}" class="btn btn-sm btn-outline-primary w-100">
                        <i class="bi bi-chat me-1"></i> Message Therapist
                    </a>
                </div>
            </div>

            <!-- Quick Tips -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="mb-0">
                        <i class="bi bi-lightbulb me-2"></i>Self-Care Tips
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0 small">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Follow your intervention plan
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Attend all scheduled sessions
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Practice coping strategies daily
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Reach out when you need support
                        </li>
                        <li class="mb-0">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Track your mood and progress
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
