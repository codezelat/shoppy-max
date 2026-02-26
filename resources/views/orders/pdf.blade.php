<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $order->order_number }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 14px;
            color: #333;
            line-height: 1.5;
        }
        .header {
            width: 100%;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .header table {
            width: 100%;
        }
        .company-info {
            text-align: right;
        }
        .company-name {
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }
        .invoice-title {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }
        .invoice-details {
            margin-top: 5px;
            color: #666;
            font-size: 12px;
        }
        .section {
            margin-bottom: 20px;
        }
        .columns {
            width: 100%;
            margin-bottom: 20px;
        }
        .columns td {
            vertical-align: top;
            width: 50%;
        }
        .heading {
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            color: #777;
            margin-bottom: 5px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 2px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            text-align: left;
            background-color: #f8f9fa;
            padding: 8px;
            border-bottom: 1px solid #ddd;
            font-size: 12px;
            text-transform: uppercase;
            color: #555;
        }
        .items-table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .totals {
            width: 100%;
        }
        .totals td {
            padding: 5px 8px;
        }
        .grand-total {
            font-size: 16px;
            font-weight: bold;
            background-color: #eee;
        }
        .footer {
            margin-top: 50px;
            border-top: 1px solid #eee;
            padding-top: 20px;
            font-size: 12px;
            color: #777;
            text-align: center;
        }
        .status-badge {
            background-color: #eee;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 10px;
            text-transform: uppercase;
            display: inline-block;
        }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td>
                    <div class="invoice-title">INVOICE</div>
                    <div class="invoice-details">Order #{{ $order->order_number }}</div>
                    <div class="invoice-details">Date: {{ $order->order_date->format('d M, Y') }}</div>
                    <div style="margin-top: 5px;">
                        <span class="status-badge">{{ $order->status }}</span>
                    </div>
                </td>
                <td class="company-info">
                    <div class="company-name">{{ config('app.name', 'ShoppyMax') }}</div>
                    <div>123 Business Road,<br>Colombo 03,<br>Sri Lanka</div>
                </td>
            </tr>
        </table>
    </div>

    <table class="columns">
        <tr>
            <td>
                <div class="heading">Bill To</div>
                <strong>{{ $order->customer->name ?? $order->customer_name }}</strong><br>
                {{ $order->customer->address ?? $order->customer_address }}<br>
                {{ $order->customer_city ?? $order->customer->city }}{{ $order->customer_district ? ', ' . $order->customer_district : '' }}{{ $order->customer_province ? ', ' . $order->customer_province : '' }}<br>
                Mobile: {{ $order->customer->mobile ?? $order->customer_phone }}
            </td>
            <td>
                @if($order->order_type === 'reseller' && $order->reseller)
                    <div class="heading">Reseller Info</div>
                    <strong>{{ $order->reseller->name }}</strong><br>
                    {{ $order->reseller->business_name }}<br>
                    Mobile: {{ $order->reseller->mobile }}<br>
                    Account: {{ $order->reseller->reseller_type === 'direct_reseller' ? 'Direct Reseller' : 'Reseller' }}
                @else
                    <div class="heading">Details</div>
                    Type: {{ ucfirst($order->order_type) }}<br>
                    Pay Method: {{ $order->payment_method === 'COD' ? 'Cash on Delivery (COD)' : $order->payment_method }}<br>
                    @if($order->courier)
                        Courier: {{ $order->courier->name }}<br>
                    @endif
                    Call Status: {{ ucfirst($order->call_status) }}<br>
                    Created By: {{ $order->user->name ?? 'System' }}
                @endif
            </td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th>Product</th>
                <th class="text-center">SKU</th>
                <th class="text-center">Qty</th>
                <th class="text-right">Price</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>{{ $item->product_name }}</td>
                    <td class="text-center">{{ $item->sku }}</td>
                    <td class="text-center">{{ $item->quantity }}</td>
                    <td class="text-right">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-right">{{ number_format($item->subtotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table style="width: 100%">
        <tr>
            <td style="width: 50%"></td>
            <td style="width: 50%">
                <table class="totals">
                    @if($order->order_type === 'reseller')
                        <tr>
                            <td class="text-right">Total Commission Paid:</td>
                            <td class="text-right">LKR {{ number_format($order->total_commission, 2) }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="text-right">Subtotal:</td>
                        <td class="text-right">LKR {{ number_format($order->total_amount - $order->courier_charge, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-right" style="border-bottom: 1px solid #ddd;">Courier Charge:</td>
                        <td class="text-right" style="border-bottom: 1px solid #ddd;">LKR {{ number_format($order->courier_charge, 2) }}</td>
                    </tr>
                    <tr class="grand-total">
                        <td class="text-right">Grand Total:</td>
                        <td class="text-right">LKR {{ number_format($order->total_amount, 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="footer">
        Thank you for your business!<br>
        Authorized Signature: __________________________
    </div>

</body>
</html>
