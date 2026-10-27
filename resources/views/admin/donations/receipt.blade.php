<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">

    <title>
        Donation Receipt - {{ $donation->donation_number ?? 'DON-' . $donation->id }}
    </title>

    <style>
        @page {
            size: A4 portrait;
            margin: 8mm 11mm 8mm 11mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            background: #ffffff;
            color: #222222;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9px;
            line-height: 1.25;
        }

        .receipt {
            width: 100%;
            margin: 0 auto;
            position: relative;
        }

        /* Security watermark */
        .security-pattern {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            opacity: 0.035;
            overflow: hidden;
        }

        .security-pattern span {
            position: absolute;
            font-size: 40px;
            font-weight: 700;
            color: #00096A;
            transform: rotate(-28deg);
            white-space: nowrap;
        }

        .security-pattern span:nth-child(1) {
            top: 12%;
            left: 5%;
        }

        .security-pattern span:nth-child(2) {
            top: 38%;
            left: 35%;
        }

        .security-pattern span:nth-child(3) {
            top: 65%;
            left: 5%;
        }

        .security-pattern span:nth-child(4) {
            top: 82%;
            left: 40%;
        }

        /* Header */
        .header {
            display: table;
            width: 100%;
            border-bottom: 2px solid #00096A;
            padding-bottom: 5px;
            margin-bottom: 6px;
        }

        .header-left,
        .header-right {
            display: table-cell;
            vertical-align: middle;
        }

        .header-left {
            width: 60%;
        }

        .header-right {
            width: 40%;
            text-align: right;
        }

        .logo {
            max-height: 48px;
            max-width: 155px;
        }

        .organisation-name {
            color: #00096A;
            font-size: 17px;
            font-weight: 700;
            margin-bottom: 1px;
        }

        .organisation-subtitle {
            color: #555555;
            font-size: 8px;
        }

        .document-title {
            color: #00096A;
            font-size: 15px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .document-number {
            font-size: 9px;
            color: #555555;
            margin-top: 2px;
        }

        /* Original four-column summary */
        .summary {
            display: table;
            width: 100%;
            margin-bottom: 6px;
            border: 1px solid #d8dce5;
            border-radius: 3px;
            page-break-inside: avoid;
        }

        .summary-item {
            display: table-cell;
            width: 25%;
            padding: 5px 7px;
            border-right: 1px solid #d8dce5;
            vertical-align: top;
        }

        .summary-item:last-child {
            border-right: none;
        }

        .summary-label {
            font-size: 7px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            color: #666666;
            margin-bottom: 2px;
        }

        .summary-value {
            font-size: 9px;
            font-weight: 700;
            color: #00096A;
            overflow-wrap: anywhere;
        }

        /* Sections */
        .section {
            margin-top: 6px;
            page-break-inside: avoid;
        }

        .section-title {
            color: #00096A;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            border-bottom: 1px solid #d8dce5;
            padding-bottom: 3px;
            margin-top: 0;
            margin-bottom: 4px;
        }

        /* Details */
        .details {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 3px;
        }

        .details td {
            padding: 2px 4px;
            vertical-align: top;
        }

        .details .label {
            width: 17%;
            color: #666666;
            font-size: 7.5px;
            text-transform: uppercase;
        }

        .details .value {
            width: 33%;
            font-weight: 600;
            overflow-wrap: anywhere;
        }

        /* Amount */
        .amount-panel {
            margin-top: 5px;
            padding: 6px 8px;
            border-left: 3px solid #00096A;
            background: #f2f4fa;
            page-break-inside: avoid;
        }

        .amount-label {
            color: #00096A;
            font-size: 8px;
            font-weight: 700;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .amount-value {
            color: #00096A;
            font-size: 16px;
            font-weight: 700;
        }

        .amount-note {
            color: #666666;
            font-size: 7px;
            margin-top: 2px;
        }

        /* In-kind items */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 3px;
        }

        .items-table th {
            background: #00096A;
            color: #ffffff;
            font-size: 7.5px;
            text-transform: uppercase;
            letter-spacing: 0.2px;
            padding: 4px 4px;
            border: 1px solid #00096A;
            text-align: left;
        }

        .items-table td {
            padding: 3px 4px;
            border: 1px solid #d8dce5;
            vertical-align: top;
            font-size: 8px;
        }

        .items-table tbody tr:nth-child(even) {
            background: #fafbfc;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .muted {
            color: #777777;
            font-weight: 400;
        }

        /* Notes */
        .notes {
            border: 1px solid #d8dce5;
            padding: 5px 7px;
            margin-top: 3px;
            background: #ffffff;
            min-height: 18px;
            overflow-wrap: anywhere;
        }

        /* Acknowledgement */
        .acknowledgement {
            margin-top: 6px;
            padding: 5px 7px;
            border: 1px solid #d8dce5;
            font-size: 8px;
            color: #555555;
            line-height: 1.3;
            page-break-inside: avoid;
        }

        .acknowledgement strong {
            color: #00096A;
        }

        /* Signatures */
        .signature-area {
            width: 100%;
            display: table;
            margin-top: 9px;
            page-break-inside: avoid;
        }

        .signature-column {
            display: table-cell;
            width: 50%;
            padding-right: 15px;
            vertical-align: bottom;
        }

        .signature-column:last-child {
            padding-right: 0;
            padding-left: 15px;
        }

        .signature-line {
            border-bottom: 1px solid #555555;
            height: 17px;
            margin-bottom: 3px;
        }

        .signature-label {
            font-size: 7px;
            color: #666666;
            text-transform: uppercase;
        }

        /* QR code */
        .qr-area {
            text-align: center;
            margin-top: 4px;
        }

        .qr-code {
            width: 48px;
            height: 48px;
        }

        .qr-caption {
            font-size: 7px;
            color: #666666;
            margin-top: 1px;
        }

        /* Footer */
        .footer {
            margin-top: 7px;
            padding-top: 4px;
            border-top: 1px solid #d8dce5;
            display: table;
            width: 100%;
            font-size: 7px;
            color: #777777;
            page-break-inside: avoid;
        }

        .footer-left,
        .footer-right {
            display: table-cell;
            vertical-align: top;
            width: 50%;
        }

        .footer-right {
            text-align: right;
        }

        .navy {
            color: #00096A;
        }

        .orange {
            color: #EF4700;
        }

        @media print {
            body {
                background: #ffffff;
            }

            .receipt {
                width: 100%;
            }
        }

        .details {
    width: 100%;
    table-layout: fixed;
    border-collapse: collapse;
    margin: 4px 0 6px;
    border: 1px solid #00096A;
}

.details td {
    box-sizing: border-box;
    padding: 5px 6px;
    vertical-align: middle;
    border: 1px solid #DCE1EE;
    line-height: 1.25;
    overflow-wrap: break-word;
}

.details .label {
    width: 18%;
    background-color: #E9ECF7;
    color: #00096A;
    font-weight: bold;
    font-size: 7.5px;
    border-left: 2px solid #00096A;
}

.details .value {
    width: 32%;
    background-color: #FFFFFF;
    color: #252A35;
    font-size: 8.5px;
}

/* Subtle navy accent on alternate rows */
.details tr:nth-child(even) .label {
    background-color: #DDE3F3;
}

/* Payment reference row */
.details td[colspan="3"] {
    background-color: #F8F9FC;
}

    </style>
</head>

<body>

@php
    /*
    |--------------------------------------------------------------------------
    | Receipt values
    |--------------------------------------------------------------------------
    */

    $receiptNumber = $donation->donation_number
        ?? ('DON-' . $donation->id);

    $donationDate = $donation->donation_date
        ? \Carbon\Carbon::parse($donation->donation_date)->format('d M Y')
        : '—';

    $recordedDate = $donation->created_at
        ? $donation->created_at->format('d M Y, H:i')
        : '—';

    $donationType = ucfirst(
        str_replace('_', ' ', $donation->type ?? '—')
    );

    $donor = $donation->donor ?? null;

    $isInKind = strtolower(
        str_replace(['-', ' '], '_', $donation->type ?? '')
    ) === 'in_kind';

    $isInternalReceipt = strtolower(
        (string) ($receiptType ?? 'donor')
    ) === 'internal';

    $items = $donation->items ?? collect();

    $donationAmount = (float) ($donation->amount ?? 0);

    $currency = $donation->currency ?? 'KES';

    $estimatedTotal = 0;

    foreach ($items as $item) {
        $quantity = (float) ($item->quantity ?? 0);

        $unitValue = (float) (
            $item->estimated_value
            ?? $item->unit_value
            ?? 0
        );

        $lineValue = (float) (
            $item->total_value
            ?? ($quantity * $unitValue)
        );

        $estimatedTotal += $lineValue;
    }

    if ($estimatedTotal <= 0) {
        $estimatedTotal = (float) (
            $donation->estimated_value ?? 0
        );
    }

    $logoPath = public_path('assets/img/logo.png');
@endphp

<div class="security-pattern">
    <span>TEULE KENYA</span>
    <span>TEULE KENYA</span>
    <span>TEULE KENYA</span>
    <span>TEULE KENYA</span>
</div>

<div class="receipt">

    {{-- =========================================================
         HEADER
         ========================================================= --}}

    <div class="header">

        <div class="header-left">

            
                <img
                    src="{{ $logoPath }}"
                    class="logo"
                    alt="Teule Kenya"
                >
           
                <div class="organisation-name">
                    TEULE KENYA
                </div>
            

            

        </div>

        <div class="header-right">

            <div class="document-title">
                Donation Receipt
            </div>

            <div class="document-number">
                Receipt No:
                <strong>{{ $receiptNumber }}</strong>
            </div>

        </div>

    </div>


    {{-- =========================================================
         ORIGINAL SUMMARY DETAILS
         ========================================================= --}}

    <div class="summary">

        <div class="summary-item">

            <div class="summary-label">
                Donation Date
            </div>

            <div class="summary-value">
                {{ $donationDate }}
            </div>

        </div>

        <div class="summary-item">

            <div class="summary-label">
                Donation Type
            </div>

            <div class="summary-value">
                {{ $donationType }}
            </div>

        </div>

        <div class="summary-item">

            <div class="summary-label">
                Recorded
            </div>

            <div class="summary-value">
                {{ $recordedDate }}
            </div>

        </div>

        <div class="summary-item">

            <div class="summary-label">
                Reference
            </div>

            <div class="summary-value">
                {{ $donation->reference ?? $receiptNumber }}
            </div>

        </div>

    </div>


    {{-- =========================================================
         DONOR DETAILS
         ========================================================= --}}

    <div class="section">

        <div class="section-title">
            Donor Details
        </div>

        <table class="details">

            <tbody>

                <tr>
                    <td class="label">Donor Name</td>

                    <td class="value">
                        {{ $donor->name ?? 'Anonymous / Not Specified' }}
                    </td>

                    <td class="label">Donor Number</td>

                    <td class="value">
                        {{ $donor->donor_number ?? '—' }}
                    </td>
                </tr>

                <tr>
                    <td class="label">Phone</td>

                    <td class="value">
                        {{ $donor->phone ?? $donor->phone_number ?? '—' }}
                    </td>

                    <td class="label">Email</td>

                    <td class="value">
                        {{ $donor->email ?? '—' }}
                    </td>
                </tr>

                <tr>
                    <td class="label">Address</td>

                    <td class="value" colspan="3">
                        {{ $donor->address ?? $donor->postal_address ?? '—' }}
                    </td>
                </tr>

            </tbody>

        </table>

    </div>


    {{-- =========================================================
         DONATION DETAILS
         ========================================================= --}}

    <div class="section">

        <div class="section-title">
            Donation Details
        </div>

<table class="details">
    <colgroup>
        <col style="width: 18%;">
        <col style="width: 32%;">
        <col style="width: 18%;">
        <col style="width: 32%;">
    </colgroup>
    <tbody>
        <tr>
            <td class="label">Purpose</td>
            <td class="value">{{ $donation->purpose ?? 'General Support' }}</td>

            <td class="label">Classification</td>
            <td class="value">
                {{ ucfirst(str_replace('_', ' ', $donation->classification ?? '—')) }}
            </td>
        </tr>

        <tr>
            <td class="label">Received By</td>
            <td class="value">{{ $donation->receivedBy->name ?? '—' }}</td>

            <td class="label">Payment Method</td>
            <td class="value">
                {{ ucfirst(str_replace('_', ' ', $donation->payment_method ?? '—')) }}
            </td>
        </tr>

        @if(!empty($donation->payment_reference))
            <tr>
                <td class="label">Payment Ref.</td>
                <td class="value" colspan="3">
                    {{ $donation->payment_reference }}
                </td>
            </tr>
        @endif
    </tbody>
</table>

    </div>


    {{-- =========================================================
         CASH DONATION
         ========================================================= --}}

    @if(!$isInKind)

        <div class="amount-panel">

            <div class="amount-label">
                Donation Amount
            </div>

            <div class="amount-value">
                {{ $currency }}
                {{ number_format($donationAmount, 2) }}
            </div>

            @if($isInternalReceipt)
                <div class="amount-note">
                    Recorded donation value.
                </div>
            @endif

        </div>

    @endif


    {{-- =========================================================
         IN-KIND DONATION ITEMS
         ========================================================= --}}

    @if($isInKind)

        <div class="section">

            <div class="section-title">
                Items Donated
            </div>

            <table class="items-table">

                <thead>
                    <tr>
                        <th style="width: 6%;" class="text-center">#</th>
                        <th style="width: 40%;">Item Description</th>
                        <th style="width: 14%;" class="text-right">Quantity</th>
                        <th style="width: 16%;">Unit</th>

                        @if($isInternalReceipt)
                            <th style="width: 12%;" class="text-right">
                                Unit Value
                            </th>

                            <th style="width: 12%;" class="text-right">
                                Total
                            </th>
                        @endif
                    </tr>
                </thead>

                <tbody>

                    @forelse($items as $index => $item)

                        @php
                            $itemQuantity = (float) ($item->quantity ?? 0);

                            $itemUnitValue = (float) (
                                $item->estimated_value
                                ?? $item->unit_value
                                ?? 0
                            );

                            $itemTotalValue = (float) (
                                $item->total_value
                                ?? ($itemQuantity * $itemUnitValue)
                            );
                        @endphp

                        <tr>

                            <td class="text-center">
                                {{ $index + 1 }}
                            </td>

                            <td>
                                <strong>
                                     {{ $item->item ?? '—' }}
                                    {{ $item->item ?? $item->name ?? $item->description ?? 'Donated item' }}
                                </strong>

                                @if(!empty($item->description) && !empty($item->item_name))
                                    <div class="muted">
                                        {{ $item->description }}
                                    </div>
                                @endif
                            </td>

                            <td class="text-right">
                                {{ number_format($itemQuantity, 2) }}
                            </td>

                            <td>
                                {{ $item->unit ?? $item->unit_of_measure ?? '—' }}
                            </td>

                            @if($isInternalReceipt)

                                <td class="text-right">
                                    {{ number_format($itemUnitValue, 2) }}
                                </td>

                                <td class="text-right">
                                    {{ number_format($itemTotalValue, 2) }}
                                </td>

                            @endif

                        </tr>

                    @empty

                        <tr>
                            <td
                                colspan="{{ $isInternalReceipt ? 6 : 4 }}"
                                class="text-center muted"
                            >
                                No individual items were recorded.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

                @if($isInternalReceipt && $items->count())
                    <tfoot>
                        <tr>
                            <td colspan="5" class="text-right">
                                <strong>Estimated Total Value</strong>
                            </td>

                            <td class="text-right">
                                <strong>
                                    {{ number_format($estimatedTotal, 2) }}
                                </strong>
                            </td>
                        </tr>
                    </tfoot>
                @endif

            </table>

            @if(!$isInternalReceipt)
                <div class="amount-note" style="margin-top: 3px;">
                    This receipt acknowledges the items listed above.
                </div>
            @endif

        </div>

        @if($isInternalReceipt)

            <div class="amount-panel">

                <div class="amount-label">
                    Estimated Donation Value
                </div>

                <div class="amount-value">
                    {{ $currency }}
                    {{ number_format($estimatedTotal, 2) }}
                </div>

                <div class="amount-note">
                    For internal record and inventory valuation purposes.
                </div>

            </div>

        @endif

    @endif


    {{-- =========================================================
         NOTES
         ========================================================= --}}

    @if(!empty($donation->description) || !empty($donation->notes))

        <div class="section">

            <div class="section-title">
                Notes
            </div>

            <div class="notes">
                @if(!empty($donation->description))
                    {{ $donation->description }}
                @endif

                @if(!empty($donation->description) && !empty($donation->notes))
                    <br>
                @endif

                @if(!empty($donation->notes))
                    {{ $donation->notes }}
                @endif
            </div>

        </div>

    @endif


    {{-- =========================================================
         ACKNOWLEDGEMENT
         ========================================================= --}}

    <div class="acknowledgement">

        <strong>Thank you for your generosity.</strong>

        Teule Kenya gratefully acknowledges receipt of this
        {{ $isInKind ? 'in-kind donation' : 'donation' }} in support of our mission
        to restore hope and build futures for vulnerable children.

        @if(!$isInKind)
            This receipt confirms the amount recorded by Teule Kenya.
        @else
            This receipt confirms the donated items recorded above.
        @endif

    </div>


    {{-- =========================================================
         SIGNATURES
         ========================================================= --}}

    <div class="signature-area">

        <div class="signature-column">

            <div class="signature-line"></div>

            <div class="signature-label">
                Received By <span> <b> {{ $donation->receivedBy->name ?? 'Authorised Representative' }} </b> </span>
            </div>

        </div>

        <div class="signature-column">

            <div class="signature-line"></div>

            <div class="signature-label">
                Donor / Representative
            </div>

        </div>

    </div>


    {{-- =========================================================
         QR CODE
         ========================================================= --}}

    @if(!empty($qrCodePath) && file_exists($qrCodePath))

        <div class="qr-area">

            <img
                src="{{ $qrCodePath }}"
                class="qr-code"
                alt="Receipt verification QR code"
            >

            <div class="qr-caption">
                Receipt verification
            </div>

        </div>

    @endif


    {{-- =========================================================
         FOOTER
         ========================================================= --}}

    <div class="footer">

        <div class="footer-left">
            <strong class="navy">TEULE KENYA</strong>
            <br>
            Chombo Cha Upendo — Vessel of Love
        </div>

        <div class="footer-right">
            Generated:
            {{ isset($generatedAt) ? $generatedAt : now()->format('d M Y, H:i') }}
            <br>
            
        </div>

    </div>

</div>

</body>
</html>