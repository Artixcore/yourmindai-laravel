@props(['resource', 'patient'])

<div 
    class="card bg-white rounded-xl shadow p-4 hover-shadow-xl"
    x-data="{ showActions: false }"
    @mouseenter="showActions = true"
    @mouseleave="showActions = false"
>
    <!-- Resource Type Icon & Title -->
    <div class="d-flex align-items-start gap-3 mb-3">
        @if($resource->is_pdf)
            <div class="flex-shrink-0 bg-red-100 rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                <svg style="width: 24px; height: 24px;" class="text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
            </div>
        @else
            <div class="flex-shrink-0 bg-red-100 rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                <svg style="width: 24px; height: 24px;" class="text-red-600" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                </svg>
            </div>
        @endif
        
        <div class="flex-grow-1" style="min-width: 0;">
            <h3 class="h6 fw-semibold text-stone-900 text-truncate mb-1">{{ $resource->title }}</h3>
            <p class="small text-stone-500 mb-0">
                {{ $resource->created_at->format('M d, Y') }}
            </p>
        </div>
    </div>

    <!-- Linked Session/Day Info -->
    @if($resource->session || $resource->sessionDay)
        <div class="mb-3 pt-3 border-top border-stone-200">
            @if($resource->session)
                <div class="d-flex align-items-center gap-2 small text-stone-600 mb-1">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span class="fw-medium">{{ $resource->session->title }}</span>
                </div>
            @endif
            @if($resource->sessionDay)
                <div class="d-flex align-items-center gap-2 small text-stone-600">
                    <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>{{ $resource->sessionDay->day_date->format('M d, Y') }}</span>
                </div>
            @endif
        </div>
    @endif

    <!-- YouTube Preview -->
    @if($resource->is_youtube && $resource->youtube_embed_url)
        <div class="mb-3 rounded overflow-hidden bg-stone-100" style="aspect-ratio: 16/9;">
            <iframe 
                src="{{ $resource->youtube_embed_url }}"
                frameborder="0"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                allowfullscreen
                class="w-100 h-100"
            ></iframe>
        </div>
    @endif

    <!-- Actions -->
    <div 
        x-show="showActions"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform translate-y-2"
        x-transition:enter-end="opacity-100 transform translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="d-flex align-items-center justify-content-end gap-2 pt-3 border-top border-stone-200"
    >
        @if($resource->is_pdf)
            <a
                href="{{ route('patients.resources.download', [$patient, $resource]) }}"
                class="btn btn-primary btn-sm d-flex align-items-center gap-2"
            >
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                </svg>
                <span>Download</span>
            </a>
        @else
            <a
                href="{{ $resource->youtube_url }}"
                target="_blank"
                rel="noopener noreferrer"
                class="btn btn-danger btn-sm d-flex align-items-center gap-2"
            >
                <svg style="width: 16px; height: 16px;" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                </svg>
                <span>Watch</span>
            </a>
        @endif
        
        <button
            type="button"
            @click="$dispatch('resource-edit-open', { resource: {{ json_encode($resource->toArray()) }} })"
            class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2"
        >
            <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            <span>Edit</span>
        </button>
        
        <form
            action="{{ route('patients.resources.destroy', [$patient, $resource]) }}"
            method="POST"
            class="d-inline"
            onsubmit="return confirm('Are you sure you want to delete this resource?');"
        >
            @csrf
            @method('DELETE')
            <button
                type="submit"
                class="btn btn-outline-danger btn-sm d-flex align-items-center gap-2"
            >
                <svg style="width: 16px; height: 16px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
                <span>Delete</span>
            </button>
        </form>
    </div>
</div>

<style>
    .hover-shadow-xl:hover {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
        transform: translateY(-4px);
        transition: all 0.3s ease;
    }
</style>
