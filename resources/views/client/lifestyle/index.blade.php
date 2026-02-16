@extends('client.layout')

@section('title', 'Lifestyle Monitoring - Your Mind Aid')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Lifestyle Monitoring</h4>
    <p class="text-muted mb-0 small">Track habits, diet, and activity notes</p>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Quick log -->
<div class="card mb-3">
    <div class="card-body">
        <h6 class="fw-semibold mb-3">Log for today ({{ \Carbon\Carbon::parse($today)->format('M d, Y') }})</h6>
        <form action="{{ route('client.lifestyle.store') }}" method="POST">
            @csrf
            <input type="hidden" name="logged_date" value="{{ $today }}">
            <div class="mb-2">
                <label class="form-label small">Type</label>
                <select name="type" class="form-select form-select-sm" required>
                    <option value="habit">Habit</option>
                    <option value="diet">Diet / Nutrition</option>
                    <option value="activity_note">Activity note</option>
                    <option value="lifestyle_error">Lifestyle error / Issue</option>
                </select>
            </div>
            <div class="mb-2">
                <label class="form-label small">Label (optional)</label>
                <input type="text" name="label" class="form-control form-control-sm" placeholder="e.g. Drank 8 glasses water">
            </div>
            <div class="mb-2">
                <label class="form-label small">Details (optional)</label>
                <textarea name="value" class="form-control form-control-sm" rows="2" placeholder="Notes..."></textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">Add</button>
        </form>
    </div>
</div>

<!-- Today's logs -->
@if($logs->isNotEmpty())
    <div class="card mb-3">
        <div class="card-header bg-white border-0">
            <h6 class="mb-0 fw-semibold">Today's entries</h6>
        </div>
        <div class="card-body">
            @foreach($logs as $type => $entries)
                <div class="mb-2">
                    <small class="text-muted text-uppercase">{{ str_replace('_', ' ', $type) }}</small>
                    @foreach($entries as $entry)
                        <div class="d-flex justify-content-between align-items-start border-bottom py-2">
                            <div>
                                <strong>{{ $entry->label ?: ucfirst($type) }}</strong>
                                @if($entry->value)<br><small class="text-muted">{{ $entry->value }}</small>@endif
                            </div>
                            <small class="text-muted">{{ $entry->created_at->format('g:i A') }}</small>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
@endif

<!-- Recent history -->
@if($recentLogs->isNotEmpty())
    <div class="card">
        <div class="card-header bg-white border-0">
            <h6 class="mb-0 fw-semibold">Recent history</h6>
        </div>
        <div class="card-body">
            @foreach($recentLogs->take(10) as $entry)
                <div class="d-flex justify-content-between align-items-start border-bottom py-2">
                    <div>
                        <span class="badge bg-light text-dark">{{ str_replace('_', ' ', $entry->type) }}</span>
                        {{ $entry->label ?: $entry->value ? Str::limit($entry->value, 40) : '—' }}
                    </div>
                    <small class="text-muted">{{ $entry->logged_date->format('M d') }}</small>
                </div>
            @endforeach
        </div>
    </div>
@endif
@endsection
