@extends('layouts.app')
@section('title', 'Mood Logs')
@section('content')
<div class="mb-4"><h1 class="h3 fw-semibold">Mood Logs</h1></div>
<div class="card"><div class="card-body">
@if($moodLogs->isEmpty())<p class="mb-0">No mood logs found.</p>
@else
<table class="table"><thead><tr><th>Date</th><th>Patient</th><th>Rating</th></tr></thead><tbody>
@foreach($moodLogs as $log)<tr><td>{{ $log->log_date ?? '-' }}</td><td>{{ optional(optional($log->patient)->user)->name ?? '-' }}</td><td>{{ $log->mood_rating ?? '-' }}</td></tr>@endforeach
</tbody></table>{{ $moodLogs->links() }}
@endif
</div></div>
@endsection
