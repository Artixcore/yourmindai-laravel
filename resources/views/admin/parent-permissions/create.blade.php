@extends('layouts.app')

@section('title', 'Add Parent Permission')

@section('content')
<div class="mb-4">
    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('admin.dashboard')],
        ['label' => 'Parent Permissions', 'url' => route('admin.parent-permissions.index')],
        ['label' => 'Create']
    ]" />
    <h1 class="h3 mb-1 fw-semibold">Add Parent Permission</h1>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.parent-permissions.store') }}">
            @csrf
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="parent_id" class="form-label">Parent</label>
                    <select name="parent_id" id="parent_id" class="form-select" required>
                        <option value="">Select Parent</option>
                        @foreach($parents as $p)
                        <option value="{{ $p->id }}" {{ old('parent_id') == $p->id ? 'selected' : '' }}>{{ $p->name ?? $p->email }}</option>
                        @endforeach
                    </select>
                    @error('parent_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label for="patient_id" class="form-label">Patient</label>
                    <select name="patient_id" id="patient_id" class="form-select" required>
                        <option value="">Select Patient</option>
                        @foreach($patients as $pt)
                        <option value="{{ $pt->id }}" {{ old('patient_id') == $pt->id ? 'selected' : '' }}>{{ optional($pt->user)->name ?? $pt->full_name ?? 'Patient' }}</option>
                        @endforeach
                    </select>
                    @error('patient_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Permissions</label>
                <div class="form-check">
                    <input type="checkbox" name="can_view_medical_records" id="can_view_medical_records" class="form-check-input" {{ old('can_view_medical_records') ? 'checked' : '' }}>
                    <label for="can_view_medical_records" class="form-check-label">View Medical Records</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="can_view_session_notes" id="can_view_session_notes" class="form-check-input" {{ old('can_view_session_notes') ? 'checked' : '' }}>
                    <label for="can_view_session_notes" class="form-check-label">View Session Notes</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="can_provide_feedback" id="can_provide_feedback" class="form-check-input" {{ old('can_provide_feedback') ? 'checked' : '' }}>
                    <label for="can_provide_feedback" class="form-check-label">Provide Feedback</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="can_view_progress" id="can_view_progress" class="form-check-input" {{ old('can_view_progress') ? 'checked' : '' }}>
                    <label for="can_view_progress" class="form-check-label">View Progress</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="can_view_assessments" id="can_view_assessments" class="form-check-input" {{ old('can_view_assessments') ? 'checked' : '' }}>
                    <label for="can_view_assessments" class="form-check-label">View Assessments</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="can_communicate_with_doctor" id="can_communicate_with_doctor" class="form-check-input" {{ old('can_communicate_with_doctor') ? 'checked' : '' }}>
                    <label for="can_communicate_with_doctor" class="form-check-label">Communicate with Doctor</label>
                </div>
            </div>
            <div class="mb-3">
                <label for="notes" class="form-label">Notes</label>
                <textarea name="notes" id="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
            </div>
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">Create</button>
                <a href="{{ route('admin.parent-permissions.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
