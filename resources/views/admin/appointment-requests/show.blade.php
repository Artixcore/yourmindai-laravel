@extends('layouts.app')

@section('title', 'Appointment Request Details')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => ($isAdmin ?? true) ? route('admin.dashboard') : route('dashboard')],
            ['label' => 'Appointment Requests', 'url' => ($isAdmin ?? true) ? route('admin.appointment-requests.index') : route('doctors.appointment-requests.index')],
            ['label' => 'Details']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">Appointment Request Details</h1>
        <p class="text-muted mb-0">View and manage appointment request</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-4">
    <div class="col-12 col-lg-8">
        <!-- Request Information -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0 fw-semibold">Request Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <p class="small text-muted mb-1">Name</p>
                        <p class="fw-semibold mb-0">{{ $appointmentRequest->first_name }} {{ $appointmentRequest->last_name }}</p>
                    </div>
                    <div class="col-6">
                        <p class="small text-muted mb-1">Email</p>
                        <p class="fw-semibold mb-0">{{ $appointmentRequest->email }}</p>
                    </div>
                    <div class="col-6">
                        <p class="small text-muted mb-1">Phone</p>
                        <p class="fw-semibold mb-0">{{ $appointmentRequest->phone ?? 'Not provided' }}</p>
                    </div>
                    <div class="col-6">
                        <p class="small text-muted mb-1">Status</p>
                        <p class="mb-0">
                            @if($appointmentRequest->status === 'pending')
                            <span class="badge bg-warning">Pending</span>
                            @elseif($appointmentRequest->status === 'approved')
                            <span class="badge bg-success">Approved</span>
                            @elseif($appointmentRequest->status === 'rejected')
                            <span class="badge bg-danger">Rejected</span>
                            @elseif($appointmentRequest->status === 'converted')
                            <span class="badge bg-info">Converted</span>
                            @endif
                        </p>
                    </div>
                    @if($appointmentRequest->address)
                    <div class="col-12">
                        <p class="small text-muted mb-1">Address</p>
                        <p class="fw-semibold mb-0">{{ $appointmentRequest->address }}</p>
                    </div>
                    @endif
                    <div class="col-6">
                        <p class="small text-muted mb-1">Preferred Date</p>
                        <p class="fw-semibold mb-0">{{ $appointmentRequest->preferred_date->format('M d, Y') }}</p>
                    </div>
                    <div class="col-6">
                        <p class="small text-muted mb-1">Preferred Time</p>
                        <p class="fw-semibold mb-0">{{ $appointmentRequest->preferred_time ?? 'Not specified' }}</p>
                    </div>
                    @if($appointmentRequest->notes)
                    <div class="col-12">
                        <p class="small text-muted mb-1">Notes</p>
                        <p class="mb-0">{{ $appointmentRequest->notes }}</p>
                    </div>
                    @endif
                    <div class="col-6">
                        <p class="small text-muted mb-1">Submitted</p>
                        <p class="fw-semibold mb-0">{{ $appointmentRequest->created_at->format('M d, Y h:i A') }}</p>
                    </div>
                    @if($appointmentRequest->doctor)
                    <div class="col-6">
                        <p class="small text-muted mb-1">Assigned Doctor</p>
                        <p class="fw-semibold mb-0">{{ $appointmentRequest->doctor->name ?? $appointmentRequest->doctor->email ?? 'N/A' }}</p>
                    </div>
                    @endif
                    @if($appointmentRequest->patient || $appointmentRequest->patientProfile)
                    <div class="col-12">
                        <p class="small text-muted mb-1">Patient Created</p>
                        <p class="mb-0">
                            @if($appointmentRequest->patient)
                            <a href="{{ route('admin.patients.show', $appointmentRequest->patient) }}" class="btn btn-sm btn-outline-primary">
                                View Patient
                            </a>
                            @elseif($appointmentRequest->patientProfile)
                            <span class="text-success">Patient Profile Created</span>
                            @endif
                        </p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-lg-4">
        <!-- Actions -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0 fw-semibold">Actions</h5>
            </div>
            <div class="card-body">
                @if(($isAdmin ?? true) && $appointmentRequest->isPending())
                <form method="POST" action="{{ ($isAdmin ?? true) ? route('admin.appointment-requests.approve', $appointmentRequest) : route('doctors.appointment-requests.approve', $appointmentRequest) }}" class="mb-3">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small">Assign Doctor (Optional)</label>
                        <select name="doctor_id" class="form-select form-select-sm">
                            <option value="">No doctor assigned</option>
                            @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}">{{ $doctor->name ?? $doctor->email ?? 'Doctor #' . $doctor->id }}</option>
                            @endforeach
                        </select>
                        @error('doctor_id')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-success w-100">
                        <i class="bi bi-check-circle me-2"></i>Approve Request
                    </button>
                </form>
                
                <form method="POST" action="{{ ($isAdmin ?? true) ? route('admin.appointment-requests.reject', $appointmentRequest) : route('doctors.appointment-requests.reject', $appointmentRequest) }}" class="mb-3">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label small">Rejection Reason (Optional)</label>
                        <textarea name="reason" class="form-control form-control-sm" rows="2" placeholder="Reason for rejection"></textarea>
                    </div>
                    <button type="submit" class="btn btn-danger w-100">
                        <i class="bi bi-x-circle me-2"></i>Reject Request
                    </button>
                </form>
                @endif
                
                @if(($isAdmin ?? true) && ($appointmentRequest->isPending() || $appointmentRequest->isApproved()))
                <a href="{{ ($isAdmin ?? true) ? route('admin.appointment-requests.create-patient', $appointmentRequest) : route('doctors.appointment-requests.create-patient', $appointmentRequest) }}" class="btn btn-primary w-100">
                    <i class="bi bi-person-plus me-2"></i>Create Patient
                </a>
                @endif
            </div>
        </div>
        
        <!-- Status Info -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0">
                <h5 class="mb-0 fw-semibold">Status Information</h5>
            </div>
            <div class="card-body">
                <p class="small text-muted mb-2">
                    @if($appointmentRequest->isPending())
                    This request is pending review. You can approve, reject, or create a patient from it.
                    @elseif($appointmentRequest->isApproved())
                    This request has been approved. You can create a patient from it.
                    @elseif($appointmentRequest->isConverted())
                    A patient has been created from this request.
                    @else
                    This request has been rejected.
                    @endif
                </p>
            </div>
        </div>
    </div>
</div>

<div class="mt-4">
    <a href="{{ ($isAdmin ?? true) ? route('admin.appointment-requests.index') : route('doctors.appointment-requests.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Requests
    </a>
</div>
@endsection
