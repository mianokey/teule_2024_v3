@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/stores.css') }}">
@endpush

@section('content')

<div class="store-requisition-page">

    {{-- PAGE HEADER --}}
    <div class="requisition-page-header">

        <div class="requisition-header-content">

            <div class="requisition-header-icon">
                <i class="fa fa-exchange-alt"></i>
            </div>

            <div>

                <div class="requisition-breadcrumb">
                    <span>M-Pesa</span>
                    <span>/</span>
                    <span>Transactions</span>
                </div>

                <h1 class="requisition-page-title">
                    M-Pesa Transactions
                </h1>

                <p class="requisition-page-subtitle">
                    View, search and monitor transactions extracted from collected M-Pesa messages.
                </p>

            </div>

        </div>

        <div class="requisition-header-right">

            <span class="requisition-list-status requisition-list-status-approved">
                <span class="requisition-list-status-dot"></span>
                Transaction Management
            </span>

        </div>

    </div>


    {{-- MESSAGES --}}
    <x-message></x-message>


    {{-- TRANSACTION SUMMARY --}}
    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fa fa-chart-pie"></i>
                </div>

                <div>
                    <h5>Transaction Overview</h5>

                    <p>
                        Summary of collected and processed M-Pesa transactions.
                    </p>
                </div>

            </div>

        </div>

        <div class="requisition-details-body">

            <div class="row">

                @php
                    $summaryCards = [
                        ['All Transactions', 'all', 'fa-list'],
                        ['Incoming', 'incoming', 'fa-arrow-down'],
                        ['Outgoing', 'outgoing', 'fa-arrow-up'],
                        ['Processed', 'processed', 'fa-check-circle'],
                        ['Failed', 'failed', 'fa-exclamation-circle'],
                    ];
                @endphp

                @foreach($summaryCards as $card)

                    <div class="col-xl-2 col-md-4 col-6 mb-3">

                        <div class="requisition-section h-100 mb-0">

                            <div class="requisition-section-heading">

                                <div class="requisition-section-icon">
                                    <i class="fa {{ $card[2] }}"></i>
                                </div>

                            </div>

                            <div class="mt-3">

                                <div class="requisition-list-meta">
                                    {{ $card[0] }}
                                </div>

                                <div class="requisition-page-title" style="font-size:26px;">
                                    {{ number_format($counts[$card[1]] ?? 0) }}
                                </div>

                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

        </div>

    </div>


    {{-- TRANSACTION FILTERS --}}
    <div class="requisition-section donation-filters-section">

        <details class="donation-filters-details"
            {{ request()->hasAny(['q', 'type', 'direction', 'status', 'device_id', 'date_from', 'date_to']) ? 'open' : '' }}>

            <summary class="requisition-section-header donation-filters-summary">

                <div class="requisition-section-heading">

                    <div class="requisition-section-icon">
                        <i class="fa fa-filter"></i>
                    </div>

                    <div>
                        <h5>Search & Filter Transactions</h5>

                        <p>
                            Find transactions using receipts, names, dates, devices and transaction types.
                        </p>
                    </div>

                </div>

                <i class="fa fa-chevron-down donation-filters-chevron"></i>

            </summary>

            <div class="requisition-details-body donation-filters-body">

                <div class="requisition-section">

                    <div class="requisition-details-body">

                        <form method="GET"
                            action="{{ route('admin.mpesa-collector.transactions.index') }}">

                            <div class="row mb-3">

                                <div class="col-md-4 mb-3">

                                    <label class="requisition-field-label">
                                        Search Transaction
                                    </label>

                                    <input type="text"
                                        name="q"
                                        class="requisition-input"
                                        value="{{ request('q') }}"
                                        placeholder="Receipt, name, phone, account...">

                                </div>


                                <div class="col-md-4 mb-3">

                                    <label class="requisition-field-label">
                                        Transaction Type
                                    </label>

                                    <select name="type" class="requisition-input">

                                        <option value="">All Transaction Types</option>

                                        @foreach($transactionTypes as $type)

                                            <option value="{{ $type }}"
                                                @selected(request('type') === $type)>

                                                {{ ucwords(str_replace('_', ' ', $type)) }}

                                            </option>

                                        @endforeach

                                    </select>

                                </div>


                                <div class="col-md-4 mb-3">

                                    <label class="requisition-field-label">
                                        Transaction Direction
                                    </label>

                                    <select name="direction" class="requisition-input">

                                        <option value="">All Directions</option>

                                        <option value="INCOMING"
                                            @selected(request('direction') === 'INCOMING')>
                                            Incoming
                                        </option>

                                        <option value="OUTGOING"
                                            @selected(request('direction') === 'OUTGOING')>
                                            Outgoing
                                        </option>

                                    </select>

                                </div>


                                <div class="col-md-3 mb-3">

                                    <label class="requisition-field-label">
                                        Processing Status
                                    </label>

                                    <select name="status" class="requisition-input">

                                        <option value="">All Statuses</option>

                                        <option value="processed"
                                            @selected(request('status') === 'processed')>
                                            Processed
                                        </option>

                                        <option value="failed"
                                            @selected(request('status') === 'failed')>
                                            Failed
                                        </option>

                                        <option value="pending"
                                            @selected(request('status') === 'pending')>
                                            Pending
                                        </option>

                                    </select>

                                </div>


                                <div class="col-md-3 mb-3">

                                    <label class="requisition-field-label">
                                        Collector Device
                                    </label>

                                    <select name="device_id" class="requisition-input">

                                        <option value="">All Devices</option>

                                        @foreach($devices as $device)

                                            <option value="{{ $device->id }}"
                                                @selected(request('device_id') == $device->id)>

                                                {{ $device->device_name }}
                                                ({{ $device->device_id }})

                                            </option>

                                        @endforeach

                                    </select>

                                </div>


                                <div class="col-md-3 mb-3">

                                    <label class="requisition-field-label">
                                        Date From
                                    </label>

                                    <input type="date"
                                        name="date_from"
                                        class="requisition-input"
                                        value="{{ request('date_from') }}">

                                </div>


                                <div class="col-md-3 mb-3">

                                    <label class="requisition-field-label">
                                        Date To
                                    </label>

                                    <input type="date"
                                        name="date_to"
                                        class="requisition-input"
                                        value="{{ request('date_to') }}">

                                </div>

                            </div>


                            <div class="d-flex flex-wrap justify-content-end"
                                style="gap:10px;">

                                <button type="submit" class="requisition-save-button">

                                    <i class="fa fa-search"></i>
                                    Apply Filters

                                </button>

                                <a href="{{ route('admin.mpesa-collector.transactions.index') }}"
                                    class="requisition-cancel-button"
                                    style="display:inline-flex;align-items:center;justify-content:center;gap:6px;">

                                    <i class="fa fa-times"></i>
                                    Clear Filters

                                </a>

                            </div>

                        </form>

                    </div>

                </div>

            </div>

        </details>

    </div>


    {{-- TRANSACTION RECORDS --}}
    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fa fa-exchange-alt"></i>
                </div>

                <div>

                    <h5>Recorded M-Pesa Transactions</h5>

                    <p>

                        {{ number_format($transactions->total()) }}

                        {{ $transactions->total() === 1 ? 'transaction' : 'transactions' }}

                        <span class="mx-1">·</span>

                        Generated {{ now()->format('d M Y, H:i') }}

                    </p>

                </div>

            </div>

        </div>


        {{-- TABLE --}}
        <div class="requisition-list-table-wrapper">

            <table class="requisition-list-table">

                <thead>

                    <tr>

                        <th style="min-width:130px;">Transaction Date</th>

                        <th style="min-width:130px;">Receipt Number</th>

                        <th style="min-width:130px;">Transaction Type</th>

                        <th style="min-width:180px;">Counterparty</th>

                        <th style="min-width:110px;">Direction</th>

                        <th style="min-width:130px;">Amount</th>

                        <th style="min-width:150px;">Device</th>

                        <th style="min-width:130px;">Status</th>
                        <th style="width:90px;">Details</th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($transactions as $transaction)

                        @php

                            $statusClasses = [

                                'processed' => 'requisition-list-status-approved',

                                'failed' => 'requisition-list-status-rejected',

                                'pending' => 'requisition-list-status-pending',

                            ];

                            $directionLabels = [

                                'INCOMING' => 'Incoming',

                                'OUTGOING' => 'Outgoing',

                            ];

                        @endphp

                        <tr>

                            {{-- DATE --}}
                            <td>

                                @if($transaction->transaction_at)

                                    <span class="requisition-list-date">
                                        {{ \Illuminate\Support\Carbon::parse($transaction->transaction_at)->format('d M Y') }}
                                    </span>

                                    <div class="requisition-list-meta">
                                        {{ \Illuminate\Support\Carbon::parse($transaction->transaction_at)->format('h:i A') }}
                                    </div>

                                @else

                                    <span class="requisition-list-meta">—</span>

                                @endif

                            </td>


                            {{-- RECEIPT --}}
                            <td>

                                <div class="requisition-list-number">
                                    {{ $transaction->receipt_number ?: '—' }}
                                </div>

                            </td>


                            {{-- TYPE --}}
                            <td>

                                <span class="requisition-list-department">
                                    {{ ucwords(str_replace('_', ' ', $transaction->transaction_type ?? 'Unknown')) }}
                                </span>

                            </td>


                            {{-- COUNTERPARTY --}}
                            <td>

                                <div class="requisition-list-number">
                                    {{ $transaction->counterparty_name ?: '—' }}
                                </div>

                                @if($transaction->counterparty_phone)

                                    <div class="requisition-list-meta">
                                        {{ $transaction->counterparty_phone }}
                                    </div>

                                @endif

                                @if($transaction->account_reference)

                                    <div class="requisition-list-meta">
                                        Account: {{ $transaction->account_reference }}
                                    </div>

                                @endif

                            </td>


                            {{-- DIRECTION --}}
                            <td>

                                <span class="requisition-list-department">
                                    {{ $directionLabels[$transaction->direction] ?? $transaction->direction ?? '—' }}
                                </span>

                            </td>


                            {{-- AMOUNT --}}
                            <td>

                                <div class="requisition-list-number">
                                    Ksh {{ number_format((float) $transaction->amount, 2) }}
                                </div>

                            </td>


                            {{-- DEVICE --}}
                            <td>

                                <div class="requisition-list-number">
                                    {{ $transaction->message?->device?->device_name ?? '—' }}
                                </div>

                                <div class="requisition-list-meta">
                                    {{ $transaction->message?->device?->device_id ?? '' }}
                                </div>

                            </td>


                            {{-- STATUS --}}
                            <td>

                                <span class="requisition-list-status {{ $statusClasses[$transaction->processing_status] ?? 'requisition-list-status-pending' }}">

                                    <span class="requisition-list-status-dot"></span>

                                    {{ ucfirst($transaction->processing_status ?? 'Unknown') }}

                                </span>

                            </td>
                            <td>
    <button type="button"
        class="requisition-list-action primary"
        data-toggle="modal"
        data-target="#transactionModal{{ $transaction->id }}"
        title="View transaction details">

        <i class="fa fa-eye"></i>

    </button>
</td>


                        </tr>

                    @empty

                        <tr>

                            <td colspan="8">

                                <div class="requisition-table-empty">

                                    <div class="requisition-empty-icon">
                                        <i class="fa fa-exchange-alt"></i>
                                    </div>

                                    <h5>No transactions found</h5>

                                    <p>
                                        Transactions extracted from collected M-Pesa messages will appear here.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

            {{-- TRANSACTION DETAILS POPUPS --}}
@foreach($transactions as $transaction)

    <div class="modal fade"
        id="transactionModal{{ $transaction->id }}"
        tabindex="-1"
        role="dialog"
        aria-hidden="true">

        <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">
                        <i class="fa fa-receipt mr-2"></i>
                        Transaction Details
                    </h5>

                    <button type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close">

                        <span aria-hidden="true">&times;</span>

                    </button>

                </div>

                <div class="modal-body">

                    <div class="requisition-section">

                        <div class="requisition-section-header">
                            <div class="requisition-section-heading">
                                <div class="requisition-section-icon">
                                    <i class="fa fa-info-circle"></i>
                                </div>
                                <div>
                                    <h5>Transaction Information</h5>
                                </div>
                            </div>
                        </div>

                        <div class="requisition-details-body">

                            <div class="row">

                                <div class="col-md-6 mb-3">
                                    <label class="requisition-field-label">Receipt Number</label>
                                    <div class="requisition-list-number">
                                        {{ $transaction->receipt_number ?? '—' }}
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="requisition-field-label">Transaction Type</label>
                                    <div class="requisition-list-number">
                                        {{ $transaction->transaction_type ?? '—' }}
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="requisition-field-label">Amount</label>
                                    <div class="requisition-list-number">
                                        Ksh {{ number_format((float)$transaction->amount, 2) }}
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="requisition-field-label">Transaction Cost</label>
                                    <div class="requisition-list-number">
                                        Ksh {{ number_format((float)$transaction->transaction_cost, 2) }}
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="requisition-field-label">M-Pesa Balance</label>
                                    <div class="requisition-list-number">
                                        {{ $transaction->balance !== null ? 'Ksh '.number_format((float)$transaction->balance, 2) : '—' }}
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="requisition-field-label">Daily Transaction Limit Remaining</label>
                                    <div class="requisition-list-number">
                                        {{ $transaction->daily_limit_remaining !== null ? 'Ksh '.number_format((float)$transaction->daily_limit_remaining, 2) : '—' }}
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="requisition-field-label">Counterparty</label>
                                    <div class="requisition-list-number">
                                        {{ $transaction->counterparty_name ?? '—' }}
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="requisition-field-label">Phone Number</label>
                                    <div class="requisition-list-number">
                                        {{ $transaction->counterparty_phone ?? '—' }}
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="requisition-field-label">Account Reference</label>
                                    <div class="requisition-list-number">
                                        {{ $transaction->account_reference ?? '—' }}
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="requisition-field-label">Shortcode</label>
                                    <div class="requisition-list-number">
                                        {{ $transaction->shortcode ?? '—' }}
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="requisition-field-label">Direction</label>
                                    <div class="requisition-list-number">
                                        {{ $transaction->direction ?? '—' }}
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="requisition-field-label">Transaction Date</label>
                                    <div class="requisition-list-number">
                                        {{ $transaction->transaction_at ? \Illuminate\Support\Carbon::parse($transaction->transaction_at)->format('d M Y, h:i A') : '—' }}
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="requisition-field-label">Collector Device</label>
                                    <div class="requisition-list-number">
                                        {{ $transaction->message?->device?->device_name ?? '—' }}
                                    </div>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="requisition-field-label">Processing Status</label>
                                    <div class="requisition-list-number">
                                        {{ ucfirst($transaction->processing_status ?? 'Unknown') }}
                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- ORIGINAL SMS --}}
                    <div class="requisition-section mt-3">

                        <div class="requisition-section-header">
                            <div class="requisition-section-heading">
                                <div class="requisition-section-icon">
                                    <i class="fa fa-comment-alt"></i>
                                </div>
                                <div>
                                    <h5>Original M-Pesa Message</h5>
                                    <p>Unmodified SMS received by the collector device.</p>
                                </div>
                            </div>
                        </div>

                        <div class="requisition-details-body">

                            <div class="p-3 bg-light rounded"
                                style="white-space:pre-wrap;word-break:break-word;">

                                {{ $transaction->message?->raw_message ?? 'Original message unavailable.' }}

                            </div>

                        </div>

                    </div>


                    {{-- PARSED DATA --}}
                    @if($transaction->parsed_data)

                        <div class="requisition-section mt-3">

                            <div class="requisition-section-header">
                                <div class="requisition-section-heading">
                                    <div class="requisition-section-icon">
                                        <i class="fa fa-code"></i>
                                    </div>
                                    <div>
                                        <h5>Parsed Transaction Data</h5>
                                    </div>
                                </div>
                            </div>

                            <div class="requisition-details-body">

                                <pre class="p-3 bg-light rounded"
                                    style="white-space:pre-wrap;word-break:break-word;">{{ json_encode($transaction->parsed_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>

                            </div>

                        </div>

                    @endif


                    {{-- PROCESSING ERROR --}}
                    @if($transaction->processing_error)

                        <div class="alert alert-danger mt-3">

                            <strong>Processing Error</strong>

                            <p class="mb-0">
                                {{ $transaction->processing_error }}
                            </p>

                        </div>

                    @endif

                </div>

                <div class="modal-footer">

                    <button type="button"
                        class="requisition-cancel-button"
                        data-dismiss="modal">

                        Close

                    </button>

                </div>

            </div>

        </div>

    </div>

@endforeach



        </div>


        {{-- SCROLL INDICATOR --}}
        @if($transactions->count() > 8)

            <div class="requisition-scroll-hint">

                <i class="fa fa-arrows-alt-v"></i>

                Scroll to view more transactions

            </div>

        @endif


        {{-- PAGINATION --}}
        @if($transactions->total() > 0)

            <div class="stores-pagination-section">

                <div class="stores-pagination-info">

                    <div class="stores-pagination-info-icon">
                        <i class="fa fa-layer-group"></i>
                    </div>

                    <div class="stores-pagination-info-text">

                        <span class="stores-pagination-info-label">
                            Showing transactions
                        </span>

                        <strong>
                            {{ $transactions->firstItem() ?? 0 }}
                            –
                            {{ $transactions->lastItem() ?? 0 }}
                        </strong>

                        <span class="stores-pagination-info-total">
                            of {{ number_format($transactions->total()) }} total
                        </span>

                    </div>

                </div>

                @if($transactions->hasPages())

                    <div class="stores-pagination-controls">

                        {{ $transactions->onEachSide(2)->withQueryString()->links('pagination::bootstrap-5') }}

                    </div>

                @endif

            </div>

        @endif

    </div>

</div>

@endsection
