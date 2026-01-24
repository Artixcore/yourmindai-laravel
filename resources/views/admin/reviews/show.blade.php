@extends('layouts.app')

@section('title', 'Review Details')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('admin.reviews.index') }}" class="text-decoration-none">← Back to Reviews</a>
        <h1 class="h2 fw-bold mt-2">Review Details</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <x-review-card :review="$review" :showPatient="true" :showDoctor="true" :showSession="true" />
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Moderation</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.reviews.moderate', $review) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="published" {{ $review->status === 'published' ? 'selected' : '' }}>Published</option>
                                <option value="flagged" {{ $review->status === 'flagged' ? 'selected' : '' }}>Flagged</option>
                                <option value="pending" {{ $review->status === 'pending' ? 'selected' : '' }}>Pending</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Reason (optional)</label>
                            <textarea name="reason" class="form-control" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Update Status</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
