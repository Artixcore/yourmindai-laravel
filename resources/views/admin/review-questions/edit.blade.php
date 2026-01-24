@extends('layouts.app')

@section('title', 'Edit Review Question')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('admin.review-questions.index') }}" class="text-decoration-none">← Back</a>
        <h1 class="h2 fw-bold mt-2">Edit Review Question</h1>
    </div>

    <form method="POST" action="{{ route('admin.review-questions.update', $reviewQuestion) }}">
        @csrf
        @method('PUT')
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Question Text *</label>
                    <textarea name="question_text" class="form-control" rows="2" required>{{ old('question_text', $reviewQuestion->question_text) }}</textarea>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Question Type *</label>
                        <select name="question_type" class="form-select" required>
                            <option value="star_rating" {{ $reviewQuestion->question_type === 'star_rating' ? 'selected' : '' }}>Star Rating</option>
                            <option value="yes_no" {{ $reviewQuestion->question_type === 'yes_no' ? 'selected' : '' }}>Yes/No</option>
                            <option value="multiple_choice" {{ $reviewQuestion->question_type === 'multiple_choice' ? 'selected' : '' }}>Multiple Choice</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Applies To *</label>
                        <select name="applies_to" class="form-select" required>
                            <option value="both" {{ $reviewQuestion->applies_to === 'both' ? 'selected' : '' }}>Both</option>
                            <option value="doctor" {{ $reviewQuestion->applies_to === 'doctor' ? 'selected' : '' }}>Doctor Reviews</option>
                            <option value="session" {{ $reviewQuestion->applies_to === 'session' ? 'selected' : '' }}>Session Reviews</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" {{ $reviewQuestion->is_active ? 'checked' : '' }}>
                            <label class="form-check-label">Active</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_required" value="1" {{ $reviewQuestion->is_required ? 'checked' : '' }}>
                            <label class="form-check-label">Required</label>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Condition Field (optional)</label>
                        <input type="text" name="condition_field" class="form-control" value="{{ old('condition_field', $reviewQuestion->condition_field) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Condition Value (optional)</label>
                        <input type="text" name="condition_value" class="form-control" value="{{ old('condition_value', $reviewQuestion->condition_value) }}">
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Update Question</button>
                    <a href="{{ route('admin.review-questions.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
