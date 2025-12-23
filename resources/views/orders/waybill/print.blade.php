<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Waybills Print</title>
    <style>
        @page {
            size: A4;
            margin: 0;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .page {
            width: 210mm;
            height: 297mm;
            display: grid;
            grid-template-columns: 1fr 1fr;
            grid-template-rows: 1fr 1fr;
            page-break-after: always;
        }
        .waybill {
            border: 1px dashed #ccc;
            padding: 10mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 18px;
        }
        .details {
            margin-bottom: 10px;
        }
        .details p {
            margin: 2px 0;
        }
        .barcode {
            text-align: center;
            margin: 10px 0;
            border: 1px solid #000;
            padding: 5px;
        }
        .products {
            font-size: 10px;
            border-top: 1px solid #ddd;
            margin-top: 5px;
            padding-top: 5px;
        }
        .footer {
            text-align: center;
            font-size: 10px;
            margin-top: auto;
        }
    </style>
</head>
<body>
    @php $chunks = $orders->chunk(4); @endphp
    @foreach($chunks as $chunk)
        <div class="page">
            @foreach($chunk as $order)
                <div class="waybill">
                    <div class="header">
                        <h1>Waybill</h1>
                        <p><strong>{{ $order->waybill_number }}</strong></p>
                    </div>
                    
                    <div class="details">
                        <p><strong>To:</strong> {{ $order->customer_name }}</p>
                        <p>{{ $order->customer_address }}</p>
                        <p><strong>City:</strong> {{ $order->city->name ?? 'N/A' }}</p>
                        <p><strong>Phone:</strong> {{ $order->customer_phone }}</p>
                    </div>

                    <div class="details">
                        <p><strong>Order #:</strong> {{ $order->order_number }}</p>
                        <p><strong>Date:</strong> {{ $order->created_at->format('Y-m-d') }}</p>
                        <p><strong>COD Amount:</strong> {{ $order->payment_method == 'cod' ? number_format($order->total_amount, 2) : 'PREPAID' }}</p>
                    </div>

                    <div class="barcode">
                        {{-- Barcode Placeholder or use a library generator --}}
                        <div style="height: 30px; background: #eee; line-height: 30px;">||| |||| || ||| ||||</div>
                        {{ $order->waybill_number }}
                    </div>

                    <div class="products">
                        <strong>Items:</strong>
                        @foreach($order->items as $item)
                            <div>{{ $item->quantity }}x {{ $item->product_name }}</div>
                        @endforeach
                    </div>
                    
                    <div class="footer">
                        Powered by ShoppyMax
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
    <script>
        window.print();
    </script>
</body>
</html>
