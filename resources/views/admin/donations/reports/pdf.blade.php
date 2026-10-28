
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>

    <style>
        @page {
            margin: 22px;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 9px;
            color: #222;
        }

        h1 {
            color: #00096A;
            font-size: 19px;
            margin-bottom: 5px;
        }

        .meta {
            color: #555;
            margin-bottom: 14px;
            line-height: 1.6;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #00096A;
            color: #fff;
            text-align: left;
            padding: 6px 4px;
        }

        td {
            border: 1px solid #ddd;
            padding: 5px 4px;
            vertical-align: top;
        }

        tr:nth-child(even) td {
            background: #f5f6fa;
        }

        .footer {
            margin-top: 12px;
            color: #666;
            font-size: 8px;
        }
    </style>
</head>
<body>

    <h1>{{ $title }}</h1>

    <div class="meta">
        Generated: {{ $generatedAt->format('d M Y H:i') }}<br>

        @if(!empty($filters['date_from']))
            From: {{ $filters['date_from'] }}
        @endif

        @if(!empty($filters['date_to']))
            &nbsp; To: {{ $filters['date_to'] }}
        @endif

        @if(!empty($filters['search']))
            <br>Search: {{ $filters['search'] }}
        @endif

        <br>Total records: {{ $rows->count() }}
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
                    <td colspan="{{ count($headings) }}">
                        No donation records match the selected filters.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Teule Kenya — Donations Report
    </div>

</body>
</html>