@extends('layouts.app')

@section('title', 'Parent Permissions')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <x-breadcrumb :items="[['label' => 'Home', 'url' => route('admin.dashboard')], ['label' => 'Parent Permissions']]" />
        <h1 class="h3 mb-1 fw-semibold">Parent Permissions</h1>
        <p class="text-muted mb-0">Manage parent access to child data</p>
    </div>
    <a href="{{ route('admin.parent-permissions.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Add Permission
    </a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('admin.parent-permissions.index') }}" class="row g-3">
            <div class="col-12 col-md-3">
                <label class="form-label small text-muted">Parent</label>
                <select name="parent_id" class="form-select form-select-sm">
                    <option value="">All Parents</option>
                    @foreach($parents as $p)
                    <option value="{{ $p->id }}" {{ request('parent_id') == $p->id ? 'selected' : '' }}>{{ $p->name ?? $p->email }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label small text-muted">Patient</label>
                <select name="patient_id" class="form-select form-select-sm">
                    <option value="">All Patients</option>
                    @foreach($patients as $pt)
                    <option value="{{ $pt->id }}" {{ request('patient_id') == $pt->id ? 'selected' : '' }}>{{ optional($pt->user)->name ?? $pt->full_name ?? 'Patient' }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            </div>
        </form>
    </div>
</div>

@if($permissions->isEmpty())
<div class="card border-0 shadow-sm">
    <div class="card-body text-center py-5">
        <i class="bi bi-shield-check text-muted" style="font-size: 3rem;"></i>
        <p class="text-muted mt-3 mb-0">No parent permissions found.</p>
        <a href="{{ route('admin.parent-permissions.create') }}" class="btn btn-primary mt-3">Add Permission</a>
    </div>
</div>
@else
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="border-0">Parent</th>
                        <th class="border-0">Patient</th>
                        <th class="border-0">Permissions</th>
                        <th class="border-0 text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($permissions as $perm)
                    <tr>
                        <td>{{ optional($perm->parent)->name ?? optional($perm->parent)->email ?? 'N/A' }}</td>
                        <td>{{ optional($perm->patient->user)->name ?? optional($perm->patient)->full_name ?? 'N/A' }}</td>
                        <td>
                            @if($perm->can_view_medical_records)<span class="badge bg-success me-1">Medical</span>@endif
                            @if($perm->can_view_session_notes)<span class="badge bg-info me-1">Sessions</span>@endif
                            @if($perm->can_provide_feedback)<span class="badge bg-primary me-1">Feedback</span>@endif
                            @if($perm->can_view_progress)<span class="badge bg-warning me-1">Progress</span>@endif
                            @if(!$perm->can_view_medical_records && !$perm->can_view_session_notes && !$perm->can_provide_feedback && !$perm->can_view_progress)
                            <span class="text-muted small">None</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.parent-permissions.show', $perm) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i></a>
                            <a href="{{ route('admin.parent-permissions.edit', $perm) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
{{ $permissions->links() }}
@endif
@endsection
