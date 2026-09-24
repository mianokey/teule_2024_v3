@extends('layouts.admin')

@section('content')

<div class="communication-page">

    {{-- Header --}}
    <div class="page-header">

        <div>

            <div class="text-muted small mb-1">

                Communications /

                {{ $communication->donation?->donation_number ?? 'Message' }}

            </div>

            <h5 class="mb-0">
                Communication Details
            </h5>

        </div>


        <div class="page-actions">

            <a href="{{ route(
                'admin.donation-communications.index'
            ) }}"
               class="btn btn-light btn-sm">

                <i class="fas fa-arrow-left me-1"></i>

                Back

            </a>

        </div>

    </div>


    <x-message></x-message>


    <div class="row">

        {{-- Main message --}}
        <div class="col-lg-8">

            <div class="simple-card">

                <div class="simple-card-header">

                    <div>

                        <span class="text-muted small">

                            @if($communication->channel === 'sms')

                                <i class="fas fa-sms me-1"></i>

                            @elseif($communication->channel === 'email')

                                <i class="fas fa-envelope me-1"></i>

                            @endif

                            {{ ucfirst($communication->channel) }}

                        </span>


                        <h5 class="mb-0 mt-1">

                            {{ ucfirst(str_replace(
                                '_',
                                ' ',
                                $communication->type
                            )) }}

                        </h5>

                    </div>


                    @php

                        $statusClasses = [

                            'pending'   => 'status-pending',

                            'sending'   => 'status-sending',

                            'sent'      => 'status-sent',

                            'failed'    => 'status-failed',

                            'cancelled' => 'status-cancelled',

                        ];

                        $statusClass =
                            $statusClasses[$communication->status]
                            ?? 'status-pending';

                    @endphp


                    <span class="status-badge {{ $statusClass }}">

                        {{ ucfirst($communication->status) }}

                    </span>

                </div>


                {{-- Communication information --}}
                <div class="details-grid">


                    {{-- Recipient --}}
                    <div>

                        <span class="label">
                            Recipient
                        </span>

                        <span class="value">

                            {{ $communication->recipient }}

                        </span>

                    </div>


                    {{-- Channel --}}
                    <div>

                        <span class="label">
                            Channel
                        </span>

                        <span class="value">

                            {{ strtoupper($communication->channel) }}

                        </span>

                    </div>


                    {{-- Created --}}
                    <div>

                        <span class="label">
                            Created
                        </span>

                        <span class="value">

                            {{ $communication->created_at?->format(
                                'd M Y H:i:s'
                            ) }}

                        </span>

                    </div>


                    {{-- Scheduled --}}
                    <div>

                        <span class="label">
                            Scheduled
                        </span>

                        <span class="value">

                            {{ $communication->scheduled_at?->format(
                                'd M Y H:i:s'
                            ) ?? 'Immediately' }}

                        </span>

                    </div>


                    {{-- Sent --}}
                    <div>

                        <span class="label">
                            Sent At
                        </span>

                        <span class="value">

                            {{ $communication->sent_at?->format(
                                'd M Y H:i:s'
                            ) ?? 'Not sent' }}

                        </span>

                    </div>


                    {{-- Cancelled --}}
                    <div>

                        <span class="label">
                            Cancelled At
                        </span>

                        <span class="value">

                            {{ $communication->cancelled_at?->format(
                                'd M Y H:i:s'
                            ) ?? 'Not cancelled' }}

                        </span>

                    </div>


                    {{-- Delivered --}}
                    <div>

                        <span class="label">
                            Delivered At
                        </span>

                        <span class="value">

                            {{ $communication->delivered_at?->format(
                                'd M Y H:i:s'
                            ) ?? 'Not available' }}

                        </span>

                    </div>


                    {{-- Sent By --}}
                    <div>

                        <span class="label">
                            Created By
                        </span>

                        <span class="value">

                            {{ $communication->sentBy?->name ?? 'System' }}

                        </span>

                    </div>


                </div>


                {{-- Subject --}}
                @if($communication->subject)

                    <div class="text-section">

                        <span class="label">
                            Subject
                        </span>

                        <p>
                            {{ $communication->subject }}
                        </p>

                    </div>

                @endif


                {{-- Message --}}
                <div class="text-section">

                    <span class="label">
                        Message
                    </span>

                    <div class="message-box">

                        {{ $communication->message }}

                    </div>

                </div>


                {{-- Provider reference --}}
                @if($communication->provider_reference)

                    <div class="text-section">

                        <span class="label">
                            Provider Reference
                        </span>

                        <p>

                            {{ $communication->provider_reference }}

                        </p>

                    </div>

                @endif


                {{-- Error --}}
                @if($communication->error_message)

                    <div class="text-section">

                        <span class="label">
                            Error
                        </span>

                        <div class="error-box">

                            {{ $communication->error_message }}

                        </div>

                    </div>

                @endif


                {{-- Cancellation --}}
                @if($communication->status === 'pending')

                    <div class="action-section">

                        <form method="POST"
                              action="{{ route(
                                  'admin.donation-communications.cancel',
                                  $communication
                              ) }}"
                              onsubmit="return confirm(
                                  'Cancel this message?'
                              )">

                            @csrf

                            @method('PATCH')

                            <button type="submit"
                                    class="btn btn-outline-danger btn-sm">

                                <i class="fas fa-ban me-1"></i>

                                Cancel Sending

                            </button>

                            <span class="text-muted small ms-2">

                                This will prevent the pending message
                                from being sent.

                            </span>

                        </form>

                    </div>

                @endif

            </div>

        </div>


        {{-- Sidebar --}}
        <div class="col-lg-4">


            {{-- Donor --}}
            <div class="simple-card mb-3">

                <div class="simple-card-header">

                    <strong>
                        Donor
                    </strong>

                </div>


                <div class="sidebar-body">

                    @if($communication->donor)

                        <div class="donor-name">

                            {{ $communication->donor->name }}

                        </div>


                        <div class="text-muted small mb-3">

                            {{ $communication->donor->donor_number }}

                        </div>


                        @if($communication->donor->phone)

                            <div class="sidebar-row">

                                <span>
                                    Phone
                                </span>

                                <strong>

                                    {{ $communication->donor->phone }}

                                </strong>

                            </div>

                        @endif


                        @if($communication->donor->email)

                            <div class="sidebar-row">

                                <span>
                                    Email
                                </span>

                                <strong>

                                    {{ $communication->donor->email }}

                                </strong>

                            </div>

                        @endif


                        <a href="{{ route(
                            'admin.donors.show',
                            $communication->donor
                        ) }}"
                           class="btn btn-light btn-sm w-100 mt-3">

                            <i class="fas fa-user me-1"></i>

                            View Donor

                        </a>

                    @else

                        <div class="text-muted">

                            Unknown donor

                        </div>

                    @endif

                </div>

            </div>


            {{-- Donation --}}
            <div class="simple-card">

                <div class="simple-card-header">

                    <strong>
                        Donation
                    </strong>

                </div>


                <div class="sidebar-body">

                    @if($communication->donation)

                        <div class="sidebar-row">

                            <span>
                                Number
                            </span>

                            <strong>

                                {{ $communication->donation->donation_number }}

                            </strong>

                        </div>


                        <div class="sidebar-row">

                            <span>
                                Amount
                            </span>

                            <strong>

                                {{ $communication->donation->currency }}

                                {{ number_format(
                                    $communication->donation->amount ?? 0,
                                    2
                                ) }}

                            </strong>

                        </div>


                        <div class="sidebar-row">

                            <span>
                                Date
                            </span>

                            <strong>

                                {{ $communication->donation->donation_date?->format(
                                    'd M Y'
                                ) }}

                            </strong>

                        </div>


                        @if($communication->donation->classification)

                            <div class="sidebar-row">

                                <span>
                                    Classification
                                </span>

                                <strong>

                                    {{ ucfirst(
                                        $communication->donation->classification
                                    ) }}

                                </strong>

                            </div>

                        @endif


                        <a href="{{ route(
                            'admin.donations.show',
                            $communication->donation
                        ) }}"
                           class="btn btn-light btn-sm w-100 mt-3">

                            <i class="fas fa-hand-holding-heart me-1"></i>

                            View Donation

                        </a>

                    @else

                        <div class="text-muted">

                            Donation not available

                        </div>

                    @endif

                </div>

            </div>

        </div>

    </div>

</div>


<style>

    .communication-page {
        padding-bottom: 30px;
    }


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
    }


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
        word-break: break-word;
    }


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


    .message-box {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 6px;
        padding: 15px;
        color: #495057;
        font-size: 14px;
        line-height: 1.7;
        white-space: pre-line;
    }


    .error-box {
        background: #ffebee;
        border: 1px solid #ffcdd2;
        border-radius: 6px;
        padding: 12px 15px;
        color: #c62828;
        font-size: 13px;
        line-height: 1.6;
        word-break: break-word;
    }


    .action-section {
        padding: 15px 20px;
        border-top: 1px solid #f0f1f2;
        background: #fffdfd;
    }


    .status-badge {
        display: inline-block;
        padding: 5px 9px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 500;
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


    @media (max-width: 767px) {

        .page-header {
            align-items: flex-start;
            flex-direction: column;
        }


        .page-actions {
            width: 100%;
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
        .text-section,
        .action-section {
            padding: 13px 16px;
        }


        .action-section form {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }


        .action-section .ms-2 {
            margin-left: 0 !important;
        }

    }

</style>

@endsection