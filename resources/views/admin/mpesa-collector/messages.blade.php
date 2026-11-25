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
                <i class="fa fa-envelope"></i>
            </div>

            <div>
                <div class="requisition-breadcrumb">
                    <span>M-Pesa</span>
                    <span>/</span>
                    <span>Messages</span>
                </div>

                <h1 class="requisition-page-title">
                    Collected M-Pesa Messages
                </h1>

                <p class="requisition-page-subtitle">
                    View uploaded SMS messages and monitor their processing status.
                </p>
            </div>

        </div>

                <div class="d-flex flex-wrap" style="gap:10px;">

   

</div>


        <div class="requisition-header-right">
            <span class="requisition-list-status requisition-list-status-approved">
                <span class="requisition-list-status-dot"></span>
                Message Management
            </span>
             @can('RETRY MPESA COLLECTOR MESSAGES')
        <form method="POST"
              action="{{ route('admin.mpesa-collector.messages.retry-all') }}"
              onsubmit="return confirm('Retry all failed and unrecognized messages matching the current search and device filters?');">

            @csrf

            <input type="hidden" name="q" value="{{ request('q') }}">
            <input type="hidden" name="status" value="{{ request('status') }}">
            <input type="hidden" name="device_id" value="{{ request('device_id') }}">

            <button type="submit" class="requisition-save-button">
                <i class="fa fa-redo mr-1"></i>
                Retry All Failed / Unrecognized
            </button>

        </form>
    @endcan
    
        </div>

    </div>

    <x-message></x-message>


    {{-- MESSAGE SUMMARY --}}
    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">
                <div class="requisition-section-icon">
                    <i class="fa fa-chart-pie"></i>
                </div>

                <div>
                    <h5>Message Processing Overview</h5>
                    <p>Summary of uploaded messages and processing results.</p>
                </div>
            </div>

        </div>



        <div class="requisition-details-body">

            <div class="row">

                @php
                    $summaryCards = [
                        ['label' => 'All Messages', 'value' => $counts['all'] ?? 0, 'icon' => 'fa-envelope'],
                        ['label' => 'Pending', 'value' => $counts['pending'] ?? 0, 'icon' => 'fa-clock'],
                        ['label' => 'Processed', 'value' => $counts['processed'] ?? 0, 'icon' => 'fa-check-circle'],
                        ['label' => 'Unrecognized', 'value' => $counts['unrecognized'] ?? 0, 'icon' => 'fa-question-circle'],
                        ['label' => 'Failed', 'value' => $counts['failed'] ?? 0, 'icon' => 'fa-exclamation-circle'],
                    ];
                @endphp

                @foreach($summaryCards as $card)

                    <div class="col-xl col-md-4 col-6 mb-3">

                        <div class="requisition-section h-100 mb-0">

                            <div class="requisition-section-heading">
                                <div class="requisition-section-icon">
                                    <i class="fa {{ $card['icon'] }}"></i>
                                </div>
                            </div>

                            <div class="mt-3">
                                <div class="requisition-list-meta">
                                    {{ $card['label'] }}
                                </div>

                                <div class="requisition-page-title" style="font-size:26px;">
                                    {{ number_format($card['value']) }}
                                </div>
                            </div>

                        </div>

                    </div>

                @endforeach

            </div>

        </div>

    </div>


    {{-- SEARCH AND FILTERS --}}
    <div class="requisition-section donation-filters-section">

        <details class="donation-filters-details"
            {{ request()->hasAny(['q', 'status', 'device_id']) ? 'open' : '' }}>

            <summary class="requisition-section-header donation-filters-summary">

                <div class="requisition-section-heading">

                    <div class="requisition-section-icon">
                        <i class="fa fa-filter"></i>
                    </div>

                    <div>
                        <h5>Search & Filter Messages</h5>
                        <p>Find messages by SMS content, receipt, device or status.</p>
                    </div>

                </div>

                <i class="fa fa-chevron-down donation-filters-chevron"></i>

            </summary>

            <div class="requisition-details-body donation-filters-body">

                <div class="requisition-section">

                    <div class="requisition-details-body">

                        <form method="GET"
                            action="{{ route('admin.mpesa-collector.messages.index') }}">

                            <div class="row mb-3">

                                <div class="col-md-5 mb-3">

                                    <label class="requisition-field-label">
                                        Search
                                    </label>

                                    <input type="text"
                                        name="q"
                                        class="requisition-input"
                                        value="{{ request('q') }}"
                                        placeholder="SMS content, sender or receipt number">

                                </div>

                                <div class="col-md-3 mb-3">

                                    <label class="requisition-field-label">
                                        Processing Status
                                    </label>

                                    <select name="status" class="requisition-input">

                                        <option value="">All Statuses</option>

                                        @foreach(['pending', 'processed', 'unrecognized', 'failed'] as $status)

                                            <option value="{{ $status }}"
                                                @selected(request('status') === $status)>
                                                {{ ucfirst($status) }}
                                            </option>

                                        @endforeach

                                    </select>

                                </div>

                                <div class="col-md-4 mb-3">

                                    <label class="requisition-field-label">
                                        Collector Device
                                    </label>

                                    <select name="device_id" class="requisition-input">

                                        <option value="">All Devices</option>

                                        @foreach($devices as $device)

                                            <option value="{{ $device->id }}"
                                                @selected((string) request('device_id') === (string) $device->id)>
                                                {{ $device->device_name }} ({{ $device->device_id }})
                                            </option>

                                        @endforeach

                                    </select>

                                </div>

                            </div>

                            <div class="d-flex flex-wrap justify-content-end"
                                style="gap:10px;">

                                <button type="submit" class="requisition-save-button">
                                    <i class="fa fa-search"></i>
                                    Apply Filters
                                </button>

                                <a href="{{ route('admin.mpesa-collector.messages.index') }}"
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


    {{-- UPLOADED MESSAGES --}}
    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fa fa-comments"></i>
                </div>

                <div>
                    <h5>Uploaded SMS Messages</h5>

                    <p>
                        {{ number_format($messages->total()) }}
                        {{ $messages->total() === 1 ? 'message' : 'messages' }}
                        <span class="mx-1">·</span>
                        Generated {{ now()->format('d M Y, H:i') }}
                    </p>
                </div>

            </div>

        </div>


        <div class="requisition-list-table-wrapper">

            <table class="requisition-list-table">

                <thead>
                    <tr>
                        <th style="min-width:110px;">Sender</th>
                        <th style="min-width:240px;">Message Preview</th>
                        <th style="min-width:160px;">Device</th>
                        <th style="min-width:160px;">Transaction</th>
                        <th style="min-width:120px;">Status</th>
                        <th style="min-width:130px;">Received</th>
                        <th style="width:90px;">Action</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($messages as $message)

                        @php

                            $statusClasses = [
                                'pending' => 'requisition-list-status-pending',
                                'processed' => 'requisition-list-status-approved',
                                'unrecognized' => 'requisition-list-status-rejected',
                                'failed' => 'requisition-list-status-rejected',
                            ];

                        @endphp

                        <tr>

                            {{-- SENDER --}}
                            <td>
                                <span class="requisition-list-number">
                                    {{ $message->sender }}
                                </span>
                            </td>


                            {{-- MESSAGE PREVIEW --}}
                            <td style="max-width:340px;">

                                <div style="white-space:normal;overflow-wrap:anywhere;">
                                    {{ \Illuminate\Support\Str::limit(
                                        preg_replace('/\s+/', ' ', $message->raw_message),
                                        105
                                    ) }}
                                </div>

                            </td>


                            {{-- DEVICE --}}
                            <td>

                                @if($message->device)

                                    <div class="requisition-list-number">
                                        {{ $message->device->device_name }}
                                    </div>

                                    <div class="requisition-list-meta">
                                        {{ $message->device->device_id }}
                                    </div>

                                @else

                                    <span class="requisition-list-meta">
                                        Unknown device
                                    </span>

                                @endif

                            </td>


                            {{-- TRANSACTION --}}
                            <td>

                                @if($message->transaction)

                                    <div class="requisition-list-number">
                                        {{ $message->transaction->receipt_number ?: 'No Receipt' }}
                                    </div>

                                    <div class="requisition-list-meta">
                                        {{ str_replace('_', ' ', $message->transaction->transaction_type) }}
                                    </div>

                                    <div class="requisition-list-meta">
                                        {{ $message->transaction->currency ?? 'KES' }}
                                        {{ number_format((float) $message->transaction->amount, 2) }}
                                    </div>

                                @else

                                    <span class="requisition-list-meta">
                                        Not parsed
                                    </span>

                                @endif

                            </td>


                            {{-- STATUS --}}
                            <td>

                                <span class="requisition-list-status {{ $statusClasses[$message->processing_status] ?? 'requisition-list-status-draft' }}">

                                    <span class="requisition-list-status-dot"></span>

                                    {{ ucfirst($message->processing_status) }}

                                </span>

                            </td>


                            {{-- RECEIVED --}}
                            <td>

                                <span class="requisition-list-date">
                                    {{ $message->message_received_at
                                        ? \Illuminate\Support\Carbon::parse($message->message_received_at)->format('d M Y')
                                        : '—' }}
                                </span>

                                <div class="requisition-list-meta">
                                    {{ $message->message_received_at
                                        ? \Illuminate\Support\Carbon::parse($message->message_received_at)->format('h:i A')
                                        : '' }}
                                </div>

                            </td>


                            {{-- VIEW DETAILS --}}
{{-- MESSAGE ACTIONS --}}
<td>

    <div class="requisition-list-actions">

        {{-- VIEW MESSAGE --}}
        <button type="button"
            class="requisition-list-action primary"
            title="View complete message"
            data-toggle="modal"
            data-target="#messageModal{{ $message->id }}">

            <i class="fa fa-eye"></i>

        </button>

        {{-- INDIVIDUAL RETRY --}}
        @if(in_array($message->processing_status, ['failed', 'unrecognized']))
            @can('RETRY MPESA COLLECTOR MESSAGES')

                <form method="POST"
                      action="{{ route('admin.mpesa-collector.messages.retry', $message->id) }}"
                      onsubmit="return confirm('Retry processing this message?');">

                    @csrf
                    @method('PATCH')

                    <button type="submit"
                        class="requisition-list-action"
                        title="Retry message">

                        <i class="fa fa-redo"></i>

                    </button>

                </form>

            @endcan
        @endif

    </div>

</td>


                        </tr>


                        {{-- MESSAGE DETAILS POPUP --}}
                        <div class="modal fade"
                            id="messageModal{{ $message->id }}"
                            tabindex="-1"
                            role="dialog"
                            aria-hidden="true">

                            <div class="modal-dialog modal-lg modal-dialog-scrollable"
                                role="document">

                                <div class="modal-content">

                                    <div class="modal-header">

                                        <h5 class="modal-title">
                                            <i class="fa fa-envelope mr-2"></i>
                                            SMS Message Details
                                        </h5>

                                        <button type="button"
                                            class="close"
                                            data-dismiss="modal"
                                            aria-label="Close">

                                            <span aria-hidden="true">&times;</span>

                                        </button>

                                    </div>


                                    <div class="modal-body">

                                        <div class="row mb-3">

                                            <div class="col-md-6 mb-3">
                                                <label class="requisition-field-label">Sender</label>
                                                <div class="requisition-list-number">
                                                    {{ $message->sender }}
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="requisition-field-label">Device</label>
                                                <div class="requisition-list-number">
                                                    {{ $message->device->device_name ?? 'Unknown device' }}
                                                </div>
                                                <div class="requisition-list-meta">
                                                    {{ $message->device->device_id ?? '—' }}
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="requisition-field-label">Received At</label>
                                                <div>
                                                    {{ $message->message_received_at
                                                        ? \Illuminate\Support\Carbon::parse($message->message_received_at)->format('d M Y, h:i A')
                                                        : '—' }}
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="requisition-field-label">Uploaded At</label>
                                                <div>
                                                    {{ $message->uploaded_at
                                                        ? \Illuminate\Support\Carbon::parse($message->uploaded_at)->format('d M Y, h:i A')
                                                        : ($message->created_at ? \Illuminate\Support\Carbon::parse($message->created_at)->format('d M Y, h:i A') : '—') }}
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="requisition-field-label">Processing Status</label>
                                                <div>
                                                    <span class="requisition-list-status {{ $statusClasses[$message->processing_status] ?? 'requisition-list-status-draft' }}">
                                                        <span class="requisition-list-status-dot"></span>
                                                        {{ ucfirst($message->processing_status) }}
                                                    </span>
                                                </div>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label class="requisition-field-label">Processed At</label>
                                                <div>
                                                    {{ $message->processed_at
                                                        ? \Illuminate\Support\Carbon::parse($message->processed_at)->format('d M Y, h:i A')
                                                        : 'Not processed' }}
                                                </div>
                                            </div>

                                        </div>


                                        <div class="mb-4">

                                            <label class="requisition-field-label">
                                                Complete Original SMS
                                            </label>

                                            <div class="requisition-section">
                                                <div class="requisition-details-body"
                                                    style="white-space:pre-wrap;overflow-wrap:anywhere;">
                                                    {{ $message->raw_message }}
                                                </div>
                                            </div>

                                        </div>


                                        @if($message->transaction)

                                            <div class="mb-4">

                                                <label class="requisition-field-label">
                                                    Parsed Transaction Details
                                                </label>

                                                <div class="requisition-section">

                                                    <div class="requisition-details-body">

                                                        <div class="row">

                                                            <div class="col-md-6 mb-3">
                                                                <label class="requisition-field-label">Receipt Number</label>
                                                                <div>{{ $message->transaction->receipt_number ?: '—' }}</div>
                                                            </div>

                                                            <div class="col-md-6 mb-3">
                                                                <label class="requisition-field-label">Transaction Type</label>
                                                                <div>{{ str_replace('_', ' ', $message->transaction->transaction_type) }}</div>
                                                            </div>

                                                            <div class="col-md-6 mb-3">
                                                                <label class="requisition-field-label">Direction</label>
                                                                <div>{{ $message->transaction->direction ?: '—' }}</div>
                                                            </div>

                                                            <div class="col-md-6 mb-3">
                                                                <label class="requisition-field-label">Payment Channel</label>
                                                                <div>{{ $message->transaction->payment_channel ?: '—' }}</div>
                                                            </div>

                                                            <div class="col-md-6 mb-3">
                                                                <label class="requisition-field-label">Amount</label>
                                                                <div>
                                                                    {{ $message->transaction->currency ?? 'KES' }}
                                                                    {{ number_format((float) $message->transaction->amount, 2) }}
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6 mb-3">
                                                                <label class="requisition-field-label">Transaction Cost</label>
                                                                <div>
                                                                    {{ number_format((float) $message->transaction->transaction_cost, 2) }}
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6 mb-3">
                                                                <label class="requisition-field-label">M-Pesa Balance</label>
                                                                <div>
                                                                    {{ $message->transaction->balance !== null
                                                                        ? number_format((float) $message->transaction->balance, 2)
                                                                        : '—' }}
                                                                </div>
                                                            </div>

                                                            <div class="col-md-6 mb-3">
                                                                <label class="requisition-field-label">Counterparty</label>
                                                                <div>{{ $message->transaction->counterparty_name ?: '—' }}</div>
                                                            </div>

                                                            <div class="col-md-6 mb-3">
                                                                <label class="requisition-field-label">Counterparty Phone</label>
                                                                <div>{{ $message->transaction->counterparty_phone ?: '—' }}</div>
                                                            </div>

                                                            <div class="col-md-6 mb-3">
                                                                <label class="requisition-field-label">Transaction Date</label>
                                                                <div>
                                                                    {{ $message->transaction->transaction_at
                                                                        ? \Illuminate\Support\Carbon::parse($message->transaction->transaction_at)->format('d M Y, h:i A')
                                                                        : '—' }}
                                                                </div>
                                                            </div>

                                                        </div>

                                                        @if($message->transaction->parsed_data)

                                                            <hr>

                                                            <label class="requisition-field-label">
                                                                Additional Parsed Data
                                                            </label>

                                                            <pre style="white-space:pre-wrap;overflow-wrap:anywhere;margin-bottom:0;">{{ json_encode($message->transaction->parsed_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>

                                                        @endif

                                                    </div>

                                                </div>

                                            </div>

                                        @endif


                                        @if($message->processing_error)

                                            <div class="alert alert-danger">

                                                <strong>
                                                    <i class="fa fa-exclamation-triangle mr-1"></i>
                                                    Processing Error
                                                </strong>

                                                <p class="mb-0 mt-2">
                                                    {{ $message->processing_error }}
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
                                        @if(in_array($message->processing_status, ['failed', 'unrecognized']))
    @can('RETRY MPESA COLLECTOR MESSAGES')
        <form method="POST"
              action="{{ route('admin.mpesa-collector.messages.retry', $message->id) }}"
              onsubmit="return confirm('Retry processing this M-Pesa message?');">
            @csrf
            @method('PATCH')

            <button type="submit" class="requisition-save-button">
                <i class="fa fa-redo mr-1"></i> Retry Processing
            </button>
        </form>
    @endcan
@endif


                                    </div>

                                </div>

                            </div>

                        </div>

                    @empty

                        <tr>

                            <td colspan="7">

                                <div class="requisition-table-empty">

                                    <div class="requisition-empty-icon">
                                        <i class="fa fa-envelope-open"></i>
                                    </div>

                                    <h5>No M-Pesa messages found</h5>

                                    <p>
                                        Messages uploaded by approved collector devices
                                        will appear here.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- PAGINATION --}}
        @if($messages->total() > 0)

            <div class="stores-pagination-section">

                <div class="stores-pagination-info">

                    <div class="stores-pagination-info-icon">
                        <i class="fa fa-layer-group"></i>
                    </div>

                    <div class="stores-pagination-info-text">

                        <span class="stores-pagination-info-label">
                            Showing messages
                        </span>

                        <strong>
                            {{ $messages->firstItem() ?? 0 }}
                            –
                            {{ $messages->lastItem() ?? 0 }}
                        </strong>

                        <span class="stores-pagination-info-total">
                            of {{ number_format($messages->total()) }} total
                        </span>

                    </div>

                </div>

                @if($messages->hasPages())

                    <div class="stores-pagination-controls">
                        {{ $messages->onEachSide(2)->withQueryString()->links('pagination::bootstrap-5') }}
                    </div>

                @endif

            </div>

        @endif

    </div>

</div>

@endsection
