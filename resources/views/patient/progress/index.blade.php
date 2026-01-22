@extends('layouts.app')

@section('title', 'My Progress - Your Mind Aid')

@section('content')
<div class="container-fluid" style="max-width: 1200px;">
    <div class="mb-4">
        <h1 class="h3 fw-bold text-stone-900 mb-2">My Progress</h1>
        <p class="text-stone-600 mb-0">Track your therapy progress and statistics</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted small mb-1">Total Sessions</p>
                            <h3 class="h4 mb-0 fw-bold">{{ $stats['total_sessions'] ?? 0 }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-calendar-check text-primary fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted small mb-1">Task Completion</p>
                            <h3 class="h4 mb-0 fw-bold">{{ $stats['task_completion_rate'] ?? 0 }}%</h3>
                            <p class="text-muted small mb-0">{{ $stats['completed_tasks'] ?? 0 }} of {{ $stats['total_tasks'] ?? 0 }} tasks</p>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-check-circle text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted small mb-1">Average Mood Score</p>
                            <h3 class="h4 mb-0 fw-bold">
                                @if(isset($stats['average_mood_score']) && $stats['average_mood_score'] !== null)
                                    {{ number_format($stats['average_mood_score'], 1) }}
                                @else
                                    N/A
                                @endif
                            </h3>
                            <p class="text-muted small mb-0">Last 30 days</p>
                        </div>
                        <div class="bg-info bg-opacity-10 rounded-3 p-3">
                            <i class="bi bi-emoji-smile text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Mood Trends Chart -->
    @if(isset($stats['mood_trends']) && $stats['mood_trends']->count() > 0)
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-bottom py-3">
            <h5 class="card-title mb-0 fw-semibold">Mood Trends (Last 30 Days)</h5>
        </div>
        <div class="card-body p-4">
            <canvas id="moodChart" height="100"></canvas>
        </div>
    </div>
    @else
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body text-center py-5">
            <i class="bi bi-graph-up text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 mb-2">No mood data available</h5>
            <p class="text-muted mb-0">Start journaling to track your mood trends.</p>
        </div>
    </div>
    @endif

    <!-- Task Progress -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom py-3">
            <h5 class="card-title mb-0 fw-semibold">Task Progress</h5>
        </div>
        <div class="card-body p-4">
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small text-muted">Completion Rate</span>
                    <span class="small fw-semibold">{{ $stats['task_completion_rate'] ?? 0 }}%</span>
                </div>
                <div class="progress" style="height: 20px;">
                    <div class="progress-bar bg-success" role="progressbar" 
                         style="width: {{ $stats['task_completion_rate'] ?? 0 }}%"
                         aria-valuenow="{{ $stats['task_completion_rate'] ?? 0 }}" 
                         aria-valuemin="0" 
                         aria-valuemax="100">
                    </div>
                </div>
            </div>
            <div class="row g-3">
                <div class="col-6">
                    <div class="text-center p-3 border rounded">
                        <p class="text-muted small mb-1">Total Tasks</p>
                        <h4 class="fw-bold mb-0">{{ $stats['total_tasks'] ?? 0 }}</h4>
                    </div>
                </div>
                <div class="col-6">
                    <div class="text-center p-3 border rounded">
                        <p class="text-muted small mb-1">Completed</p>
                        <h4 class="fw-bold text-success mb-0">{{ $stats['completed_tasks'] ?? 0 }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if(isset($stats['mood_trends']) && $stats['mood_trends']->count() > 0)
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    // Mood Trends Chart
    const moodCtx = document.getElementById('moodChart');
    if (moodCtx) {
        const moodData = @json($stats['mood_trends']);
        new Chart(moodCtx, {
            type: 'line',
            data: {
                labels: moodData.map(item => item.date),
                datasets: [{
                    label: 'Mood Score',
                    data: moodData.map(item => item.mood),
                    borderColor: 'rgb(20, 184, 166)',
                    backgroundColor: 'rgba(20, 184, 166, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 10
                    }
                }
            }
        });
    }
</script>
@endif
@endsection
