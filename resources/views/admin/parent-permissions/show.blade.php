@extends('layouts.app')

@section('title', 'Parent Permission Details')

@section('content')
<div class="mb-4">
    <x-breadcrumb :items="[['label' => 'Home', 'url' => route('admin.dashboard')], ['label' => 'Parent Permissions', 'url' => route('admin.parent-permissions.index')], ['label' => 'Details']]" />
    <h1 class="h3 mb-1 fw-semibold">Permission Details</h1>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="text-muted small">Parent</label>
                <p class="mb-0 fw-semibold">{{ optional($permission->parent)->name ?? optional($permission->parent)->email ?? 'N/A' }}</p>
            </div>
            <div class="col-md-6">
                <label class="text-muted small">Patient</label>
                <p class="mb-0 fw-semibold">{{ optional($permission->patient->user)->name ?? optional($permission->patient)->full_name ?? 'N/A' }}</p>
            </div>
        </div>
        <div class="row mb-3">
            <div class="col-12">
                <label class="text-muted small">Permissions</label>
                <div class="d-flex flex-wrap gap-2">
                    @if($permission->can_view_medical_records)<span class="badge bg-success">Medical Records</span>@endif
                    @if($permission->can_view_session_notes)<span class="badge bg-info">Session Notes</span>@endif
                    @if($permission->can_provide_feedback)<span class="badge bg-primary">Provide Feedback</span>@endif
                    @if($permission->can_view_progress)<span class="badge bg-warning">View Progress</span>@endif
                    @if($permission->can_view_assessments)<span class="badge bg-secondary">View Assessments</span>@endif
                    @if($permission->can_communicate_with_doctor)<span class="badge bg-dark">Communicate</span>@endif
                </div>
            </div>
        </div>
        @if($permission->notes)
        <div class="mb-3">
            <label class="text-muted small">Notes</label>
            <p class="mb-0">{{ $permission->notes }}</p>
        </div>
        @endif
        <div class="d-flex gap-2">
            <a href="{{ route('admin.parent-permissions.edit', $permission) }}" class="btn btn-primary">Edit</a>
            <a href="{{ route('admin.parent-permissions.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>
</div>
@endsection
