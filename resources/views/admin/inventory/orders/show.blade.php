@extends('layouts.app')

@section('title', 'Order ' . $order->order_number)

@section('content')
<div class="mb-4">
    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('admin.dashboard')],
        ['label' => 'Inventory', 'url' => route('admin.inventory.products.index')],
        ['label' => 'Orders', 'url' => route('admin.inventory.orders.index')],
        ['label' => $order->order_number]
    ]" />
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-1 fw-semibold">Order {{ $order->order_number }}</h1>
            <p class="text-muted mb-0">{{ $order->created_at->format('M d, Y H:i') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.inventory.orders.invoice', $order) }}" class="btn btn-primary"><i class="bi bi-download me-1"></i>Download Invoice</a>
            <a href="{{ route('admin.inventory.orders.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row">
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0">Items</h5>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0">Product</th>
                            <th class="border-0 text-end">Qty</th>
                            <th class="border-0 text-end">Unit Price</th>
                            <th class="border-0 text-end">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderItems as $item)
                            <tr>
                                <td>{{ $item->product->name ?? 'N/A' }}</td>
                                <td class="text-end">{{ $item->quantity }}</td>
                                <td class="text-end">{{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-end">{{ number_format($item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-0 d-flex justify-content-end gap-3 py-3">
                <span>Subtotal: {{ number_format($order->subtotal, 2) }}</span>
                @if($order->tax > 0)<span>Tax: {{ number_format($order->tax, 2) }}</span>@endif
                <strong>Total: {{ number_format($order->total, 2) }}</strong>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="mb-0">Customer &amp; Status</h5>
            </div>
            <div class="card-body">
                <p class="mb-1"><strong>{{ $order->customer_name }}</strong></p>
                <p class="mb-1 text-muted small">{{ $order->customer_email }}</p>
                @if($order->customer_phone)<p class="mb-1 text-muted small">{{ $order->customer_phone }}</p>@endif
                @if($order->customer_address)<p class="mb-1 text-muted small">{{ $order->customer_address }}</p>@endif
                @if($order->customer_city)<p class="mb-2 text-muted small">{{ $order->customer_city }}</p>@endif
                @if($order->notes)<p class="mb-2"><small>{{ $order->notes }}</small></p>@endif

                <form action="{{ route('admin.inventory.orders.update-status', $order) }}" method="POST" class="mt-3">
                    @csrf
                    <label class="form-label small">Status</label>
                    <div class="d-flex gap-2">
                        <select name="status" class="form-select form-select-sm">
                            @foreach(['pending','confirmed','processing','shipped','cancelled'] as $s)
                                <option value="{{ $s }}" {{ $order->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="btn btn-sm btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
