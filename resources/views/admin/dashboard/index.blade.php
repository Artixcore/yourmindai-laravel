@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('admin.dashboard')],
            ['label' => 'Dashboard']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">Admin Dashboard</h1>
        <p class="text-muted mb-0">Welcome back, {{ auth()->user()->full_name ?? auth()->user()->name }}!</p>
    </div>
</div>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small mb-1">Total Doctors</p>
                        <h3 class="h4 mb-0 fw-bold">{{ \App\Models\User::where('role', 'doctor')->where('status', 'active')->count() }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                        <i class="bi bi-person-badge text-primary fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small mb-1">Total Patients</p>
                        <h3 class="h4 mb-0 fw-bold">{{ \App\Models\Patient::where('status', 'active')->count() }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 rounded-3 p-3">
                        <i class="bi bi-people text-success fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small mb-1">Active Sessions</p>
                        <h3 class="h4 mb-0 fw-bold">{{ \App\Models\Session::where('status', 'active')->count() }}</h3>
                    </div>
                    <div class="bg-info bg-opacity-10 rounded-3 p-3">
                        <i class="bi bi-calendar-check text-info fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-muted small mb-1">Pending Messages</p>
                        <h3 class="h4 mb-0 fw-bold">{{ \App\Models\ContactMessage::where('status', 'new')->count() }}</h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                        <i class="bi bi-chat-dots text-warning fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions & Recent Activity -->
<div class="row g-3">
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom py-3">
                <h5 class="card-title mb-0 fw-semibold">Quick Actions</h5>
            </div>
            <div class="card-body p-4">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.staff.create') }}" class="btn btn-primary">
                        <i class="bi bi-person-plus me-2"></i>Add New Staff Member
                    </a>
                    <a href="{{ route('admin.ai-reports.index') }}" class="btn btn-outline-primary">
                        <i class="bi bi-file-earmark-text me-2"></i>Generate AI Report
                    </a>
                    <a href="{{ route('admin.analytics.index') }}" class="btn btn-outline-success">
                        <i class="bi bi-graph-up me-2"></i>View Analytics
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-lg-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-bottom py-3">
                <h5 class="card-title mb-0 fw-semibold">Recent Activity</h5>
            </div>
            <div class="card-body p-4">
                <p class="text-muted mb-0">Recent activity will be displayed here.</p>
            </div>
        </div>
    </div>
</div>
@endsection
