@extends('client.layout')

@section('title', 'Check-in History - Your Mind Aid')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Check-in History</h4>
    <p class="text-muted mb-0 small">{{ $plan->title }}</p>
</div>

<!-- Date Filter -->
<form method="GET" class="card mb-3">
    <div class="card-body">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small">From</label>
                <input type="date" name="from" class="form-control form-control-sm" value="{{ $from }}">
            </div>
            <div class="col-md-4">
                <label class="form-label small">To</label>
                <input type="date" name="to" class="form-control form-control-sm" value="{{ $to }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary btn-sm">Filter</button>
            </div>
        </div>
    </div>
</form>

<!-- History Table -->
<div class="card">
    <div class="card-body p-0">
        @if($checkins->isEmpty())
        <div class="text-center py-5 text-muted">
            <i class="bi bi-clock-history display-4 mb-3"></i>
            <p class="mb-0">No check-ins in this date range.</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="border-0">Date</th>
                        <th class="border-0">Behavior</th>
                        <th class="border-0">Followed</th>
                        <th class="border-0">Your Note</th>
                        <th class="border-0">Reviewer Note</th>
                        <th class="border-0">Applied Reward</th>
                        <th class="border-0">Applied Punishment</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($checkins as $checkin)
                    <tr>
                        <td>{{ $checkin->date->format('M d, Y') }}</td>
                        <td>{{ $checkin->planItem->target_behavior ?? '—' }}</td>
                        <td>
                            <span class="badge {{ $checkin->followed ? 'bg-success' : 'bg-warning' }}">
                                {{ $checkin->followed ? 'Yes' : 'No' }}
                            </span>
                        </td>
                        <td>{{ Str::limit($checkin->client_note, 40) ?: '—' }}</td>
                        <td>{{ Str::limit($checkin->reviewer_note, 40) ?: '—' }}</td>
                        <td>{{ Str::limit($checkin->applied_reward, 40) ?: '—' }}</td>
                        <td>{{ Str::limit($checkin->applied_punishment, 40) ?: '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('client.contingency-plans.show', $plan) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>
        Back to Plan
    </a>
</div>
@endsection
