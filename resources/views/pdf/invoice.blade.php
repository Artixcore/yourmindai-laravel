<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $type === 'order' ? $entity->order_number : $entity->sale_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; line-height: 1.4; color: #333; }
        .header { margin-bottom: 20px; }
        .company { font-size: 14px; font-weight: bold; margin-bottom: 4px; }
        .meta { color: #666; font-size: 10px; }
        table.items { width: 100%; border-collapse: collapse; margin: 16px 0; }
        table.items th, table.items td { border: 1px solid #ddd; padding: 6px 8px; text-align: left; }
        table.items th { background: #f5f5f5; font-size: 10px; }
        .text-right { text-align: right; }
        .totals { margin-top: 16px; width: 280px; margin-left: auto; }
        .totals row { display: block; }
        .totals .row { display: flex; justify-content: space-between; padding: 4px 0; }
        .totals .total { font-weight: bold; font-size: 12px; border-top: 2px solid #333; margin-top: 4px; padding-top: 6px; }
        .footer { margin-top: 32px; font-size: 10px; color: #666; }
        .signature-block { margin-top: 24px; }
        .signature-img { max-height: 50px; }
    </style>
</head>
<body>
    <div class="header">
        @if($invoiceSetting->logo_path && file_exists(storage_path('app/public/' . $invoiceSetting->logo_path)))
            @php $logoPath = 'file:///' . str_replace('\\', '/', storage_path('app/public/' . $invoiceSetting->logo_path)); @endphp
            <img src="{{ $logoPath }}" alt="Logo" style="max-height: 50px; margin-bottom: 8px;" />
        @endif
        @if($invoiceSetting->company_name)
            <div class="company">{{ $invoiceSetting->company_name }}</div>
        @endif
        @if($invoiceSetting->address)
            <div class="meta">{{ $invoiceSetting->address }}</div>
        @endif
        @if($invoiceSetting->phone || $invoiceSetting->email)
            <div class="meta">{{ $invoiceSetting->phone }} {{ $invoiceSetting->email }}</div>
        @endif
    </div>

    <h2 style="font-size: 14px;">Invoice {{ $type === 'order' ? $entity->order_number : $entity->sale_number }}</h2>

    <div class="meta" style="margin-bottom: 12px;">
        @if($type === 'order')
            Date: {{ $entity->created_at->format('M d, Y H:i') }}<br/>
        @else
            Date: {{ $entity->sale_date->format('M d, Y') }}<br/>
        @endif
        Customer: {{ $entity->customer_name }}<br/>
        @if($entity->customer_email)
            Email: {{ $entity->customer_email }}<br/>
        @endif
        @if($type === 'order' && $entity->customer_phone)
            Phone: {{ $entity->customer_phone }}
        @endif
    </div>

    <table class="items">
        <thead>
            <tr>
                <th>#</th>
                <th>Product</th>
                <th class="text-right">Qty</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @php
                $items = $type === 'order' ? $entity->orderItems : $entity->saleItems;
                $num = 1;
            @endphp
            @foreach($items as $item)
                <tr>
                    <td>{{ $num++ }}</td>
                    <td>{{ $item->product->name ?? 'N/A' }}</td>
                    <td class="text-right">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">{{ number_format($item->total, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <div class="row">
            <span>Subtotal</span>
            <span>{{ number_format($entity->subtotal, 2) }}</span>
        </div>
        @if($entity->tax > 0)
        <div class="row">
            <span>Tax</span>
            <span>{{ number_format($entity->tax, 2) }}</span>
        </div>
        @endif
        <div class="row total">
            <span>Total</span>
            <span>{{ number_format($entity->total, 2) }}</span>
        </div>
    </div>

    @if($invoiceSetting->signature_image_path && file_exists(storage_path('app/public/' . $invoiceSetting->signature_image_path)))
        @php $sigPath = 'file:///' . str_replace('\\', '/', storage_path('app/public/' . $invoiceSetting->signature_image_path)); @endphp
        <div class="signature-block">
            <img src="{{ $sigPath }}" alt="Signature" class="signature-img" />
        </div>
    @endif

    @if($invoiceSetting->footer_text)
        <div class="footer" style="margin-top: 24px;">{{ $invoiceSetting->footer_text }}</div>
    @endif

    <div class="meta" style="margin-top: 16px;">
        Generated on {{ now()->format('M d, Y H:i') }}
    </div>
</body>
</html>
