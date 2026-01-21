@props(['session'])

<div class="card session-card shadow-sm border-0 mb-3">
    <div class="card-body p-3">
        <div class="d-flex align-items-start justify-content-between gap-3">
            <div class="flex-grow-1">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <h5 class="h6 fw-semibold text-stone-900 mb-0">
                        {{ $session->title }}
                    </h5>
                    <span class="badge bg-{{ $session->status === 'active' ? 'success' : 'secondary' }}">
                        {{ ucfirst($session->status) }}
                    </span>
                </div>
                
                @if($session->notes)
                    <p class="small text-stone-600 mb-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                        {{ Str::limit($session->notes, 100) }}
                    </p>
                @endif
                
                <div class="d-flex flex-wrap align-items-center gap-3 small text-stone-500 mb-2">
                    <span>
                        <i class="bi bi-calendar3 me-1"></i>
                        {{ $session->created_at->format('M d, Y') }}
                    </span>
                    @if(isset($session->days_count))
                        <span>
                            <i class="bi bi-calendar-check me-1"></i>
                            {{ $session->days_count }} day{{ $session->days_count !== 1 ? 's' : '' }}
                        </span>
                    @elseif($session->days)
                        <span>
                            <i class="bi bi-calendar-check me-1"></i>
                            {{ $session->days->count() }} day{{ $session->days->count() !== 1 ? 's' : '' }}
                        </span>
                    @endif
                </div>
                
                @if(isset($session->doctor))
                    <div class="d-flex align-items-center gap-2 small text-stone-600">
                        <i class="bi bi-person-heart text-patient-primary"></i>
                        <span class="fw-medium">Dr. {{ $session->doctor->name ?? $session->doctor->email }}</span>
                        @if(isset($session->doctor->email))
                            <span class="text-stone-400">•</span>
                            <a href="mailto:{{ $session->doctor->email }}" class="text-decoration-none text-patient-primary">
                                <i class="bi bi-envelope me-1"></i>
                                Contact
                            </a>
                        @endif
                    </div>
                @endif
            </div>
            <div class="flex-shrink-0">
                <a href="{{ route('patient.sessions.show', $session->id) }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-eye me-1"></i>
                    View
                </a>
            </div>
        </div>
    </div>
</div>
