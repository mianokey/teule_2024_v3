@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/stores.css') }}">
@endpush

@section('content')

@php
    $communicationsByDonation = $donor->communications
        ->groupBy('donation_id');

    $donationCount = $donor->donations->count();
    $communicationCountTotal = $donor->communications->count();
@endphp

<div class="store-requisition-page">

    {{-- ============================================================
         DONOR HEADER
    ============================================================= --}}
    <div class="requisition-page-header">

        <div class="requisition-header-content">
            <div class="requisition-header-icon">
                <i class="fas fa-user"></i>
            </div>

            <div>
                <div class="requisition-breadcrumb">
                    <span>Donations</span>
                    <span>/</span>
                    <span>Donors</span>
                    <span>/</span>
                    <span>{{ $donor->donor_number }}</span>
                </div>

                <h1 class="requisition-page-title">
                    {{ $donor->name }}
                </h1>

                <p class="requisition-page-subtitle">
                    Donor profile and donation communication history
                </p>
            </div>
        </div>

        <div class="requisition-header-right">
            <a href="{{ route('admin.donors.edit', $donor) }}"
               class="requisition-add-button primary">
                <i class="fas fa-edit"></i>
                Edit Donor
            </a>

            <a href="{{ route('admin.donors.index') }}"
               class="requisition-cancel-button">
                <i class="fas fa-arrow-left"></i>
                Back
            </a>
        </div>

    </div>

    {{-- ============================================================
         DONOR INFORMATION
    ============================================================= --}}
    <div class="requisition-section">

        <div class="requisition-section-header">
            <div class="requisition-section-heading">
                <div class="requisition-section-icon">
                    <i class="fas fa-address-card"></i>
                </div>

                <div>
                    <h5>Donor Information</h5>
                    <p>Contact and profile details</p>
                </div>
            </div>

            <span class="requisition-count-badge">
                {{ $donor->donor_number }}
            </span>
        </div>

        <div class="requisition-details-body">

            <div class="row g-3">

                <div class="col-md-4 col-sm-6">
                    <div class="requisition-field-label">Donor Number</div>
                    <div class="requisition-input">
                        {{ $donor->donor_number ?: '—' }}
                    </div>
                </div>

                <div class="col-md-4 col-sm-6">
                    <div class="requisition-field-label">Full Name</div>
                    <div class="requisition-input">
                        {{ $donor->name ?: '—' }}
                    </div>
                </div>

                <div class="col-md-4 col-sm-6">
                    <div class="requisition-field-label">Organization</div>
                    <div class="requisition-input">
                        {{ $donor->organization ?: 'Not stated' }}
                    </div>
                </div>

                <div class="col-md-4 col-sm-6">
                    <div class="requisition-field-label">Phone</div>
                    <div class="requisition-input">
                        {{ $donor->phone ?: 'Not stated' }}
                    </div>
                </div>

                <div class="col-md-4 col-sm-6">
                    <div class="requisition-field-label">Email</div>
                    <div class="requisition-input">
                        {{ $donor->email ?: 'Not stated' }}
                    </div>
                </div>

                <div class="col-md-4 col-sm-6">
                    <div class="requisition-field-label">Address</div>
                    <div class="requisition-input">
                        {{ $donor->address ?: 'Not stated' }}
                    </div>
                </div>

                @if($donor->notes)
                    <div class="col-12">
                        <div class="requisition-field-label">Notes</div>
                        <div class="requisition-input">
                            {{ $donor->notes }}
                        </div>
                    </div>
                @endif

            </div>

            {{-- Donor totals --}}
            <div class="row g-3 mt-2">

                <div class="col-md-6">
                    <div class="requisition-section">
                        <div class="requisition-details-body">
                            <div class="d-flex align-items-center gap-3">
                                <div class="requisition-section-icon">
                                    <i class="fas fa-hand-holding-heart"></i>
                                </div>

                                <div>
                                    <div class="requisition-field-label">
                                        Total Donations
                                    </div>

                                    <div class="requisition-page-title">
                                        {{ $donationCount }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="requisition-section">
                        <div class="requisition-details-body">
                            <div class="d-flex align-items-center gap-3">
                                <div class="requisition-section-icon">
                                    <i class="fas fa-comments"></i>
                                </div>

                                <div>
                                    <div class="requisition-field-label">
                                        Total Communications
                                    </div>

                                    <div class="requisition-page-title">
                                        {{ $communicationCountTotal }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>

    {{-- ============================================================
         DONATION HISTORY
    ============================================================= --}}
    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">
                <div class="requisition-section-icon">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>

                <div>
                    <h5>Donation History</h5>
                    <p>Donations and related communications</p>
                </div>
            </div>

            <span class="requisition-count-badge">
                {{ $donationCount }}
                {{ $donationCount === 1 ? 'Donation' : 'Donations' }}
            </span>

        </div>

        <div class="requisition-details-body">

            @if($donor->donations->isNotEmpty())

                <div class="requisition-list-table-wrapper">
                    <table class="requisition-list-table">

                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Donation</th>
                                <th>Date</th>
                                <th>Source</th>
                                <th>Amount</th>
                                <th>Communications</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>

                            @foreach($donor->donations as $donation)

                                @php
                                    $donationCommunications =
                                        $communicationsByDonation->get(
                                            $donation->id,
                                            collect()
                                        );

                                    $communicationCount =
                                        $donationCommunications->count();

                                    $sentCount = $donationCommunications
                                        ->where('status', 'sent')
                                        ->count();

                                    $pendingCount = $donationCommunications
                                        ->where('status', 'pending')
                                        ->count();

                                    $sendingCount = $donationCommunications
                                        ->where('status', 'sending')
                                        ->count();

                                    $failedCount = $donationCommunications
                                        ->where('status', 'failed')
                                        ->count();

                                    $cancelledCount = $donationCommunications
                                        ->where('status', 'cancelled')
                                        ->count();
                                @endphp

                                <tr>

                                    <td>
                                        <span class="requisition-list-row-number">
                                            {{ $loop->iteration }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="requisition-list-number">
                                            <a href="{{ route('admin.donations.show', $donation) }}">
                                                {{ $donation->donation_number }}
                                            </a>
                                        </div>

                                        <div class="requisition-list-meta">
                                            {{ ucfirst(str_replace('_', ' ', $donation->type)) }}
                                        </div>

                                        @if($donation->purpose)
                                            <div class="requisition-list-purpose">
                                                {{ $donation->purpose }}
                                            </div>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="requisition-list-date">
                                            {{ $donation->donation_date?->format('d M Y') ?? '—' }}
                                        </div>
                                    </td>

                                    <td>
                                        {{ $donation->source
                                            ? ucfirst(str_replace('_', ' ', $donation->source))
                                            : '—' }}
                                    </td>

                                    <td>
                                        @if($donation->amount !== null)
                                            <strong>
                                                {{ $donation->currency ?: 'KES' }}
                                                {{ number_format((float) $donation->amount, 2) }}
                                            </strong>
                                        @else
                                            <span>—</span>
                                        @endif
                                    </td>

                                    {{-- Communication summary --}}
                                    <td>
                                        @if($communicationCount > 0)

                                            <div class="d-flex flex-wrap gap-1">

                                                @if($sentCount > 0)
                                                    <span class="requisition-list-status requisition-list-status-approved">
                                                        <i class="fas fa-check-circle"></i>
                                                        {{ $sentCount }} sent
                                                    </span>
                                                @endif

                                                @if($pendingCount > 0)
                                                    <span class="requisition-list-status requisition-list-status-pending">
                                                        <i class="fas fa-clock"></i>
                                                        {{ $pendingCount }} pending
                                                    </span>
                                                @endif

                                                @if($sendingCount > 0)
                                                    <span class="requisition-list-status requisition-list-status-pending">
                                                        <i class="fas fa-paper-plane"></i>
                                                        {{ $sendingCount }} sending
                                                    </span>
                                                @endif

                                                @if($failedCount > 0)
                                                    <span class="requisition-list-status requisition-list-status-rejected">
                                                        <i class="fas fa-exclamation-circle"></i>
                                                        {{ $failedCount }} failed
                                                    </span>
                                                @endif

                                                @if($cancelledCount > 0)
                                                    <span class="requisition-list-status">
                                                        <i class="fas fa-ban"></i>
                                                        {{ $cancelledCount }} cancelled
                                                    </span>
                                                @endif

                                            </div>

                                        @else

                                            <span class="text-muted">
                                                No communications
                                            </span>

                                        @endif
                                    </td>

                                    {{-- Actions --}}
                                    <td>
                                        <div class="requisition-list-actions">

                                            <a href="{{ route('admin.donations.show', $donation) }}"
                                               class="requisition-list-action"
                                               title="View donation">
                                                <i class="fas fa-eye"></i>
                                            </a>

                                            @if($communicationCount > 0)
                                                <button type="button"
                                                        class="requisition-list-action"
                                                        title="View communication history"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#communicationModal{{ $donation->id }}">
                                                    <i class="fas fa-comments"></i>
                                                </button>
                                            @endif

                                        </div>
                                    </td>

                                </tr>

                            @endforeach

                        </tbody>
                    </table>
                </div>

                {{-- ====================================================
                     COMMUNICATION HISTORY MODALS
                ===================================================== --}}
                @foreach($donor->donations as $donation)

                    @php
                        $donationCommunications =
                            $communicationsByDonation->get(
                                $donation->id,
                                collect()
                            );

                        $communicationCount =
                            $donationCommunications->count();
                    @endphp

                    @if($communicationCount > 0)

                        <div class="modal fade"
                             id="communicationModal{{ $donation->id }}"
                             tabindex="-1"
                             aria-labelledby="communicationModalLabel{{ $donation->id }}"
                             aria-hidden="true">

                            <div class="modal-dialog modal-lg modal-dialog-scrollable">

                                <div class="modal-content">

                                    {{-- Modal header --}}
                                    <div class="modal-header">

                                        <div>
                                            <h5 class="modal-title"
                                                id="communicationModalLabel{{ $donation->id }}">
                                                <i class="fas fa-comments me-2"></i>
                                                Communication History
                                            </h5>

                                            <div class="text-muted small mt-1">
                                                {{ $donation->donation_number }}

                                                @if($donation->amount !== null)
                                                    <span class="mx-1">•</span>
                                                    {{ $donation->currency ?: 'KES' }}
                                                    {{ number_format((float) $donation->amount, 2) }}
                                                @endif

                                                @if($donation->donation_date)
                                                    <span class="mx-1">•</span>
                                                    {{ $donation->donation_date->format('d M Y') }}
                                                @endif
                                            </div>
                                        </div>

                                        <button type="button"
                                                class="btn-close"
                                                data-bs-dismiss="modal"
                                                aria-label="Close"></button>

                                    </div>

                                    {{-- Modal body --}}
                                    <div class="modal-body">

                                        @foreach($donationCommunications as $communication)

                                            @php
                                                $statusClasses = [
                                                    'pending' => 'requisition-list-status-pending',
                                                    'sending' => 'requisition-list-status-pending',
                                                    'sent' => 'requisition-list-status-approved',
                                                    'failed' => 'requisition-list-status-rejected',
                                                    'cancelled' => '',
                                                ];

                                                $statusClass =
                                                    $statusClasses[$communication->status]
                                                    ?? '';

                                                $channelIcon = match($communication->channel) {
                                                    'email' => 'fa-envelope',
                                                    'sms' => 'fa-sms',
                                                    default => 'fa-paper-plane',
                                                };
                                            @endphp

                                            <div class="requisition-section mb-3">

                                                {{-- Record header --}}
                                                <div class="requisition-section-header">

                                                    <div class="requisition-section-heading">

                                                        <div class="requisition-section-icon">
                                                            <i class="fas {{ $channelIcon }}"></i>
                                                        </div>

                                                        <div>
                                                            <h5>
                                                                {{ strtoupper($communication->channel) }}
                                                            </h5>

                                                            <p class="text-break">
                                                                {{ $communication->recipient ?: 'Recipient not recorded' }}
                                                            </p>
                                                        </div>

                                                    </div>

                                                    <span class="requisition-list-status {{ $statusClass }}">
                                                        {{ ucfirst($communication->status) }}
                                                    </span>

                                                </div>

                                                <div class="requisition-details-body">

                                                    {{-- Timestamps --}}
                                                    <div class="row g-3 mb-3">

                                                        <div class="col-md-4">
                                                            <div class="requisition-field-label">
                                                                Created
                                                            </div>

                                                            <div>
                                                                {{ $communication->created_at?->format('d M Y H:i') ?? '—' }}
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="requisition-field-label">
                                                                Scheduled
                                                            </div>

                                                            <div>
                                                                {{ $communication->scheduled_at?->format('d M Y H:i') ?? '—' }}
                                                            </div>
                                                        </div>

                                                        <div class="col-md-4">
                                                            <div class="requisition-field-label">
                                                                Sent
                                                            </div>

                                                            <div>
                                                                {{ $communication->sent_at?->format('d M Y H:i') ?? '—' }}
                                                            </div>
                                                        </div>

                                                    </div>

                                                    {{-- Communication type --}}
                                                    @if($communication->type)
                                                        <div class="mb-3">
                                                            <div class="requisition-field-label">
                                                                Communication Type
                                                            </div>

                                                            <div>
                                                                {{ ucfirst(str_replace('_', ' ', $communication->type)) }}
                                                            </div>
                                                        </div>
                                                    @endif

                                                    {{-- Subject --}}
                                                    @if($communication->subject)
                                                        <div class="mb-3">
                                                            <div class="requisition-field-label">
                                                                Subject
                                                            </div>

                                                            <div>
                                                                {{ $communication->subject }}
                                                            </div>
                                                        </div>
                                                    @endif

                                                    {{-- Message --}}
                                                    <div class="mb-3">
                                                        <div class="requisition-field-label">
                                                            Message
                                                        </div>

                                                        <div class="text-break">
                                                            {{ $communication->message ?: 'No message recorded.' }}
                                                        </div>
                                                    </div>

                                                    {{-- Error --}}
                                                    @if($communication->error_message)
                                                        <div class="alert alert-danger mb-3">
                                                            <strong>
                                                                <i class="fas fa-exclamation-triangle me-1"></i>
                                                                Sending Error
                                                            </strong>

                                                            <div class="mt-1 text-break">
                                                                {{ $communication->error_message }}
                                                            </div>
                                                        </div>
                                                    @endif

                                                    {{-- Record actions --}}
                                                    <div class="d-flex flex-wrap align-items-center gap-2 mt-3">

                                                        <a href="{{ route('admin.donation-communications.show', $communication) }}"
                                                           class="requisition-add-button primary">
                                                            <i class="fas fa-eye"></i>
                                                            View Communication
                                                        </a>

                                                        @if(
                                                            in_array($communication->channel, ['sms', 'email'])
                                                            && in_array($communication->status, ['sent', 'failed', 'cancelled'])
                                                        )

                                                            <form method="POST"
                                                                  action="{{ route('admin.donations.resend-thank-you', $donation) }}"
                                                                  onsubmit="return confirm('Schedule a new thank-you {{ $communication->channel }}?')">

                                                                @csrf

                                                                <input type="hidden"
                                                                       name="channel"
                                                                       value="{{ $communication->channel }}">

                                                                <button type="submit"
                                                                        class="requisition-add-button">
                                                                    <i class="fas fa-redo"></i>
                                                                    Resend
                                                                </button>

                                                            </form>

                                                        @endif

                                                    </div>

                                                </div>
                                            </div>

                                        @endforeach

                                    </div>

                                    {{-- Modal footer --}}
                                    <div class="modal-footer">

                                        <span class="text-muted small me-auto">
                                            {{ $communicationCount }}
                                            {{ $communicationCount === 1 ? 'communication' : 'communications' }}
                                            recorded for this donation.
                                        </span>

                                        <button type="button"
                                                class="requisition-cancel-button"
                                                data-bs-dismiss="modal">
                                            Close
                                        </button>

                                    </div>

                                </div>
                            </div>
                        </div>

                    @endif

                @endforeach

            @else

                {{-- Empty donation history --}}
                <div class="requisition-table-empty">

                    <div class="requisition-empty-icon">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>

                    <h5>No donations yet</h5>

                    <p>
                        No donations have been recorded for this donor.
                    </p>

                </div>

            @endif

        </div>
    </div>

</div>

@endsection

