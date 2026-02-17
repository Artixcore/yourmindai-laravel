@extends('layouts.app')

@section('title', 'Add Supervisor Link')

@section('content')
<div class="mb-4">
    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('admin.dashboard')],
        ['label' => 'Supervisor Links', 'url' => route('admin.supervisor-links.index')],
        ['label' => 'Create']
    ]" />
    <h1 class="h3 mb-1 fw-semibold">Add Supervisor Link</h1>
    <p class="text-muted mb-0 small">Link a supervision account to a client. Supervisors can verify tasks and add remarks.</p>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <form method="POST" action="{{ route('admin.supervisor-links.store') }}">
            @csrf
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="supervisor_id" class="form-label">Supervisor</label>
                    <select name="supervisor_id" id="supervisor_id" class="form-select @error('supervisor_id') is-invalid @enderror" required>
                        <option value="">Select Supervisor</option>
                        @foreach($supervisors as $s)
                        <option value="{{ $s->id }}" {{ old('supervisor_id') == $s->id ? 'selected' : '' }}>{{ $s->name ?? $s->email }}</option>
                        @endforeach
                    </select>
                    @error('supervisor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    @if($supervisors->isEmpty())
                    <small class="text-muted">No users with supervision role. Create users and set role to "supervision" in Staff Management.</small>
                    @endif
                </div>
                <div class="col-md-6">
                    <label for="patient_id" class="form-label">Client</label>
                    <select name="patient_id" id="patient_id" class="form-select @error('patient_id') is-invalid @enderror" required>
                        <option value="">Select Client</option>
                        @foreach($patients as $pt)
                        <option value="{{ $pt->id }}" {{ old('patient_id') == $pt->id ? 'selected' : '' }}>{{ optional($pt->user)->name ?? $pt->full_name ?? 'Patient' }}</option>
                        @endforeach
                    </select>
                    @error('patient_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Create Link</button>
                <a href="{{ route('admin.supervisor-links.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
