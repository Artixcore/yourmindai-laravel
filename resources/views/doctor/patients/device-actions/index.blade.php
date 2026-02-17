@extends('layouts.app')

@section('title', 'Device Actions Timeline')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('dashboard')],
            ['label' => 'Patients', 'url' => route('patients.index')],
            ['label' => $patient->name, 'url' => route('patients.show', $patient)],
            ['label' => 'Device Actions Timeline']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">Device Actions Timeline</h1>
        <p class="text-muted mb-0">View {{ $patient->name }}'s device-related actions</p>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($actions->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-broadcast text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 mb-2">No device actions</h5>
            <p class="text-muted mb-0">The client has not logged any device actions yet.</p>
        </div>
        @else
        <div class="list-group list-group-flush">
            @foreach($actions as $action)
            <div class="list-group-item">
                <div class="d-flex justify-content-between">
                    <div>
                        <span class="badge bg-primary me-2">{{ \App\Models\DeviceAction::actionTypes()[$action->action_type] ?? $action->action_type }}</span>
                        @if($action->device)
                        <span class="text-muted">{{ $action->device->device_name }}</span>
                        @endif
                        <div class="small text-muted mt-1">{{ $action->created_at->format('M d, Y H:i') }}</div>
                        @if($action->action_note)
                        <div class="small mt-1">{{ $action->action_note }}</div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <div class="p-3">
            {{ $actions->links() }}
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
