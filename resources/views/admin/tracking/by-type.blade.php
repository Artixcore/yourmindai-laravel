@extends('layouts.app')
@section('title', 'Tracking by Type')
@section('content')
<div class="mb-4"><h1 class="h3 fw-semibold">Tracking: {{ ucfirst($type) }}</h1><p class="text-muted mb-0">{{ $startDate }} to {{ $endDate }}</p></div>
<div class="card"><div class="card-body">
@if(is_object($logs) && $logs->isEmpty())<p class="mb-0">No logs.</p>
@elseif(is_object($logs))
<table class="table"><thead><tr><th>Date</th><th>Patient</th></tr></thead><tbody>
@foreach($logs as $log)<tr><td>{{ $log->log_date ?? $log->sleep_date ?? $log->exercise_date ?? '-' }}</td><td>{{ optional(optional($log->patient)->user)->name ?? '-' }}</td></tr>@endforeach
</tbody></table>{{ $logs->links() }}
@else<p class="mb-0">No data.</p>@endif
</div></div>
@endsection
