@extends('client.layout')

@section('content')
<div class="container mx-auto px-4 py-6 pb-24">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">My Reviews</h1>
        <p class="text-gray-600 mt-1">Your feedback helps improve care quality</p>
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

    <!-- Pending Reviews Section -->
    @if (!$doctorReviewed || $unreviewedSessions->count() > 0)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <h2 class="text-lg font-semibold text-blue-900 mb-3">Pending Reviews</h2>
            
            @if (!$doctorReviewed)
                <a href="{{ route('client.reviews.create', ['type' => 'doctor']) }}" 
                   class="block bg-white border border-blue-300 rounded-lg p-4 mb-3 hover:bg-blue-50 transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-medium text-gray-900">Review Your Doctor</h3>
                            <p class="text-sm text-gray-600">Dr. {{ $patient->doctor->name }}</p>
                        </div>
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
            @endif

            @foreach ($unreviewedSessions as $session)
                <a href="{{ route('client.reviews.create', ['type' => 'session', 'session_id' => $session->id]) }}" 
                   class="block bg-white border border-blue-300 rounded-lg p-4 mb-3 hover:bg-blue-50 transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="font-medium text-gray-900">Review Session</h3>
                            <p class="text-sm text-gray-600">{{ $session->title }}</p>
                            <p class="text-xs text-gray-500">{{ $session->created_at->format('M d, Y') }}</p>
                        </div>
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </div>
                </a>
            @endforeach
        </div>
    @endif

    <!-- Past Reviews -->
    <div class="mb-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Past Reviews</h2>
        
        @if ($reviews->count() > 0)
            <div class="space-y-4">
                @foreach ($reviews as $review)
                    <a href="{{ route('client.reviews.show', $review) }}" class="block">
                        <x-review-card :review="$review" :showDoctor="true" :showSession="true" :compact="true" 
                                       class="hover:shadow-md transition" />
                    </a>
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
                <p class="text-gray-600">Start by reviewing your doctor or recent sessions</p>
            </div>
        @endif
    </div>
</div>
@endsection
