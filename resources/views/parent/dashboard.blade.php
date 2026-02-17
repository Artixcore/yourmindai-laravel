@extends('parent.layout')

@section('title', 'Parent Dashboard - Your Mind Aid')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Welcome, {{ auth()->user()->name }}!</h4>
    <p class="text-muted mb-0 small">Monitor and support your children's mental health journey</p>
</div>

<!-- Quick Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Total Children</h6>
                    <h2 class="mb-0 fw-bold">{{ $stats['total_children'] }}</h2>
                </div>
                <div class="text-primary" style="font-size: 2rem;">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Pending Feedback</h6>
                    <h2 class="mb-0 fw-bold text-warning">{{ $stats['pending_feedback'] }}</h2>
                </div>
                <div class="text-warning" style="font-size: 2rem;">
                    <i class="bi bi-chat-left-dots"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="text-muted mb-1">Recent Progress Updates</h6>
                    <h2 class="mb-0 fw-bold text-success">{{ $stats['recent_progressions'] }}</h2>
                </div>
                <div class="text-success" style="font-size: 2rem;">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Children List -->
<div class="card">
    <div class="card-header bg-white border-0">
        <h5 class="mb-0 fw-semibold">
            <i class="bi bi-people me-2"></i>
            Your Children
        </h5>
    </div>
    <div class="card-body">
        @if($children->isNotEmpty())
            <div class="row g-3">
                @foreach($children as $child)
                <div class="col-md-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-start mb-3">
                                <div class="flex-shrink-0 me-3">
                                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 60px; height: 60px; font-size: 1.5rem;">
                                        <i class="bi bi-person"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-1">{{ $child->user->name ?? 'Patient' }}</h5>
                                    <p class="text-muted small mb-0">
                                        <i class="bi bi-person-badge me-1"></i>
                                        Under care of: {{ $child->doctor->name ?? 'Not assigned' }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <a href="{{ route('parent.child.tasks', $child->id) }}" class="btn btn-primary" style="min-height: 44px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-check2-square me-2"></i>Verify Tasks
                                </a>
                                <a href="{{ route('parent.child.show', $child->id) }}" class="btn btn-outline-primary" style="min-height: 44px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-eye me-2"></i>View Progress
                                </a>
                                <a href="{{ route('parent.child.homework', $child->id) }}" class="btn btn-outline-primary" style="min-height: 44px; display: flex; align-items: center; justify-content: center;">
                                    <i class="bi bi-list-check me-2"></i>View Homework
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-3 mb-0">No children linked to your account yet.</p>
                <p class="text-muted small">Contact your healthcare provider to link your child's account.</p>
            </div>
        @endif
    </div>
</div>

<!-- Info Card -->
<div class="card mt-4">
    <div class="card-body">
        <h6 class="fw-semibold mb-3">
            <i class="bi bi-info-circle me-2"></i>About Parent Portal
        </h6>
        <p class="small text-muted mb-2">
            As a parent, you can:
        </p>
        <ul class="small text-muted">
            <li>Monitor your child's therapy progress</li>
            <li>Provide feedback on homework and techniques</li>
            <li>Track practice progression over time</li>
            <li>View session reports and therapist notes</li>
            <li>Control data visibility and permissions</li>
        </ul>
    </div>
</div>
@endsection
