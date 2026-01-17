@extends('layouts.app')

@section('title', 'Sessions - ' . $patient->name)

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-stone-900">Therapy Sessions</h1>
            <p class="text-stone-600 mt-2">{{ $patient->name }}</p>
        </div>
        <div class="flex items-center space-x-4">
            <a
                href="{{ route('patients.show', $patient) }}"
                class="px-4 py-2 bg-stone-100 text-stone-700 rounded-lg hover:bg-stone-200 transition-colors duration-200 flex items-center space-x-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Back to Patient</span>
            </a>
            <a
                href="{{ route('patients.sessions.create', $patient) }}"
                class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors duration-200 flex items-center space-x-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Create Session</span>
            </a>
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

    <!-- Sessions List -->
    @if($sessions->isEmpty())
        <x-card class="text-center py-12">
            <svg class="mx-auto h-16 w-16 text-stone-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <h3 class="text-lg font-semibold text-stone-900 mb-2">No sessions found</h3>
            <p class="text-stone-600 mb-6">Get started by creating your first therapy session.</p>
            <a
                href="{{ route('patients.sessions.create', $patient) }}"
                class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors duration-200 inline-flex items-center space-x-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Create Session</span>
            </a>
        </x-card>
    @else
        <div class="grid grid-cols-1 gap-4">
            @foreach($sessions as $session)
                <x-card class="hover:shadow-xl transition-shadow">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3 mb-2">
                                <a
                                    href="{{ route('patients.sessions.show', [$patient, $session]) }}"
                                    class="text-xl font-semibold text-stone-900 hover:text-teal-600 transition-colors"
                                >
                                    {{ $session->title }}
                                </a>
                                <x-badge :variant="$session->status === 'active' ? 'success' : 'default'">
                                    {{ ucfirst($session->status) }}
                                </x-badge>
                            </div>
                            @if($session->notes)
                                <p class="text-stone-600 mb-3 line-clamp-2">{{ Str::limit($session->notes, 150) }}</p>
                            @endif
                            <div class="flex items-center space-x-4 text-sm text-stone-500">
                                <span>Created: {{ $session->created_at->format('M d, Y') }}</span>
                                <span>{{ $session->days->count() }} day{{ $session->days->count() !== 1 ? 's' : '' }}</span>
                                @if($session->doctor)
                                    <span>Doctor: {{ $session->doctor->name ?? $session->doctor->email }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center space-x-2 ml-4">
                            <a
                                href="{{ route('patients.sessions.show', [$patient, $session]) }}"
                                class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors"
                            >
                                View
                            </a>
                            <a
                                href="{{ route('patients.sessions.edit', [$patient, $session]) }}"
                                class="px-4 py-2 bg-stone-100 text-stone-700 rounded-lg hover:bg-stone-200 transition-colors"
                            >
                                Edit
                            </a>
                        </div>
                    </div>
                </x-card>
            @endforeach
        </div>
    @endif
</div>
@endsection
