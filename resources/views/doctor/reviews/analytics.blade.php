@extends('layouts.app')

@section('title', 'Review Analytics')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="mb-4">
        <h1 class="h2 fw-bold text-stone-900">Review Analytics</h1>
        <p class="text-stone-600 mt-2">Analyze your performance and patient feedback trends</p>
    </div>

    <!-- Overall Statistics -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-4">
            <x-rating-summary 
                :averageRating="$stats['average_rating']" 
                :totalReviews="$stats['total_reviews']" 
                :ratingDistribution="$ratingDistribution->toArray()" 
            />
        </div>

        <div class="col-12 col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Performance Metrics</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="p-3 bg-light rounded">
                                <p class="text-muted small mb-1">Doctor Reviews</p>
                                <h4 class="mb-0">{{ $stats['doctor_reviews'] }}</h4>
                                <p class="text-success small mb-0">
                                    Avg: {{ number_format($stats['doctor_average_rating'], 1) }} ★
                                </p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded">
                                <p class="text-muted small mb-1">Session Reviews</p>
                                <h4 class="mb-0">{{ $stats['session_reviews'] }}</h4>
                                <p class="text-success small mb-0">
                                    Avg: {{ number_format($stats['session_average_rating'], 1) }} ★
                                </p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded">
                                <p class="text-muted small mb-1">5-Star Reviews</p>
                                <h4 class="mb-0">{{ $stats['five_star_count'] }}</h4>
                                <p class="text-muted small mb-0">
                                    {{ $stats['total_reviews'] > 0 ? round(($stats['five_star_count'] / $stats['total_reviews']) * 100) : 0 }}% of total
                                </p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-light rounded">
                                <p class="text-muted small mb-1">4+ Star Reviews</p>
                                <h4 class="mb-0">{{ $stats['five_star_count'] + $stats['four_star_count'] }}</h4>
                                <p class="text-muted small mb-0">
                                    {{ $stats['total_reviews'] > 0 ? round((($stats['five_star_count'] + $stats['four_star_count']) / $stats['total_reviews']) * 100) : 0 }}% of total
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reviews Over Time -->
    @if ($reviewsOverTime->count() > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Reviews Over Time (Last 12 Months)</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Reviews</th>
                                <th>Average Rating</th>
                                <th>Trend</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reviewsOverTime as $data)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($data->month . '-01')->format('M Y') }}</td>
                                    <td>{{ $data->count }}</td>
                                    <td>
                                        <x-star-rating :rating="$data->avg_rating" :readonly="true" size="sm" />
                                        <span class="ms-2">{{ number_format($data->avg_rating, 1) }}</span>
                                    </td>
                                    <td>
                                        @if ($data->avg_rating >= 4.5)
                                            <span class="badge bg-success">Excellent</span>
                                        @elseif ($data->avg_rating >= 3.5)
                                            <span class="badge bg-primary">Good</span>
                                        @else
                                            <span class="badge bg-warning">Needs Improvement</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    <!-- Question-Specific Analytics -->
    @if ($questionAnalytics && $questionAnalytics->count() > 0)
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Question-Specific Analytics</h5>
            </div>
            <div class="card-body">
                @foreach ($questionAnalytics as $questionId => $stats)
                    <div class="mb-4 pb-4 border-bottom last:border-bottom-0 last:pb-0 last:mb-0">
                        <h6 class="mb-3">{{ $stats['question'] }}</h6>
                        <p class="text-muted small mb-2">Total Responses: {{ $stats['total_responses'] }}</p>
                        
                        @if ($stats['type'] === 'star_rating')
                            <div class="d-flex align-items-center">
                                <x-star-rating :rating="$stats['average_rating']" :readonly="true" size="md" />
                                <span class="ms-2 h5 mb-0">{{ number_format($stats['average_rating'], 2) }}</span>
                                <span class="text-muted ms-2">(Range: {{ $stats['min_rating'] }} - {{ $stats['max_rating'] }})</span>
                            </div>
                        @elseif ($stats['type'] === 'yes_no')
                            <div class="progress" style="height: 30px;">
                                <div class="progress-bar bg-success" style="width: {{ $stats['yes_percentage'] }}%">
                                    Yes: {{ $stats['yes_count'] }} ({{ $stats['yes_percentage'] }}%)
                                </div>
                                <div class="progress-bar bg-danger" style="width: {{ 100 - $stats['yes_percentage'] }}%">
                                    No: {{ $stats['no_count'] }} ({{ round(100 - $stats['yes_percentage'], 1) }}%)
                                </div>
                            </div>
                        @elseif ($stats['type'] === 'multiple_choice')
                            @foreach ($stats['distribution'] as $option => $count)
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="small">{{ $option }}</span>
                                        <span class="small">{{ $count }} responses</span>
                                    </div>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar" style="width: {{ ($count / $stats['total_responses']) * 100 }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Recent Reviews -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Recent Reviews</h5>
            <a href="{{ route('doctor.reviews.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
        </div>
        <div class="card-body">
            @if ($recentReviews->count() > 0)
                <div class="list-group list-group-flush">
                    @foreach ($recentReviews as $review)
                        <div class="list-group-item px-0">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-2">
                                        <x-star-rating :rating="$review->overall_rating" :readonly="true" size="sm" />
                                        <span class="ms-2 text-muted small">{{ $review->created_at->diffForHumans() }}</span>
                                    </div>
                                    @if ($review->comment)
                                        <p class="mb-1 text-muted small">{{ Str::limit($review->comment, 100) }}</p>
                                    @endif
                                    <p class="mb-0 text-muted small">
                                        {{ $review->getPatientDisplayName() }} • {{ ucfirst($review->review_type) }} Review
                                    </p>
                                </div>
                                <a href="{{ route('doctor.reviews.show', $review) }}" class="btn btn-sm btn-outline-secondary">
                                    View
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-muted text-center py-4 mb-0">No recent reviews</p>
            @endif
        </div>
    </div>
</div>
@endsection
