@extends('layouts.app')

@section('title', $patient->name)

@section('content')
<div class="container-fluid" style="max-width: 1024px;">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <x-breadcrumb :items="[
                ['label' => 'Home', 'url' => route('admin.dashboard')],
                ['label' => 'Patients', 'url' => route('admin.patients.index')],
                ['label' => $patient->name]
            ]" />
            <h1 class="h3 mb-1 fw-semibold">{{ $patient->name }}</h1>
            <p class="text-muted mb-0">Patient Profile - Admin View</p>
        </div>
        <a href="{{ route('admin.patients.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Patients
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div 
            x-data="{ show: true }"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="alert alert-success alert-dismissible fade show mb-4"
            x-init="setTimeout(() => show = false, 5000)"
        >
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Patient Details -->
    <div class="row g-3 mb-4">
        <!-- Main Info Card -->
        <div class="col-12 col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title mb-0 fw-semibold">Patient Information</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-muted">Full Name</label>
                            <p class="fw-medium mb-0">{{ $patient->name }}</p>
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-muted">Email</label>
                            <p class="fw-medium mb-0">{{ $patient->email }}</p>
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-muted">Phone</label>
                            <p class="fw-medium mb-0">{{ $patient->phone ?? '—' }}</p>
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-muted">Status</label>
                            <div>
                                <x-badge :variant="$patient->status === 'active' ? 'success' : 'default'">
                                    {{ ucfirst($patient->status) }}
                                </x-badge>
                            </div>
                        </div>
                        
                        @if($patient->doctor)
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-muted">Assigned Doctor</label>
                            <p class="fw-medium mb-0">
                                {{ $patient->doctor->name ?? $patient->doctor->email }}
                            </p>
                        </div>
                        @endif
                        
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-muted">Created</label>
                            <p class="fw-medium mb-0">{{ $patient->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Photo Card -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title mb-0 fw-semibold">Photo</h5>
                </div>
                <div class="card-body p-4">
                    @if($patient->photo_path)
                        <img 
                            src="{{ $patient->photo_url }}" 
                            alt="{{ $patient->name }}"
                            class="w-100 rounded"
                            style="aspect-ratio: 1; object-fit: cover;"
                        />
                    @else
                        <div class="w-100 rounded bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="aspect-ratio: 1;">
                            <span class="text-primary fw-bold" style="font-size: 4rem;">
                                {{ strtoupper(substr($patient->name, 0, 1)) }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Sessions Summary -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-transparent border-bottom py-3">
            <h5 class="card-title mb-0 fw-semibold">Therapy Sessions Summary</h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <div class="border rounded p-3">
                        <p class="text-muted small mb-1">Total Sessions</p>
                        <h4 class="fw-bold mb-0">{{ $sessionsSummary['total'] }}</h4>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="border rounded p-3">
                        <p class="text-muted small mb-1">Active Sessions</p>
                        <h4 class="fw-bold text-success mb-0">{{ $sessionsSummary['active'] }}</h4>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="border rounded p-3">
                        <p class="text-muted small mb-1">Closed Sessions</p>
                        <h4 class="fw-bold text-muted mb-0">{{ $sessionsSummary['closed'] }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    @if($recentActivity->count() > 0)
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom py-3">
            <h5 class="card-title mb-0 fw-semibold">Recent Activity</h5>
        </div>
        <div class="card-body p-4">
            <div class="d-flex flex-column gap-3">
                @foreach($recentActivity as $session)
                    <div class="border rounded p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <h6 class="fw-semibold mb-0">{{ $session->title }}</h6>
                                    <x-badge :variant="$session->status === 'active' ? 'success' : 'default'">
                                        {{ ucfirst($session->status) }}
                                    </x-badge>
                                </div>
                                @if($session->notes)
                                    <p class="text-muted small mb-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                        {{ Str::limit($session->notes, 100) }}
                                    </p>
                                @endif
                                <div class="d-flex align-items-center gap-3 small text-muted">
                                    <span><i class="bi bi-calendar me-1"></i>{{ $session->created_at->format('M d, Y') }}</span>
                                    <span><i class="bi bi-clock me-1"></i>{{ $session->days->count() }} day{{ $session->days->count() !== 1 ? 's' : '' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
