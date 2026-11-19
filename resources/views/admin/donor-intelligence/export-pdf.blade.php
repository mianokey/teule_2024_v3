<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Teule Kenya Donor Intelligence Report</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #263247;
        }

        h1, h2, h3 {
            color: #00096A;
        }

        h1 {
            font-size: 21px;
            margin-bottom: 4px;
        }

        h2 {
            font-size: 14px;
            margin-top: 22px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 6px;
        }

        .subtitle {
            color: #777;
            margin-bottom: 20px;
        }

        .summary {
            width: 100%;
            margin-bottom: 18px;
        }

        .summary td {
            border: 1px solid #ddd;
            padding: 10px;
            width: 25%;
        }

        .summary strong {
            display: block;
            font-size: 17px;
            color: #00096A;
            margin-top: 5px;
        }

        table.data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        table.data th {
            background: #00096A;
            color: white;
            padding: 8px;
            text-align: left;
        }

        table.data td {
            border-bottom: 1px solid #ddd;
            padding: 7px;
        }

        .muted {
            color: #777;
        }

        .footer {
            margin-top: 30px;
            border-top: 1px solid #ddd;
            padding-top: 8px;
            color: #777;
            font-size: 8px;
        }
    </style>
</head>
<body>

    <h1>TEULE KENYA</h1>
    <div class="subtitle">Donor Intelligence Report</div>

    <p>
        <strong>Generated:</strong> {{ now()->format('d M Y H:i') }}<br>
        <strong>Reporting period:</strong>
        {{ $filters['from'] ?: 'All dates' }}
        to
        {{ $filters['to'] ?: 'Present' }}
    </p>

    <table class="summary">
        <tr>
            <td>
                Registered donors
                <strong>{{ number_format($totalDonors) }}</strong>
            </td>
            <td>
                Donors with giving history
                <strong>{{ number_format($donorsWithGiving) }}</strong>
            </td>
            <td>
                Repeat donors
                <strong>{{ number_format($repeatDonors) }}</strong>
            </td>
            <td>
                Repeat donor rate
                <strong>{{ number_format($retentionRate, 1) }}%</strong>
            </td>
        </tr>
        <tr>
            <td>
                New donors
                <strong>{{ number_format($newDonors) }}</strong>
            </td>
            <td>
                Active donors
                <strong>{{ number_format($activeDonors) }}</strong>
            </td>
            <td>
                Lapsed donors
                <strong>{{ number_format($lapsedDonors) }}</strong>
            </td>
            <td>
                Donation records
                <strong>{{ number_format($totalDonations) }}</strong>
            </td>
        </tr>
    </table>

    <h2>Cash donation performance</h2>

    <table class="data">
        <thead>
            <tr>
                <th>Currency</th>
                <th>Donation count</th>
                <th>Total cash amount</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cashByCurrency as $cash)
                <tr>
                    <td>{{ $cash->currency }}</td>
                    <td>{{ number_format($cash->donation_count) }}</td>
                    <td>{{ number_format((float) $cash->total_amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="3">No cash donations recorded.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>In-kind donation performance</h2>

    <table class="data">
        <thead>
            <tr>
                <th>Currency</th>
                <th>Donation count</th>
                <th>Estimated value</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inKindByCurrency as $kind)
                <tr>
                    <td>{{ $kind->currency }}</td>
                    <td>{{ number_format($kind->donation_count) }}</td>
                    <td>{{ number_format((float) $kind->estimated_value, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="3">No in-kind donations recorded.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Top cash donors</h2>

    <table class="data">
        <thead>
            <tr>
                <th>Donor</th>
                <th>Currency</th>
                <th>Donation count</th>
                <th>Total giving</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topDonors as $donor)
                <tr>
                    <td>{{ $donor->name }}</td>
                    <td>{{ $donor->currency }}</td>
                    <td>{{ number_format($donor->donation_count) }}</td>
                    <td>{{ number_format((float) $donor->total_amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="4">No named cash donors recorded.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Communication performance</h2>

    <table class="data">
        <thead>
            <tr>
                <th>Channel</th>
                <th>Status</th>
                <th>Records</th>
            </tr>
        </thead>
        <tbody>
            @forelse($communications as $communication)
                <tr>
                    <td>{{ ucfirst($communication->channel) }}</td>
                    <td>{{ ucfirst($communication->status) }}</td>
                    <td>{{ number_format($communication->total) }}</td>
                </tr>
            @empty
                <tr><td colspan="3">No communication records.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Teule Kenya | Donor Intelligence Report | Confidential
    </div>

</body>
</html>