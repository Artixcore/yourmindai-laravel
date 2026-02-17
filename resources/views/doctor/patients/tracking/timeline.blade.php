@extends('layouts.app')

@section('title', 'Activity Timeline')

@section('content')
@php
    $patientName = $patient->full_name ?? $patient->user->name ?? $patient->name ?? 'Patient';
@endphp
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('dashboard')],
            ['label' => 'Patients', 'url' => route('patients.index')],
            ['label' => $patientName, 'url' => route('patients.show', $patient)],
            ['label' => 'Activity Timeline']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">Activity Timeline</h1>
        <p class="text-muted mb-0">Recent activity for {{ $patientName }}</p>
    </div>
</div>

<form method="GET" class="mb-4">
    <div class="row g-2">
        <div class="col-auto">
            <select name="limit" class="form-select">
                <option value="25" {{ ($limit ?? 50) == 25 ? 'selected' : '' }}>25</option>
                <option value="50" {{ ($limit ?? 50) == 50 ? 'selected' : '' }}>50</option>
                <option value="100" {{ ($limit ?? 50) == 100 ? 'selected' : '' }}>100</option>
            </select>
        </div>
        <div class="col-auto">
            <select name="type" class="form-select">
                <option value="">All types</option>
                <option value="feedback" {{ ($activityType ?? '') == 'feedback' ? 'selected' : '' }}>Feedback</option>
                <option value="homework" {{ ($activityType ?? '') == 'homework' ? 'selected' : '' }}>Homework</option>
                <option value="mood" {{ ($activityType ?? '') == 'mood' ? 'selected' : '' }}>Mood</option>
                <option value="practice" {{ ($activityType ?? '') == 'practice' ? 'selected' : '' }}>Practice</option>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">Filter</button>
        </div>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        @if(empty($timeline) || $timeline->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-activity text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 mb-2">No activity</h5>
            <p class="text-muted mb-0">No recent activity found.</p>
        </div>
        @else
        <div class="list-group list-group-flush">
            @foreach($timeline as $item)
            <div class="list-group-item d-flex align-items-start gap-3">
                <div class="flex-shrink-0">
                    <i class="bi bi-{{ $item['icon'] ?? 'circle' }} text-{{ $item['color'] ?? 'secondary' }}"></i>
                </div>
                <div class="flex-grow-1">
                    <span class="badge bg-light text-dark">{{ ucfirst(str_replace('_', ' ', $item['type'] ?? '')) }}</span>
                    <span class="text-muted ms-2">{{ isset($item['date']) ? \Carbon\Carbon::parse($item['date'])->format('M d, Y') : '' }}</span>
                    @if(isset($item['data']))
                    <div class="small mt-1">{{ Str::limit($item['data']->notes ?? $item['data']->title ?? (string) $item['data'], 100) }}</div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Patient
    </a>
</div>
@endsection
