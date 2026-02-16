@extends('layouts.guest')

@section('title', 'Book Appointment - Your Mind Aid')

@section('content')
<section class="py-5 px-3 px-md-4 px-lg-5">
    <div class="container-fluid" style="max-width: 896px;">
        <div class="text-center mb-4">
            <h1 class="h2 fw-bold text-psychological-primary">Book an Appointment</h1>
            <p class="text-stone-600">
                @if($doctor)
                    Request an appointment with {{ $doctor->name ?? $doctor->full_name ?? 'your doctor' }}
                    @if($doctor->doctor_number)
                        <span class="badge bg-primary">ID: {{ $doctor->doctor_number }}</span>
                    @endif
                @else
                    Request an appointment with our mental health professionals.
                @endif
            </p>
        </div>

        <div class="card card-psychological shadow-sm">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show m-4 mb-0" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show m-4 mb-0" role="alert">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card-body p-4 p-md-5">
                <form method="POST" action="{{ route('appointment-request.store') }}">
                    @csrf
                    @if($doctor)
                        <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">
                        <input type="hidden" name="doctor_number" value="{{ $doctor->doctor_number }}">
                    @elseif($doctors->isNotEmpty())
                        <div class="mb-4">
                            <label for="doctor_id" class="form-label fw-semibold">Select Doctor</label>
                            <select name="doctor_id" id="doctor_id" class="form-select">
                                <option value="">Any available doctor</option>
                                @foreach($doctors as $d)
                                    <option value="{{ $d->id }}" {{ old('doctor_id') == $d->id ? 'selected' : '' }}>
                                        {{ $d->name ?? $d->full_name ?? 'Doctor' }}
                                        @if($d->doctor_number) ({{ $d->doctor_number }}) @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endif

                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">First Name <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" value="{{ old('first_name') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Last Name <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" value="{{ old('last_name') }}" required>
                        </div>
                    </div>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Phone</label>
                            <input type="tel" name="phone" class="form-control" value="{{ old('phone') }}">
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Session Mode</label>
                        <select name="session_mode" class="form-select">
                            <option value="">Select</option>
                            <option value="in_person" {{ old('session_mode') == 'in_person' ? 'selected' : '' }}>In-person</option>
                            <option value="online" {{ old('session_mode') == 'online' ? 'selected' : '' }}>Online</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Address</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                    </div>
                    <div class="row g-4 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Preferred Date <span class="text-danger">*</span></label>
                            <input type="date" name="preferred_date" class="form-control" value="{{ old('preferred_date') }}" min="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Preferred Time</label>
                            <select name="preferred_time" class="form-select">
                                <option value="">Select</option>
                                @foreach(['09:00','10:00','11:00','12:00','13:00','14:00','15:00','16:00','17:00'] as $t)
                                    <option value="{{ $t }}" {{ old('preferred_time') == $t ? 'selected' : '' }}>{{ \Carbon\Carbon::parse($t)->format('g:i A') }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Submit Request</button>
                        <a href="{{ url('/') }}#appointment" class="btn btn-outline-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection
