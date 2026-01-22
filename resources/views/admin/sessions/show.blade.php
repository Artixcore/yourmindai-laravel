@extends('layouts.app')

@section('title', $session->title)

@section('content')
<div class="container-fluid" style="max-width: 1024px;">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <x-breadcrumb :items="[
                ['label' => 'Home', 'url' => route('admin.dashboard')],
                ['label' => 'Sessions', 'url' => route('admin.sessions.index')],
                ['label' => $session->title]
            ]" />
            <h1 class="h3 mb-1 fw-semibold">{{ $session->title }}</h1>
            <p class="text-muted mb-0">Session Details - Admin View</p>
        </div>
        <a href="{{ route('admin.sessions.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Sessions
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

    <!-- Session Information -->
    <div class="row g-3 mb-4">
        <!-- Main Info Card -->
        <div class="col-12 col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title mb-0 fw-semibold">Session Information</h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small text-muted">Title</label>
                            <p class="fw-medium mb-0">{{ $session->title }}</p>
                        </div>
                        
                        @if($session->notes)
                        <div class="col-12">
                            <label class="form-label small text-muted">Notes</label>
                            <p class="mb-0" style="white-space: pre-wrap;">{{ $session->notes }}</p>
                        </div>
                        @endif
                        
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-muted">Status</label>
                            <div>
                                <x-badge :variant="$session->status === 'active' ? 'success' : 'default'">
                                    {{ ucfirst($session->status) }}
                                </x-badge>
                            </div>
                        </div>
                        
                        <div class="col-12 col-md-6">
                            <label class="form-label small text-muted">Created</label>
                            <p class="fw-medium mb-0">{{ $session->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Related Info Card -->
        <div class="col-12 col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-bottom py-3">
                    <h5 class="card-title mb-0 fw-semibold">Related Information</h5>
                </div>
                <div class="card-body p-4">
                    @if($session->patient)
                    <div class="mb-3">
                        <label class="form-label small text-muted">Patient</label>
                        <p class="fw-medium mb-0">
                            <a href="{{ route('admin.patients.show', $session->patient) }}" class="text-decoration-none">
                                {{ $session->patient->name }}
                            </a>
                        </p>
                    </div>
                    @endif
                    
                    @if($session->doctor)
                    <div class="mb-3">
                        <label class="form-label small text-muted">Doctor</label>
                        <p class="fw-medium mb-0">
                            {{ $session->doctor->name ?? $session->doctor->email }}
                        </p>
                    </div>
                    @endif
                    
                    <div>
                        <label class="form-label small text-muted">Day Entries</label>
                        <p class="fw-medium mb-0">{{ $session->days->count() }} day{{ $session->days->count() !== 1 ? 's' : '' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Day Entries -->
    @if($session->days->count() > 0)
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent border-bottom py-3">
            <h5 class="card-title mb-0 fw-semibold">Day Entries</h5>
        </div>
        <div class="card-body p-4">
            <div class="d-flex flex-column gap-4">
                @foreach($session->days->sortByDesc('day_date') as $day)
                    <div class="position-relative ps-4 pb-4 border-start border-stone-200">
                        <!-- Timeline dot -->
                        <div class="position-absolute start-0 top-0 rounded-circle bg-primary" style="width: 16px; height: 16px; transform: translateX(-50%);"></div>
                        
                        <!-- Day content -->
                        <div class="bg-stone-50 rounded p-3">
                            <h6 class="fw-semibold text-stone-900 mb-3">
                                {{ $day->day_date->format('F d, Y') }}
                            </h6>

                            <div class="row g-3">
                                @if($day->symptoms)
                                    <div class="col-12 col-md-4">
                                        <h6 class="small font-medium text-stone-500 mb-1">Symptoms</h6>
                                        <p class="text-stone-700 small mb-0" style="white-space: pre-wrap;">{{ $day->symptoms }}</p>
                                    </div>
                                @endif
                                @if($day->alerts)
                                    <div class="col-12 col-md-4">
                                        <h6 class="small font-medium text-stone-500 mb-1">Alerts</h6>
                                        <p class="text-stone-700 small mb-0" style="white-space: pre-wrap;">{{ $day->alerts }}</p>
                                    </div>
                                @endif
                                @if($day->tasks)
                                    <div class="col-12 col-md-4">
                                        <h6 class="small font-medium text-stone-500 mb-1">Tasks</h6>
                                        <p class="text-stone-700 small mb-0" style="white-space: pre-wrap;">{{ $day->tasks }}</p>
                                    </div>
                                @endif
                            </div>

                            @if(!$day->symptoms && !$day->alerts && !$day->tasks)
                                <p class="text-stone-400 fst-italic small mb-0">No entries for this day.</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @else
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 mb-2">No day entries</h5>
            <p class="text-muted mb-0">No day entries have been added to this session yet.</p>
        </div>
    </div>
    @endif
</div>
@endsection
