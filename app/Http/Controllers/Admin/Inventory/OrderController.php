<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Notifications\NewOrderNotification;
use App\Services\InvoicePdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    public function __construct(
        protected InvoicePdfService $invoicePdfService
    ) {}

    public function index(Request $request)
    {
        $query = Order::with('orderItems.product');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        $orders = $query->latest()->paginate(15);

        return view('admin.inventory.orders.index', compact('orders'));
    }

    public function create()
    {
        $products = Product::active()->orderBy('name')->get();
        return view('admin.inventory.orders.create', compact('products'));
    }

    public function store(StoreOrderRequest $request)
    {
        $order = DB::transaction(function () use ($request) {
            $subtotal = 0;
            $items = [];

            foreach ($request->items as $row) {
                $product = Product::findOrFail($row['product_id']);
                $qty = (int) $row['quantity'];
                $unitPrice = $product->price;
                $total = $unitPrice * $qty;
                $subtotal += $total;
                $items[] = [
                    'product_id' => $product->id,
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'total' => $total,
                ];
            }

            $taxRate = \App\Models\InvoiceSetting::get()->tax_rate ?? 0;
            $tax = round($subtotal * ($taxRate / 100), 2);
            $total = $subtotal + $tax;

            $order = Order::create([
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'notes' => $request->notes,
                'status' => $request->status ?? 'pending',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'created_by' => auth()->id(),
            ]);

            foreach ($items as $item) {
                $order->orderItems()->create($item);
            }

            return $order;
        });

        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new NewOrderNotification($order));
        }

        return redirect()->route('admin.inventory.orders.show', $order)
            ->with('success', 'Order created successfully. Admins have been notified.');
    }

    public function show(Order $order)
    {
        $order->load(['orderItems.product']);
        return view('admin.inventory.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate(['status' => 'required|in:pending,confirmed,processing,shipped,cancelled']);
        $order->update(['status' => $request->status]);
        return back()->with('success', 'Order status updated.');
    }

    public function downloadInvoice(Order $order)
    {
        $path = $this->invoicePdfService->getPathForOrder($order);
        $filename = 'invoice-order-' . $order->order_number . '.pdf';
        return Storage::disk('public')->download($path, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
