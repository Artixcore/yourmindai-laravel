@extends('client.layout')

@section('title', 'Digital Wellbeing - Your Mind Aid')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Digital Wellbeing & Device Log</h4>
    <p class="text-muted mb-0 small">Track screen time, log lifestyle issues, and view tips</p>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Log Screen Time -->
<div class="card mb-3">
    <div class="card-body">
        <h6 class="fw-semibold mb-3">
            <i class="bi bi-phone me-2 text-primary"></i>
            Log Screen Time
        </h6>
        <form action="{{ route('client.wellbeing.store') }}" method="POST" class="ajax-form">
            @csrf
            <input type="hidden" name="log_date" value="{{ $todayLog?->log_date?->format('Y-m-d') ?? now()->toDateString() }}">
            <div class="row g-2 mb-2">
                <div class="col-8">
                    <label class="form-label small">Screen time (minutes)</label>
                    <input type="number" name="screentime_minutes" class="form-control form-control-sm"
                           min="0" max="1440" placeholder="e.g. 120"
                           value="{{ old('screentime_minutes', $todayLog?->screentime_minutes) }}">
                </div>
                <div class="col-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-sm w-100" data-loading-text="Saving...">Save</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Screen Time History -->
@if($wellbeingLogs->isNotEmpty())
<div class="card mb-3">
    <div class="card-header bg-white border-0">
        <h6 class="mb-0 fw-semibold">Screen Time History</h6>
    </div>
    <div class="card-body">
        @foreach($wellbeingLogs->take(14) as $log)
        <div class="d-flex justify-content-between align-items-center border-bottom py-2">
            <div>{{ $log->log_date->format('M d, Y') }}</div>
            <div>
                @if($log->screentime_minutes !== null)
                    <span class="badge bg-primary">{{ $log->screentime_minutes }} min</span>
                @else
                    <span class="text-muted">—</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

<!-- Lifestyle Errors (from Lifestyle log) -->
@if($lifestyleErrors->isNotEmpty())
<div class="card mb-3">
    <div class="card-header bg-white border-0">
        <h6 class="mb-0 fw-semibold">Lifestyle Issues Logged</h6>
    </div>
    <div class="card-body">
        @foreach($lifestyleErrors as $err)
        <div class="d-flex justify-content-between align-items-start border-bottom py-2">
            <div>
                <strong>{{ $err->label ?: 'Issue' }}</strong>
                @if($err->value)<br><small class="text-muted">{{ Str::limit($err->value, 80) }}</small>@endif
            </div>
            <small class="text-muted">{{ $err->logged_date->format('M d') }}</small>
        </div>
        @endforeach
        <p class="small text-muted mb-0 mt-2">Log lifestyle issues from the <a href="{{ route('client.lifestyle.index') }}">Lifestyle</a> page (type: Lifestyle Error).</p>
    </div>
</div>
@endif

<!-- Screen time awareness -->
<div class="card mb-3">
    <div class="card-body">
        <h6 class="fw-semibold mb-2">
            <i class="bi bi-phone me-2 text-primary"></i>
            Screen time awareness
        </h6>
        <ul class="mb-0 ps-3">
            <li>Set daily limits for social media and entertainment apps.</li>
            <li>Use your device’s built-in screen time or digital wellbeing tools to track usage.</li>
            <li>Schedule screen-free periods (e.g. meals, first hour after waking).</li>
            <li>Turn off non-essential notifications to reduce constant checking.</li>
        </ul>
    </div>
</div>

<!-- Mindfulness prompts -->
<div class="card mb-3">
    <div class="card-body">
        <h6 class="fw-semibold mb-2">
            <i class="bi bi-heart me-2 text-primary"></i>
            Mindfulness prompts
        </h6>
        <ul class="mb-0 ps-3">
            <li>Before opening an app, take one deep breath and ask: “Do I need this right now?”</li>
            <li>Practice a 30-second pause: notice your feet on the floor and your breath before reaching for your phone.</li>
            <li>End your day with 2 minutes of quiet—no screens—to help sleep and mood.</li>
        </ul>
    </div>
</div>

<!-- Tips -->
<div class="card mb-3">
    <div class="card-body">
        <h6 class="fw-semibold mb-2">
            <i class="bi bi-lightbulb me-2 text-primary"></i>
            Wellbeing tips
        </h6>
        <ul class="mb-0 ps-3">
            <li>Keep your bedroom screen-free when possible to support better sleep.</li>
            <li>Replace some scroll time with a short walk or stretch.</li>
            <li>Curate your feed: mute or unfollow accounts that increase stress or comparison.</li>
            <li>Use technology for connection (e.g. calls, video) rather than passive scrolling when you can.</li>
        </ul>
    </div>
</div>
@endsection
