@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="mb-4">
    <x-breadcrumb :items="[
        ['label' => 'Home', 'url' => route('admin.dashboard')],
        ['label' => 'Inventory', 'url' => route('admin.inventory.products.index')],
        ['label' => 'Products', 'url' => route('admin.inventory.products.index')],
        ['label' => $product->name]
    ]" />
    <div class="d-flex justify-content-between align-items-start">
        <div>
            <h1 class="h3 mb-1 fw-semibold">{{ $product->name }}</h1>
            <p class="text-muted mb-0">{{ $product->sku ?? 'No SKU' }}</p>
        </div>
        <div>
            <a href="{{ route('admin.inventory.products.edit', $product) }}" class="btn btn-primary">Edit</a>
            <a href="{{ route('admin.inventory.products.index') }}" class="btn btn-outline-secondary">Back</a>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">
        @if($product->image_path)
            <img src="{{ Storage::url($product->image_path) }}" alt="" class="rounded mb-3" style="max-height: 120px;">
        @endif
        <p class="text-muted mb-2">{{ $product->description ?: 'No description.' }}</p>
        <dl class="row mb-0">
            <dt class="col-sm-3">Price</dt>
            <dd class="col-sm-9">{{ number_format($product->price, 2) }}</dd>
            <dt class="col-sm-3">Stock</dt>
            <dd class="col-sm-9">{{ $product->quantity }}</dd>
            <dt class="col-sm-3">Status</dt>
            <dd class="col-sm-9"><span class="badge bg-{{ $product->is_active ? 'success' : 'secondary' }}">{{ $product->is_active ? 'Active' : 'Inactive' }}</span></dd>
        </dl>
    </div>
</div>
@endsection
