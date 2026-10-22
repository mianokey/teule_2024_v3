
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>

    <style>
        @page {
            margin: 25px 22px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #222;
        }

        .header {
            border-bottom: 3px solid #EF4700;
            padding-bottom: 12px;
            margin-bottom: 15px;
        }

        .organization {
            font-size: 19px;
            font-weight: bold;
            color: #00096A;
        }

        .subtitle {
            color: #555;
            margin-top: 4px;
        }

        h2 {
            color: #00096A;
            font-size: 15px;
            margin: 15px 0 8px;
        }

        .meta {
            margin-bottom: 12px;
            line-height: 1.6;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
        }

        th {
            background-color: #00096A;
            color: #fff;
            font-weight: bold;
            text-align: left;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 5px 4px;
            vertical-align: top;
            word-wrap: break-word;
        }

        tr:nth-child(even) td {
            background-color: #f4f5f8;
        }

        .footer {
            margin-top: 15px;
            padding-top: 7px;
            border-top: 1px solid #ccc;
            color: #666;
            font-size: 8px;
        }
    </style>
</head>
<body>

    <div class="header">
        <div class="organization">TEULE KENYA</div>
        <div class="subtitle">Stores Management Reports</div>
    </div>

    <h2>{{ $title }}</h2>

    <div class="meta">
        <strong>Generated:</strong> {{ $generatedAt->format('d M Y H:i:s') }}<br>

        @if(!empty($filters['date_from']) || !empty($filters['date_to']))
            <strong>Date range:</strong>
            {{ $filters['date_from'] ?? 'Beginning' }}
            to
            {{ $filters['date_to'] ?? 'Present' }}<br>
        @endif

        <strong>Records:</strong> {{ $rows->count() }}
    </div>

    <table>
        <thead>
            <tr>
                @foreach($headings as $heading)
                    <th>{{ $heading }}</th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            @forelse($rows as $row)
                <tr>
                    @foreach($row as $value)
                        <td>{{ $value ?? '—' }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headings) }}">No matching records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Teule Kenya · Confidential internal report ·
        Generated on {{ $generatedAt->format('d M Y H:i') }}
    </div>

</body>
</html>