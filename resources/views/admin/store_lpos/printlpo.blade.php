<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <title>
        LPO - {{ $storeLpo->lpo_number }}
    </title>

    <style>

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
        | INNER PAGE MARGINS
        |--------------------------------------------------------------------------
        */

        .lpo-container {
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

        .lpo-title {
            font-size: 17px;
            font-weight: bold;
            color: #00096A;
            letter-spacing: 0.5px;
        }

        .lpo-number {
            font-size: 9px;
            color: #555;
            margin-top: 3px;
        }

        .lpo-number strong {
            color: #00096A;
        }


        /*
        |--------------------------------------------------------------------------
        | STATUS
        |--------------------------------------------------------------------------
        */

        .status-badge {
            display: inline-block;
            margin-top: 5px;
            padding: 3px 8px;
            background: #f0f2f7;
            border: 1px solid #d7d9e3;
            color: #333;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
        }


        /*
        |--------------------------------------------------------------------------
        | SUMMARY STRIP
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
            width: 20%;
            color: #777;
            font-size: 8.5px;
            font-weight: bold;
        }

        .value {
            width: 30%;
            font-size: 9px;
        }


        /*
        |--------------------------------------------------------------------------
        | SUPPLIER
        |--------------------------------------------------------------------------
        */

        .supplier-name {
            font-size: 12px;
            font-weight: bold;
            color: #00096A;
        }

        .supplier-meta {
            margin-top: 2px;
            color: #555;
            font-size: 8.5px;
        }


        /*
        |--------------------------------------------------------------------------
        | ITEMS
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

        .items-table tbody tr {
            page-break-inside: avoid;
        }

        .items-table .amount {
            text-align: right;
            white-space: nowrap;
        }

        .items-table .quantity {
            text-align: right;
            white-space: nowrap;
        }

        .total-row td {
            background: #f7f8fb;
            font-weight: bold;
        }


        /*
        |--------------------------------------------------------------------------
        | TOTALS
        |--------------------------------------------------------------------------
        */

        .totals-table {
            width: 100%;
            margin-top: 8px;
        }

        .totals-table td {
            padding: 4px 6px;
            font-size: 9px;
        }

        .totals-label {
            width: 75%;
            text-align: right;
            color: #666;
        }

        .totals-value {
            width: 25%;
            text-align: right;
            white-space: nowrap;
        }

        .grand-total td {
            border-top: 2px solid #00096A;
            padding-top: 6px;
            font-size: 11px;
            font-weight: bold;
            color: #00096A;
        }


        /*
        |--------------------------------------------------------------------------
        | NOTES
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
        | APPROVAL HISTORY
        |--------------------------------------------------------------------------
        */

        .approval-table {
            width: 100%;
        }

        .approval-table th {
            background: #f0f2f7;
            border: 1px solid #d4d7df;
            padding: 5px 6px;
            font-size: 8px;
            text-align: left;
        }

        .approval-table td {
            border: 1px solid #e0e2e7;
            padding: 4px 6px;
            font-size: 8px;
            vertical-align: top;
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
            TEULE KENYA • OFFICIAL LOCAL PURCHASE ORDER • TEULE KENYA • OFFICIAL LOCAL PURCHASE ORDER
        </div>

        <div>
            OFFICIAL LOCAL PURCHASE ORDER • TEULE KENYA • OFFICIAL LOCAL PURCHASE ORDER • TEULE KENYA
        </div>

        <div>
            TEULE KENYA • OFFICIAL LOCAL PURCHASE ORDER • TEULE KENYA • OFFICIAL LOCAL PURCHASE ORDER
        </div>

        <div>
            OFFICIAL LOCAL PURCHASE ORDER • TEULE KENYA • OFFICIAL LOCAL PURCHASE ORDER • TEULE KENYA
        </div>

        <div>
            TEULE KENYA • OFFICIAL LOCAL PURCHASE ORDER • TEULE KENYA • OFFICIAL LOCAL PURCHASE ORDER
        </div>

        <div>
            OFFICIAL LOCAL PURCHASE ORDER • TEULE KENYA • OFFICIAL LOCAL PURCHASE ORDER • TEULE KENYA
        </div>

    </div>


    {{-- ============================================================
         MAIN CONTAINER
         ============================================================ --}}

    <div class="lpo-container">


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

                        <div class="lpo-title">
                            LOCAL PURCHASE ORDER
                        </div>

                        <div class="lpo-number">

                            LPO No:

                            <strong>
                                {{ $storeLpo->lpo_number }}
                            </strong>

                        </div>

                        <div class="status-badge">

                            {{ strtoupper(
                                str_replace(
                                    '_',
                                    ' ',
                                    $storeLpo->status ?? 'DRAFT'
                                )
                            ) }}

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
                        LPO Date
                    </span>

                    <span class="summary-value">
                        {{ optional($storeLpo->lpo_date)->format('d M Y') }}
                    </span>

                </td>


                <td style="width: 25%;">

                    <span class="summary-label">
                        Expected Delivery
                    </span>

                    <span class="summary-value">

                        @if($storeLpo->expected_delivery_date)

                            {{ $storeLpo->expected_delivery_date->format('d M Y') }}

                        @else

                            —

                        @endif

                    </span>

                </td>


                <td style="width: 25%;">

                    <span class="summary-label">
                        Approval Stage
                    </span>

                    <span class="summary-value">

                        {{ $storeLpo->approval_stage
                            ? strtoupper(
                                str_replace(
                                    '_',
                                    ' ',
                                    $storeLpo->approval_stage
                                )
                            )
                            : '—'
                        }}

                    </span>

                </td>


                <td style="width: 25%;">

                    <span class="summary-label">
                        Items
                    </span>

                    <span class="summary-value">
                        {{ $storeLpo->items->count() }}
                    </span>

                </td>

            </tr>

        </table>


        {{-- ============================================================
             SUPPLIER
             ============================================================ --}}

        <div class="section">

            <div class="section-title">
                Supplier
            </div>

            <div class="section-body">

                @if($storeLpo->supplier)

                    <div class="supplier-name">
                        {{ $storeLpo->supplier->name ?? '—' }}
                    </div>

                    <div class="supplier-meta">

                        @if($storeLpo->supplier->supplier_code)
                            Code: {{ $storeLpo->supplier->supplier_code }}
                        @endif

                        @if($storeLpo->supplier->supplier_code && $storeLpo->supplier->phone)
                            &nbsp; | &nbsp;
                        @endif

                        @if($storeLpo->supplier->phone)
                            Phone: {{ $storeLpo->supplier->phone }}
                        @endif

                        @if(
                            ($storeLpo->supplier->supplier_code || $storeLpo->supplier->phone)
                            && $storeLpo->supplier->email
                        )
                            &nbsp; | &nbsp;
                        @endif

                        @if($storeLpo->supplier->email)
                            Email: {{ $storeLpo->supplier->email }}
                        @endif

                    </div>

                @else

                    <span class="muted">
                        No supplier information recorded.
                    </span>

                @endif

            </div>

        </div>


        {{-- ============================================================
             LPO DETAILS
             ============================================================ --}}

        <div class="section">

            <div class="section-title">
                Purchase Order Details
            </div>

            <div class="section-body">

                <table class="details-table">

                    <tr>

                        <td class="label">
                            Store
                        </td>

                        <td class="value">
                            {{ $storeLpo->store->name ?? '—' }}
                        </td>

                        <td class="label">
                            Created By
                        </td>

                        <td class="value">
                            {{ $storeLpo->creator->name ?? '—' }}
                        </td>

                    </tr>


                    <tr>

                        <td class="label">
                            LPO Date
                        </td>

                        <td class="value">
                            {{ optional($storeLpo->lpo_date)->format('d M Y') }}
                        </td>

                        <td class="label">
                            Expected Delivery
                        </td>

                        <td class="value">

                            {{ $storeLpo->expected_delivery_date
                                ? $storeLpo->expected_delivery_date->format('d M Y')
                                : '—'
                            }}

                        </td>

                    </tr>

                </table>

            </div>

        </div>


        {{-- ============================================================
             ITEMS
             ============================================================ --}}

        <div class="section">

            <div class="section-title">
                Ordered Items
            </div>

            <div class="section-body">

                <table class="items-table">

                    <thead>

                        <tr>

                            <th style="width: 5%;" class="text-center">
                                #
                            </th>

                            <th style="width: 28%;">
                                Item
                            </th>

                            <th style="width: 17%;">
                                Variant
                            </th>

                            <th style="width: 11%;" class="text-right">
                                Qty
                            </th>

                            <th style="width: 13%;" class="text-right">
                                Unit Price
                            </th>

                            <th style="width: 10%;" class="text-right">
                                Discount
                            </th>

                            <th style="width: 16%;" class="text-right">
                                Line Total
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @forelse($storeLpo->items as $index => $item)

                            <tr>

                                <td class="text-center">
                                    {{ $index + 1 }}
                                </td>

                                <td>

                                    <strong>
                                        {{ $item->item->name ?? $item->description ?? '—' }}
                                    </strong>

                                    @if($item->description && $item->item)
                                        <br>
                                        <span class="muted">
                                            {{ $item->description }}
                                        </span>
                                    @endif

                                </td>

                                <td>

                                    {{ $item->variant->name
                                        ?? $item->variant->code
                                        ?? '—'
                                    }}

                                </td>

                                <td class="quantity">

                                    {{ number_format(
                                        (float) $item->ordered_quantity,
                                        3
                                    ) }}

                                </td>

                                <td class="amount">

                                    {{ number_format(
                                        (float) $item->unit_price,
                                        2
                                    ) }}

                                </td>

                                <td class="amount">

                                    {{ number_format(
                                        (float) $item->discount,
                                        2
                                    ) }}

                                </td>

                                <td class="amount">

                                    {{ number_format(
                                        (float) $item->line_total,
                                        2
                                    ) }}

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td colspan="7" class="text-center">
                                    No items recorded on this LPO.
                                </td>

                            </tr>

                        @endforelse


                        @if($storeLpo->items->count())

                            <tr class="total-row">

                                <td colspan="6" class="text-right">
                                    Subtotal
                                </td>

                                <td class="amount">
                                    {{ number_format((float) $storeLpo->subtotal, 2) }}
                                </td>

                            </tr>

                        @endif

                    </tbody>

                </table>


                {{-- ====================================================
                     TOTALS
                     ==================================================== --}}

                <table class="totals-table">

                    <tr>

                        <td class="totals-label">
                            Subtotal
                        </td>

                        <td class="totals-value">
                            {{ number_format((float) $storeLpo->subtotal, 2) }}
                        </td>

                    </tr>


                    <tr>

                        <td class="totals-label">
                            Discount
                        </td>

                        <td class="totals-value">
                            {{ number_format((float) $storeLpo->discount, 2) }}
                        </td>

                    </tr>


                    <tr>

                        <td class="totals-label">
                            Tax
                        </td>

                        <td class="totals-value">
                            {{ number_format((float) $storeLpo->tax, 2) }}
                        </td>

                    </tr>


                    <tr class="grand-total">

                        <td class="totals-label">
                            TOTAL
                        </td>

                        <td class="totals-value">
                            {{ number_format((float) $storeLpo->total, 2) }}
                        </td>

                    </tr>

                </table>

            </div>

        </div>


        {{-- ============================================================
             NOTES
             ============================================================ --}}

        @if($storeLpo->notes)

            <div class="section">

                <div class="section-title">
                    Notes / Instructions
                </div>

                <div class="section-body">

                    <div class="note">

                        {!! nl2br(e($storeLpo->notes)) !!}

                    </div>

                </div>

            </div>

        @endif


        {{-- ============================================================
             APPROVAL HISTORY
             ============================================================ --}}

        @if($storeLpo->approvals->count())

            <div class="section">

                <div class="section-title">
                    Approval History
                </div>

                <div class="section-body">

                    <table class="approval-table">

                        <thead>

                            <tr>

                                <th style="width: 18%;">
                                    Stage
                                </th>

                                <th style="width: 20%;">
                                    Action
                                </th>

                                <th style="width: 27%;">
                                    Officer
                                </th>

                                <th style="width: 35%;">
                                    Date / Comments
                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @foreach($storeLpo->approvals as $approval)

                                <tr>

                                    <td>

                                        {{ strtoupper(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $approval->approval_stage ?? '—'
                                            )
                                        ) }}

                                    </td>

                                    <td>

                                        {{ strtoupper(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $approval->action ?? '—'
                                            )
                                        ) }}

                                    </td>

                                    <td>
                                        {{ $approval->user->name ?? '—' }}
                                    </td>

                                    <td>

                                        {{ optional($approval->acted_at)->format('d M Y, H:i') }}

                                        @if($approval->comments)

                                            <br>

                                            <span class="muted">
                                                {{ $approval->comments }}
                                            </span>

                                        @endif

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        @endif


        {{-- ============================================================
             ACKNOWLEDGEMENT
             ============================================================ --}}

        <div class="acknowledgement">

            <strong>
                TEULE KENYA — LOCAL PURCHASE ORDER
            </strong>

            <br>

            This document represents the purchase order recorded under

            <strong>
                {{ $storeLpo->lpo_number }}
            </strong>.

            @if($storeLpo->status === 'APPROVED')
                <br>
                The LPO has been approved through the applicable approval workflow.
            @else
                <br>
                This LPO is currently
                <strong>
                    {{ strtoupper(str_replace('_', ' ', $storeLpo->status ?? 'DRAFT')) }}
                </strong>.
            @endif

        </div>


        {{-- ============================================================
             SIGNATURES
             ============================================================ --}}

        <table class="signature-table">

            <tr>

                <td>

                    <div class="signature-line">
                        Prepared / Requested By
                    </div>

                </td>


                <td>

                    <div class="signature-line">
                        Approved By
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

