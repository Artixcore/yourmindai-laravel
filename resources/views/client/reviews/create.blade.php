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
        
        <h1 class="text-2xl font-bold text-gray-900">
            @if ($reviewType === 'doctor')
                Review Your Doctor
            @else
                Review Session
            @endif
        </h1>
        
        @if ($reviewType === 'doctor')
            <p class="text-gray-600 mt-1">Dr. {{ $patient->doctor->name }}</p>
        @elseif ($session)
            <p class="text-gray-600 mt-1">{{ $session->title }}</p>
            <p class="text-sm text-gray-500">{{ $session->created_at->format('M d, Y') }}</p>
        @endif
    </div>

    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('client.reviews.store') }}" method="POST" class="space-y-6">
        @csrf
        <input type="hidden" name="review_type" value="{{ $reviewType }}">
        @if ($session)
            <input type="hidden" name="session_id" value="{{ $session->id }}">
        @endif

        <!-- Overall Rating -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <label class="block text-lg font-medium text-gray-900 mb-4">
                Overall Rating <span class="text-red-500">*</span>
            </label>
            <x-star-rating name="overall_rating" :rating="old('overall_rating', 0)" size="lg" />
            @error('overall_rating')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Dynamic Questions -->
        @foreach ($questions as $question)
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <label class="block text-base font-medium text-gray-900 mb-4">
                    {{ $question->question_text }}
                    @if ($question->is_required)
                        <span class="text-red-500">*</span>
                    @endif
                </label>

                @if ($question->question_type === 'star_rating')
                    <x-star-rating 
                        :name="'answers[' . $question->id . ']'" 
                        :rating="old('answers.' . $question->id, 0)" 
                        size="md" 
                    />
                @elseif ($question->question_type === 'yes_no')
                    <div class="flex gap-4">
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="answers[{{ $question->id }}]" 
                                   value="yes" 
                                   {{ old('answers.' . $question->id) === 'yes' ? 'checked' : '' }}
                                   class="w-4 h-4 text-purple-600 focus:ring-purple-500 border-gray-300">
                            <span class="ml-2 text-gray-700">Yes</span>
                        </label>
                        <label class="flex items-center">
                            <input type="radio" 
                                   name="answers[{{ $question->id }}]" 
                                   value="no" 
                                   {{ old('answers.' . $question->id) === 'no' ? 'checked' : '' }}
                                   class="w-4 h-4 text-purple-600 focus:ring-purple-500 border-gray-300">
                            <span class="ml-2 text-gray-700">No</span>
                        </label>
                    </div>
                @elseif ($question->question_type === 'multiple_choice')
                    <div class="space-y-2">
                        @foreach ($question->options as $option)
                            <label class="flex items-center">
                                <input type="radio" 
                                       name="answers[{{ $question->id }}]" 
                                       value="{{ $option->option_value }}" 
                                       {{ old('answers.' . $question->id) === $option->option_value ? 'checked' : '' }}
                                       class="w-4 h-4 text-purple-600 focus:ring-purple-500 border-gray-300">
                                <span class="ml-2 text-gray-700">{{ $option->option_text }}</span>
                            </label>
                        @endforeach
                    </div>
                @endif

                @error('answers.' . $question->id)
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        @endforeach

        <!-- Additional Comments -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <label for="comment" class="block text-base font-medium text-gray-900 mb-4">
                Additional Comments
            </label>
            <textarea 
                name="comment" 
                id="comment" 
                rows="4" 
                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent"
                placeholder="Share more about your experience..."
            >{{ old('comment') }}</textarea>
        </div>

        <!-- Anonymous Option -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <label class="flex items-center">
                <input type="checkbox" 
                       name="is_anonymous" 
                       value="1" 
                       {{ old('is_anonymous') ? 'checked' : '' }}
                       class="w-4 h-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                <span class="ml-2 text-gray-700">Submit this review anonymously</span>
            </label>
            <p class="text-sm text-gray-500 mt-2 ml-6">Your identity will be hidden from other users, but visible to administrators</p>
        </div>

        <!-- Submit Button -->
        <div class="flex gap-4">
            <button type="submit" 
                    class="flex-1 bg-purple-600 text-white py-3 px-6 rounded-lg font-medium hover:bg-purple-700 transition">
                Submit Review
            </button>
            <a href="{{ route('client.reviews.index') }}" 
               class="flex-1 bg-gray-200 text-gray-700 py-3 px-6 rounded-lg font-medium text-center hover:bg-gray-300 transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
