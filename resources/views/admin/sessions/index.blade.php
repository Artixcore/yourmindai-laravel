@extends('layouts.app')

@section('title', 'Sessions')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('admin.dashboard')],
            ['label' => 'Sessions']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">Therapy Sessions</h1>
        <p class="text-muted mb-0">View and manage all therapy sessions across the system</p>
    </div>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('admin.sessions.index') }}" class="row g-3">
            <div class="col-12 col-md-3">
                <label class="form-label small text-muted">Doctor</label>
                <select name="doctor_id" class="form-select form-select-sm">
                    <option value="">All Doctors</option>
                    @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}" {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>
                            {{ $doctor->name ?? $doctor->email }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label small text-muted">Patient</label>
                <select name="patient_id" class="form-select form-select-sm">
                    <option value="">All Patients</option>
                    @foreach($patients as $patient)
                        <option value="{{ $patient->id }}" {{ request('patient_id') == $patient->id ? 'selected' : '' }}>
                            {{ $patient->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label small text-muted">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
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
            <div class="col-12 col-md-12 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary btn-sm">Apply Filters</button>
                @if(request()->hasAny(['doctor_id', 'patient_id', 'status', 'date_from', 'date_to']))
                    <a href="{{ route('admin.sessions.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Table -->
@if($sessions->count() > 0)
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0">Title</th>
                            <th class="border-0">Patient</th>
                            <th class="border-0">Doctor</th>
                            <th class="border-0">Status</th>
                            <th class="border-0">Days</th>
                            <th class="border-0">Created</th>
                            <th class="border-0 text-end" style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sessions as $session)
                            <tr>
                                <td>
                                    <div class="fw-medium">{{ $session->title }}</div>
                                    @if($session->notes)
                                        <div class="text-muted small" style="display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden;">
                                            {{ Str::limit($session->notes, 50) }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="text-muted small">
                                        {{ $session->patient ? $session->patient->name : '—' }}
                                    </div>
                                </td>
                                <td>
                                    <div class="text-muted small">
                                        {{ $session->doctor ? ($session->doctor->name ?? $session->doctor->email) : '—' }}
                                    </div>
                                </td>
                                <td>
                                    <x-badge :variant="$session->status === 'active' ? 'success' : 'default'">
                                        {{ ucfirst($session->status) }}
                                    </x-badge>
                                </td>
                                <td>
                                    <div class="text-muted small">{{ $session->days->count() }} day{{ $session->days->count() !== 1 ? 's' : '' }}</div>
                                </td>
                                <td>
                                    <div class="text-muted small">{{ $session->created_at->format('M d, Y') }}</div>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.sessions.show', $session) }}" 
                                       class="btn btn-sm btn-link text-primary p-1" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($sessions->hasPages())
                <div class="card-footer bg-transparent border-top py-3">
                    {{ $sessions->links() }}
                </div>
            @endif
        </div>
    </div>
@else
    <!-- Empty state -->
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 mb-2">No sessions found</h5>
            <p class="text-muted mb-0">
                @if(request()->hasAny(['doctor_id', 'patient_id', 'status', 'date_from', 'date_to']))
                    No sessions match your filter criteria.
                @else
                    No therapy sessions have been created yet.
                @endif
            </p>
        </div>
    </div>
@endif
@endsection
