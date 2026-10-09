@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/stores.css') }}">
@endpush

@section('content')

@php
    $status = strtolower((string) ($communication->status ?? 'pending'));
    $channel = strtolower((string) ($communication->channel ?? 'unknown'));

    $donation = $communication->donation ?? null;
    $donor = $donation->donor ?? null;

    $statusLabels = [
        'sent' => 'Sent',
        'failed' => 'Failed',
        'pending' => 'Pending',
        'sending' => 'Sending',
        'cancelled' => 'Cancelled',
    ];

    $statusLabel = $statusLabels[$status] ?? ucfirst($status);

    $statusClasses = [
        'sent' => 'badge-success',
        'failed' => 'badge-danger',
        'pending' => 'badge-warning',
        'sending' => 'badge-warning',
        'cancelled' => 'badge-secondary',
    ];

    $statusClass = $statusClasses[$status] ?? 'badge-secondary';

    $donorName = $donor->name ?? $donor->donor_name ?? null;
    $donorEmail = $donor->email ?? $donor->email_address ?? null;
    $donorPhone = $donor->phone ?? $donor->phone_number ?? $donor->mobile ?? null;

    $donationReference = $donation->donation_number
        ?? $donation->reference_number
        ?? null;

    $createdAt = $communication->created_at ?? null;
    $sentAt = $communication->sent_at ?? null;
    $donationCurrency = $donation->currency ?? 'KES';
    $donationAmount = $donation->amount ?? null;
@endphp

<div class="store-requisition-page">

    {{-- PAGE HEADER --}}
    <div class="requisition-page-header">
        <div class="requisition-header-content">
            <div class="requisition-header-icon">
                <i class="fas fa-envelope"></i>
            </div>

            <div>
                <div class="requisition-breadcrumb">
                    <a href="{{ route('admin.donation-communications.index') }}">
                        Communications
                    </a>
                    <i class="fas fa-chevron-right"></i>
                    <span>Details</span>
                </div>

                <h1 class="requisition-page-title">
                    Communication Details
                </h1>

                <p class="requisition-page-subtitle">
                    View message delivery and associated donor information.
                </p>
            </div>
        </div>

        <div class="requisition-header-right">
            <div class="requisition-page-actions">
                <a href="{{ route('admin.donation-communications.index') }}"
                   class="requisition-cancel-button">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Back
                </a>
            </div>
        </div>
    </div>

    <x-message></x-message>

    {{-- STATUS SUMMARY --}}
    <div class="requisition-state-panel">
        <div class="requisition-state-main">
            <div class="requisition-state-icon">
                @if($status === 'sent')
                    <i class="fas fa-check-circle"></i>
                @elseif($status === 'failed')
                    <i class="fas fa-exclamation-circle"></i>
                @elseif($status === 'cancelled')
                    <i class="fas fa-ban"></i>
                @else
                    <i class="fas fa-clock"></i>
                @endif
            </div>

            <div class="requisition-state-text">
                <strong>Communication {{ $statusLabel }}</strong>
                <span>
                    {{ $communication->recipient ?? 'No recipient recorded' }}
                </span>
            </div>
        </div>

        <div class="requisition-state-actions">
            <span class="badge {{ $statusClass }}">
                {{ $statusLabel }}
            </span>
        </div>
    </div>

    {{-- TWO-COLUMN CONTENT --}}
    <div class="row">

        {{-- LEFT COLUMN: COMMUNICATION --}}
        <div class="col-lg-8">

            {{-- MESSAGE INFORMATION --}}
            <div class="requisition-section">
                <div class="requisition-section-header">
                    <div class="requisition-section-heading">
                        <span class="requisition-section-icon">
                            <i class="fas fa-paper-plane"></i>
                        </span>

                        <div>
                            <h5>Message Information</h5>
                            <p>Channel and delivery details.</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="requisition-detail-item">
                            <div class="requisition-detail-label">Channel</div>
                            <div class="requisition-detail-value fw-semibold">
                                {{ ucfirst($channel) }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="requisition-detail-item">
                            <div class="requisition-detail-label">Status</div>
                            <div class="requisition-detail-value">
                                <span class="badge {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="requisition-detail-item">
                            <div class="requisition-detail-label">Recipient</div>
                            <div class="requisition-detail-value">
                                {{ $communication->recipient ?? '—' }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="requisition-detail-item">
                            <div class="requisition-detail-label">Communication Type</div>
                            <div class="requisition-detail-value">
                                {{ $communication->type
                                    ? ucfirst(str_replace('_', ' ', $communication->type))
                                    : '—' }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="requisition-detail-item">
                            <div class="requisition-detail-label">Created At</div>
                            <div class="requisition-detail-value">
                                {{ $createdAt ? \Carbon\Carbon::parse($createdAt)->format('d M Y, H:i') : '—' }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="requisition-detail-item">
                            <div class="requisition-detail-label">Sent At</div>
                            <div class="requisition-detail-value">
                                {{ $sentAt ? \Carbon\Carbon::parse($sentAt)->format('d M Y, H:i') : 'Not sent' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SUBJECT --}}
            <div class="requisition-section">
                <div class="requisition-section-header">
                    <div class="requisition-section-heading">
                        <span class="requisition-section-icon">
                            <i class="fas fa-heading"></i>
                        </span>

                        <div>
                            <h5>Subject</h5>
                            <p>Message subject line.</p>
                        </div>
                    </div>
                </div>

                <div class="requisition-detail-item">
                    <div class="requisition-detail-value fw-semibold">
                        {{ $communication->subject ?: 'No subject recorded' }}
                    </div>
                </div>
            </div>

            {{-- MESSAGE BODY --}}
            <div class="requisition-section">
                <div class="requisition-section-header">
                    <div class="requisition-section-heading">
                        <span class="requisition-section-icon">
                            <i class="fas fa-comment-alt"></i>
                        </span>

                        <div>
                            <h5>Message Content</h5>
                            <p>Recorded communication message.</p>
                        </div>
                    </div>
                </div>

                <div class="requisition-detail-item">
                    <div class="requisition-detail-value">
                        {!! nl2br(e($communication->message ?? 'No message content recorded.')) !!}
                    </div>
                </div>

                @if($status === 'failed' && $communication->error_message)
                    <div class="requisition-detail-item">
                        <div class="requisition-detail-label text-danger">
                            <i class="fas fa-exclamation-triangle mr-1"></i>
                            Delivery Error
                        </div>

                        <div class="requisition-detail-value text-danger">
                            {{ $communication->error_message }}
                        </div>
                    </div>
                @endif
            </div>

        </div>

        {{-- RIGHT COLUMN: DONOR AND DONATION --}}
        <div class="col-lg-4">

            {{-- DONOR INFORMATION --}}
            <div class="requisition-section">
                <div class="requisition-section-header">
                    <div class="requisition-section-heading">
                        <span class="requisition-section-icon">
                            <i class="fas fa-user"></i>
                        </span>

                        <div>
                            <h5>Donor Information</h5>
                            <p>Contact associated with this communication.</p>
                        </div>
                    </div>
                </div>

                <div class="requisition-detail-item">
                    <div class="requisition-detail-label">Donor Name</div>

                    <div class="requisition-detail-value fw-semibold">
                        @if($donor)
                            {{ $donorName ?: 'Name not recorded' }}
                        @else
                            Donor information unavailable
                        @endif
                    </div>
                </div>

                <div class="requisition-detail-item">
                    <div class="requisition-detail-label">Email Address</div>

                    <div class="requisition-detail-value">
                        @if($donorEmail)
                            <a href="mailto:{{ $donorEmail }}">
                                {{ $donorEmail }}
                            </a>
                        @else
                            —
                        @endif
                    </div>
                </div>

                <div class="requisition-detail-item">
                    <div class="requisition-detail-label">Phone Number</div>

                    <div class="requisition-detail-value">
                        @if($donorPhone)
                            <a href="tel:{{ $donorPhone }}">
                                {{ $donorPhone }}
                            </a>
                        @else
                            —
                        @endif
                    </div>
                </div>

                @if($donor && \Illuminate\Support\Facades\Route::has('admin.donors.show'))
                    <div class="requisition-detail-item">
                        <a href="{{ route('admin.donors.show', $donor) }}"
                           class="requisition-donation-link">
                            <i class="fas fa-external-link-alt mr-1"></i>
                            View Donor
                        </a>
                    </div>
                @endif
            </div>

            {{-- RELATED DONATION --}}
            <div class="requisition-section">
                <div class="requisition-section-header">
                    <div class="requisition-section-heading">
                        <span class="requisition-section-icon">
                            <i class="fas fa-hand-holding-heart"></i>
                        </span>

                        <div>
                            <h5>Related Donation</h5>
                            <p>Donation associated with this message.</p>
                        </div>
                    </div>
                </div>

                @if($donation)

                    <div class="requisition-detail-item">
                        <div class="requisition-detail-label">Donation Reference</div>

                        <div class="requisition-detail-value fw-semibold">
                            {{ $donationReference ?: 'Not recorded' }}
                        </div>
                    </div>

                    @if(isset($donation->amount))
                        <div class="requisition-detail-item">
                            <div class="requisition-detail-label">Amount</div>

                            <div class="requisition-detail-value fw-semibold">
                                {{ $donationCurrency }}
                                {{ number_format((float) $donationAmount, 2) }}
                            </div>
                        </div>
                    @endif

                    @if(isset($donation->donation_date))
                        <div class="requisition-detail-item">
                            <div class="requisition-detail-label">Donation Date</div>

                            <div class="requisition-detail-value">
                                {{ \Carbon\Carbon::parse($donation->donation_date)->format('d M Y') }}
                            </div>
                        </div>
                    @endif

                    @if(\Illuminate\Support\Facades\Route::has('admin.donations.show'))
                        <div class="requisition-detail-item">
                            <a href="{{ route('admin.donations.show', $donation) }}"
                               class="requisition-donation-link">
                                <i class="fas fa-external-link-alt mr-1"></i>
                                View Donation
                            </a>
                        </div>
                    @endif

                @else

                    <div class="requisition-detail-item">
                        <div class="requisition-detail-value text-muted">
                            No related donation is linked to this communication.
                        </div>
                    </div>

                @endif
            </div>

        </div>
    </div>

    {{-- BOTTOM ACTIONS --}}
    <div class="requisition-bottom-actions">
        <div class="requisition-bottom-actions-left">
            <a href="{{ route('admin.donation-communications.index') }}"
               class="requisition-cancel-button">
                <i class="fas fa-arrow-left mr-1"></i>
                Back to Communications
            </a>
        </div>

        <div class="requisition-bottom-actions-right">
            @if($status === 'pending' &&
                \Illuminate\Support\Facades\Route::has('admin.donation-communications.cancel'))

                <form action="{{ route('admin.donation-communications.cancel', $communication) }}"
                      method="POST"
                      onsubmit="return confirm('Are you sure you want to cancel this communication?');">
                    @csrf
                    @method('PATCH')

                    <button type="submit" class="requisition-cancel-button">
                        <i class="fas fa-ban mr-1"></i>
                        Cancel Communication
                    </button>
                </form>
            @endif
        </div>
    </div>

</div>
@endsection