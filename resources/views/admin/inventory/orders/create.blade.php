@extends('layouts.app')

@section('title', 'New Order')

@section('content')
<div class="container-fluid" style="max-width: 900px;">
    <div class="mb-4">
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('admin.dashboard')],
            ['label' => 'Inventory', 'url' => route('admin.inventory.products.index')],
            ['label' => 'Orders', 'url' => route('admin.inventory.orders.index')],
            ['label' => 'Create']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">New Order</h1>
        <p class="text-muted mb-0">Create an order (admins will be notified)</p>
    </div>

    <div class="card border-0 shadow-sm">
        <form action="{{ route('admin.inventory.orders.store') }}" method="POST" id="order-form">
            @csrf
            <div class="card-body p-4">
                <h5 class="fw-semibold mb-3">Customer</h5>
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-4">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="customer_name" value="{{ old('customer_name') }}" class="form-control @error('customer_name') is-invalid @enderror" required>
                        @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="customer_email" value="{{ old('customer_email') }}" class="form-control @error('customer_email') is-invalid @enderror" required>
                        @error('customer_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Phone</label>
                        <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" class="form-control @error('customer_phone') is-invalid @enderror">
                        @error('customer_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" rows="2" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed">Confirmed</option>
                            <option value="processing">Processing</option>
                            <option value="shipped">Shipped</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>

                <h5 class="fw-semibold mb-3">Items</h5>
                <div id="order-items">
                    @if(old('items'))
                        @foreach(old('items') as $i => $item)
                            <div class="row g-2 align-items-end mb-2 item-row">
                                <div class="col-12 col-md-5">
                                    <label class="form-label small">Product</label>
                                    <select name="items[{{ $i }}][product_id]" class="form-select form-select-sm product-select" required>
                                        <option value="">Select product</option>
                                        @foreach($products as $p)
                                            <option value="{{ $p->id }}" data-price="{{ $p->price }}" {{ ($item['product_id'] ?? '') == $p->id ? 'selected' : '' }}>{{ $p->name }} ({{ number_format($p->price, 2) }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-4 col-md-2">
                                    <label class="form-label small">Qty</label>
                                    <input type="number" name="items[{{ $i }}][quantity]" value="{{ $item['quantity'] ?? 1 }}" min="1" class="form-control form-control-sm qty-input" required>
                                </div>
                                <div class="col-6 col-md-2">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-item"><i class="bi bi-trash"></i></button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="row g-2 align-items-end mb-2 item-row">
                            <div class="col-12 col-md-5">
                                <label class="form-label small">Product</label>
                                <select name="items[0][product_id]" class="form-select form-select-sm product-select" required>
                                    <option value="">Select product</option>
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}" data-price="{{ $p->price }}">{{ $p->name }} ({{ number_format($p->price, 2) }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-4 col-md-2">
                                <label class="form-label small">Qty</label>
                                <input type="number" name="items[0][quantity]" value="1" min="1" class="form-control form-control-sm qty-input" required>
                            </div>
                            <div class="col-6 col-md-2">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-item"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                    @endif
                </div>
                <button type="button" id="add-item" class="btn btn-outline-primary btn-sm mt-2"><i class="bi bi-plus me-1"></i>Add line</button>

                @error('items')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
            </div>
            <div class="card-footer bg-white border-0 p-4">
                <button type="submit" class="btn btn-primary">Create Order</button>
                <a href="{{ route('admin.inventory.orders.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
(function() {
    const products = @json($products->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'price' => (float)$p->price])->values());
    let itemIndex = document.querySelectorAll('.item-row').length;

    document.getElementById('add-item').addEventListener('click', function() {
        const row = document.createElement('div');
        row.className = 'row g-2 align-items-end mb-2 item-row';
        let opts = '<option value="">Select product</option>';
        products.forEach(p => { opts += `<option value="${p.id}" data-price="${p.price}">${p.name} (${p.price.toFixed(2)})</option>`; });
        row.innerHTML = `
            <div class="col-12 col-md-5">
                <label class="form-label small">Product</label>
                <select name="items[${itemIndex}][product_id]" class="form-select form-select-sm product-select" required>
                    ${opts}
                </select>
            </div>
            <div class="col-4 col-md-2">
                <label class="form-label small">Qty</label>
                <input type="number" name="items[${itemIndex}][quantity]" value="1" min="1" class="form-control form-control-sm qty-input" required>
            </div>
            <div class="col-6 col-md-2">
                <button type="button" class="btn btn-outline-danger btn-sm remove-item"><i class="bi bi-trash"></i></button>
            </div>
        `;
        document.getElementById('order-items').appendChild(row);
        itemIndex++;
    });

    document.getElementById('order-items').addEventListener('click', function(e) {
        if (e.target.closest('.remove-item')) {
            const row = e.target.closest('.item-row');
            if (document.querySelectorAll('.item-row').length > 1) row.remove();
        }
    });
})();
</script>
@endpush
@endsection
