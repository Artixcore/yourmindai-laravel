@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="mb-1">Risk Assessments Management</h2>
            <p class="text-muted mb-0">System-wide risk assessment monitoring and oversight</p>
        </div>
        <div>
            <a href="{{ route('admin.risk-assessments.analytics') }}" class="btn btn-info me-2">
                <i class="bi bi-graph-up"></i> Analytics
            </a>
            <a href="{{ route('admin.risk-assessments.high-risk') }}" class="btn btn-danger">
                <i class="bi bi-exclamation-triangle"></i> High Risk
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
                            <i class="bi bi-bell text-warning" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Alerts Sent</h6>
                            <h3 class="mb-0">{{ $stats['alerts_sent'] }}</h3>
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
                            <i class="bi bi-calendar-check text-info" style="font-size: 2rem;"></i>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">This Month</h6>
                            <h3 class="mb-0">{{ $stats['this_month'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.risk-assessments.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="risk_level" class="form-label">Risk Level</label>
                        <select class="form-select" name="risk_level" id="risk_level">
                            <option value="">All Levels</option>
                            <option value="none" {{ request('risk_level') == 'none' ? 'selected' : '' }}>None</option>
                            <option value="low" {{ request('risk_level') == 'low' ? 'selected' : '' }}>Low</option>
                            <option value="moderate" {{ request('risk_level') == 'moderate' ? 'selected' : '' }}>Moderate</option>
                            <option value="high" {{ request('risk_level') == 'high' ? 'selected' : '' }}>High</option>
                            <option value="critical" {{ request('risk_level') == 'critical' ? 'selected' : '' }}>Critical</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="doctor_id" class="form-label">Doctor</label>
                        <select class="form-select" name="doctor_id" id="doctor_id">
                            <option value="">All Doctors</option>
                            @foreach($doctors as $doctor)
                                <option value="{{ $doctor->id }}" {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                    {{ $doctor->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" class="form-control" name="date_from" id="date_from" value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="date_to" class="form-label">Date To</label>
                        <input type="date" class="form-control" name="date_to" id="date_to" value="{{ request('date_to') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="alert_status" class="form-label">Alert Status</label>
                        <select class="form-select" name="alert_status" id="alert_status">
                            <option value="">All</option>
                            <option value="sent" {{ request('alert_status') == 'sent' ? 'selected' : '' }}>Sent</option>
                            <option value="not_sent" {{ request('alert_status') == 'not_sent' ? 'selected' : '' }}>Not Sent</option>
                        </select>
                    </div>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-filter"></i> Apply Filters
                    </button>
                    <a href="{{ route('admin.risk-assessments.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x"></i> Clear
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Assessments List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h5 class="mb-0">All Risk Assessments</h5>
        </div>
        <div class="card-body p-0">
            @if($assessments->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-clipboard-x text-muted" style="font-size: 3rem;"></i>
                    <p class="text-muted mt-3 mb-0">No risk assessments found</p>
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
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assessments as $assessment)
                                <tr>
                                    <td>
                                        <i class="bi bi-person me-1 text-muted"></i>
                                        {{ $assessment->patient->user->name ?? 'Unknown' }}
                                    </td>
                                    <td>{{ $assessment->assessment_date->format('M d, Y') }}</td>
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
                                            <span class="text-muted">None</span>
                                        @endif
                                    </td>
                                    <td>{{ $assessment->assessedBy->name ?? 'Unknown' }}</td>
                                    <td>
                                        @if($assessment->alert_sent)
                                            <span class="badge bg-warning">
                                                <i class="bi bi-bell"></i> Sent
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.risk-assessments.show', $assessment) }}" 
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
