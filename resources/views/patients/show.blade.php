@extends('layouts.app')

@section('title', $patient->name)

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-stone-900">{{ $patient->name }}</h1>
            <p class="text-stone-600 mt-2">Patient Profile</p>
        </div>
        <div class="flex items-center space-x-2">
            <a
                href="{{ route('patients.edit', $patient) }}"
                class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center space-x-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <span>Edit</span>
            </a>
            <form
                action="{{ route('patients.destroy', $patient) }}"
                method="POST"
                class="inline"
                onsubmit="return confirm('Are you sure you want to deactivate this patient?');"
            >
                @csrf
                @method('DELETE')
                <button
                    type="submit"
                    class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200 flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            class="mb-6 p-4 bg-emerald-100 border border-emerald-400 text-emerald-700 rounded-lg"
            x-init="setTimeout(() => show = false, 5000)"
        >
            {{ session('success') }}
        </div>
    @endif

    <!-- Password Display (Only shown once after creation) -->
    @if($password && $patientCreated)
        <x-card class="mb-6 bg-yellow-50 border-yellow-200">
            <div class="flex items-start space-x-4">
                <div class="flex-shrink-0">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-yellow-900 mb-2">Patient Password</h3>
                    <p class="text-yellow-800 mb-4">This password will not be shown again. Please save it securely.</p>
                    <div class="flex items-center space-x-2">
                        <div class="flex-1 px-4 py-3 bg-white border-2 border-yellow-300 rounded-lg font-mono text-lg font-bold text-stone-900" x-data="{ copied: false }">
                            <span id="password-text">{{ $password }}</span>
                        </div>
                        <button
                            type="button"
                            onclick="copyPassword()"
                            class="px-4 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition-colors duration-200 flex items-center space-x-2"
                            id="copy-btn"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Main Info Card -->
        <x-card class="md:col-span-2">
            <h2 class="text-xl font-semibold text-stone-900 mb-4">Patient Information</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="text-sm font-medium text-stone-500">Full Name</label>
                    <p class="text-stone-900 font-medium mt-1">{{ $patient->name }}</p>
                </div>
                
                <div>
                    <label class="text-sm font-medium text-stone-500">Email</label>
                    <p class="text-stone-900 font-medium mt-1">{{ $patient->email }}</p>
                </div>
                
                <div>
                    <label class="text-sm font-medium text-stone-500">Phone</label>
                    <p class="text-stone-900 font-medium mt-1">{{ $patient->phone ?? '—' }}</p>
                </div>
                
                <div>
                    <label class="text-sm font-medium text-stone-500">Status</label>
                    <div class="mt-1">
                        <x-badge :variant="$patient->status === 'active' ? 'success' : 'default'">
                            {{ ucfirst($patient->status) }}
                        </x-badge>
                    </div>
                </div>
                
                <div>
                    <label class="text-sm font-medium text-stone-500">Created</label>
                    <p class="text-stone-900 font-medium mt-1">{{ $patient->created_at->format('M d, Y') }}</p>
                </div>
            </div>
        </x-card>

        <!-- Photo & Doctor Card -->
        <x-card>
            <h2 class="text-xl font-semibold text-stone-900 mb-4">Photo</h2>
            
            @if($patient->photo_path)
                <img 
                    src="{{ $patient->photo_url }}" 
                    alt="{{ $patient->name }}"
                    class="w-full rounded-lg object-cover mb-4"
                />
            @else
                <div class="w-full aspect-square rounded-lg bg-teal-100 flex items-center justify-center mb-4">
                    <span class="text-teal-600 font-semibold text-4xl">
                        {{ strtoupper(substr($patient->name, 0, 1)) }}
                    </span>
                </div>
            @endif

            @if($patient->doctor)
                <div class="pt-4 border-t border-stone-200">
                    <label class="text-sm font-medium text-stone-500">Assigned Doctor</label>
                    <p class="text-stone-900 font-medium mt-1">{{ $patient->doctor->name ?? $patient->doctor->email }}</p>
                </div>
            @endif
        </x-card>
    </div>

    <!-- Back Link -->
    <div class="mt-6">
        <a
            href="{{ route('patients.index') }}"
            class="text-teal-600 hover:text-teal-800 flex items-center space-x-2"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
        copyBtn.classList.add('bg-green-600');
        copyBtn.classList.remove('bg-yellow-600', 'hover:bg-yellow-700');
        
        setTimeout(() => {
            copyText.textContent = originalText;
            copyBtn.classList.remove('bg-green-600');
            copyBtn.classList.add('bg-yellow-600', 'hover:bg-yellow-700');
        }, 2000);
    });
}
</script>
@endsection
