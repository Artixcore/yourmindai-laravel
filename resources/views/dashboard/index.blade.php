@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-stone-900">Dashboard</h1>
        <p class="text-stone-600 mt-2">Welcome back, {{ auth()->user()->name ?? 'User' }}!</p>
    </div>
    
    @if($role === 'admin')
        <!-- Admin Dashboard -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-stone-600">Total Users</p>
                        <p class="text-3xl font-bold text-stone-900 mt-2">0</p>
                    </div>
                    <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
            </x-card>
            
            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-stone-600">Active Patients</p>
                        <p class="text-3xl font-bold text-stone-900 mt-2">0</p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
            </x-card>
            
            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-stone-600">Appointments</p>
                        <p class="text-3xl font-bold text-stone-900 mt-2">0</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </x-card>
            
            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-stone-600">Messages</p>
                        <p class="text-3xl font-bold text-stone-900 mt-2">0</p>
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
                <h3 class="text-xl font-semibold text-stone-900 mb-4">Recent Activity</h3>
                <div class="space-y-4">
                    <p class="text-stone-500 text-center py-8">No recent activity</p>
                </div>
            </x-card>
            
            <x-card>
                <h3 class="text-xl font-semibold text-stone-900 mb-4">Statistics</h3>
                <div class="h-64 flex items-center justify-center bg-stone-50 rounded-lg">
                    <p class="text-stone-400">Chart placeholder</p>
                </div>
            </x-card>
        </div>
        
    @elseif($role === 'doctor')
        <!-- Doctor Dashboard -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-stone-600">My Patients</p>
                        <p class="text-3xl font-bold text-stone-900 mt-2">0</p>
                    </div>
                    <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
            </x-card>
            
            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-stone-600">Today's Appointments</p>
                        <p class="text-3xl font-bold text-stone-900 mt-2">0</p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </x-card>
            
            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-stone-600">Pending Notes</p>
                        <p class="text-3xl font-bold text-stone-900 mt-2">0</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>
            </x-card>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <x-card>
                <h3 class="text-xl font-semibold text-stone-900 mb-4">Upcoming Appointments</h3>
                <div class="space-y-4">
                    <p class="text-stone-500 text-center py-8">No upcoming appointments</p>
                </div>
            </x-card>
            
            <x-card>
                <h3 class="text-xl font-semibold text-stone-900 mb-4">Patient Overview</h3>
                <div class="h-64 flex items-center justify-center bg-stone-50 rounded-lg">
                    <p class="text-stone-400">Chart placeholder</p>
                </div>
            </x-card>
        </div>
        
    @else
        <!-- Assistant Dashboard -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-stone-600">Tasks</p>
                        <p class="text-3xl font-bold text-stone-900 mt-2">0</p>
                    </div>
                    <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-teal-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                </div>
            </x-card>
            
            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-stone-600">Messages</p>
                        <p class="text-3xl font-bold text-stone-900 mt-2">0</p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                        </svg>
                    </div>
                </div>
            </x-card>
            
            <x-card>
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-stone-600">Scheduled</p>
                        <p class="text-3xl font-bold text-stone-900 mt-2">0</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </x-card>
        </div>
        
        <x-card>
            <h3 class="text-xl font-semibold text-stone-900 mb-4">Recent Tasks</h3>
            <div class="space-y-4">
                <p class="text-stone-500 text-center py-8">No recent tasks</p>
            </div>
        </x-card>
    @endif
@endsection
