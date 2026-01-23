@extends('layouts.app')

@section('title', 'Create Patient from Request')

@section('content')
<!-- Page Header -->
<div class="mb-4">
    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => (auth()->user()->role === 'admin') ? route('admin.dashboard') : route('dashboard')],
        ['label' => 'Appointment Requests', 'url' => (auth()->user()->role === 'admin') ? route('admin.appointment-requests.index') : route('doctors.appointment-requests.index')],
        ['label' => 'Request #' . $appointmentRequest->id, 'url' => (auth()->user()->role === 'admin') ? route('admin.appointment-requests.show', $appointmentRequest) : route('doctors.appointment-requests.show', $appointmentRequest)],
        ['label' => 'Create Patient']
    ]" />
    <h1 class="h3 mb-1 fw-semibold">Create Patient from Request</h1>
    <p class="text-muted mb-0">Create a new patient account from appointment request</p>
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

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0">
        <h5 class="mb-0 fw-semibold">Patient Information</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ (auth()->user()->role === 'admin') ? route('admin.appointment-requests.store-patient', $appointmentRequest) : route('doctors.appointment-requests.store-patient', $appointmentRequest) }}">
            @csrf
            
            <!-- Pre-filled Information Display -->
            <div class="alert alert-info mb-4">
                <h6 class="fw-semibold mb-2">Request Information:</h6>
                <p class="mb-1"><strong>Name:</strong> {{ $appointmentRequest->first_name }} {{ $appointmentRequest->last_name }}</p>
                <p class="mb-1"><strong>Email:</strong> {{ $appointmentRequest->email }}</p>
                <p class="mb-1"><strong>Phone:</strong> {{ $appointmentRequest->phone ?? 'Not provided' }}</p>
                <p class="mb-0"><strong>Preferred Date:</strong> {{ $appointmentRequest->preferred_date->format('M d, Y') }}</p>
            </div>
            
            <div class="row g-3 mb-4">
                <div class="col-12 col-md-6">
                    <label class="form-label">First Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="first_name" value="{{ old('first_name', $appointmentRequest->first_name) }}" required readonly>
                </div>
                
                <div class="col-12 col-md-6">
                    <label class="form-label">Last Name <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="last_name" value="{{ old('last_name', $appointmentRequest->last_name) }}" required readonly>
                </div>
                
                <div class="col-12 col-md-6">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" name="email" value="{{ old('email', $appointmentRequest->email) }}" required readonly>
                </div>
                
                <div class="col-12 col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="tel" class="form-control" name="phone" value="{{ old('phone', $appointmentRequest->phone) }}" readonly>
                </div>
                
                <div class="col-12">
                    <label class="form-label">Assign Doctor <span class="text-danger">*</span></label>
                    <select class="form-select" name="doctor_id" required>
                        <option value="">Select a doctor...</option>
                        @foreach($doctors as $doctor)
                        <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                            {{ $doctor->name ?? $doctor->email ?? 'Doctor #' . $doctor->id }}
                        </option>
                        @endforeach
                    </select>
                    @error('doctor_id')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="create_appointment" id="create_appointment" value="1" {{ old('create_appointment') ? 'checked' : '' }}>
                        <label class="form-check-label" for="create_appointment">
                            Create appointment for preferred date/time
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                <a href="{{ (auth()->user()->role === 'admin') ? route('admin.appointment-requests.show', $appointmentRequest) : route('doctors.appointment-requests.show', $appointmentRequest) }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Create Patient</button>
            </div>
        </form>
    </div>
</div>
@endsection
