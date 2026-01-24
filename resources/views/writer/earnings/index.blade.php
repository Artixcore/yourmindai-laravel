@extends('layouts.app')

@section('title', 'My Earnings')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h2 fw-bold">My Earnings</h1>
        <p class="text-muted">Track your article revenue</p>
    </div>

    <!-- Earnings Summary -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted small">Total Earnings</h6>
                    <h2 class="mb-0 text-success">${{ number_format($earningsData['total_earnings'], 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted small">Paid</h6>
                    <h2 class="mb-0">${{ number_format($earningsData['paid_earnings'], 2) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h6 class="text-muted small">Pending Payment</h6>
                    <h2 class="mb-0 text-warning">${{ number_format($earningsData['pending_earnings'], 2) }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Earnings History -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Earnings History</h5>
        </div>
        <div class="card-body p-0">
            @if ($earnings->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>Period</th>
                                <th>Article</th>
                                <th>Views</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($earnings as $earning)
                                <tr>
                                    <td>{{ $earning->period_start->format('M Y') }}</td>
                                    <td>{{ Str::limit($earning->article->title, 40) }}</td>
                                    <td>{{ number_format($earning->views_count) }}</td>
                                    <td class="fw-bold">${{ number_format($earning->earnings_amount, 2) }}</td>
                                    <td>
                                        <span class="badge {{ $earning->status === 'paid' ? 'bg-success' : 'bg-warning' }}">
                                            {{ ucfirst($earning->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">{{ $earnings->links() }}</div>
            @else
                <div class="text-center py-5">
                    <p class="text-muted">No earnings yet. Keep writing!</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
