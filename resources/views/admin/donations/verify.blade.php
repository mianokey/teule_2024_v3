<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Donation Receipt | Teule Kenya</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            padding: 24px 14px;
            font-family: Arial, sans-serif;
            background: #f4f6fb;
            color: #252525;
            line-height: 1.5;
        }

        .receipt {
            width: 100%;
            max-width: 680px;
            margin: 0 auto;
            padding: 28px;
            background: #fff;
            border: 1px solid #e0e3ec;
            border-top: 5px solid #00096A;
            border-radius: 8px;
        }

        /* Keep the logo immediately beside the organization name. */
        .brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            margin: 0 auto;
            color: #00096A;
            font-size: 24px;
            font-weight: 800;
            letter-spacing: .4px;
        }

        .brand img {
            display: block;
            width: 36px;
            height: 36px;
            flex: 0 0 36px;
            object-fit: contain;
            margin: 0;
            padding: 0;
        }

        .subtitle {
            margin: 3px 0 24px;
            color: #6b7280;
            text-align: center;
            font-size: 12px;
            letter-spacing: .7px;
            text-transform: uppercase;
        }

        .receipt-heading {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            padding-bottom: 18px;
            border-bottom: 1px solid #e5e7eb;
        }

        h1 {
            margin: 0;
            color: #00096A;
            font-size: 23px;
        }

        .receipt-ref {
            margin-top: 5px;
            color: #555;
            font-size: 13px;
            overflow-wrap: anywhere;
        }

        .qr {
            flex: 0 0 112px;
            text-align: center;
        }

        .qr img {
            display: block;
            width: 100px;
            height: 100px;
            margin: 0 auto;
            object-fit: contain;
        }

        .qr p {
            margin: 4px 0 0;
            color: #6b7280;
            font-size: 10px;
        }

        .status {
            margin: 20px 0;
            padding: 12px 14px;
            border-radius: 5px;
            background: #edf7ef;
            color: #176534;
            font-size: 13px;
            font-weight: 700;
        }

        .intro {
            margin: 16px 0;
            color: #555;
            font-size: 14px;
        }

        .section-heading {
            margin: 24px 0 8px;
            color: #00096A;
            font-size: 16px;
        }

        .details {
            width: 100%;
            margin: 12px 0 20px;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .details td,
        .details th {
            padding: 10px 8px;
            border-bottom: 1px solid #e8eaf0;
            text-align: left;
            vertical-align: top;
            overflow-wrap: anywhere;
            font-size: 13px;
        }

        .details td:first-child {
            width: 38%;
            color: #555;
            font-weight: 700;
        }

        .details th {
            background: #f4f6fb;
            color: #00096A;
        }

        .field { margin: 18px 0; }

        label {
            display: block;
            margin-bottom: 7px;
            font-size: 13px;
            font-weight: 700;
        }

        input {
            display: block;
            width: 100%;
            padding: 12px;
            border: 1px solid #cdd2df;
            border-radius: 5px;
            font-size: 15px;
        }

        input:focus {
            outline: 2px solid #00096A;
            outline-offset: 1px;
        }

        button,
        .print-button {
            display: inline-block;
            width: 100%;
            padding: 12px;
            border: 0;
            border-radius: 5px;
            background: #00096A;
            color: #fff;
            text-align: center;
            text-decoration: none;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
        }

        button:hover,
        .print-button:hover { background: #101a83; }

        .error {
            margin-top: 6px;
            color: #b42318;
            font-size: 13px;
        }

        .notice {
            padding: 12px;
            border-left: 3px solid #00096A;
            background: #f4f6fb;
            color: #555;
            font-size: 12px;
        }

        .footer {
            margin-top: 26px;
            padding-top: 15px;
            border-top: 1px solid #e5e7eb;
            color: #777;
            text-align: center;
            font-size: 11px;
        }

        .footer a {
            color: #00096A;
            text-decoration: none;
        }

        @media (max-width: 480px) {
            .receipt { padding: 18px; }
            .brand { font-size: 21px; gap: 4px; }
            .brand img { width: 32px; height: 32px; flex-basis: 32px; }
            .receipt-heading { gap: 8px; }
            h1 { font-size: 20px; }
            .qr { flex-basis: 88px; }
            .qr img { width: 82px; height: 82px; }
            .details td, .details th { padding: 8px 5px; font-size: 12px; }
        }

        @media print {
            body { padding: 0; background: #fff; }
            .receipt {
                max-width: none;
                border: 0;
                border-top: 4px solid #00096A;
                border-radius: 0;
                box-shadow: none;
            }
            .no-print { display: none !important; }
            .status, .notice, .details th {
                print-color-adjust: exact;
                -webkit-print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>
<div class="receipt">

    <div class="brand">
        <img src="{{ asset('assets/img/logo.png') }}" alt="Teule Kenya">
        <span>TEULE KENYA</span>
    </div>

    <div class="subtitle">Chombo Cha Upendo · Official Donation Receipt</div>

    <div class="receipt-heading">
        <div>
            <h1>Donation Receipt</h1>
            <div class="receipt-ref">
                <strong>Receipt No:</strong><br>
                {{ $donation->donation_number }}
            </div>
            <div class="receipt-ref">
                <strong>Date:</strong>
                {{ $donation->donation_date
                    ? \Illuminate\Support\Carbon::parse($donation->donation_date)->format('d M Y')
                    : 'Not recorded' }}
            </div>
        </div>

        @if (!empty($verificationUrl))
            <div class="qr">
                <img
                    src="data:image/svg+xml;base64,{{ base64_encode(
                        \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
                            ->size(200)
                            ->margin(1)
                            ->generate($verificationUrl)
                    ) }}"
                    alt="Scan to verify donation receipt"
                >
                <p>Scan to verify receipt</p>
            </div>
        @endif
    </div>

    @if (!($verified ?? false))
        <div class="status">
            QR reference found. Verify the registered mobile number to view the receipt details.
        </div>

        <p class="intro">
            Enter the mobile phone number registered with this donation.
            The receipt details will only be displayed when the number matches
            Teule Kenya's donor record.
        </p>

        <form class="no-print"
              method="POST"
              action="{{ route('donations.verify.confirm', [
                  'encodedDonationNumber' => $encodedDonationNumber
              ]) }}">
            @csrf

            <div class="field">
                <label for="mobile">Donor's registered mobile number</label>
                <input
                    type="tel"
                    id="mobile"
                    name="mobile"
                    value="{{ old('mobile') }}"
                    required
                    maxlength="25"
                    autocomplete="tel"
                    inputmode="tel"
                    placeholder="Enter registered mobile number"
                >

                @error('mobile')
                    <div class="error">{{ $message }}</div>
                @enderror

                @if (session('verification_error'))
                    <div class="error">{{ session('verification_error') }}</div>
                @endif
            </div>

            <button type="submit">Verify and View Receipt</button>
        </form>

    @else
        <div class="status">
            &#10003; Receipt record found and registered mobile number matched.
        </div>

        <p class="intro">
            The receipt reference matches a donation record in Teule Kenya's
            system, and the entered mobile number matches the donor's registered
            contact details.
        </p>

        <h2 class="section-heading">Donation Details</h2>

        <table class="details">
            <tbody>
                <tr>
                    <td>Receipt number</td>
                    <td>{{ $donation->donation_number }}</td>
                </tr>
                <tr>
                    <td>Donor</td>
                    <td>{{ $donation->donor?->name ?? 'Not recorded' }}</td>
                </tr>
                <tr>
                    <td>Donation date</td>
                    <td>
                        {{ $donation->donation_date
                            ? \Illuminate\Support\Carbon::parse($donation->donation_date)->format('d M Y')
                            : 'Not recorded' }}
                    </td>
                </tr>
                <tr>
                    <td>Donation type</td>
                    <td>{{ ucfirst(str_replace('_', ' ', $donation->type ?? 'Donation')) }}</td>
                </tr>

                @if ($donation->type === 'cash')
                    <tr>
                        <td>Amount received</td>
                        <td>
                            {{ $donation->currency ?? 'KES' }}
                            {{ number_format((float) $donation->amount, 2) }}
                        </td>
                    </tr>
                @endif

                <tr>
                    <td>Purpose</td>
                    <td>{{ $donation->purpose ?: 'Not specified' }}</td>
                </tr>
            </tbody>
        </table>

        @if ($donation->type === 'in_kind' && $donation->items->isNotEmpty())
            <h2 class="section-heading">Goods Recorded as Received</h2>

            <table class="details">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Quantity recorded</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($donation->items as $item)
                        <tr>
                            <td>{{ $item->item }}</td>
                            <td>
                                {{ rtrim(rtrim(number_format((float) $item->quantity, 3, '.', ''), '0'), '.') }}
                                {{ $item->unit }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <div class="notice">
            <strong>Verification note:</strong>
            The details above match the donation record held by Teule Kenya,
            and the donor's registered mobile number has been matched.
            Physical delivery should be independently confirmed with Teule Kenya
            where required.
        </div>

        <button class="print-button no-print" type="button" onclick="window.print()" style="margin-top:16px;">
            Print Receipt
        </button>
    @endif

    <div class="footer">
        <strong>Teule Kenya · Chombo Cha Upendo</strong><br>
        Loitokitok, Kajiado County, Kenya<br>
        <a href="https://www.teulekenya.org">www.teulekenya.org</a><br>
        Official receipt verification
    </div>
</div>
</body>
</html>
