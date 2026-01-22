@extends('client.layout')

@section('title', 'My Sessions - Your Mind Aid')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Therapy Sessions</h4>
    <p class="text-muted mb-0 small">View all your therapy sessions</p>
</div>

@if($sessions->isEmpty())
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-calendar-x display-1 text-muted mb-3"></i>
        <h6 class="mb-2">No therapy sessions yet</h6>
        <p class="text-muted small mb-0">Your doctor will create sessions for you.</p>
    </div>
</div>
@else
@foreach($sessions as $session)
<div class="card mb-3">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-start">
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-1">{{ $session->title ?? 'Session #' . $session->id }}</h6>
                @if($session->doctor)
                <p class="small text-muted mb-2">
                    <i class="bi bi-person me-1"></i>
                    Dr. {{ $session->doctor->name ?? $session->doctor->email }}
                </p>
                @endif
                <div class="small text-muted">
                    <i class="bi bi-calendar3 me-1"></i>
                    {{ $session->created_at->format('M d, Y') }}
                </div>
                @if($session->days && $session->days->count() > 0)
                <div class="small text-muted mt-1">
                    <i class="bi bi-list-ul me-1"></i>
                    {{ $session->days->count() }} day(s)
                </div>
                @endif
            </div>
            <a href="{{ route('client.sessions.show', $session->id) }}" class="btn btn-sm btn-outline-primary">
                View
            </a>
        </div>
    </div>
</div>
@endforeach
@endif
@endsection
