@extends('layouts.app')

@section('title', 'Appointments - Your Mind Aid')

@section('content')
<div class="container-fluid" style="max-width: 1200px;">
    <div class="mb-4">
        <h1 class="h3 fw-bold text-stone-900 mb-2">Appointments</h1>
        <p class="text-stone-600 mb-0">Manage all your scheduled appointments</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters -->
    <x-card class="mb-4">
        <form method="GET" action="{{ route('doctors.appointments.index') }}" class="d-flex flex-wrap align-items-end gap-3">
            <div class="flex-grow-1" style="min-width: 200px;">
                <label class="form-label small text-stone-700 mb-1">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="confirmed" {{ $filterStatus === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="pending" {{ $filterStatus === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="cancelled" {{ $filterStatus === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div style="min-width: 150px;">
                <label class="form-label small text-stone-700 mb-1">From Date</label>
                <input type="date" name="date_from" class="form-control form-control-sm" value="{{ $dateFrom }}">
            </div>
            <div style="min-width: 150px;">
                <label class="form-label small text-stone-700 mb-1">To Date</label>
                <input type="date" name="date_to" class="form-control form-control-sm" value="{{ $dateTo }}">
            </div>
            <div>
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
                <a href="{{ route('doctors.appointments.index') }}" class="btn btn-outline-secondary btn-sm">Clear</a>
            </div>
        </form>
    </x-card>

    @if($appointments->isEmpty())
        <x-card>
            <div class="text-center py-5">
                <i class="bi bi-calendar-x fs-1 text-stone-300 mb-3"></i>
                <p class="text-stone-500 mb-0">No appointments found.</p>
            </div>
        </x-card>
    @else
        <!-- Upcoming Appointments -->
        @if($upcoming->isNotEmpty())
            <div class="mb-4">
                <h2 class="h5 fw-semibold text-stone-900 mb-3">Upcoming Appointments</h2>
                <div class="row g-3">
                    @foreach($upcoming as $appointment)
                        <div class="col-12">
                            <x-card>
                                <div class="d-flex align-items-start justify-content-between">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center gap-3 mb-2">
                                            <h5 class="fw-semibold text-stone-900 mb-0">
                                                {{ $appointment->date->format('F d, Y') }}
                                            </h5>
                                            @if($appointment->status)
                                                <x-badge :variant="$appointment->status === 'confirmed' ? 'success' : ($appointment->status === 'cancelled' ? 'danger' : 'warning')">
                                                    {{ ucfirst($appointment->status) }}
                                                </x-badge>
                                            @endif
                                        </div>
                                        
                                        @if($appointment->time_slot)
                                            <p class="text-stone-600 mb-2">
                                                <i class="bi bi-clock me-2"></i>{{ $appointment->time_slot }}
                                            </p>
                                        @endif
                                        
                                        @if($appointment->patient)
                                            <p class="text-stone-600 mb-2">
                                                <i class="bi bi-person me-2"></i>
                                                {{ $appointment->patient->full_name ?? 'Unknown Patient' }}
                                            </p>
                                        @endif
                                        
                                        @if($appointment->appointment_type)
                                            <p class="text-stone-600 mb-2">
                                                <i class="bi bi-calendar-event me-2"></i>{{ ucfirst($appointment->appointment_type) }}
                                            </p>
                                        @endif
                                        
                                        @if($appointment->setting_place)
                                            <p class="text-stone-600 mb-2">
                                                <i class="bi bi-geo-alt me-2"></i>{{ $appointment->setting_place }}
                                            </p>
                                        @endif
                                        
                                        @if($appointment->notes)
                                            <p class="text-stone-600 mb-0 mt-2" style="white-space: pre-wrap;">{{ $appointment->notes }}</p>
                                        @endif
                                    </div>
                                </div>
                            </x-card>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Past Appointments -->
        @if($past->isNotEmpty())
            <div>
                <h2 class="h5 fw-semibold text-stone-900 mb-3">Past Appointments</h2>
                <div class="row g-3">
                    @foreach($past as $appointment)
                        <div class="col-12">
                            <x-card>
                                <div class="d-flex align-items-start justify-content-between">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center gap-3 mb-2">
                                            <h5 class="fw-semibold text-stone-900 mb-0">
                                                {{ $appointment->date->format('F d, Y') }}
                                            </h5>
                                            @if($appointment->status)
                                                <x-badge :variant="$appointment->status === 'confirmed' ? 'success' : ($appointment->status === 'cancelled' ? 'danger' : 'warning')">
                                                    {{ ucfirst($appointment->status) }}
                                                </x-badge>
                                            @endif
                                        </div>
                                        
                                        @if($appointment->time_slot)
                                            <p class="text-stone-600 mb-2">
                                                <i class="bi bi-clock me-2"></i>{{ $appointment->time_slot }}
                                            </p>
                                        @endif
                                        
                                        @if($appointment->patient)
                                            <p class="text-stone-600 mb-2">
                                                <i class="bi bi-person me-2"></i>
                                                {{ $appointment->patient->full_name ?? 'Unknown Patient' }}
                                            </p>
                                        @endif
                                        
                                        @if($appointment->appointment_type)
                                            <p class="text-stone-600 mb-2">
                                                <i class="bi bi-calendar-event me-2"></i>{{ ucfirst($appointment->appointment_type) }}
                                            </p>
                                        @endif
                                        
                                        @if($appointment->cancellation_reason)
                                            <div class="alert alert-warning mt-2 mb-0">
                                                <strong>Cancellation Reason:</strong> {{ $appointment->cancellation_reason }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </x-card>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endif
</div>
@endsection
