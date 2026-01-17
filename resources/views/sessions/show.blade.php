@extends('layouts.app')

@section('title', $session->title)

@section('content')
<div class="max-w-6xl mx-auto" x-data="sessionTimeline()">
    <!-- Header -->
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-bold text-stone-900">{{ $session->title }}</h1>
            <p class="text-stone-600 mt-2">
                <a href="{{ route('patients.show', $patient) }}" class="hover:text-teal-600">{{ $patient->name }}</a>
                <span class="mx-2">•</span>
                <x-badge :variant="$session->status === 'active' ? 'success' : 'default'">
                    {{ ucfirst($session->status) }}
                </x-badge>
            </p>
        </div>
        <div class="flex items-center space-x-2">
            <a
                href="{{ route('patients.sessions.edit', [$patient, $session]) }}"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center space-x-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                <span>Edit Session</span>
            </a>
            <a
                href="{{ route('patients.sessions.index', $patient) }}"
                class="px-4 py-2 bg-stone-100 text-stone-700 rounded-lg hover:bg-stone-200 transition-colors duration-200 flex items-center space-x-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            class="mb-6 p-4 bg-emerald-100 border border-emerald-400 text-emerald-700 rounded-lg"
            x-init="setTimeout(() => show = false, 5000)"
        >
            {{ session('success') }}
        </div>
    @endif

    <!-- Session Info -->
    <x-card class="mb-6">
        <h2 class="text-xl font-semibold text-stone-900 mb-4">Session Information</h2>
        @if($session->notes)
            <div class="prose max-w-none">
                <p class="text-stone-700 whitespace-pre-wrap">{{ $session->notes }}</p>
            </div>
        @else
            <p class="text-stone-500 italic">No notes added yet.</p>
        @endif
        <div class="mt-4 flex items-center space-x-4 text-sm text-stone-500">
            <span>Created: {{ $session->created_at->format('M d, Y') }}</span>
            <span>Last updated: {{ $session->updated_at->format('M d, Y') }}</span>
        </div>
    </x-card>

    <!-- Days Timeline -->
    <x-card>
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-semibold text-stone-900">Day Entries</h2>
            <button
                @click="openDayModal()"
                class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 transition-colors duration-200 flex items-center space-x-2"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span>Add Day</span>
            </button>
        </div>

        @if($session->days->isEmpty())
            <div class="text-center py-12 text-stone-500">
                <svg class="w-16 h-16 mx-auto mb-4 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <p>No day entries yet.</p>
                <p class="text-sm mt-2">Click "Add Day" to start tracking daily progress.</p>
            </div>
        @else
            <!-- Timeline -->
            <div class="space-y-6">
                @foreach($session->days->sortByDesc('day_date') as $day)
                    <div class="relative pl-8 pb-6 border-l-2 border-stone-200 last:border-l-0 last:pb-0">
                        <!-- Timeline dot -->
                        <div class="absolute left-0 top-0 w-4 h-4 bg-teal-600 rounded-full -translate-x-1/2"></div>
                        
                        <!-- Day content -->
                        <div class="bg-stone-50 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-lg font-semibold text-stone-900">
                                    {{ $day->day_date->format('F d, Y') }}
                                </h3>
                                <div class="flex items-center space-x-2">
                                    <button
                                        @click="editDay({{ $day->id }}, '{{ $day->day_date->format('Y-m-d') }}', `{{ addslashes($day->symptoms ?? '') }}`, `{{ addslashes($day->alerts ?? '') }}`, `{{ addslashes($day->tasks ?? '') }}`)"
                                        class="px-3 py-1 text-sm bg-stone-100 text-stone-700 rounded hover:bg-stone-200 transition-colors"
                                    >
                                        Edit
                                    </button>
                                    <form
                                        action="{{ route('patients.sessions.days.destroy', [$patient, $session, $day]) }}"
                                        method="POST"
                                        class="inline"
                                        onsubmit="return confirm('Are you sure you want to delete this day entry?');"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="px-3 py-1 text-sm bg-red-100 text-red-700 rounded hover:bg-red-200 transition-colors"
                                        >
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                @if($day->symptoms)
                                    <div>
                                        <h4 class="text-sm font-medium text-stone-500 mb-1">Symptoms</h4>
                                        <p class="text-stone-700 whitespace-pre-wrap text-sm">{{ $day->symptoms }}</p>
                                    </div>
                                @endif
                                @if($day->alerts)
                                    <div>
                                        <h4 class="text-sm font-medium text-stone-500 mb-1">Alerts</h4>
                                        <p class="text-stone-700 whitespace-pre-wrap text-sm">{{ $day->alerts }}</p>
                                    </div>
                                @endif
                                @if($day->tasks)
                                    <div>
                                        <h4 class="text-sm font-medium text-stone-500 mb-1">Tasks</h4>
                                        <p class="text-stone-700 whitespace-pre-wrap text-sm">{{ $day->tasks }}</p>
                                    </div>
                                @endif
                            </div>

                            @if(!$day->symptoms && !$day->alerts && !$day->tasks)
                                <p class="text-stone-400 italic text-sm">No entries for this day.</p>
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
