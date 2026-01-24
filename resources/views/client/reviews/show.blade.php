@extends('client.layout')

@section('content')
<div class="container mx-auto px-4 py-6 pb-24">
    <div class="mb-6">
        <a href="{{ route('client.reviews.index') }}" class="inline-flex items-center text-purple-600 hover:text-purple-700 mb-4">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Reviews
        </a>
        
        <h1 class="text-2xl font-bold text-gray-900">Review Details</h1>
    </div>

    @if (session('success'))
        <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Review Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-4">
        <div class="flex items-start justify-between mb-4">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">
                    @if ($review->review_type === 'doctor')
                        Dr. {{ $review->doctor->name }}
                    @else
                        {{ $review->session->title }}
                    @endif
                </h2>
                <div class="flex items-center gap-2 mb-2">
                    <x-star-rating :rating="$review->overall_rating" :readonly="true" size="md" />
                    <span class="text-lg font-medium text-gray-700">{{ number_format($review->overall_rating, 1) }}</span>
                </div>
                <p class="text-sm text-gray-500">Submitted {{ $review->created_at->format('M d, Y') }}</p>
            </div>
            
            <div class="flex flex-col gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    {{ $review->review_type === 'doctor' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                    {{ ucfirst($review->review_type) }} Review
                </span>
                
                @if ($review->is_anonymous)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                        Anonymous
                    </span>
                @endif
            </div>
        </div>

        @if ($review->comment)
            <div class="border-t border-gray-200 pt-4">
                <p class="text-gray-700 leading-relaxed">{{ $review->comment }}</p>
            </div>
        @endif
    </div>

    <!-- Question Answers -->
    @if ($review->answers && $review->answers->count() > 0)
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Your Responses</h3>
            <div class="space-y-4">
                @foreach ($review->answers as $answer)
                    <div class="border-b border-gray-100 pb-4 last:border-b-0 last:pb-0">
                        <p class="text-sm font-medium text-gray-700 mb-2">{{ $answer->question->question_text }}</p>
                        
                        @if ($answer->question->question_type === 'star_rating')
                            <x-star-rating :rating="(int)$answer->answer_value" :readonly="true" size="sm" />
                        @else
                            <p class="text-gray-900">{{ $answer->formatted_answer }}</p>
                        @endif

                        @if ($answer->answer_text)
                            <p class="text-sm text-gray-600 mt-1">{{ $answer->answer_text }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="flex gap-4">
        @if ($review->canBeEdited())
            <a href="{{ route('client.reviews.edit', $review) }}" 
               class="flex-1 bg-purple-600 text-white py-3 px-6 rounded-lg font-medium text-center hover:bg-purple-700 transition">
                Edit Review
            </a>
        @else
            <div class="flex-1 bg-gray-100 text-gray-500 py-3 px-6 rounded-lg font-medium text-center">
                Editing period expired
            </div>
        @endif
    </div>

    @if ($review->canBeEdited())
        <p class="text-sm text-gray-500 text-center mt-2">
            You can edit this review within 48 hours of submission
        </p>
    @endif
</div>
@endsection
