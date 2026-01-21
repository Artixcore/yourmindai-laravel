@extends('layouts.app')

@section('title', 'Edit Staff Member')

@section('content')
<div class="container-fluid" style="max-width: 800px;">
    <!-- Page Header -->
    <div class="mb-4">
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('admin.dashboard')],
            ['label' => 'Staff', 'url' => route('admin.staff.index')],
            ['label' => $staff->full_name ?? $staff->name, 'url' => route('admin.staff.show', $staff)],
            ['label' => 'Edit']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">Edit Staff Member</h1>
        <p class="text-muted mb-0">Update staff member information</p>
    </div>

    <!-- Form -->
    <div class="card border-0 shadow-sm">
        <form action="{{ route('admin.staff.update', $staff) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="card-body p-4">
                <!-- Personal Information Section -->
                <h5 class="fw-semibold mb-3 pb-2 border-bottom">Personal Information</h5>
                
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" value="{{ old('full_name', $staff->full_name ?? $staff->name) }}" 
                               class="form-control @error('full_name') is-invalid @enderror" required>
                        @error('full_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12 col-md-6">
                        <label class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" value="{{ old('username', $staff->username) }}" 
                               class="form-control @error('username') is-invalid @enderror" required>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12 col-md-6">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" value="{{ old('email', $staff->email) }}" 
                               class="form-control @error('email') is-invalid @enderror" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12 col-md-6">
                        <label class="form-label">Phone</label>
                        <input type="tel" name="phone" value="{{ old('phone', $staff->phone) }}" 
                               class="form-control @error('phone') is-invalid @enderror">
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" value="{{ old('address', $staff->address) }}" 
                               class="form-control @error('address') is-invalid @enderror">
                        @error('address')
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
                            <option value="doctor" {{ old('role', $staff->role) == 'doctor' ? 'selected' : '' }}>Doctor</option>
                            <option value="assistant" {{ old('role', $staff->role) == 'assistant' ? 'selected' : '' }}>Assistant</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-12 col-md-6">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="active" {{ old('status', $staff->status) == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ old('status', $staff->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    
                    <div class="col-12">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" 
                               class="form-control @error('password') is-invalid @enderror"
                               placeholder="Leave blank to keep current password">
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Leave blank to keep current password. Minimum 8 characters if changing.</small>
                    </div>
                </div>
            </div>
            
            <!-- Form Actions -->
            <div class="card-footer bg-transparent border-top d-flex justify-content-between align-items-center py-3">
                <a href="{{ route('admin.staff.show', $staff) }}" class="btn btn-outline-secondary">
                    Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-2"></i>Update Staff Member
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
