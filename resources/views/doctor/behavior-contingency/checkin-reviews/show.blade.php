@extends('layouts.app')

@section('title', 'Review Check-ins - ' . $plan->title)

@section('content')
<div class="mb-4">
    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Check-in Reviews', 'url' => route('behavior-contingency.checkin-reviews.index')],
        ['label' => $plan->title]
    ]" />
    <h1 class="h3 mb-1 fw-semibold">Review Check-ins</h1>
    <p class="text-muted mb-0">{{ $plan->title }} – Set reviewer notes and applied reward/punishment</p>
</div>

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

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($checkins->isEmpty())
        <div class="text-center py-5 text-muted">
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
                        <th class="border-0">Client Note</th>
                        <th class="border-0">Reviewer Note</th>
                        <th class="border-0">Applied Reward</th>
                        <th class="border-0">Applied Punishment</th>
                        <th class="border-0">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($checkins as $checkin)
                    <tr>
                        <td>{{ $checkin->date->format('M d, Y') }}</td>
                        <td>{{ $checkin->planItem->target_behavior ?? '—' }}</td>
                        <td><span class="badge {{ $checkin->followed ? 'bg-success' : 'bg-warning' }}">{{ $checkin->followed ? 'Yes' : 'No' }}</span></td>
                        <td>{{ Str::limit($checkin->client_note, 30) ?: '—' }}</td>
                        <td>{{ Str::limit($checkin->reviewer_note, 30) ?: '—' }}</td>
                        <td>{{ Str::limit($checkin->applied_reward, 30) ?: '—' }}</td>
                        <td>{{ Str::limit($checkin->applied_punishment, 30) ?: '—' }}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modal-{{ $checkin->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </td>
                    </tr>
                    <div class="modal fade" id="modal-{{ $checkin->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Review Check-in – {{ $checkin->date->format('M d, Y') }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST" action="{{ route('behavior-contingency.checkins.update', $checkin) }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body">
                                        <p class="small"><strong>Behavior:</strong> {{ $checkin->planItem->target_behavior }}</p>
                                        <p class="small"><strong>Followed:</strong> {{ $checkin->followed ? 'Yes' : 'No' }}</p>
                                        @if($checkin->client_note)
                                        <p class="small"><strong>Client note:</strong> {{ $checkin->client_note }}</p>
                                        @endif
                                        <div class="mb-3">
                                            <label class="form-label">Reviewer Note</label>
                                            <textarea class="form-control" name="reviewer_note" rows="2">{{ $checkin->reviewer_note }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label">Applied Reward</label>
                                            <input type="text" class="form-control" name="applied_reward" value="{{ $checkin->applied_reward }}" placeholder="If followed">
                                        </div>
                                        <div class="mb-0">
                                            <label class="form-label">Applied Punishment</label>
                                            <input type="text" class="form-control" name="applied_punishment" value="{{ $checkin->applied_punishment }}" placeholder="If not followed">
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-primary">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('behavior-contingency.checkin-reviews.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Reviews
    </a>
</div>
@endsection
