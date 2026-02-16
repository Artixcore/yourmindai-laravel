@extends('layouts.app')
@section('title', 'Patient Goals - Your Mind Aid')
@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Patients</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('patients.show', $patient) }}">{{ $patientProfile->user->name ?? $patientProfile->full_name ?? $patient->name ?? 'Patient' }}</a></li>
                    <li class="breadcrumb-item active">Goals</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">Goals</h2>
                    <p class="text-muted mb-0">Set and track goals for {{ $patientProfile->user->name ?? $patientProfile->full_name ?? $patient->name ?? 'Patient' }}</p>
                </div>
                <a href="{{ route('patients.goals.create', $patient) }}" class="btn btn-primary"><i class="bi bi-plus-lg me-2"></i>New Goal</a>
            </div>
        </div>
    </div>
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if($goals->isEmpty())
        <div class="card shadow-sm"><div class="card-body text-center py-5"><i class="bi bi-bullseye text-muted" style="font-size: 3rem;"></i><p class="text-muted mt-3 mb-0">No goals set yet.</p><a href="{{ route('patients.goals.create', $patient) }}" class="btn btn-primary mt-3">Create first goal</a></div></div>
    @else
        <div class="card shadow-sm"><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover mb-0"><thead class="table-light"><tr><th>Title</th><th>Start / End</th><th>Frequency</th><th>Duration</th><th>Status</th><th>Visibility</th><th></th></tr></thead><tbody>
            @foreach($goals as $goal)
            <tr>
                <td><strong>{{ $goal->title }}</strong>@if($goal->description)<br><small class="text-muted">{{ Str::limit($goal->description, 50) }}</small>@endif</td>
                <td>@if($goal->start_date){{ $goal->start_date->format('M d, Y') }}@else—@endif @if($goal->end_date)<br>to {{ $goal->end_date->format('M d, Y') }}@endif</td>
                <td>{{ $goal->frequency_per_day ? $goal->frequency_per_day . 'x/day' : '—' }}</td>
                <td>{{ $goal->duration_minutes ? $goal->duration_minutes . ' min' : '—' }}</td>
                <td><span class="badge bg-secondary">{{ $goal->status ?? '—' }}</span></td>
                <td>@if($goal->visible_to_patient)<span class="badge bg-info">Patient</span>@endif @if($goal->visible_to_parent)<span class="badge bg-success">Parent</span>@endif</td>
                <td><a href="{{ route('patients.goals.edit', [$patient, $goal]) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                    <form action="{{ route('patients.goals.destroy', [$patient, $goal]) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this goal?');">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline-danger">Delete</button></form></td>
            </tr>
            @endforeach
        </tbody></table></div></div></div>
    @endif
</div>
@endsection
