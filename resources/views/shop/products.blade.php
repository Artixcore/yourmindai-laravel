@extends('layouts.guest')

@section('title', 'Products - Your Mind Aid')

@section('content')
<section class="public-page-hero px-3 px-md-4 px-lg-5">
    <div class="container-fluid">
        <div class="text-center mb-4" data-aos="fade-up">
            <h1 class="h1 public-section-title">Our Products</h1>
            <p class="public-section-lead text-stone-600 mx-auto mb-0">Browse and add items to your cart</p>
        </div>
        <div class="d-flex justify-content-end mb-4">
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
                    <p class="text-muted mb-4">No products available at the moment. Please check back later.</p>
                    <a href="{{ route('landing') }}" class="btn btn-gradient-outline">Back to Home</a>
                </div>
            </div>
        @else
            <div class="row g-4">
                @foreach($products as $product)
                    <div class="col-12 col-sm-6 col-lg-4" data-aos="fade-up">
                        <div class="card card-psychological shop-product-card h-100 shadow-sm">
                            @if($product->image_path)
                                <img src="{{ Storage::url($product->image_path) }}" class="card-img-top" alt="{{ $product->name }}">
                            @else
                                <div class="card-img-placeholder card-img-top">
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
