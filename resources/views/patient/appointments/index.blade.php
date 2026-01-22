@extends('layouts.app')

@section('title', 'My Appointments - Your Mind Aid')

@section('content')
<div class="container-fluid" style="max-width: 1200px;">
    <div class="mb-4">
        <h1 class="h3 fw-bold text-stone-900 mb-2">Appointments</h1>
        <p class="text-stone-600 mb-0">View all your scheduled appointments</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($appointments->isEmpty())
        <div class="card border-0 shadow-sm">
            <div class="card-body p-5 text-center">
                <i class="bi bi-calendar-x fs-1 text-stone-300 mb-3"></i>
                <p class="text-stone-500 mb-0">No appointments scheduled yet.</p>
                <p class="small text-stone-400 mt-2 mb-0">Your doctor will schedule appointments for you.</p>
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach($appointments as $appointment)
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-4">
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
                                    
                                    @if($appointment->doctor)
                                        <p class="text-stone-600 mb-2">
                                            <i class="bi bi-person-badge me-2"></i>Dr. {{ $appointment->doctor->name ?? $appointment->doctor->email }}
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
                                    
                                    @if($appointment->cancellation_reason)
                                        <div class="alert alert-warning mt-2 mb-0">
                                            <strong>Cancellation Reason:</strong> {{ $appointment->cancellation_reason }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
