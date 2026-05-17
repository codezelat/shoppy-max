<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Receipt #{{ $payment->id }}</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            line-height: 1.6;
        }
        .container {
            width: 100%;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #eee;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            color: #333;
        }
        .company-info {
            font-size: 0.9em;
            color: #777;
        }
        .invoice-details {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .col {
            display: table-cell;
            width: 50%;
        }
        .receipt-info {
            text-align: right;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        .table th {
            background-color: #f8f9fa;
        }
        .total-section {
            text-align: right;
            margin-top: 20px;
        }
        .total-label {
            font-weight: bold;
            font-size: 1.2em;
        }
        .total-amount {
            font-size: 1.5em;
            color: #2c3e50;
            font-weight: bold;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 0.8em;
            color: #aaa;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        .status-stamp {
            border: 2px solid green;
            color: green;
            padding: 5px 10px;
            border-radius: 5px;
            display: inline-block;
            font-weight: bold;
            text-transform: uppercase;
        }
        .cancelled {
            border-color: red;
            color: red;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>PAYMENT VOUCHER</h1>
            <div class="company-info">
                ShoppyMax<br>
                123 Business Street, Colombo<br>
                Email: accounts@shoppymax.com
            </div>
        </div>

        <div class="invoice-details">
            <div class="col">
                <strong>Paid To:</strong><br>
                {{ $payment->reseller->name }}<br>
                {{ $payment->reseller->business_name }}<br>
                {{ $payment->reseller->address }}<br>
                {{ $payment->reseller->city }}
            </div>
            <div class="col receipt-info">
                <strong>Voucher #:</strong> {{ str_pad($payment->id, 6, '0', STR_PAD_LEFT) }}<br>
                <strong>Date:</strong> {{ $payment->payment_date->format('M d, Y') }}<br>
                <strong>Method:</strong> {{ ucfirst($payment->payment_method) }}<br>
                <strong>Reference:</strong> {{ $payment->reference_id ?? 'N/A' }}
            </div>
        </div>

        <div style="text-align: center; margin-bottom: 20px;">
            @if($payment->status == 'paid')
                <span class="status-stamp">PAID</span>
            @else
                <span class="status-stamp cancelled">CANCELLED</span>
            @endif
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th style="text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Payment Issued via {{ ucfirst($payment->payment_method) }}</td>
                    <td style="text-align: right;">Rs. {{ number_format($payment->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="total-section">
            <span class="total-label">Total Amount:</span>
            <span class="total-amount">Rs. {{ number_format($payment->amount, 2) }}</span>
        </div>

        <div class="footer">
            <p>Thank you for your business!</p>
            <p>This is a computer-generated voucher and requires no signature.</p>
        </div>
    </div>
</body>
</html>
