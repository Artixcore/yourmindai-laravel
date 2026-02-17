@extends('supervision.layout')

@section('title', 'Supervision Dashboard')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Welcome, {{ auth()->user()->name }}!</h4>
    <p class="text-muted mb-0 small">Verify tasks and add remarks for linked clients</p>
</div>

<div class="card">
    <div class="card-header bg-white border-0"><h5 class="mb-0 fw-semibold"><i class="bi bi-people me-2"></i>Linked Clients</h5></div>
    <div class="card-body">
        @if($children->isNotEmpty())
            <div class="row g-3">
                @foreach($children as $child)
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h5 class="mb-2">{{ optional($child->user)->name ?? optional($child)->full_name ?? 'Client' }}</h5>
                            <p class="text-muted small mb-3">{{ optional($child->doctor)->name ?? 'Not assigned' }}</p>
                            <a href="{{ route('supervision.child.tasks', $child->id) }}" class="btn btn-primary w-100" style="min-height: 44px;">
                                <i class="bi bi-check2-square me-2"></i>Verify Tasks
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-3 mb-0">No clients linked to your account yet.</p>
                <p class="text-muted small">Contact your healthcare provider.</p>
            </div>
        @endif
    </div>
</div>
@endsection
