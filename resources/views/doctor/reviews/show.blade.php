@extends('layouts.app')

@section('title', 'Review Details')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="mb-4">
        <a href="{{ route('doctor.reviews.index') }}" class="text-decoration-none text-primary mb-3 d-inline-flex align-items-center">
            <svg style="width: 20px; height: 20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            <span class="ms-1">Back to Reviews</span>
        </a>
        <h1 class="h2 fw-bold text-stone-900">Review Details</h1>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Review Header -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h4 class="mb-2">
                                @if ($review->review_type === 'doctor')
                                    Doctor Review
                                @else
                                    {{ $review->session->title }}
                                @endif
                            </h4>
                            <p class="text-muted small mb-2">
                                From: {{ $review->getPatientDisplayName() }}
                            </p>
                            <p class="text-muted small mb-0">
                                Submitted on {{ $review->created_at->format('M d, Y \a\t g:i A') }}
                            </p>
                        </div>
                        
                        <div class="text-end">
                            <span class="badge {{ $review->review_type === 'doctor' ? 'bg-purple' : 'bg-info' }} mb-2">
                                {{ ucfirst($review->review_type) }} Review
                            </span>
                            @if ($review->is_anonymous)
                                <br>
                                <span class="badge bg-secondary">
                                    Anonymous
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <x-star-rating :rating="$review->overall_rating" :readonly="true" size="lg" />
                        <span class="h4 ms-2">{{ number_format($review->overall_rating, 1) }} / 5.0</span>
                    </div>

                    @if ($review->comment)
                        <div class="border-top pt-3">
                            <h6 class="mb-2">Patient Comments:</h6>
                            <p class="text-muted mb-0">{{ $review->comment }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Question Responses -->
            @if ($review->answers && $review->answers->count() > 0)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0">Detailed Responses</h5>
                    </div>
                    <div class="card-body">
                        @foreach ($review->answers as $answer)
                            <div class="mb-4 pb-4 border-bottom last:border-bottom-0 last:pb-0 last:mb-0">
                                <p class="fw-medium mb-2">{{ $answer->question->question_text }}</p>
                                
                                @if ($answer->question->question_type === 'star_rating')
                                    <div class="d-flex align-items-center">
                                        <x-star-rating :rating="(int)$answer->answer_value" :readonly="true" size="sm" />
                                        <span class="ms-2 text-muted">{{ $answer->answer_value }} / 5</span>
                                    </div>
                                @else
                                    <p class="mb-0 text-success fw-medium">{{ $answer->formatted_answer }}</p>
                                @endif

                                @if ($answer->answer_text)
                                    <p class="text-muted small mt-2 mb-0">{{ $answer->answer_text }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Session Info (if applicable) -->
            @if ($review->session)
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">Session Information</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>Title:</strong> {{ $review->session->title }}</p>
                        <p class="mb-2"><strong>Patient:</strong> {{ $review->session->patient->name }}</p>
                        <p class="mb-2"><strong>Created:</strong> {{ $review->session->created_at->format('M d, Y') }}</p>
                        <p class="mb-0"><strong>Status:</strong> 
                            <span class="badge {{ $review->session->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($review->session->status) }}
                            </span>
                        </p>
                    </div>
                </div>
            @endif

            <!-- Review Metadata -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="mb-0">Review Metadata</h6>
                </div>
                <div class="card-body">
                    <p class="mb-2"><strong>Status:</strong> 
                        <span class="badge {{ $review->status === 'published' ? 'bg-success' : ($review->status === 'flagged' ? 'bg-danger' : 'bg-warning') }}">
                            {{ ucfirst($review->status) }}
                        </span>
                    </p>
                    <p class="mb-2"><strong>Submitted:</strong> {{ $review->created_at->diffForHumans() }}</p>
                    <p class="mb-0"><strong>Last Updated:</strong> {{ $review->updated_at->diffForHumans() }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
