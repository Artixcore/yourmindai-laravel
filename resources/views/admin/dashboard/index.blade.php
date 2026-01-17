@extends('layouts.app')

@section('title', 'Admin Dashboard - Your Mind Aid')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold text-stone-900">Admin Dashboard</h1>
    <p class="text-stone-600 mt-2">Welcome back, {{ auth()->user()->full_name ?? auth()->user()->name }}!</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <x-card>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-stone-600">Total Doctors</p>
                <p class="text-3xl font-bold text-stone-900 mt-2">{{ \App\Models\User::where('role', 'doctor')->where('status', 'active')->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
        </div>
    </x-card>
    
    <x-card>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-stone-600">Total Patients</p>
                <p class="text-3xl font-bold text-stone-900 mt-2">{{ \App\Models\Patient::where('status', 'active')->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
        </div>
    </x-card>
    
    <x-card>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-stone-600">Active Sessions</p>
                <p class="text-3xl font-bold text-stone-900 mt-2">{{ \App\Models\Session::where('status', 'active')->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </x-card>
    
    <x-card>
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-stone-600">Pending Messages</p>
                <p class="text-3xl font-bold text-stone-900 mt-2">{{ \App\Models\ContactMessage::where('status', 'new')->count() }}</p>
            </div>
            <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-amber-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
            </div>
        </div>
    </x-card>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <x-card>
        <h2 class="text-xl font-semibold text-stone-900 mb-4">Quick Actions</h2>
        <div class="space-y-3">
            <a href="{{ route('admin.staff.create') }}" class="block w-full px-4 py-3 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors text-center">
                Add New Staff Member
            </a>
            <a href="{{ route('admin.ai-reports.index') }}" class="block w-full px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-center">
                Generate AI Report
            </a>
            <a href="{{ route('admin.analytics.index') }}" class="block w-full px-4 py-3 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-center">
                View Analytics
            </a>
        </div>
    </x-card>
    
    <x-card>
        <h2 class="text-xl font-semibold text-stone-900 mb-4">Recent Activity</h2>
        <div class="space-y-3">
            <p class="text-stone-600">Recent activity will be displayed here.</p>
        </div>
    </x-card>
</div>
@endsection
