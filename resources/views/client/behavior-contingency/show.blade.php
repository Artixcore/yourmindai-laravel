@extends('client.layout')

@section('title', $plan->title . ' - Your Mind Aid')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">{{ $plan->title }}</h4>
    <p class="text-muted mb-0 small">Today: {{ $today }}</p>
</div>

<!-- Behavior Table -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h6 class="mb-0 fw-semibold">Behavior Plan</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="border-0">Target Behavior</th>
                        <th class="border-0">Condition/Stimulus</th>
                        <th class="border-0">Reward (if followed)</th>
                        <th class="border-0">Punishment (if not)</th>
                        <th class="border-0">Today's Check-in</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($plan->items as $item)
                    <tr>
                        <td>{{ $item->target_behavior }}</td>
                        <td>{{ $item->condition_stimulus }}</td>
                        <td>{{ $item->reward_if_followed ?? '—' }}</td>
                        <td>{{ $item->punishment_if_not_followed ?? '—' }}</td>
                        <td>
                            @php $checkin = $todayCheckins->get($item->id); @endphp
                            @if($checkin)
                            <span class="badge {{ $checkin->followed ? 'bg-success' : 'bg-warning' }}">
                                {{ $checkin->followed ? 'Followed' : 'Not followed' }}
                            </span>
                            @if($checkin->client_note)
                            <small class="d-block text-muted mt-1">{{ Str::limit($checkin->client_note, 50) }}</small>
                            @endif
                            @else
                            <span class="text-muted small">Not yet</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Today's Check-in Form -->
@if($plan->isActive())
<div class="card mb-4">
    <div class="card-header bg-primary bg-opacity-10">
        <h6 class="mb-0 fw-semibold">Submit Today's Check-in</h6>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('client.contingency-plans.checkins.store', $plan) }}">
            @csrf
            @foreach($plan->items as $item)
            <div class="border rounded p-3 mb-3">
                <h6 class="fw-semibold mb-2">{{ $item->target_behavior }}</h6>
                <div class="mb-2">
                    <label class="form-label small">Did you follow this today?</label>
                    <div class="d-flex gap-3">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="checkins[{{ $loop->index }}][followed]" value="1" id="followed_yes_{{ $item->id }}" {{ ($todayCheckins->get($item->id)?->followed ?? null) === true ? 'checked' : '' }}>
                            <label class="form-check-label" for="followed_yes_{{ $item->id }}">Yes</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="checkins[{{ $loop->index }}][followed]" value="0" id="followed_no_{{ $item->id }}" {{ ($todayCheckins->get($item->id)?->followed ?? null) === false ? 'checked' : '' }}>
                            <label class="form-check-label" for="followed_no_{{ $item->id }}">No</label>
                        </div>
                    </div>
                </div>
                <div class="mb-0">
                    <label class="form-label small">Note (optional)</label>
                    <textarea class="form-control form-control-sm" name="checkins[{{ $loop->index }}][client_note]" rows="2" placeholder="Any notes...">{{ $todayCheckins->get($item->id)?->client_note }}</textarea>
                </div>
                <input type="hidden" name="checkins[{{ $loop->index }}][plan_item_id]" value="{{ $item->id }}">
            </div>
            @endforeach
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check2-circle me-2"></i>
                Save Check-in
            </button>
        </form>
    </div>
</div>
@endif

<div class="d-flex gap-2">
    <a href="{{ route('client.contingency-plans.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>
        Back to Plans
    </a>
    <a href="{{ route('client.contingency-plans.history', $plan) }}" class="btn btn-outline-primary">
        <i class="bi bi-clock-history me-2"></i>
        View History
    </a>
</div>
@endsection
