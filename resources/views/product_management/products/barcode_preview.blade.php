<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Barcode - {{ $variant->sku }}</title>
    <style>
        :root {
            --label-width: 34mm;
            --label-height: 25mm;
        }

        @page {
            size: 34mm 25mm;
            margin: 0;
        }

        * {
            box-sizing: border-box;
        }

        @media print {
            html,
            body {
                width: var(--label-width);
                height: var(--label-height);
                margin: 0;
                padding: 0;
                background: #fff;
                overflow: hidden;
            }

            body {
                display: block;
            }

            .screen-shell {
                min-height: auto;
                display: block;
                padding: 0;
                margin: 0;
                background: #fff;
            }

            .screen-shell > div,
            .label-sheet {
                width: var(--label-width);
                margin: 0;
                padding: 0;
            }

            .label {
                border: none;
                box-shadow: none;
                page-break-inside: avoid;
                page-break-after: avoid;
            }

            .toolbar {
                display: none;
            }
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #eef2f7;
            color: #111827;
        }

        .screen-shell {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .label-sheet {
            width: var(--label-width);
            padding: 0;
        }

        .label {
            width: var(--label-width);
            height: var(--label-height);
            background: #fff;
            border: 0.2mm solid #d1d5db;
            overflow: hidden;
            padding: 1.3mm 1.5mm 1mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            text-align: center;
            box-shadow: 0 4px 10px rgba(15, 23, 42, 0.08);
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
            font-size: 6.5px;
            line-height: 1.1;
            color: #4b5563;
            min-height: 2.6mm;
        }

        .barcode-wrapper {
            margin: 0.8mm 0 0.4mm;
            height: 9.5mm;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .barcode-image {
            width: 100%;
            max-height: 9.5mm;
            object-fit: contain;
        }

        .sku-text {
            font-family: "Courier New", monospace;
            font-size: 7px;
            font-weight: 700;
            letter-spacing: 0.4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .toolbar {
            margin-top: 18px;
            display: flex;
            justify-content: center;
        }

        .print-btn {
            border: 0;
            border-radius: 999px;
            background: #1d4ed8;
            color: #fff;
            font-size: 13px;
            font-weight: 600;
            padding: 10px 16px;
            cursor: pointer;
        }

        .print-btn:hover {
            background: #1e40af;
        }
    </style>
</head>
<body>
    <div class="screen-shell">
        <div>
            <div class="label-sheet">
                <div class="label">
                    <div>
                        <div class="product-name">{{ $variant->product->name }}</div>
                        <div class="variant-info">
                            {{ $variant->unit_value ? $variant->unit_value . ' ' : '' }}{{ $variant->unit->name }}{{ $variant->unit->short_name ? ' (' . $variant->unit->short_name . ')' : '' }}
                        </div>
                    </div>

                    <div class="barcode-wrapper">
                        <img class="barcode-image" src="data:image/png;base64,{{ $barcode }}" alt="Barcode for {{ $variant->sku }}">
                    </div>

                    <div class="sku-text">{{ $variant->sku }}</div>
                </div>
            </div>

            <div class="toolbar">
                <button onclick="window.print()" class="print-btn">Print Label</button>
            </div>
        </div>
    </div>
</body>
</html>
