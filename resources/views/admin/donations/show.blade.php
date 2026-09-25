@extends('layouts.admin')

@section('content')

@php
    $isCash = $donation->type === 'cash';
    $isInKind = $donation->type === 'in_kind';

    $classificationClasses = [
        'donation' => 'donation-status-success',
        'payment' => 'donation-status-info',
        'refund' => 'donation-status-warning',
        'other' => 'donation-status-secondary',
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
       PAGE
    ========================================================= */

    .donation-page {
        max-width: 1500px;
        margin: 0 auto;
        padding-bottom: 25px;
    }

    .donation-topbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 12px;
        flex-wrap: wrap;
        gap: 8px;
    }

    .donation-topbar-left,
    .donation-topbar-right {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 6px;
    }


    /* =========================================================
       HERO - BLUE THEME
    ========================================================= */

    .donation-hero {
        border-radius: 11px;
        padding: 15px 18px;
        margin-bottom: 12px;

        background:
            linear-gradient(
                135deg,
                #eef4ff 0%,
                #f8fbff 55%,
                #ffffff 100%
            );

        border: 1px solid #dce7f8;

        box-shadow:
            0 3px 10px rgba(37, 99, 235, .06);
    }

    .donation-hero-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 20px;
    }

    .donation-hero-left {
        min-width: 0;
    }

    .donation-hero-right {
        text-align: right;
        flex-shrink: 0;
    }

    .donation-type {
        display: inline-flex;
        align-items: center;
        font-size: 12px;
        font-weight: 600;
        color: #52709d;
        margin-bottom: 4px;
        text-transform: uppercase;
        letter-spacing: .3px;
    }

    .donation-type i {
        margin-right: 6px;
        color: #3b82f6;
    }

    .donation-amount {
        font-size: 27px;
        line-height: 1.1;
        font-weight: 700;
        color: #163a68;
        margin-bottom: 5px;
    }

    .donation-meta {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 5px 14px;
        color: #687b95;
        font-size: 13px;
    }

    .donation-meta span {
        white-space: nowrap;
    }

    .donation-classification {
        display: inline-block;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
    }


    /* =========================================================
       STATUS COLORS
    ========================================================= */

    .donation-status-success {
        background: #e7f7ef;
        color: #18794e;
    }

    .donation-status-info {
        background: #e7f1ff;
        color: #1769aa;
    }

    .donation-status-warning {
        background: #fff4db;
        color: #996c00;
    }

    .donation-status-secondary {
        background: #edf0f5;
        color: #59636f;
    }

    .donation-status-light {
        background: #eef3f9;
        color: #52657d;
    }


    /* =========================================================
       COMMUNICATIONS
    ========================================================= */

    .communication-card {
        border: 1px solid #dfe8f4;
        border-radius: 9px;
        margin-bottom: 12px;
        background: #fff;

        box-shadow:
            0 2px 7px rgba(37, 99, 235, .035);
    }

    .communication-header {
        padding: 9px 13px;

        border-bottom: 1px solid #e7eef8;

        background: #f6f9fe;

        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .communication-header h6 {
        margin: 0;
        font-size: 13px;
        font-weight: 600;
        color: #29486d;
    }

    .communication-body {
        padding: 9px 11px;
    }

    .communication-item {
        display: flex;
        align-items: center;
        justify-content: space-between;

        padding: 7px 9px;

        border: 1px solid #e6edf6;
        border-radius: 6px;

        margin-bottom: 6px;
    }

    .communication-item:last-child {
        margin-bottom: 0;
    }

    .communication-main {
        min-width: 0;
    }

    .communication-title {
        font-size: 13px;
        font-weight: 600;
        color: #304967;
    }

    .communication-recipient {
        font-size: 12px;
        color: #7a899d;
    }

    .communication-countdown {
        font-size: 12px;
        font-weight: 600;
        color: #b36b00;
        white-space: nowrap;
        margin-left: 12px;
    }


    /* =========================================================
       MAIN LAYOUT
    ========================================================= */

    .donation-layout {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 300px;
        gap: 14px;
        align-items: start;
    }

    .donation-primary {
        min-width: 0;
    }

    .donation-sidebar {
        min-width: 0;
    }


    /* =========================================================
       CARDS
    ========================================================= */

    .donation-card {
        border: 1px solid #dfe7f1;
        border-radius: 10px;
        background: #fff;
        overflow: hidden;

        box-shadow:
            0 2px 8px rgba(37, 99, 235, .035);
    }

    .donation-card-header {
        min-height: 45px;
        padding: 10px 14px;

        display: flex;
        align-items: center;
        justify-content: space-between;

        border-bottom: 1px solid #e5edf7;

        background: #f7faff;
    }

    .donation-card-header h6 {
        margin: 0;
        font-size: 14px;
        font-weight: 600;
        color: #29486d;
    }

    .donation-card-header h6 i {
        color: #3d7dcc;
    }

    .donation-card-body {
        padding: 13px 14px;
    }


    /* =========================================================
       DONATION DETAILS
    ========================================================= */

    .donation-details-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));

        border: 1px solid #e3ebf5;
        border-radius: 7px;

        overflow: hidden;
    }

    .detail-item {
        padding: 11px 13px;
        min-height: 68px;

        border-right: 1px solid #e3ebf5;
        border-bottom: 1px solid #e3ebf5;

        background: #fff;
    }

    .detail-item:nth-child(4n) {
        border-right: 0;
    }

    .detail-label {
        font-size: 11px;
        color: #74859b;

        margin-bottom: 4px;

        text-transform: uppercase;
        letter-spacing: .25px;

        font-weight: 500;
    }

    .detail-value {
        font-size: 14px;
        font-weight: 500;

        color: #263f5d;

        line-height: 1.4;
        word-break: break-word;
    }

    .detail-wide {
        padding: 10px 12px;

        background: #f5f8fc;

        border: 1px solid #e5edf6;
        border-radius: 7px;
    }


    /* =========================================================
       IN-KIND ITEMS
    ========================================================= */

    .items-header {
        min-height: 48px;
    }

    .items-summary {
        display: flex;
        align-items: center;

        gap: 18px;

        color: #687b95;
        font-size: 13px;

        flex-wrap: wrap;
    }

    .items-summary strong {
        color: #304967;
    }

    /*
     * Only the items table scrolls.
     * This keeps the whole card visible.
     */
    .items-table-wrapper {
        max-height: 460px;

        overflow-y: auto;
        overflow-x: auto;
    }

    .items-table {
        min-width: 680px;

        margin-bottom: 0 !important;

        font-size: 13px;
    }

    .items-table thead th {
        position: sticky;
        top: 0;
        z-index: 2;

        background: #eef4fb;

        border-top: 0;

        padding: 10px 12px;

        font-size: 12px;
        font-weight: 600;

        color: #48627f;

        white-space: nowrap;
    }

    .items-table tbody td {
        padding: 11px 12px;
        vertical-align: middle;

        border-color: #edf1f6;
    }

    .items-table tbody tr:hover {
        background: #f8fbff;
    }

    .items-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .item-name {
        font-size: 14px;
        font-weight: 500;
        color: #263f5d;
    }

    .item-notes {
        margin-top: 3px;

        font-size: 12px;

        color: #7a899d;
    }


    /* =========================================================
       DONOR
    ========================================================= */

    .donor-profile {
        display: flex;
        align-items: center;
        margin-bottom: 13px;
    }

    .donor-avatar {
        width: 42px;
        height: 42px;
        min-width: 42px;

        border-radius: 50%;

        display: flex;
        align-items: center;
        justify-content: center;

        background: #eaf2fc;
        color: #3975b9;

        font-size: 17px;
        font-weight: 600;

        margin-right: 10px;
    }

    .donor-name {
        font-size: 15px;
        font-weight: 600;
        color: #263f5d;
        line-height: 1.3;
    }

    .donor-meta {
        font-size: 12px;
        color: #7a899d;
        margin-top: 2px;
    }

    .donor-detail {
        font-size: 13px;

        padding: 7px 0;

        border-bottom: 1px solid #edf1f6;

        word-break: break-word;

        line-height: 1.4;
    }

    .donor-detail strong {
        font-weight: 500;
        color: #59718e;
    }


    /* =========================================================
       RECORD
    ========================================================= */

    .record-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;

        padding: 8px 0;

        border-bottom: 1px solid #edf1f6;

        font-size: 13px;
        line-height: 1.4;
    }

    .record-row:last-child {
        border-bottom: 0;
    }

    .record-row span {
        color: #7a899d;
        margin-right: 10px;
    }

    .record-row strong {
        text-align: right;
        font-weight: 500;
        color: #304967;
    }


    /* =========================================================
       BUTTONS
    ========================================================= */

    .donation-topbar .btn {
        font-size: 13px;
        padding: 6px 10px;
    }

    .donation-topbar .dropdown-menu {
        font-size: 13px;
    }


    /* =========================================================
       RESPONSIVE
    ========================================================= */

    @media (max-width: 1199px) {

        .donation-layout {
            grid-template-columns: minmax(0, 1fr) 275px;
        }

        .donation-details-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .detail-item:nth-child(4n) {
            border-right: 1px solid #e3ebf5;
        }

        .detail-item:nth-child(2n) {
            border-right: 0;
        }

    }


    @media (max-width: 991px) {

        .donation-layout {
            grid-template-columns: 1fr;
        }

        .donation-sidebar {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

    }


    @media (max-width: 767px) {

        .donation-page {
            padding-bottom: 15px;
        }

        .donation-hero-inner {
            align-items: flex-start;
            flex-direction: column;
        }

        .donation-hero-right {
            text-align: left;
        }

        .donation-amount {
            font-size: 25px;
        }

        .donation-layout {
            display: block;
        }

        .donation-sidebar {
            display: block;
        }

        .donation-sidebar .donation-card {
            margin-top: 12px;
        }

        .donation-details-grid {
            grid-template-columns: 1fr;
        }

        .detail-item,
        .detail-item:nth-child(2n),
        .detail-item:nth-child(4n) {
            border-right: 0;
        }

        .items-table-wrapper {
            max-height: 360px;
        }

        .items-summary {
            gap: 8px;
        }

        .communication-item {
            align-items: flex-start;
            flex-direction: column;
        }

        .communication-countdown {
            margin-left: 0;
            margin-top: 4px;
        }

    }

</style>


<div class="donation-page">


    {{-- =====================================================
         TOP BAR
    ====================================================== --}}

    <div class="donation-topbar">

        <div class="donation-topbar-left">

            <a href="{{ route('admin.donations.index') }}"
               class="btn btn-light btn-sm">

                <i class="fa fa-arrow-left mr-1"></i>
                Back

            </a>

            <a href="{{ route('admin.donations.edit', $donation) }}"
               class="btn btn-primary btn-sm">

                <i class="fa fa-edit mr-1"></i>
                Edit

            </a>

        </div>


        @if(
            $donation->classification === 'donation'
            && $donation->donor
        )

            <div class="donation-topbar-right">

                <div class="dropdown">

                    <button
                        class="btn btn-outline-primary btn-sm dropdown-toggle"
                        type="button"
                        data-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false">

                        <i class="fa fa-paper-plane mr-1"></i>
                        Thank You

                    </button>

                    <div class="dropdown-menu dropdown-menu-right">

                        <a class="dropdown-item"
                           href="#"
                           onclick="
                               event.preventDefault();
                               document.getElementById('resend-sms-form').submit();
                           ">

                            <i class="fa fa-mobile-alt mr-2"></i>
                            Send SMS

                        </a>

                        <a class="dropdown-item"
                           href="#"
                           onclick="
                               event.preventDefault();
                               document.getElementById('resend-email-form').submit();
                           ">

                            <i class="fa fa-envelope mr-2"></i>
                            Send Email

                        </a>

                        <div class="dropdown-divider"></div>

                        <a class="dropdown-item"
                           href="#"
                           onclick="
                               event.preventDefault();
                               document.getElementById('resend-both-form').submit();
                           ">

                            <i class="fa fa-paper-plane mr-2"></i>
                            Send Both

                        </a>

                    </div>

                </div>


                <form
                    id="resend-sms-form"
                    method="POST"
                    action="{{ route('admin.donations.resend-thank-you', $donation) }}"
                    style="display:none;">

                    @csrf

                    <input type="hidden"
                           name="channel"
                           value="sms">

                </form>


                <form
                    id="resend-email-form"
                    method="POST"
                    action="{{ route('admin.donations.resend-thank-you', $donation) }}"
                    style="display:none;">

                    @csrf

                    <input type="hidden"
                           name="channel"
                           value="email">

                </form>


                <form
                    id="resend-both-form"
                    method="POST"
                    action="{{ route('admin.donations.resend-thank-you', $donation) }}"
                    style="display:none;">

                    @csrf

                    <input type="hidden"
                           name="channel"
                           value="both">

                </form>

            </div>

        @endif

    </div>


    {{-- =====================================================
         HERO
    ====================================================== --}}

    <div class="donation-hero">

        <div class="donation-hero-inner">

            <div class="donation-hero-left">

                <div class="donation-type">

                    @if($isCash)

                        <i class="fa fa-money-bill-wave"></i>
                        Cash Donation

                    @elseif($isInKind)

                        <i class="fa fa-gift"></i>
                        In-Kind Donation

                    @else

                        <i class="fa fa-hand-holding-heart"></i>
                        Donation

                    @endif

                </div>


                <div class="donation-amount">

                    {{ $donation->currency ?? 'KES' }}

                    {{ number_format((float) $donation->amount, 2) }}

                </div>


                <div class="donation-meta">

                    <span>
                        <i class="fa fa-hashtag mr-1"></i>
                        {{ $donation->donation_number }}
                    </span>

                    <span>
                        <i class="fa fa-calendar mr-1"></i>

                        {{ optional($donation->donation_date)->format('d M Y') }}

                    </span>

                    @if($donation->source)

                        <span>
                            <i class="fa fa-credit-card mr-1"></i>
                            {{ ucfirst($donation->source) }}
                        </span>

                    @endif

                </div>

            </div>


            <div class="donation-hero-right">

                <span class="donation-classification {{ $classificationClass }}">

                    {{ str_replace('_', ' ', $donation->classification) }}

                </span>

            </div>

        </div>

    </div>


    {{-- =====================================================
         PENDING COMMUNICATIONS
    ====================================================== --}}

    @if($pendingCommunications->count())

        <div class="communication-card">

            <div class="communication-header">

                <h6>

                    <i class="fa fa-clock mr-2"></i>
                    Pending Thank-You Communications

                </h6>

                <span class="badge badge-warning">

                    {{ $pendingCommunications->count() }}

                </span>

            </div>


            <div class="communication-body">

                @foreach($pendingCommunications as $communication)

                    <div class="communication-item">

                        <div class="communication-main">

                            <div class="communication-title">

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

                            <div class="communication-recipient">

                                {{ $communication->recipient }}

                            </div>

                        </div>


                        <div class="communication-countdown"
                             data-scheduled-at="{{ optional($communication->scheduled_at)->toIso8601String() }}">

                            Calculating...

                        </div>


                        <form
                            method="POST"
                            action="{{ route('admin.donation-communications.cancel', $communication) }}"
                            class="ml-2">

                            @csrf

                            @method('PATCH')

                            <button
                                type="submit"
                                class="btn btn-outline-danger btn-sm"
                                onclick="return confirm('Cancel this scheduled communication?')">

                                <i class="fa fa-times"></i>

                            </button>

                        </form>

                    </div>

                @endforeach

            </div>

        </div>

    @endif


    {{-- =====================================================
         MAIN CONTENT
    ====================================================== --}}

    <div class="donation-layout">


        {{-- =================================================
             LEFT COLUMN
        ================================================== --}}

        <div class="donation-primary">


            {{-- =============================================
                 DONATION DETAILS
            ============================================== --}}

            <div class="card donation-card mb-3">

                <div class="donation-card-header">

                    <h6>

                        <i class="fa fa-info-circle mr-2"></i>
                        Donation Details

                    </h6>

                </div>


                <div class="donation-card-body">

                    <div class="donation-details-grid">


                        <div class="detail-item">

                            <div class="detail-label">
                                Donation Number
                            </div>

                            <div class="detail-value">
                                {{ $donation->donation_number }}
                            </div>

                        </div>


                        <div class="detail-item">

                            <div class="detail-label">
                                Date
                            </div>

                            <div class="detail-value">

                                {{ optional($donation->donation_date)->format('d M Y') }}

                            </div>

                        </div>


                        <div class="detail-item">

                            <div class="detail-label">
                                Type
                            </div>

                            <div class="detail-value">

                                {{ ucfirst(str_replace('_', ' ', $donation->type)) }}

                            </div>

                        </div>


                        <div class="detail-item">

                            <div class="detail-label">
                                Purpose
                            </div>

                            <div class="detail-value">

                                {{ $donation->purpose ?: '—' }}

                            </div>

                        </div>


                        @if($isCash)

                            <div class="detail-item">

                                <div class="detail-label">
                                    Source
                                </div>

                                <div class="detail-value">

                                    {{ ucfirst($donation->source ?? '—') }}

                                </div>

                            </div>


                            <div class="detail-item">

                                <div class="detail-label">
                                    Currency
                                </div>

                                <div class="detail-value">

                                    {{ $donation->currency ?? 'KES' }}

                                </div>

                            </div>


                            <div class="detail-item">

                                <div class="detail-label">
                                    Reference
                                </div>

                                <div class="detail-value">

                                    {{ $donation->reference ?: '—' }}

                                </div>

                            </div>


                            <div class="detail-item">

                                <div class="detail-label">
                                    Payment Reference
                                </div>

                                <div class="detail-value">

                                    {{ $donation->payment_reference ?: '—' }}

                                </div>

                            </div>

                        @endif


                    </div>


                    @if($donation->description)

                        <div class="detail-wide mt-3">

                            <div class="detail-label">
                                Description
                            </div>

                            <div class="detail-value">

                                {{ $donation->description }}

                            </div>

                        </div>

                    @endif


                    @if($donation->notes)

                        <div class="detail-wide mt-2">

                            <div class="detail-label">
                                Notes
                            </div>

                            <div class="detail-value">

                                {{ $donation->notes }}

                            </div>

                        </div>

                    @endif

                </div>

            </div>


            {{-- =============================================
                 IN-KIND ITEMS
            ============================================== --}}

            @if($isInKind && $donation->items->count())

                <div class="card donation-card">

                    <div class="donation-card-header items-header">

                        <h6>

                            <i class="fa fa-gift mr-2"></i>
                            In-Kind Items

                        </h6>


                        <div class="items-summary">

                            <span>

                                {{ $donation->items->count() }}

                                item{{ $donation->items->count() == 1 ? '' : 's' }}

                            </span>


                            @if($totalEstimatedValue > 0)

                                <span>

                                    Estimated:

                                    <strong>

                                        KES
                                        {{ number_format($totalEstimatedValue, 2) }}

                                    </strong>

                                </span>

                            @endif

                        </div>

                    </div>


                    <div class="items-table-wrapper">

                        <table class="table table-hover items-table">

                            <thead>

                                <tr>

                                    <th>
                                        Item
                                    </th>

                                    <th width="90">
                                        Qty
                                    </th>

                                    <th width="110">
                                        Unit
                                    </th>

                                    <th width="130">
                                        Condition
                                    </th>

                                    <th width="150">
                                        Estimated Value
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @foreach($donation->items as $item)

                                    <tr>

                                        <td>

                                            {{-- IMPORTANT:
                                                 Database column is "item" --}}
                                            <div class="item-name">

                                                {{ $item->item }}

                                            </div>


                                            @if($item->notes)

                                                <div class="item-notes">

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

                                            {{ $item->condition ?: '—' }}

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


        {{-- =================================================
             RIGHT COLUMN
        ================================================== --}}

        <div class="donation-sidebar">


            {{-- =============================================
                 DONOR
            ============================================== --}}

            <div class="card donation-card mb-3">

                <div class="donation-card-header">

                    <h6>

                        <i class="fa fa-user mr-2"></i>
                        Donor

                    </h6>

                </div>


                <div class="donation-card-body">

                    @if($donation->donor)

                        <div class="donor-profile">

                            <div class="donor-avatar">

                                {{ strtoupper(substr($donation->donor->name, 0, 1)) }}

                            </div>


                            <div>

                                <div class="donor-name">

                                    {{ $donation->donor->name }}

                                </div>


                                @if($donation->donor->donor_number)

                                    <div class="donor-meta">

                                        {{ $donation->donor->donor_number }}

                                    </div>

                                @endif

                            </div>

                        </div>


                        @if($donation->donor->organization)

                            <div class="donor-detail">

                                <strong>
                                    Organization:
                                </strong>

                                {{ $donation->donor->organization }}

                            </div>

                        @endif


                        @if($donation->donor->phone)

                            <div class="donor-detail">

                                <strong>
                                    Phone:
                                </strong>

                                {{ $donation->donor->phone }}

                            </div>

                        @endif


                        @if($donation->donor->email)

                            <div class="donor-detail">

                                <strong>
                                    Email:
                                </strong>

                                {{ $donation->donor->email }}

                            </div>

                        @endif


                        <a
                            href="{{ route('admin.donors.show', $donation->donor) }}"
                            class="btn btn-outline-primary btn-sm btn-block mt-3">

                            <i class="fa fa-user mr-1"></i>
                            View Donor

                        </a>

                    @else

                        <div class="text-muted">

                            No donor linked to this donation.

                        </div>

                    @endif

                </div>

            </div>


            {{-- =============================================
                 RECORD
            ============================================== --}}

            <div class="card donation-card">

                <div class="donation-card-header">

                    <h6>

                        <i class="fa fa-history mr-2"></i>
                        Record

                    </h6>

                </div>


                <div class="donation-card-body">

                    <div class="record-row">

                        <span>
                            Received By
                        </span>

                        <strong>

                            {{ optional($donation->receivedBy)->name ?? '—' }}

                        </strong>

                    </div>


                    <div class="record-row">

                        <span>
                            Created
                        </span>

                        <strong>

                            {{ optional($donation->created_at)->format('d M Y H:i') }}

                        </strong>

                    </div>


                    <div class="record-row">

                        <span>
                            Updated
                        </span>

                        <strong>

                            {{ optional($donation->updated_at)->format('d M Y H:i') }}

                        </strong>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<script>

document.addEventListener('DOMContentLoaded', function () {

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


    updateCountdowns();

    setInterval(
        updateCountdowns,
        1000
    );

});

</script>

@endsection