@extends('layouts.app')

@section('title', 'My Dashboard - Your Mind Aid')

@section('content')
<div class="container-fluid" style="max-width: 1200px;">
    <!-- Welcome Header -->
    <div class="mb-4">
        <h1 class="h3 fw-bold text-stone-900 mb-2">Welcome back!</h1>
        <p class="text-stone-600 mb-0">Here's your therapy overview</p>
    </div>

    <!-- Profile Card -->
    @if(isset($patientData))
    <x-patient.profile-card 
        :patient="$patientData" 
        :doctor="$patientData->doctor ?? null" 
    />
    @endif

    <!-- Quick Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card stats-card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="small text-stone-600 mb-1">Total Points</p>
                            <p class="h4 fw-bold text-stone-900 mb-0">{{ number_format($stats['total_points']) }}</p>
                        </div>
                        <div class="stats-icon bg-teal-100">
                            <i class="bi bi-star-fill text-teal-700 fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-6 col-md-3">
            <div class="card stats-card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="small text-stone-600 mb-1">Sessions</p>
                            <p class="h4 fw-bold text-stone-900 mb-0">{{ $stats['total_sessions'] }}</p>
                        </div>
                        <div class="stats-icon bg-indigo-100">
                            <i class="bi bi-calendar-check text-indigo-700 fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-6 col-md-3">
            <div class="card stats-card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="small text-stone-600 mb-1">Tasks Done</p>
                            <p class="h4 fw-bold text-stone-900 mb-0">{{ $stats['completed_tasks'] }}</p>
                        </div>
                        <div class="stats-icon bg-emerald-100">
                            <i class="bi bi-check-circle-fill text-emerald-700 fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-6 col-md-3">
            <div class="card stats-card border-0 shadow-sm">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="small text-stone-600 mb-1">Resources</p>
                            <p class="h4 fw-bold text-stone-900 mb-0">{{ $stats['total_resources'] }}</p>
                        </div>
                        <div class="stats-icon bg-amber-100">
                            <i class="bi bi-folder-fill text-amber-700 fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="row g-4">
        <!-- Left Column -->
        <div class="col-12 col-lg-8">
            <!-- Therapy Sessions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 pb-0 pt-3 px-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="h6 fw-semibold text-stone-900 mb-0">
                            <i class="bi bi-calendar-check me-2 text-patient-primary"></i>
                            Recent Therapy Sessions
                        </h5>
                        <a href="{{ route('patient.sessions.index') }}" class="btn btn-sm btn-outline-primary">
                            View All
                        </a>
                    </div>
                </div>
                <div class="card-body p-3">
                    @if($sessions->isEmpty())
                        <div class="empty-state py-4">
                            <i class="bi bi-calendar-x empty-state-icon"></i>
                            <p class="text-stone-500 mb-0">No therapy sessions yet.</p>
                        </div>
                    @else
                        @foreach($sessions as $session)
                            <x-patient.session-card :session="$session" />
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Resources from Doctor (Videos & PDFs) -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pb-0 pt-3 px-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="h6 fw-semibold text-stone-900 mb-0">
                            <i class="bi bi-folder-fill me-2 text-patient-primary"></i>
                            Resources from Doctor
                        </h5>
                        <a href="{{ route('patient.resources.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-arrow-right me-1"></i>
                            View All
                        </a>
                    </div>
                </div>
                <div class="card-body p-3">
                    @if($resources->isEmpty())
                        <div class="empty-state py-5 text-center">
                            <i class="bi bi-folder-x fs-1 text-stone-300 mb-3"></i>
                            <p class="text-stone-500 mb-0">No resources available yet.</p>
                            <p class="small text-stone-400 mt-2 mb-0">Your doctor will share videos and PDFs here.</p>
                        </div>
                    @else
                        <div class="row g-3">
                            @foreach($resources as $resource)
                                <div class="col-12 col-md-6">
                                    <x-patient.resource-card :resource="$resource" />
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            
            <!-- Tasks -->
            @if(isset($tasks) && $tasks->isNotEmpty())
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-header bg-white border-0 pb-0 pt-3 px-3">
                    <div class="d-flex align-items-center justify-content-between">
                        <h5 class="h6 fw-semibold text-stone-900 mb-0">
                            <i class="bi bi-check-square me-2 text-patient-primary"></i>
                            Recent Tasks
                        </h5>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="list-group list-group-flush">
                        @foreach($tasks as $task)
                            <div class="list-group-item border-0 px-0 py-2">
                                <div class="d-flex align-items-start gap-2">
                                    <div class="flex-shrink-0 mt-1">
                                        @if($task->status === 'completed')
                                            <i class="bi bi-check-circle-fill text-success"></i>
                                        @else
                                            <i class="bi bi-circle text-stone-300"></i>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="small fw-semibold text-stone-900 mb-1">{{ $task->title }}</h6>
                                        @if($task->description)
                                            <p class="small text-stone-600 mb-0">{{ Str::limit($task->description, 80) }}</p>
                                        @endif
                                        <p class="small text-stone-500 mt-1 mb-0">
                                            <i class="bi bi-calendar3 me-1"></i>
                                            {{ $task->created_at->format('M d, Y') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column -->
        <div class="col-12 col-lg-4">
            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white border-0 pb-0 pt-3 px-3">
                    <h5 class="h6 fw-semibold text-stone-900 mb-0">
                        <i class="bi bi-lightning-fill me-2 text-patient-primary"></i>
                        Quick Actions
                    </h5>
                </div>
                <div class="card-body p-3">
                    <div class="d-grid gap-2">
                        <a href="{{ route('patient.appointments.index') }}" class="btn btn-outline-primary quick-action-btn">
                            <i class="bi bi-calendar-event me-2"></i>
                            View Appointments
                        </a>
                        <a href="{{ route('patient.assessments.index') }}" class="btn btn-outline-primary quick-action-btn">
                            <i class="bi bi-clipboard-check me-2"></i>
                            Complete Assessment
                        </a>
                        <a href="{{ route('patient.journal.create') }}" class="btn btn-outline-primary quick-action-btn">
                            <i class="bi bi-journal-text me-2"></i>
                            Add Journal Entry
                        </a>
                        <a href="{{ route('patient.messages.index') }}" class="btn btn-outline-primary quick-action-btn">
                            <i class="bi bi-chat-dots me-2"></i>
                            Message Doctor
                        </a>
                    </div>
                </div>
            </div>

            <!-- Upcoming Appointments -->
            @if($upcomingAppointments->isNotEmpty())
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 pb-0 pt-3 px-3">
                        <h5 class="h6 fw-semibold text-stone-900 mb-0">
                            <i class="bi bi-calendar-event me-2 text-patient-primary"></i>
                            Upcoming Appointments
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        @foreach($upcomingAppointments as $appointment)
                            <div class="border-bottom border-stone-200 pb-2 mb-2">
                                <p class="small fw-semibold text-stone-900 mb-1">
                                    {{ $appointment->appointment_date->format('M d, Y') }}
                                </p>
                                <p class="small text-stone-600 mb-0">
                                    {{ $appointment->appointment_date->format('h:i A') }}
                                </p>
                            </div>
                        @endforeach
                        <a href="{{ route('patient.appointments.index') }}" class="btn btn-sm btn-outline-primary w-100 mt-2">
                            View All
                        </a>
                    </div>
                </div>
            @endif

            <!-- Pending Assessments -->
            @if($pendingAssessments->isNotEmpty())
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 pb-0 pt-3 px-3">
                        <h5 class="h6 fw-semibold text-stone-900 mb-0">
                            <i class="bi bi-clipboard-check me-2 text-patient-primary"></i>
                            Pending Assessments
                        </h5>
                    </div>
                    <div class="card-body p-3">
                        @foreach($pendingAssessments as $assessment)
                            <div class="border-bottom border-stone-200 pb-2 mb-2">
                                <p class="small fw-semibold text-stone-900 mb-1">
                                    {{ $assessment->title ?? 'Assessment' }}
                                </p>
                                <a href="{{ route('patient.assessments.show', $assessment->id) }}" class="btn btn-sm btn-primary">
                                    Complete
                                </a>
                            </div>
                        @endforeach
                        <a href="{{ route('patient.assessments.index') }}" class="btn btn-sm btn-outline-primary w-100 mt-2">
                            View All
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
