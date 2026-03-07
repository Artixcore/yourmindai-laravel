@extends('layouts.app')

@section('title', 'Edit Sale ' . $sale->sale_number)

@section('content')
<div class="container-fluid" style="max-width: 900px;">
    <div class="mb-4">
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('admin.dashboard')],
            ['label' => 'Inventory', 'url' => route('admin.inventory.products.index')],
            ['label' => 'Sales', 'url' => route('admin.inventory.sales.index')],
            ['label' => $sale->sale_number, 'url' => route('admin.inventory.sales.show', $sale)],
            ['label' => 'Edit']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">Edit Sale {{ $sale->sale_number }}</h1>
        <p class="text-muted mb-0">Update sale details</p>
    </div>

    <div class="card border-0 shadow-sm">
        <form action="{{ route('admin.inventory.sales.update', $sale) }}" method="POST" id="sale-edit-form">
            @csrf
            @method('PUT')
            <div class="card-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-4">
                        <label class="form-label">Customer Name <span class="text-danger">*</span></label>
                        <input type="text" name="customer_name" value="{{ old('customer_name', $sale->customer_name) }}" class="form-control" required>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Customer Email</label>
                        <input type="email" name="customer_email" value="{{ old('customer_email', $sale->customer_email) }}" class="form-control">
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Sale Date <span class="text-danger">*</span></label>
                        <input type="date" name="sale_date" value="{{ old('sale_date', $sale->sale_date->format('Y-m-d')) }}" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" rows="2" class="form-control">{{ old('notes', $sale->notes) }}</textarea>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="completed" {{ old('status', $sale->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="pending" {{ old('status', $sale->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="cancelled" {{ old('status', $sale->status) == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                </div>

                <h5 class="fw-semibold mb-3">Items</h5>
                <div id="sale-edit-items">
                    @foreach($sale->saleItems as $i => $item)
                        <div class="row g-2 align-items-end mb-2 sale-edit-item-row">
                            <div class="col-12 col-md-4">
                                <label class="form-label small">Product</label>
                                <select name="items[{{ $i }}][product_id]" class="form-select form-select-sm" required>
                                    @foreach($products as $p)
                                        <option value="{{ $p->id }}" data-price="{{ $p->price }}" {{ $item->product_id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-3 col-md-2">
                                <label class="form-label small">Qty</label>
                                <input type="number" name="items[{{ $i }}][quantity]" value="{{ $item->quantity }}" min="1" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-3 col-md-2">
                                <label class="form-label small">Unit Price</label>
                                <input type="number" name="items[{{ $i }}][unit_price]" value="{{ $item->unit_price }}" step="0.01" min="0" class="form-control form-control-sm" required>
                            </div>
                            <div class="col-4 col-md-2">
                                <button type="button" class="btn btn-outline-danger btn-sm remove-edit-item"><i class="bi bi-trash"></i></button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" id="add-edit-sale-item" class="btn btn-outline-primary btn-sm mt-2"><i class="bi bi-plus me-1"></i>Add line</button>
            </div>
            <div class="card-footer bg-white border-0 p-4">
                <button type="submit" class="btn btn-primary">Update Sale</button>
                <a href="{{ route('admin.inventory.sales.show', $sale) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
(function() {
    const products = @json($products->map(fn($p) => ['id' => $p->id, 'name' => $p->name, 'price' => (float)$p->price])->values());
    let idx = document.querySelectorAll('.sale-edit-item-row').length;

    document.getElementById('add-edit-sale-item').addEventListener('click', function() {
        let opts = '';
        products.forEach(p => { opts += `<option value="${p.id}" data-price="${p.price}">${p.name}</option>`; });
        const row = document.createElement('div');
        row.className = 'row g-2 align-items-end mb-2 sale-edit-item-row';
        row.innerHTML = `
            <div class="col-12 col-md-4">
                <label class="form-label small">Product</label>
                <select name="items[${idx}][product_id]" class="form-select form-select-sm" required>${opts}</select>
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
                <button type="button" class="btn btn-outline-danger btn-sm remove-edit-item"><i class="bi bi-trash"></i></button>
            </div>
        `;
        document.getElementById('sale-edit-items').appendChild(row);
        idx++;
    });

    document.getElementById('sale-edit-items').addEventListener('click', function(e) {
        if (e.target.closest('.remove-edit-item') && document.querySelectorAll('.sale-edit-item-row').length > 1)
            e.target.closest('.sale-edit-item-row').remove();
    });
})();
</script>
@endpush
@endsection
