@extends('layouts.app')

@section('title', 'Patient Comparison')

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-semibold">Patient Comparison</h1>
    <p class="text-muted mb-0">{{ $startDate }} to {{ $endDate }}</p>
</div>
<div class="card">
    <div class="card-body">
        @if($patientStats->isEmpty())
        <p class="mb-0">No patient data.</p>
        @else
        <table class="table">
            <thead><tr><th>Patient</th><th>Mood</th><th>Sleep</th><th>Exercise</th><th>Total</th></tr></thead>
            <tbody>
                @foreach($patientStats as $s)
                <tr>
                    <td>{{ optional(optional($s['patient'])->user)->name ?? '—' }}</td>
                    <td>{{ $s['mood_logs'] ?? 0 }}</td>
                    <td>{{ $s['sleep_logs'] ?? 0 }}</td>
                    <td>{{ $s['exercise_logs'] ?? 0 }}</td>
                    <td>{{ $s['total_logs'] ?? 0 }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>
</div>
@endsection
