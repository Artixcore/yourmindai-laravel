@extends('client.layout')

@section('content')
<div class="container mx-auto px-4 py-6 pb-24">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Dr. {{ $doctor->name }}</h1>
        <p class="text-gray-600 mt-1">Patient Reviews</p>
    </div>

    <!-- Rating Summary -->
    <x-rating-summary 
        :averageRating="$averageRating" 
        :totalReviews="$reviews->total()" 
        :ratingDistribution="$ratingDistribution" 
        class="mb-6"
    />

    <!-- Reviews List -->
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Reviews</h2>
        
        @if ($reviews->count() > 0)
            <div class="space-y-4">
                @foreach ($reviews as $review)
                    <x-review-card :review="$review" :showPatient="true" />
                @endforeach
            </div>

            <div class="mt-6">
                {{ $reviews->links() }}
            </div>
        @else
            <div class="text-center py-12 bg-gray-50 rounded-lg">
                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 mb-1">No reviews yet</h3>
                <p class="text-gray-600">Be the first to review this doctor</p>
            </div>
        @endif
    </div>
</div>
@endsection
