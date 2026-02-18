@extends('layouts.app')

@section('title', 'Devices - ' . (optional($patient->user)->name ?? optional($patient)->full_name ?? 'Patient'))

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('dashboard')],
            ['label' => 'Patients', 'url' => route('patients.index')],
            ['label' => optional($patient->user)->name ?? optional($patient)->full_name ?? 'Patient', 'url' => $patient->getPatient() ? route('patients.show', $patient->getPatient()) : route('patients.index')],
            ['label' => 'Devices']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">Patient Devices</h1>
        <p class="text-muted mb-0">View registered devices for {{ optional($patient->user)->name ?? optional($patient)->full_name ?? 'patient' }}</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <p class="text-muted small mb-1">Total Devices</p>
                <h4 class="fw-bold mb-0">{{ $stats['total'] ?? 0 }}</h4>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center">
                <p class="text-muted small mb-1">Active (7 days)</p>
                <h4 class="fw-bold text-success mb-0">{{ $stats['active'] ?? 0 }}</h4>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($devices->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-phone text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 mb-2">No devices registered</h5>
            <p class="text-muted mb-0">The client has not registered any devices yet.</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>Device</th>
                        <th>Type</th>
                        <th>OS</th>
                        <th>Last Active</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($devices as $d)
                    <tr>
                        <td>{{ $d->device_name ?? '—' }}</td>
                        <td>{{ ucfirst($d->device_type ?? '—') }}</td>
                        <td>{{ $d->os_type ?? '—' }} {{ $d->os_version ?? '' }}</td>
                        <td>{{ $d->last_active_at?->diffForHumans() ?? '—' }}</td>
                        <td class="text-end">
                            <a href="{{ route('patients.devices.show', [$patient, $d]) }}" class="btn btn-sm btn-outline-primary">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

<div class="mt-3">
    <a href="{{ $patient->getPatient() ? route('patients.show', $patient->getPatient()) : route('patients.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Patient
    </a>
</div>
@endsection
