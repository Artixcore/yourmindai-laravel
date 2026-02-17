@extends('layouts.app')
@section('title', 'All Tracking')
@section('content')
<div class="mb-4"><h1 class="h3 fw-semibold">All Tracking</h1><p class="text-muted mb-0">{{ $startDate }} to {{ $endDate }}</p></div>
<div class="card"><div class="card-body">
@if($allActivities->isEmpty())<p class="mb-0">No tracking data.</p>
@else
<table class="table"><thead><tr><th>Date</th><th>Type</th><th>Patient</th></tr></thead><tbody>
@foreach($allActivities as $a)<tr><td>{{ $a['date'] ?? '-' }}</td><td>{{ ucfirst($a['type'] ?? '-') }}</td><td>{{ $a['patient_name'] ?? '-' }}</td></tr>@endforeach
</tbody></table>
@endif
</div></div>
@endsection
