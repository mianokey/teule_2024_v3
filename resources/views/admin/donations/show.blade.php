@extends('layouts.admin')

@section('content')

@php

$isCash = $donation->type === 'cash';

$isInKind = $donation->type === 'in_kind';

$classificationClasses = [
    'donation'     => 'badge-success',
    'payment'      => 'badge-info',
    'refund'       => 'badge-warning',
    'other'        => 'badge-secondary',
    'unclassified' => 'badge-light',
];

$classificationClass =
    $classificationClasses[$donation->classification]
    ?? 'badge-light';

$totalEstimatedValue = $donation->items->sum(
    fn ($item) => (float) ($item->estimated_value ?? 0)
);

$pendingCommunications = $donation->communications
    ->where('status', 'pending')
    ->whereNotNull('scheduled_at')
    ->sortBy('scheduled_at');


@endphp

<div class="donation-page">

{{-- =========================================================
     COMMUNICATION COUNTDOWNS
     ========================================================== --}}

@if($pendingCommunications->count())

    @foreach($pendingCommunications as $communication)

        @php
            $scheduledAt = $communication->scheduled_at;
            $hasStarted = $scheduledAt && !$scheduledAt->isFuture();
        @endphp

        <div
            class="alert {{ $hasStarted ? 'alert-info' : 'alert-warning' }} mb-3 donation-communication-item"
            id="donationCommunication-{{ $communication->id }}"
            data-communication-id="{{ $communication->id }}"
            data-scheduled-at="{{ $scheduledAt ? $scheduledAt->timestamp * 1000 : '' }}"
            role="alert"
        >

            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

                <div>

                    <strong class="communication-status-title">

                        @if($communication->channel === 'sms')
                            SMS Thank-you
                        @elseif($communication->channel === 'email')
                            Email Thank-you
                        @else
                            {{ ucfirst($communication->channel) }} Thank-you
                        @endif

                        @if($hasStarted)
                            is being processed...
                        @else
                            scheduled
                        @endif

                    </strong>


                    <div class="small mt-1 communication-countdown-area">

                        @if($hasStarted)

                            <span class="communication-processing-text">
                                Processing automatically...
                            </span>

                        @else

                            <span class="communication-countdown-text">

                                Sending automatically in

                                <strong>
                                    <span class="communication-countdown">--</span>
                                    seconds
                                </strong>.

                            </span>

                        @endif

                    </div>


                    <div class="small text-muted mt-1">

                        Recipient:
                        {{ $communication->recipient }}

                    </div>

                </div>


                <div>

                    <form
                        method="POST"
                        action="{{ route(
                            'admin.donation-communications.cancel',
                            $communication
                        ) }}"
                        class="communication-cancel-form"
                        onsubmit="return confirm(
                            'Cancel this {{ strtoupper($communication->channel) }} message?'
                        )"
                    >

                        @csrf

                        <button
                            type="submit"
                            class="btn btn-danger btn-sm communication-cancel-button"
                            @if($hasStarted) disabled @endif
                        >

                            <i class="fas fa-times me-1"></i>

                            @if($hasStarted)
                                Processing...
                            @else
                                Cancel {{ strtoupper($communication->channel) }}
                            @endif

                        </button>

                    </form>

                </div>

            </div>

        </div>

    @endforeach


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


                const countdownText =
                    item.querySelector(
                        '.communication-countdown-text'
                    );


                const processingText =
                    item.querySelector(
                        '.communication-processing-text'
                    );


                const statusTitle =
                    item.querySelector(
                        '.communication-status-title'
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


                    item.classList.remove(
                        'alert-warning'
                    );

                    item.classList.add(
                        'alert-info'
                    );


                    if (statusTitle) {

                        let title =
                            statusTitle.textContent.trim();

                        title = title
                            .replace(
                                ' scheduled',
                                ''
                            )
                            .replace(
                                ' is being processed...',
                                ''
                            );


                        statusTitle.textContent =
                            title +
                            ' is being processed...';

                    }


                    if (countdownText) {

                        countdownText.style.display =
                            'none';

                    }


                    if (processingText) {

                        processingText.style.display =
                            'inline';

                    } else {

                        const countdownArea =
                            item.querySelector(
                                '.communication-countdown-area'
                            );


                        if (countdownArea) {

                            countdownArea.textContent =
                                'Processing automatically...';

                        }

                    }


                    if (cancelButton) {

                        cancelButton.disabled = true;

                        cancelButton.innerHTML =
                            '<i class="fas fa-spinner me-1"></i> Processing...';

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
     HEADER
     ========================================================== --}}

<div class="page-header">

    <div>

        <div class="text-muted small mb-1">

            Donations /
            {{ $donation->donation_number }}

        </div>

        <h5 class="mb-0">
            Donation Details
        </h5>

    </div>


    <div class="page-actions">

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


        {{-- =================================================
             RESEND THANK-YOU
             ================================================== --}}

        @if(
            $donation->classification === 'donation'
            && $donation->donor
        )

            <div class="dropdown d-inline-block">

                <button
                    type="button"
                    class="btn btn-success btn-sm dropdown-toggle"
                    data-toggle="dropdown"
                    aria-haspopup="true"
                    aria-expanded="false"
                >

                    <i class="fas fa-paper-plane me-1"></i>
                    Resend Thank-You

                </button>


                <div class="dropdown-menu dropdown-menu-right">

                    {{-- SMS --}}

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


                    {{-- EMAIL --}}

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


                    {{-- BOTH --}}

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


<div class="row">

    {{-- =====================================================
         MAIN DETAILS
         ====================================================== --}}

    <div class="col-lg-8">

        <div class="simple-card">

            <div class="simple-card-header">

                <div>

                    <span class="text-muted small">

                        {{ $isCash
                            ? 'Cash Donation'
                            : 'In-Kind Donation'
                        }}

                    </span>


                    <h4 class="amount mb-0">

                        {{ $donation->currency }}

                        {{ number_format(
                            $donation->amount ?? 0,
                            2
                        ) }}

                    </h4>

                </div>


                <span
                    class="status-badge {{ $classificationClass }}"
                >

                    {{ ucfirst($donation->classification) }}

                </span>

            </div>


            {{-- Basic information --}}

            <div class="details-grid">

                <div>

                    <span class="label">
                        Donation Number
                    </span>

                    <span class="value">
                        {{ $donation->donation_number }}
                    </span>

                </div>


                <div>

                    <span class="label">
                        Date
                    </span>

                    <span class="value">

                        {{ $donation->donation_date?->format('d M Y') }}

                    </span>

                </div>


                <div>

                    <span class="label">
                        Type
                    </span>

                    <span class="value">

                        {{ $isCash ? 'Cash' : 'In-Kind' }}

                    </span>

                </div>


                <div>

                    <span class="label">
                        Purpose
                    </span>

                    <span class="value">

                        {{ $donation->purpose ?: '—' }}

                    </span>

                </div>


                @if($isCash)

                    <div>

                        <span class="label">
                            Source
                        </span>

                        <span class="value">

                            {{ ucfirst($donation->source) }}

                        </span>

                    </div>


                    <div>

                        <span class="label">
                            Currency
                        </span>

                        <span class="value">

                            {{ $donation->currency }}

                        </span>

                    </div>


                    <div>

                        <span class="label">
                            Reference
                        </span>

                        <span class="value">

                            {{ $donation->reference ?: '—' }}

                        </span>

                    </div>


                    <div>

                        <span class="label">
                            Payment Reference
                        </span>

                        <span class="value">

                            {{ $donation->payment_reference ?: '—' }}

                        </span>

                    </div>

                @endif

            </div>


            {{-- Description --}}

            @if($donation->description)

                <div class="text-section">

                    <span class="label">
                        Description
                    </span>

                    <p>
                        {{ $donation->description }}
                    </p>

                </div>

            @endif


            {{-- Notes --}}

            @if($donation->notes)

                <div class="text-section">

                    <span class="label">
                        Notes
                    </span>

                    <p>
                        {{ $donation->notes }}
                    </p>

                </div>

            @endif

        </div>


        {{-- =================================================
             IN-KIND ITEMS
             ================================================== --}}

        @if($isInKind && $donation->items->count())

            <div class="simple-card mt-3">

                <div class="simple-card-header">

                    <div>

                        <strong>
                            Items Received
                        </strong>

                        <div class="text-muted small">

                            {{ $donation->items->count() }}

                            {{ $donation->items->count() === 1
                                ? 'item'
                                : 'items'
                            }}

                        </div>

                    </div>


                    <div class="text-end">

                        <span class="label">
                            Total Estimated Value
                        </span>

                        <div class="fw-semibold">

                            {{ $donation->currency }}

                            {{ number_format(
                                $totalEstimatedValue,
                                2
                            ) }}

                        </div>

                    </div>

                </div>


                <div class="table-responsive">

                    <table class="table items-table mb-0">

                        <thead>

                            <tr>

                                <th>
                                    Item
                                </th>

                                <th>
                                    Quantity
                                </th>

                                <th>
                                    Unit
                                </th>

                                <th>
                                    Condition
                                </th>

                                <th class="text-end">
                                    Estimated Value
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($donation->items as $item)

                                <tr>

                                    <td>

                                        <div>
                                            {{ $item->item }}
                                        </div>

                                        @if($item->notes)

                                            <small class="text-muted">
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
         SIDEBAR
         ====================================================== --}}

    <div class="col-lg-4">

        {{-- Donor --}}

        <div class="simple-card mb-3">

            <div class="simple-card-header">

                <strong>
                    Donor
                </strong>

            </div>


            <div class="sidebar-body">

                @if($donation->donor)

                    <div class="donor-name">

                        {{ $donation->donor->name }}

                    </div>


                    <div class="text-muted small mb-3">

                        {{ $donation->donor->donor_number }}

                    </div>


                    @if($donation->donor->organization)

                        <div class="sidebar-row">

                            <span>
                                Organization
                            </span>

                            <strong>
                                {{ $donation->donor->organization }}
                            </strong>

                        </div>

                    @endif


                    @if($donation->donor->phone)

                        <div class="sidebar-row">

                            <span>
                                Phone
                            </span>

                            <strong>
                                {{ $donation->donor->phone }}
                            </strong>

                        </div>

                    @endif


                    @if($donation->donor->email)

                        <div class="sidebar-row">

                            <span>
                                Email
                            </span>

                            <strong>
                                {{ $donation->donor->email }}
                            </strong>

                        </div>

                    @endif


                    <a
                        href="{{ route(
                            'admin.donors.show',
                            $donation->donor
                        ) }}"
                        class="btn btn-light btn-sm w-100 mt-3"
                    >

                        View Donor

                    </a>

                @else

                    <div class="text-muted">

                        Anonymous / Not Specified

                    </div>

                @endif

            </div>

        </div>


        {{-- Record information --}}

        <div class="simple-card">

            <div class="simple-card-header">

                <strong>
                    Record Information
                </strong>

            </div>


            <div class="sidebar-body">

                <div class="sidebar-row">

                    <span>
                        Received By
                    </span>

                    <strong>

                        {{ $donation->receivedBy?->name
                            ?? 'Not specified'
                        }}

                    </strong>

                </div>


                <div class="sidebar-row">

                    <span>
                        Created
                    </span>

                    <strong>

                        {{ $donation->created_at?->format('d M Y H:i') }}

                    </strong>

                </div>


                <div class="sidebar-row">

                    <span>
                        Last Updated
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

<style>

    /* =========================================================
       DONATION PAGE
       ========================================================== */

    .donation-page {
        padding-bottom: 30px;
    }


    /* =========================================================
       COMMUNICATION COUNTDOWN
       ========================================================== */

    .donation-communication-item {
        border-radius: 7px;
    }


    .donation-communication-item
    .communication-status-title {
        font-size: 14px;
    }


    .communication-countdown {
        min-width: 24px;
        display: inline-block;
        text-align: center;
    }


    .communication-cancel-button:disabled {
        cursor: not-allowed;
        opacity: .7;
    }


    /* =========================================================
       HEADER
       ========================================================== */

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
    }


    .page-actions {
        display: flex;
        gap: 8px;
        align-items: center;
        flex-wrap: wrap;
    }


    /* =========================================================
       CARDS
       ========================================================== */

    .simple-card {
        background: #fff;
        border: 1px solid #e6e8eb;
        border-radius: 8px;
        overflow: hidden;
    }


    .simple-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        padding: 16px 20px;
        border-bottom: 1px solid #e9ecef;
    }


    .amount {
        margin-top: 4px;
        font-size: 25px;
        font-weight: 600;
    }


    /* =========================================================
       STATUS
       ========================================================== */

    .status-badge {
        display: inline-block;
        padding: 5px 9px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 500;
    }


    .badge-success {
        background: #e8f5e9;
        color: #2e7d32;
    }


    .badge-info {
        background: #e3f2fd;
        color: #1976d2;
    }


    .badge-warning {
        background: #fff8e1;
        color: #a36b00;
    }


    .badge-secondary {
        background: #f1f3f5;
        color: #495057;
    }


    .badge-light {
        background: #f8f9fa;
        color: #495057;
    }


    /* =========================================================
       DETAILS
       ========================================================== */

    .details-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
    }


    .details-grid > div {
        padding: 15px 20px;
        border-bottom: 1px solid #f0f1f2;
    }


    .details-grid > div:nth-child(odd) {
        border-right: 1px solid #f0f1f2;
    }


    .label {
        display: block;
        color: #8a929a;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .3px;
        margin-bottom: 4px;
    }


    .value {
        color: #343a40;
        font-size: 14px;
    }


    /* =========================================================
       DESCRIPTION / NOTES
       ========================================================== */

    .text-section {
        padding: 15px 20px;
        border-top: 1px solid #f0f1f2;
    }


    .text-section p {
        margin: 0;
        color: #495057;
        font-size: 14px;
        white-space: pre-line;
    }


    /* =========================================================
       ITEMS
       ========================================================== */

    .items-table {
        font-size: 13px;
    }


    .items-table thead th {
        background: #f8f9fa;
        color: #6c757d;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        padding: 11px 16px;
        border-bottom: 1px solid #e9ecef;
    }


    .items-table tbody td {
        padding: 13px 16px;
        vertical-align: middle;
        border-bottom: 1px solid #f0f1f2;
    }


    .items-table tbody tr:last-child td {
        border-bottom: 0;
    }


    /* =========================================================
       SIDEBAR
       ========================================================== */

    .sidebar-body {
        padding: 16px 20px;
    }


    .donor-name {
        font-size: 16px;
        font-weight: 600;
    }


    .sidebar-row {
        display: flex;
        justify-content: space-between;
        gap: 15px;
        padding: 9px 0;
        border-bottom: 1px solid #f0f1f2;
        font-size: 13px;
    }


    .sidebar-row:last-child {
        border-bottom: 0;
    }


    .sidebar-row span {
        color: #8a929a;
    }


    .sidebar-row strong {
        color: #343a40;
        font-weight: 500;
        text-align: right;
        word-break: break-word;
    }


    /* =========================================================
       MOBILE
       ========================================================== */

    @media (max-width: 767px) {

        .page-header {
            align-items: flex-start;
            flex-direction: column;
        }


        .page-actions {
            width: 100%;
        }


        .page-actions .btn {
            flex: 1;
        }


        .details-grid {
            grid-template-columns: 1fr;
        }


        .details-grid > div:nth-child(odd) {
            border-right: 0;
        }


        .simple-card-header {
            padding: 14px 16px;
        }


        .details-grid > div,
        .text-section {
            padding: 13px 16px;
        }


        .donation-communication-item .d-flex {
            align-items: flex-start !important;
        }


        .communication-cancel-button {
            width: 100%;
            margin-top: 5px;
        }

    }

</style>

@endsection
