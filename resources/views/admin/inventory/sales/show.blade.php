@extends('layouts.app')

@section('title', 'Sale ' . $sale->sale_number)

@section('content')
<div class="mb-4">
    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('admin.dashboard')],
        ['label' => 'Inventory', 'url' => route('admin.inventory.products.index')],
        ['label' => 'Sales', 'url' => route('admin.inventory.sales.index')],
        ['label' => $sale->sale_number]
    ]" />
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
        <div>
            <h1 class="h3 mb-1 fw-semibold">Sale {{ $sale->sale_number }}</h1>
            <p class="text-muted mb-0">{{ $sale->sale_date->format('M d, Y') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.inventory.sales.invoice', $sale) }}" class="btn btn-primary"><i class="bi bi-download me-1"></i>Download Invoice</a>
            <a href="{{ route('admin.inventory.sales.edit', $sale) }}" class="btn btn-outline-primary">Edit</a>
            <a href="{{ route('admin.inventory.sales.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
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
                @foreach($sale->saleItems as $item)
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
        <span>Subtotal: {{ number_format($sale->subtotal, 2) }}</span>
        @if($sale->tax > 0)<span>Tax: {{ number_format($sale->tax, 2) }}</span>@endif
        <strong>Total: {{ number_format($sale->total, 2) }}</strong>
    </div>
</div>

<div class="card border-0 shadow-sm mt-4">
    <div class="card-body">
        <p class="mb-1"><strong>Customer:</strong> {{ $sale->customer_name }}</p>
        @if($sale->customer_email)<p class="mb-1 text-muted small">{{ $sale->customer_email }}</p>@endif
        <p class="mb-0"><span class="badge bg-{{ $sale->status === 'completed' ? 'success' : ($sale->status === 'cancelled' ? 'secondary' : 'warning') }}">{{ ucfirst($sale->status) }}</span></p>
        @if($sale->notes)<p class="mt-2 small text-muted">{{ $sale->notes }}</p>@endif
    </div>
</div>
@endsection
