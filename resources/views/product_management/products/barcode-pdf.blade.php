<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Barcodes</title>
    <style>
        body { font-family: sans-serif; }
        .container {
            width: 100%;
        }
        .barcode-item {
            float: left;
            width: 32%; /* 3 items per row with margin */
            margin-right: 1.3%;
            margin-bottom: 10px;
            border: 1px dashed #ccc;
            padding: 5px;
            text-align: center;
            height: 110px; /* Fixed height to force alignment */
            box-sizing: border-box;
            page-break-inside: avoid;
        }
        /* Clear every 3rd item to ensure new row starts cleanly */
        .barcode-item:nth-child(3n+1) {
            clear: left;
        }
        /* Remove right margin for the last item in a row */
        .barcode-item:nth-child(3n) {
            margin-right: 0;
        }

        .product-name {
            font-size: 10px;
            font-weight: bold;
            margin-bottom: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 100%;
        }
        .variant-info {
            font-size: 9px;
            margin-bottom: 3px;
        }
        .price {
            font-size: 10px;
            font-weight: bold;
            margin-top: 3px;
        }
        .barcode-img {
            height: 25px; /* Slightly smaller to fit */
            margin: 2px auto;
            display: block;
            width: 90%; /* prevent overflow */
        }
        .sku {
             font-size: 8px;
             letter-spacing: 1px;
        }
        @page { 
            size: A4 portrait;
            margin: 20px; 
        }
    </style>
</head>
<body>
    @php
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
    @endphp

    <div>
        @foreach($variants as $variant)
            <div class="barcode-item">
                <div class="product-name">{{ $variant->product->name }}</div>
                <div class="variant-info">
                    {{ $variant->unit_value }} {{ $variant->unit->short_name }}
                </div>
                
                <img class="barcode-img" src="data:image/png;base64,{{ base64_encode($generator->getBarcode($variant->sku, $generator::TYPE_CODE_128)) }}">
                
                <div class="sku">{{ $variant->sku }}</div>
    
            </div>
        @endforeach
    </div>
</body>
</html>
