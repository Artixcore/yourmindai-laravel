@extends('layouts.app')

@section('title', 'Device Actions Timeline')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <x-breadcrumb :items="[
            ['label' => 'Admin', 'url' => route('admin.dashboard')],
            ['label' => 'Device Actions Timeline']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">Device Actions Timeline</h1>
        <p class="text-muted mb-0">View device-related actions across all clients</p>
    </div>
</div>

<form method="GET" class="mb-4 card">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label small">Filter by Client</label>
                <select name="patient_id" class="form-select">
                    <option value="">All clients</option>
                    @foreach($patientProfiles as $pp)
                    <option value="{{ $pp->id }}" {{ $patientId == $pp->id ? 'selected' : '' }}>
                        {{ $pp->full_name ?? $pp->user?->name ?? 'Profile #'.$pp->id }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($actions->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-broadcast text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 mb-2">No device actions</h5>
            <p class="text-muted mb-0">No actions found for the selected filters.</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 ps-4">Date</th>
                        <th class="border-0">Client</th>
                        <th class="border-0">Action</th>
                        <th class="border-0">Device</th>
                        <th class="border-0 pe-4">Note</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($actions as $action)
                    <tr>
                        <td class="ps-4">{{ $action->created_at->format('M d, Y H:i') }}</td>
                        <td>
                            @if($action->patientProfile)
                            {{ $action->patientProfile->full_name ?? $action->patientProfile->user?->name ?? '—' }}
                            @elseif($action->patient)
                            {{ $action->patient->name }}
                            @else
                            —
                            @endif
                        </td>
                        <td>{{ \App\Models\DeviceAction::actionTypes()[$action->action_type] ?? $action->action_type }}</td>
                        <td>{{ $action->device?->device_name ?? '—' }}</td>
                        <td class="pe-4">{{ Str::limit($action->action_note, 60) ?: '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">
            {{ $actions->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
