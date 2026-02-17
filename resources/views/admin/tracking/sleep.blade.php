@extends('layouts.app')
@section('title', 'Sleep Logs')
@section('content')
<div class="mb-4"><h1 class="h3 fw-semibold">Sleep Logs</h1></div>
<div class="card"><div class="card-body">
@if($sleepLogs->isEmpty())<p class="mb-0">No sleep logs found.</p>
@else
<table class="table"><thead><tr><th>Date</th><th>Patient</th><th>Hours</th></tr></thead><tbody>
@foreach($sleepLogs as $log)<tr><td>{{ $log->sleep_date ?? '-' }}</td><td>{{ optional(optional($log->patient)->user)->name ?? '-' }}</td><td>{{ $log->hours_slept ?? '-' }}</td></tr>@endforeach
</tbody></table>{{ $sleepLogs->links() }}
@endif
</div></div>
@endsection
