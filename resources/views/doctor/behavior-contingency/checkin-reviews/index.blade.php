@extends('layouts.app')

@section('title', 'Check-in Reviews')

@section('content')
<div class="mb-4">
    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'Check-in Reviews']
    ]" />
    <h1 class="h3 mb-1 fw-semibold">Check-in Reviews</h1>
    <p class="text-muted mb-0">Review and finalize client check-ins with reward/punishment decisions</p>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($plans->isEmpty())
        <div class="text-center py-5">
            <i class="bi bi-clipboard-check text-muted" style="font-size: 3rem;"></i>
            <h5 class="mt-3 mb-2">No active plans</h5>
            <p class="text-muted mb-0">Create behavior contingency plans for patients to see check-ins here.</p>
        </div>
        @else
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 ps-4">Plan</th>
                        <th class="border-0">Patient</th>
                        <th class="border-0">Created By</th>
                        <th class="border-0 text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($plans as $plan)
                    <tr>
                        <td class="ps-4"><strong>{{ $plan->title }}</strong></td>
                        <td>
                            @if($plan->patient)
                            {{ $plan->patient->name }}
                            @elseif($plan->patientProfile)
                            {{ $plan->patientProfile->full_name ?? '-' }}
                            @else
                            -
                            @endif
                        </td>
                        <td>{{ $plan->createdBy->name ?? '-' }}</td>
                        <td class="text-end pe-4">
                            <a href="{{ route('behavior-contingency.checkin-reviews.show', $plan) }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-clipboard-check me-1"></i>Review
                            </a>
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
