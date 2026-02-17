@extends('layouts.app')

@section('title', 'Tracking Overview')

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-semibold">Tracking Overview</h1>
    <p class="text-muted mb-0">{{ $startDate }} to {{ $endDate }}</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="text-muted mb-1">Mood Logs</h6>
                <h3 class="mb-0">{{ $stats['mood_logs']['total'] ?? 0 }}</h3>
                <small class="text-muted">Avg: {{ number_format($stats['mood_logs']['avg_rating'] ?? 0, 1) }}/10</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="text-muted mb-1">Sleep Logs</h6>
                <h3 class="mb-0">{{ $stats['sleep_logs']['total'] ?? 0 }}</h3>
                <small class="text-muted">Avg: {{ number_format($stats['sleep_logs']['avg_hours'] ?? 0, 1) }} hrs</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body">
                <h6 class="text-muted mb-1">Exercise Logs</h6>
                <h3 class="mb-0">{{ $stats['exercise_logs']['total'] ?? 0 }}</h3>
                <small class="text-muted">Avg: {{ number_format($stats['exercise_logs']['avg_duration'] ?? 0, 0) }} min</small>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <h5 class="mb-3">Quick Links</h5>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('admin.tracking.mood') }}" class="btn btn-outline-primary">Mood Logs</a>
            <a href="{{ route('admin.tracking.sleep') }}" class="btn btn-outline-primary">Sleep Logs</a>
            <a href="{{ route('admin.tracking.exercise') }}" class="btn btn-outline-primary">Exercise Logs</a>
            <a href="{{ route('admin.tracking.compliance') }}" class="btn btn-outline-secondary">Compliance</a>
            <a href="{{ route('admin.tracking.patient-comparison') }}" class="btn btn-outline-secondary">Patient Comparison</a>
        </div>
    </div>
</div>
@endsection
