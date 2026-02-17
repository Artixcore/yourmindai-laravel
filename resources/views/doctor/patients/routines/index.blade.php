@extends('layouts.app')
@section('title', 'Routines')
@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Patients</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('patients.show', $patient) }}">{{ $patientProfile->user->name ?? 'Patient' }}</a></li>
                    <li class="breadcrumb-item active">Routines</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <div><h2 class="mb-1">Routines</h2></div>
                <a href="{{ route('patients.routines.create', $patient) }}" class="btn btn-primary">Create Routine</a>
            </div>
        </div>
    </div>
    @if($routines->isEmpty())
    <div class="card"><div class="card-body text-center py-5">
        <p class="mb-3">No routines yet.</p>
        <a href="{{ route('patients.routines.create', $patient) }}" class="btn btn-primary">Create First Routine</a>
    </div></div>
    @else
    <div class="card"><div class="card-body">
        <table class="table"><thead><tr><th>Title</th><th>Frequency</th><th>Status</th><th>Actions</th></tr></thead><tbody>
        @foreach($routines as $r)
        <tr><td>{{ $r->title ?? '-' }}</td><td>{{ $r->frequency ?? '-' }}</td><td>{{ $r->is_active ? 'Active' : 'Inactive' }}</td><td><a href="{{ route('patients.routines.show', [$patient, $r]) }}">View</a></td></tr>
        @endforeach
        </tbody></table>
    </div></div>
    @endif
</div>
@endsection
