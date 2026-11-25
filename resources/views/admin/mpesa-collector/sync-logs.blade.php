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
                <i class="fa fa-sync-alt"></i>
            </div>

            <div>
                <div class="requisition-breadcrumb">
                    M-Pesa Collector / Sync History
                </div>

                <h2 class="requisition-page-title">Sync History</h2>

                <p class="requisition-page-subtitle">
                    Monitor device synchronization activity, uploaded messages and processing results.
                </p>
            </div>
        </div>

        <div class="requisition-header-right">
            <a href="{{ route('admin.mpesa-collector.devices.index') }}"
               class="requisition-cancel-button">
                <i class="fa fa-mobile-alt mr-1"></i> Devices
            </a>
        </div>
    </div>

    <x-message></x-message>

    {{-- SUMMARY CARDS --}}
    <div class="requisition-section mb-4">
        <div class="requisition-section-header">
            <div>
                <h5 class="requisition-section-heading">
                    <span class="requisition-section-icon">
                        <i class="fa fa-chart-bar"></i>
                    </span>
                    Synchronization Overview
                </h5>

                <p class="requisition-list-meta mb-0">
                    Overall synchronization statistics.
                </p>
            </div>
        </div>

        <div class="requisition-details-body">
            <div class="row">

                <div class="col-md-4 col-lg-2 mb-3">
                    <div class="requisition-section h-100 mb-0">
                        <div class="requisition-section-header">
                            <i class="fa fa-history text-primary"></i>
                            <span class="requisition-list-meta">Total Syncs</span>
                        </div>
                        <div class="requisition-page-title">
                            {{ number_format($counts['all']) }}
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-lg-2 mb-3">
                    <div class="requisition-section h-100 mb-0">
                        <div class="requisition-section-header">
                            <i class="fa fa-check-circle text-success"></i>
                            <span class="requisition-list-meta">Completed</span>
                        </div>
                        <div class="requisition-page-title">
                            {{ number_format($counts['completed']) }}
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-lg-2 mb-3">
                    <div class="requisition-section h-100 mb-0">
                        <div class="requisition-section-header">
                            <i class="fa fa-exclamation-circle text-warning"></i>
                            <span class="requisition-list-meta">Partial</span>
                        </div>
                        <div class="requisition-page-title">
                            {{ number_format($counts['partial']) }}
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-lg-2 mb-3">
                    <div class="requisition-section h-100 mb-0">
                        <div class="requisition-section-header">
                            <i class="fa fa-times-circle text-danger"></i>
                            <span class="requisition-list-meta">Failed</span>
                        </div>
                        <div class="requisition-page-title">
                            {{ number_format($counts['failed']) }}
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-lg-2 mb-3">
                    <div class="requisition-section h-100 mb-0">
                        <div class="requisition-section-header">
                            <i class="fa fa-envelope text-info"></i>
                            <span class="requisition-list-meta">Messages Received</span>
                        </div>
                        <div class="requisition-page-title">
                            {{ number_format($counts['messages_received']) }}
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-lg-2 mb-3">
                    <div class="requisition-section h-100 mb-0">
                        <div class="requisition-section-header">
                            <i class="fa fa-database text-success"></i>
                            <span class="requisition-list-meta">Messages Saved</span>
                        </div>
                        <div class="requisition-page-title">
                            {{ number_format($counts['messages_saved']) }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- FILTERS --}}
    <div class="requisition-section donation-filters-section mb-4">
        <details class="donation-filters-details"
                 {{ request()->hasAny(['q', 'status', 'device_id', 'date_from', 'date_to']) ? 'open' : '' }}>

            <summary class="requisition-section-header donation-filters-summary">
                <div>
                    <h5 class="requisition-section-heading mb-1">
                        <span class="requisition-section-icon">
                            <i class="fa fa-filter"></i>
                        </span>
                        Filter Sync Records
                    </h5>

                    <p class="requisition-list-meta mb-0">
                        Search and narrow down synchronization records.
                    </p>
                </div>

                <i class="fa fa-chevron-down donation-filters-chevron"></i>
            </summary>

            <div class="donation-filters-body">
                <form method="GET"
                      action="{{ route('admin.mpesa-collector.sync-logs.index') }}">

                    <div class="row">

                        <div class="col-md-4 mb-3">
                            <label class="requisition-field-label">Search</label>
                            <input type="text"
                                   name="q"
                                   class="requisition-input"
                                   value="{{ request('q') }}"
                                   placeholder="Sync reference or device name">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="requisition-field-label">Sync Status</label>
                            <select name="status" class="requisition-input">
                                <option value="">All Statuses</option>
                                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="partial" {{ request('status') === 'partial' ? 'selected' : '' }}>Partial</option>
                                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                                <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="requisition-field-label">Device</label>
                            <select name="device_id" class="requisition-input">
                                <option value="">All Devices</option>

                                @foreach($devices as $device)
                                    <option value="{{ $device->id }}"
                                        {{ (string) request('device_id') === (string) $device->id ? 'selected' : '' }}>
                                        {{ $device->device_name }} ({{ $device->device_id }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="requisition-field-label">Date From</label>
                            <input type="date"
                                   name="date_from"
                                   class="requisition-input"
                                   value="{{ request('date_from') }}">
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="requisition-field-label">Date To</label>
                            <input type="date"
                                   name="date_to"
                                   class="requisition-input"
                                   value="{{ request('date_to') }}">
                        </div>

                    </div>

                    <div class="d-flex flex-wrap" style="gap:10px;">
                        <button type="submit" class="requisition-save-button">
                            <i class="fa fa-search mr-1"></i> Apply Filters
                        </button>

                        <a href="{{ route('admin.mpesa-collector.sync-logs.index') }}"
                           class="requisition-cancel-button">
                            <i class="fa fa-times mr-1"></i> Clear Filters
                        </a>
                    </div>

                </form>
            </div>
        </details>
    </div>

    {{-- SYNC RECORDS --}}
    <div class="requisition-section">

        <div class="requisition-section-header">
            <div>
                <h5 class="requisition-section-heading">
                    <span class="requisition-section-icon">
                        <i class="fa fa-list"></i>
                    </span>
                    Synchronization Records
                </h5>

                <p class="requisition-list-meta mb-0">
                    {{ $syncLogs->total() }} synchronization records found.
                </p>
            </div>
        </div>

        <div class="requisition-details-body">

            <div class="requisition-list-table-wrapper">
                <table class="requisition-list-table">

                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Device</th>
                            <th>Sync Reference</th>
                            <th>Received</th>
                            <th>Saved</th>
                            <th>Duplicates</th>
                            <th>Processed</th>
                            <th>Status</th>
                            <th>Details</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($syncLogs as $syncLog)

                            @php
                                $statusClass = match($syncLog->sync_status) {
                                    'completed' => 'approved',
                                    'partial' => 'pending',
                                    'failed' => 'rejected',
                                    default => 'pending',
                                };
                            @endphp

                            <tr>
                                <td>
                                    <strong>
                                        {{ $syncLog->created_at ? \Illuminate\Support\Carbon::parse($syncLog->created_at)->format('d M Y') : '—' }}
                                    </strong>
                                    <div class="requisition-list-meta">
                                        {{ $syncLog->created_at ? \Illuminate\Support\Carbon::parse($syncLog->created_at)->format('h:i A') : '—' }}
                                    </div>
                                </td>

                                <td>
                                    <strong>
                                        {{ $syncLog->device?->device_name ?? 'Unknown Device' }}
                                    </strong>
                                    <div class="requisition-list-meta">
                                        {{ $syncLog->device?->device_id ?? '—' }}
                                    </div>
                                </td>

                                <td>
                                    {{ $syncLog->sync_reference ?? '—' }}
                                </td>

                                <td>{{ number_format($syncLog->messages_received) }}</td>

                                <td>{{ number_format($syncLog->messages_saved) }}</td>

                                <td>{{ number_format($syncLog->duplicates) }}</td>

                                <td>{{ number_format($syncLog->messages_processed) }}</td>

                                <td>
                                    <span class="requisition-list-status requisition-list-status-{{ $statusClass }}">
                                        <span class="requisition-list-status-dot"></span>
                                        {{ ucfirst($syncLog->sync_status) }}
                                    </span>
                                </td>

                                <td>
                                    <div class="requisition-list-actions">
                                        <button type="button"
                                                class="requisition-list-action primary"
                                                data-toggle="modal"
                                                data-target="#syncLogModal{{ $syncLog->id }}"
                                                title="View sync details">
                                            <i class="fa fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                        @empty

                            <tr>
                                <td colspan="9">
                                    <div class="requisition-table-empty">
                                        <div class="requisition-empty-icon">
                                            <i class="fa fa-sync-alt"></i>
                                        </div>
                                        <strong>No synchronization records found.</strong>
                                        <p class="requisition-list-meta">
                                            Sync records will appear here when devices upload messages.
                                        </p>
                                    </div>
                                </td>
                            </tr>

                        @endforelse

                    </tbody>
                </table>
            </div>

            <div class="requisition-scroll-hint">
                <i class="fa fa-arrows-alt-h mr-1"></i>
                Scroll horizontally to view all columns.
            </div>

            {{-- PAGINATION --}}
            @if($syncLogs->hasPages())
                <div class="stores-pagination-section">
                    <div class="stores-pagination-info">
                        Showing {{ $syncLogs->firstItem() }} to {{ $syncLogs->lastItem() }}
                        of {{ $syncLogs->total() }} records
                    </div>

                    <div class="stores-pagination-controls">
                        {{ $syncLogs->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            @endif

        </div>
    </div>

    {{-- SYNC DETAILS MODALS --}}
    @foreach($syncLogs as $syncLog)

        <div class="modal fade"
             id="syncLogModal{{ $syncLog->id }}"
             tabindex="-1"
             role="dialog"
             aria-labelledby="syncLogModalLabel{{ $syncLog->id }}"
             aria-hidden="true">

            <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title"
                            id="syncLogModalLabel{{ $syncLog->id }}">
                            <i class="fa fa-sync-alt mr-2"></i>
                            Synchronization Details
                        </h5>

                        <button type="button"
                                class="close"
                                data-dismiss="modal"
                                aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <h6 class="font-weight-bold mb-3">Sync Information</h6>

                        <div class="row mb-3">

                            <div class="col-md-6 mb-3">
                                <small class="text-muted">Sync Reference</small>
                                <div>{{ $syncLog->sync_reference ?? '—' }}</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <small class="text-muted">Status</small>
                                <div>
                                    <strong>{{ ucfirst($syncLog->sync_status) }}</strong>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <small class="text-muted">Device Name</small>
                                <div>{{ $syncLog->device?->device_name ?? 'Unknown Device' }}</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <small class="text-muted">Device ID</small>
                                <div>{{ $syncLog->device?->device_id ?? '—' }}</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <small class="text-muted">Started At</small>
                                <div>
                                    {{ $syncLog->started_at ? \Illuminate\Support\Carbon::parse($syncLog->started_at)->format('d M Y, h:i:s A') : '—' }}
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <small class="text-muted">Completed At</small>
                                <div>
                                    {{ $syncLog->completed_at ? \Illuminate\Support\Carbon::parse($syncLog->completed_at)->format('d M Y, h:i:s A') : '—' }}
                                </div>
                            </div>

                        </div>

                        <hr>

                        <h6 class="font-weight-bold mb-3">Message Statistics</h6>

                        <div class="row">

                            <div class="col-md-4 mb-3">
                                <small class="text-muted">Messages Received</small>
                                <h5>{{ number_format($syncLog->messages_received) }}</h5>
                            </div>

                            <div class="col-md-4 mb-3">
                                <small class="text-muted">Messages Saved</small>
                                <h5>{{ number_format($syncLog->messages_saved) }}</h5>
                            </div>

                            <div class="col-md-4 mb-3">
                                <small class="text-muted">Duplicates</small>
                                <h5>{{ number_format($syncLog->duplicates) }}</h5>
                            </div>

                            <div class="col-md-4 mb-3">
                                <small class="text-muted">Processed</small>
                                <h5>{{ number_format($syncLog->messages_processed) }}</h5>
                            </div>

                            <div class="col-md-4 mb-3">
                                <small class="text-muted">Unrecognized</small>
                                <h5>{{ number_format($syncLog->messages_unrecognized) }}</h5>
                            </div>

                            <div class="col-md-4 mb-3">
                                <small class="text-muted">Failed</small>
                                <h5>{{ number_format($syncLog->messages_failed) }}</h5>
                            </div>

                        </div>

                        @if($syncLog->error_message)
                            <hr>

                            <h6 class="font-weight-bold text-danger">
                                Synchronization Error
                            </h6>

                            <div class="alert alert-danger">
                                {{ $syncLog->error_message }}
                            </div>
                        @endif

                        @if($syncLog->metadata)
                            <hr>

                            <h6 class="font-weight-bold mb-2">Additional Metadata</h6>

                            <pre class="bg-light p-3 rounded"
                                 style="white-space:pre-wrap; word-break:break-word;">{{ json_encode($syncLog->metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
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

@endsection
