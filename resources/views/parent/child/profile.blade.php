@extends('parent.layout')

@section('title', 'Child Progress')

@section('content')
<div class="mb-4">
    <a href="{{ route('parent.dashboard') }}" class="btn btn-link text-decoration-none p-0 mb-2">
        <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
    </a>
    <h4 class="fw-bold mb-1">{{ optional($patient->user)->name ?? optional($patient)->full_name ?? 'Child' }}</h4>
    <p class="text-muted mb-0 small">View progress and homework</p>
</div>

<div class="row g-3 mb-4">
    <div class="col-12 col-md-4">
        <a href="{{ route('parent.child.tasks', $patient) }}" class="card text-decoration-none text-dark h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-check2-square fs-5"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-semibold">Verify Tasks</h6>
                    <small class="text-muted">Mark tasks as verified</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-12 col-md-4">
        <a href="{{ route('parent.child.homework', $patient) }}" class="card text-decoration-none text-dark h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-list-check fs-5"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-semibold">View Homework</h6>
                    <small class="text-muted">{{ $homeworkAssignments->count() }} assignments</small>
                </div>
            </div>
        </a>
    </div>
    <div class="col-12 col-md-4">
        <a href="{{ route('parent.child.sessions', $patient) }}" class="card text-decoration-none text-dark h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                    <i class="bi bi-calendar-check fs-5"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-semibold">View Sessions</h6>
                    <small class="text-muted">Therapy sessions</small>
                </div>
            </div>
        </a>
    </div>
</div>

@if($homeworkAssignments->isNotEmpty())
<div class="card mb-3">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-list-check me-2"></i>Recent Homework</h5>
    </div>
    <div class="card-body">
        <ul class="list-group list-group-flush">
            @foreach($homeworkAssignments->take(5) as $hw)
            <li class="list-group-item px-0">
                <strong>{{ $hw->title }}</strong>
                <span class="badge bg-{{ $hw->status === 'completed' ? 'success' : 'warning' }} ms-2">{{ ucfirst($hw->status) }}</span>
            </li>
            @endforeach
        </ul>
    </div>
</div>
@endif

@if($recentProgressions->isNotEmpty())
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0"><i class="bi bi-graph-up-arrow me-2"></i>Recent Progress</h5>
    </div>
    <div class="card-body">
        <ul class="list-group list-group-flush">
            @foreach($recentProgressions as $prog)
            <li class="list-group-item px-0">
                {{ $prog->progress_date->format('M d, Y') }} - {{ \Illuminate\Support\Str::limit($prog->notes ?? 'Progress logged', 80) }}
            </li>
            @endforeach
        </ul>
    </div>
</div>
@endif

@if($homeworkAssignments->isEmpty() && $recentProgressions->isEmpty())
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-clipboard-check text-muted" style="font-size: 3rem;"></i>
        <p class="text-muted mt-3 mb-0">No homework or progress data yet.</p>
    </div>
</div>
@endif
@endsection
