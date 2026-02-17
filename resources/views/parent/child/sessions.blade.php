@extends('parent.layout')

@section('title', 'Sessions - ' . (optional($patient->user)->name ?? optional($patient)->full_name ?? 'Child'))

@section('content')
<div class="mb-4">
    <a href="{{ route('parent.child.show', $patient) }}" class="btn btn-link text-decoration-none p-0 mb-2">
        <i class="bi bi-arrow-left me-1"></i>Back to Child
    </a>
    <h4 class="fw-bold mb-1">Sessions for {{ optional($patient->user)->name ?? optional($patient)->full_name ?? 'Child' }}</h4>
    <p class="text-muted mb-0 small">Therapy session history</p>
</div>

@if($sessions->isEmpty())
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-calendar-check text-muted" style="font-size: 3rem;"></i>
        <p class="text-muted mt-3 mb-0">No sessions recorded yet.</p>
    </div>
</div>
@else
<div class="row g-3">
    @foreach($sessions as $session)
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-semibold mb-1">{{ $session->title ?? 'Session #' . $session->id }}</h6>
                <div class="d-flex flex-wrap gap-2 small text-muted">
                    <span><i class="bi bi-calendar3 me-1"></i>{{ $session->created_at->format('M d, Y') }}</span>
                    @if($session->doctor)
                    <span><i class="bi bi-person me-1"></i>{{ optional($session->doctor)->name ?? 'Doctor' }}</span>
                    @endif
                    @if($session->status)
                    <span class="badge bg-{{ $session->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($session->status) }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection
