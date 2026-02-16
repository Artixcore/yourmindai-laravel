@extends('layouts.app')

@section('title', 'Behavior Contingency Plans - Admin')

@section('content')
<div class="mb-4">
    <x-breadcrumb :items="[
        ['label' => 'Admin', 'url' => route('admin.dashboard')],
        ['label' => 'Behavior Contingency Plans']
    ]" />
    <h1 class="h3 mb-1 fw-semibold">Behavior Contingency Plans</h1>
    <p class="text-muted mb-0">All behavior plans across the platform</p>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($plans->isEmpty())
        <div class="text-center py-5">
            <p class="text-muted mb-0">No behavior contingency plans yet.</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 ps-4">Title</th>
                        <th class="border-0">Patient</th>
                        <th class="border-0">Status</th>
                        <th class="border-0">Created</th>
                        <th class="border-0 text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($plans as $plan)
                    <tr>
                        <td class="ps-4"><strong>{{ $plan->title }}</strong></td>
                        <td>
                            @if($plan->patient)
                            <a href="{{ route('admin.patients.show', $plan->patient) }}">{{ $plan->patient->name }}</a>
                            @else
                            -
                            @endif
                        </td>
                        <td><span class="badge {{ $plan->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ $plan->status }}</span></td>
                        <td>{{ $plan->created_at->format('M d, Y') }}</td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.behavior-contingency.checkin-reviews.show', $plan) }}" class="btn btn-sm btn-outline-primary">Review</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            {{ $plans->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
