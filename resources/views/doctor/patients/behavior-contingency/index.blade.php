@extends('layouts.app')

@section('title', 'Behavior Contingency Plans')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('dashboard')],
            ['label' => 'Patients', 'url' => route('patients.index')],
            ['label' => $patient->name, 'url' => route('patients.show', $patient)],
            ['label' => 'Behavior Contingency Plans']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">Behavior Contingency Plans</h1>
        <p class="text-muted mb-0">Manage behavior plans with daily check-ins for {{ $patient->name }}</p>
    </div>
    <a href="{{ route('patients.behavior-contingency-plans.create', $patient) }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Create Plan
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($plans->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-clipboard-check text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 mb-2">No behavior contingency plans</h5>
            <p class="text-muted mb-0">Create a plan to track behaviors with rewards and punishments.</p>
            <a href="{{ route('patients.behavior-contingency-plans.create', $patient) }}" class="btn btn-primary mt-3">
                <i class="bi bi-plus-circle me-2"></i>Create Plan
            </a>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 ps-4">Title</th>
                        <th class="border-0">Dates</th>
                        <th class="border-0">Status</th>
                        <th class="border-0">Items</th>
                        <th class="border-0 text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($plans as $plan)
                    <tr>
                        <td class="ps-4"><strong>{{ $plan->title }}</strong></td>
                        <td>
                            {{ $plan->starts_at->format('M d, Y') }}
                            @if($plan->ends_at)
                            – {{ $plan->ends_at->format('M d, Y') }}
                            @endif
                        </td>
                        <td>
                            @if($plan->status === 'active')
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-secondary">Archived</span>
                            @endif
                        </td>
                        <td>{{ $plan->items->count() }} behavior(s)</td>
                        <td class="text-end pe-4">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('patients.behavior-contingency-plans.show', [$patient, $plan]) }}" class="btn btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('patients.behavior-contingency-plans.edit', [$patient, $plan]) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <a href="{{ route('behavior-contingency.checkin-reviews.show', $plan) }}" class="btn btn-outline-info" title="Review Check-ins">
                                    <i class="bi bi-clipboard-check"></i>
                                </a>
                                <form method="POST" action="{{ route('patients.behavior-contingency-plans.destroy', [$patient, $plan]) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this plan?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('behavior-contingency.checkin-reviews.index') }}" class="btn btn-outline-primary">
        <i class="bi bi-clipboard-check me-2"></i>Check-in Reviews
    </a>
</div>
@endsection
