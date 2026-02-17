@extends('layouts.app')

@section('title', 'Compliance Report')

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
            ['label' => 'Compliance Report']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">Compliance Report</h1>
        <p class="text-muted mb-0">Compliance metrics for {{ $patientName }}</p>
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
                <h6 class="text-muted mb-2">Homework Compliance</h6>
                <h3 class="fw-bold mb-0">{{ $homeworkStats['compliance_rate'] ?? 0 }}%</h3>
                <small class="text-muted">{{ $homeworkStats['completed'] ?? 0 }}/{{ $homeworkStats['assigned'] ?? 0 }} completed</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted mb-2">Mood Tracking</h6>
                <h3 class="fw-bold mb-0">{{ $trackingCompliance['mood']['rate'] ?? 0 }}%</h3>
                <small class="text-muted">{{ $trackingCompliance['mood']['days_logged'] ?? 0 }} days logged</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted mb-2">Sleep Tracking</h6>
                <h3 class="fw-bold mb-0">{{ $trackingCompliance['sleep']['rate'] ?? 0 }}%</h3>
                <small class="text-muted">{{ $trackingCompliance['sleep']['days_logged'] ?? 0 }} days logged</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted mb-2">Exercise Tracking</h6>
                <h3 class="fw-bold mb-0">{{ $trackingCompliance['exercise']['rate'] ?? 0 }}%</h3>
                <small class="text-muted">{{ $trackingCompliance['exercise']['days_logged'] ?? 0 }} days logged</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <h6 class="text-muted mb-2">Practice Progression</h6>
                <h3 class="fw-bold mb-0">{{ $practiceStats['compliance_rate'] ?? 0 }}%</h3>
                <small class="text-muted">{{ $practiceStats['completed'] ?? 0 }}/{{ $practiceStats['total'] ?? 0 }} completed</small>
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
