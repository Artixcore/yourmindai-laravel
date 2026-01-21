@extends('layouts.app')

@section('title', 'Create Staff Member')

@section('content')
<div class="container-fluid" style="max-width: 800px;">
    <!-- Page Header -->
    <div class="mb-4">
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('admin.dashboard')],
            ['label' => 'Staff', 'url' => route('admin.staff.index')],
            ['label' => 'Create']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">Create Staff Member</h1>
        <p class="text-muted mb-0">Add a new doctor or assistant to the system</p>
    </div>

    <!-- Form -->
    <div class="card border-0 shadow-sm">
        <form action="{{ route('admin.staff.store') }}" method="POST">
            @csrf
            
            <div class="card-body p-4">
                <!-- Personal Information Section -->
                <h5 class="fw-semibold mb-3 pb-2 border-bottom">Personal Information</h5>
                
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" value="{{ old('full_name') }}" 
                               class="form-control @error('full_name') is-invalid @enderror" required>
                        @error('full_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12 col-md-6">
                        <label class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" value="{{ old('username') }}" 
                               class="form-control @error('username') is-invalid @enderror" required>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12 col-md-6">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" value="{{ old('email') }}" 
                               class="form-control @error('email') is-invalid @enderror" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12 col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}" 
                               class="form-control @error('phone') is-invalid @enderror">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                <!-- Account Settings Section -->
                <h5 class="fw-semibold mb-3 pb-2 border-bottom mt-4">Account Settings</h5>
                
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Role <span class="text-danger">*</span></label>
                        <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                            <option value="">Select Role</option>
                            <option value="doctor" {{ old('role') == 'doctor' ? 'selected' : '' }}>Doctor</option>
                            <option value="assistant" {{ old('role') == 'assistant' ? 'selected' : '' }}>Assistant</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12 col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    
                    <div class="col-12 col-md-6">
                        <label class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="password" name="password" 
                               class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Minimum 8 characters</small>
                    </div>
                    
                    <div class="col-12 col-md-6">
                        <label class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" name="password_confirmation" 
                               class="form-control" required>
                    </div>
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="card-footer bg-transparent border-top d-flex justify-content-between align-items-center py-3">
                <a href="{{ route('admin.staff.index') }}" class="btn btn-outline-secondary">
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-2"></i>Create Staff Member
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
