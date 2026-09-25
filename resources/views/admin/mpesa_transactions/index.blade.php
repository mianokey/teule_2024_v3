@extends('layouts.admin')

@section('content')

<style>
    .mpesa-page {
        --mpesa-primary: #0b1f3a;
        --mpesa-accent: #00a884;
        --mpesa-border: #e7ebf0;
        --mpesa-muted: #7b8794;
        --mpesa-bg: #f6f8fb;
    }

    .mpesa-shell {
        max-width: 1500px;
        margin: 0 auto;
    }

    /* Header */
    .mpesa-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        margin-bottom: 14px;
    }

    .mpesa-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .mpesa-icon {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #0b1f3a, #123d68);
        color: #fff;
        font-size: 16px;
        box-shadow: 0 5px 15px rgba(11, 31, 58, .15);
    }

    .mpesa-title h5 {
        margin: 0;
        font-weight: 700;
        color: var(--mpesa-primary);
        letter-spacing: -.2px;
    }

    .mpesa-title small {
        color: var(--mpesa-muted);
        font-size: 11px;
    }

    /* Summary */
    .mpesa-summary {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        margin-bottom: 14px;
    }

    .summary-card {
        background: #fff;
        border: 1px solid var(--mpesa-border);
        border-radius: 10px;
        padding: 11px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 66px;
    }

    .summary-label {
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: .7px;
        color: var(--mpesa-muted);
        font-weight: 700;
    }

    .summary-value {
        font-size: 19px;
        line-height: 1.2;
        font-weight: 700;
        color: var(--mpesa-primary);
        margin-top: 2px;
    }

    .summary-dot {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f1f5f9;
        color: var(--mpesa-primary);
        font-size: 12px;
    }

    .summary-card.pending .summary-dot {
        background: #fff8df;
        color: #a87800;
    }

    .summary-card.confirmed .summary-dot {
        background: #eaf8f3;
        color: #008765;
    }

    .summary-card.amount .summary-dot {
        background: #edf4ff;
        color: #2764ad;
    }

    /* Main card */
    .mpesa-card {
        background: #fff;
        border: 1px solid var(--mpesa-border);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(15, 23, 42, .035);
    }

    .mpesa-card-top {
        padding: 10px 14px;
        border-bottom: 1px solid var(--mpesa-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10px;
    }

    .mpesa-card-top span {
        font-size: 11px;
        color: var(--mpesa-muted);
    }

    .mpesa-card-top strong {
        font-size: 13px;
        color: var(--mpesa-primary);
    }

    /* Table */
    .mpesa-table {
        margin: 0;
        border: 0 !important;
    }

    .mpesa-table thead th {
        background: #fafbfc;
        border-top: 0;
        border-bottom: 1px solid var(--mpesa-border);
        color: #7a8694;
        font-size: 9px;
        text-transform: uppercase;
        letter-spacing: .65px;
        font-weight: 700;
        padding: 9px 10px;
        white-space: nowrap;
    }

    .mpesa-table tbody td {
        padding: 9px 10px;
        border-color: #eef1f4;
        font-size: 12px;
        color: #344054;
        vertical-align: middle;
        white-space: nowrap;
    }

    .mpesa-table tbody tr {
        transition: background .15s ease;
    }

    .mpesa-table tbody tr:hover {
        background: #fafcff;
    }

    .transaction-id {
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        font-size: 11px;
        font-weight: 700;
        color: #17385f;
    }

    .donor-name {
        font-weight: 600;
        color: #26384d;
    }

    .phone-number,
    .reference {
        color: #667085;
        font-size: 11px;
    }

    .amount {
        font-weight: 700;
        color: #142b46;
    }

    .date-main {
        font-size: 11px;
        font-weight: 600;
        color: #44546a;
    }

    .date-time {
        font-size: 10px;
        color: #98a2b3;
    }

    /* Pills */
    .mpesa-pill {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        border-radius: 20px;
        padding: 4px 8px;
        font-size: 9px;
        font-weight: 700;
        letter-spacing: .15px;
        border: 1px solid transparent;
    }

    .mpesa-pill-dot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        background: currentColor;
    }

    .pill-donation {
        color: #087f5b;
        background: #eaf8f3;
        border-color: #d5f0e7;
    }

    .pill-payment {
        color: #2864a8;
        background: #edf4ff;
        border-color: #dce9fa;
    }

    .pill-refund {
        color: #936d00;
        background: #fff8df;
        border-color: #f7eabd;
    }

    .pill-other {
        color: #667085;
        background: #f2f4f7;
        border-color: #e5e7eb;
    }

    .pill-unclassified {
        color: #667085;
        background: #fff;
        border-color: #d9dee5;
    }

    .pill-confirmed {
        color: #087f5b;
        background: #eaf8f3;
        border-color: #d5f0e7;
    }

    .pill-rejected {
        color: #b42318;
        background: #fff0ef;
        border-color: #fbd5d2;
    }

    .pill-pending {
        color: #936d00;
        background: #fff8df;
        border-color: #f7eabd;
    }

    /* View button */
    .mpesa-view-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 5px;
        min-width: 56px;
        height: 29px;
        padding: 0 9px;
        border-radius: 7px;
        font-size: 10px;
        font-weight: 700;
        text-decoration: none;
        color: #fff !important;
        background: linear-gradient(135deg, #0b1f3a, #174a78);
        border: 0;
        box-shadow: 0 3px 8px rgba(11, 31, 58, .12);
        transition: all .15s ease;
    }

    .mpesa-view-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 5px 12px rgba(11, 31, 58, .18);
    }

    /* Empty state */
    .mpesa-empty {
        padding: 55px 20px;
        text-align: center;
    }

    .mpesa-empty-icon {
        width: 46px;
        height: 46px;
        border-radius: 12px;
        margin: 0 auto 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f1f4f8;
        color: #8090a0;
        font-size: 18px;
    }

    .mpesa-empty h6 {
        margin-bottom: 4px;
        color: #344054;
        font-weight: 700;
    }

    .mpesa-empty p {
        margin: 0;
        color: #98a2b3;
        font-size: 11px;
    }

    /* Responsive */
    @media (max-width: 1100px) {
        .mpesa-summary {
            grid-template-columns: repeat(2, 1fr);
        }

        .mpesa-table {
            min-width: 1050px;
        }
    }

    @media (max-width: 576px) {
        .mpesa-summary {
            grid-template-columns: 1fr 1fr;
            gap: 7px;
        }

        .summary-card {
            padding: 9px 10px;
        }

        .summary-value {
            font-size: 16px;
        }

        .summary-dot {
            width: 26px;
            height: 26px;
        }

        .mpesa-header {
            align-items: flex-start;
        }

        .mpesa-title h5 {
            font-size: 14px;
        }
    }

    .mpesa-reminder-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    border-radius: 12px;
    padding: 3px 7px;
    font-size: 9px;
    font-weight: 700;
    white-space: nowrap;
}

.reminder-none {
    color: #667085;
    background: #f2f4f7;
}

.reminder-1 {
    color: #936d00;
    background: #fff8df;
}

.reminder-2 {
    color: #087f5b;
    background: #eaf8f3;
}

</style>

@php
    $totalTransactions = $transactions->count();

    $pendingCount = $transactions
        ->where('status', 'pending_review')
        ->count();

    $confirmedCount = $transactions
        ->where('status', 'confirmed')
        ->count();

    $totalAmount = $transactions->sum(
        fn ($transaction) => (float) $transaction->amount
    );
@endphp

<div class="mpesa-page">
    <div class="mpesa-shell">

        {{-- Header --}}
        <div class="mpesa-header">

            <div class="mpesa-title">

                <div class="mpesa-icon">
                    <i class="fas fa-mobile-alt"></i>
                </div>

                <div>
                    <h5>M-Pesa Transactions</h5>
                    <small>Review and classify incoming Paybill transactions</small>
                </div>

            </div>

        </div>

        <x-message></x-message>

        {{-- Summary --}}
        <div class="mpesa-summary">

            <div class="summary-card">

                <div>
                    <div class="summary-label">
                        Transactions
                    </div>

                    <div class="summary-value">
                        {{ number_format($totalTransactions) }}
                    </div>
                </div>

                <div class="summary-dot">
                    <i class="fas fa-receipt"></i>
                </div>

            </div>

            <div class="summary-card pending">

                <div>
                    <div class="summary-label">
                        Pending Review
                    </div>

                    <div class="summary-value">
                        {{ number_format($pendingCount) }}
                    </div>
                </div>

                <div class="summary-dot">
                    <i class="fas fa-clock"></i>
                </div>

            </div>

            <div class="summary-card confirmed">

                <div>
                    <div class="summary-label">
                        Confirmed
                    </div>

                    <div class="summary-value">
                        {{ number_format($confirmedCount) }}
                    </div>
                </div>

                <div class="summary-dot">
                    <i class="fas fa-check"></i>
                </div>

            </div>

            <div class="summary-card amount">

                <div>
                    <div class="summary-label">
                        Total Value
                    </div>

                    <div class="summary-value">
                        KES {{ number_format($totalAmount, 0) }}
                    </div>
                </div>

                <div class="summary-dot">
                    <i class="fas fa-coins"></i>
                </div>

            </div>

        </div>

        {{-- Transactions --}}
        <div class="mpesa-card">

            <div class="mpesa-card-top">

                <div>
                    <strong>Transaction Stream</strong>
                </div>

                <span>
                    Latest transactions first
                </span>

            </div>

            @if($transactions->count())

                <div class="table-responsive">

                    <table class="table mpesa-table align-middle">

                        <thead>

                            <tr>
                                <th>Date</th>
                                <th>Transaction</th>
                                <th>Donor / Name</th>
                                <th>Phone</th>
                                <th>Amount</th>
                                <th>Reference</th>
                                <th>Classification</th>
                                <th>Reminder</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>
                            </tr>

                        </thead>

                        <tbody>

                            @foreach($transactions as $transaction)

                                @php
                                    $displayName = $transaction->donor
                                        ? $transaction->donor->name
                                        : trim(
                                            ($transaction->first_name ?? '') . ' ' .
                                            ($transaction->middle_name ?? '') . ' ' .
                                            ($transaction->last_name ?? '')
                                        );

                                    $displayName = $displayName ?: 'Unknown';

                                    $classification = $transaction->classification;

                                    $classificationConfig = match ($classification) {
                                        'donation' => [
                                            'class' => 'pill-donation',
                                            'label' => 'Donation',
                                        ],
                                        'payment' => [
                                            'class' => 'pill-payment',
                                            'label' => 'Payment',
                                        ],
                                        'refund' => [
                                            'class' => 'pill-refund',
                                            'label' => 'Refund',
                                        ],
                                        'other' => [
                                            'class' => 'pill-other',
                                            'label' => 'Other',
                                        ],
                                        default => [
                                            'class' => 'pill-unclassified',
                                            'label' => 'Unclassified',
                                        ],
                                    };

                                    $statusConfig = match ($transaction->status) {
                                        'confirmed' => [
                                            'class' => 'pill-confirmed',
                                            'label' => 'Confirmed',
                                        ],
                                        'rejected' => [
                                            'class' => 'pill-rejected',
                                            'label' => 'Rejected',
                                        ],
                                        default => [
                                            'class' => 'pill-pending',
                                            'label' => 'Pending Review',
                                        ],
                                    };
                                @endphp

                                <tr>

                                    {{-- Date --}}
                                    <td>
                                        @if($transaction->received_at)
                                            <div class="date-main">
                                                {{ $transaction->received_at->format('d/m/Y') }}
                                            </div>

                                            <div class="date-time">
                                                {{ $transaction->received_at->format('H:i') }}
                                            </div>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    {{-- Transaction --}}
                                    <td>
                                        <span class="transaction-id">
                                            {{ $transaction->transaction_id }}
                                        </span>
                                    </td>

                                    {{-- Donor --}}
                                    <td>
                                        <span class="donor-name">
                                            {{ $displayName }}
                                        </span>
                                    </td>

                                    {{-- Phone --}}
                                    <td>
                                        <span class="phone-number">
                                            {{ $transaction->phone_number ?: '—' }}
                                        </span>
                                    </td>

                                    {{-- Amount --}}
                                    <td>
                                        <span class="amount">
                                            KES {{ number_format((float) $transaction->amount, 2) }}
                                        </span>
                                    </td>

                                    {{-- Reference --}}
                                    <td>
                                        <span class="reference">
                                            {{ $transaction->bill_ref_number ?: '—' }}
                                        </span>
                                    </td>

                                    {{-- Classification --}}
                                    <td>
                                        <span class="mpesa-pill {{ $classificationConfig['class'] }}">
                                            <span class="mpesa-pill-dot"></span>
                                            {{ $classificationConfig['label'] }}
                                        </span>
                                    </td>

                                    <td>
    @if($transaction->status !== 'pending_review')
        <span class="text-muted" style="font-size:10px;">
            —
        </span>
    @elseif($transaction->review_reminder_2_sent_at)
        <span class="mpesa-reminder-pill reminder-2">
            <i class="fas fa-check-double"></i>
            2 sent
        </span>
    @elseif($transaction->review_reminder_1_sent_at)
        <span class="mpesa-reminder-pill reminder-1">
            <i class="fas fa-bell"></i>
            1 sent
        </span>
    @else
        <span class="mpesa-reminder-pill reminder-none">
            <i class="fas fa-minus"></i>
            None
        </span>
    @endif
</td>


                                    {{-- Status --}}
                                    <td>
                                        <span class="mpesa-pill {{ $statusConfig['class'] }}">
                                            <span class="mpesa-pill-dot"></span>
                                            {{ $statusConfig['label'] }}
                                        </span>
                                    </td>

                                    {{-- Action --}}
                                    <td class="text-end">

                                        <a href="{{ route(
                                            'admin.mpesa-transactions.show',
                                            $transaction
                                        ) }}"
                                           class="mpesa-view-btn">

                                            <i class="fas fa-arrow-right"></i>
                                            View

                                        </a>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="mpesa-empty">

                    <div class="mpesa-empty-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>

                    <h6>No M-Pesa transactions</h6>

                    <p>
                        Transactions received through the M-Pesa callback
                        will appear here for review.
                    </p>

                </div>

            @endif

        </div>

    </div>
</div>

@endsection