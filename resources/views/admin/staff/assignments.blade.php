@extends('layouts.app')

@section('title', 'Assistant-Doctor Assignments')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('admin.dashboard')],
            ['label' => 'Staff', 'url' => route('admin.staff.index')],
            ['label' => 'Assignments']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">Assistant-Doctor Assignments</h1>
        <p class="text-muted mb-0">Manage which assistants are assigned to which doctors</p>
    </div>
    <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Staff
    </a>
</div>

<!-- Create Assignment Form -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-transparent border-bottom py-3">
        <h5 class="card-title mb-0 fw-semibold">Create New Assignment</h5>
    </div>
    <div class="card-body p-4">
        <form action="{{ route('admin.staff.assignments.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-12 col-md-5">
                    <label class="form-label">Assistant <span class="text-danger">*</span></label>
                    <select name="assistant_id" class="form-select @error('assistant_id') is-invalid @enderror" required>
                        <option value="">Select Assistant</option>
                        @foreach($assistants as $assistant)
                            <option value="{{ $assistant->id }}" {{ old('assistant_id') == $assistant->id ? 'selected' : '' }}>
                                {{ $assistant->full_name ?? $assistant->name }} ({{ $assistant->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('assistant_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-12 col-md-5">
                    <label class="form-label">Doctor <span class="text-danger">*</span></label>
                    <select name="doctor_id" class="form-select @error('doctor_id') is-invalid @enderror" required>
                        <option value="">Select Doctor</option>
                        @foreach($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                {{ $doctor->full_name ?? $doctor->name }} ({{ $doctor->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('doctor_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="col-12 col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-plus-circle me-2"></i>Assign
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Assignments Table -->
@if($assignments->count() > 0)
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0">Assistant</th>
                            <th class="border-0">Doctor</th>
                            <th class="border-0">Assigned</th>
                            <th class="border-0 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($assignments as $assignment)
                            <tr>
                                <td>
                                    <div>
                                        <p class="mb-0 fw-semibold">{{ $assignment->assistant->full_name ?? $assignment->assistant->name }}</p>
                                        <small class="text-muted">{{ $assignment->assistant->email }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <p class="mb-0 fw-semibold">{{ $assignment->doctor->full_name ?? $assignment->doctor->name }}</p>
                                        <small class="text-muted">{{ $assignment->doctor->email }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-muted small">{{ $assignment->created_at->format('M d, Y') }}</div>
                                </td>
                                <td class="text-end">
                                    <form action="{{ route('admin.staff.assignments.destroy', $assignment) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                onclick="return confirm('Are you sure you want to remove this assignment?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@else
    <div class="card border-0 shadow-sm">
        <div class="card-body p-5 text-center">
            <i class="bi bi-link-45deg fs-1 text-muted mb-3"></i>
            <p class="text-muted mb-0">No assignments found. Create one above to get started.</p>
        </div>
    </div>
@endif
@endsection
