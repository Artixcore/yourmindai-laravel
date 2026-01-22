@extends('layouts.app')

@section('title', 'Contingency Plans')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('dashboard')],
            ['label' => 'Patients', 'url' => route('patients.index')],
            ['label' => $patient->name, 'url' => route('patients.show', $patient)],
            ['label' => 'Contingency Plans']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">Contingency Plans</h1>
        <p class="text-muted mb-0">Manage emergency plans for {{ $patient->name }}</p>
    </div>
    <a href="{{ route('patients.contingency-plans.create', $patient) }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Create Plan
    </a>
</div>

<!-- Plans List -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($plans->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-shield-exclamation text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 mb-2">No contingency plans</h5>
            <p class="text-muted mb-0">Create a contingency plan for this patient.</p>
            <a href="{{ route('patients.contingency-plans.create', $patient) }}" class="btn btn-primary mt-3">
                <i class="bi bi-plus-circle me-2"></i>Create Plan
            </a>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 ps-4">Title</th>
                        <th class="border-0">Status</th>
                        <th class="border-0">Activations</th>
                        <th class="border-0">Created</th>
                        <th class="border-0 text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($plans as $plan)
                    <tr>
                        <td class="ps-4">
                            <strong>{{ $plan->title }}</strong>
                        </td>
                        <td>
                            @if($plan->status === 'active')
                            <span class="badge bg-success">Active</span>
                            @else
                            <span class="badge bg-secondary">Inactive</span>
                            @endif
                        </td>
                        <td>
                            {{ $plan->activations->count() }} activation(s)
                        </td>
                        <td>
                            {{ $plan->created_at->format('M d, Y') }}
                        </td>
                        <td class="text-end pe-4">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('patients.contingency-plans.show', ['patient' => $patient, 'contingencyPlan' => $plan]) }}" class="btn btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('patients.contingency-plans.edit', ['patient' => $patient, 'contingencyPlan' => $plan]) }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('patients.contingency-plans.destroy', ['patient' => $patient, 'contingencyPlan' => $plan]) }}" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this plan?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
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
@endsection
