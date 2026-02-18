@extends('layouts.app')

@section('title', 'Create Task')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('tasks.index') }}" class="text-decoration-none text-primary">Back to Tasks</a>
        <h1 class="h2 fw-bold mt-2">Create Task</h1>
    </div>

    <form action="{{ route('tasks.store') }}" method="POST">
        @csrf
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="mb-3">
                    <label for="patient_id" class="form-label">Patient</label>
                    <select name="patient_id" id="patient_id" class="form-select" required>
                        <option value="">Select patient</option>
                        @foreach($patients as $p)
                        <option value="{{ $p->id }}" {{ old('patient_id') == $p->id ? 'selected' : '' }}>{{ $p->full_name ?? $p->user->name ?? $p->id }}</option>
                        @endforeach
                    </select>
                    @error('patient_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" required>
                    @error('title')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                </div>
                <div class="mb-3">
                    <label for="due_date" class="form-label">Due Date</label>
                    <input type="date" name="due_date" id="due_date" class="form-control" value="{{ old('due_date') }}" required>
                    @error('due_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="points" class="form-label">Points (+ or -)</label>
                    <input type="number" name="points" id="points" class="form-control" value="{{ old('points', 0) }}" min="-100" max="100" placeholder="0">
                    <small class="text-muted">Positive for reward, negative for penalty. Affects patient's total points.</small>
                </div>
                <div class="form-check mb-2">
                    <input type="checkbox" name="visible_to_patient" id="visible_to_patient" class="form-check-input" {{ old('visible_to_patient', true) ? 'checked' : '' }}>
                    <label for="visible_to_patient" class="form-check-label">Visible to patient</label>
                </div>
                <div class="form-check">
                    <input type="checkbox" name="visible_to_parent" id="visible_to_parent" class="form-check-input" {{ old('visible_to_parent') ? 'checked' : '' }}>
                    <label for="visible_to_parent" class="form-check-label">Visible to parent</label>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Create Task</button>
        <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">Cancel</a>
    </form>
</div>
@endsection
