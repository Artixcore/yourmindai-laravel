@extends('parent.layout')

@section('title', 'My Permissions')

@section('content')
<div class="mb-4">
    <a href="{{ route('parent.dashboard') }}" class="btn btn-link text-decoration-none p-0 mb-2">
        <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
    </a>
    <h4 class="fw-bold mb-1">Data Access Permissions</h4>
    <p class="text-muted mb-0 small">Your access settings for each linked child</p>
</div>

@if($permissions->isEmpty())
<div class="card">
    <div class="card-body text-center py-5">
        <i class="bi bi-shield-check text-muted" style="font-size: 3rem;"></i>
        <p class="text-muted mt-3 mb-0">No permissions configured yet.</p>
        <p class="text-muted small">Contact your healthcare provider to set up access.</p>
    </div>
</div>
@else
<div class="row g-3">
    @foreach($permissions as $perm)
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-semibold mb-2">{{ optional($perm->patient->user)->name ?? optional($perm->patient)->full_name ?? 'Child' }}</h6>
                <div class="d-flex flex-wrap gap-2 small">
                    @if($perm->can_view_medical_records ?? false)
                    <span class="badge bg-success">Medical Records</span>
                    @endif
                    @if($perm->can_view_session_notes ?? false)
                    <span class="badge bg-info">Session Notes</span>
                    @endif
                    @if($perm->can_provide_feedback ?? false)
                    <span class="badge bg-primary">Feedback</span>
                    @endif
                    @if($perm->can_view_progress ?? false)
                    <span class="badge bg-warning">Progress</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection
