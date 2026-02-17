@extends('layouts.app')

@section('title', 'Tracking Overview')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('dashboard')],
            ['label' => 'Patients', 'url' => route('patients.index')],
            ['label' => $patient->name ?? $patient->full_name ?? 'Patient', 'url' => route('patients.show', $patient)],
            ['label' => 'Tracking Overview']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">Tracking Overview</h1>
        <p class="text-muted mb-0">Mood, sleep, and exercise logs</p>
    </div>
</div>

<form method="GET" class="mb-4">
    <div class="row g-2">
        <div class="col-auto">
            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
        </div>
        <div class="col-auto">
            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </div>
</form>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted mb-2">Mood Entries</h6>
                <h3 class="fw-bold mb-0">{{ $stats['mood_entries'] ?? $stats['mood_logs'] ?? 0 }}</h3>
                @if(isset($stats['avg_mood_rating']) && $stats['avg_mood_rating'])
                <small class="text-muted">Avg: {{ round($stats['avg_mood_rating'], 1) }}/10</small>
                @endif
                <div class="mt-2">
                    <a href="{{ route('patients.tracking.mood', $patient) }}" class="btn btn-sm btn-outline-primary">View</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted mb-2">Sleep Entries</h6>
                <h3 class="fw-bold mb-0">{{ $stats['sleep_entries'] ?? $stats['sleep_logs'] ?? 0 }}</h3>
                @if(isset($stats['avg_sleep_hours']) && $stats['avg_sleep_hours'])
                <small class="text-muted">Avg: {{ round($stats['avg_sleep_hours'], 1) }} hrs</small>
                @endif
                <div class="mt-2">
                    <a href="{{ route('patients.tracking.sleep', $patient) }}" class="btn btn-sm btn-outline-info">View</a>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted mb-2">Exercise Entries</h6>
                <h3 class="fw-bold mb-0">{{ $stats['exercise_entries'] ?? $stats['exercise_logs'] ?? 0 }}</h3>
                @if(isset($stats['total_exercise_minutes']) && $stats['total_exercise_minutes'])
                <small class="text-muted">Total: {{ round($stats['total_exercise_minutes'] / 60, 1) }} hrs</small>
                @endif
                <div class="mt-2">
                    <a href="{{ route('patients.tracking.exercise', $patient) }}" class="btn btn-sm btn-outline-success">View</a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Patient
    </a>
</div>
@endsection
