@extends('layouts.app')

@section('title', 'Orders')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('admin.dashboard')],
            ['label' => 'Inventory', 'url' => route('admin.inventory.products.index')],
            ['label' => 'Orders']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">Orders</h1>
        <p class="text-muted mb-0">View and manage orders</p>
    </div>
    <a href="{{ route('admin.inventory.orders.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>New Order
    </a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('admin.inventory.orders.index') }}" class="row g-3">
            <div class="col-12 col-md-5">
                <label class="form-label small text-muted">Search</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Order #, customer..." class="form-control form-control-sm">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label small text-muted">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

@if($orders->count() > 0)
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0">Order #</th>
                            <th class="border-0">Customer</th>
                            <th class="border-0">Total</th>
                            <th class="border-0">Status</th>
                            <th class="border-0">Date</th>
                            <th class="border-0 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td class="fw-semibold">{{ $order->order_number }}</td>
                                <td>{{ $order->customer_name }}<br><small class="text-muted">{{ $order->customer_email }}</small></td>
                                <td>{{ number_format($order->total, 2) }}</td>
                                <td><span class="badge bg-{{ match($order->status) { 'pending' => 'warning', 'confirmed','processing' => 'info', 'shipped' => 'success', 'cancelled' => 'secondary', default => 'secondary' } }}">{{ ucfirst($order->status) }}</span></td>
                                <td>{{ $order->created_at->format('M d, Y') }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.inventory.orders.show', $order) }}" class="btn btn-outline-primary btn-sm">View</a>
                                    <a href="{{ route('admin.inventory.orders.invoice', $order) }}" class="btn btn-outline-secondary btn-sm">Invoice</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0">
            {{ $orders->links() }}
        </div>
    </div>
@else
    <div class="card border-0 shadow-sm">
        <div class="card-body p-5 text-center">
            <i class="bi bi-cart fs-1 text-muted mb-3"></i>
            <p class="text-muted mb-0">No orders found.</p>
            <a href="{{ route('admin.inventory.orders.create') }}" class="btn btn-primary mt-3">Create Order</a>
        </div>
    </div>
@endif
@endsection
