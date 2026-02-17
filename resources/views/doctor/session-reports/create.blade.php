@extends('layouts.app')

@section('title', 'Create Session Report')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('session-reports.index') }}" class="text-decoration-none text-primary mb-3 d-inline-flex align-items-center">
            <i class="bi bi-arrow-left me-1"></i> Back to Reports
        </a>
        <h1 class="h2 fw-bold text-stone-900 mb-0">Create Session Report</h1>
    </div>

    <form action="{{ route('session-reports.store') }}" method="POST">
        @csrf
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="patient_id" class="form-label">Patient</label>
                        <select name="patient_id" id="patient_id" class="form-select" required>
                            <option value="">Select patient</option>
                            @foreach($patients as $p)
                            <option value="{{ $p->id }}" {{ old('patient_id') == $p->id ? 'selected' : '' }}>{{ $p->full_name ?? optional($p->user)->name ?? $p->id }}</option>
                            @endforeach
                        </select>
                        @error('patient_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="session_id" class="form-label">Session (optional)</label>
                        <select name="session_id" id="session_id" class="form-select">
                            <option value="">None</option>
                            @if(isset($sessions) && $sessions)
                            @foreach($sessions as $s)
                            <option value="{{ $s->id }}" {{ old('session_id') == $s->id ? 'selected' : '' }}>{{ $s->title ?? 'Session #' . $s->id }}</option>
                            @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-12">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
                        @error('title')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label for="summary" class="form-label">Summary</label>
                        <textarea name="summary" id="summary" class="form-control" rows="4" required>{{ old('summary') }}</textarea>
                        @error('summary')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label for="assessments_summary" class="form-label">Assessments Summary</label>
                        <textarea name="assessments_summary" id="assessments_summary" class="form-control" rows="2">{{ old('assessments_summary') }}</textarea>
                    </div>
                    <div class="col-12">
                        <label for="techniques_assigned" class="form-label">Techniques Assigned</label>
                        <textarea name="techniques_assigned" id="techniques_assigned" class="form-control" rows="2">{{ old('techniques_assigned') }}</textarea>
                    </div>
                    <div class="col-12">
                        <label for="progress_notes" class="form-label">Progress Notes</label>
                        <textarea name="progress_notes" id="progress_notes" class="form-control" rows="2">{{ old('progress_notes') }}</textarea>
                    </div>
                    <div class="col-12">
                        <label for="next_steps" class="form-label">Next Steps</label>
                        <textarea name="next_steps" id="next_steps" class="form-control" rows="2">{{ old('next_steps') }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select" required>
                            <option value="draft" {{ old('status', 'draft') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="reviewed" {{ old('status') == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" name="shared_with_patient" id="shared_with_patient" class="form-check-input" {{ old('shared_with_patient') ? 'checked' : '' }}>
                            <label for="shared_with_patient" class="form-check-label">Share with patient</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="shared_with_parents" id="shared_with_parents" class="form-check-input" {{ old('shared_with_parents') ? 'checked' : '' }}>
                            <label for="shared_with_parents" class="form-check-label">Share with parents</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="shared_with_others" id="shared_with_others" class="form-check-input" {{ old('shared_with_others') ? 'checked' : '' }}>
                            <label for="shared_with_others" class="form-check-label">Share with others</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Create Report</button>
            <a href="{{ route('session-reports.index') }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
