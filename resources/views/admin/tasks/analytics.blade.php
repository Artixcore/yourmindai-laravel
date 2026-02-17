@extends('layouts.app')

@section('title', 'Task Analytics')

@section('content')
<div class="mb-4">
    <h1 class="h3 mb-1 fw-semibold">Task Analytics</h1>
    <p class="text-muted mb-0">Completion rates and trends</p>
</div>
<div class="card">
    <div class="card-body">
        <p>Completion Rate: {{ $completionRate ?? 0 }}%</p>
        <p>Total Tasks: {{ $totalTasks ?? 0 }}</p>
        <p>Completed: {{ $completedTasks ?? 0 }}</p>
    </div>
</div>
@endsection
