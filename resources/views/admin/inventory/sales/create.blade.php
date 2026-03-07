@extends('layouts.app')

@section('title', 'New Sale')

@section('content')
<div class="container-fluid" style="max-width: 900px;">
    <div class="mb-4">
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('admin.dashboard')],
            ['label' => 'Inventory', 'url' => route('admin.inventory.products.index')],
            ['label' => 'Sales', 'url' => route('admin.inventory.sales.index')],
            ['label' => 'Create']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">New Sale</h1>
        <p class="text-muted mb-0">Record a new sale</p>
    </div>

    <div class="card border-0 shadow-sm">
        <form action="{{ route('admin.inventory.sales.store') }}" method="POST" id="sale-form">
            @csrf
            <div class="card-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-4">
                        <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                        <input type="text" name="customer_name" value="{{ old('customer_name') }}" class="form-control @error('customer_name') is-invalid @enderror" required>
                        @error('customer_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Customer Email</label>
                        <input type="email" name="customer_email" value="{{ old('customer_email') }}" class="form-control @error('customer_email') is-invalid @enderror">
                        @error('customer_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Sale Date <span class="text-danger">*</span></label>
                        <input type="date" name="sale_date" value="{{ old('sale_date', date('Y-m-d')) }}" class="form-control @error('sale_date') is-invalid @enderror" required>
                        @error('sale_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="completed" {{ old('status', 'completed') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="pending">Pending</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>

                <h5 class="fw-semibold mb-3">Items</h5>
                <div id="sale-items">
                    @if(old('items'))
                        @foreach(old('items') as $i => $item)
                            <div class="row g-2 align-items-end mb-2 sale-item-row">
                                <div class="col-12 col-md-4">
                                    <label class="form-label small">Product</label>
                                    <select name="items[{{ $i }}][product_id]" class="form-select form-select-sm sale-product-select" required>
                                        <option value="">Select</option>
                                        @foreach($products as $p)
                                            <option value="{{ $p->id }}" data-price="{{ $p->price }}" {{ ($item['product_id'] ?? '') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-3 col-md-2">
                                    <label class="form-label small">Qty</label>
                                    <input type="number" name="items[{{ $i }}][quantity]" value="{{ $item['quantity'] ?? 1 }}" min="1" class="form-control form-control-sm" required>
                                </div>
                                <div class="col-3 col-md-2">
                                    <label class="form-label small">Unit Price</label>
                                    <input type="number" name="items[{{ $i }}][unit_price]" value="{{ $item['unit_price'] ?? '' }}" step="0.01" min="0" class="form-control form-control-sm" required>
                                </div>
                                <div class="col-4 col-md-2">
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-sale-item"><i class="bi bi-trash"></i></button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="row g-2 align-items-end mb-2 sale-item-row">
                            <div class="col-12 col-md-4">
                                <label class="form-label small">Product</label>
                                <select name="items[0][product_id]" class="form-select form-select-sm sale-product-select" required>
                                    <option value="">Select</option>
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}" data-price="{{ $p->price }}">{{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-3 col-md-2">
                                <label class="form-label small">Qty</label>
                                <input type="number" name="items[0][quantity]" value="1" min="1" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-3 col-md-2">
                                <label class="form-label small">Unit Price</label>
                                <input type="number" name="items[0][unit_price]" value="" step="0.01" min="0" class="form-control form-control-sm" required placeholder="0.00">
                            </div>
                            <div class="col-4 col-md-2">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-sale-item"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                    @endif
                </div>
                <button type="button" id="add-sale-item" class="btn btn-outline-primary btn-sm mt-2"><i class="bi bi-plus me-1"></i>Add line</button>
                @error('items')<div class="text-danger small mt-2">{{ $message }}</div>@enderror
            </div>
            <div class="card-footer bg-white border-0 p-4">
                <button type="submit" class="btn btn-primary">Create Sale</button>
                <a href="{{ route('admin.inventory.sales.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
(function() {
    const products = @json($products->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'price' => (float)$p->price])->values());
    let idx = document.querySelectorAll('.sale-item-row').length;

    document.getElementById('add-sale-item').addEventListener('click', function() {
        let opts = '<option value="">Select</option>';
        products.forEach(p => { opts += `<option value="${p.id}" data-price="${p.price}">${p.name}</option>`; });
        const row = document.createElement('div');
        row.className = 'row g-2 align-items-end mb-2 sale-item-row';
        row.innerHTML = `
            <div class="col-12 col-md-4">
                <label class="form-label small">Product</label>
                <select name="items[${idx}][product_id]" class="form-select form-select-sm sale-product-select" required>${opts}</select>
            </div>
            <div class="col-3 col-md-2">
                <label class="form-label small">Qty</label>
                <input type="number" name="items[${idx}][quantity]" value="1" min="1" class="form-control form-control-sm" required>
            </div>
            <div class="col-3 col-md-2">
                <label class="form-label small">Unit Price</label>
                <input type="number" name="items[${idx}][unit_price]" step="0.01" min="0" class="form-control form-control-sm" required>
            </div>
            <div class="col-4 col-md-2">
                <button type="button" class="btn btn-outline-danger btn-sm remove-sale-item"><i class="bi bi-trash"></i></button>
            </div>
        `;
        document.getElementById('sale-items').appendChild(row);
        idx++;
    });

    document.getElementById('sale-items').addEventListener('change', function(e) {
        const sel = e.target.closest('.sale-product-select');
        if (sel) {
            const opt = sel.selectedOptions[0];
            const price = opt ? opt.dataset.price : '';
            const row = sel.closest('.sale-item-row');
            const priceInput = row.querySelector('input[name*="[unit_price]"]');
            if (priceInput && price && !priceInput.value) priceInput.value = price;
        }
    });

    document.getElementById('sale-items').addEventListener('click', function(e) {
        if (e.target.closest('.remove-sale-item')) {
            if (document.querySelectorAll('.sale-item-row').length > 1)
                e.target.closest('.sale-item-row').remove();
        }
    });
})();
</script>
@endpush
@endsection
