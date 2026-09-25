@extends('layouts.admin')

@section('content')

@php
$customerName = trim(
($mpesaTransaction->first_name ?? '') . ' ' .
($mpesaTransaction->middle_name ?? '') . ' ' .
($mpesaTransaction->last_name ?? '')
);


$customerName = $customerName ?: 'Unknown';

$statusClass = match ($mpesaTransaction->status) {
    'confirmed' => 'success',
    'rejected' => 'danger',
    default => 'warning',
};

$classificationLabel = match ($mpesaTransaction->classification) {
    'donation' => 'Donation',
    'payment' => 'Payment',
    'refund' => 'Refund',
    'other' => 'Other',
    default => 'Unclassified',
};


@endphp

<style>
    .mpesa-dashboard {
        max-width: 1400px;
        margin: 0 auto;
    }

    .mpesa-hero {
        border-radius: 16px;
        background: linear-gradient(
            135deg,
            #111827 0%,
            #1f2937 100%
        );
        color: #fff;
        padding: 24px;
        box-shadow: 0 8px 30px rgba(0, 0, 0, .08);
    }

    .mpesa-amount {
        font-size: 2rem;
        font-weight: 700;
        letter-spacing: -1px;
    }

    .mpesa-label {
        font-size: .72rem;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #9ca3af;
        margin-bottom: 3px;
    }

    .mpesa-value {
        font-weight: 600;
    }

    .mpesa-card {
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #fff;
        height: 100%;
        box-shadow: 0 3px 15px rgba(0, 0, 0, .035);
    }

    .mpesa-card-header {
        padding: 14px 18px;
        border-bottom: 1px solid #f0f0f0;
        font-weight: 600;
    }

    .mpesa-card-body {
        padding: 18px;
    }

    .mpesa-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border-radius: 20px;
        padding: 5px 11px;
        font-size: .78rem;
        font-weight: 600;
    }

    .mpesa-status-warning {
        background: #fff3cd;
        color: #856404;
    }

    .mpesa-status-success {
        background: #d1e7dd;
        color: #0f5132;
    }

    .mpesa-status-danger {
        background: #f8d7da;
        color: #842029;
    }

    .mpesa-match {
        border: 1px dashed #0d6efd;
        background: #f4f8ff;
        border-radius: 10px;
        padding: 12px;
    }

    .mpesa-review {
        background: #f8fafc;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        padding: 18px;
    }

    .mpesa-input {
        border-radius: 9px;
    }

    .mpesa-action {
        border-radius: 9px;
        padding: 9px 18px;
        font-weight: 600;
    }

    .mpesa-mini {
        font-size: .78rem;
        color: #6b7280;
    }

    @media (max-width: 991px) {
        .mpesa-amount {
            font-size: 1.6rem;
        }
    }
</style>

<div class="mpesa-dashboard">


{{-- HEADER --}}
<div class="mpesa-hero mb-3">

    <div class="d-flex justify-content-between align-items-start">

        <div>

            <div class="mpesa-label">
                M-Pesa Paybill Transaction
            </div>

            <div class="mpesa-amount">
                KES {{ number_format((float) $mpesaTransaction->amount, 2) }}
            </div>

            <div class="mt-1 small">
                {{ $mpesaTransaction->transaction_id }}
            </div>

        </div>

        <div class="text-end">

            @if($mpesaTransaction->status === 'confirmed')

                <span class="mpesa-status mpesa-status-success">
                    ● Confirmed
                </span>

            @elseif($mpesaTransaction->status === 'rejected')

                <span class="mpesa-status mpesa-status-danger">
                    ● Rejected
                </span>

            @else

                <span class="mpesa-status mpesa-status-warning">
                    ● Pending Review
                </span>

            @endif

            <div class="mt-3">
                <a
                    href="{{ route('admin.mpesa-transactions.index') }}"
                    class="btn btn-sm btn-light"
                >
                    ← Back
                </a>
            </div>

        </div>

    </div>

</div>


{{-- MAIN GRID --}}
<div class="row g-3">

    {{-- CUSTOMER --}}
    <div class="col-lg-4">

        <div class="mpesa-card">

            <div class="mpesa-card-header">
                Customer
            </div>

            <div class="mpesa-card-body">

                <div class="mb-3">

                    <div class="mpesa-label">
                        Name
                    </div>

                    <div class="mpesa-value">
                        {{ $customerName }}
                    </div>

                </div>

                <div class="mb-3">

                    <div class="mpesa-label">
                        Phone
                    </div>

                    <div class="mpesa-value">
                        {{ $mpesaTransaction->phone_number ?: '—' }}
                    </div>

                </div>

                <div class="mb-3">

                    <div class="mpesa-label">
                        Bill Reference
                    </div>

                    <div class="mpesa-value">
                        {{ $mpesaTransaction->bill_ref_number ?: '—' }}
                    </div>

                </div>

                <div>

                    <div class="mpesa-label">
                        Transaction Time
                    </div>

                    <div class="mpesa-value">
                        {{ $mpesaTransaction->trans_time ?: '—' }}
                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- TRANSACTION --}}
    <div class="col-lg-4">

        <div class="mpesa-card">

            <div class="mpesa-card-header">
                Transaction Details
            </div>

            <div class="mpesa-card-body">

                <div class="row g-3">

                    <div class="col-6">

                        <div class="mpesa-label">
                            Type
                        </div>

                        <div class="mpesa-value">
                            {{ $mpesaTransaction->transaction_type ?: '—' }}
                        </div>

                    </div>

                    <div class="col-6">

                        <div class="mpesa-label">
                            Short Code
                        </div>

                        <div class="mpesa-value">
                            {{ $mpesaTransaction->business_short_code ?: '—' }}
                        </div>

                    </div>

                    <div class="col-6">

                        <div class="mpesa-label">
                            Classification
                        </div>

                        <div>
                            <span class="badge bg-light text-dark border">
                                {{ $classificationLabel }}
                            </span>
                        </div>

                    </div>

                    <div class="col-6">

                        <div class="mpesa-label">
                            Invoice
                        </div>

                        <div class="mpesa-value">
                            {{ $mpesaTransaction->invoice_number ?: '—' }}
                        </div>

                    </div>

                    <div class="col-6">

                        <div class="mpesa-label">
                            Org. Balance
                        </div>

                        <div class="mpesa-value">
                            {{ $mpesaTransaction->org_account_balance ?: '—' }}
                        </div>

                    </div>

                    <div class="col-6">

                        <div class="mpesa-label">
                            Received
                        </div>

                        <div class="mpesa-mini">
                            {{ $mpesaTransaction->received_at?->format('d/m/Y H:i') ?: '—' }}
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- DONOR --}}
    <div class="col-lg-4">

        <div class="mpesa-card">

            <div class="mpesa-card-header">
                Donor Matching
            </div>

            <div class="mpesa-card-body">

                @if($mpesaTransaction->donor)

                    <div class="mpesa-match">

                        <div class="mpesa-label">
                            Matched Donor
                        </div>

                        <strong>
                            {{ $mpesaTransaction->donor->name }}
                        </strong>

                        @if($mpesaTransaction->donor->phone)

                            <div class="mpesa-mini">
                                {{ $mpesaTransaction->donor->phone }}
                            </div>

                        @endif

                    </div>

                @elseif($matchedDonor)

                    <div class="mpesa-match">

                        <div class="mpesa-label">
                            Possible Match
                        </div>

                        <strong>
                            {{ $matchedDonor->name }}
                        </strong>

                        @if($matchedDonor->phone)

                            <div class="mpesa-mini">
                                {{ $matchedDonor->phone }}
                            </div>

                        @endif

                        @if($mpesaTransaction->status === 'pending_review')

                            <button
                                type="button"
                                class="btn btn-sm btn-primary mt-2"
                                onclick="document.getElementById('donor_id').value='{{ $matchedDonor->id }}'"
                            >
                                Use This Donor
                            </button>

                        @endif

                    </div>

                @else

                    <div class="text-muted small">
                        No donor match found for this phone number.
                    </div>

                @endif

            </div>

        </div>

    </div>

</div>


{{-- REVIEW --}}
@if($mpesaTransaction->status === 'pending_review')

    <div class="mpesa-review mt-3">

        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>

                <strong>
                    Review Transaction
                </strong>

                <div class="mpesa-mini">
                    Confirm the classification and donor before posting.
                </div>

            </div>

            <span class="mpesa-status mpesa-status-warning">
                Action Required
            </span>

        </div>


        <form
            method="POST"
            id="confirm-form"
            action="{{ route(
                'admin.mpesa-transactions.confirm',
                $mpesaTransaction
            ) }}"
        >

            @csrf

            <div class="row g-3">

                <div class="col-lg-3">

                    <label class="form-label small">
                        Classification
                    </label>

                    <select
                        name="classification"
                        class="form-select mpesa-input"
                        required
                    >
                        <option value="">
                            Select
                        </option>

                        <option value="donation">
                            Donation
                        </option>

                        <option value="payment">
                            Payment
                        </option>

                        <option value="refund">
                            Refund
                        </option>

                        <option value="other">
                            Other
                        </option>

                        <option value="unclassified">
                            Unclassified
                        </option>

                    </select>

                </div>


                <div class="col-lg-5">

                    <label class="form-label small">
                        Donor
                    </label>

                    <select
                        name="donor_id"
                        id="donor_id"
                        class="form-select mpesa-input"
                    >

                        <option value="">
                            Select donor
                        </option>

                        @foreach($donors as $donor)

                            <option
                                value="{{ $donor->id }}"
                                @selected(
                                    $mpesaTransaction->donor_id == $donor->id
                                )
                            >
                                {{ $donor->name }}
                                @if($donor->phone)
                                    — {{ $donor->phone }}
                                @endif
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="col-lg-4">

                    <label class="form-label small">
                        Notes
                    </label>

                    <input
                        type="text"
                        name="notes"
                        class="form-control mpesa-input"
                        placeholder="Optional note"
                    >

                </div>

            </div>


            <div class="d-flex justify-content-end gap-2 mt-3">

                <button
                    type="submit"
                    class="btn btn-success mpesa-action"
                >
                    ✓ Confirm Transaction
                </button>

                <button
                    type="submit"
                    form="reject-form"
                    class="btn btn-outline-danger mpesa-action"
                    onclick="return confirm(
                        'Are you sure you want to reject this transaction?'
                    );"
                >
                    Reject
                </button>

            </div>

        </form>


        <form
            method="POST"
            id="reject-form"
            action="{{ route(
                'admin.mpesa-transactions.reject',
                $mpesaTransaction
            ) }}"
        >
            @csrf
        </form>

    </div>

@else

    <div class="mt-3">

        <div class="alert alert-secondary mb-0">
            This transaction has already been reviewed.

            @if($mpesaTransaction->reviewedBy)
                Reviewed by
                <strong>{{ $mpesaTransaction->reviewedBy->name }}</strong>.
            @endif
        </div>

    </div>

@endif


{{-- LINKED DONATION --}}
@if($mpesaTransaction->donation)

    <div class="text-end mt-2">

        <small class="text-muted">
            Linked Donation:
        </small>

        <a href="{{ route(
            'admin.donations.show',
            $mpesaTransaction->donation
        ) }}">
            {{ $mpesaTransaction->donation->donation_number }}
        </a>

    </div>

@endif


</div>

@endsection
