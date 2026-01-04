<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Barcode - {{ $variant->sku }}</title>
    <style>
        @media print {
            @page { margin: 0; }
            body { margin: 0; }
            .no-print { display: none; }
        }
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
            background-color: #f3f4f6;
        }
        .label-container {
            background-color: white;
            padding: 20px;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            max-width: 300px;
            margin-bottom: 20px;
        }
        .product-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 250px;
        }
        .variant-info {
            font-size: 12px;
            color: #6b7280;
            margin-bottom: 10px;
        }
        .barcode-wrapper {
            margin: 10px 0;
        }
        .sku-text {
            font-family: 'Courier New', Courier, monospace;
            font-weight: bold;
            font-size: 14px;
            letter-spacing: 1px;
            margin-top: 5px;
        }
        .price {
            font-size: 16px;
            font-weight: bold;
            margin-top: 5px;
        }
        .print-btn {
            background-color: #3b82f6;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: medium;
        }
        .print-btn:hover {
            background-color: #2563eb;
        }
    </style>
</head>
<body>

    <div class="label-container">
        <div class="product-name">{{ $variant->product->name }}</div>
        <div class="variant-info">{{ $variant->unit_value ? $variant->unit_value . ' ' : '' }}{{ $variant->unit->name }} ({{ $variant->unit->short_name }})</div>
        
        <div class="barcode-wrapper">
             {!! $barcode !!}
        </div>
        
        <div class="sku-text">{{ $variant->sku }}</div>

    </div>

    <button onclick="window.print()" class="print-btn no-print">Print Label</button>

</body>
</html>
