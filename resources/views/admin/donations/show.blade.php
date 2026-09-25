@extends('layouts.admin')

@section('content')

@php
$isCash = $donation->type === 'cash';
$isInKind = $donation->type === 'in_kind';


$classificationClasses = [
    'donation'     => 'donation-status-success',
    'payment'      => 'donation-status-info',
    'refund'       => 'donation-status-warning',
    'other'        => 'donation-status-secondary',
    'unclassified' => 'donation-status-light',
];

$classificationClass =
    $classificationClasses[$donation->classification]
    ?? 'donation-status-light';

$totalEstimatedValue = $donation->items->sum(
    fn ($item) => (float) ($item->estimated_value ?? 0)
);

$pendingCommunications = $donation->communications
    ->where('status', 'pending')
    ->whereNotNull('scheduled_at')
    ->sortBy('scheduled_at');


@endphp

<style>
    /* =========================================================
       COMPACT DONATION DASHBOARD
       ========================================================= */

    .donation-page {
        width: 100%;
        max-width: 1500px;
        margin: 0 auto;
        padding: 0 0 12px;
    }

    .donation-topbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        margin-bottom: 8px;
    }

    .donation-breadcrumb {
        font-size: 9px;
        color: #94a3b8;
        letter-spacing: .05em;
        margin-bottom: 1px;
    }

    .donation-title {
        font-size: 16px;
        font-weight: 650;
        margin: 0;
        color: #172033;
    }

    .donation-actions {
        display: flex;
        gap: 5px;
        align-items: center;
    }

    .donation-actions .btn {
        border-radius: 6px;
        padding: 4px 8px;
        font-size: 10px;
    }


    /* =========================================================
       HERO
       ========================================================= */

    .donation-hero {
        position: relative;
        overflow: hidden;
        border-radius: 10px;
        padding: 13px 16px;
        margin-bottom: 8px;
        color: #fff;

        background:
            radial-gradient(
                circle at 85% 15%,
                rgba(255,255,255,.12),
                transparent 30%
            ),
            linear-gradient(
                135deg,
                #101828 0%,
                #172554 55%,
                #1e293b 100%
            );

        box-shadow: 0 5px 16px rgba(15, 23, 42, .09);
    }

    .donation-hero-content {
        position: relative;
        z-index: 1;
    }

    .donation-hero-label {
        font-size: 8px;
        text-transform: uppercase;
        letter-spacing: .12em;
        color: #94a3b8;
        margin-bottom: 3px;
    }

    .donation-amount {
        font-size: 25px;
        line-height: 1;
        font-weight: 700;
        letter-spacing: -.7px;
    }

    .donation-number {
        margin-top: 4px;
        font-size: 9px;
        color: #cbd5e1;
    }

    .donation-hero-right {
        text-align: right;
    }

    .donation-date {
        margin-top: 5px;
        font-size: 9px;
        color: #94a3b8;
    }


    /* =========================================================
       STATUS
       ========================================================= */

    .donation-status {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 7px;
        border-radius: 14px;
        font-size: 9px;
        font-weight: 600;
        white-space: nowrap;
    }

    .donation-status::before {
        content: '';
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: currentColor;
    }

    .donation-status-success {
        background: rgba(34, 197, 94, .12);
        color: #15803d;
    }

    .donation-status-info {
        background: rgba(59, 130, 246, .12);
        color: #2563eb;
    }

    .donation-status-warning {
        background: rgba(245, 158, 11, .13);
        color: #b45309;
    }

    .donation-status-secondary {
        background: rgba(100, 116, 139, .12);
        color: #475569;
    }

    .donation-status-light {
        background: #f1f5f9;
        color: #475569;
    }


    /* =========================================================
       CARDS
       ========================================================= */

    .donation-card {
        background: #fff;
        border: 1px solid #edf0f4;
        border-radius: 9px;
        overflow: hidden;
        height: 100%;
        box-shadow: 0 2px 10px rgba(15, 23, 42, .025);
    }

    .donation-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 12px;
        border-bottom: 1px solid #f1f3f5;
    }

    .donation-card-title {
        font-size: 10px;
        font-weight: 650;
        color: #334155;
    }

    .donation-card-body {
        padding: 10px 12px;
    }

    .donation-page .mb-3 {
        margin-bottom: 8px !important;
    }


    /* =========================================================
       DETAILS
       ========================================================= */

    .donation-details-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
    }

    .donation-detail {
        padding: 7px 10px;
        border-bottom: 1px solid #f3f4f6;
    }

    .donation-detail:nth-child(odd) {
        border-right: 1px solid #f3f4f6;
    }

    .donation-label {
        display: block;
        font-size: 7px;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #94a3b8;
        margin-bottom: 2px;
    }

    .donation-value {
        font-size: 10px;
        font-weight: 550;
        color: #334155;
        word-break: break-word;
    }


    /* =========================================================
       DONOR
       ========================================================= */

    .donor-profile {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .donor-avatar {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eef2ff;
        color: #3730a3;
        font-weight: 700;
        font-size: 11px;
        flex-shrink: 0;
    }

    .donor-name {
        font-size: 11px;
        font-weight: 650;
        color: #1e293b;
    }

    .donor-number {
        font-size: 8px;
        color: #94a3b8;
    }

    .donor-contact {
        margin-top: 7px;
    }

    .donor-contact-row {
        display: flex;
        justify-content: space-between;
        gap: 8px;
        padding: 5px 0;
        border-bottom: 1px solid #f3f4f6;
        font-size: 9px;
    }

    .donor-contact-row:last-child {
        border-bottom: 0;
    }

    .donor-contact-row span {
        color: #94a3b8;
    }

    .donor-contact-row strong {
        color: #475569;
        text-align: right;
        font-weight: 550;
        word-break: break-word;
    }


    /* =========================================================
       COMMUNICATIONS
       ========================================================= */

    .communication-card {
        border-radius: 7px;
        border: 1px solid #e7edf5;
        background: #f8fafc;
        padding: 7px 9px;
        margin-bottom: 5px;
    }

    .communication-card:last-child {
        margin-bottom: 0;
    }

    .communication-icon {
        width: 24px;
        height: 24px;
        border-radius: 7px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        color: #475569;
        box-shadow: 0 1px 4px rgba(0,0,0,.04);
        font-size: 10px;
    }

    .communication-title {
        font-size: 9px;
        font-weight: 650;
        color: #334155;
    }

    .communication-recipient {
        font-size: 8px;
        color: #94a3b8;
    }

    .communication-countdown {
        font-size: 9px;
        font-weight: 650;
        color: #b45309;
    }

    .communication-processing {
        font-size: 9px;
        color: #2563eb;
        font-weight: 600;
    }

    .communication-cancel-button {
        font-size: 8px !important;
    }


    /* =========================================================
       IN-KIND ITEMS
       ========================================================= */

    .items-summary {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 12px;
        background: #f8fafc;
        border-bottom: 1px solid #eef1f4;
    }

    /*
       Keep the item list compact.
       If there are many items, only this area scrolls.
    */
    .items-table-wrapper {
        max-height: 260px;
        overflow-y: auto;
        overflow-x: auto;
    }

    .items-table {
        font-size: 9px;
        margin-bottom: 0;
    }

    .items-table th {
        position: sticky;
        top: 0;
        z-index: 2;

        font-size: 7px;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #94a3b8;
        background: #fafbfc;
        border-bottom: 1px solid #edf0f4;
        padding: 6px 9px;
        white-space: nowrap;
    }

    .items-table td {
        padding: 6px 9px;
        vertical-align: middle;
        border-bottom: 1px solid #f3f4f6;
    }

    .items-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .items-table small {
        font-size: 7px;
    }


    /* =========================================================
       TEXT SECTIONS
       ========================================================= */

    .donation-note {
        padding: 8px 12px;
        border-top: 1px solid #f1f3f5;
    }

    .donation-note-text {
        margin: 0;
        color: #64748b;
        font-size: 9px;
        line-height: 1.4;
        white-space: pre-line;
    }


    /* =========================================================
       RECORD
       ========================================================= */

    .record-row {
        display: flex;
        justify-content: space-between;
        gap: 10px;
        padding: 6px 0;
        border-bottom: 1px solid #f1f3f5;
        font-size: 9px;
    }

    .record-row:last-child {
        border-bottom: 0;
    }

    .record-row span {
        color: #94a3b8;
    }

    .record-row strong {
        color: #475569;
        text-align: right;
        font-weight: 550;
    }


    /* =========================================================
       PAGE SCROLL / VIEWPORT
       ========================================================= */

    /*
       Never allow the donation page to be trapped inside
       a fixed-height container.
    */
    .donation-page {
        min-height: 0;
        overflow: visible;
    }

    .donation-page .row {
        min-height: 0;
    }

    /*
       On smaller screens the entire page remains scrollable.
    */
    @media (max-width: 991px) {
        .donation-page {
            padding-bottom: 20px;
        }

        .items-table-wrapper {
            max-height: 300px;
        }
    }


    /* =========================================================
       MOBILE
       ========================================================= */

    @media (max-width: 767px) {

        .donation-topbar {
            align-items: flex-start;
            flex-direction: column;
        }

        .donation-actions {
            width: 100%;
            flex-wrap: wrap;
        }

        .donation-actions .btn {
            flex: 1;
        }

        .donation-hero {
            padding: 12px;
        }

        .donation-amount {
            font-size: 23px;
        }

        .donation-hero-right {
            margin-top: 7px;
            text-align: left;
        }

        .donation-details-grid {
            grid-template-columns: 1fr;
        }

        .donation-detail:nth-child(odd) {
            border-right: 0;
        }

        .items-table-wrapper {
            max-height: 280px;
        }

        .items-table {
            min-width: 560px;
        }
    }
</style>

<div class="donation-page">


{{-- =========================================================
     TOP BAR
     ========================================================= --}}

<div class="donation-topbar">

    <div>
        <div class="donation-breadcrumb">
            DONATIONS / {{ $donation->donation_number }}
        </div>

        <h5 class="donation-title">
            Donation Overview
        </h5>
    </div>

    <div class="donation-actions">

        <a
            href="{{ route('admin.donations.index') }}"
            class="btn btn-light btn-sm"
        >
            <i class="fas fa-arrow-left me-1"></i>
            Back
        </a>

        <a
            href="{{ route('admin.donations.edit', $donation) }}"
            class="btn btn-primary btn-sm"
        >
            <i class="fas fa-edit me-1"></i>
            Edit
        </a>

        {{-- RESEND --}}

        @if(
            $donation->classification === 'donation'
            && $donation->donor
        )

            <div class="dropdown d-inline-block">

                <button
                    type="button"
                    class="btn btn-success btn-sm dropdown-toggle"
                    data-toggle="dropdown"
                >
                    <i class="fas fa-paper-plane me-1"></i>
                    Thank-You
                </button>

                <div class="dropdown-menu dropdown-menu-right">

                    @if($donation->donor->phone)

                        <form
                            method="POST"
                            action="{{ route(
                                'admin.donations.resend-thank-you',
                                $donation
                            ) }}"
                        >
                            @csrf

                            <input
                                type="hidden"
                                name="channel"
                                value="sms"
                            >

                            <button
                                type="submit"
                                class="dropdown-item"
                                onclick="return confirm(
                                    'Schedule a new thank-you SMS?'
                                )"
                            >
                                <i class="fas fa-sms mr-2"></i>
                                Resend SMS
                            </button>

                        </form>

                    @endif


                    @if($donation->donor->email)

                        <form
                            method="POST"
                            action="{{ route(
                                'admin.donations.resend-thank-you',
                                $donation
                            ) }}"
                        >
                            @csrf

                            <input
                                type="hidden"
                                name="channel"
                                value="email"
                            >

                            <button
                                type="submit"
                                class="dropdown-item"
                                onclick="return confirm(
                                    'Schedule a new thank-you email?'
                                )"
                            >
                                <i class="fas fa-envelope mr-2"></i>
                                Resend Email
                            </button>

                        </form>

                    @endif


                    @if(
                        $donation->donor->phone
                        && $donation->donor->email
                    )

                        <div class="dropdown-divider"></div>

                        <form
                            method="POST"
                            action="{{ route(
                                'admin.donations.resend-thank-you',
                                $donation
                            ) }}"
                        >
                            @csrf

                            <input
                                type="hidden"
                                name="channel"
                                value="both"
                            >

                            <button
                                type="submit"
                                class="dropdown-item"
                                onclick="return confirm(
                                    'Schedule a new thank-you SMS and email?'
                                )"
                            >
                                <i class="fas fa-paper-plane mr-2"></i>
                                Resend Both
                            </button>

                        </form>

                    @endif

                </div>
            </div>

        @endif

    </div>

</div>


<x-message></x-message>


{{-- =========================================================
     HERO
     ========================================================= --}}

<div class="donation-hero">

    <div class="donation-hero-content">

        <div class="row align-items-center">

            <div class="col-md-8">

                <div class="donation-hero-label">
                    {{ $isCash ? 'Cash Donation' : 'In-Kind Donation' }}
                </div>

                <div class="donation-amount">
                    {{ $donation->currency }}
                    {{ number_format($donation->amount ?? 0, 2) }}
                </div>

                <div class="donation-number">
                    {{ $donation->donation_number }}
                    ·
                    {{ $donation->donation_date?->format('d M Y') }}
                </div>

            </div>

            <div class="col-md-4 donation-hero-right">

                <span class="donation-status {{ $classificationClass }}">
                    {{ ucfirst($donation->classification) }}
                </span>

                @if($donation->source)

                    <div class="donation-date">
                        Source · {{ ucfirst($donation->source) }}
                    </div>

                @endif

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     COMMUNICATION COUNTDOWNS
     ========================================================= --}}

@if($pendingCommunications->count())

    <div class="donation-card mb-3">

        <div class="donation-card-header">

            <div class="donation-card-title">
                <i class="fas fa-bolt me-1"></i>
                Scheduled Communications
            </div>

            <span class="text-muted small">
                {{ $pendingCommunications->count() }}
            </span>

        </div>


        <div class="donation-card-body">

            @foreach($pendingCommunications as $communication)

                @php
                    $scheduledAt = $communication->scheduled_at;
                    $hasStarted =
                        $scheduledAt && !$scheduledAt->isFuture();
                @endphp

                <div
                    class="communication-card donation-communication-item"
                    id="donationCommunication-{{ $communication->id }}"
                    data-communication-id="{{ $communication->id }}"
                    data-scheduled-at="{{ $scheduledAt ? $scheduledAt->timestamp * 1000 : '' }}"
                >

                    <div class="d-flex justify-content-between align-items-center">

                        <div class="d-flex align-items-center">

                            <div class="communication-icon mr-2">

                                @if($communication->channel === 'sms')
                                    <i class="fas fa-sms"></i>

                                @elseif($communication->channel === 'email')
                                    <i class="fas fa-envelope"></i>

                                @else
                                    <i class="fas fa-paper-plane"></i>
                                @endif

                            </div>

                            <div>

                                <div class="communication-title communication-status-title">

                                    @if($communication->channel === 'sms')
                                        SMS Thank-you

                                    @elseif($communication->channel === 'email')
                                        Email Thank-you

                                    @else
                                        {{ ucfirst($communication->channel) }}
                                        Thank-you
                                    @endif

                                </div>

                                <div class="communication-recipient">
                                    {{ $communication->recipient }}
                                </div>

                            </div>

                        </div>


                        <div class="text-end">

                            @if($hasStarted)

                                <div class="communication-processing">
                                    <i class="fas fa-spinner fa-spin"></i>
                                    Processing
                                </div>

                            @else

                                <div class="communication-countdown-text">
                                    <span class="communication-countdown">
                                        --
                                    </span>
                                    sec
                                </div>

                            @endif


                            <form
                                method="POST"
                                action="{{ route(
                                    'admin.donation-communications.cancel',
                                    $communication
                                ) }}"
                                class="communication-cancel-form mt-1"
                                onsubmit="return confirm(
                                    'Cancel this ' +
                                    '{{ strtoupper($communication->channel) }}' +
                                    ' message?'
                                )"
                            >

                                @csrf

                                <button
                                    type="submit"
                                    class="btn btn-link btn-sm text-danger p-0 communication-cancel-button"
                                    @if($hasStarted) disabled @endif
                                >
                                    @if($hasStarted)
                                        Processing...
                                    @else
                                        Cancel
                                    @endif
                                </button>

                            </form>

                        </div>

                    </div>

                </div>

            @endforeach

        </div>

    </div>


    <script>

        document.addEventListener('DOMContentLoaded', function () {

            const communicationItems =
                document.querySelectorAll(
                    '.donation-communication-item'
                );

            communicationItems.forEach(function (item) {

                const scheduledAt =
                    parseInt(
                        item.dataset.scheduledAt,
                        10
                    );

                const countdownElement =
                    item.querySelector(
                        '.communication-countdown'
                    );

                const cancelButton =
                    item.querySelector(
                        '.communication-cancel-button'
                    );

                if (isNaN(scheduledAt)) {
                    return;
                }

                let timer = null;

                function showProcessingState() {

                    if (timer) {
                        clearInterval(timer);
                        timer = null;
                    }

                    if (countdownElement) {
                        countdownElement.innerHTML =
                            '<i class="fas fa-spinner fa-spin"></i> Processing';
                    }

                    if (cancelButton) {
                        cancelButton.disabled = true;
                        cancelButton.textContent =
                            'Processing...';
                    }
                }

                function updateCountdown() {

                    const remainingMilliseconds =
                        scheduledAt - Date.now();

                    const remainingSeconds =
                        Math.max(
                            0,
                            Math.ceil(
                                remainingMilliseconds / 1000
                            )
                        );

                    if (countdownElement) {
                        countdownElement.textContent =
                            remainingSeconds;
                    }

                    if (remainingMilliseconds <= 0) {
                        showProcessingState();
                    }
                }

                if (Date.now() >= scheduledAt) {
                    showProcessingState();
                    return;
                }

                updateCountdown();

                timer = setInterval(
                    updateCountdown,
                    1000
                );

            });

        });

    </script>

@endif


{{-- =========================================================
     MAIN CONTENT
     ========================================================= --}}

<div class="row" style="margin-left:-4px;margin-right:-4px;">

    {{-- =====================================================
         LEFT
         ===================================================== --}}

    <div
        class="col-lg-8"
        style="padding-left:4px;padding-right:4px;"
    >

        {{-- Donation Details --}}

        <div class="donation-card mb-3">

            <div class="donation-card-header">

                <span class="donation-card-title">
                    Donation Details
                </span>

                <span class="text-muted small">
                    {{ $isCash ? 'Cash' : 'In-Kind' }}
                </span>

            </div>


            <div class="donation-details-grid">

                <div class="donation-detail">
                    <span class="donation-label">
                        Donation Number
                    </span>

                    <span class="donation-value">
                        {{ $donation->donation_number }}
                    </span>
                </div>


                <div class="donation-detail">
                    <span class="donation-label">
                        Date
                    </span>

                    <span class="donation-value">
                        {{ $donation->donation_date?->format('d M Y') }}
                    </span>
                </div>


                <div class="donation-detail">
                    <span class="donation-label">
                        Type
                    </span>

                    <span class="donation-value">
                        {{ $isCash ? 'Cash' : 'In-Kind' }}
                    </span>
                </div>


                <div class="donation-detail">
                    <span class="donation-label">
                        Purpose
                    </span>

                    <span class="donation-value">
                        {{ $donation->purpose ?: '—' }}
                    </span>
                </div>


                @if($isCash)

                    <div class="donation-detail">
                        <span class="donation-label">
                            Source
                        </span>

                        <span class="donation-value">
                            {{ ucfirst($donation->source) }}
                        </span>
                    </div>


                    <div class="donation-detail">
                        <span class="donation-label">
                            Currency
                        </span>

                        <span class="donation-value">
                            {{ $donation->currency }}
                        </span>
                    </div>


                    <div class="donation-detail">
                        <span class="donation-label">
                            Reference
                        </span>

                        <span class="donation-value">
                            {{ $donation->reference ?: '—' }}
                        </span>
                    </div>


                    <div class="donation-detail">
                        <span class="donation-label">
                            Payment Reference
                        </span>

                        <span class="donation-value">
                            {{ $donation->payment_reference ?: '—' }}
                        </span>
                    </div>

                @endif

            </div>


            @if($donation->description)

                <div class="donation-note">

                    <span class="donation-label">
                        Description
                    </span>

                    <p class="donation-note-text">
                        {{ $donation->description }}
                    </p>

                </div>

            @endif


            @if($donation->notes)

                <div class="donation-note">

                    <span class="donation-label">
                        Notes
                    </span>

                    <p class="donation-note-text">
                        {{ $donation->notes }}
                    </p>

                </div>

            @endif

        </div>


        {{-- =================================================
             IN-KIND ITEMS
             ================================================= --}}

        @if($isInKind && $donation->items->count())

            <div class="donation-card">

                <div class="items-summary">

                    <div>

                        <div class="donation-card-title">
                            Items Received
                        </div>

                        <div class="text-muted"
                             style="font-size:8px;">
                            {{ $donation->items->count() }}
                            {{ $donation->items->count() === 1 ? 'item' : 'items' }}
                        </div>

                    </div>


                    <div class="text-end">

                        <span class="donation-label">
                            Estimated Value
                        </span>

                        <strong style="font-size:9px;">
                            {{ $donation->currency }}
                            {{ number_format($totalEstimatedValue, 2) }}
                        </strong>

                    </div>

                </div>


                <div class="items-table-wrapper">

                    <table class="table items-table">

                        <thead>

                            <tr>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Unit</th>
                                <th>Condition</th>
                                <th class="text-end">
                                    Value
                                </th>
                            </tr>

                        </thead>


                        <tbody>

                            @foreach($donation->items as $item)

                                <tr>

                                    <td>

                                        {{ $item->item }}

                                        @if($item->notes)

                                            <small class="d-block text-muted">
                                                {{ $item->notes }}
                                            </small>

                                        @endif

                                    </td>


                                    <td>

                                        {{ rtrim(
                                            rtrim(
                                                number_format(
                                                    $item->quantity,
                                                    2
                                                ),
                                                '0'
                                            ),
                                            '.'
                                        ) }}

                                    </td>


                                    <td>
                                        {{ $item->unit ?: '—' }}
                                    </td>


                                    <td>
                                        {{ $item->condition ?: '—' }}
                                    </td>


                                    <td class="text-end">

                                        @if($item->estimated_value !== null)

                                            {{ $donation->currency }}
                                            {{ number_format(
                                                $item->estimated_value,
                                                2
                                            ) }}

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


    {{-- =====================================================
         RIGHT
         ===================================================== --}}

    <div
        class="col-lg-4"
        style="padding-left:4px;padding-right:4px;"
    >

        {{-- Donor --}}

        <div class="donation-card mb-3">

            <div class="donation-card-header">

                <span class="donation-card-title">
                    Donor
                </span>

            </div>


            <div class="donation-card-body">

                @if($donation->donor)

                    @php

                        $initials = collect(
                            preg_split(
                                '/\s+/',
                                trim($donation->donor->name)
                            )
                        )
                        ->filter()
                        ->take(2)
                        ->map(
                            fn($name) =>
                                strtoupper(
                                    substr($name, 0, 1)
                                )
                        )
                        ->implode('');

                    @endphp


                    <div class="donor-profile">

                        <div class="donor-avatar">
                            {{ $initials ?: 'D' }}
                        </div>

                        <div>

                            <div class="donor-name">
                                {{ $donation->donor->name }}
                            </div>

                            <div class="donor-number">
                                {{ $donation->donor->donor_number }}
                            </div>

                        </div>

                    </div>


                    <div class="donor-contact">

                        @if($donation->donor->organization)

                            <div class="donor-contact-row">

                                <span>
                                    Organization
                                </span>

                                <strong>
                                    {{ $donation->donor->organization }}
                                </strong>

                            </div>

                        @endif


                        @if($donation->donor->phone)

                            <div class="donor-contact-row">

                                <span>
                                    Phone
                                </span>

                                <strong>
                                    {{ $donation->donor->phone }}
                                </strong>

                            </div>

                        @endif


                        @if($donation->donor->email)

                            <div class="donor-contact-row">

                                <span>
                                    Email
                                </span>

                                <strong>
                                    {{ $donation->donor->email }}
                                </strong>

                            </div>

                        @endif

                    </div>


                    <a
                        href="{{ route(
                            'admin.donors.show',
                            $donation->donor
                        ) }}"
                        class="btn btn-light btn-sm w-100 mt-2"
                        style="font-size:9px;padding:4px 7px;"
                    >
                        View Donor
                    </a>

                @else

                    <div class="text-muted"
                         style="font-size:9px;">
                        Anonymous / Not Specified
                    </div>

                @endif

            </div>

        </div>


        {{-- Record Information --}}

        <div class="donation-card">

            <div class="donation-card-header">

                <span class="donation-card-title">
                    Record
                </span>

            </div>


            <div class="donation-card-body">

                <div class="record-row">

                    <span>
                        Received By
                    </span>

                    <strong>
                        {{ $donation->receivedBy?->name ?? 'Not specified' }}
                    </strong>

                </div>


                <div class="record-row">

                    <span>
                        Created
                    </span>

                    <strong>
                        {{ $donation->created_at?->format('d M Y H:i') }}
                    </strong>

                </div>


                <div class="record-row">

                    <span>
                        Updated
                    </span>

                    <strong>
                        {{ $donation->updated_at?->format('d M Y H:i') }}
                    </strong>

                </div>

            </div>

        </div>

    </div>

</div>


</div>

@endsection
