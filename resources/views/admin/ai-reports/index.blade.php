@extends('layouts.app')

@section('title', 'AI Reports')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('admin.dashboard')],
            ['label' => 'AI Reports']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">AI Reports</h1>
        <p class="text-muted mb-0">View and manage AI-generated reports</p>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('admin.ai-reports.index') }}" class="row g-3">
            <div class="col-12 col-md-3">
                <label class="form-label small text-muted">Scope</label>
                <select name="scope" class="form-select form-select-sm">
                    <option value="">All Scopes</option>
                    <option value="patient" {{ request('scope') == 'patient' ? 'selected' : '' }}>Patient</option>
                    <option value="doctor" {{ request('scope') == 'doctor' ? 'selected' : '' }}>Doctor</option>
                    <option value="clinic" {{ request('scope') == 'clinic' ? 'selected' : '' }}>Clinic</option>
                    <option value="session" {{ request('scope') == 'session' ? 'selected' : '' }}>Session</option>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label small text-muted">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="queued" {{ request('status') == 'queued' ? 'selected' : '' }}>Queued</option>
                    <option value="running" {{ request('status') == 'running' ? 'selected' : '' }}>Running</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="failed" {{ request('status') == 'failed' ? 'selected' : '' }}>Failed</option>
                </select>
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label small text-muted">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" 
                       class="form-control form-control-sm">
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label small text-muted">Date To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" 
                       class="form-control form-control-sm">
            </div>
            <div class="col-12 col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
            </div>
            <div class="col-12 col-md-12 d-flex align-items-end gap-2">
                @if(request()->hasAny(['scope', 'status', 'date_from', 'date_to']))
                    <a href="{{ route('admin.ai-reports.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Table -->
@if($reports->count() > 0)
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0">Scope</th>
                            <th class="border-0">Subject</th>
                            <th class="border-0">Status</th>
                            <th class="border-0">Model</th>
                            <th class="border-0">Requested By</th>
                            <th class="border-0">Created</th>
                            <th class="border-0 text-end" style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $report)
                            <tr>
                                <td>
                                    <x-badge :variant="$report->scope === 'patient' ? 'success' : ($report->scope === 'doctor' ? 'primary' : ($report->scope === 'clinic' ? 'info' : 'default'))">
                                        {{ ucfirst($report->scope) }}
                                    </x-badge>
                                </td>
                                <td>
                                    <div class="fw-medium">
                                        @if($report->patient)
                                            {{ $report->patient->name }}
                                        @elseif($report->doctor)
                                            {{ $report->doctor->name ?? $report->doctor->email }}
                                        @elseif($report->scope === 'clinic')
                                            Clinic Report
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <x-badge :variant="$report->status === 'completed' ? 'success' : ($report->status === 'failed' ? 'danger' : ($report->status === 'running' ? 'warning' : 'default'))">
                                        {{ ucfirst($report->status) }}
                                    </x-badge>
                                </td>
                                <td>
                                    <div class="text-muted small">{{ $report->model ?? 'N/A' }}</div>
                                </td>
                                <td>
                                    <div class="text-muted small">
                                        {{ $report->requestedBy ? ($report->requestedBy->name ?? $report->requestedBy->email) : 'N/A' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="text-muted small">{{ $report->created_at->format('M d, Y') }}</div>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.ai-reports.show', $report) }}" 
                                       class="btn btn-sm btn-link text-primary p-1" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($reports->hasPages())
                <div class="card-footer bg-transparent border-top py-3">
                    {{ $reports->links() }}
                </div>
            @endif
        </div>
    </div>
@else
    <!-- Empty state -->
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="bi bi-file-earmark-text text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 mb-2">No reports found</h5>
            <p class="text-muted mb-0">
                @if(request()->hasAny(['scope', 'status', 'date_from', 'date_to']))
                    No reports match your filter criteria.
                @else
                    No AI reports have been generated yet.
                @endif
            </p>
        </div>
    </div>
@endif
@endsection
