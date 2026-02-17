@extends('layouts.app')

@section('title', 'Sleep Hygiene Logs')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <x-breadcrumb :items="[
            ['label' => 'Admin', 'url' => route('admin.dashboard')],
            ['label' => 'Sleep Hygiene Logs']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">Sleep Hygiene Logs</h1>
        <p class="text-muted mb-0">View sleep hygiene checklist completion across all clients</p>
    </div>
</div>

<form method="GET" class="mb-4 card">
    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-3">
                <label class="form-label small">Patient (Profile)</label>
                <select name="patient_id" class="form-select">
                    <option value="">All clients</option>
                    @foreach($patientProfiles as $pp)
                    <option value="{{ $pp->id }}" {{ $patientId == $pp->id ? 'selected' : '' }}>
                        {{ $pp->full_name ?? $pp->user?->name ?? 'Profile #'.$pp->id }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">From</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small">To</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </div>
    </div>
</form>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($logs->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-moon-stars text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 mb-2">No sleep hygiene logs</h5>
            <p class="text-muted mb-0">No logs found for the selected filters.</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 ps-4">Date</th>
                        <th class="border-0">Client</th>
                        <th class="border-0">Item</th>
                        <th class="border-0">Completed</th>
                        <th class="border-0 pe-4">Notes</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    <tr>
                        <td class="ps-4">{{ $log->log_date->format('M d, Y') }}</td>
                        <td>
                            @if($log->patientProfile)
                            {{ $log->patientProfile->full_name ?? $log->patientProfile->user?->name ?? '—' }}
                            @elseif($log->patient)
                            {{ $log->patient->name }}
                            @else
                            —
                            @endif
                        </td>
                        <td>{{ $log->item->label ?? '—' }}</td>
                        <td>
                            @if($log->is_completed)
                            <span class="badge bg-success">Yes</span>
                            @else
                            <span class="badge bg-secondary">No</span>
                            @endif
                        </td>
                        <td class="pe-4">{{ Str::limit($log->notes, 80) ?: '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">
            {{ $logs->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
