@extends('layouts.app')

@section('title', $staff->full_name ?? $staff->name)

@section('content')
<div class="container-fluid" style="max-width: 1024px;">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <x-breadcrumb :items="[
                ['label' => 'Home', 'url' => route('admin.dashboard')],
                ['label' => 'Staff', 'url' => route('admin.staff.index')],
                ['label' => $staff->full_name ?? $staff->name]
            ]" />
            <h1 class="h3 mb-1 fw-semibold">{{ $staff->full_name ?? $staff->name }}</h1>
            <p class="text-muted mb-0">Staff Member Details</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.staff.edit', $staff) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-2"></i>Edit
            </a>
            <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Back to Staff
            </a>
        </div>
    </div>

    <!-- Staff Information -->
    <div class="row g-3 mb-4">
        <!-- Main Info Card -->
        <div class="col-12 col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title mb-0 fw-semibold">Personal Information</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-muted">Full Name</label>
                            <p class="fw-medium mb-0">{{ $staff->full_name ?? $staff->name ?? '—' }}</p>
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-muted">Username</label>
                            <p class="fw-medium mb-0">{{ $staff->username ?? '—' }}</p>
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-muted">Email</label>
                            <p class="fw-medium mb-0">
                                <a href="mailto:{{ $staff->email }}">{{ $staff->email }}</a>
                            </p>
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-muted">Phone</label>
                            <p class="fw-medium mb-0">{{ $staff->phone ?? '—' }}</p>
                        </div>
                        
                        @if($staff->address)
                        <div class="col-12">
                            <label class="form-label small text-muted">Address</label>
                            <p class="fw-medium mb-0">{{ $staff->address }}</p>
                        </div>
                        @endif
                        
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-muted">Role</label>
                            <div>
                                <x-badge :variant="$staff->role === 'doctor' ? 'primary' : 'info'">
                                    {{ ucfirst($staff->role) }}
                                </x-badge>
                            </div>
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-muted">Status</label>
                            <div>
                                <x-badge :variant="$staff->status === 'active' ? 'success' : 'secondary'">
                                    {{ ucfirst($staff->status) }}
                                </x-badge>
                            </div>
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-muted">Created</label>
                            <p class="fw-medium mb-0">{{ $staff->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Card -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title mb-0 fw-semibold">Statistics</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex flex-column gap-3">
                        <div>
                            <p class="text-muted small mb-1">Papers</p>
                            <h4 class="fw-bold mb-0">{{ $staff->papers->count() }}</h4>
                        </div>
                        <div>
                            <p class="text-muted small mb-1">Patients</p>
                            <h4 class="fw-bold mb-0">{{ $staff->patients->count() }}</h4>
                        </div>
                        <div>
                            <p class="text-muted small mb-1">Sessions</p>
                            <h4 class="fw-bold mb-0">{{ $staff->sessions->count() }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Related Information -->
    @if($staff->papers->count() > 0 || $staff->patients->count() > 0 || $staff->sessions->count() > 0)
    <div class="row g-3">
        @if($staff->papers->count() > 0)
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title mb-0 fw-semibold">Recent Papers</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex flex-column gap-2">
                        @foreach($staff->papers->take(5) as $paper)
                            <div class="border rounded p-2">
                                <div class="fw-medium small">{{ $paper->title }}</div>
                                <div class="text-muted small">{{ $paper->created_at->format('M d, Y') }}</div>
                            </div>
                        @endforeach
                        @if($staff->papers->count() > 5)
                            <div class="text-muted small text-center">+{{ $staff->papers->count() - 5 }} more</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($staff->patients->count() > 0)
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title mb-0 fw-semibold">Recent Patients</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex flex-column gap-2">
                        @foreach($staff->patients->take(5) as $patient)
                            <div class="border rounded p-2">
                                <div class="fw-medium small">
                                    <a href="{{ route('admin.patients.show', $patient) }}" class="text-decoration-none">
                                        {{ $patient->name }}
                                    </a>
                                </div>
                                <div class="text-muted small">{{ $patient->created_at->format('M d, Y') }}</div>
                            </div>
                        @endforeach
                        @if($staff->patients->count() > 5)
                            <div class="text-muted small text-center">+{{ $staff->patients->count() - 5 }} more</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif

        @if($staff->sessions->count() > 0)
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title mb-0 fw-semibold">Recent Sessions</h5>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex flex-column gap-2">
                        @foreach($staff->sessions->take(5) as $session)
                            <div class="border rounded p-2">
                                <div class="fw-medium small">{{ $session->title }}</div>
                                <div class="text-muted small">{{ $session->created_at->format('M d, Y') }}</div>
                            </div>
                        @endforeach
                        @if($staff->sessions->count() > 5)
                            <div class="text-muted small text-center">+{{ $staff->sessions->count() - 5 }} more</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif
</div>
@endsection
