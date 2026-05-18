<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pick GRN {{ $order->pick_grn_number }}</title>
    <style>
        body { margin: 0; background: #f3f4f6; color: #111827; font-family: Arial, Helvetica, sans-serif; }
        .toolbar { max-width: 980px; margin: 16px auto 0; display: flex; justify-content: space-between; align-items: center; gap: 12px; }
        .toolbar a, .toolbar button { border: 0; border-radius: 6px; padding: 10px 14px; font-size: 12px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn-back { background: #e5e7eb; color: #111827; }
        .btn-print { background: #1d4ed8; color: #fff; }
        .paper { max-width: 980px; margin: 12px auto 24px; background: #fff; border: 1px solid #e5e7eb; border-radius: 10px; overflow: hidden; }
        .header { padding: 28px 32px; border-bottom: 1px solid #e5e7eb; display: flex; justify-content: space-between; gap: 24px; }
        h1 { margin: 0; font-size: 28px; font-weight: 800; }
        .meta { margin-top: 5px; font-size: 13px; color: #4b5563; }
        .company { text-align: right; font-size: 13px; color: #4b5563; line-height: 1.45; }
        .company strong { display: block; font-size: 18px; color: #111827; margin-bottom: 6px; }
        .sections { padding: 24px 32px 8px; display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; }
        .label { margin: 0 0 7px; font-size: 11px; letter-spacing: 0.05em; text-transform: uppercase; color: #6b7280; font-weight: 700; }
        .value { font-size: 14px; color: #374151; line-height: 1.5; }
        .value strong { color: #111827; font-size: 16px; }
        .table-wrap { padding: 12px 32px 28px; }
        table { width: 100%; border-collapse: collapse; }
        thead th { text-align: left; font-size: 11px; text-transform: uppercase; letter-spacing: 0.04em; color: #6b7280; background: #f9fafb; border-bottom: 1px solid #e5e7eb; padding: 10px 12px; }
        tbody td { border-bottom: 1px solid #f3f4f6; padding: 10px 12px; font-size: 13px; color: #111827; vertical-align: top; }
        .mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", monospace; }
        .muted { color: #6b7280; font-size: 12px; }
        .footer { border-top: 1px solid #e5e7eb; padding: 18px 32px 24px; font-size: 12px; color: #6b7280; }
        @media print {
            body { background: #fff; }
            .no-print { display: none !important; }
            .paper { margin: 0; max-width: 100%; border: 0; border-radius: 0; }
            @page { margin: 12mm; }
        }
    </style>
</head>
<body>
    <div class="toolbar no-print">
        <a href="{{ route('orders.packing.picking') }}" class="btn-back">Back To Picking</a>
        <button type="button" class="btn-print" onclick="window.print()">Print / Save PDF</button>
    </div>

    <main class="paper">
        <section class="header">
            <div>
                <h1>Pick GRN</h1>
                <div class="meta">Pick GRN No: <strong class="mono">{{ $order->pick_grn_number }}</strong></div>
                <div class="meta">Created: {{ optional($order->pick_grn_created_at)->format('d M Y h:i A') ?: '-' }}</div>
            </div>
            <div class="company">
                <strong>Shoppy Max</strong>
                Order: <span class="mono">{{ $order->order_number }}</span><br>
                Waybill: <span class="mono">{{ $order->waybill_number ?: '-' }}</span><br>
                Courier: {{ $order->courier?->name ?? '-' }}
            </div>
        </section>

        <section class="sections">
            <div>
                <p class="label">Customer</p>
                <div class="value">
                    <strong>{{ $order->customer_name ?: ($order->customer->name ?? '-') }}</strong><br>
                    {{ $order->customer_phone ?: ($order->customer->mobile ?? '-') }}
                </div>
            </div>
            <div>
                <p class="label">Address</p>
                <div class="value">{{ $order->customer_address ?: '-' }}</div>
            </div>
            <div>
                <p class="label">Picker</p>
                <div class="value">
                    {{ $order->pickGrnCreator?->name ?? '-' }}<br>
                    <span class="muted">Scan from Picking after this sheet is printed/saved.</span>
                </div>
            </div>
        </section>

        <section class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>SKU</th>
                        <th>Barcode</th>
                        <th>Pick Location</th>
                        <th>GRN Source</th>
                        <th>Check</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach(collect($packingSummary['items'] ?? []) as $item)
                        @foreach(collect($item['units'] ?? []) as $unit)
                            <tr>
                                <td>{{ $item['product_name'] ?? '-' }}</td>
                                <td class="mono">{{ $item['sku'] ?? '-' }}</td>
                                <td class="mono">{{ $unit['barcode_value'] ?? '-' }}</td>
                                <td>
                                    {{ $unit['store_label'] ?? 'Unassigned Store' }}<br>
                                    <span class="muted">{{ $unit['rack_label'] ?? 'Unassigned Rack' }}</span>
                                </td>
                                <td class="mono">{{ $unit['purchase_number'] ?? 'Legacy stock' }}</td>
                                <td>□</td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </section>

        <footer class="footer">
            Pick the exact labels listed here. Retail locations are prioritized before warehouse stock during allocation.
        </footer>
    </main>

    @if(request()->boolean('print'))
        <script>
            window.addEventListener('load', function () {
                window.setTimeout(function () {
                    window.print();
                }, 350);
            });
        </script>
    @endif
</body>
</html>
