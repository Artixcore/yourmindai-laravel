@props(['patient', 'doctor'])

@php
    // Handle both Patient and PatientProfile models
    $patientName = $patient->full_name ?? $patient->name ?? 'Patient';
    $patientEmail = $patient->user->email ?? $patient->email ?? '';
    $patientPhone = $patient->phone ?? '';
    $patientNumber = $patient->patient_number ?? '';
@endphp

<div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
    <div class="card-body p-4 text-white">
        <div class="row align-items-center">
            <div class="col-12 col-md-8">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div class="bg-white bg-opacity-20 rounded-circle d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                        <i class="bi bi-person-fill fs-1"></i>
                    </div>
                    <div>
                        <h2 class="h4 fw-bold mb-1 text-white">{{ $patientName }}</h2>
                        @if($patientNumber)
                            <p class="small mb-0 opacity-75">Patient #{{ $patientNumber }}</p>
                        @endif
                    </div>
                </div>
                
                <div class="row g-3">
                    @if($patientEmail)
                    <div class="col-12 col-sm-6">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-envelope-fill"></i>
                            <span class="small">{{ $patientEmail }}</span>
                        </div>
                    </div>
                    @endif
                    
                    @if($patientPhone)
                    <div class="col-12 col-sm-6">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-telephone-fill"></i>
                            <span class="small">{{ $patientPhone }}</span>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            
            @if($doctor)
            <div class="col-12 col-md-4 mt-3 mt-md-0">
                <div class="bg-white bg-opacity-20 rounded p-3">
                    <h6 class="small fw-semibold mb-2 text-white opacity-90">Your Doctor</h6>
                    <p class="h6 fw-bold mb-2 text-white">{{ $doctor->name ?? $doctor->email }}</p>
                    @if($doctor->email)
                        <a href="mailto:{{ $doctor->email }}" class="btn btn-sm btn-light text-primary">
                            <i class="bi bi-envelope me-1"></i>
                            Contact
                        </a>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
