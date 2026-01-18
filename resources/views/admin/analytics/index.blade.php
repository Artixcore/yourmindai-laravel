@extends('layouts.app')

@section('title', 'Analytics - Your Mind Aid')

@section('content')
<div class="mb-4">
    <h1 class="h2 fw-bold text-stone-900">Analytics Dashboard</h1>
    <p class="text-stone-600 mt-2 mb-0">Clinic-wide statistics and insights</p>
</div>

<!-- KPIs -->
<div class="row g-4 mb-5">
    <div class="col-12 col-md-6 col-lg-3">
        <x-card>
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="small text-stone-600 mb-0">Total Doctors</p>
                    <p class="h3 fw-bold text-stone-900 mt-2 mb-0">{{ $totalDoctors }}</p>
                </div>
                <div class="bg-teal-100 rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <svg style="width: 24px; height: 24px;" class="text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            </div>
        </x-card>
    </div>
    
    <div class="col-12 col-md-6 col-lg-3">
        <x-card>
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="small text-stone-600 mb-0">Total Patients</p>
                    <p class="h3 fw-bold text-stone-900 mt-2 mb-0">{{ $totalPatients }}</p>
                </div>
                <div class="bg-primary bg-opacity-25 rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <svg style="width: 24px; height: 24px;" class="text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </x-card>
    </div>
    
    <div class="col-12 col-md-6 col-lg-3">
        <x-card>
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="small text-stone-600 mb-0">Active Sessions</p>
                    <p class="h3 fw-bold text-stone-900 mt-2 mb-0">{{ $activeSessions }}</p>
                </div>
                <div class="bg-emerald-100 rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <svg style="width: 24px; height: 24px;" class="text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </x-card>
    </div>
    
    <div class="col-12 col-md-6 col-lg-3">
        <x-card>
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="small text-stone-600 mb-0">Attention Flags</p>
                    <p class="h3 fw-bold text-stone-900 mt-2 mb-0">{{ $attentionFlags }}</p>
                </div>
                <div class="bg-red-100 rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <svg style="width: 24px; height: 24px;" class="text-red-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
        </x-card>
    </div>
</div>

<!-- Charts -->
<div class="row g-4 mb-5">
    <div class="col-12 col-lg-6">
        <x-card>
            <h2 class="h5 font-semibold text-stone-900 mb-3">Sessions Over Time</h2>
            <canvas id="sessionsChart" height="100"></canvas>
        </x-card>
    </div>
    
    <div class="col-12 col-lg-6">
        <x-card>
            <h2 class="h5 font-semibold text-stone-900 mb-3">Active vs Closed Sessions</h2>
            <canvas id="sessionsRatioChart" height="100"></canvas>
        </x-card>
    </div>
</div>

<x-card>
    <h2 class="h5 font-semibold text-stone-900 mb-3">Per-Doctor Caseload Distribution</h2>
    <canvas id="caseloadChart" height="50"></canvas>
</x-card>

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
