@extends('client.layout')

@section('title', 'Session Details - Your Mind Aid')

@section('content')
<div class="mb-4">
    <a href="{{ route('client.dashboard') }}" class="btn btn-outline-secondary btn-sm mb-3">
        <i class="bi bi-arrow-left me-1"></i>
        Back to Dashboard
    </a>
    <h4 class="fw-bold mb-1">{{ $session->title ?? 'Session Details' }}</h4>
    <p class="text-muted mb-0 small">View session information and progress</p>
</div>

<div class="card mb-3">
    <div class="card-header bg-white border-0">
        <div class="d-flex align-items-center justify-content-between">
            <h6 class="mb-0 fw-semibold">Session Information</h6>
            <span class="badge bg-{{ $session->status === 'active' ? 'success' : 'secondary' }}">
                {{ ucfirst($session->status) }}
            </span>
        </div>
    </div>
    <div class="card-body">
        <div class="mb-3">
            <p class="small text-muted mb-1">Created Date</p>
            <p class="fw-semibold mb-0">
                {{ $session->created_at->format('M d, Y') }}
            </p>
        </div>
        
        @if($session->doctor)
        <div class="mb-3">
            <p class="small text-muted mb-1">Doctor</p>
            <p class="fw-semibold mb-0">
                Dr. {{ $session->doctor->name ?? $session->doctor->email }}
            </p>
        </div>
        @endif

        @if($session->notes)
        <div class="mb-3">
            <p class="small text-muted mb-2">Notes</p>
            <p class="mb-0">{{ $session->notes }}</p>
        </div>
        @endif

        @if($session->days && $session->days->isNotEmpty())
        <div class="mt-3">
            <h6 class="fw-semibold mb-3">Session Days</h6>
            <div class="list-group list-group-flush">
                @foreach($session->days as $day)
                    <div class="list-group-item border-bottom px-0 py-2">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <p class="fw-semibold mb-1">
                                    Day {{ $day->day_number ?? $loop->iteration }}
                                </p>
                                <p class="small text-muted mb-0">
                                    {{ $day->day_date ? $day->day_date->format('M d, Y') : 'Date not set' }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
