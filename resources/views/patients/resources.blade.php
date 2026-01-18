@extends('layouts.app')

@section('title', $patient->name . ' - Resources')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h1 class="h2 fw-bold text-stone-900">{{ $patient->name }}</h1>
            <p class="text-stone-600 mt-2 mb-0">Patient Resources</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a
                href="{{ route('patients.show', $patient) }}"
                class="btn btn-outline-secondary d-flex align-items-center gap-2"
            >
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Back to Patient</span>
            </a>
            <button
                type="button"
                @click="$dispatch('resource-modal-open')"
                class="btn btn-primary d-flex align-items-center gap-2"
            >
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            class="alert alert-success alert-dismissible fade show mb-4"
            x-init="setTimeout(() => show = false, 5000)"
        >
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Filters -->
    <x-card class="mb-4">
        <div class="d-flex flex-wrap align-items-center gap-3">
            <span class="small font-medium text-stone-700">Filter:</span>
            
            <a
                href="{{ route('patients.resources.index', $patient) }}"
                class="btn btn-sm {{ !$selectedSessionId && !$selectedSessionDayId ? 'btn-primary' : 'btn-outline-secondary' }}"
            >
                All Resources
            </a>

            <div class="position-relative">
                <select
                    id="session-filter"
                    onchange="filterBySession(this.value)"
                    class="form-select form-select-sm"
                    style="padding-right: 2rem;"
                >
                    <option value="">By Session</option>
                    @foreach($sessions as $session)
                        <option value="{{ $session->id }}" {{ $selectedSessionId == $session->id ? 'selected' : '' }}>
                            {{ $session->title }}
                        </option>
                    @endforeach
                </select>
            </div>

            @if($selectedSessionId)
                <div class="position-relative">
                    <select
                        id="session-day-filter"
                        onchange="filterBySessionDay(this.value)"
                        class="form-select form-select-sm"
                        style="padding-right: 2rem;"
                    >
                        <option value="">By Day</option>
                        @foreach($sessionDays as $day)
                            <option value="{{ $day->id }}" {{ $selectedSessionDayId == $day->id ? 'selected' : '' }}>
                                {{ $day->day_date->format('M d, Y') }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
    </x-card>

    <!-- Resources Grid -->
    @if($resources->isEmpty())
        <x-card>
            <div class="text-center py-5">
                <svg class="mx-auto mb-3 text-stone-400" style="width: 64px; height: 64px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <h3 class="h5 font-semibold text-stone-900 mb-2">No Resources Yet</h3>
                <p class="text-stone-600 mb-4">Start by adding your first resource for this patient.</p>
                <button
                    type="button"
                    @click="$dispatch('resource-modal-open')"
                    class="btn btn-primary d-inline-flex align-items-center gap-2"
                >
                    <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Add Resource</span>
                </button>
            </div>
        </x-card>
    @else
        <div class="row g-4">
            @foreach($resources as $resource)
                <div class="col-12 col-md-6 col-lg-4">
                    <x-resource-card :resource="$resource" :patient="$patient" />
                </div>
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
