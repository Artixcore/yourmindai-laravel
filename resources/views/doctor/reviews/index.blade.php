@extends('layouts.app')

@section('title', 'My Reviews')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="mb-4">
        <h1 class="h2 fw-bold text-stone-900">Patient Reviews</h1>
        <p class="text-stone-600 mt-2">View and analyze feedback from your patients</p>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted small mb-1">Total Reviews</p>
                            <h3 class="mb-0 fw-bold">{{ $stats['total_reviews'] }}</h3>
                        </div>
                        <div class="bg-primary bg-opacity-10 rounded p-3">
                            <svg class="text-primary" style="width: 24px; height: 24px;" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted small mb-1">Average Rating</p>
                            <h3 class="mb-0 fw-bold">{{ number_format($stats['average_rating'], 1) }}</h3>
                        </div>
                        <div class="bg-warning bg-opacity-10 rounded p-3">
                            <svg class="text-warning" style="width: 24px; height: 24px;" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted small mb-1">Doctor Reviews</p>
                            <h3 class="mb-0 fw-bold">{{ $stats['doctor_reviews'] }}</h3>
                        </div>
                        <div class="bg-success bg-opacity-10 rounded p-3">
                            <svg class="text-success" style="width: 24px; height: 24px;" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <p class="text-muted small mb-1">Session Reviews</p>
                            <h3 class="mb-0 fw-bold">{{ $stats['session_reviews'] }}</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 rounded p-3">
                            <svg class="text-info" style="width: 24px; height: 24px;" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 6a2 2 0 012-2h12a2 2 0 012 2v2a2 2 0 01-2 2H4a2 2 0 01-2-2V6zM14 16a2 2 0 002-2v-2a2 2 0 00-2-2h-8a2 2 0 00-2 2v2a2 2 0 002 2h8z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('doctor.reviews.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small">Type</label>
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="doctor" {{ request('type') === 'doctor' ? 'selected' : '' }}>Doctor Reviews</option>
                        <option value="session" {{ request('type') === 'session' ? 'selected' : '' }}>Session Reviews</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label small">Minimum Rating</label>
                    <select name="rating" class="form-select">
                        <option value="">All Ratings</option>
                        <option value="5" {{ request('rating') == 5 ? 'selected' : '' }}>5 Stars</option>
                        <option value="4" {{ request('rating') == 4 ? 'selected' : '' }}>4+ Stars</option>
                        <option value="3" {{ request('rating') == 3 ? 'selected' : '' }}>3+ Stars</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label small">From Date</label>
                    <input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>

                <div class="col-md-2">
                    <label class="form-label small">To Date</label>
                    <input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>

                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">Apply</button>
                    <a href="{{ route('doctor.reviews.index') }}" class="btn btn-outline-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Reviews List -->
    @if ($reviews->count() > 0)
        <div class="row g-4">
            @foreach ($reviews as $review)
                <div class="col-12">
                    <x-review-card :review="$review" :showPatient="true" :showSession="true">
                        <div class="mt-3 pt-3 border-top">
                            <a href="{{ route('doctor.reviews.show', $review) }}" class="btn btn-sm btn-outline-primary">
                                View Details
                            </a>
                        </div>
                    </x-review-card>
                </div>
            @endforeach
        </div>

        <div class="mt-4">
            {{ $reviews->links() }}
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <svg class="text-muted mb-3" style="width: 64px; height: 64px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
                <h3 class="h5 mb-2">No reviews found</h3>
                <p class="text-muted">{{ request()->hasAny(['type', 'rating', 'from_date', 'to_date']) ? 'Try adjusting your filters' : 'You haven\'t received any reviews yet' }}</p>
            </div>
        </div>
    @endif
</div>
@endsection
