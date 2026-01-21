@extends('layouts.app')

@section('title', 'Staff Management')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('admin.dashboard')],
            ['label' => 'Staff']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">Staff Management</h1>
        <p class="text-muted mb-0">Manage doctors and assistants</p>
    </div>
    <a href="{{ route('admin.staff.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Add Staff Member
    </a>
</div>

<!-- Filters -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('admin.staff.index') }}" class="row g-3">
            <div class="col-12 col-md-4">
                <label class="form-label small text-muted">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Name, email, or username..." class="form-control form-control-sm">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label small text-muted">Role</label>
                <select name="role" class="form-select form-select-sm">
                    <option value="">All Roles</option>
                    <option value="doctor" {{ request('role') == 'doctor' ? 'selected' : '' }}>Doctor</option>
                    <option value="assistant" {{ request('role') == 'assistant' ? 'selected' : '' }}>Assistant</option>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label small text-muted">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<!-- Table -->
@if($staff->count() > 0)
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0">Name</th>
                            <th class="border-0">Email</th>
                            <th class="border-0">Role</th>
                            <th class="border-0">Status</th>
                            <th class="border-0 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($staff as $member)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div>
                                            <p class="mb-0 fw-semibold">{{ $member->full_name ?? $member->name ?? 'N/A' }}</p>
                                            <small class="text-muted">{{ $member->username ?? '' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $member->email }}</td>
                                <td>
                                    <span class="badge bg-{{ $member->role === 'doctor' ? 'primary' : 'info' }}">
                                        {{ ucfirst($member->role) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $member->status === 'active' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($member->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.staff.show', $member) }}" class="btn btn-outline-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.staff.edit', $member) }}" class="btn btn-outline-secondary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if($member->id !== auth()->id())
                                            <form action="{{ route('admin.staff.toggle-status', $member) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-{{ $member->status === 'active' ? 'warning' : 'success' }}">
                                                    <i class="bi bi-{{ $member->status === 'active' ? 'x-circle' : 'check-circle' }}"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0">
            {{ $staff->links() }}
        </div>
    </div>
@else
    <div class="card border-0 shadow-sm">
        <div class="card-body p-5 text-center">
            <i class="bi bi-people fs-1 text-muted mb-3"></i>
            <p class="text-muted mb-0">No staff members found.</p>
        </div>
    </div>
@endif
@endsection
