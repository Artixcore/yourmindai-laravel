@extends('client.layout')

@section('title', 'My Progress - Your Mind Aid')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">My Progress</h4>
    <p class="text-muted mb-0 small">Track your therapy progress and statistics</p>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-3">
    <div class="col-6">
        <div class="card">
            <div class="card-body text-center">
                <p class="text-muted small mb-1">Total Sessions</p>
                <h3 class="h4 mb-0 fw-bold">{{ $stats['total_sessions'] ?? 0 }}</h3>
            </div>
        </div>
    </div>
    
    <div class="col-6">
        <div class="card">
            <div class="card-body text-center">
                <p class="text-muted small mb-1">Task Completion</p>
                <h3 class="h4 mb-0 fw-bold">{{ $stats['task_completion_rate'] ?? 0 }}%</h3>
                <p class="text-muted small mb-0">{{ $stats['completed_tasks'] ?? 0 }}/{{ $stats['total_tasks'] ?? 0 }}</p>
            </div>
        </div>
    </div>
    
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center">
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
        </div>
    </div>
</div>

<!-- Mood Trends Chart -->
@if(isset($stats['mood_trends']) && $stats['mood_trends']->count() > 0)
<div class="card mb-3">
    <div class="card-header bg-white border-0">
        <h6 class="mb-0 fw-semibold">Mood Trends (Last 30 Days)</h6>
    </div>
    <div class="card-body">
        <canvas id="moodChart" height="200"></canvas>
    </div>
</div>
@else
<div class="card mb-3">
    <div class="card-body text-center py-4">
        <i class="bi bi-graph-up text-muted fs-1 mb-3"></i>
        <h6 class="mb-2">No mood data available</h6>
        <p class="text-muted small mb-0">Start journaling to track your mood trends.</p>
    </div>
</div>
@endif

<!-- Task Progress -->
<div class="card">
    <div class="card-header bg-white border-0">
        <h6 class="mb-0 fw-semibold">Task Progress</h6>
    </div>
    <div class="card-body">
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
        <div class="row g-2">
            <div class="col-6">
                <div class="text-center p-2 border rounded">
                    <p class="text-muted small mb-1">Total Tasks</p>
                    <h5 class="fw-bold mb-0">{{ $stats['total_tasks'] ?? 0 }}</h5>
                </div>
            </div>
            <div class="col-6">
                <div class="text-center p-2 border rounded">
                    <p class="text-muted small mb-1">Completed</p>
                    <h5 class="fw-bold text-success mb-0">{{ $stats['completed_tasks'] ?? 0 }}</h5>
                </div>
            </div>
        </div>
    </div>
</div>

@if(isset($stats['mood_trends']) && $stats['mood_trends']->count() > 0)
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
    'use strict';
    
    try {
        const moodCtx = document.getElementById('moodChart');
        if (!moodCtx) return;
        
        const moodDataRaw = @json($stats['mood_trends']);
        if (!moodDataRaw || !Array.isArray(moodDataRaw) || moodDataRaw.length === 0) return;
        
        // Validate and process mood data
        const moodData = moodDataRaw.filter(item => {
            return item && typeof item === 'object' && 
                   item.hasOwnProperty('date') && 
                   item.hasOwnProperty('mood') &&
                   typeof item.mood === 'number';
        });
        
        if (moodData.length === 0) return;
        
        // Check if Chart is available
        if (typeof Chart === 'undefined') {
            console.warn('Chart.js library not loaded');
            return;
        }
        
        // Create chart with error handling
        try {
            new Chart(moodCtx, {
                type: 'line',
                data: {
                    labels: moodData.map(item => item.date || ''),
                    datasets: [{
                        label: 'Mood Score',
                        data: moodData.map(item => item.mood || 0),
                        borderColor: 'rgb(102, 126, 234)',
                        backgroundColor: 'rgba(102, 126, 234, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
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
        } catch (chartError) {
            // Silently fail - chart will not be displayed
        }
    } catch (e) {
        // Silently fail - chart will not be displayed
    }
})();
</script>
@endpush
@endif
@endsection
