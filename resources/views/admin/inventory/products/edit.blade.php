@extends('layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="container-fluid" style="max-width: 800px;">
    <div class="mb-4">
        <x-breadcrumb :items="[
            ['label' => 'Home', 'url' => route('admin.dashboard')],
            ['label' => 'Inventory', 'url' => route('admin.inventory.products.index')],
            ['label' => 'Products', 'url' => route('admin.inventory.products.index')],
            ['label' => $product->name, 'url' => route('admin.inventory.products.show', $product)],
            ['label' => 'Edit']
        ]" />
        <h1 class="h3 mb-1 fw-semibold">Edit Product</h1>
        <p class="text-muted mb-0">{{ $product->name }}</p>
    </div>

    <div class="card border-0 shadow-sm">
        <form action="{{ route('admin.inventory.products.update', $product) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="card-body p-4">
                <div class="row g-3 mb-4">
                    <div class="col-12 col-md-6">
                        <label class="form-label">Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $product->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-6">
                        <label class="form-label">SKU</label>
                        <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" class="form-control @error('sku') is-invalid @enderror">
                        @error('sku')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description', $product->description) }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Price <span class="text-danger">*</span></label>
                        <input type="number" name="price" value="{{ old('price', $product->price) }}" step="0.01" min="0" class="form-control @error('price') is-invalid @enderror" required>
                        @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Quantity (stock) <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" value="{{ old('quantity', $product->quantity) }}" min="0" class="form-control @error('quantity') is-invalid @enderror" required>
                        @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label">Image</label>
                        @if($product->image_path)
                            <div class="mb-2"><img src="{{ Storage::url($product->image_path) }}" alt="" class="rounded" style="max-height: 60px;"></div>
                        @endif
                        <input type="file" name="image" accept="image/*" class="form-control @error('image') is-invalid @enderror">
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12">
                        <div class="form-check">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">Active</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-white border-0 p-4">
                <button type="submit" class="btn btn-primary">Update Product</button>
                <a href="{{ route('admin.inventory.products.show', $product) }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
