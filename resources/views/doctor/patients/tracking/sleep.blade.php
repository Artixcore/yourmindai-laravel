@extends('layouts.app')

@section('title', 'Sleep Logs')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('dashboard')],
            ['label' => 'Patients', 'url' => route('patients.index')],
            ['label' => $patient->name ?? $patient->full_name ?? optional($patient->user)->name ?? 'Patient', 'url' => route('patients.show', $patient)],
            ['label' => 'Tracking', 'url' => route('patients.tracking.index', $patient)],
            ['label' => 'Sleep Logs']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">Sleep Logs</h1>
        <p class="text-muted mb-0">Sleep tracking entries for {{ $patient->name ?? $patient->full_name ?? optional($patient->user)->name ?? 'Patient' }}</p>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($sleepLogs->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-moon-stars text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 mb-2">No sleep logs</h5>
            <p class="text-muted mb-0">The client has not logged any sleep entries yet.</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 ps-4">Date</th>
                        <th class="border-0">Hours</th>
                        <th class="border-0">Quality</th>
                        <th class="border-0">Notes</th>
                        <th class="border-0 pe-4">Created</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sleepLogs as $log)
                    <tr>
                        <td class="ps-4">{{ $log->sleep_date?->format('M d, Y') ?? '—' }}</td>
                        <td>{{ $log->hours_slept ?? '—' }} hrs</td>
                        <td>{{ $log->sleep_quality ?? '—' }}</td>
                        <td>{{ Str::limit($log->notes ?? '', 60) ?: '—' }}</td>
                        <td class="pe-4">{{ $log->created_at?->format('M d, Y') ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="p-3">
            {{ $sleepLogs->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('patients.tracking.index', $patient) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Tracking
    </a>
</div>
@endsection
