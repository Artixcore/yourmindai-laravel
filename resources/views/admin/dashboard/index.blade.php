@extends('layouts.app')

@section('title', 'Admin Dashboard - Your Mind Aid')

@section('content')
<div class="mb-4">
    <h1 class="h2 fw-bold text-stone-900">Admin Dashboard</h1>
    <p class="text-stone-600 mt-2 mb-0">Welcome back, {{ auth()->user()->full_name ?? auth()->user()->name }}!</p>
</div>

<div class="row g-4 mb-5">
    <div class="col-12 col-md-6 col-lg-3">
        <x-card>
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="small text-stone-600 mb-0">Total Doctors</p>
                    <p class="h3 fw-bold text-stone-900 mt-2 mb-0">{{ \App\Models\User::where('role', 'doctor')->where('status', 'active')->count() }}</p>
                </div>
                <div class="bg-teal-100 rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <svg style="width: 24px; height: 24px;" class="text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                </div>
            </div>
        </x-card>
    </div>
    
    <div class="col-12 col-md-6 col-lg-3">
        <x-card>
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="small text-stone-600 mb-0">Total Patients</p>
                    <p class="h3 fw-bold text-stone-900 mt-2 mb-0">{{ \App\Models\Patient::where('status', 'active')->count() }}</p>
                </div>
                <div class="bg-primary bg-opacity-25 rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <svg style="width: 24px; height: 24px;" class="text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </x-card>
    </div>
    
    <div class="col-12 col-md-6 col-lg-3">
        <x-card>
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="small text-stone-600 mb-0">Active Sessions</p>
                    <p class="h3 fw-bold text-stone-900 mt-2 mb-0">{{ \App\Models\Session::where('status', 'active')->count() }}</p>
                </div>
                <div class="bg-emerald-100 rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <svg style="width: 24px; height: 24px;" class="text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
        </x-card>
    </div>
    
    <div class="col-12 col-md-6 col-lg-3">
        <x-card>
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <p class="small text-stone-600 mb-0">Pending Messages</p>
                    <p class="h3 fw-bold text-stone-900 mt-2 mb-0">{{ \App\Models\ContactMessage::where('status', 'new')->count() }}</p>
                </div>
                <div class="bg-amber-100 rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <svg style="width: 24px; height: 24px;" class="text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                    </svg>
                </div>
            </div>
        </x-card>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-lg-6">
        <x-card>
            <h2 class="h5 fw-semibold text-stone-900 mb-4">Quick Actions</h2>
            <div class="d-flex flex-column gap-3">
                <a href="{{ route('admin.staff.create') }}" class="btn btn-primary w-100">
                    Add New Staff Member
                </a>
                <a href="{{ route('admin.ai-reports.index') }}" class="btn btn-primary w-100">
                    Generate AI Report
                </a>
                <a href="{{ route('admin.analytics.index') }}" class="btn btn-success w-100">
                    View Analytics
                </a>
            </div>
        </x-card>
    </div>
    
    <div class="col-12 col-lg-6">
        <x-card>
            <h2 class="h5 fw-semibold text-stone-900 mb-4">Recent Activity</h2>
            <div>
                <p class="text-stone-600 mb-0">Recent activity will be displayed here.</p>
            </div>
        </x-card>
    </div>
</div>
@endsection
