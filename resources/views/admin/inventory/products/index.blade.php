@extends('layouts.app')

@section('title', 'Products')

@section('content')
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('admin.dashboard')],
            ['label' => 'Inventory', 'url' => route('admin.inventory.products.index')],
            ['label' => 'Products']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">Products</h1>
        <p class="text-muted mb-0">Manage inventory products</p>
    </div>
    <a href="{{ route('admin.inventory.products.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Add Product
    </a>
</div>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('admin.inventory.products.index') }}" class="row g-3">
            <div class="col-12 col-md-5">
                <label class="form-label small text-muted">Search</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Name or SKU..." class="form-control form-control-sm">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label small text-muted">Status</label>
                <select name="status" class="form-select form-select-sm">
                    <option value="">All</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary btn-sm w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

@if($products->count() > 0)
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="border-0">Product</th>
                            <th class="border-0">SKU</th>
                            <th class="border-0">Price</th>
                            <th class="border-0">Stock</th>
                            <th class="border-0">Status</th>
                            <th class="border-0 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        @if($product->image_path)
                                            <img src="{{ Storage::url($product->image_path) }}" alt="" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="bi bi-box text-muted"></i>
                                            </div>
                                        @endif
                                        <span class="fw-semibold">{{ $product->name }}</span>
                                    </div>
                                </td>
                                <td>{{ $product->sku ?? '—' }}</td>
                                <td>{{ number_format($product->price, 2) }}</td>
                                <td>{{ $product->quantity }}</td>
                                <td>
                                    <span class="badge bg-{{ $product->is_active ? 'success' : 'secondary' }}">
                                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.inventory.products.show', $product) }}" class="btn btn-outline-primary btn-sm"><i class="bi bi-eye"></i></a>
                                    <a href="{{ route('admin.inventory.products.edit', $product) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('admin.inventory.products.destroy', $product) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this product?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-0">
            {{ $products->links() }}
        </div>
    </div>
@else
    <div class="card border-0 shadow-sm">
        <div class="card-body p-5 text-center">
            <i class="bi bi-box fs-1 text-muted mb-3"></i>
            <p class="text-muted mb-0">No products found.</p>
            <a href="{{ route('admin.inventory.products.create') }}" class="btn btn-primary mt-3">Add Product</a>
        </div>
    </div>
@endif
@endsection
