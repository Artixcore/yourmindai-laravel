<?php

namespace App\Http\Controllers;

use App\Http\Requests\PublicStoreOrderRequest;
use App\Models\InvoiceSetting;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    private function getCart(): array
    {
        return session('cart', []);
    }

    private function setCart(array $cart): void
    {
        session(['cart' => $cart]);
    }

    private function getCartProducts(): \Illuminate\Support\Collection
    {
        $cart = $this->getCart();
        if (empty($cart)) {
            return collect();
        }
        $products = Product::active()
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');
        return collect($cart)->map(function ($qty, $productId) use ($products) {
            $product = $products->get($productId);
            if (!$product) {
                return null;
            }
            $qty = max(1, (int) $qty);
            $available = $product->quantity ?? 0;
            $qty = min($qty, max(1, $available));
            return [
                'product' => $product,
                'quantity' => $qty,
                'unit_price' => $product->price,
                'total' => $product->price * $qty,
            ];
        })->filter();
    }

    public function products()
    {
        $products = Product::active()->orderBy('name')->get();
        return view('shop.products', compact('products'));
    }

    public function cart()
    {
        $items = $this->getCartProducts();
        $subtotal = $items->sum('total');
        $taxRate = (float) (InvoiceSetting::get()->tax_rate ?? 0);
        $tax = round($subtotal * ($taxRate / 100), 2);
        $total = $subtotal + $tax;
        return view('shop.cart', compact('items', 'subtotal', 'tax', 'total'));
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'nullable|integer|min:1|max:99',
        ]);
        $product = Product::active()->findOrFail($request->product_id);
        $qty = (int) ($request->quantity ?? 1);
        $available = $product->quantity ?? 0;
        $qty = min($qty, max(1, $available));
        $cart = $this->getCart();
        $cart[$product->id] = ($cart[$product->id] ?? 0) + $qty;
        $this->setCart($cart);
        return redirect()->route('shop.cart')->with('success', 'Item added to cart.');
    }

    public function updateCart(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:0',
        ]);
        $cart = [];
        foreach ($request->items as $row) {
            $product = Product::active()->find($row['product_id']);
            if (!$product) {
                continue;
            }
            $qty = (int) $row['quantity'];
            if ($qty <= 0) {
                continue;
            }
            $available = $product->quantity ?? 0;
            $qty = min($qty, max(1, $available));
            $cart[$product->id] = $qty;
        }
        $this->setCart($cart);
        return redirect()->route('shop.cart')->with('success', 'Cart updated.');
    }

    public function removeFromCart(Product $product)
    {
        $cart = $this->getCart();
        unset($cart[$product->id]);
        $this->setCart($cart);
        return redirect()->route('shop.cart')->with('success', 'Item removed from cart.');
    }

    public function checkout()
    {
        $items = $this->getCartProducts();
        if ($items->isEmpty()) {
            return redirect()->route('shop.cart')->with('warning', 'Your cart is empty. Add products before checkout.');
        }
        $subtotal = $items->sum('total');
        $taxRate = (float) (InvoiceSetting::get()->tax_rate ?? 0);
        $tax = round($subtotal * ($taxRate / 100), 2);
        $total = $subtotal + $tax;
        $cities = config('bangladesh_cities.cities', []);
        return view('shop.checkout', compact('items', 'subtotal', 'tax', 'total', 'cities'));
    }

    public function storeOrder(PublicStoreOrderRequest $request)
    {
        $items = $this->getCartProducts();
        if ($items->isEmpty()) {
            return redirect()->route('shop.cart')->with('error', 'Your cart is empty. Add products before placing an order.');
        }

        $order = DB::transaction(function () use ($request, $items) {
            $subtotal = $items->sum('total');
            $taxRate = (float) (InvoiceSetting::get()->tax_rate ?? 0);
            $tax = round($subtotal * ($taxRate / 100), 2);
            $total = $subtotal + $tax;

            $order = Order::create([
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email ?? '',
                'customer_phone' => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'customer_city' => $request->customer_city,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'created_by' => null,
            ]);

            foreach ($items as $item) {
                $order->orderItems()->create([
                    'product_id' => $item['product']->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['total'],
                ]);
            }

            return $order;
        });

        $this->setCart([]);

        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewOrderNotification($order));
        }

        return redirect()->route('shop.order-confirmed', ['order_number' => $order->order_number])
            ->with('success', 'Your order has been placed successfully.');
    }

    public function orderConfirmed(Request $request)
    {
        $orderNumber = $request->query('order_number');
        return view('shop.order-confirmed', compact('orderNumber'));
    }
}
