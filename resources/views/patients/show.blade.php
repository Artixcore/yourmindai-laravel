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

    <!-- Password Display (Only shown once after creation) -->
    @if($password && $patientCreated)
        <x-card class="mb-4 bg-yellow-50 border-yellow-200">
            <div class="d-flex align-items-start gap-3">
                <div class="flex-shrink-0">
                    <svg style="width: 24px; height: 24px;" class="text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="flex-grow-1">
                    <h3 class="h5 font-semibold text-yellow-900 mb-2">Patient Password</h3>
                    <p class="text-yellow-800 mb-3">This password will not be shown again. Please save it securely.</p>
                    <div class="d-flex align-items-center gap-2">
                        <div class="flex-grow-1 px-3 py-2 bg-white border border-yellow-300 rounded font-monospace fw-bold text-stone-900" x-data="{ copied: false }">
                            <span id="password-text">{{ $password }}</span>
                        </div>
                        <button
                            type="button"
                            onclick="copyPassword()"
                            class="btn btn-warning d-flex align-items-center gap-2"
                            id="copy-btn"
                        >
                            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <span id="copy-text">Copy</span>
                        </button>
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
function copyPassword() {
    const passwordText = document.getElementById('password-text').textContent;
    navigator.clipboard.writeText(passwordText).then(() => {
        const copyBtn = document.getElementById('copy-btn');
        const copyText = document.getElementById('copy-text');
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
