@props(['resource'])

<div class="card resource-card-{{ $resource->type === 'pdf' ? 'pdf' : 'video' }} shadow-sm border-0 mb-3">
    <div class="card-body p-3">
        <div class="d-flex align-items-start gap-3">
            <div class="flex-shrink-0">
                @if($resource->type === 'pdf')
                    <div class="bg-red-100 rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-file-earmark-pdf text-red-600 fs-4"></i>
                    </div>
                @else
                    <div class="bg-red-100 rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-youtube text-red-600 fs-4"></i>
                    </div>
                @endif
            </div>
            
            <div class="flex-grow-1" style="min-width: 0;">
                <h6 class="fw-semibold text-stone-900 mb-1 text-truncate">
                    {{ $resource->title }}
                </h6>
                <p class="small text-stone-500 mb-2">
                    <i class="bi bi-calendar3 me-1"></i>
                    {{ $resource->created_at->format('M d, Y') }}
                </p>
                
                @if($resource->session)
                    <p class="small text-stone-600 mb-2">
                        <i class="bi bi-journal-text me-1"></i>
                        Session: {{ $resource->session->title }}
                    </p>
                @endif
                
                <div class="d-flex align-items-center gap-2">
                    @if($resource->type === 'pdf' && isset($resource->file_url))
                        <a 
                            href="{{ $resource->file_url }}" 
                            target="_blank"
                            class="btn btn-sm btn-danger"
                        >
                            <i class="bi bi-download me-1"></i>
                            Download PDF
                        </a>
                    @elseif($resource->type === 'youtube' && isset($resource->youtube_url))
                        <a 
                            href="{{ $resource->youtube_url }}" 
                            target="_blank"
                            rel="noopener noreferrer"
                            class="btn btn-sm btn-danger"
                        >
                            <i class="bi bi-youtube me-1"></i>
                            Watch Video
                        </a>
                    @endif
                </div>
            </div>
        </div>
        
        @if($resource->type === 'youtube' && isset($resource->youtube_embed_url))
            <div class="mt-3 rounded overflow-hidden bg-stone-100" style="aspect-ratio: 16/9;">
                <iframe 
                    src="{{ $resource->youtube_embed_url }}"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                    allowfullscreen
                    class="w-100 h-100"
                ></iframe>
            </div>
        @endif
    </div>
</div>
