@extends('layouts.admin')

@section('content')

@php

/*
|--------------------------------------------------------------------------
| Group communications by donation
|--------------------------------------------------------------------------
*/

$communicationsByDonation = $donor->communications
->groupBy('donation_id');

@endphp


<div class="donor-page">


    {{-- ================================================================
    DONOR HEADER
    ================================================================= --}}

    <div class="donor-header">

        <div class="donor-header-left">

            <div class="donor-avatar">
                {{ strtoupper(substr($donor->name, 0, 1)) }}
            </div>

            <div>

                <div class="text-muted small">
                    Donor
                </div>

                <h4 class="mb-1">
                    {{ $donor->name }}
                </h4>

                <div class="donor-meta">

                    <span>
                        <i class="fas fa-id-card me-1"></i>
                        {{ $donor->donor_number }}
                    </span>

                    @if($donor->email)

                    <span>
                        <i class="fas fa-envelope me-1"></i>
                        {{ $donor->email }}
                    </span>

                    @endif

                    @if($donor->phone)

                    <span>
                        <i class="fas fa-phone me-1"></i>
                        {{ $donor->phone }}
                    </span>

                    @endif

                </div>

            </div>

        </div>


        <div class="donor-header-actions">

            <a href="{{ route(
                'admin.donors.edit',
                $donor
            ) }}" class="btn btn-primary btn-sm">

                <i class="fas fa-edit me-1"></i>
                Edit Donor

            </a>

            <a href="{{ route(
                'admin.donors.index'
            ) }}" class="btn btn-light btn-sm">

                <i class="fas fa-arrow-left me-1"></i>
                Back

            </a>

        </div>

    </div>



    {{-- ================================================================
    MAIN CONTENT
    ================================================================= --}}

    <div class="row g-4">


        {{-- ============================================================
        LEFT - DONOR INFORMATION
        ============================================================= --}}

        <div class="col-lg-4">

            <div class="modern-card">

                <div class="modern-card-header">

                    <div>

                        <div class="section-title">
                            Donor Information
                        </div>

                        <div class="section-subtitle">
                            Contact and profile details
                        </div>

                    </div>

                    <div class="section-icon">
                        <i class="fas fa-user"></i>
                    </div>

                </div>


                <div class="modern-card-body">


                    {{-- =================================================
                    TWO COLUMN INFORMATION
                    ================================================== --}}

                    <div class="donor-info-grid">


                        {{-- Donor Number --}}
                        <div class="info-item">

                            <div class="info-icon">
                                <i class="fas fa-id-card"></i>
                            </div>

                            <div>

                                <div class="info-label">
                                    Donor Number
                                </div>

                                <div class="info-value">
                                    {{ $donor->donor_number }}
                                </div>

                            </div>

                        </div>


                        {{-- Name --}}
                        <div class="info-item">

                            <div class="info-icon">
                                <i class="fas fa-user"></i>
                            </div>

                            <div>

                                <div class="info-label">
                                    Name
                                </div>

                                <div class="info-value">
                                    {{ $donor->name }}
                                </div>

                            </div>

                        </div>


                        {{-- Phone --}}
                        <div class="info-item">

                            <div class="info-icon">
                                <i class="fas fa-phone"></i>
                            </div>

                            <div>

                                <div class="info-label">
                                    Phone
                                </div>

                                <div class="info-value">

                                    {{ $donor->phone ?: 'Not stated' }}

                                </div>

                            </div>

                        </div>


                        {{-- Email --}}
                        <div class="info-item">

                            <div class="info-icon">
                                <i class="fas fa-envelope"></i>
                            </div>

                            <div>

                                <div class="info-label">
                                    Email
                                </div>

                                <div class="info-value">

                                    {{ $donor->email ?: 'Not stated' }}

                                </div>

                            </div>

                        </div>


                        {{-- Organization --}}
                        <div class="info-item">

                            <div class="info-icon">
                                <i class="fas fa-building"></i>
                            </div>

                            <div>

                                <div class="info-label">
                                    Organization
                                </div>

                                <div class="info-value">

                                    {{ $donor->organization ?: 'Not stated' }}

                                </div>

                            </div>

                        </div>


                        {{-- Address --}}
                        <div class="info-item">

                            <div class="info-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>

                            <div>

                                <div class="info-label">
                                    Address
                                </div>

                                <div class="info-value">

                                    {{ $donor->address ?: 'Not stated' }}

                                </div>

                            </div>

                        </div>


                    </div>


                    {{-- =================================================
                    NOTES
                    ================================================== --}}

                    @if($donor->notes)

                    <div class="notes-box">

                        <div class="info-label mb-2">
                            Notes
                        </div>

                        {{ $donor->notes }}

                    </div>

                    @endif



                    {{-- =================================================
                    DONOR SUMMARY
                    ================================================== --}}

                    <div class="donor-summary">


                        {{-- Donations --}}
                        <div class="summary-item">

                            <div class="summary-icon donation-icon">

                                <i class="fas fa-hand-holding-heart"></i>

                            </div>

                            <div>

                                <div class="summary-value">

                                    {{ $donor->donations->count() }}

                                </div>

                                <div class="summary-label">

                                    Donations

                                </div>

                            </div>

                        </div>


                        <div class="summary-divider"></div>


                        {{-- Communications --}}
                        <div class="summary-item">

                            <div class="summary-icon communication-icon">

                                <i class="fas fa-comments"></i>

                            </div>

                            <div>

                                <div class="summary-value">

                                    {{ $donor->communications->count() }}

                                </div>

                                <div class="summary-label">

                                    Communications

                                </div>

                            </div>

                        </div>


                    </div>

                </div>

            </div>

        </div>



        {{-- ============================================================
        RIGHT - DONATION HISTORY
        ============================================================= --}}

        <div class="col-lg-8">

            <div class="modern-card">


                {{-- =====================================================
                DONATION HEADER
                ====================================================== --}}

                <div class="modern-card-header">

                    <div>

                        <div class="section-title">
                            Donation History
                        </div>

                        <div class="section-subtitle">
                            Donations and related communications
                        </div>

                    </div>


                    <div class="count-badge">

                        {{ $donor->donations->count() }}

                        {{ $donor->donations->count() === 1
                        ? 'Donation'
                        : 'Donations'
                        }}

                    </div>

                </div>



                {{-- =====================================================
                DONATIONS
                ====================================================== --}}

                <div style="max-height: 500px;overflow-x:auto;" class="donation-list">

                    @forelse($donor->donations as $donation)


                    @php

                    $donationCommunications =
                    $communicationsByDonation->get(
                    $donation->id,
                    collect()
                    );

                    $communicationCount =
                    $donationCommunications->count();

                    $sentCount =
                    $donationCommunications
                    ->where('status', 'sent')
                    ->count();

                    $pendingCount =
                    $donationCommunications
                    ->where('status', 'pending')
                    ->count();

                    $failedCount =
                    $donationCommunications
                    ->where('status', 'failed')
                    ->count();

                    @endphp


                    {{-- =================================================
                    DONATION ITEM
                    ================================================== --}}

                    <div class="donation-item">


                        {{-- =============================================
                        DONATION MAIN INFORMATION
                        ============================================== --}}

                        <div class="donation-main">


                            {{-- Date --}}
                            <div class="donation-date">

                                <div class="date-day">

                                    {{ $donation->donation_date?->format('d') }}

                                </div>

                                <div class="date-month">

                                    {{ $donation->donation_date?->format('M Y') }}

                                </div>

                            </div>


                            {{-- Donation information --}}
                            <div class="donation-info">

                                <div class="donation-number">

                                    <a href="{{ route(
                                            'admin.donations.show',
                                            $donation
                                        ) }}">

                                        {{ $donation->donation_number }}

                                    </a>

                                </div>


                                <div class="donation-description">

                                    {{ ucfirst(str_replace(
                                    '_',
                                    ' ',
                                    $donation->type
                                    )) }}


                                    @if($donation->purpose)

                                    <span class="dot-separator">
                                        •
                                    </span>

                                    {{ $donation->purpose }}

                                    @endif

                                </div>


                                <div class="donation-source">

                                    <i class="fas fa-wallet me-1"></i>

                                    {{ ucfirst($donation->source) }}

                                </div>

                            </div>


                            {{-- Amount --}}
                            <div class="donation-amount">

                                @if($donation->amount !== null)

                                <div class="amount-value">

                                    {{ $donation->currency }}

                                    {{ number_format(
                                    $donation->amount,
                                    2
                                    ) }}

                                </div>

                                @else

                                <div class="amount-value">
                                    —
                                </div>

                                @endif

                                <div class="amount-label">
                                    Donation
                                </div>

                            </div>


                        </div>



                        {{-- =================================================
                        COMMUNICATION SUMMARY
                        ================================================== --}}

                        <div class="communication-bar">


                            <div class="communication-summary">


                                <div class="communication-icon">

                                    <i class="fas fa-comments"></i>

                                </div>


                                <div>

                                    @if($communicationCount > 0)


                                    <div class="communication-title">

                                        {{ $communicationCount }}

                                        {{ $communicationCount === 1
                                        ? 'communication'
                                        : 'communications'
                                        }}

                                    </div>


                                    <div class="communication-status">


                                        @if($sentCount > 0)

                                        <span class="mini-status sent">

                                            <i class="fas fa-check"></i>

                                            {{ $sentCount }}

                                            sent

                                        </span>

                                        @endif


                                        @if($pendingCount > 0)

                                        <span class="mini-status pending">

                                            <i class="fas fa-clock"></i>

                                            {{ $pendingCount }}

                                            pending

                                        </span>

                                        @endif


                                        @if($failedCount > 0)

                                        <span class="mini-status failed">

                                            <i class="fas fa-exclamation-circle"></i>

                                            {{ $failedCount }}

                                            failed

                                        </span>

                                        @endif


                                    </div>


                                    @else


                                    <div class="communication-title muted">

                                        No communications

                                    </div>


                                    <div class="communication-status">

                                        No messages recorded for this
                                        donation.

                                    </div>


                                    @endif

                                </div>

                            </div>



                            {{-- View button --}}
                            @if($communicationCount > 0)

                            <button type="button" class="btn btn-communication" onclick="openCommunicationModal(
                                                'communicationModal{{ $donation->id }}'
                                            )">

                                View

                                <i class="fas fa-chevron-right ms-1"></i>

                            </button>

                            @endif


                        </div>

                    </div>



                    {{-- =================================================
                    COMMUNICATION MODAL
                    ================================================== --}}

                    @if($communicationCount > 0)

                    <div class="communication-modal" id="communicationModal{{ $donation->id }}" onclick="closeCommunicationModalOutside(
                                     event,
                                     'communicationModal{{ $donation->id }}'
                                 )">


                        <div class="communication-modal-dialog">


                            {{-- =================================================
                            MODAL HEADER
                            ================================================== --}}

                            <div class="communication-modal-header">


                                <div>

                                    <div class="modal-eyebrow">

                                        Communication History

                                    </div>


                                    <h5 class="mb-1">

                                        {{ $donation->donation_number }}

                                    </h5>


                                    <div class="modal-donation-summary">

                                        @if($donation->amount !== null)

                                        {{ $donation->currency }}

                                        {{ number_format(
                                        $donation->amount,
                                        2
                                        ) }}

                                        <span>•</span>

                                        @endif


                                        {{ $donation->donation_date?->format(
                                        'd M Y'
                                        ) }}

                                    </div>

                                </div>


                                <button type="button" class="modal-close" onclick="closeCommunicationModal(
                                                    'communicationModal{{ $donation->id }}'
                                                )">

                                    <i class="fas fa-times"></i>

                                </button>


                            </div>



                            {{-- =================================================
                            MODAL BODY
                            ================================================== --}}

                            <div class="communication-modal-body">


                                @foreach(
                                $donationCommunications
                                as $communication
                                )


                                @php

                                $statusClasses = [

                                'pending' =>
                                'status-pending',

                                'sending' =>
                                'status-sending',

                                'sent' =>
                                'status-sent',

                                'failed' =>
                                'status-failed',

                                'cancelled' =>
                                'status-cancelled',

                                ];

                                $statusClass =
                                $statusClasses[
                                $communication->status
                                ]
                                ?? 'status-pending';

                                @endphp



                                {{-- =========================================
                                COMMUNICATION RECORD
                                ========================================== --}}

                                <div class="communication-record">


                                    {{-- Record header --}}
                                    <div class="record-header">


                                        <div class="record-channel">


                                            @if(
                                            $communication->channel
                                            === 'email'
                                            )


                                            <span class="record-channel-icon email">

                                                <i class="fas fa-envelope"></i>

                                            </span>


                                            <div>

                                                <div class="record-channel-name">

                                                    Email

                                                </div>

                                                <div class="record-recipient">

                                                    {{ $communication->recipient }}

                                                </div>

                                            </div>


                                            @elseif(
                                            $communication->channel
                                            === 'sms'
                                            )


                                            <span class="record-channel-icon sms">

                                                <i class="fas fa-sms"></i>

                                            </span>


                                            <div>

                                                <div class="record-channel-name">

                                                    SMS

                                                </div>

                                                <div class="record-recipient">

                                                    {{ $communication->recipient }}

                                                </div>

                                            </div>


                                            @else


                                            <span class="record-channel-icon">

                                                <i class="fas fa-paper-plane"></i>

                                            </span>


                                            <div>

                                                <div class="record-channel-name">

                                                    {{ ucfirst(
                                                    $communication->channel
                                                    ) }}

                                                </div>

                                                <div class="record-recipient">

                                                    {{ $communication->recipient }}

                                                </div>

                                            </div>


                                            @endif


                                        </div>


                                        {{-- Status --}}
                                        <span class="status-badge {{ $statusClass }}">

                                            {{ ucfirst(
                                            $communication->status
                                            ) }}

                                        </span>


                                    </div>



                                    {{-- Record metadata --}}
                                    <div class="record-meta">


                                        <span>

                                            <i class="far fa-clock me-1"></i>

                                            {{ $communication->created_at?->format(
                                            'd M Y H:i'
                                            ) }}

                                        </span>


                                        @if($communication->sent_at)

                                        <span>

                                            <i class="fas fa-check me-1"></i>

                                            Sent

                                            {{ $communication->sent_at->format(
                                            'd M Y H:i'
                                            ) }}

                                        </span>

                                        @endif


                                        @if($communication->type)

                                        <span>

                                            <i class="fas fa-tag me-1"></i>

                                            {{ ucfirst(
                                            str_replace(
                                            '_',
                                            ' ',
                                            $communication->type
                                            )
                                            ) }}

                                        </span>

                                        @endif


                                    </div>



                                    {{-- Subject --}}
                                    @if($communication->subject)

                                    <div class="record-subject">

                                        <strong>
                                            Subject:
                                        </strong>

                                        {{ $communication->subject }}

                                    </div>

                                    @endif



                                    {{-- Message --}}
                                    <div class="record-message">

                                        {{ $communication->message }}

                                    </div>



                                    {{-- Error --}}
                                    @if($communication->error_message)

                                    <div class="record-error">

                                        <i class="fas fa-exclamation-triangle me-1"></i>

                                        {{ $communication->error_message }}

                                    </div>

                                    @endif



                                    {{-- =================================================
                                    RECORD FOOTER
                                    ================================================== --}}

                                    <div class="record-footer">


                                        <a href="{{ route(
                                                        'admin.donation-communications.show',
                                                        $communication
                                                    ) }}" class="record-view-link">

                                            <i class="fas fa-eye me-1"></i>

                                            View

                                        </a>



                                        {{-- =============================================
                                        RESEND
                                        ============================================== --}}

                                        @if(
                                        in_array(
                                        $communication->channel,
                                        ['sms', 'email']
                                        )
                                        &&
                                        in_array(
                                        $communication->status,
                                        [
                                        'sent',
                                        'failed',
                                        'cancelled'
                                        ]
                                        )
                                        )


                                        <form method="POST" action="{{ route(
          'admin.donations.resend-thank-you',
          $donation
      ) }}" class="resend-form" onsubmit="return confirm(
          'Schedule a new thank-you {{ $communication->channel }}?'
      )">

                                            @csrf

                                            <input type="hidden" name="channel" value="{{ $communication->channel }}">

                                            <button type="submit" class="btn btn-resend btn-sm">

                                                <i class="fas fa-redo me-1"></i>
                                                Resend

                                            </button>

                                        </form>

                                        @endif


                                    </div>


                                </div>


                                @endforeach


                            </div>



                            {{-- =================================================
                            MODAL FOOTER
                            ================================================== --}}

                            <div class="communication-modal-footer">


                                <span class="text-muted small">

                                    {{ $communicationCount }}

                                    {{ $communicationCount === 1
                                    ? 'communication'
                                    : 'communications'
                                    }}

                                    recorded for this donation.

                                </span>


                                <button type="button" class="btn btn-light btn-sm" onclick="closeCommunicationModal(
                                                    'communicationModal{{ $donation->id }}'
                                                )">

                                    Close

                                </button>


                            </div>


                        </div>

                    </div>

                    @endif


                    @empty


                    {{-- =================================================
                    EMPTY DONATIONS
                    ================================================== --}}

                    <div class="empty-state">


                        <div class="empty-icon">

                            <i class="fas fa-hand-holding-heart"></i>

                        </div>


                        <h6>
                            No donations yet
                        </h6>


                        <p>
                            No donations have been recorded for this donor.
                        </p>


                    </div>


                    @endforelse


                </div>

            </div>

        </div>

    </div>

</div>



{{-- =====================================================================
STYLES
====================================================================== --}}

<style>
    .donor-page {
        padding-bottom: 40px;
    }


    /* ================================================================
       DONOR HEADER
    ================================================================ */

    .donor-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
        padding: 20px 24px;
        margin-bottom: 24px;
        background: #fff;
        border: 1px solid #e7e9ed;
        border-radius: 12px;
    }


    .donor-header-left {
        display: flex;
        align-items: center;
        gap: 15px;
    }


    .donor-avatar {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: #00096A;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 21px;
        font-weight: 700;
        flex-shrink: 0;
    }


    .donor-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        color: #7b8490;
        font-size: 12px;
    }


    .donor-meta span {
        white-space: nowrap;
    }


    .donor-header-actions {
        display: flex;
        gap: 8px;
    }



    /* ================================================================
       CARDS
    ================================================================ */

    .modern-card {
        background: #fff;
        border: 1px solid #e7e9ed;
        border-radius: 12px;
        overflow: hidden;
    }


    .modern-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        padding: 17px 20px;
        border-bottom: 1px solid #edf0f2;
    }


    .section-title {
        font-size: 15px;
        font-weight: 600;
        color: #252a31;
    }


    .section-subtitle {
        color: #8a929a;
        font-size: 12px;
        margin-top: 3px;
    }


    .section-icon {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        background: #eef1fa;
        color: #00096A;
        display: flex;
        align-items: center;
        justify-content: center;
    }


    .modern-card-body {
        padding: 8px 20px 20px;
    }


    .count-badge {
        padding: 6px 10px;
        background: #eef1fa;
        color: #00096A;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }



    /* ================================================================
       DONOR INFORMATION - 2 COLUMNS
    ================================================================ */

    .donor-info-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        column-gap: 25px;
    }


    .info-item {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 13px 0;
        border-bottom: 1px solid #f0f1f3;
        min-width: 0;
    }


    .info-icon {
        width: 30px;
        height: 30px;
        border-radius: 7px;
        background: #f5f6f8;
        color: #68717c;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        flex-shrink: 0;
    }


    .info-label {
        color: #8a929a;
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: .35px;
        margin-bottom: 2px;
    }


    .info-value {
        color: #343a40;
        font-size: 13px;
        word-break: break-word;
    }


    .notes-box {
        margin-top: 14px;
        padding: 12px;
        background: #f8f9fa;
        border-radius: 8px;
        color: #59616a;
        font-size: 13px;
        line-height: 1.6;
    }



    /* ================================================================
       DONOR SUMMARY
    ================================================================ */

    .donor-summary {
        display: flex;
        align-items: center;
        margin-top: 18px;
        padding-top: 17px;
        border-top: 1px solid #edf0f2;
    }


    .summary-item {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 11px;
    }


    .summary-divider {
        width: 1px;
        height: 40px;
        background: #e9ecef;
        margin: 0 20px;
    }


    .summary-icon {
        width: 34px;
        height: 34px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
    }


    .donation-icon {
        background: #fff3ed;
        color: #EF4700;
    }


    .communication-icon {
        background: #eef1fa;
        color: #00096A;
    }


    .summary-value {
        font-size: 18px;
        font-weight: 700;
        color: #252a31;
        line-height: 1.1;
    }


    .summary-label {
        font-size: 11px;
        color: #8a929a;
        margin-top: 2px;
    }



    /* ================================================================
       DONATION LIST
    ================================================================ */

    .donation-list {
        padding: 5px 0;
    }


    .donation-item {
        border-bottom: 1px solid #edf0f2;
    }


    .donation-item:last-child {
        border-bottom: 0;
    }


    .donation-main {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 18px 20px 14px;
    }


    .donation-date {
        width: 50px;
        text-align: center;
        flex-shrink: 0;
    }


    .date-day {
        font-size: 20px;
        line-height: 1;
        font-weight: 700;
        color: #00096A;
    }


    .date-month {
        font-size: 10px;
        color: #8a929a;
        margin-top: 4px;
        text-transform: uppercase;
    }


    .donation-info {
        flex: 1;
        min-width: 0;
    }


    .donation-number a {
        color: #252a31;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
    }


    .donation-number a:hover {
        color: #00096A;
    }


    .donation-description {
        margin-top: 4px;
        color: #59616a;
        font-size: 12px;
    }


    .dot-separator {
        color: #b5bbc1;
        padding: 0 4px;
    }


    .donation-source {
        margin-top: 5px;
        color: #969da5;
        font-size: 11px;
    }


    .donation-amount {
        text-align: right;
        flex-shrink: 0;
    }


    .amount-value {
        color: #252a31;
        font-size: 14px;
        font-weight: 700;
    }


    .amount-label {
        color: #9aa1a8;
        font-size: 10px;
        margin-top: 3px;
    }



    /* ================================================================
       COMMUNICATION BAR
    ================================================================ */

    .communication-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        margin: 0 20px 14px;
        padding: 10px 12px;
        background: #f8f9fb;
        border: 1px solid #edf0f2;
        border-radius: 8px;
    }


    .communication-summary {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }


    .communication-icon {
        width: 30px;
        height: 30px;
        border-radius: 7px;
        background: #eef1fa;
        color: #00096A;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        flex-shrink: 0;
    }


    .communication-title {
        color: #343a40;
        font-size: 12px;
        font-weight: 600;
    }


    .communication-title.muted {
        color: #7c858e;
    }


    .communication-status {
        display: flex;
        flex-wrap: wrap;
        gap: 7px;
        margin-top: 3px;
        color: #9aa1a8;
        font-size: 10px;
    }


    .mini-status {
        display: inline-flex;
        align-items: center;
        gap: 3px;
    }


    .mini-status.sent {
        color: #2e7d32;
    }


    .mini-status.pending {
        color: #a36b00;
    }


    .mini-status.failed {
        color: #c62828;
    }


    .btn-communication {
        border: 1px solid #dfe3e7;
        background: #fff;
        color: #495057;
        border-radius: 6px;
        padding: 6px 11px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }


    .btn-communication:hover {
        background: #00096A;
        border-color: #00096A;
        color: #fff;
    }



    /* ================================================================
       STATUS
    ================================================================ */

    .status-badge {
        display: inline-block;
        padding: 5px 9px;
        border-radius: 5px;
        font-size: 10px;
        font-weight: 600;
        white-space: nowrap;
    }


    .status-pending {
        background: #fff8e1;
        color: #a36b00;
    }


    .status-sending {
        background: #e3f2fd;
        color: #1565c0;
    }


    .status-sent {
        background: #e8f5e9;
        color: #2e7d32;
    }


    .status-failed {
        background: #ffebee;
        color: #c62828;
    }


    .status-cancelled {
        background: #f1f3f5;
        color: #6c757d;
    }



    /* ================================================================
       EMPTY STATE
    ================================================================ */

    .empty-state {
        text-align: center;
        padding: 50px 20px;
    }


    .empty-icon {
        width: 50px;
        height: 50px;
        margin: 0 auto 12px;
        border-radius: 50%;
        background: #f5f6f8;
        color: #9aa1a8;
        display: flex;
        align-items: center;
        justify-content: center;
    }


    .empty-state h6 {
        margin-bottom: 5px;
        color: #495057;
    }


    .empty-state p {
        margin: 0;
        color: #9aa1a8;
        font-size: 12px;
    }



    /* ================================================================
       COMMUNICATION MODAL
    ================================================================ */

    .communication-modal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(15, 23, 42, .55);
        padding: 30px 15px;
        overflow-y: auto;
    }


    .communication-modal.show {
        display: flex;
        align-items: flex-start;
        justify-content: center;
    }


    .communication-modal-dialog {
        width: 100%;
        max-width: 720px;
        margin: auto;
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, .20);
        overflow: hidden;
        animation: modalSlideIn .18s ease-out;
    }


    @keyframes modalSlideIn {

        from {
            opacity: 0;
            transform: translateY(-15px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }

    }


    .communication-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 15px;
        padding: 20px 22px;
        background: #00096A;
        color: #fff;
    }


    .communication-modal-header h5 {
        color: #fff;
        font-weight: 600;
    }


    .modal-eyebrow {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: .8px;
        opacity: .7;
        margin-bottom: 5px;
    }


    .modal-donation-summary {
        font-size: 11px;
        opacity: .75;
    }


    .modal-donation-summary span {
        margin: 0 5px;
    }


    .modal-close {
        width: 32px;
        height: 32px;
        border: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, .12);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        flex-shrink: 0;
    }


    .modal-close:hover {
        background: rgba(255, 255, 255, .22);
    }


    .communication-modal-body {
        max-height: 65vh;
        overflow-y: auto;
        padding: 15px;
        background: #f7f8fa;
    }


    .communication-record {
        background: #fff;
        border: 1px solid #e5e8ec;
        border-radius: 10px;
        margin-bottom: 12px;
        overflow: hidden;
    }


    .communication-record:last-child {
        margin-bottom: 0;
    }


    .record-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        padding: 14px 15px;
    }


    .record-channel {
        display: flex;
        align-items: center;
        gap: 10px;
        min-width: 0;
    }


    .record-channel-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        background: #f1f3f5;
        color: #68717c;
    }


    .record-channel-icon.email {
        background: #eef1fa;
        color: #00096A;
    }


    .record-channel-icon.sms {
        background: #fff3ed;
        color: #EF4700;
    }


    .record-channel-name {
        font-size: 13px;
        font-weight: 600;
        color: #343a40;
    }


    .record-recipient {
        color: #8a929a;
        font-size: 11px;
        margin-top: 2px;
        word-break: break-word;
    }


    .record-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        padding: 9px 15px;
        border-top: 1px solid #f0f1f3;
        border-bottom: 1px solid #f0f1f3;
        color: #8a929a;
        font-size: 10px;
    }


    .record-subject {
        padding: 12px 15px 0;
        color: #495057;
        font-size: 12px;
        line-height: 1.5;
    }


    .record-message {
        margin: 10px 15px;
        padding: 12px;
        background: #f8f9fa;
        border-radius: 7px;
        color: #59616a;
        font-size: 12px;
        line-height: 1.65;
        white-space: pre-line;
    }


    .record-error {
        margin: 10px 15px;
        padding: 10px 12px;
        background: #ffebee;
        border-radius: 6px;
        color: #c62828;
        font-size: 11px;
    }


    /* ================================================================
       RECORD FOOTER
    ================================================================ */

    .record-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
        padding: 10px 15px;
        border-top: 1px solid #f0f1f3;
    }


    .record-view-link {
        color: #00096A;
        font-size: 11px;
        font-weight: 600;
        text-decoration: none;
    }


    .record-view-link:hover {
        color: #EF4700;
    }


    .resend-form {
        margin: 0;
    }


    .btn-resend {
        border: 1px solid #dfe3e7;
        background: #fff;
        color: #00096A;
        font-size: 11px;
        font-weight: 600;
        padding: 5px 9px;
        border-radius: 6px;
    }


    .btn-resend:hover {
        background: #00096A;
        border-color: #00096A;
        color: #fff;
    }



    /* ================================================================
       MODAL FOOTER
    ================================================================ */

    .communication-modal-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        padding: 13px 20px;
        border-top: 1px solid #e9ecef;
    }



    /* ================================================================
       MOBILE
    ================================================================ */

    @media (max-width: 767px) {


        .donor-header {
            align-items: flex-start;
            flex-direction: column;
            padding: 16px;
        }


        .donor-header-actions {
            width: 100%;
        }


        .donor-header-actions .btn {
            flex: 1;
        }


        .donor-info-grid {
            grid-template-columns: 1fr;
        }


        .donation-main {
            align-items: flex-start;
            padding: 15px;
        }


        .donation-amount {
            display: none;
        }


        .communication-bar {
            margin-left: 15px;
            margin-right: 15px;
        }


        .communication-status {
            display: none;
        }


        .communication-modal {
            padding: 10px;
        }


        .communication-modal-dialog {
            border-radius: 10px;
        }


        .record-header {
            align-items: flex-start;
        }


        .record-footer {
            flex-wrap: wrap;
        }


        .donor-summary {
            gap: 10px;
        }


        .summary-divider {
            margin: 0 8px;
        }

    }
</style>



{{-- =====================================================================
JAVASCRIPT
====================================================================== --}}

<script>
    function openCommunicationModal(id)
    {
        const modal = document.getElementById(id);

        if (!modal) {
            return;
        }

        modal.classList.add('show');

        document.body.style.overflow = 'hidden';
    }


    function closeCommunicationModal(id)
    {
        const modal = document.getElementById(id);

        if (!modal) {
            return;
        }

        modal.classList.remove('show');

        document.body.style.overflow = '';
    }


    function closeCommunicationModalOutside(event, id)
    {
        if (event.target.id === id) {

            closeCommunicationModal(id);

        }
    }


    document.addEventListener('keydown', function(event)
    {

        if (event.key !== 'Escape') {
            return;
        }


        document
            .querySelectorAll('.communication-modal.show')
            .forEach(function(modal)
            {

                modal.classList.remove('show');

            });


        document.body.style.overflow = '';

    });

</script>

@endsection