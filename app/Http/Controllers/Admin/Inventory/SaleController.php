<?php

namespace App\Http\Controllers\Admin\Inventory;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSaleRequest;
use App\Http\Requests\UpdateSaleRequest;
use App\Models\InvoiceSetting;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Services\InvoicePdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SaleController extends Controller
{
    public function __construct(
        protected InvoicePdfService $invoicePdfService
    ) {}

    public function index(Request $request)
    {
        $query = Sale::with('saleItems.product');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('sale_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%");
            });
        }

        $sales = $query->latest()->paginate(15);

        return view('admin.inventory.sales.index', compact('sales'));
    }

    public function create()
    {
        $products = Product::active()->orderBy('name')->get();
        return view('admin.inventory.sales.create', compact('products'));
    }

    public function store(StoreSaleRequest $request)
    {
        $sale = DB::transaction(function () use ($request) {
            $subtotal = 0;
            $items = [];

            foreach ($request->items as $row) {
                $qty = (int) $row['quantity'];
                $unitPrice = (float) $row['unit_price'];
                $total = round($unitPrice * $qty, 2);
                $subtotal += $total;
                $items[] = [
                    'product_id' => $row['product_id'],
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'total' => $total,
                ];
            }

            $taxRate = InvoiceSetting::get()->tax_rate ?? 0;
            $tax = round($subtotal * ($taxRate / 100), 2);
            $total = $subtotal + $tax;

            $sale = Sale::create([
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'sale_date' => $request->sale_date,
                'notes' => $request->notes,
                'status' => $request->status ?? 'completed',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'created_by' => auth()->id(),
            ]);

            foreach ($items as $item) {
                $sale->saleItems()->create($item);
            }

            return $sale;
        });

        return redirect()->route('admin.inventory.sales.show', $sale)
            ->with('success', 'Sale created successfully.');
    }

    public function show(Sale $sale)
    {
        $sale->load(['saleItems.product']);
        return view('admin.inventory.sales.show', compact('sale'));
    }

    public function edit(Sale $sale)
    {
        $sale->load(['saleItems.product']);
        $products = Product::active()->orderBy('name')->get();
        return view('admin.inventory.sales.edit', compact('sale', 'products'));
    }

    public function update(UpdateSaleRequest $request, Sale $sale)
    {
        DB::transaction(function () use ($request, $sale) {
            $sale->saleItems()->delete();

            $subtotal = 0;
            foreach ($request->items as $row) {
                $qty = (int) $row['quantity'];
                $unitPrice = (float) $row['unit_price'];
                $total = round($unitPrice * $qty, 2);
                $subtotal += $total;
                $sale->saleItems()->create([
                    'product_id' => $row['product_id'],
                    'quantity' => $qty,
                    'unit_price' => $unitPrice,
                    'total' => $total,
                ]);
            }

            $taxRate = InvoiceSetting::get()->tax_rate ?? 0;
            $tax = round($subtotal * ($taxRate / 100), 2);
            $total = $subtotal + $tax;

            $sale->update([
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'sale_date' => $request->sale_date,
                'notes' => $request->notes,
                'status' => $request->status ?? 'completed',
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'invoice_path' => null,
            ]);
        });

        return redirect()->route('admin.inventory.sales.show', $sale)
            ->with('success', 'Sale updated successfully.');
    }

    public function destroy(Sale $sale)
    {
        $sale->delete();
        return redirect()->route('admin.inventory.sales.index')
            ->with('success', 'Sale deleted successfully.');
    }

    public function downloadInvoice(Sale $sale)
    {
        $path = $this->invoicePdfService->getPathForSale($sale);
        $filename = 'invoice-sale-' . $sale->sale_number . '.pdf';
        return Storage::disk('public')->download($path, $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
