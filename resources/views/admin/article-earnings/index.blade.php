@extends('layouts.app')

@section('title', 'Article Earnings')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h1 class="h2 fw-bold">Article Earnings</h1>
        <p class="text-muted">Manage author earnings and payments</p>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><small>Total Earnings</small><h4>${{ number_format($stats['total_earnings'], 2) }}</h4></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><small>Paid</small><h4 class="text-success">${{ number_format($stats['paid_earnings'], 2) }}</h4></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><small>Pending</small><h4 class="text-warning">${{ number_format($stats['pending_earnings'], 2) }}</h4></div></div></div>
        <div class="col-md-3"><div class="card border-0 shadow-sm"><div class="card-body"><small>Authors</small><h4>{{ $stats['total_authors'] }}</h4></div></div></div>
    </div>

    <!-- Calculate Earnings Form -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white"><h6 class="mb-0">Calculate New Period</h6></div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.article-earnings.calculate') }}" class="row g-3">
                @csrf
                <div class="col-md-5">
                    <label class="form-label">Period Start</label>
                    <input type="date" name="period_start" class="form-control" required>
                </div>
                <div class="col-md-5">
                    <label class="form-label">Period End</label>
                    <input type="date" name="period_end" class="form-control" required>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Calculate</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Earnings List -->
    @if ($earnings->count() > 0)
        <div class="card border-0 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th>Author</th>
                            <th>Article</th>
                            <th>Period</th>
                            <th>Views</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($earnings as $earning)
                            <tr>
                                <td>{{ $earning->user->name }}</td>
                                <td>{{ Str::limit($earning->article->title, 40) }}</td>
                                <td>{{ $earning->period_start->format('M Y') }}</td>
                                <td>{{ number_format($earning->views_count) }}</td>
                                <td class="fw-bold">${{ number_format($earning->earnings_amount, 2) }}</td>
                                <td>
                                    <span class="badge {{ $earning->status === 'paid' ? 'bg-success' : 'bg-warning' }}">
                                        {{ ucfirst($earning->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if ($earning->status !== 'paid')
                                        <form method="POST" action="{{ route('admin.article-earnings.paid', $earning) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">Mark Paid</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-4">{{ $earnings->links() }}</div>
    @else
        <div class="card"><div class="card-body text-center py-5"><p class="text-muted mb-0">No earnings calculated yet</p></div></div>
    @endif
</div>
@endsection
