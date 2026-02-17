@extends('parent.layout')

@section('title', 'Select Child - Verify Tasks')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Verify Tasks</h4>
    <p class="text-muted mb-0 small">Select a child to view and verify their tasks</p>
</div>

<div class="row g-3">
    @foreach($children as $child)
    <div class="col-12">
        <a href="{{ route('parent.child.tasks', $child->id) }}" class="card text-decoration-none text-dark" style="min-height: 60px; display: flex; align-items: center;">
            <div class="card-body d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 48px; height: 48px;">
                    <i class="bi bi-person"></i>
                </div>
                <div class="flex-grow-1">
                    <h5 class="mb-0">{{ $child->user->name ?? 'Patient' }}</h5>
                    <small class="text-muted">{{ $child->doctor->name ?? 'Not assigned' }}</small>
                </div>
                <i class="bi bi-chevron-right text-muted"></i>
            </div>
        </a>
    </div>
    @endforeach
</div>
@endsection
