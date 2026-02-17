@extends('layouts.app')

@section('title', 'Edit Task: ' . $task->title)

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('tasks.show', $task) }}" class="text-decoration-none text-primary mb-3 d-inline-flex align-items-center">
            <i class="bi bi-arrow-left me-1"></i> Back to Task
        </a>
        <h1 class="h2 fw-bold text-stone-900 mb-0">Edit Task</h1>
    </div>

    <form action="{{ route('tasks.update', $task) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="patient_id" class="form-label">Patient</label>
                        <select name="patient_id" id="patient_id" class="form-select" required>
                            @foreach($patients as $p)
                            <option value="{{ $p->id }}" {{ old('patient_id', $task->patient_id) == $p->id ? 'selected' : '' }}>{{ optional($p->user)->name ?? $p->full_name ?? $p->id }}</option>
                            @endforeach
                        </select>
                        @error('patient_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $task->title) }}" required>
                        @error('title')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="3">{{ old('description', $task->description) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="due_date" class="form-label">Due Date</label>
                        <input type="date" name="due_date" id="due_date" class="form-control" value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}" required>
                        @error('due_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6">
                        <label for="points" class="form-label">Points</label>
                        <input type="number" name="points" id="points" class="form-control" value="{{ old('points', $task->points ?? 0) }}" min="0">
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input type="checkbox" name="visible_to_patient" id="visible_to_patient" class="form-check-input" {{ old('visible_to_patient', $task->visible_to_patient) ? 'checked' : '' }}>
                            <label for="visible_to_patient" class="form-check-label">Visible to patient</label>
                        </div>
                        <div class="form-check">
                            <input type="checkbox" name="visible_to_parent" id="visible_to_parent" class="form-check-input" {{ old('visible_to_parent', $task->visible_to_parent) ? 'checked' : '' }}>
                            <label for="visible_to_parent" class="form-check-label">Visible to parent</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="{{ route('tasks.show', $task) }}" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
