<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Supplier List</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10pt;
        }
        h1 {
            text-align: center;
            font-size: 16pt;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #444;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .meta {
            margin-bottom: 20px;
            font-size: 9pt;
            color: #555;
            text-align: right;
        }
    </style>
</head>
<body>
    <h1>Supplier List</h1>
    <div class="meta">
        Generated on: {{ now()->format('Y-m-d H:i:s') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Business Name</th>
                <th>Mobile</th>
                <th>City</th>
                <th>District</th>
                <th>Province</th>
            </tr>
        </thead>
        <tbody>
            @foreach($suppliers as $supplier)
                <tr>
                    <td>{{ $supplier->name }}</td>
                    <td>{{ $supplier->business_name ?? '-' }}</td>
                    <td>{{ $supplier->mobile }}</td>
                    <td>{{ $supplier->city ?? '-' }}</td>
                    <td>{{ $supplier->district ?? '-' }}</td>
                    <td>{{ $supplier->province ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
