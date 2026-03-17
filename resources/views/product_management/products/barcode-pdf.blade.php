<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Barcodes</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 6mm;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            color: #111827;
        }

        .sheet {
            width: 100%;
        }

        .barcode-item {
            float: left;
            width: 34mm;
            height: 25mm;
            margin-right: 2mm;
            margin-bottom: 2mm;
            border: 0.2mm solid #d1d5db;
            padding: 1.3mm 1.5mm 1mm;
            text-align: center;
            box-sizing: border-box;
            overflow: hidden;
            page-break-inside: avoid;
        }

        .barcode-item:nth-child(5n) {
            margin-right: 0;
        }

        .barcode-item:nth-child(5n+1) {
            clear: left;
        }

        .product-name {
            font-size: 8px;
            font-weight: 700;
            line-height: 1.1;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .variant-info {
            margin-top: 0.6mm;
            min-height: 2.6mm;
            font-size: 6.5px;
            line-height: 1.1;
            color: #4b5563;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .barcode-wrap {
            margin: 0.8mm 0 0.4mm;
            height: 9.5mm;
        }

        .barcode-img {
            width: 100%;
            height: 9.5mm;
            object-fit: contain;
            display: block;
        }

        .sku {
            font-family: "Courier New", monospace;
            font-size: 7px;
            font-weight: 700;
            letter-spacing: 0.4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    </style>
</head>
<body>
    @php
        $generator = new \Picqer\Barcode\BarcodeGeneratorPNG();
    @endphp

    <div class="sheet">
        @foreach($variants as $variant)
            <div class="barcode-item">
                <div class="product-name">{{ $variant->product->name }}</div>
                <div class="variant-info">
                    {{ $variant->unit_value ? $variant->unit_value . ' ' : '' }}{{ $variant->unit->name }}{{ $variant->unit->short_name ? ' (' . $variant->unit->short_name . ')' : '' }}
                </div>

                <div class="barcode-wrap">
                    <img
                        class="barcode-img"
                        src="data:image/png;base64,{{ base64_encode($generator->getBarcode($variant->sku, $generator::TYPE_CODE_128)) }}"
                        alt="Barcode for {{ $variant->sku }}"
                    >
                </div>

                <div class="sku">{{ $variant->sku }}</div>
            </div>
        @endforeach
    </div>
</body>
</html>
