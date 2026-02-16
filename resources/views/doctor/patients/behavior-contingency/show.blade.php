@extends('layouts.app')

@section('title', 'Behavior Contingency Plan - ' . $plan->title)

@section('content')
<div class="mb-4">
    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Patients', 'url' => route('patients.index')],
        ['label' => $patient->name, 'url' => route('patients.show', $patient)],
        ['label' => 'Behavior Contingency Plans', 'url' => route('patients.behavior-contingency-plans.index', $patient)],
        ['label' => $plan->title]
    ]" />
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1 class="h3 mb-1 fw-semibold">{{ $plan->title }}</h1>
            <p class="text-muted mb-0">
                {{ $plan->starts_at->format('M d, Y') }}
                @if($plan->ends_at)
                – {{ $plan->ends_at->format('M d, Y') }}
                @endif
                | {{ $plan->items->count() }} behavior(s)
            </p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('patients.behavior-contingency-plans.edit', [$patient, $plan]) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-2"></i>Edit
            </a>
            <a href="{{ route('behavior-contingency.checkin-reviews.show', $plan) }}" class="btn btn-outline-primary">
                <i class="bi bi-clipboard-check me-2"></i>Review Check-ins
            </a>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0">
        <h5 class="mb-0 fw-semibold">Behavior Items</h5>
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
                    </tr>
                </thead>
                <tbody>
                    @foreach($plan->items as $item)
                    <tr>
                        <td>{{ $item->target_behavior }}</td>
                        <td>{{ $item->condition_stimulus }}</td>
                        <td>{{ $item->reward_if_followed ?? '—' }}</td>
                        <td>{{ $item->punishment_if_not_followed ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0">
        <h5 class="mb-0 fw-semibold">Recent Check-ins</h5>
    </div>
    <div class="card-body p-0">
        @php $recentCheckins = $plan->checkins()->with('planItem')->orderBy('date', 'desc')->take(10)->get(); @endphp
        @if($recentCheckins->isEmpty())
        <div class="text-center py-4 text-muted">
            <p class="mb-0">No check-ins yet.</p>
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
                        <th class="border-0">Reviewer</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentCheckins as $c)
                    <tr>
                        <td>{{ $c->date->format('M d, Y') }}</td>
                        <td>{{ $c->planItem->target_behavior ?? '—' }}</td>
                        <td><span class="badge {{ $c->followed ? 'bg-success' : 'bg-warning' }}">{{ $c->followed ? 'Yes' : 'No' }}</span></td>
                        <td>{{ Str::limit($c->client_note, 30) ?: '—' }}</td>
                        <td>{{ $c->reviewer->name ?? '—' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('patients.behavior-contingency-plans.index', $patient) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Plans
    </a>
</div>
@endsection
