<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>City List</title>
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
    <h1>City List</h1>
    <div class="meta">
        Generated on: {{ now()->format('Y-m-d H:i:s') }}
    </div>

    <table>
        <thead>
            <tr>
                <th>City Name</th>
                <th>Postal Code</th>
                <th>District</th>
                <th>Province</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cities as $city)
                <tr>
                    <td>{{ $city->city_name }}</td>
                    <td>{{ $city->postal_code }}</td>
                    <td>{{ $city->district }}</td>
                    <td>{{ $city->province }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
