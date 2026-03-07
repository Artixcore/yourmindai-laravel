@extends('layouts.guest')

@section('title', 'Cart - Your Mind Aid')

@section('content')
<section class="py-5 px-3 px-md-4 px-lg-5">
    <div class="container-fluid" style="max-width: 960px;">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <h1 class="h2 fw-bold text-psychological-primary">Shopping Cart</h1>
            <a href="{{ route('shop.products') }}" class="btn btn-outline-primary"><i class="bi bi-arrow-left me-2"></i>Continue Shopping</a>
        </div>

        @if($items->isEmpty())
            <div class="card card-psychological shadow-sm">
                <div class="card-body p-5 text-center">
                    <i class="bi bi-cart-x display-4 text-muted mb-3"></i>
                    <p class="text-muted mb-4">Your cart is empty.</p>
                    <a href="{{ route('shop.products') }}" class="btn btn-gradient-primary">Browse Products</a>
                </div>
            </div>
        @else
            <form action="{{ route('shop.cart.update') }}" method="POST">
                @csrf
                <div class="card card-psychological shadow-sm mb-4">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="border-0 ps-4">Product</th>
                                        <th class="border-0 text-end">Unit Price</th>
                                        <th class="border-0 text-center">Quantity</th>
                                        <th class="border-0 text-end">Total</th>
                                        <th class="border-0 text-end pe-4">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($items as $item)
                                        <tr>
                                            <td class="ps-4">
                                                <strong>{{ $item['product']->name }}</strong>
                                            </td>
                                            <td class="text-end">৳{{ number_format($item['unit_price'], 2) }}</td>
                                            <td class="text-center">
                                                <input type="hidden" name="items[{{ $loop->index }}][product_id]" value="{{ $item['product']->id }}">
                                                <input type="number" name="items[{{ $loop->index }}][quantity]" value="{{ $item['quantity'] }}" min="1" max="{{ max(1, $item['product']->quantity ?? 99) }}" class="form-control form-control-sm text-center" style="width: 70px; display: inline-block;">
                                            </td>
                                            <td class="text-end">৳{{ number_format($item['total'], 2) }}</td>
                                            <td class="text-end pe-4">
                                                <form action="{{ route('shop.cart.remove', $item['product']) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this item from cart?');">
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
                    <div class="card-footer bg-white border-0 d-flex flex-wrap justify-content-between align-items-center gap-3 py-3 px-4">
                        <button type="submit" class="btn btn-outline-primary">Update Cart</button>
                        <div class="d-flex flex-column align-items-end">
                            @if($tax > 0)
                                <span class="text-muted small">Subtotal: ৳{{ number_format($subtotal, 2) }}</span>
                                <span class="text-muted small">Tax: ৳{{ number_format($tax, 2) }}</span>
                            @endif
                            <strong class="fs-5">Total: ৳{{ number_format($total, 2) }}</strong>
                        </div>
                    </div>
                </div>
            </form>
            <div class="d-flex justify-content-end">
                <a href="{{ route('shop.checkout') }}" class="btn btn-gradient-primary btn-lg">
                    <i class="bi bi-check2-circle me-2"></i>Proceed to Checkout
                </a>
            </div>
        @endif
    </div>
</section>
@endsection
