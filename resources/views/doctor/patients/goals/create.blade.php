@extends('layouts.app')

@section('title', 'New Goal - Your Mind Aid')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Patients</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('patients.show', $patient) }}">{{ $patientProfile->user->name ?? $patientProfile->full_name ?? $patient->name ?? 'Patient' }}</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('patients.goals.index', $patient) }}">Goals</a></li>
                    <li class="breadcrumb-item active">New Goal</li>
                </ol>
            </nav>
            <h2 class="mb-1">New Goal</h2>
            <p class="text-muted">Set a goal for {{ $patientProfile->user->name ?? $patientProfile->full_name ?? $patient->name ?? 'Patient' }}</p>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form action="{{ route('patients.goals.store', $patient) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="title" class="form-label fw-semibold">Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label fw-semibold">Description</label>
                            <textarea name="description" id="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="start_date" class="form-label fw-semibold">Start date</label>
                                <input type="date" name="start_date" id="start_date" class="form-control @error('start_date') is-invalid @enderror" value="{{ old('start_date') }}">
                                @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="end_date" class="form-label fw-semibold">End date</label>
                                <input type="date" name="end_date" id="end_date" class="form-control @error('end_date') is-invalid @enderror" value="{{ old('end_date') }}">
                                @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="frequency_per_day" class="form-label fw-semibold">Frequency (times per day)</label>
                                <input type="number" name="frequency_per_day" id="frequency_per_day" min="1" max="100" class="form-control @error('frequency_per_day') is-invalid @enderror" value="{{ old('frequency_per_day') }}" placeholder="e.g. 2">
                                @error('frequency_per_day')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label for="duration_minutes" class="form-label fw-semibold">Duration (minutes per time)</label>
                                <input type="number" name="duration_minutes" id="duration_minutes" min="1" max="480" class="form-control @error('duration_minutes') is-invalid @enderror" value="{{ old('duration_minutes') }}" placeholder="e.g. 15">
                                @error('duration_minutes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label fw-semibold">Status</label>
                            <input type="text" name="status" id="status" class="form-control @error('status') is-invalid @enderror" value="{{ old('status') }}" placeholder="e.g. active, completed">
                            @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" name="visible_to_patient" id="visible_to_patient" value="1" class="form-check-input" {{ old('visible_to_patient') ? 'checked' : '' }}>
                                <label for="visible_to_patient" class="form-check-label">Visible to patient</label>
                            </div>
                            <div class="form-check">
                                <input type="checkbox" name="visible_to_parent" id="visible_to_parent" value="1" class="form-check-input" {{ old('visible_to_parent') ? 'checked' : '' }}>
                                <label for="visible_to_parent" class="form-check-label">Visible to parent</label>
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Create Goal</button>
                            <a href="{{ route('patients.goals.index', $patient) }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
