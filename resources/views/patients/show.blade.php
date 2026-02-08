@extends('layouts.app')

@section('title', $patient->name)

@section('content')
<div class="container-fluid" style="max-width: 1024px;">
    <!-- Header -->
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h1 class="h2 fw-bold text-stone-900">{{ $patient->name }}</h1>
            <p class="text-stone-600 mt-2 mb-0">Patient Profile</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a
                href="{{ route('patients.edit', $patient) }}"
                class="btn btn-primary d-flex align-items-center gap-2"
            >
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <span>Edit</span>
            </a>
            <form
                action="{{ route('patients.destroy', $patient) }}"
                method="POST"
                class="d-inline"
                onsubmit="return confirm('Are you sure you want to deactivate this patient?');"
            >
                @csrf
                @method('DELETE')
                <button
                    type="submit"
                    class="btn btn-danger d-flex align-items-center gap-2"
                >
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    <span>Deactivate</span>
                </button>
            </form>
        </div>
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

    <!-- Credentials Display (Only shown once after creation) -->
    @if($password && $patientCreated)
        <x-card class="mb-4 bg-yellow-50 border-yellow-200">
            <div class="d-flex align-items-start gap-3">
                <div class="flex-shrink-0">
                    <svg style="width: 24px; height: 24px;" class="text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="flex-grow-1">
                    <h3 class="h5 font-semibold text-yellow-900 mb-2">Patient Login Credentials</h3>
                    <p class="text-yellow-800 mb-3">These credentials will not be shown again. Please save them securely. The patient can use these to log in at <strong>/client</strong>.</p>
                    
                    <!-- Username -->
                    @if(isset($username) && $username)
                    <div class="mb-3">
                        <label class="small font-medium text-yellow-900 mb-1 d-block">Username</label>
                        <div class="d-flex align-items-center gap-2">
                            <div class="flex-grow-1 px-3 py-2 bg-white border border-yellow-300 rounded font-monospace fw-bold text-stone-900">
                                <span id="username-text">{{ $username }}</span>
                            </div>
                            <button
                                type="button"
                                onclick="copyUsername()"
                                class="btn btn-warning btn-sm d-flex align-items-center gap-2"
                                id="copy-username-btn"
                            >
                                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                <span id="copy-username-text">Copy</span>
                            </button>
                        </div>
                    </div>
                    @endif

                    <!-- Password -->
                    <div>
                        <label class="small font-medium text-yellow-900 mb-1 d-block">Password</label>
                        <div class="d-flex align-items-center gap-2">
                            <div class="flex-grow-1 px-3 py-2 bg-white border border-yellow-300 rounded font-monospace fw-bold text-stone-900">
                                <span id="password-text">{{ $password }}</span>
                            </div>
                            <button
                                type="button"
                                onclick="copyPassword()"
                                class="btn btn-warning btn-sm d-flex align-items-center gap-2"
                                id="copy-password-btn"
                            >
                                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                <span id="copy-password-text">Copy</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </x-card>
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
                    
                    <div>
                        <label class="small font-medium text-stone-500">Created</label>
                        <p class="text-stone-900 font-medium mt-1 mb-0">{{ $patient->created_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </x-card>
        </div>

        <!-- Photo & Doctor Card -->
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

                @if($patient->doctor)
                    <div class="pt-3 border-top border-stone-200">
                        <label class="small font-medium text-stone-500">Assigned Doctor</label>
                        <p class="text-stone-900 font-medium mt-1 mb-0">{{ $patient->doctor->name ?? $patient->doctor->email }}</p>
                    </div>
                @endif
            </x-card>
        </div>
    </div>

    <!-- Sessions Section -->
    <x-card class="mt-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h2 class="h5 font-semibold text-stone-900 mb-0">Therapy Sessions</h2>
            <a
                href="{{ route('patients.sessions.create', $patient) }}"
                class="btn btn-primary d-flex align-items-center gap-2"
            >
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Create Session</span>
            </a>
        </div>

        @php
            $sessions = $patient->sessions()->with('doctor')->orderBy('created_at', 'desc')->get();
        @endphp

        @if($sessions->isEmpty())
            <div class="text-center py-5 text-stone-500">
                <svg class="mx-auto mb-3 text-stone-400" style="width: 48px; height: 48px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="mb-0">No therapy sessions yet.</p>
                <p class="small mt-2 mb-0">Create your first session to start tracking therapy progress.</p>
            </div>
        @else
            <div class="d-flex flex-column gap-3">
                @foreach($sessions as $session)
                    <div class="border border-stone-200 rounded p-3 hover-bg-stone-50">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <a
                                        href="{{ route('patients.sessions.show', [$patient, $session]) }}"
                                        class="h6 font-semibold text-stone-900 hover-text-teal-700 text-decoration-none mb-0"
                                    >
                                        {{ $session->title }}
                                    </a>
                                    <x-badge :variant="$session->status === 'active' ? 'success' : 'default'">
                                        {{ ucfirst($session->status) }}
                                    </x-badge>
                                </div>
                                @if($session->notes)
                                    <p class="small text-stone-600 mt-1 mb-0" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ Str::limit($session->notes, 100) }}</p>
                                @endif
                                <div class="d-flex align-items-center gap-3 mt-2 small text-stone-500">
                                    <span>{{ $session->created_at->format('M d, Y') }}</span>
                                    <span>{{ $session->days->count() }} day{{ $session->days->count() !== 1 ? 's' : '' }}</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2 ms-3">
                                <a
                                    href="{{ route('patients.sessions.show', [$patient, $session]) }}"
                                    class="btn btn-sm btn-outline-secondary"
                                >
                                    View
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-card>

    <!-- Psychometric Assessments Section -->
    <x-card class="mt-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h2 class="h5 font-semibold text-stone-900 mb-0">Psychometric Assessments</h2>
            <a
                href="{{ route('patients.psychometric.index', $patient) }}"
                class="btn btn-primary d-flex align-items-center gap-2"
            >
                <i class="bi bi-clipboard-check"></i>
                <span>Manage Assessments</span>
            </a>
        </div>

        @php
            $patientProfile = \App\Models\PatientProfile::where('doctor_id', $patient->doctor_id)
                ->where(function($query) use ($patient) {
                    $query->where('full_name', $patient->name)
                          ->orWhere('phone', $patient->phone);
                })
                ->first();
            
            if ($patient->email) {
                $user = \App\Models\User::where('email', $patient->email)->first();
                if ($user && !$patientProfile) {
                    $patientProfile = \App\Models\PatientProfile::where('user_id', $user->id)->first();
                }
            }
            
            $assessments = \App\Models\PsychometricAssessment::where(function($query) use ($patientProfile, $patient) {
                if ($patientProfile) {
                    $query->where('patient_profile_id', $patientProfile->id);
                }
                $query->orWhere('patient_id', $patient->id);
            })
            ->where('assigned_by_doctor_id', auth()->id())
            ->with('scale')
            ->orderBy('created_at', 'desc')
            ->get();
            
            $pendingCount = $assessments->where('status', 'pending')->count();
            $completedCount = $assessments->where('status', 'completed')->count();
        @endphp

        <div class="row g-3 mb-3">
            <div class="col-12 col-md-4">
                <div class="border rounded p-3 text-center">
                    <p class="text-muted small mb-1">Total Assessments</p>
                    <h4 class="fw-bold mb-0">{{ $assessments->count() }}</h4>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="border rounded p-3 text-center">
                    <p class="text-muted small mb-1">Pending</p>
                    <h4 class="fw-bold text-warning mb-0">{{ $pendingCount }}</h4>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="border rounded p-3 text-center">
                    <p class="text-muted small mb-1">Completed</p>
                    <h4 class="fw-bold text-success mb-0">{{ $completedCount }}</h4>
                </div>
            </div>
        </div>

        @if($assessments->isEmpty())
            <div class="text-center py-4 text-stone-500">
                <i class="bi bi-clipboard-check display-6 mb-3 d-block"></i>
                <p class="mb-0">No assessments assigned yet.</p>
                <p class="small mt-2 mb-0">Assign psychometric assessments to track patient progress.</p>
            </div>
        @else
            <div class="d-flex flex-column gap-2">
                @foreach($assessments->take(3) as $assessment)
                    <div class="border border-stone-200 rounded p-3 hover-bg-stone-50">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <h6 class="font-semibold text-stone-900 mb-0">{{ $assessment->scale->name ?? 'Assessment' }}</h6>
                                    @if($assessment->status === 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($assessment->status === 'in_progress')
                                        <span class="badge bg-warning">In Progress</span>
                                    @else
                                        <span class="badge bg-secondary">Pending</span>
                                    @endif
                                </div>
                                <div class="small text-stone-500">
                                    Assigned: {{ $assessment->assigned_at ? $assessment->assigned_at->format('M d, Y') : 'Recently' }}
                                    @if($assessment->status === 'completed' && $assessment->total_score !== null)
                                        | Score: {{ $assessment->total_score }}
                                    @endif
                                </div>
                            </div>
                            <a
                                href="{{ route('patients.psychometric.show', [$patient, $assessment]) }}"
                                class="btn btn-sm btn-outline-primary"
                            >
                                View
                            </a>
                        </div>
                    </div>
                @endforeach
                @if($assessments->count() > 3)
                    <div class="text-center mt-2">
                        <a href="{{ route('patients.psychometric.index', $patient) }}" class="btn btn-sm btn-link">
                            View all {{ $assessments->count() }} assessments
                        </a>
                    </div>
                @endif
            </div>
        @endif
    </x-card>

    <!-- General Assessments Section -->
    <x-card class="mt-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h2 class="h5 font-semibold text-stone-900 mb-0">General Assessments</h2>
            <div class="d-flex gap-2">
                <a href="{{ route('patients.general-assessments.create', $patient) }}" class="btn btn-primary d-flex align-items-center gap-2">
                    <i class="bi bi-plus-lg"></i>
                    <span>Create Assessment</span>
                </a>
                <a href="{{ route('patients.general-assessments.index', $patient) }}" class="btn btn-outline-primary d-flex align-items-center gap-2">
                    <i class="bi bi-clipboard-data"></i>
                    <span>View All</span>
                </a>
            </div>
        </div>

        @php
            $generalAssessments = \App\Models\GeneralAssessment::where('patient_id', $patient->id)
                ->orderBy('assigned_at', 'desc')
                ->take(3)
                ->get();
        @endphp

        @if($generalAssessments->isEmpty())
            <div class="text-center py-4 text-stone-500">
                <i class="bi bi-clipboard-data text-stone-400" style="font-size: 2rem;"></i>
                <p class="mb-0 mt-2">No general assessments yet.</p>
                <p class="small mt-1 mb-0">Create custom assessments for this patient.</p>
            </div>
        @else
            <div class="d-flex flex-column gap-3">
                @foreach($generalAssessments as $assessment)
                    <div class="border border-stone-200 rounded p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="flex-grow-1">
                                <h6 class="font-semibold text-stone-900 mb-1">{{ $assessment->title }}</h6>
                                <div class="d-flex align-items-center gap-2">
                                    @if($assessment->status === 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($assessment->status === 'in_progress')
                                        <span class="badge bg-warning">In Progress</span>
                                    @else
                                        <span class="badge bg-secondary">Pending</span>
                                    @endif
                                    <small class="text-stone-500">
                                        {{ $assessment->questions->count() }} questions
                                    </small>
                                </div>
                            </div>
                            <a href="{{ route('patients.general-assessments.show', [$patient, $assessment]) }}" class="btn btn-sm btn-outline-primary">
                                View
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-card>

    <!-- Homework & Techniques Section -->
    <x-card class="mt-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h2 class="h5 font-semibold text-stone-900 mb-0">Homework & Techniques</h2>
            <div class="d-flex gap-2">
                <a href="{{ route('patients.homework.create', $patient) }}" class="btn btn-primary d-flex align-items-center gap-2">
                    <i class="bi bi-plus-lg"></i>
                    <span>Assign Homework</span>
                </a>
                <a href="{{ route('patients.homework.index', $patient) }}" class="btn btn-outline-primary d-flex align-items-center gap-2">
                    <i class="bi bi-journal-check"></i>
                    <span>View All</span>
                </a>
            </div>
        </div>

        @php
            $homework = \App\Models\HomeworkAssignment::where('patient_id', $patient->id)
                ->whereIn('status', ['assigned', 'in_progress'])
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get();
        @endphp

        @if($homework->isEmpty())
            <div class="text-center py-4 text-stone-500">
                <i class="bi bi-journal-check text-stone-400" style="font-size: 2rem;"></i>
                <p class="mb-0 mt-2">No active homework assignments.</p>
                <p class="small mt-1 mb-0">Assign therapy techniques to track patient progress.</p>
            </div>
        @else
            <div class="d-flex flex-column gap-2">
                @foreach($homework as $hw)
                    <div class="border border-stone-200 rounded p-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2">
                                    <strong class="text-stone-900">{{ $hw->title }}</strong>
                                    <span class="badge bg-light text-dark">{{ ucfirst(str_replace('_', ' ', $hw->homework_type)) }}</span>
                                    @if($hw->status === 'in_progress')
                                        <span class="badge bg-primary">In Progress</span>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ route('patients.homework.show', [$patient, $hw]) }}" class="btn btn-sm btn-outline-primary">
                                View
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-card>

    <!-- Goals Section -->
    <x-card class="mt-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h2 class="h5 font-semibold text-stone-900 mb-0">Goals</h2>
            <div class="d-flex gap-2">
                <a href="{{ route('patients.goals.create', $patient) }}" class="btn btn-primary d-flex align-items-center gap-2">
                    <i class="bi bi-plus-lg"></i>
                    <span>New Goal</span>
                </a>
                <a href="{{ route('patients.goals.index', $patient) }}" class="btn btn-outline-primary d-flex align-items-center gap-2">
                    <i class="bi bi-bullseye"></i>
                    <span>View All</span>
                </a>
            </div>
        </div>
        @php
            $goals = \App\Models\Goal::where('patient_id', $patient->id)->orderBy('start_date', 'desc')->take(5)->get();
        @endphp
        @if($goals->isEmpty())
            <div class="text-center py-4 text-stone-500">
                <i class="bi bi-bullseye text-stone-400" style="font-size: 2rem;"></i>
                <p class="mb-0 mt-2">No goals set yet.</p>
                <a href="{{ route('patients.goals.create', $patient) }}" class="btn btn-primary mt-2">Set first goal</a>
            </div>
        @else
            <div class="d-flex flex-column gap-2">
                @foreach($goals as $goal)
                    <div class="border border-stone-200 rounded p-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="flex-grow-1">
                                <strong class="text-stone-900">{{ $goal->title }}</strong>
                                @if($goal->start_date || $goal->end_date)
                                    <br><small class="text-muted">{{ $goal->start_date?->format('M d') }} @if($goal->end_date)– {{ $goal->end_date->format('M d, Y') }}@endif</small>
                                @endif
                            </div>
                            <a href="{{ route('patients.goals.edit', [$patient, $goal]) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-card>

    <!-- Routines Section -->
    <x-card class="mt-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h2 class="h5 font-semibold text-stone-900 mb-0">Daily Routines</h2>
            <div class="d-flex gap-2">
                <a href="{{ route('patients.routines.create', $patient) }}" class="btn btn-primary d-flex align-items-center gap-2">
                    <i class="bi bi-plus-lg"></i>
                    <span>Create Routine</span>
                </a>
                <a href="{{ route('patients.routines.index', $patient) }}" class="btn btn-outline-primary d-flex align-items-center gap-2">
                    <i class="bi bi-calendar-check"></i>
                    <span>View All</span>
                </a>
            </div>
        </div>

        @php
            $routines = \App\Models\Routine::where('patient_id', $patient->id)
                ->where('is_active', true)
                ->with('items')
                ->orderBy('created_at', 'desc')
                ->get();
        @endphp

        @if($routines->isEmpty())
            <div class="text-center py-4 text-stone-500">
                <i class="bi bi-calendar-check text-stone-400" style="font-size: 2rem;"></i>
                <p class="mb-0 mt-2">No routines created yet.</p>
                <p class="small mt-1 mb-0">Create daily routines to help structure the patient's day.</p>
            </div>
        @else
            <div class="d-flex flex-column gap-2">
                @foreach($routines as $routine)
                    <div class="border border-stone-200 rounded p-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="flex-grow-1">
                                <strong class="text-stone-900">{{ $routine->title }}</strong>
                                <div class="small text-stone-500 mt-1">
                                    {{ $routine->items->count() }} tasks | {{ ucfirst($routine->frequency) }}
                                </div>
                            </div>
                            <a href="{{ route('patients.routines.show', [$patient, $routine]) }}" class="btn btn-sm btn-outline-primary">
                                View
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-card>

    <!-- Tracking Logs Section -->
    <x-card class="mt-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h2 class="h5 font-semibold text-stone-900 mb-0">Tracking Logs</h2>
            <a href="{{ route('patients.tracking.index', $patient) }}" class="btn btn-outline-primary d-flex align-items-center gap-2">
                <i class="bi bi-graph-up"></i>
                <span>View All Logs</span>
            </a>
        </div>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="card border-primary border-opacity-25">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-emoji-smile text-primary" style="font-size: 1.5rem;"></i>
                            <strong>Mood Logs</strong>
                        </div>
                        @php
                            $moodCount = \App\Models\MoodLog::where('patient_id', $patient->id)->count();
                            $avgMood = \App\Models\MoodLog::where('patient_id', $patient->id)->avg('mood_rating');
                        @endphp
                        <div class="small text-muted">{{ $moodCount }} entries</div>
                        @if($avgMood)
                            <div class="small">Avg: <strong>{{ round($avgMood, 1) }}/10</strong></div>
                        @endif
                        <a href="{{ route('patients.tracking.mood', $patient) }}" class="btn btn-sm btn-primary mt-2">View</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-info border-opacity-25">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-moon-stars text-info" style="font-size: 1.5rem;"></i>
                            <strong>Sleep Logs</strong>
                        </div>
                        @php
                            $sleepCount = \App\Models\SleepLog::where('patient_id', $patient->id)->count();
                            $avgSleep = \App\Models\SleepLog::where('patient_id', $patient->id)->avg('hours_slept');
                        @endphp
                        <div class="small text-muted">{{ $sleepCount }} entries</div>
                        @if($avgSleep)
                            <div class="small">Avg: <strong>{{ round($avgSleep, 1) }} hrs</strong></div>
                        @endif
                        <a href="{{ route('patients.tracking.sleep', $patient) }}" class="btn btn-sm btn-info mt-2">View</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-success border-opacity-25">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-bicycle text-success" style="font-size: 1.5rem;"></i>
                            <strong>Exercise Logs</strong>
                        </div>
                        @php
                            $exerciseCount = \App\Models\ExerciseLog::where('patient_id', $patient->id)->count();
                            $totalMinutes = \App\Models\ExerciseLog::where('patient_id', $patient->id)->sum('duration_minutes');
                        @endphp
                        <div class="small text-muted">{{ $exerciseCount }} entries</div>
                        @if($totalMinutes)
                            <div class="small">Total: <strong>{{ round($totalMinutes / 60, 1) }} hrs</strong></div>
                        @endif
                        <a href="{{ route('patients.tracking.exercise', $patient) }}" class="btn btn-sm btn-success mt-2">View</a>
                    </div>
                </div>
            </div>
        </div>
    </x-card>

    <!-- Resources Section -->
    <x-card class="mt-4">
        <div class="d-flex align-items-center justify-content-between mb-3">
            <h2 class="h5 font-semibold text-stone-900 mb-0">Patient Resources</h2>
            <a
                href="{{ route('patients.resources.index', $patient) }}"
                class="btn btn-primary d-flex align-items-center gap-2"
            >
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>Manage Resources</span>
            </a>
        </div>

        @php
            $recentResources = \App\Models\PatientResource::where('patient_id', $patient->id)
                ->with(['session', 'sessionDay'])
                ->orderBy('created_at', 'desc')
                ->take(3)
                ->get();
        @endphp

        @if($recentResources->isEmpty())
            <div class="text-center py-5 text-stone-500">
                <svg class="mx-auto mb-3 text-stone-400" style="width: 48px; height: 48px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="mb-0">No resources yet.</p>
                <p class="small mt-2 mb-0">Add resources to share PDFs and YouTube videos with this patient.</p>
            </div>
        @else
            <div class="row g-3">
                @foreach($recentResources as $resource)
                    <div class="col-12 col-md-4">
                        <div class="border border-stone-200 rounded p-3 hover-bg-stone-50 h-100">
                            <div class="d-flex align-items-start gap-2 mb-2">
                                @if($resource->is_pdf)
                                    <svg style="width: 24px; height: 24px;" class="text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                @else
                                    <svg style="width: 24px; height: 24px;" class="text-red-600 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                    </svg>
                                @endif
                                <div class="flex-grow-1" style="min-width: 0;">
                                    <h3 class="small font-semibold text-stone-900 text-truncate mb-0">{{ $resource->title }}</h3>
                                    <p class="small text-stone-500 mt-1 mb-0">{{ $resource->created_at->format('M d, Y') }}</p>
                                </div>
                            </div>
                            @if($resource->session)
                                <p class="small text-stone-600 mt-2 mb-0">
                                    <span class="font-medium">Session:</span> {{ $resource->session->title }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            @if(\App\Models\PatientResource::where('patient_id', $patient->id)->count() > 3)
                <div class="mt-3 text-center">
                    <a
                        href="{{ route('patients.resources.index', $patient) }}"
                        class="small text-teal-700 hover-text-teal-800 font-medium text-decoration-none"
                    >
                        View all resources →
                    </a>
                </div>
            @endif
        @endif
    </x-card>

    <!-- Back Link -->
    <div class="mt-4">
        <a
            href="{{ route('patients.index') }}"
            class="text-teal-700 hover-text-teal-800 d-flex align-items-center gap-2 text-decoration-none"
        >
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            <span>Back to Patients</span>
        </a>
    </div>
</div>

<script>
function copyUsername() {
    const usernameText = document.getElementById('username-text').textContent;
    navigator.clipboard.writeText(usernameText).then(() => {
        const copyBtn = document.getElementById('copy-username-btn');
        const copyText = document.getElementById('copy-username-text');
        const originalText = copyText.textContent;
        copyText.textContent = 'Copied!';
        copyBtn.classList.remove('btn-warning');
        copyBtn.classList.add('btn-success');
        
        setTimeout(() => {
            copyText.textContent = originalText;
            copyBtn.classList.remove('btn-success');
            copyBtn.classList.add('btn-warning');
        }, 2000);
    });
}

function copyPassword() {
    const passwordText = document.getElementById('password-text').textContent;
    navigator.clipboard.writeText(passwordText).then(() => {
        const copyBtn = document.getElementById('copy-password-btn');
        const copyText = document.getElementById('copy-password-text');
        const originalText = copyText.textContent;
        copyText.textContent = 'Copied!';
        copyBtn.classList.remove('btn-warning');
        copyBtn.classList.add('btn-success');
        
        setTimeout(() => {
            copyText.textContent = originalText;
            copyBtn.classList.remove('btn-success');
            copyBtn.classList.add('btn-warning');
        }, 2000);
    });
}
</script>
@endsection
