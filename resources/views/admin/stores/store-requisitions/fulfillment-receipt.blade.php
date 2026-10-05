<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <title>
        Fulfillment Receipt - {{ $fulfillment->transaction_number }}
    </title>

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        @page {
            size: A4;
            margin: 12mm 13mm 14mm 13mm;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            background: #ffffff;
            color: #222;
            font-family: Arial, Helvetica, sans-serif;
            font-size: 11px;
            line-height: 1.4;
        }

        .receipt {
            width: 100%;
            max-width: 900px;
            margin: 0 auto;
            position: relative;
        }

        /* Security background */
        .security-pattern {
            position: fixed;
            inset: 0;
            z-index: -1;
            opacity: 0.035;
            overflow: hidden;
            pointer-events: none;
        }

        .security-pattern span {
            position: absolute;
            font-size: 42px;
            font-weight: 700;
            color: #00096A;
            transform: rotate(-28deg);
            white-space: nowrap;
        }

        .security-pattern span:nth-child(1) {
            top: 10%;
            left: 5%;
        }

        .security-pattern span:nth-child(2) {
            top: 38%;
            left: 35%;
        }

        .security-pattern span:nth-child(3) {
            top: 68%;
            left: 5%;
        }

        .security-pattern span:nth-child(4) {
            top: 84%;
            left: 45%;
        }

        /* Header */
        .header {
            display: table;
            width: 100%;
            border-bottom: 2px solid #00096A;
            padding-bottom: 9px;
            margin-bottom: 12px;
        }

        .header-left,
        .header-right {
            display: table-cell;
            vertical-align: middle;
        }

        .header-left {
            width: 65%;
        }

        .header-right {
            width: 35%;
            text-align: right;
        }

        .logo {
            max-height: 62px;
            max-width: 190px;
        }

        .organisation-name {
            color: #00096A;
            font-size: 19px;
            font-weight: 700;
            margin-bottom: 2px;
        }

        .organisation-subtitle {
            color: #555;
            font-size: 9px;
        }

        .document-title {
            color: #00096A;
            font-size: 17px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .document-number {
            font-size: 10px;
            color: #555;
            margin-top: 3px;
        }

        /* Summary */
        .summary {
            display: table;
            width: 100%;
            margin-bottom: 12px;
            border: 1px solid #d8dce5;
            border-radius: 4px;
        }

        .summary-item {
            display: table-cell;
            width: 25%;
            padding: 8px 10px;
            border-right: 1px solid #d8dce5;
            vertical-align: top;
        }

        .summary-item:last-child {
            border-right: none;
        }

        .summary-label {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #777;
            margin-bottom: 2px;
        }

        .summary-value {
            font-size: 11px;
            font-weight: 700;
            color: #222;
        }

        /* Section */
        .section-title {
            color: #00096A;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            border-bottom: 1px solid #d8dce5;
            padding-bottom: 4px;
            margin-top: 12px;
            margin-bottom: 7px;
        }

        /* Details */
        .details {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        .details td {
            padding: 4px 5px;
            vertical-align: top;
        }

        .details .label {
            width: 17%;
            color: #777;
            font-size: 9px;
            text-transform: uppercase;
        }

        .details .value {
            width: 33%;
            font-weight: 600;
        }

        /* Items table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 4px;
        }

        .items-table th {
            background: #00096A;
            color: #ffffff;
            font-size: 8.5px;
            text-transform: uppercase;
            letter-spacing: 0.25px;
            padding: 6px 5px;
            border: 1px solid #00096A;
            text-align: left;
            white-space: nowrap;
        }

        .items-table td {
            padding: 6px 5px;
            border: 1px solid #d8dce5;
            vertical-align: middle;
            font-size: 9.5px;
        }

        .items-table tbody tr:nth-child(even) {
            background: #fafbfc;
        }

        .items-table .number {
            width: 4%;
            text-align: center;
        }

        .items-table .item {
            width: 24%;
            font-weight: 600;
        }

        .items-table .variant {
            width: 12%;
        }

        .items-table .unit {
            width: 8%;
            text-align: center;
        }

        .items-table .quantity {
            width: 9%;
            text-align: right;
        }

        .items-table .last-issue {
            width: 18%;
        }

        .muted {
            color: #888;
            font-weight: 400;
        }

        .issued-now {
            font-weight: 700;
            color: #00096A;
        }

        .balance {
            font-weight: 700;
        }

        /* Balance notice */
        .balance-notice {
            margin-top: 10px;
            padding: 8px 10px;
            border-left: 4px solid #EF4700;
            background: #fff8f3;
            color: #333;
        }

        .balance-notice strong {
            color: #EF4700;
        }

        /* Notes */
        .notes {
            border: 1px solid #d8dce5;
            padding: 8px 10px;
            min-height: 38px;
            margin-top: 5px;
            background: #fff;
        }

        /* Acknowledgement */
        .acknowledgement {
            margin-top: 13px;
            padding: 8px 10px;
            border: 1px solid #d8dce5;
            font-size: 9px;
            color: #555;
        }

        /* Signature */
        .signature-area {
            width: 100%;
            display: table;
            margin-top: 18px;
        }

        .signature-column {
            display: table-cell;
            width: 50%;
            padding-right: 20px;
            vertical-align: bottom;
        }

        .signature-column:last-child {
            padding-right: 0;
            padding-left: 20px;
        }

        .signature-line {
            border-bottom: 1px solid #333;
            height: 25px;
            margin-bottom: 4px;
        }

        .signature-label {
            font-size: 8px;
            color: #666;
            text-transform: uppercase;
        }

        /* Footer */
        .footer {
            margin-top: 15px;
            padding-top: 7px;
            border-top: 1px solid #d8dce5;
            display: table;
            width: 100%;
            font-size: 8px;
            color: #777;
        }

        .footer-left,
        .footer-right {
            display: table-cell;
            vertical-align: top;
        }

        .footer-right {
            text-align: right;
        }

        /* Print */
        @media print {
            body {
                background: #ffffff;
            }

            .receipt {
                max-width: none;
            }

            .no-print {
                display: none !important;
            }
        }
    </style>
</head>

<body>

@php

    /*
    |--------------------------------------------------------------------------
    | Requisition associated with this fulfillment
    |--------------------------------------------------------------------------
    */

    $storeRequisition = $fulfillment->requisition;


    /*
    |--------------------------------------------------------------------------
    | Basic receipt values
    |--------------------------------------------------------------------------
    */

    $totalBalance = 0;
    $itemCount = 0;

    foreach ($fulfillment->items as $fulfillmentItem) {

        $requisitionItem = $fulfillmentItem->requisitionItem;

        if (!$requisitionItem) {
            continue;
        }

        $itemCount++;

        $totalBalance += (float) (
            $requisitionItem->outstanding_quantity ?? 0
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Previous issue lookup
    |--------------------------------------------------------------------------
    |
    | Find the most recent previous issue for the same:
    |
    | - Department
    | - Store item
    | - Variant
    |
    | The current fulfillment is excluded.
    |
    */

    $historicalIssueItems = \App\Models\StoreFulfillmentItem::query()
        ->with([
            'fulfillment.requisition',
        ])
        ->where(
            'store_fulfillment_id',
            '!=',
            $fulfillment->id
        )
        ->whereHas(
            'fulfillment.requisition',
            function ($query) use ($storeRequisition) {

                $query->where(
                    'department',
                    $storeRequisition->department
                );

            }
        )
        ->orderByDesc('created_at')
        ->get();


    /*
    |--------------------------------------------------------------------------
    | Keep only the most recent issue for each item + variant
    |--------------------------------------------------------------------------
    */

    $previousIssues = [];

    foreach ($historicalIssueItems as $historicalItem) {

        $key =
            (string) $historicalItem->store_item_id
            . '|'
            . (string) ($historicalItem->variant_id ?? 0);

        if (!isset($previousIssues[$key])) {
            $previousIssues[$key] = $historicalItem;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Format previous issue
    |--------------------------------------------------------------------------
    */

    $formatLastIssue = function ($historicalItem) {

        if (!$historicalItem) {
            return null;
        }

        $quantity = (float) $historicalItem->quantity;

        $formattedQuantity = rtrim(
            rtrim(
                number_format(
                    $quantity,
                    3,
                    '.',
                    ''
                ),
                '0'
            ),
            '.'
        );

        return [
            'quantity' => $formattedQuantity,
            'date' => optional(
                $historicalItem->created_at
            )->format('d M Y'),
        ];
    };


    /*
    |--------------------------------------------------------------------------
    | Quantity formatter
    |--------------------------------------------------------------------------
    */

    $formatQuantity = function ($value) {

        return rtrim(
            rtrim(
                number_format(
                    (float) $value,
                    3,
                    '.',
                    ''
                ),
                '0'
            ),
            '.'
        );
    };

@endphp


<div class="receipt">

    {{-- Security background --}}

    <div class="security-pattern">

        <span>TEULE KENYA</span>
        <span>TEULE KENYA</span>
        <span>TEULE KENYA</span>
        <span>TEULE KENYA</span>

    </div>


    {{-- Header --}}

    <div class="header">

        <div class="header-left">

            @if(file_exists(public_path('images/logo.png')))

                <img
                    src="{{ public_path('images/logo.png') }}"
                    class="logo"
                    alt="Teule Kenya"
                >

            @else

                <div class="organisation-name">
                    TEULE KENYA
                </div>

            @endif

            <div class="organisation-name">
                Teule Kenya
            </div>

            <div class="organisation-subtitle">
                Chombo Cha Upendo · Vessel of Love
            </div>

        </div>


        <div class="header-right">

            <div class="document-title">
                Fulfillment Receipt
            </div>

            <div class="document-number">
                {{ $fulfillment->transaction_number }}
            </div>

        </div>

    </div>


    {{-- Summary --}}

    <div class="summary">

        <div class="summary-item">

            <div class="summary-label">
                Transaction
            </div>

            <div class="summary-value">
                {{ $fulfillment->transaction_number }}
            </div>

        </div>


        <div class="summary-item">

            <div class="summary-label">
                Date & Time
            </div>

            <div class="summary-value">
                {{ optional($fulfillment->created_at)->format('d M Y, H:i') }}
            </div>

        </div>


        <div class="summary-item">

            <div class="summary-label">
                Items Fulfilled
            </div>

            <div class="summary-value">
                {{ $itemCount }}
            </div>

        </div>


        <div class="summary-item">

            <div class="summary-label">
                Status
            </div>

            <div class="summary-value">
                Fulfilled
            </div>

        </div>

    </div>


    {{-- Fulfillment details --}}

    <div class="section-title">
        Fulfillment Details
    </div>


    <table class="details">

        <tr>

            <td class="label">
                Requisition
            </td>

            <td class="value">
                {{ $storeRequisition->requisition_number }}
            </td>


            <td class="label">
                Department
            </td>

            <td class="value">
                {{ $storeRequisition->department ?: '—' }}
            </td>

        </tr>


        <tr>

            <td class="label">
                Requested By
            </td>

            <td class="value">
                {{ optional($storeRequisition->requester)->name ?? '—' }}
            </td>


            <td class="label">
                Processed By
            </td>

            <td class="value">
                {{ optional($fulfillment->processor)->name ?? '—' }}
            </td>

        </tr>


        <tr>

            <td class="label">
                Source Store
            </td>

            <td class="value">
                {{ optional($fulfillment->sourceStore)->name ?? '—' }}
            </td>


            <td class="label">
                Destination
            </td>

            <td class="value">
                {{ optional($fulfillment->destinationStore)->name ?? '—' }}
            </td>

        </tr>

    </table>


    {{-- Fulfilled items --}}

    <div class="section-title">
        Fulfilled Items
    </div>


    <table class="items-table">

        <thead>

            <tr>

                <th class="number">
                    #
                </th>

                <th class="item">
                    Item
                </th>

                <th class="variant">
                    Variant
                </th>

                <th class="unit">
                    Unit
                </th>

                <th class="quantity">
                    Approved
                </th>

                <th class="last-issue">
                    Last Issue
                </th>

                <th class="quantity">
                    Issued Now
                </th>

                <th class="quantity">
                    Balance
                </th>

            </tr>

        </thead>


        <tbody>

        @foreach($fulfillment->items as $index => $fulfillmentItem)

            @php

                /*
                |--------------------------------------------------------------------------
                | Requisition item
                |--------------------------------------------------------------------------
                */

                $requisitionItem =
                    $fulfillmentItem->requisitionItem;

                if (!$requisitionItem) {
                    continue;
                }


                /*
                |--------------------------------------------------------------------------
                | Store item
                |--------------------------------------------------------------------------
                */

                $storeItem =
                    $requisitionItem->item ?? null;


                /*
                |--------------------------------------------------------------------------
                | Variant
                |--------------------------------------------------------------------------
                */

                $variant =
                    $requisitionItem->variant ?? null;


                /*
                |--------------------------------------------------------------------------
                | Item name
                |--------------------------------------------------------------------------
                */

                $itemName = '—';

                if ($storeItem) {

                    $itemName =
                        $storeItem->name
                        ?? $storeItem->item_name
                        ?? '—';

                } elseif (!empty($requisitionItem->item_name)) {

                    $itemName =
                        $requisitionItem->item_name;

                }


                /*
                |--------------------------------------------------------------------------
                | Variant name
                |--------------------------------------------------------------------------
                */

                $variantName = '—';

                if ($variant) {

                    $variantName =
                        $variant->name
                        ?? $variant->variant_name
                        ?? $variant->value
                        ?? '—';
                }


                /*
                |--------------------------------------------------------------------------
                | Unit
                |--------------------------------------------------------------------------
                |
                | IMPORTANT:
                | The unit is an Eloquent model in this application.
                | Do NOT print {{ $unit }} directly.
                |
                */

                $unit = null;

                /*
                | First try the variant's unit relationship.
                */

                if ($variant) {

                    $variantUnit = $variant->unit ?? null;

                    if ($variantUnit) {

                        if (is_object($variantUnit)) {

                            $unit =
                                $variantUnit->name
                                ?? $variantUnit->code
                                ?? null;

                        } else {

                            $unit = $variantUnit;

                        }
                    }
                }


                /*
                | If variant has no unit, try the store item's unit.
                */

                if (!$unit && $storeItem) {

                    $itemUnit = $storeItem->unit ?? null;

                    if ($itemUnit) {

                        if (is_object($itemUnit)) {

                            $unit =
                                $itemUnit->name
                                ?? $itemUnit->code
                                ?? null;

                        } else {

                            $unit = $itemUnit;

                        }
                    }
                }


                /*
                | Finally try the requisition item's unit.
                */

                if (!$unit) {

                    $requisitionUnit =
                        $requisitionItem->unit ?? null;

                    if ($requisitionUnit) {

                        if (is_object($requisitionUnit)) {

                            $unit =
                                $requisitionUnit->name
                                ?? $requisitionUnit->code
                                ?? null;

                        } else {

                            $unit = $requisitionUnit;

                        }
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | Previous issue
                |--------------------------------------------------------------------------
                */

                $historyKey =
                    (string) $fulfillmentItem->store_item_id
                    . '|'
                    . (string) (
                        $fulfillmentItem->variant_id ?? 0
                    );

                $previousIssue =
                    $previousIssues[$historyKey] ?? null;

                $lastIssue =
                    $formatLastIssue($previousIssue);


                /*
                |--------------------------------------------------------------------------
                | Current quantities
                |--------------------------------------------------------------------------
                */

                $approved =
                    (float) (
                        $requisitionItem->approved_quantity ?? 0
                    );


                $issuedNow =
                    (float) (
                        $fulfillmentItem->quantity ?? 0
                    );


                $balance =
                    (float) (
                        $requisitionItem->outstanding_quantity ?? 0
                    );

            @endphp


            <tr>

                {{-- Number --}}

                <td class="number">
                    {{ $index + 1 }}
                </td>


                {{-- Item --}}

                <td class="item">
                    {{ $itemName }}
                </td>


                {{-- Variant --}}

                <td class="variant">

                    @if($variantName !== '—')

                        {{ $variantName }}

                    @else

                        <span class="muted">
                            —
                        </span>

                    @endif

                </td>


                {{-- Unit --}}

                <td class="unit">

                    @if($unit)

                        {{ $unit }}

                    @else

                        <span class="muted">
                            —
                        </span>

                    @endif

                </td>


                {{-- Approved --}}

                <td class="quantity">

                    {{ $formatQuantity($approved) }}

                </td>


                {{-- Last Issue --}}

                <td class="last-issue">

                    @if($lastIssue)

                        <strong>
                            {{ $lastIssue['quantity'] }}

                            @if($unit)
                                {{ $unit }}
                            @endif
                        </strong>

                        <br>

                        <span class="muted">
                            {{ $lastIssue['date'] }}
                        </span>

                    @else

                        <span class="muted">
                            No previous issue
                        </span>

                    @endif

                </td>


                {{-- Issued Now --}}

                <td class="quantity issued-now">

                    {{ $formatQuantity($issuedNow) }}

                </td>


                {{-- Balance --}}

                <td class="quantity balance">

                    @if($balance > 0)

                        {{ $formatQuantity($balance) }}

                    @else

                        <span class="muted">
                            0
                        </span>

                    @endif

                </td>

            </tr>

        @endforeach

        </tbody>

    </table>


    {{-- Outstanding balance --}}

    @if($totalBalance > 0)

        <div class="balance-notice">

            <strong>
                Outstanding balance:
            </strong>

            Some approved quantities were not issued during this
            fulfillment. The outstanding quantities have been carried
            forward to a new requisition for the normal approval process.

        </div>

    @endif


    {{-- Notes --}}

    @if($fulfillment->notes)

        <div class="section-title">
            Fulfillment Notes
        </div>

        <div class="notes">

            {!! nl2br(e($fulfillment->notes)) !!}

        </div>

    @endif


    {{-- Acknowledgement --}}

    <div class="acknowledgement">

        <strong>
            Acknowledgement:
        </strong>

        I acknowledge receipt of the items listed above in the
        quantities indicated. I confirm that the physical items have
        been received and checked against this fulfillment receipt.

    </div>


    {{-- Signatures --}}

    <div class="signature-area">

        <div class="signature-column">

            <div class="signature-line"></div>

            <div class="signature-label">
                Issued By / Stores Officer
            </div>

        </div>


        <div class="signature-column">

            <div class="signature-line"></div>

            <div class="signature-label">
                Received By / Department Representative
            </div>

        </div>

    </div>


    {{-- Footer --}}

    <div class="footer">

        <div class="footer-left">

            Teule Kenya · Chombo Cha Upendo

            <br>

            Official Store Fulfillment Document

        </div>


        <div class="footer-right">

            Generated:
            {{ now()->format('d M Y, H:i') }}

            <br>

            {{ $fulfillment->transaction_number }}

        </div>

    </div>

</div>


<script>
    window.onload = function () {
        window.print();
    };
</script>

</body>
</html>

