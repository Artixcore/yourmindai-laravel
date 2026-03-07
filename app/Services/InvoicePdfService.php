<?php

namespace App\Services;

use App\Models\InvoiceSetting;
use App\Models\Order;
use App\Models\Sale;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoicePdfService
{
    public function generateForOrder(Order $order): string
    {
        $order->load(['orderItems.product']);
        $invoiceSetting = InvoiceSetting::get();
        $type = 'order';
        $entity = $order;

        $pdf = Pdf::loadView('pdf.invoice', compact('invoiceSetting', 'entity', 'type'));
        $pdf->setPaper('a4', 'portrait');

        $filename = 'invoice-order-' . $order->order_number . '-' . now()->format('Y-m-d') . '.pdf';
        $path = 'invoices/orders/' . $filename;

        $content = $pdf->output();
        Storage::disk('public')->put($path, $content);

        $order->update(['invoice_path' => $path]);

        return $path;
    }

    public function generateForSale(Sale $sale): string
    {
        $sale->load(['saleItems.product']);
        $invoiceSetting = InvoiceSetting::get();
        $type = 'sale';
        $entity = $sale;

        $pdf = Pdf::loadView('pdf.invoice', compact('invoiceSetting', 'entity', 'type'));
        $pdf->setPaper('a4', 'portrait');

        $filename = 'invoice-sale-' . $sale->sale_number . '-' . now()->format('Y-m-d') . '.pdf';
        $path = 'invoices/sales/' . $filename;

        $content = $pdf->output();
        Storage::disk('public')->put($path, $content);

        $sale->update(['invoice_path' => $path]);

        return $path;
    }

    public function getPathForOrder(Order $order): ?string
    {
        if (!$order->invoice_path || !Storage::disk('public')->exists($order->invoice_path)) {
            return $this->generateForOrder($order);
        }
        return $order->invoice_path;
    }

    public function getPathForSale(Sale $sale): ?string
    {
        if (!$sale->invoice_path || !Storage::disk('public')->exists($sale->invoice_path)) {
            return $this->generateForSale($sale);
        }
        return $sale->invoice_path;
    }
}
