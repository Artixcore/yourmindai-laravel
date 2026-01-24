@extends('layouts.app')

@section('title', 'Create Review Question')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('admin.review-questions.index') }}" class="text-decoration-none">← Back</a>
        <h1 class="h2 fw-bold mt-2">Create Review Question</h1>
    </div>

    <form method="POST" action="{{ route('admin.review-questions.store') }}">
        @csrf
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label">Question Text *</label>
                    <textarea name="question_text" class="form-control" rows="2" required>{{ old('question_text') }}</textarea>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Question Type *</label>
                        <select name="question_type" class="form-select" required>
                            <option value="star_rating">Star Rating</option>
                            <option value="yes_no">Yes/No</option>
                            <option value="multiple_choice">Multiple Choice</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Applies To *</label>
                        <select name="applies_to" class="form-select" required>
                            <option value="both">Both</option>
                            <option value="doctor">Doctor Reviews</option>
                            <option value="session">Session Reviews</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label">Status</label>
                        <div class="form-check mt-2">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" checked>
                            <label class="form-check-label">Active</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_required" value="1" checked>
                            <label class="form-check-label">Required</label>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Condition Field (optional)</label>
                        <input type="text" name="condition_field" class="form-control" placeholder="e.g., session_count">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Condition Value (optional)</label>
                        <input type="text" name="condition_value" class="form-control" placeholder="e.g., >5">
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Create Question</button>
                    <a href="{{ route('admin.review-questions.index') }}" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
