@extends('layouts.app')

@section('title', 'Task Management')

@section('content')
<div class="mb-4">
    <h1 class="h3 fw-semibold">Task Management</h1>
</div>
<div class="card">
    <div class="card-body">
        @if($tasks->isEmpty())
        <x-empty-state message="No tasks found." icon="bi-list-check" />
        @else
        <table class="table">
            <thead><tr><th>Task</th><th>Patient</th><th>Status</th></tr></thead>
            <tbody>
                @foreach($tasks as $t)
                <tr>
                    <td>{{ $t->title }}</td>
                    <td>{{ optional(optional($t->patient)->user)->name ?? '—' }}</td>
                    <td>{{ ucfirst($t->status) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        {{ $tasks->links() }}
        @endif
    </div>
</div>
@endsection
