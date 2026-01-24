@props([
    'review',
    'showPatient' => false,
    'showDoctor' => false,
    'showSession' => false,
    'compact' => false
])

<div {{ $attributes->merge(['class' => 'bg-white rounded-lg shadow-sm border border-gray-200 p-4']) }}>
    <div class="flex items-start justify-between mb-3">
        <div class="flex-1">
            <div class="flex items-center gap-2 mb-1">
                @if ($showPatient)
                    <span class="font-medium text-gray-900">{{ $review->getPatientDisplayName() }}</span>
                @endif
                
                @if ($showDoctor)
                    <span class="font-medium text-gray-900">Dr. {{ $review->doctor->name }}</span>
                @endif
                
                @if ($review->is_anonymous)
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                        Anonymous
                    </span>
                @endif
                
                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium 
                    {{ $review->review_type === 'doctor' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                    {{ ucfirst($review->review_type) }} Review
                </span>
            </div>
            
            <div class="flex items-center gap-2">
                <x-star-rating :rating="$review->overall_rating" :readonly="true" size="sm" />
                <span class="text-sm font-medium text-gray-700">{{ number_format($review->overall_rating, 1) }}</span>
                <span class="text-sm text-gray-500">•</span>
                <span class="text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
            </div>
        </div>
        
        @if ($review->status !== 'published')
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                {{ $review->status === 'flagged' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                {{ ucfirst($review->status) }}
            </span>
        @endif
    </div>
    
    @if ($showSession && $review->session)
        <div class="mb-3 text-sm text-gray-600">
            <span class="font-medium">Session:</span> {{ $review->session->title }}
        </div>
    @endif
    
    @if ($review->comment && !$compact)
        <div class="mb-3">
            <p class="text-gray-700 text-sm leading-relaxed">{{ $review->comment }}</p>
        </div>
    @endif
    
    @unless ($compact)
        @if ($review->answers && $review->answers->count() > 0)
            <div class="border-t border-gray-100 pt-3 mt-3">
                <div class="space-y-2">
                    @foreach ($review->answers as $answer)
                        <div class="text-sm">
                            <span class="text-gray-600">{{ $answer->question->question_text }}</span>
                            <span class="ml-2 font-medium text-gray-900">{{ $answer->formatted_answer }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endunless
    
    {{ $slot }}
</div>
