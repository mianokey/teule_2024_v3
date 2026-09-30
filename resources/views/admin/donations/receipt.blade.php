<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <title>
        Donation Receipt - {{ $donation->donation_number }}
    </title>

    <style>

        /*
        |--------------------------------------------------------------------------
        | PAGE
        |--------------------------------------------------------------------------
        */

        @page {
            margin: 20px;
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #252525;
            line-height: 1.3;
        }

        table {
            border-collapse: collapse;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .muted {
            color: #777;
        }


        /*
        |--------------------------------------------------------------------------
        | RECEIPT INNER PAGE MARGINS
        |--------------------------------------------------------------------------
        | This is the actual left/right breathing room for the receipt.
        | Do NOT use width: 100% here.
        |--------------------------------------------------------------------------
        */

        .receipt-container {
            margin-left: 35px;
            margin-right: 35px;
        }


        /*
        |--------------------------------------------------------------------------
        | SECURITY BACKGROUND
        |--------------------------------------------------------------------------
        */

        .security-background {
            position: fixed;
            top: 170px;
            left: -35px;
            width: 850px;
            height: 500px;
            z-index: -10;
            opacity: 0.045;
            color: #00096A;
            font-size: 18px;
            font-weight: bold;
            line-height: 75px;
            transform: rotate(-28deg);
            white-space: nowrap;
        }

        .security-background div {
            word-spacing: 18px;
        }


        /*
        |--------------------------------------------------------------------------
        | HEADER
        |--------------------------------------------------------------------------
        */

        .header {
            width: 100%;
            border-bottom: 3px solid #00096A;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }

        .header-table {
            width: 100%;
        }

        .header-left {
            width: 62%;
            vertical-align: middle;
        }

        .header-right {
            width: 38%;
            vertical-align: middle;
            text-align: right;
        }

        .organization-name {
            font-size: 22px;
            font-weight: bold;
            color: #00096A;
            letter-spacing: 0.4px;
        }

        .organization-subtitle {
            font-size: 9px;
            color: #666;
            margin-top: 1px;
        }

        .receipt-title {
            font-size: 17px;
            font-weight: bold;
            color: #00096A;
            letter-spacing: 0.5px;
        }

        .receipt-number {
            font-size: 9px;
            color: #555;
            margin-top: 3px;
        }

        .receipt-number strong {
            color: #00096A;
        }


        /*
        |--------------------------------------------------------------------------
        | QUICK SUMMARY STRIP
        |--------------------------------------------------------------------------
        */

        .summary-strip {
            width: 100%;
            margin-bottom: 10px;
            border: 1px solid #d7d9e3;
            background: #f7f8fb;
        }

        .summary-strip td {
            padding: 7px 9px;
            vertical-align: middle;
            border-right: 1px solid #d7d9e3;
        }

        .summary-strip td:last-child {
            border-right: none;
        }

        .summary-label {
            display: block;
            color: #777;
            font-size: 7.5px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 2px;
        }

        .summary-value {
            display: block;
            color: #222;
            font-size: 9.5px;
            font-weight: bold;
        }


        /*
        |--------------------------------------------------------------------------
        | SECTIONS
        |--------------------------------------------------------------------------
        */

        .section {
            margin-bottom: 8px;
            page-break-inside: avoid;
        }

        .section-title {
            background: #00096A;
            color: #fff;
            padding: 5px 8px;
            font-size: 8.5px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .section-body {
            border: 1px solid #d9dce5;
            border-top: none;
            padding: 7px 8px;
            background: #ffffff;
        }


        /*
        |--------------------------------------------------------------------------
        | DONOR
        |--------------------------------------------------------------------------
        */

        .donor-compact {
            width: 100%;
        }

        .donor-name {
            width: 45%;
            font-size: 12px;
            font-weight: bold;
            color: #00096A;
        }

        .donor-contact {
            width: 55%;
            text-align: right;
            font-size: 9px;
            color: #555;
        }

        .contact-divider {
            color: #bbb;
            padding: 0 5px;
        }


        /*
        |--------------------------------------------------------------------------
        | DETAILS
        |--------------------------------------------------------------------------
        */

        .details-table {
            width: 100%;
        }

        .details-table td {
            padding: 4px 6px;
            border-bottom: 1px solid #eeeeee;
            vertical-align: top;
        }

        .details-table tr:last-child td {
            border-bottom: none;
        }

        .label {
            width: 25%;
            color: #777;
            font-size: 8.5px;
            font-weight: bold;
        }

        .value {
            width: 25%;
            font-size: 9px;
        }


        /*
        |--------------------------------------------------------------------------
        | CASH AMOUNT
        |--------------------------------------------------------------------------
        */

        .amount-box {
            width: 100%;
            border: 2px solid #00096A;
            background: #fafbff;
            padding: 9px 12px;
            text-align: center;
        }

        .amount-label {
            font-size: 7.5px;
            text-transform: uppercase;
            color: #777;
            letter-spacing: 0.5px;
        }

        .amount-value {
            font-size: 22px;
            font-weight: bold;
            color: #00096A;
            margin-top: 2px;
        }


        /*
        |--------------------------------------------------------------------------
        | IN-KIND ITEMS
        |--------------------------------------------------------------------------
        */

        .items-table {
            width: 100%;
        }

        .items-table thead {
            display: table-header-group;
        }

        .items-table th {
            background: #f0f2f7;
            color: #333;
            border: 1px solid #d4d7df;
            padding: 5px 6px;
            font-size: 8px;
            font-weight: bold;
            text-align: left;
        }

        .items-table td {
            border: 1px solid #e0e2e7;
            padding: 4px 6px;
            font-size: 8.5px;
            vertical-align: middle;
        }

        .items-table .amount {
            text-align: right;
            white-space: nowrap;
        }

        .items-table tbody tr {
            page-break-inside: avoid;
        }

        .total-row td {
            background: #f7f8fb;
            font-weight: bold;
        }


        /*
        |--------------------------------------------------------------------------
        | DESCRIPTION / NOTES
        |--------------------------------------------------------------------------
        */

        .note {
            background: #fafafa;
            border-left: 3px solid #EF4700;
            padding: 6px 8px;
            font-size: 8.5px;
            page-break-inside: avoid;
        }


        /*
        |--------------------------------------------------------------------------
        | ACKNOWLEDGEMENT
        |--------------------------------------------------------------------------
        */

        .acknowledgement {
            margin-top: 9px;
            padding: 8px 10px;
            border: 1px solid #d7d9e3;
            background: #f8f9fc;
            text-align: center;
            color: #555;
            font-size: 8.5px;
        }

        .acknowledgement strong {
            color: #00096A;
        }


        /*
        |--------------------------------------------------------------------------
        | SIGNATURE
        |--------------------------------------------------------------------------
        */

        .signature-table {
            width: 100%;
            margin-top: 12px;
        }

        .signature-table td {
            width: 50%;
            padding-right: 25px;
            vertical-align: bottom;
        }

        .signature-line {
            border-top: 1px solid #777;
            padding-top: 3px;
            font-size: 7.5px;
            color: #777;
        }


        /*
        |--------------------------------------------------------------------------
        | FOOTER
        |--------------------------------------------------------------------------
        */

        .footer {
            margin-top: 10px;
            padding-top: 7px;
            border-top: 1px solid #d8d8d8;
            font-size: 7.5px;
            color: #777;
        }

        .footer-table {
            width: 100%;
        }

        .footer-left {
            width: 55%;
        }

        .footer-right {
            width: 45%;
            text-align: right;
        }

        .footer-accent {
            color: #00096A;
            font-weight: bold;
        }


        /*
        |--------------------------------------------------------------------------
        | PRINT / PDF CONTROL
        |--------------------------------------------------------------------------
        */

        .avoid-break {
            page-break-inside: avoid;
        }

    </style>
</head>


<body>


    {{-- ============================================================
         SECURITY BACKGROUND
         ============================================================ --}}

    <div class="security-background">

        <div>
            TEULE KENYA • OFFICIAL DONATION RECEIPT • TEULE KENYA • OFFICIAL DONATION RECEIPT
        </div>

        <div>
            OFFICIAL DONATION RECEIPT • TEULE KENYA • OFFICIAL DONATION RECEIPT • TEULE KENYA
        </div>

        <div>
            TEULE KENYA • OFFICIAL DONATION RECEIPT • TEULE KENYA • OFFICIAL DONATION RECEIPT
        </div>

        <div>
            OFFICIAL DONATION RECEIPT • TEULE KENYA • OFFICIAL DONATION RECEIPT • TEULE KENYA
        </div>

        <div>
            TEULE KENYA • OFFICIAL DONATION RECEIPT • TEULE KENYA • OFFICIAL DONATION RECEIPT
        </div>

        <div>
            OFFICIAL DONATION RECEIPT • TEULE KENYA • OFFICIAL DONATION RECEIPT • TEULE KENYA
        </div>

    </div>


    {{-- ============================================================
         RECEIPT CONTAINER
         ============================================================ --}}

    <div class="receipt-container">


        {{-- ============================================================
             HEADER
             ============================================================ --}}

        <div class="header">

            <table class="header-table">

                <tr>

                    <td class="header-left">

                        <div class="organization-name">
                            TEULE KENYA
                        </div>

                        <div class="organization-subtitle">
                            Chombo Cha Upendo — Vessel of Love
                        </div>

                        <div class="organization-subtitle">
                            Loitokitok, Kajiado County, Kenya
                        </div>

                    </td>


                    <td class="header-right">

                        <div class="receipt-title">
                            DONATION RECEIPT
                        </div>

                        <div class="receipt-number">

                            Receipt No:

                            <strong>
                                {{ $donation->donation_number }}
                            </strong>

                        </div>

                    </td>

                </tr>

            </table>

        </div>


        {{-- ============================================================
             SUMMARY
             ============================================================ --}}

        <table class="summary-strip">

            <tr>

                <td style="width: 25%;">

                    <span class="summary-label">
                        Donation Date
                    </span>

                    <span class="summary-value">
                        {{ optional($donation->donation_date)->format('d M Y') }}
                    </span>

                </td>


                <td style="width: 25%;">

                    <span class="summary-label">
                        Donation Type
                    </span>

                    <span class="summary-value">

                        @if($donation->type === 'cash')

                            Cash

                        @elseif($donation->type === 'in_kind')

                            In-Kind

                        @else

                            {{ ucfirst(str_replace('_', ' ', $donation->type ?? 'Donation')) }}

                        @endif

                    </span>

                </td>


                <td style="width: 25%;">

                    <span class="summary-label">
                        Recorded
                    </span>

                    <span class="summary-value">
                        {{ optional($donation->created_at)->format('d M Y, H:i') }}
                    </span>

                </td>


                <td style="width: 25%;">

                    <span class="summary-label">
                        Reference
                    </span>

                    <span class="summary-value">
                        {{ $donation->donation_number }}
                    </span>

                </td>

            </tr>

        </table>


        {{-- ============================================================
             DONOR
             ============================================================ --}}

        @if($donation->donor)

            <div class="section">

                <div class="section-title">
                    Donor
                </div>

                <div class="section-body">

                    <table class="donor-compact">

                        <tr>

                            <td class="donor-name">

                                {{ $donation->donor->name ?? 'Anonymous Donor' }}

                            </td>


                            <td class="donor-contact">

                                @if($donation->donor->phone)

                                    {{ $donation->donor->phone }}

                                @endif


                                @if($donation->donor->phone && $donation->donor->email)

                                    <span class="contact-divider">
                                        |
                                    </span>

                                @endif


                                @if($donation->donor->email)

                                    {{ $donation->donor->email }}

                                @endif

                            </td>

                        </tr>

                    </table>

                </div>

            </div>

        @endif


        {{-- ============================================================
             DONATION DETAILS
             ============================================================ --}}

        <div class="section">

            <div class="section-title">
                Donation Details
            </div>

            <div class="section-body">

                <table class="details-table">

                    <tr>

                        <td class="label">
                            Purpose
                        </td>

                        <td class="value">
                            {{ $donation->purpose ?: 'General Support' }}
                        </td>


                        <td class="label">
                            Source
                        </td>

                        <td class="value">

                            {{ $donation->source
                                ? ucfirst(str_replace('_', ' ', $donation->source))
                                : '—'
                            }}

                        </td>

                    </tr>


                    @if($donation->reference || $donation->payment_reference)

                        <tr>

                            <td class="label">
                                Reference
                            </td>

                            <td class="value">
                                {{ $donation->reference ?: '—' }}
                            </td>


                            <td class="label">
                                Payment Ref.
                            </td>

                            <td class="value">
                                {{ $donation->payment_reference ?: '—' }}
                            </td>

                        </tr>

                    @endif

                </table>

            </div>

        </div>


        {{-- ============================================================
             CASH
             ============================================================ --}}

        @if($donation->type === 'cash')

            <div class="section">

                <div class="section-title">
                    Amount Received
                </div>

                <div class="section-body">

                    <div class="amount-box">

                        <div class="amount-label">
                            Donation Amount
                        </div>

                        <div class="amount-value">

                            {{ $donation->currency ?? 'KES' }}

                            {{ number_format((float) $donation->amount, 2) }}

                        </div>

                    </div>

                </div>

            </div>

        @endif


        {{-- ============================================================
             IN-KIND
             ============================================================ --}}

        @if($donation->type === 'in_kind')

            @php

                $totalEstimatedValue = $donation->items->sum(
                    fn ($item) => (float) ($item->estimated_value ?? 0)
                );

            @endphp


            <div class="section">

                <div class="section-title">
                    In-Kind Donation
                </div>

                <div class="section-body">

                    <table class="items-table">

                        <thead>

                            <tr>

                                <th style="width: 38%;">
                                    Item
                                </th>

                                <th style="width: 13%;">
                                    Qty
                                </th>

                                <th style="width: 13%;">
                                    Unit
                                </th>

                                <th style="width: 17%;">
                                    Condition
                                </th>

                                <th style="width: 19%;" class="amount">
                                    Est. Value
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @forelse($donation->items as $item)

                                <tr>

                                    <td>
                                        {{ $item->item ?? '—' }}
                                    </td>

                                    <td>
                                        {{ $item->quantity ?? '—' }}
                                    </td>

                                    <td>
                                        {{ $item->unit ?? '—' }}
                                    </td>

                                    <td>
                                        {{ $item->condition ?? '—' }}
                                    </td>

                                    <td class="amount">

                                        {{ $donation->currency ?? 'KES' }}

                                        {{ number_format((float) ($item->estimated_value ?? 0), 2) }}

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="5" class="text-center">
                                        No donation items recorded.
                                    </td>

                                </tr>

                            @endforelse


                            @if($donation->items->count())

                                <tr class="total-row">

                                    <td colspan="4" class="text-right">
                                        Total Estimated Value
                                    </td>

                                    <td class="amount">

                                        {{ $donation->currency ?? 'KES' }}

                                        {{ number_format($totalEstimatedValue, 2) }}

                                    </td>

                                </tr>

                            @endif

                        </tbody>

                    </table>

                </div>

            </div>

        @endif


        {{-- ============================================================
             DESCRIPTION / NOTES
             ============================================================ --}}

        @if($donation->description || $donation->notes)

            <div class="section">

                <div class="section-title">
                    Additional Information
                </div>

                <div class="section-body">

                    @if($donation->description)

                        <div class="note">

                            <strong>
                                Description:
                            </strong>

                            {!! nl2br(e($donation->description)) !!}

                        </div>

                    @endif


                    @if($donation->notes)

                        <div class="note" style="margin-top: 5px;">

                            <strong>
                                Notes:
                            </strong>

                            {!! nl2br(e($donation->notes)) !!}

                        </div>

                    @endif

                </div>

            </div>

        @endif


        {{-- ============================================================
             ACKNOWLEDGEMENT
             ============================================================ --}}

        <div class="acknowledgement">

            <strong>
                Thank you for supporting Teule Kenya.
            </strong>

            <br>

            This receipt acknowledges the donation recorded under

            <strong>
                {{ $donation->donation_number }}
            </strong>.

        </div>


        {{-- ============================================================
             SIGNATURE
             ============================================================ --}}

        <table class="signature-table">

            <tr>

                <td>

                    <div class="signature-line">
                        Authorized Representative
                    </div>

                </td>


                <td>

                    <div class="signature-line">
                        Date
                    </div>

                </td>

            </tr>

        </table>


        {{-- ============================================================
             FOOTER
             ============================================================ --}}

        <div class="footer">

            <table class="footer-table">

                <tr>

                    <td class="footer-left">

                        <span class="footer-accent">
                            TEULE KENYA
                        </span>

                        &nbsp;|&nbsp;

                        Chombo Cha Upendo — Vessel of Love

                    </td>


                    <td class="footer-right">

                        Generated:

                        {{ $generatedAt->format('d M Y, H:i:s') }}

                    </td>

                </tr>

            </table>

        </div>


    </div>

</body>

</html>
```
