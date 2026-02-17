@extends('layouts.app')

@section('title', 'Task: ' . $task->title)

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.tasks.index') }}" class="btn btn-link">Back to Tasks</a>
    <h1 class="h3 mt-2">{{ $task->title }}</h1>
</div>
<div class="card">
    <div class="card-body">
        <p>Patient: {{ optional($task->patient->user)->name ?? optional($task->patient)->full_name ?? '—' }}</p>
        <p>Status: {{ ucfirst($task->status) }}</p>
        @if($task->description)<p>{{ $task->description }}</p>@endif
    </div>
</div>
@endsection
