@extends('layouts.guest')

@section('title', 'Order Confirmed - Your Mind Aid')

@section('content')
<section class="py-5 px-3 px-md-4 px-lg-5">
    <div class="container-fluid" style="max-width: 560px;">
        <div class="card card-psychological shadow-sm text-center">
            <div class="card-body p-5">
                <div class="mb-4">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                </div>
                <h1 class="h2 fw-bold text-psychological-primary mb-2">Thank You!</h1>
                <p class="text-stone-600 mb-4">Your order has been placed successfully.</p>
                @if($orderNumber ?? null)
                    <p class="fw-semibold mb-2">Order number: <span class="text-primary">{{ $orderNumber }}</span></p>
                @endif
                <p class="text-muted small mb-4">We will contact you shortly for delivery. Please keep your phone available.</p>
                <a href="{{ route('shop.products') }}" class="btn btn-gradient-primary">Continue Shopping</a>
                <a href="{{ route('landing') }}" class="btn btn-outline-secondary ms-2">Back to Home</a>
            </div>
        </div>
    </div>
</section>
@endsection
