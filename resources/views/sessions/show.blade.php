@extends('layouts.app')

@section('title', $session->title)

@section('content')
<div class="container-fluid" style="max-width: 1152px;" x-data="sessionTimeline()">
    <!-- Header -->
    <div class="mb-4 d-flex align-items-center justify-content-between">
        <div>
            <h1 class="h2 fw-bold text-stone-900">{{ $session->title }}</h1>
            <p class="text-stone-600 mt-2 mb-0">
                <a href="{{ route('patients.show', $patient) }}" class="text-decoration-none hover-text-teal-700">{{ $patient->name }}</a>
                <span class="mx-2">•</span>
                <x-badge :variant="$session->status === 'active' ? 'success' : 'default'">
                    {{ ucfirst($session->status) }}
                </x-badge>
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a
                href="{{ route('patients.sessions.edit', [$patient, $session]) }}"
                class="btn btn-primary d-flex align-items-center gap-2"
            >
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <span>Edit Session</span>
            </a>
            <a
                href="{{ route('patients.sessions.index', $patient) }}"
                class="btn btn-outline-secondary d-flex align-items-center gap-2"
            >
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                <span>Back</span>
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
            class="alert alert-success alert-dismissible fade show mb-4"
            x-init="setTimeout(() => show = false, 5000)"
        >
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Session Info -->
    <x-card class="mb-4">
        <h2 class="h5 font-semibold text-stone-900 mb-3">Session Information</h2>
        @if($session->notes)
            <div>
                <p class="text-stone-700 mb-0" style="white-space: pre-wrap;">{{ $session->notes }}</p>
            </div>
        @else
            <p class="text-stone-500 fst-italic mb-0">No notes added yet.</p>
        @endif
        <div class="mt-3 d-flex align-items-center gap-3 small text-stone-500">
            <span>Created: {{ $session->created_at->format('M d, Y') }}</span>
            <span>Last updated: {{ $session->updated_at->format('M d, Y') }}</span>
        </div>
    </x-card>

    <!-- Days Timeline -->
    <x-card>
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h2 class="h5 font-semibold text-stone-900 mb-0">Day Entries</h2>
            <button
                @click="openDayModal()"
                class="btn btn-primary d-flex align-items-center gap-2"
            >
                <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Add Day</span>
            </button>
        </div>

        @if($session->days->isEmpty())
            <div class="text-center py-5 text-stone-500">
                <svg class="mx-auto mb-3 text-stone-400" style="width: 64px; height: 64px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p class="mb-0">No day entries yet.</p>
                <p class="small mt-2 mb-0">Click "Add Day" to start tracking daily progress.</p>
            </div>
        @else
            <!-- Timeline -->
            <div class="d-flex flex-column gap-4">
                @foreach($session->days->sortByDesc('day_date') as $day)
                    <div class="position-relative ps-4 pb-4 border-start border-stone-200">
                        <!-- Timeline dot -->
                        <div class="position-absolute start-0 top-0 rounded-circle bg-primary" style="width: 16px; height: 16px; transform: translateX(-50%);"></div>
                        
                        <!-- Day content -->
                        <div class="bg-stone-50 rounded p-3 hover-shadow-md">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <h3 class="h6 font-semibold text-stone-900 mb-0">
                                    {{ $day->day_date->format('F d, Y') }}
                                </h3>
                                <div class="d-flex align-items-center gap-2">
                                    <button
                                        @click="editDay({{ $day->id }}, '{{ $day->day_date->format('Y-m-d') }}', `{{ addslashes($day->symptoms ?? '') }}`, `{{ addslashes($day->alerts ?? '') }}`, `{{ addslashes($day->tasks ?? '') }}`)"
                                        class="btn btn-sm btn-outline-secondary"
                                    >
                                        Edit
                                    </button>
                                    <form
                                        action="{{ route('patients.sessions.days.destroy', [$patient, $session, $day]) }}"
                                        method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm('Are you sure you want to delete this day entry?');"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-danger"
                                        >
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="row g-3">
                                @if($day->symptoms)
                                    <div class="col-12 col-md-4">
                                        <h4 class="small font-medium text-stone-500 mb-1">Symptoms</h4>
                                        <p class="text-stone-700 small mb-0" style="white-space: pre-wrap;">{{ $day->symptoms }}</p>
                                    </div>
                                @endif
                                @if($day->alerts)
                                    <div class="col-12 col-md-4">
                                        <h4 class="small font-medium text-stone-500 mb-1">Alerts</h4>
                                        <p class="text-stone-700 small mb-0" style="white-space: pre-wrap;">{{ $day->alerts }}</p>
                                    </div>
                                @endif
                                @if($day->tasks)
                                    <div class="col-12 col-md-4">
                                        <h4 class="small font-medium text-stone-500 mb-1">Tasks</h4>
                                        <p class="text-stone-700 small mb-0" style="white-space: pre-wrap;">{{ $day->tasks }}</p>
                                    </div>
                                @endif
                            </div>

                            @if(!$day->symptoms && !$day->alerts && !$day->tasks)
                                <p class="text-stone-400 fst-italic small mb-0">No entries for this day.</p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-card>

    <!-- Day Entry Modal -->
    <x-day-entry-modal 
        :patient="$patient"
        :session="$session"
    />
</div>

<script>
function sessionTimeline() {
    return {
        dayModalOpen: false,
        editingDayId: null,
        formData: {
            day_date: '',
            symptoms: '',
            alerts: '',
            tasks: '',
        },
        saving: false,
        saveTimeout: null,

        openDayModal() {
            this.editingDayId = null;
            this.formData = {
                day_date: new Date().toISOString().split('T')[0],
                symptoms: '',
                alerts: '',
                tasks: '',
            };
            this.dayModalOpen = true;
        },

        editDay(id, date, symptoms, alerts, tasks) {
            this.editingDayId = id;
            this.formData = {
                day_date: date,
                symptoms: symptoms || '',
                alerts: alerts || '',
                tasks: tasks || '',
            };
            this.dayModalOpen = true;
        },

        async saveDay() {
            if (this.saving) return;
            
            this.saving = true;
            clearTimeout(this.saveTimeout);

            const url = this.editingDayId
                ? `{{ route('patients.sessions.days.update', [$patient, $session, 'DAY_ID']) }}`.replace('DAY_ID', this.editingDayId)
                : `{{ route('patients.sessions.days.store', [$patient, $session]) }}`;

            const method = this.editingDayId ? 'PUT' : 'POST';
            const formData = new FormData();
            formData.append('day_date', this.formData.day_date);
            formData.append('symptoms', this.formData.symptoms);
            formData.append('alerts', this.formData.alerts);
            formData.append('tasks', this.formData.tasks);
            formData.append('_token', '{{ csrf_token() }}');
            if (this.editingDayId) {
                formData.append('_method', 'PUT');
            }

            try {
                const response = await fetch(url, {
                    method: method,
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                const data = await response.json();

                if (data.success) {
                    this.dayModalOpen = false;
                    window.location.reload();
                } else {
                    alert(data.error || 'An error occurred while saving.');
                }
            } catch (error) {
                console.error('Error saving day:', error);
                alert('An error occurred while saving.');
            } finally {
                this.saving = false;
            }
        },

        debouncedSave() {
            clearTimeout(this.saveTimeout);
            this.saveTimeout = setTimeout(() => {
                this.saveDay();
            }, 2000);
        },
    };
}
</script>
@endsection
