@extends('layouts.app')

@section('title', 'Session Report: ' . $report->title)

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('session-reports.index') }}" class="text-decoration-none text-primary mb-3 d-inline-flex align-items-center">
            <i class="bi bi-arrow-left me-1"></i> Back to Reports
        </a>
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
            <h1 class="h2 fw-bold text-stone-900 mb-0">{{ $report->title }}</h1>
            <div class="d-flex flex-wrap align-items-center gap-2">
                <a href="{{ route('session-reports.download-pdf', $report) }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-download me-1"></i> Download PDF
                </a>
                <button type="button" class="btn btn-outline-secondary btn-sm" id="copy-link-btn">
                    <i class="bi bi-link-45deg me-1"></i> Copy link
                </button>
                <a href="#" class="btn btn-outline-success btn-sm" id="whatsapp-share-btn" target="_blank" rel="noopener">
                    <i class="bi bi-whatsapp me-1"></i> Share via WhatsApp
                </a>
                <button type="button" class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#sendEmailModal">
                    <i class="bi bi-envelope me-1"></i> Send via email
                </button>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <p class="text-muted small mb-2">Patient: {{ $report->patient->full_name ?? $report->patient->user->name ?? '—' }}</p>
            @if($report->session)
                <p class="text-muted small mb-2">Session: {{ $report->session->title }}</p>
            @endif
            <p class="text-muted small mb-3">Status: <span class="badge {{ $report->finalized_at ? 'bg-success' : 'bg-secondary' }}">{{ $report->finalized_at ? 'Finalized' : $report->status }}</span></p>
            <hr>
            <div class="mb-3">
                <h6 class="fw-semibold">Summary</h6>
                <p class="mb-0">{{ $report->summary }}</p>
            </div>
            @if($report->assessments_summary)
                <div class="mb-3">
                    <h6 class="fw-semibold">Assessments</h6>
                    <p class="mb-0">{{ $report->assessments_summary }}</p>
                </div>
            @endif
            @if($report->techniques_assigned)
                <div class="mb-3">
                    <h6 class="fw-semibold">Techniques assigned</h6>
                    <p class="mb-0">{{ $report->techniques_assigned }}</p>
                </div>
            @endif
            @if($report->progress_notes)
                <div class="mb-3">
                    <h6 class="fw-semibold">Progress notes</h6>
                    <p class="mb-0">{{ $report->progress_notes }}</p>
                </div>
            @endif
            @if($report->next_steps)
                <div class="mb-0">
                    <h6 class="fw-semibold">Next steps</h6>
                    <p class="mb-0">{{ $report->next_steps }}</p>
                </div>
            @endif
        </div>
    </div>

    @if(!$report->finalized_at)
        <div class="d-flex gap-2">
            <a href="{{ route('session-reports.edit', $report) }}" class="btn btn-primary">Edit report</a>
            <form action="{{ route('session-reports.finalize', $report) }}" method="POST" class="d-inline" onsubmit="return confirm('Finalize this report? It cannot be edited afterwards.');">
                @csrf
                <button type="submit" class="btn btn-success">Finalize report</button>
            </form>
        </div>
    @endif
</div>

<!-- Send via email modal -->
<div class="modal fade" id="sendEmailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('session-reports.send-email', $report) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Send report via email</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <label class="form-label">Email address</label>
                    <input type="email" name="email" class="form-control" required placeholder="recipient@example.com" value="{{ $report->patient->user->email ?? '' }}">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Send</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function() {
    const shareLinkUrl = "{{ route('session-reports.share-link', $report) }}";

    document.getElementById('copy-link-btn').addEventListener('click', function() {
        fetch(shareLinkUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
                navigator.clipboard.writeText(data.url).then(function() {
                    alert('Link copied to clipboard.');
                }).catch(function() {
                    prompt('Copy this link:', data.url);
                });
            })
            .catch(() => alert('Could not get share link.'));
    });

    document.getElementById('whatsapp-share-btn').addEventListener('click', function(e) {
        e.preventDefault();
        fetch(shareLinkUrl, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
            .then(r => r.json())
            .then(data => {
                window.open('https://wa.me/?text=' + encodeURIComponent('Session report: ' + data.url), '_blank');
            })
            .catch(() => alert('Could not get share link.'));
    });
})();
</script>
@endpush
@endsection
