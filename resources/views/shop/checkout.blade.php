@extends('layouts.guest')

@section('title', 'Checkout - Your Mind Aid')

@section('content')
<section class="public-page-hero px-3 px-md-4 px-lg-5">
    <div class="container-fluid" style="max-width: 960px;">
        <div class="text-center mb-4" data-aos="fade-up">
            <h1 class="h1 public-section-title">Confirm Your Order</h1>
            <p class="public-section-lead text-stone-600 mx-auto mb-0">Enter your delivery details below</p>
        </div>

        <div class="row g-4">
            <div class="col-12 col-lg-7">
                <div class="card card-psychological shadow-sm">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 fw-semibold">Delivery Details</h5>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('shop.checkout.store') }}" method="POST" id="checkout-form">
                            @csrf
                            <div class="mb-4">
                                <label for="customer_name" class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                                <input type="text" name="customer_name" id="customer_name" class="form-control @error('customer_name') is-invalid @enderror" value="{{ old('customer_name') }}" required>
                                @error('customer_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="customer_phone" class="form-label fw-semibold">Phone Number <span class="text-danger">*</span></label>
                                <input type="text" name="customer_phone" id="customer_phone" class="form-control @error('customer_phone') is-invalid @enderror" value="{{ old('customer_phone') }}" required>
                                @error('customer_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="customer_email" class="form-label fw-semibold">Email <span class="text-muted small">(optional)</span></label>
                                <input type="email" name="customer_email" id="customer_email" class="form-control @error('customer_email') is-invalid @enderror" value="{{ old('customer_email') }}">
                                @error('customer_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="customer_address" class="form-label fw-semibold">Address <span class="text-danger">*</span></label>
                                <textarea name="customer_address" id="customer_address" rows="3" class="form-control @error('customer_address') is-invalid @enderror" required>{{ old('customer_address') }}</textarea>
                                @error('customer_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="mb-4">
                                <label for="customer_city" class="form-label fw-semibold">City <span class="text-danger">*</span></label>
                                <select name="customer_city" id="customer_city" class="form-select @error('customer_city') is-invalid @enderror" required>
                                    <option value="">Select your city</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city }}" {{ old('customer_city') === $city ? 'selected' : '' }}>{{ $city }}</option>
                                    @endforeach
                                </select>
                                @error('customer_city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-gradient-primary btn-lg w-100" id="checkout-submit">
                                <i class="bi bi-check2-circle me-2"></i>Place Order
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-5">
                <div class="card card-psychological shadow-sm shop-checkout-summary">
                    <div class="card-header bg-white border-0 py-3">
                        <h5 class="mb-0 fw-semibold text-psychological-primary">Order Summary</h5>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            @foreach($items as $item)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ $item['product']->name }} × {{ $item['quantity'] }}</span>
                                    <span>৳{{ number_format($item['total'], 2) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div class="card-footer bg-white border-0 py-3">
                        @if($tax > 0)
                            <div class="d-flex justify-content-between text-muted small mb-2">
                                <span>Subtotal</span>
                                <span>৳{{ number_format($subtotal, 2) }}</span>
                            </div>
                            <div class="d-flex justify-content-between text-muted small mb-2">
                                <span>Tax</span>
                                <span>৳{{ number_format($tax, 2) }}</span>
                            </div>
                        @endif
                        <div class="d-flex justify-content-between fw-bold fs-5 pt-2 border-top">
                            <span>Total</span>
                            <span>৳{{ number_format($total, 2) }}</span>
                        </div>
                    </div>
                </div>
                <a href="{{ route('shop.cart') }}" class="btn btn-outline-secondary mt-3 w-100"><i class="bi bi-arrow-left me-2"></i>Back to Cart</a>
            </div>
        </div>
    </div>
</section>
@if($errors->any())
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Please fix the errors',
                text: 'Check the form below and correct the highlighted fields.',
                confirmButtonColor: '#0d6efd'
            });
        }
    });
</script>
@endpush
@endif
@endsection
