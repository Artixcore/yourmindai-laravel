@extends('layouts.app')

@section('title', 'Patients')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('admin.dashboard')],
            ['label' => 'Patients']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">Patient Management</h1>
        <p class="text-muted mb-0">View and manage all patients across the system</p>
    </div>
    <a href="{{ route('patients.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Add Patient
    </a>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('admin.patients.index') }}" class="row g-3">
            <div class="col-12 col-md-3">
                <label class="form-label small text-muted">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Name, email, or phone..." class="form-control form-control-sm">
            </div>
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
            <div class="col-12 col-md-2">
                <label class="form-label small text-muted">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label small text-muted">Created From</label>
                <input type="date" name="created_from" value="{{ request('created_from') }}" 
                       class="form-control form-control-sm">
            </div>
            <div class="col-12 col-md-2">
                <label class="form-label small text-muted">Created To</label>
                <input type="date" name="created_to" value="{{ request('created_to') }}" 
                       class="form-control form-control-sm">
            </div>
            <div class="col-12 col-md-12 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary btn-sm">Apply Filters</button>
                @if(request()->hasAny(['search', 'doctor_id', 'status', 'created_from', 'created_to']))
                    <a href="{{ route('admin.patients.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Table -->
@if($patients->count() > 0)
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0" style="width: 60px;">Photo</th>
                            <th class="border-0">Name</th>
                            <th class="border-0">Email</th>
                            <th class="border-0">Phone</th>
                            <th class="border-0">Doctor</th>
                            <th class="border-0">Status</th>
                            <th class="border-0">Created</th>
                            <th class="border-0 text-end" style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($patients as $patient)
                            <tr>
                                <td>
                                    @if($patient->photo_path)
                                        <img src="{{ $patient->photo_url }}" alt="{{ $patient->name }}"
                                             class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center"
                                             style="width: 40px; height: 40px;">
                                            <span class="text-primary fw-semibold small">
                                                {{ strtoupper(substr($patient->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-medium">{{ $patient->name }}</div>
                                </td>
                                <td>
                                    <div class="text-muted small">{{ $patient->email }}</div>
                                </td>
                                <td>
                                    <div class="text-muted small">{{ $patient->phone ?? '—' }}</div>
                                </td>
                                <td>
                                    <div class="text-muted small">
                                        {{ $patient->doctor ? ($patient->doctor->name ?? $patient->doctor->email) : '—' }}
                                    </div>
                                </td>
                                <td>
                                    <x-badge :variant="$patient->status === 'active' ? 'success' : 'default'">
                                        {{ ucfirst($patient->status) }}
                                    </x-badge>
                                </td>
                                <td>
                                    <div class="text-muted small">{{ $patient->created_at->format('M d, Y') }}</div>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.patients.show', $patient) }}" 
                                       class="btn btn-sm btn-link text-primary p-1" title="View">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($patients->hasPages())
                <div class="card-footer bg-transparent border-top py-3">
                    {{ $patients->links() }}
                </div>
            @endif
        </div>
    </div>
@else
    <!-- Empty state -->
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 mb-2">No patients found</h5>
            <p class="text-muted mb-0">
                @if(request()->hasAny(['search', 'doctor_id', 'status', 'created_from', 'created_to']))
                    No patients match your filter criteria.
                @else
                    No patients have been created yet.
                @endif
            </p>
        </div>
    </div>
@endif
@endsection
