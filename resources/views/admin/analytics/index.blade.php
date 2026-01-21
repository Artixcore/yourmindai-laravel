@extends('layouts.app')

@section('title', 'Analytics')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('admin.dashboard')],
            ['label' => 'Analytics']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">Analytics Dashboard</h1>
        <p class="text-muted mb-0">Clinic-wide statistics and insights</p>
    </div>
</div>

<!-- KPIs -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small mb-1">Total Doctors</p>
                        <h3 class="h4 mb-0 fw-bold">{{ $totalDoctors }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                        <i class="bi bi-person-badge text-primary fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small mb-1">Total Patients</p>
                        <h3 class="h4 mb-0 fw-bold">{{ $totalPatients }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded-3 p-3">
                        <i class="bi bi-people text-success fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small mb-1">Active Sessions</p>
                        <h3 class="h4 mb-0 fw-bold">{{ $activeSessions }}</h3>
                    </div>
                    <div class="bg-info bg-opacity-10 rounded-3 p-3">
                        <i class="bi bi-calendar-check text-info fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small mb-1">Attention Flags</p>
                        <h3 class="h4 mb-0 fw-bold">{{ $attentionFlags }}</h3>
                    </div>
                    <div class="bg-danger bg-opacity-10 rounded-3 p-3">
                        <i class="bi bi-exclamation-triangle text-danger fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row g-3 mb-4">
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom py-3">
                <h5 class="card-title mb-0 fw-semibold">Sessions Over Time</h5>
            </div>
            <div class="card-body p-4">
                <canvas id="sessionsChart" height="100"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom py-3">
                <h5 class="card-title mb-0 fw-semibold">Active vs Closed Sessions</h5>
            </div>
            <div class="card-body p-4">
                <canvas id="sessionsRatioChart" height="100"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent border-bottom py-3">
        <h5 class="card-title mb-0 fw-semibold">Per-Doctor Caseload Distribution</h5>
    </div>
    <div class="card-body p-4">
        <canvas id="caseloadChart" height="50"></canvas>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Sessions Over Time
    const sessionsCtx = document.getElementById('sessionsChart').getContext('2d');
    new Chart(sessionsCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($sessionsOverTime->pluck('month')) !!},
            datasets: [{
                label: 'Sessions',
                data: {!! json_encode($sessionsOverTime->pluck('count')) !!},
                borderColor: 'rgb(20, 184, 166)',
                backgroundColor: 'rgba(20, 184, 166, 0.1)',
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
    
    // Active vs Closed Ratio
    const ratioCtx = document.getElementById('sessionsRatioChart').getContext('2d');
    new Chart(ratioCtx, {
        type: 'doughnut',
        data: {
            labels: ['Active', 'Closed'],
            datasets: [{
                data: [{{ $activeSessions }}, {{ $closedSessions }}],
                backgroundColor: ['rgb(16, 185, 129)', 'rgb(156, 163, 175)']
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true
        }
    });
    
    // Caseload Distribution
    const caseloadCtx = document.getElementById('caseloadChart').getContext('2d');
    new Chart(caseloadCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($doctorCaseloads->pluck('name')) !!},
            datasets: [{
                label: 'Patient Count',
                data: {!! json_encode($doctorCaseloads->pluck('count')) !!},
                backgroundColor: 'rgb(20, 184, 166)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
</script>
@endsection
