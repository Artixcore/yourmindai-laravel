@extends('parent.layout')

@section('title', 'Homework - ' . (optional($patient->user)->name ?? optional($patient)->full_name ?? 'Child'))

@section('content')
<div class="mb-4">
    <a href="{{ route('parent.child.show', $patient) }}" class="btn btn-link text-decoration-none p-0 mb-2">
        <i class="bi bi-arrow-left me-1"></i>Back to Child
    </a>
    <h4 class="fw-bold mb-1">Homework for {{ optional($patient->user)->name ?? optional($patient)->full_name ?? 'Child' }}</h4>
    <p class="text-muted mb-0 small">View therapy assignments</p>
</div>

@if($homework->isEmpty())
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-list-check text-muted" style="font-size: 3rem;"></i>
        <p class="text-muted mt-3 mb-0">No homework assigned yet.</p>
    </div>
</div>
@else
<div class="row g-3">
    @foreach($homework as $hw)
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-semibold mb-2">{{ $hw->title }}</h6>
                @if($hw->description)
                <p class="text-muted small mb-2">{{ \Illuminate\Support\Str::limit($hw->description, 200) }}</p>
                @endif
                <div class="d-flex flex-wrap gap-2 small">
                    <span class="badge bg-{{ $hw->status === 'completed' ? 'success' : ($hw->status === 'in_progress' ? 'primary' : 'warning') }}">
                        {{ ucfirst(str_replace('_', ' ', $hw->status)) }}
                    </span>
                    @if($hw->start_date)
                    <span class="text-muted"><i class="bi bi-calendar3 me-1"></i>{{ $hw->start_date->format('M d, Y') }}</span>
                    @endif
                    @if($hw->assignedByDoctor)
                    <span class="text-muted">Assigned by {{ optional($hw->assignedByDoctor)->name ?? 'Doctor' }}</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection
