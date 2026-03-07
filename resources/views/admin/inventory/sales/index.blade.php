@extends('layouts.app')

@section('title', 'Sales')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('admin.dashboard')],
            ['label' => 'Inventory', 'url' => route('admin.inventory.products.index')],
            ['label' => 'Sales']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">Sales</h1>
        <p class="text-muted mb-0">Manage sales records</p>
    </div>
    <a href="{{ route('admin.inventory.sales.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>New Sale
    </a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('admin.inventory.sales.index') }}" class="row g-3">
            <div class="col-12 col-md-5">
                <label class="form-label small text-muted">Search</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Sale #, customer..." class="form-control form-control-sm">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label small text-muted">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

@if($sales->count() > 0)
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0">Sale #</th>
                            <th class="border-0">Customer</th>
                            <th class="border-0">Date</th>
                            <th class="border-0">Total</th>
                            <th class="border-0">Status</th>
                            <th class="border-0 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sales as $sale)
                            <tr>
                                <td class="fw-semibold">{{ $sale->sale_number }}</td>
                                <td>{{ $sale->customer_name }}</td>
                                <td>{{ $sale->sale_date->format('M d, Y') }}</td>
                                <td>{{ number_format($sale->total, 2) }}</td>
                                <td><span class="badge bg-{{ $sale->status === 'completed' ? 'success' : ($sale->status === 'cancelled' ? 'secondary' : 'warning') }}">{{ ucfirst($sale->status) }}</span></td>
                                <td class="text-end">
                                    <a href="{{ route('admin.inventory.sales.show', $sale) }}" class="btn btn-outline-primary btn-sm">View</a>
                                    <a href="{{ route('admin.inventory.sales.edit', $sale) }}" class="btn btn-outline-secondary btn-sm">Edit</a>
                                    <a href="{{ route('admin.inventory.sales.invoice', $sale) }}" class="btn btn-outline-secondary btn-sm">Invoice</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0">
            {{ $sales->links() }}
        </div>
    </div>
@else
    <div class="card border-0 shadow-sm">
        <div class="card-body p-5 text-center">
            <i class="bi bi-receipt fs-1 text-muted mb-3"></i>
            <p class="text-muted mb-0">No sales found.</p>
            <a href="{{ route('admin.inventory.sales.create') }}" class="btn btn-primary mt-3">New Sale</a>
        </div>
    </div>
@endif
@endsection
