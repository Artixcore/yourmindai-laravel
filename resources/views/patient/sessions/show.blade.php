@extends('layouts.app')

@section('title', 'Session Details - Your Mind Aid')

@section('content')
<div class="container-fluid" style="max-width: 1000px;">
    <div class="mb-4">
        <a href="{{ route('patient.sessions.index') }}" class="btn btn-outline-secondary btn-sm mb-3">
            <i class="bi bi-arrow-left me-1"></i>
            Back to Sessions
        </a>
        <h1 class="h3 fw-bold text-stone-900 mb-2">{{ $session->title ?? 'Session Details' }}</h1>
        <p class="text-stone-600 mb-0">View session information and progress</p>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 pb-0 pt-3 px-4">
            <div class="d-flex align-items-center justify-content-between">
                <h5 class="h6 fw-semibold text-stone-900 mb-0">Session Information</h5>
                <span class="badge bg-{{ $session->status === 'active' ? 'success' : 'secondary' }}">
                    {{ ucfirst($session->status) }}
                </span>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row g-3 mb-3">
                <div class="col-12 col-md-6">
                    <p class="small text-stone-600 mb-1">Created Date</p>
                    <p class="fw-semibold text-stone-900 mb-0">
                        {{ $session->created_at->format('M d, Y') }}
                    </p>
                </div>
                @if($session->doctor)
                <div class="col-12 col-md-6">
                    <p class="small text-stone-600 mb-1">Doctor</p>
                    <p class="fw-semibold text-stone-900 mb-0">
                        Dr. {{ $session->doctor->name ?? $session->doctor->email }}
                    </p>
                </div>
                @endif
            </div>

            @if($session->notes)
            <div class="mb-3">
                <p class="small text-stone-600 mb-2">Notes</p>
                <p class="text-stone-900 mb-0">{{ $session->notes }}</p>
            </div>
            @endif

            @if($session->days && $session->days->isNotEmpty())
            <div class="mt-4">
                <h6 class="fw-semibold text-stone-900 mb-3">Session Days</h6>
                <div class="list-group list-group-flush">
                    @foreach($session->days as $day)
                        <div class="list-group-item border-0 px-0 py-2">
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <p class="fw-semibold text-stone-900 mb-1">
                                        Day {{ $day->day_number ?? $loop->iteration }}
                                    </p>
                                    <p class="small text-stone-600 mb-0">
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
</div>
@endsection
