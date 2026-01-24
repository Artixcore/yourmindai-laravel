@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Risk Assessment Analytics</h2>
            <p class="text-muted mb-0">System-wide risk metrics and trends</p>
        </div>
        <a href="{{ route('admin.risk-assessments.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <!-- Key Statistics -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h3 class="mb-1">{{ $stats['total_assessments'] }}</h3>
                    <small class="text-muted">Total Assessments</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h3 class="mb-1 text-danger">{{ $stats['high_risk_cases'] }}</h3>
                    <small class="text-muted">High Risk</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h3 class="mb-1 text-warning">{{ $stats['alerts_sent'] }}</h3>
                    <small class="text-muted">Alerts Sent</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h3 class="mb-1 text-info">{{ $stats['avg_per_patient'] }}</h3>
                    <small class="text-muted">Avg per Patient</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h3 class="mb-1">{{ $stats['this_week'] }}</h3>
                    <small class="text-muted">This Week</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h3 class="mb-1">{{ $stats['this_month'] }}</h3>
                    <small class="text-muted">This Month</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <!-- Risk Level Distribution -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">Risk Level Distribution</h5>
                </div>
                <div class="card-body">
                    @php
                        $total = array_sum($riskDistribution);
                    @endphp
                    @foreach(['none', 'low', 'moderate', 'high', 'critical'] as $level)
                        @php
                            $count = $riskDistribution[$level] ?? 0;
                            $percentage = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                            $badgeColor = (new \App\Models\RiskAssessment(['risk_level' => $level]))->getRiskBadgeColor();
                        @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <span>
                                    <span class="badge bg-{{ $badgeColor }}">{{ ucfirst($level) }}</span>
                                </span>
                                <span>{{ $count }} ({{ $percentage }}%)</span>
                            </div>
                            <div class="progress" style="height: 20px;">
                                <div class="progress-bar bg-{{ $badgeColor }}" 
                                     role="progressbar" 
                                     style="width: {{ $percentage }}%"
                                     aria-valuenow="{{ $percentage }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <!-- Top Risk Factors -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">Most Common Risk Factors</h5>
                </div>
                <div class="card-body">
                    @if(empty($riskFactorsFrequency))
                        <p class="text-muted">No risk factors data available</p>
                    @else
                        @foreach(array_slice($riskFactorsFrequency, 0, 10, true) as $factor => $count)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between mb-1">
                                    <span class="small">{{ ucwords(str_replace('_', ' ', $factor)) }}</span>
                                    <strong>{{ $count }}</strong>
                                </div>
                                <div class="progress" style="height: 15px;">
                                    <div class="progress-bar bg-secondary" 
                                         role="progressbar" 
                                         style="width: {{ ($count / max($riskFactorsFrequency)) * 100 }}%">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <!-- Assessments Over Time -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">Assessments Over Time (Last 12 Months)</h5>
                </div>
                <div class="card-body">
                    @if($assessmentsByMonth->isEmpty())
                        <p class="text-muted">No data available</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Count</th>
                                        <th>Trend</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($assessmentsByMonth as $data)
                                        <tr>
                                            <td>{{ DateTime::createFromFormat('!m', $data->month)->format('F') }} {{ $data->year }}</td>
                                            <td><strong>{{ $data->count }}</strong></td>
                                            <td>
                                                <div class="progress" style="height: 10px; width: 100px;">
                                                    <div class="progress-bar bg-primary" 
                                                         style="width: {{ ($data->count / $assessmentsByMonth->max('count')) * 100 }}%">
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <!-- High-Risk Cases by Doctor -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">High-Risk Cases by Doctor (Top 10)</h5>
                </div>
                <div class="card-body">
                    @if($highRiskByDoctor->isEmpty())
                        <p class="text-muted">No high-risk cases data available</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Doctor</th>
                                        <th>High-Risk Cases</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($highRiskByDoctor as $data)
                                        <tr>
                                            <td>
                                                <i class="bi bi-person me-1"></i>
                                                {{ $data->assessedBy->name ?? 'Unknown' }}
                                            </td>
                                            <td>
                                                <span class="badge bg-danger">{{ $data->count }}</span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-12 mb-4">
            <!-- High-Risk Trend -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="mb-0">High-Risk Cases Trend (Last 12 Months)</h5>
                </div>
                <div class="card-body">
                    @if($highRiskTrend->isEmpty())
                        <p class="text-muted">No high-risk trend data available</p>
                    @else
                        <div class="d-flex justify-content-between align-items-end" style="height: 200px;">
                            @foreach($highRiskTrend as $data)
                                @php
                                    $maxCount = $highRiskTrend->max('count');
                                    $height = $maxCount > 0 ? ($data->count / $maxCount) * 100 : 0;
                                @endphp
                                <div class="text-center" style="flex: 1;">
                                    <div class="bg-danger rounded" 
                                         style="height: {{ $height }}%; min-height: 20px; margin: 0 5px;"
                                         title="{{ $data->count }} cases">
                                    </div>
                                    <small class="text-muted mt-1 d-block">
                                        {{ DateTime::createFromFormat('!m', $data->month)->format('M') }}
                                    </small>
                                    <small class="fw-bold">{{ $data->count }}</small>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
