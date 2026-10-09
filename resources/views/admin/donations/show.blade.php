@extends('layouts.admin')

@push('styles') <link rel="stylesheet" href="{{ asset('assets/css/stores.css') }}">
@endpush

@section('content')

@php


$isCash = $donation->type === 'cash';

$isInKind = $donation->type === 'in_kind';

$classificationClasses = [
    'donation' => 'requisition-list-status-approved',
    'payment' => 'requisition-list-status-approved',
    'refund' => 'requisition-list-status-pending',
    'other' => 'requisition-list-status-draft',
    'unclassified' => 'requisition-list-status-draft',
];

$classificationClass =
    $classificationClasses[$donation->classification]
    ?? 'requisition-list-status-draft';

$totalEstimatedValue = $donation->items->sum(
    fn ($item) => (float) ($item->estimated_value ?? 0)
);

$pendingCommunications = $donation->communications
    ->where('status', 'pending')
    ->whereNotNull('scheduled_at')
    ->sortBy('scheduled_at');


@endphp

<div class="store-requisition-page">


{{-- ============================================================
     PAGE HEADER
     ============================================================ --}}

<div class="requisition-page-header">

    <div class="requisition-header-content">

        <div class="requisition-header-icon">
            <i class="fa fa-hand-holding-heart"></i>
        </div>

        <div>

            <div class="requisition-breadcrumb">
                <span>Donations</span>
                <span>/</span>
                <span>Donation Details</span>
            </div>

            <h1 class="requisition-page-title">
                Donation Details
            </h1>

            <p class="requisition-page-subtitle">
                View and manage donation information, receipts and donor communications.
            </p>

        </div>

    </div>


    <div
        class="requisition-header-right"
        style="
            display:flex;
            align-items:center;
            gap:8px;
            flex-wrap:wrap;
        "
    >

        {{-- =====================================================
             RECORD SUMMARY
             ===================================================== --}}

        <div
            style="
                display:flex;
                align-items:center;
                gap:12px;
                flex-wrap:wrap;
                margin-right:4px;
            "
        >

            <span
                title="Received By"
                style="
                    font-size:12px;
                    color:#687b95;
                    white-space:nowrap;
                "
            >
                <i class="fa fa-user mr-1"></i>
                {{ optional($donation->receivedBy)->name ?? '—' }}
            </span>

            <span
                title="Donation Date"
                style="
                    font-size:12px;
                    color:#687b95;
                    white-space:nowrap;
                "
            >
                <i class="fa fa-calendar mr-1"></i>
                {{ optional($donation->donation_date)->format('d M Y') }}
            </span>

            <span
                title="Last Updated"
                style="
                    font-size:12px;
                    color:#687b95;
                    white-space:nowrap;
                "
            >
                <i class="fa fa-clock mr-1"></i>
                {{ optional($donation->updated_at)->format('d M Y H:i') }}
            </span>

        </div>


        {{-- BACK --}}

        <a
            href="{{ route('admin.donations.index') }}"
            class="requisition-cancel-button"
            style="
                display:inline-flex;
                align-items:center;
                justify-content:center;
                gap:6px;
                text-decoration:none;
            "
        >
            <i class="fa fa-arrow-left"></i>
            Back
        </a>


        {{-- EDIT --}}

        <a
            href="{{ route('admin.donations.edit', $donation) }}"
            class="requisition-add-button"
            style="
                display:inline-flex;
                align-items:center;
                justify-content:center;
                gap:6px;
                text-decoration:none;
            "
        >
            <i class="fa fa-edit"></i>
            Edit
        </a>


        {{-- =====================================================
             RECEIPT DROPDOWN
             ===================================================== --}}

        <div class="dropdown">

            <button
                type="button"
                class="requisition-add-button"
                data-toggle="dropdown"
                aria-haspopup="true"
                aria-expanded="false"
                style="
                    display:inline-flex;
                    align-items:center;
                    justify-content:center;
                    gap:6px;
                "
            >
                <i class="fa fa-file-pdf"></i>
                Receipt
                <i
                    class="fa fa-chevron-down"
                    style="font-size:10px;"
                ></i>
            </button>

            <div
                class="dropdown-menu dropdown-menu-right"
                style="
                    min-width:280px;
                    padding:6px;
                "
            >

                <div
                    style="
                        padding:7px 10px;
                        font-size:11px;
                        font-weight:700;
                        text-transform:uppercase;
                        letter-spacing:.3px;
                        color:#687b95;
                    "
                >
                    <i class="fa fa-file-invoice mr-1"></i>
                    Receipt Type
                </div>


                {{-- DONOR RECEIPT --}}

                <a
                    class="dropdown-item"
                    href="{{ route('admin.donations.receipt', [
                        'donation' => $donation,
                        'type' => 'donor'
                    ]) }}"
                    target="_blank"
                    style="
                        display:flex;
                        align-items:flex-start;
                        gap:10px;
                        padding:9px 10px;
                        border-radius:6px;
                        white-space:normal;
                    "
                >

                    <span
                        style="
                            width:30px;
                            height:30px;
                            min-width:30px;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            border-radius:6px;
                            background:#eef4ff;
                            color:#00096A;
                        "
                    >
                        <i class="fa fa-user"></i>
                    </span>

                    <span>

                        <span
                            style="
                                display:block;
                                font-size:13px;
                                font-weight:600;
                                color:#304967;
                            "
                        >
                            Donor Receipt
                        </span>

                        <span
                            style="
                                display:block;
                                margin-top:2px;
                                font-size:11px;
                                line-height:1.35;
                                color:#7a899d;
                            "
                        >
                            Official copy for the donor.
                            Estimated values for in-kind items are not shown.
                        </span>

                    </span>

                </a>


                {{-- INTERNAL RECEIPT --}}

                <a
                    class="dropdown-item"
                    href="{{ route('admin.donations.receipt', [
                        'donation' => $donation,
                        'type' => 'internal'
                    ]) }}"
                    target="_blank"
                    style="
                        display:flex;
                        align-items:flex-start;
                        gap:10px;
                        padding:9px 10px;
                        border-radius:6px;
                        white-space:normal;
                    "
                >

                    <span
                        style="
                            width:30px;
                            height:30px;
                            min-width:30px;
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            border-radius:6px;
                            background:#fff4e8;
                            color:#EF4700;
                        "
                    >
                        <i class="fa fa-building"></i>
                    </span>

                    <span>

                        <span
                            style="
                                display:block;
                                font-size:13px;
                                font-weight:600;
                                color:#304967;
                            "
                        >
                            Internal Receipt
                        </span>

                        <span
                            style="
                                display:block;
                                margin-top:2px;
                                font-size:11px;
                                line-height:1.35;
                                color:#7a899d;
                            "
                        >
                            Internal records copy.
                            Includes estimated values for in-kind donations.
                        </span>

                    </span>

                </a>

            </div>

        </div>


        {{-- =====================================================
             THANK YOU DROPDOWN
             ===================================================== --}}

        @if(
            $donation->classification === 'donation'
            && $donation->donor
        )

            <div class="dropdown">

                <button
                    type="button"
                    class="requisition-add-button"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                    style="
                        display:inline-flex;
                        align-items:center;
                        justify-content:center;
                        gap:6px;
                    "
                >
                    <i class="fa fa-paper-plane"></i>
                    Thank You
                    <i
                        class="fa fa-chevron-down"
                        style="font-size:10px;"
                    ></i>
                </button>

                <div class="dropdown-menu dropdown-menu-right">

                    <a
                        class="dropdown-item"
                        href="#"
                        onclick="
                            event.preventDefault();
                            document.getElementById('resend-sms-form').submit();
                        "
                    >
                        <i class="fa fa-mobile-alt mr-2"></i>
                        Send SMS
                    </a>

                    <a
                        class="dropdown-item"
                        href="#"
                        onclick="
                            event.preventDefault();
                            document.getElementById('resend-email-form').submit();
                        "
                    >
                        <i class="fa fa-envelope mr-2"></i>
                        Send Email
                    </a>

                    <div class="dropdown-divider"></div>

                    <a
                        class="dropdown-item"
                        href="#"
                        onclick="
                            event.preventDefault();
                            document.getElementById('resend-both-form').submit();
                        "
                    >
                        <i class="fa fa-paper-plane mr-2"></i>
                        Send Both
                    </a>

                </div>

            </div>


            {{-- SMS --}}

            <form
                id="resend-sms-form"
                method="POST"
                action="{{ route('admin.donations.resend-thank-you', $donation) }}"
                style="display:none;"
            >
                @csrf

                <input
                    type="hidden"
                    name="channel"
                    value="sms"
                >
            </form>


            {{-- EMAIL --}}

            <form
                id="resend-email-form"
                method="POST"
                action="{{ route('admin.donations.resend-thank-you', $donation) }}"
                style="display:none;"
            >
                @csrf

                <input
                    type="hidden"
                    name="channel"
                    value="email"
                >
            </form>


            {{-- BOTH --}}

            <form
                id="resend-both-form"
                method="POST"
                action="{{ route('admin.donations.resend-thank-you', $donation) }}"
                style="display:none;"
            >
                @csrf

                <input
                    type="hidden"
                    name="channel"
                    value="both"
                >
            </form>

        @endif

    </div>

</div>


{{-- ============================================================
     COMPACT DONATION SUMMARY
     ============================================================ --}}

<div class="requisition-section">

    <div class="requisition-section-header">

        <div class="requisition-section-heading">

            <div class="requisition-section-icon">

                @if($isCash)
                    <i class="fa fa-money-bill-wave"></i>
                @elseif($isInKind)
                    <i class="fa fa-gift"></i>
                @else
                    <i class="fa fa-hand-holding-heart"></i>
                @endif

            </div>

            <div>

                <h5>

                    @if($isCash)
                        Cash Donation
                    @elseif($isInKind)
                        In-Kind Donation
                    @else
                        Donation
                    @endif

                </h5>

                <p>

                    {{ $donation->donation_number }}

                    &nbsp; • &nbsp;

                    {{ optional($donation->donation_date)->format('d M Y') }}

                    @if($donation->source)

                        &nbsp; • &nbsp;

                        {{ ucfirst($donation->source) }}

                    @endif

                </p>

            </div>

        </div>


        <div
            class="requisition-list-status {{ $classificationClass }}"
            style="
                display:inline-flex;
                align-items:center;
                gap:6px;
                text-transform:capitalize;
            "
        >

            <span class="requisition-list-status-dot"></span>

            {{ str_replace('_', ' ', $donation->classification) }}

        </div>

    </div>


    <div class="requisition-details-body">

        <div
            class="d-flex align-items-center flex-wrap"
            style="gap:10px 18px;"
        >

            {{-- AMOUNT --}}

            <div>

                <span class="requisition-field-label">
                    Amount
                </span>

                <strong
                    style="
                        display:block;
                        font-size:20px;
                        line-height:1.2;
                        color:#00096A;
                    "
                >
                    {{ $donation->currency ?? 'KES' }}
                    {{ number_format((float) $donation->amount, 2) }}
                </strong>

            </div>


            {{-- DONATION NUMBER --}}

            <div>

                <span class="requisition-field-label">
                    Donation Number
                </span>

                <div class="requisition-input">
                    {{ $donation->donation_number }}
                </div>

            </div>


            {{-- DATE --}}

            <div>

                <span class="requisition-field-label">
                    Date
                </span>

                <div class="requisition-input">
                    {{ optional($donation->donation_date)->format('d M Y') }}
                </div>

            </div>


            {{-- TYPE --}}

            <div>

                <span class="requisition-field-label">
                    Type
                </span>

                <div class="requisition-input">
                    {{ ucfirst(str_replace('_', ' ', $donation->type)) }}
                </div>

            </div>


            {{-- SOURCE --}}

            @if($isCash)

                <div>

                    <span class="requisition-field-label">
                        Source
                    </span>

                    <div class="requisition-input">
                        {{ ucfirst($donation->source ?? '—') }}
                    </div>

                </div>


                {{-- CURRENCY --}}

                <div>

                    <span class="requisition-field-label">
                        Currency
                    </span>

                    <div class="requisition-input">
                        {{ $donation->currency ?? 'KES' }}
                    </div>

                </div>


                {{-- REFERENCE --}}

                <div>

                    <span class="requisition-field-label">
                        Reference
                    </span>

                    <div class="requisition-input">
                        {{ $donation->reference ?: '—' }}
                    </div>

                </div>


                {{-- PAYMENT REFERENCE --}}

                <div>

                    <span class="requisition-field-label">
                        Payment Reference
                    </span>

                    <div class="requisition-input">
                        {{ $donation->payment_reference ?: '—' }}
                    </div>

                </div>

            @endif


            {{-- PURPOSE --}}

            <div>

                <span class="requisition-field-label">
                    Purpose
                </span>

                <div class="requisition-input">
                    {{ $donation->purpose ?: '—' }}
                </div>

            </div>


            {{-- DESCRIPTION --}}

            @if($donation->description)

                <div style="flex:1 1 250px; min-width:220px;">

                    <span class="requisition-field-label">
                        Description
                    </span>

                    <div
                        class="requisition-input"
                        style="
                            max-width:none;
                            overflow:hidden;
                            text-overflow:ellipsis;
                            white-space:nowrap;
                        "
                        title="{{ $donation->description }}"
                    >
                        {{ $donation->description }}
                    </div>

                </div>

            @endif

        </div>


        {{-- NOTES --}}

        @if($donation->notes)

            <div class="mt-3">

                <label class="requisition-field-label">
                    Notes
                </label>

                <div
                    class="requisition-input"
                    style="
                        min-height:50px;
                        white-space:normal;
                        line-height:1.5;
                    "
                >
                    {{ $donation->notes }}
                </div>

            </div>

        @endif

    </div>

</div>


{{-- ============================================================
     PENDING COMMUNICATIONS

     IMPORTANT:
     This section has an ID because JavaScript below periodically
     fetches the current page and replaces ONLY this section.
     ============================================================ --}}

<div id="donationCommunicationsSection">

    @if($pendingCommunications->count())

        <div class="requisition-section">

            <div class="requisition-section-header">

                <div class="requisition-section-heading">

                    <div class="requisition-section-icon">
                        <i class="fa fa-clock"></i>
                    </div>

                    <div>

                        <h5>
                            Pending Thank-You Communications
                        </h5>

                        <p>
                            Scheduled donor communications waiting to be sent.
                        </p>

                    </div>

                </div>

                <div class="requisition-count-badge">
                    {{ $pendingCommunications->count() }}
                </div>

            </div>


            <div class="requisition-details-body">

                @foreach($pendingCommunications as $communication)

                    <div
                        style="
                            display:flex;
                            align-items:center;
                            justify-content:space-between;
                            gap:12px;
                            padding:10px 12px;
                            margin-bottom:8px;
                            border:1px solid #e5eaf1;
                            border-radius:7px;
                            background:#fff;
                        "
                    >

                        <div style="min-width:0;">

                            <div
                                style="
                                    font-size:13px;
                                    font-weight:600;
                                    color:#304967;
                                "
                            >

                                @if($communication->channel === 'sms')

                                    <i class="fa fa-mobile-alt mr-1"></i>
                                    SMS

                                @elseif($communication->channel === 'email')

                                    <i class="fa fa-envelope mr-1"></i>
                                    Email

                                @else

                                    {{ ucfirst($communication->channel) }}

                                @endif

                            </div>

                            <div
                                style="
                                    font-size:12px;
                                    color:#7a899d;
                                    margin-top:2px;
                                "
                            >
                                {{ $communication->recipient }}
                            </div>

                        </div>


                        <div
                            style="
                                display:flex;
                                align-items:center;
                                gap:10px;
                                flex-shrink:0;
                            "
                        >

                            <div
                                class="communication-countdown"
                                data-scheduled-at="{{ optional($communication->scheduled_at)->toIso8601String() }}"
                                style="
                                    font-size:12px;
                                    font-weight:600;
                                    color:#b36b00;
                                    white-space:nowrap;
                                "
                            >
                                Calculating...
                            </div>


                            <form
                                method="POST"
                                action="{{ route('admin.donation-communications.cancel', $communication) }}"
                            >

                                @csrf

                                @method('PATCH')

                                <button
                                    type="submit"
                                    class="requisition-list-action danger"
                                    title="Cancel scheduled communication"
                                    onclick="return confirm('Cancel this scheduled communication?')"
                                >
                                    <i class="fa fa-times"></i>
                                </button>

                            </form>

                        </div>

                    </div>

                @endforeach

            </div>

        </div>

    @endif

</div>


{{-- ============================================================
     MAIN CONTENT
     ============================================================ --}}

<div
    style="
        display:grid;
        grid-template-columns:minmax(0, 1fr) 320px;
        gap:14px;
        align-items:start;
    "
    class="donation-show-layout"
>

    {{-- ========================================================
         LEFT COLUMN
         ======================================================== --}}

    <div>

        {{-- ====================================================
             DONATION DETAILS
             ==================================================== --}}

        <div class="requisition-section">

            <div class="requisition-section-header">

                <div class="requisition-section-heading">

                    <div class="requisition-section-icon">
                        <i class="fa fa-info-circle"></i>
                    </div>

                    <div>

                        <h5>
                            Donation Details
                        </h5>

                        <p>
                            Complete information recorded for this donation.
                        </p>

                    </div>

                </div>

            </div>


            <div class="requisition-details-body">

                <div class="row">

                    {{-- DONATION NUMBER --}}

                    <div class="col-md-3 mb-3">

                        <label class="requisition-field-label">
                            Donation Number
                        </label>

                        <div class="requisition-input">
                            {{ $donation->donation_number }}
                        </div>

                    </div>


                    {{-- DATE --}}

                    <div class="col-md-3 mb-3">

                        <label class="requisition-field-label">
                            Date
                        </label>

                        <div class="requisition-input">
                            {{ optional($donation->donation_date)->format('d M Y') }}
                        </div>

                    </div>


                    {{-- TYPE --}}

                    <div class="col-md-3 mb-3">

                        <label class="requisition-field-label">
                            Type
                        </label>

                        <div class="requisition-input">
                            {{ ucfirst(str_replace('_', ' ', $donation->type)) }}
                        </div>

                    </div>


                    {{-- PURPOSE --}}

                    <div class="col-md-3 mb-3">

                        <label class="requisition-field-label">
                            Purpose
                        </label>

                        <div class="requisition-input">
                            {{ $donation->purpose ?: '—' }}
                        </div>

                    </div>


                    @if($isCash)

                        {{-- SOURCE --}}

                        <div class="col-md-3 mb-3">

                            <label class="requisition-field-label">
                                Source
                            </label>

                            <div class="requisition-input">
                                {{ ucfirst($donation->source ?? '—') }}
                            </div>

                        </div>


                        {{-- CURRENCY --}}

                        <div class="col-md-3 mb-3">

                            <label class="requisition-field-label">
                                Currency
                            </label>

                            <div class="requisition-input">
                                {{ $donation->currency ?? 'KES' }}
                            </div>

                        </div>


                        {{-- REFERENCE --}}

                        <div class="col-md-3 mb-3">

                            <label class="requisition-field-label">
                                Reference
                            </label>

                            <div class="requisition-input">
                                {{ $donation->reference ?: '—' }}
                            </div>

                        </div>


                        {{-- PAYMENT REFERENCE --}}

                        <div class="col-md-3 mb-3">

                            <label class="requisition-field-label">
                                Payment Reference
                            </label>

                            <div class="requisition-input">
                                {{ $donation->payment_reference ?: '—' }}
                            </div>

                        </div>

                    @endif

                </div>


                {{-- DESCRIPTION --}}

                @if($donation->description)

                    <div class="mt-2">

                        <label class="requisition-field-label">
                            Description
                        </label>

                        <div
                            class="requisition-input"
                            style="
                                min-height:60px;
                                white-space:normal;
                                line-height:1.5;
                            "
                        >
                            {{ $donation->description }}
                        </div>

                    </div>

                @endif


                {{-- NOTES --}}

                @if($donation->notes)

                    <div class="mt-3">

                        <label class="requisition-field-label">
                            Notes
                        </label>

                        <div
                            class="requisition-input"
                            style="
                                min-height:60px;
                                white-space:normal;
                                line-height:1.5;
                            "
                        >
                            {{ $donation->notes }}
                        </div>

                    </div>

                @endif

            </div>

        </div>


        {{-- ====================================================
             IN-KIND ITEMS
             ==================================================== --}}

        @if($isInKind && $donation->items->count())

            <div class="requisition-section">

                <div class="requisition-section-header">

                    <div class="requisition-section-heading">

                        <div class="requisition-section-icon">
                            <i class="fa fa-gift"></i>
                        </div>

                        <div>

                            <h5>
                                In-Kind Items
                            </h5>

                            <p>

                                {{ $donation->items->count() }}
                                item{{ $donation->items->count() == 1 ? '' : 's' }}

                                @if($totalEstimatedValue > 0)

                                    &nbsp; • &nbsp;

                                    Estimated:

                                    <strong>
                                        KES
                                        {{ number_format($totalEstimatedValue, 2) }}
                                    </strong>

                                @endif

                            </p>

                        </div>

                    </div>


                    <div class="requisition-count-badge">

                        {{ $donation->items->count() }}

                        {{ $donation->items->count() == 1 ? 'Item' : 'Items' }}

                    </div>

                </div>


                <div class="requisition-list-table-wrapper">

                    <table class="requisition-list-table">

                        <thead>

                            <tr>

                                <th>
                                    #
                                </th>

                                <th>
                                    Item
                                </th>

                                <th>
                                    Qty
                                </th>

                                <th>
                                    Unit
                                </th>

                                <th>
                                    Condition
                                </th>

                                <th>
                                    Estimated Value
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($donation->items as $item)

                                <tr>

                                    <td class="requisition-list-row-number">
                                        {{ $loop->iteration }}
                                    </td>


                                    <td>

                                        <div class="requisition-list-number">
                                            {{ $item->item }}
                                        </div>

                                        @if($item->notes)

                                            <div class="requisition-list-meta">
                                                {{ $item->notes }}
                                            </div>

                                        @endif

                                    </td>


                                    <td>
                                        {{ number_format((float) $item->quantity, 2) }}
                                    </td>


                                    <td>
                                        {{ $item->unit ?: '—' }}
                                    </td>


                                    <td>

                                        @if($item->condition)

                                            <span
                                                class="requisition-list-status
                                                @if(strtolower($item->condition) === 'good')
                                                    requisition-list-status-approved
                                                @elseif(strtolower($item->condition) === 'damaged')
                                                    requisition-list-status-rejected
                                                @else
                                                    requisition-list-status-draft
                                                @endif"
                                            >

                                                <span class="requisition-list-status-dot"></span>

                                                {{ $item->condition }}

                                            </span>

                                        @else

                                            —

                                        @endif

                                    </td>


                                    <td>

                                        @if($item->estimated_value)

                                            KES
                                            {{ number_format((float) $item->estimated_value, 2) }}

                                        @else

                                            —

                                        @endif

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        @endif

    </div>


    {{-- ========================================================
         RIGHT COLUMN
         ======================================================== --}}

    <div>

        {{-- ====================================================
             DONOR
             ==================================================== --}}

        <div class="requisition-section">

            <div class="requisition-section-header">

                <div class="requisition-section-heading">

                    <div class="requisition-section-icon">
                        <i class="fa fa-user"></i>
                    </div>

                    <div>

                        <h5>
                            Donor
                        </h5>

                        <p>
                            Donor information
                        </p>

                    </div>

                </div>

            </div>


            <div class="requisition-details-body">

                @if($donation->donor)

                    <div
                        style="
                            display:flex;
                            align-items:center;
                            gap:10px;
                            margin-bottom:14px;
                        "
                    >

                        <div
                            style="
                                width:42px;
                                height:42px;
                                min-width:42px;
                                border-radius:50%;
                                display:flex;
                                align-items:center;
                                justify-content:center;
                                background:#eef4ff;
                                color:#00096A;
                                font-size:17px;
                                font-weight:600;
                            "
                        >
                            {{ strtoupper(substr($donation->donor->name, 0, 1)) }}
                        </div>


                        <div style="min-width:0;">

                            <div
                                style="
                                    font-size:15px;
                                    font-weight:600;
                                    color:#304967;
                                "
                            >
                                {{ $donation->donor->name }}
                            </div>

                            @if($donation->donor->donor_number)

                                <div
                                    style="
                                        font-size:12px;
                                        color:#7a899d;
                                        margin-top:2px;
                                    "
                                >
                                    {{ $donation->donor->donor_number }}
                                </div>

                            @endif

                        </div>

                    </div>


                    @if($donation->donor->organization)

                        <div class="mb-2">

                            <label class="requisition-field-label">
                                Organization
                            </label>

                            <div class="requisition-input">
                                {{ $donation->donor->organization }}
                            </div>

                        </div>

                    @endif


                    @if($donation->donor->phone)

                        <div class="mb-2">

                            <label class="requisition-field-label">
                                Phone
                            </label>

                            <div class="requisition-input">
                                {{ $donation->donor->phone }}
                            </div>

                        </div>

                    @endif


                    @if($donation->donor->email)

                        <div class="mb-2">

                            <label class="requisition-field-label">
                                Email
                            </label>

                            <div class="requisition-input">
                                {{ $donation->donor->email }}
                            </div>

                        </div>

                    @endif


                    <a
                        href="{{ route('admin.donors.show', $donation->donor) }}"
                        class="requisition-add-button"
                        style="
                            display:flex;
                            align-items:center;
                            justify-content:center;
                            width:100%;
                            margin-top:12px;
                            text-decoration:none;
                        "
                    >
                        <i class="fa fa-user mr-1"></i>
                        View Donor
                    </a>

                @else

                    <div
                        style="
                            padding:10px 0;
                            color:#7a899d;
                            font-size:13px;
                        "
                    >
                        <i class="fa fa-user-slash mr-1"></i>
                        No donor linked to this donation.
                    </div>

                @endif

            </div>

        </div>


        {{-- ====================================================
             RECORD
             ==================================================== --}}

        <div class="requisition-section">

            <div class="requisition-section-header">

                <div class="requisition-section-heading">

                    <div class="requisition-section-icon">
                        <i class="fa fa-history"></i>
                    </div>

                    <div>

                        <h5>
                            Record
                        </h5>

                        <p>
                            Record information
                        </p>

                    </div>

                </div>

            </div>


            <div class="requisition-details-body">

                <div class="mb-3">

                    <label class="requisition-field-label">
                        Received By
                    </label>

                    <div class="requisition-input">
                        {{ optional($donation->receivedBy)->name ?? '—' }}
                    </div>

                </div>


                <div class="mb-3">

                    <label class="requisition-field-label">
                        Created
                    </label>

                    <div class="requisition-input">
                        {{ optional($donation->created_at)->format('d M Y H:i') }}
                    </div>

                </div>


                <div>

                    <label class="requisition-field-label">
                        Updated
                    </label>

                    <div class="requisition-input">
                        {{ optional($donation->updated_at)->format('d M Y H:i') }}
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


</div>

{{-- ================================================================
RESPONSIVE LAYOUT
================================================================ --}}

<style>
@media (max-width: 991px) {

    .donation-show-layout {
        grid-template-columns: 1fr !important;
    }

}

@media (max-width: 767px) {

    .requisition-header-right {
        width: 100%;
        justify-content: flex-start;
    }

    .requisition-header-right > * {
        margin-bottom: 4px;
    }

}
</style>

{{-- ================================================================
DONATION COMMUNICATION + COUNTDOWN MONITOR
================================================================ --}}

<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
     * How often the page checks the server for communication changes.
     *
     * 10 seconds gives us a reasonably quick update without repeatedly
     * hammering the server.
     */
    const communicationCheckInterval = 10000;


    /*
     * ---------------------------------------------------------------
     * COUNTDOWN
     * ---------------------------------------------------------------
     */

    function updateCountdowns() {

        document
            .querySelectorAll('.communication-countdown')
            .forEach(function (element) {

                const scheduledAt =
                    element.dataset.scheduledAt;

                if (!scheduledAt) {
                    return;
                }

                const scheduledTime =
                    new Date(scheduledAt).getTime();

                const now =
                    new Date().getTime();

                const difference =
                    scheduledTime - now;

                if (difference <= 0) {

                    element.textContent =
                        'Sending soon...';

                    return;
                }

                const totalSeconds =
                    Math.floor(difference / 1000);

                const hours =
                    Math.floor(totalSeconds / 3600);

                const minutes =
                    Math.floor(
                        (totalSeconds % 3600) / 60
                    );

                const seconds =
                    totalSeconds % 60;

                if (hours > 0) {

                    element.textContent =
                        'Sending in ' +
                        hours +
                        'h ' +
                        minutes +
                        'm';

                } else {

                    element.textContent =
                        'Sending in ' +
                        minutes +
                        'm ' +
                        seconds +
                        's';

                }

            });
    }


    /*
     * ---------------------------------------------------------------
     * REFRESH COMMUNICATION SECTION
     * ---------------------------------------------------------------
     *
     * We deliberately fetch the existing page rather than creating
     * another controller route.
     *
     * The server therefore returns the same current communication
     * status that this page normally receives.
     *
     * Only #donationCommunicationsSection is replaced.
     *
     * The rest of the page is NOT refreshed.
     * ---------------------------------------------------------------
     */

    let communicationCheckInProgress = false;


    async function checkCommunicationStatus() {

        /*
         * Do not make another request if the previous one is still
         * running.
         */
        if (communicationCheckInProgress) {
            return;
        }


        /*
         * Do not waste requests while the browser tab is hidden.
         */
        if (document.hidden) {
            return;
        }


        communicationCheckInProgress = true;


        try {

            const response = await fetch(
                window.location.href,
                {
                    method: 'GET',

                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'text/html'
                    },

                    cache: 'no-store'
                }
            );


            if (!response.ok) {
                return;
            }


            const html =
                await response.text();


            /*
             * Convert the returned page into a temporary DOM.
             */
            const parser =
                new DOMParser();

            const newDocument =
                parser.parseFromString(
                    html,
                    'text/html'
                );


            const currentSection =
                document.getElementById(
                    'donationCommunicationsSection'
                );

            const newSection =
                newDocument.getElementById(
                    'donationCommunicationsSection'
                );


            /*
             * If the current page has a communications section and
             * the refreshed page does not, all communications have
             * completed/cancelled. Remove the section.
             */
            if (currentSection && !newSection) {

                currentSection.remove();

                return;
            }


            /*
             * If the page currently has no communications section
             * but a new pending communication has appeared, add it
             * before the main content.
             */
            if (!currentSection && newSection) {

                const mainLayout =
                    document.querySelector(
                        '.donation-show-layout'
                    );

                if (mainLayout) {

                    mainLayout.parentNode.insertBefore(
                        newSection,
                        mainLayout
                    );

                }

                return;
            }


            /*
             * If both sections exist, compare them.
             *
             * This means we only modify the DOM when the server
             * actually reports a change.
             */
            if (currentSection && newSection) {

                if (
                    currentSection.innerHTML.trim() !==
                    newSection.innerHTML.trim()
                ) {

                    currentSection.replaceWith(
                        newSection
                    );

                    /*
                     * Restart countdown immediately after replacing
                     * the communication section.
                     */
                    updateCountdowns();
                }

            }

        } catch (error) {

            /*
             * Background polling should never break the page.
             *
             * We intentionally do not display an error to the user.
             */
            console.debug(
                'Donation communication check failed.',
                error
            );

        } finally {

            communicationCheckInProgress = false;

        }

    }


    /*
     * ---------------------------------------------------------------
     * INITIAL COUNTDOWN
     * ---------------------------------------------------------------
     */

    updateCountdowns();


    /*
     * Update countdown every second.
     */
    setInterval(
        updateCountdowns,
        1000
    );


    /*
     * ---------------------------------------------------------------
     * BACKGROUND SERVER CHECK
     * ---------------------------------------------------------------
     *
     * Every 10 seconds we ask the server whether the communication
     * section has changed.
     */
    setInterval(
        checkCommunicationStatus,
        communicationCheckInterval
    );


    /*
     * ---------------------------------------------------------------
     * CHECK IMMEDIATELY WHEN USER RETURNS TO TAB
     * ---------------------------------------------------------------
     */

    document.addEventListener(
        'visibilitychange',
        function () {

            if (!document.hidden) {

                updateCountdowns();

                checkCommunicationStatus();

            }

        }
    );

});
</script>

@endsection
