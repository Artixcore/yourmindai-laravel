@extends('layouts.app')

@section('title', 'Device - ' . ($device->device_name ?? 'Details'))

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('dashboard')],
            ['label' => 'Patients', 'url' => route('patients.index')],
            ['label' => optional($patient->user)->name ?? optional($patient)->full_name ?? 'Patient', 'url' => $patient->getPatient() ? route('patients.show', $patient->getPatient()) : route('patients.index')],
            ['label' => 'Devices', 'url' => route('patients.devices.index', $patient)],
            ['label' => $device->device_name ?? 'Device']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">{{ $device->device_name ?? 'Device Details' }}</h1>
        <p class="text-muted mb-0">Device information and activity</p>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h5 class="fw-semibold mb-3">Device Information</h5>
        <dl class="row mb-0">
            <dt class="col-sm-3">Device Name</dt>
            <dd class="col-sm-9">{{ $device->device_name ?? '—' }}</dd>

            <dt class="col-sm-3">Type</dt>
            <dd class="col-sm-9">{{ ucfirst($device->device_type ?? '—') }}</dd>

            <dt class="col-sm-3">Identifier</dt>
            <dd class="col-sm-9"><code class="small">{{ $device->device_identifier ?? '—' }}</code></dd>

            <dt class="col-sm-3">OS</dt>
            <dd class="col-sm-9">{{ $device->os_type ?? '—' }} {{ $device->os_version ?? '' }}</dd>

            <dt class="col-sm-3">Source</dt>
            <dd class="col-sm-9">{{ ucfirst(str_replace('_', ' ', $device->device_source ?? '—')) }}</dd>

            <dt class="col-sm-3">Registered</dt>
            <dd class="col-sm-9">{{ $device->registered_at?->format('M d, Y H:i') ?? '—' }}</dd>

            <dt class="col-sm-3">Last Active</dt>
            <dd class="col-sm-9">{{ $device->last_active_at?->format('M d, Y H:i') ?? '—' }}</dd>

            @if($device->notes)
            <dt class="col-sm-3">Notes</dt>
            <dd class="col-sm-9">{{ $device->notes }}</dd>
            @endif
        </dl>
    </div>
</div>

@if(isset($activityInfo))
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <h5 class="fw-semibold mb-3">Activity</h5>
        <p class="mb-0">Days since registration: {{ $activityInfo['days_since_registration'] ?? '—' }}</p>
        @if(isset($activityInfo['days_since_last_active']) && $activityInfo['days_since_last_active'] !== null)
        <p class="mb-0">Days since last active: {{ $activityInfo['days_since_last_active'] }}</p>
        @endif
    </div>
</div>
@endif

<div class="d-flex gap-2">
    <a href="{{ route('patients.devices.index', $patient) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Devices
    </a>
    <a href="{{ $patient->getPatient() ? route('patients.show', $patient->getPatient()) : route('patients.index') }}" class="btn btn-outline-primary">Back to Patient</a>
</div>
@endsection
