@extends('layouts.app')

@section('title', 'Admin - ' . $patient->name)

@section('content')
<div class="container-fluid" style="max-width: 1024px;">
    <!-- Header -->
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h1 class="h2 fw-bold text-stone-900">{{ $patient->name }}</h1>
            <p class="text-stone-600 mt-2 mb-0">Patient Profile - Admin View</p>
        </div>
        <a
            href="{{ route('admin.patients.index') }}"
            class="btn btn-outline-secondary d-flex align-items-center gap-2"
        >
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Back to Patients</span>
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div 
            x-data="{ show: true }"
            x-show="show"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform translate-y-2"
            x-transition:enter-end="opacity-100 transform translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="alert alert-success alert-dismissible fade show mb-4"
            x-init="setTimeout(() => show = false, 5000)"
        >
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Patient Details -->
    <div class="row g-4">
        <!-- Main Info Card -->
        <div class="col-12 col-md-8">
            <x-card>
                <h2 class="h5 font-semibold text-stone-900 mb-3">Patient Information</h2>
                
                <div class="d-flex flex-column gap-3">
                    <div>
                        <label class="small font-medium text-stone-500">Full Name</label>
                        <p class="text-stone-900 font-medium mt-1 mb-0">{{ $patient->name }}</p>
                    </div>
                    
                    <div>
                        <label class="small font-medium text-stone-500">Email</label>
                        <p class="text-stone-900 font-medium mt-1 mb-0">{{ $patient->email }}</p>
                    </div>
                    
                    <div>
                        <label class="small font-medium text-stone-500">Phone</label>
                        <p class="text-stone-900 font-medium mt-1 mb-0">{{ $patient->phone ?? '—' }}</p>
                    </div>
                    
                    <div>
                        <label class="small font-medium text-stone-500">Status</label>
                        <div class="mt-1">
                            <x-badge :variant="$patient->status === 'active' ? 'success' : 'default'">
                                {{ ucfirst($patient->status) }}
                            </x-badge>
                        </div>
                    </div>
                    
                    @if($patient->doctor)
                    <div>
                        <label class="small font-medium text-stone-500">Assigned Doctor</label>
                        <p class="text-stone-900 font-medium mt-1 mb-0">
                            {{ $patient->doctor->name ?? $patient->doctor->email }}
                        </p>
                    </div>
                    @endif
                    
                    <div>
                        <label class="small font-medium text-stone-500">Created</label>
                        <p class="text-stone-900 font-medium mt-1 mb-0">{{ $patient->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Photo Card -->
        <div class="col-12 col-md-4">
            <x-card>
                <h2 class="h5 font-semibold text-stone-900 mb-3">Photo</h2>
                
                @if($patient->photo_path)
                    <img 
                        src="{{ $patient->photo_url }}" 
                        alt="{{ $patient->name }}"
                        class="w-100 rounded object-fit-cover mb-3"
                        style="aspect-ratio: 1;"
                    />
                @else
                    <div class="w-100 rounded bg-teal-100 d-flex align-items-center justify-content-center mb-3" style="aspect-ratio: 1;">
                        <span class="text-teal-600 font-semibold display-4">
                            {{ strtoupper(substr($patient->name, 0, 1)) }}
                        </span>
                    </div>
                @endif
            </x-card>
        </div>
    </div>

    <!-- Sessions Summary -->
    <x-card class="mt-4">
        <h2 class="h5 font-semibold text-stone-900 mb-3">Therapy Sessions Summary</h2>
        
        <div class="row g-3">
            <div class="col-12 col-md-4">
                <div class="border border-stone-200 rounded p-3">
                    <p class="small text-stone-600 mb-0">Total Sessions</p>
                    <p class="h4 fw-bold text-stone-900 mt-2 mb-0">{{ $sessionsSummary['total'] }}</p>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="border border-stone-200 rounded p-3">
                    <p class="small text-stone-600 mb-0">Active Sessions</p>
                    <p class="h4 fw-bold text-emerald-600 mt-2 mb-0">{{ $sessionsSummary['active'] }}</p>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="border border-stone-200 rounded p-3">
                    <p class="small text-stone-600 mb-0">Closed Sessions</p>
                    <p class="h4 fw-bold text-stone-600 mt-2 mb-0">{{ $sessionsSummary['closed'] }}</p>
                </div>
            </div>
        </div>
    </x-card>

    <!-- Recent Activity -->
    @if($recentActivity->count() > 0)
    <x-card class="mt-4">
        <h2 class="h5 font-semibold text-stone-900 mb-3">Recent Activity</h2>
        
        <div class="d-flex flex-column gap-3">
            @foreach($recentActivity as $session)
                <div class="border border-stone-200 rounded p-3 hover-bg-stone-50">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h3 class="h6 font-semibold text-stone-900 mb-0">{{ $session->title }}</h3>
                                <x-badge :variant="$session->status === 'active' ? 'success' : 'default'">
                                    {{ ucfirst($session->status) }}
                                </x-badge>
                            </div>
                            @if($session->notes)
                                <p class="small text-stone-600 mt-1 mb-0" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    {{ Str::limit($session->notes, 100) }}
                                </p>
                            @endif
                            <div class="d-flex align-items-center gap-3 mt-2 small text-stone-500">
                                <span>{{ $session->created_at->format('M d, Y') }}</span>
                                <span>{{ $session->days->count() }} day{{ $session->days->count() !== 1 ? 's' : '' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </x-card>
    @endif
</div>
@endsection
