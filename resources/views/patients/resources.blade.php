@extends('layouts.app')

@section('title', $patient->name . ' - Resources')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-stone-900">{{ $patient->name }}</h1>
            <p class="text-stone-600 mt-2">Patient Resources</p>
        </div>
        <div class="flex items-center space-x-2">
            <a
                href="{{ route('patients.show', $patient) }}"
                class="px-6 py-2 border border-stone-300 text-stone-700 rounded-lg hover:bg-stone-50 transition-colors duration-200 flex items-center space-x-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Back to Patient</span>
            </a>
            <button
                type="button"
                @click="$dispatch('resource-modal-open')"
                class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors duration-200 flex items-center space-x-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Add Resource</span>
            </button>
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

    <!-- Filters -->
    <x-card class="mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <span class="text-sm font-medium text-stone-700">Filter:</span>
            
            <a
                href="{{ route('patients.resources.index', $patient) }}"
                class="px-4 py-2 rounded-lg transition-colors duration-200 {{ !$selectedSessionId && !$selectedSessionDayId ? 'bg-teal-600 text-white' : 'bg-stone-100 text-stone-700 hover:bg-stone-200' }}"
            >
                All Resources
            </a>

            <div class="relative">
                <select
                    id="session-filter"
                    onchange="filterBySession(this.value)"
                    class="px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors duration-200 appearance-none bg-white pr-8"
                >
                    <option value="">By Session</option>
                    @foreach($sessions as $session)
                        <option value="{{ $session->id }}" {{ $selectedSessionId == $session->id ? 'selected' : '' }}>
                            {{ $session->title }}
                        </option>
                    @endforeach
                </select>
                <svg class="absolute right-2 top-1/2 transform -translate-y-1/2 w-5 h-5 text-stone-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </div>

            @if($selectedSessionId)
                <div class="relative">
                    <select
                        id="session-day-filter"
                        onchange="filterBySessionDay(this.value)"
                        class="px-4 py-2 border border-stone-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 transition-colors duration-200 appearance-none bg-white pr-8"
                    >
                        <option value="">By Day</option>
                        @foreach($sessionDays as $day)
                            <option value="{{ $day->id }}" {{ $selectedSessionDayId == $day->id ? 'selected' : '' }}>
                                {{ $day->day_date->format('M d, Y') }}
                            </option>
                        @endforeach
                    </select>
                    <svg class="absolute right-2 top-1/2 transform -translate-y-1/2 w-5 h-5 text-stone-500 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            @endif
        </div>
    </x-card>

    <!-- Resources Grid -->
    @if($resources->isEmpty())
        <x-card>
            <div class="text-center py-12">
                <svg class="w-16 h-16 mx-auto mb-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="text-lg font-semibold text-stone-900 mb-2">No Resources Yet</h3>
                <p class="text-stone-600 mb-6">Start by adding your first resource for this patient.</p>
                <button
                    type="button"
                    @click="$dispatch('resource-modal-open')"
                    class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors duration-200 inline-flex items-center space-x-2"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Add Resource</span>
                </button>
            </div>
        </x-card>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($resources as $resource)
                <x-resource-card :resource="$resource" :patient="$patient" />
            @endforeach
        </div>
    @endif
</div>

<!-- Resource Modal -->
<x-resource-modal :patient="$patient" :sessions="$sessions" :allSessionDays="$allSessionDays" />

<script>
function filterBySession(sessionId) {
    const url = new URL(window.location.href);
    if (sessionId) {
        url.searchParams.set('session_id', sessionId);
        url.searchParams.delete('session_day_id');
    } else {
        url.searchParams.delete('session_id');
        url.searchParams.delete('session_day_id');
    }
    window.location.href = url.toString();
}

function filterBySessionDay(dayId) {
    const url = new URL(window.location.href);
    if (dayId) {
        url.searchParams.set('session_day_id', dayId);
    } else {
        url.searchParams.delete('session_day_id');
    }
    window.location.href = url.toString();
}
</script>
@endsection
