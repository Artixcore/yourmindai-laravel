@extends('layouts.app')

@section('title', 'Manage Reviews')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h2 fw-bold">Review Management</h1>
        <p class="text-muted">Moderate and manage all patient reviews</p>
    </div>

    <!-- Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted small">Total Reviews</h6>
                    <h3 class="mb-0">{{ $stats['total_reviews'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted small">Published</h6>
                    <h3 class="mb-0 text-success">{{ $stats['published_reviews'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted small">Flagged</h6>
                    <h3 class="mb-0 text-danger">{{ $stats['flagged_reviews'] }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted small">Average Rating</h6>
                    <h3 class="mb-0">{{ number_format($stats['average_rating'], 1) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <select name="doctor_id" class="form-select">
                        <option value="">All Doctors</option>
                        @foreach ($doctors as $doctor)
                            <option value="{{ $doctor->id }}" {{ request('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                Dr. {{ $doctor->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="doctor" {{ request('type') === 'doctor' ? 'selected' : '' }}>Doctor</option>
                        <option value="session" {{ request('type') === 'session' ? 'selected' : '' }}>Session</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Published</option>
                        <option value="flagged" {{ request('status') === 'flagged' ? 'selected' : '' }}>Flagged</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="rating" class="form-select">
                        <option value="">All Ratings</option>
                        <option value="5" {{ request('rating') == 5 ? 'selected' : '' }}>5 Stars</option>
                        <option value="4" {{ request('rating') == 4 ? 'selected' : '' }}>4+ Stars</option>
                        <option value="3" {{ request('rating') == 3 ? 'selected' : '' }}>3+ Stars</option>
                        <option value="2" {{ request('rating') == 2 ? 'selected' : '' }}>2+ Stars</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Filter</button>
                    <a href="{{ route('admin.reviews.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Reviews List -->
    @if ($reviews->count() > 0)
        <div class="row g-3">
            @foreach ($reviews as $review)
                <div class="col-12">
                    <x-review-card :review="$review" :showPatient="true" :showDoctor="true" :showSession="true">
                        <div class="mt-3 pt-3 border-top d-flex gap-2">
                            <a href="{{ route('admin.reviews.show', $review) }}" class="btn btn-sm btn-outline-primary">
                                View Details
                            </a>
                            @if ($review->status !== 'flagged')
                                <form method="POST" action="{{ route('admin.reviews.moderate', $review) }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="status" value="flagged">
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Flag</button>
                                </form>
                            @else
                                <form method="POST" action="{{ route('admin.reviews.moderate', $review) }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="status" value="published">
                                    <button type="submit" class="btn btn-sm btn-outline-success">Publish</button>
                                </form>
                            @endif
                        </div>
                    </x-review-card>
                </div>
            @endforeach
        </div>
        <div class="mt-4">
            {{ $reviews->links() }}
        </div>
    @else
        <div class="card">
            <div class="card-body text-center py-5">
                <p class="text-muted mb-0">No reviews found</p>
            </div>
        </div>
    @endif
</div>
@endsection
