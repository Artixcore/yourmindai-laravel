@extends('layouts.app')

@section('title', 'My Profile - Your Mind Aid')

@section('content')
<div class="container-fluid" style="max-width: 800px;">
    <div class="mb-4">
        <h1 class="h3 fw-bold text-stone-900 mb-2">My Profile</h1>
        <p class="text-stone-600 mb-0">Manage your personal information</p>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('patient.profile.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Profile Picture -->
                <div class="mb-4 text-center">
                    @if(auth()->user()->avatar_path ?? null)
                        <img 
                            src="{{ auth()->user()->avatar_url ?? asset('storage/' . auth()->user()->avatar_path) }}" 
                            alt="Profile"
                            class="profile-avatar rounded-circle mb-3"
                        />
                    @else
                        <div class="profile-avatar-placeholder rounded-circle mx-auto mb-3">
                            {{ strtoupper(substr(auth()->user()->name ?? 'P', 0, 1)) }}
                        </div>
                    @endif
                    <div>
                        <label for="avatar" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-camera me-1"></i>
                            Change Photo
                        </label>
                        <input type="file" id="avatar" name="avatar" accept="image/*" class="d-none" onchange="this.form.submit()">
                    </div>
                </div>

                <hr>

                <!-- Personal Information -->
                <h5 class="h6 fw-semibold text-stone-900 mb-3">Personal Information</h5>
                
                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-medium text-stone-700">Full Name</label>
                        <input type="text" class="form-control" name="name" value="{{ auth()->user()->name ?? auth()->user()->full_name ?? '' }}" required>
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-medium text-stone-700">Email</label>
                        <input type="email" class="form-control" value="{{ auth()->user()->email }}" disabled>
                        <small class="text-stone-500">Email cannot be changed</small>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-medium text-stone-700">Phone</label>
                        <input type="tel" class="form-control" name="phone" value="{{ auth()->user()->phone ?? '' }}">
                    </div>
                </div>

                <hr>

                <!-- Change Password -->
                <h5 class="h6 fw-semibold text-stone-900 mb-3">Change Password</h5>
                
                <div class="row g-3 mb-3">
                    <div class="col-12">
                        <label class="form-label small fw-medium text-stone-700">Current Password</label>
                        <input type="password" class="form-control" name="current_password">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-medium text-stone-700">New Password</label>
                        <input type="password" class="form-control" name="password">
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-medium text-stone-700">Confirm New Password</label>
                        <input type="password" class="form-control" name="password_confirmation">
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('patient.dashboard') }}" class="btn btn-outline-secondary">
                        Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
