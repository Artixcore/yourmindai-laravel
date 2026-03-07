@extends('layouts.guest')

@section('title', 'Products - Your Mind Aid')

@section('content')
<section class="py-5 px-3 px-md-4 px-lg-5">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div>
                <h1 class="h2 fw-bold text-psychological-primary">Our Products</h1>
                <p class="text-stone-600 mb-0">Browse and add items to your cart</p>
            </div>
            <a href="{{ route('shop.cart') }}" class="btn btn-gradient-primary">
                <i class="bi bi-cart3 me-2"></i>View Cart
                @if(count(session('cart', [])) > 0)
                    <span class="badge bg-white text-primary ms-1">{{ array_sum(session('cart', [])) }}</span>
                @endif
            </a>
        </div>

        @if($products->isEmpty())
            <div class="card card-psychological shadow-sm">
                <div class="card-body p-5 text-center">
                    <i class="bi bi-box-seam display-4 text-muted mb-3"></i>
                    <p class="text-muted mb-0">No products available at the moment. Please check back later.</p>
                </div>
            </div>
        @else
            <div class="row g-4">
                @foreach($products as $product)
                    <div class="col-12 col-sm-6 col-lg-4" data-aos="fade-up">
                        <div class="card card-psychological h-100 shadow-sm">
                            @if($product->image_path)
                                <img src="{{ Storage::url($product->image_path) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
                            @else
                                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                    <i class="bi bi-box-seam display-4 text-muted"></i>
                                </div>
                            @endif
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title fw-semibold text-psychological-primary">{{ $product->name }}</h5>
                                @if($product->description)
                                    <p class="card-text text-stone-600 small flex-grow-1">{{ Str::limit($product->description, 100) }}</p>
                                @endif
                                <p class="fw-bold text-primary mb-3">৳{{ number_format($product->price, 2) }}</p>
                                <form action="{{ route('shop.cart.add') }}" method="POST" class="mt-auto">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-gradient-primary w-100">
                                        <i class="bi bi-cart-plus me-2"></i>Add to Cart
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>
@endsection
