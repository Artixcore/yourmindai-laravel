@extends('layouts.app')
@section('title', 'Exercise Logs')
@section('content')
<div class="mb-4"><h1 class="h3 fw-semibold">Exercise Logs</h1></div>
<div class="card"><div class="card-body">
@if($exerciseLogs->isEmpty())<p class="mb-0">No exercise logs found.</p>
@else
<table class="table"><thead><tr><th>Date</th><th>Patient</th><th>Duration</th></tr></thead><tbody>
@foreach($exerciseLogs as $log)<tr><td>{{ $log->exercise_date ?? '-' }}</td><td>{{ optional(optional($log->patient)->user)->name ?? '-' }}</td><td>{{ $log->duration_minutes ?? '-' }} min</td></tr>@endforeach
</tbody></table>{{ $exerciseLogs->links() }}
@endif
</div></div>
@endsection
