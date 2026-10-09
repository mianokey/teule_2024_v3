@extends('layouts.admin')
@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/stores.css') }}">
@endpush

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