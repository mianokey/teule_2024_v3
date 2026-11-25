@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/stores.css') }}">
@endpush

@section('content')

<div class="store-requisition-page">

    {{-- =========================================================
    PAGE HEADER
    ========================================================= --}}
    <div class="requisition-page-header">

        <div class="requisition-header-content">

            <div class="requisition-header-icon">
                <i class="fa fa-mobile-alt"></i>
            </div>

            <div>

                <div class="requisition-breadcrumb">
                    <span>M-Pesa</span>
                    <span>/</span>
                    <span>Collector Devices</span>
                </div>

                <h1 class="requisition-page-title">
                    M-Pesa Collector Devices
                </h1>

                <p class="requisition-page-subtitle">
                    Register, approve and manage devices collecting M-Pesa transaction messages.
                </p>

            </div>

        </div>

        <div class="requisition-header-right">
            <span class="requisition-list-status requisition-list-status-approved">
                <span class="requisition-list-status-dot"></span>
                Device Management
            </span>
        </div>

    </div>


    {{-- =========================================================
    MESSAGES
    ========================================================= --}}
    <x-message></x-message>


    {{-- =========================================================
    DEVICE APPROVAL TOKEN
    ========================================================= --}}
    @if(session('new_device_token'))

        <div class="requisition-section">

            <div class="requisition-section-header">

                <div class="requisition-section-heading">

                    <div class="requisition-section-icon">
                        <i class="fa fa-shield-alt"></i>
                    </div>

                    <div>
                        <h5>Device Approved Successfully</h5>

                        <p>
                            The device has been approved and its API credentials have been generated.
                        </p>
                    </div>

                </div>

            </div>

            <div class="requisition-details-body">

                <div class="alert alert-warning">
                    <i class="fa fa-exclamation-triangle mr-2"></i>
                    Copy the API token and securely enter it into the approved Android collector.
                    This token should be treated as confidential.
                </div>

                <div class="row">

                    <div class="col-md-4 mb-3">

                        <label class="requisition-field-label">
                            Device Name
                        </label>

                        <input type="text"
                            class="requisition-input"
                            value="{{ session('new_device_name') }}"
                            readonly>

                    </div>

                    <div class="col-md-4 mb-3">

                        <label class="requisition-field-label">
                            Device ID
                        </label>

                        <input type="text"
                            class="requisition-input"
                            value="{{ session('new_device_id') }}"
                            readonly>

                    </div>

                    <div class="col-md-4 mb-3">

                        <label class="requisition-field-label">
                            Device API Token
                        </label>

                        <div class="d-flex" style="gap:8px;">

                            <input type="password"
                                id="mpesaDeviceToken"
                                class="requisition-input"
                                value="{{ session('new_device_token') }}"
                                readonly>

                            <button type="button"
                                class="requisition-save-button"
                                onclick="toggleMpesaToken()"
                                title="Show or hide token">

                                <i class="fa fa-eye"></i>

                            </button>

                            <button type="button"
                                class="requisition-cancel-button"
                                onclick="copyMpesaToken()"
                                title="Copy token">

                                <i class="fa fa-copy"></i>

                            </button>

                        </div>

                    </div>

                </div>

                <small class="text-muted">
                    Keep this token secure. It will not be displayed again after this approval response.
                </small>

            </div>

        </div>

    @endif


    {{-- =========================================================
    DEVICE SUMMARY
    ========================================================= --}}
    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fa fa-chart-pie"></i>
                </div>

                <div>
                    <h5>Device Registration Overview</h5>

                    <p>
                        Summary of registered M-Pesa collector devices by approval status.
                    </p>
                </div>

            </div>

        </div>

        <div class="requisition-details-body">

            <div class="row">

                <div class="col-xl-2 col-md-4 col-6 mb-3">

                    <div class="requisition-section h-100 mb-0">

                        <div class="requisition-section-heading">
                            <div class="requisition-section-icon">
                                <i class="fa fa-mobile-alt"></i>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="requisition-list-meta">All Devices</div>

                            <div class="requisition-page-title" style="font-size:26px;">
                                {{ number_format($counts['all'] ?? 0) }}
                            </div>
                        </div>

                    </div>

                </div>

                <div class="col-xl-2 col-md-4 col-6 mb-3">

                    <div class="requisition-section h-100 mb-0">

                        <div class="requisition-section-heading">
                            <div class="requisition-section-icon">
                                <i class="fa fa-clock"></i>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="requisition-list-meta">Pending</div>

                            <div class="requisition-page-title" style="font-size:26px;">
                                {{ number_format($counts['pending'] ?? 0) }}
                            </div>
                        </div>

                    </div>

                </div>

                <div class="col-xl-2 col-md-4 col-6 mb-3">

                    <div class="requisition-section h-100 mb-0">

                        <div class="requisition-section-heading">
                            <div class="requisition-section-icon">
                                <i class="fa fa-check-circle"></i>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="requisition-list-meta">Approved</div>

                            <div class="requisition-page-title" style="font-size:26px;">
                                {{ number_format($counts['approved'] ?? 0) }}
                            </div>
                        </div>

                    </div>

                </div>

                <div class="col-xl-2 col-md-4 col-6 mb-3">

                    <div class="requisition-section h-100 mb-0">

                        <div class="requisition-section-heading">
                            <div class="requisition-section-icon">
                                <i class="fa fa-pause-circle"></i>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="requisition-list-meta">Suspended</div>

                            <div class="requisition-page-title" style="font-size:26px;">
                                {{ number_format($counts['suspended'] ?? 0) }}
                            </div>
                        </div>

                    </div>

                </div>

                <div class="col-xl-2 col-md-4 col-6 mb-3">

                    <div class="requisition-section h-100 mb-0">

                        <div class="requisition-section-heading">
                            <div class="requisition-section-icon">
                                <i class="fa fa-times-circle"></i>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="requisition-list-meta">Rejected</div>

                            <div class="requisition-page-title" style="font-size:26px;">
                                {{ number_format($counts['rejected'] ?? 0) }}
                            </div>
                        </div>

                    </div>

                </div>

                <div class="col-xl-2 col-md-4 col-6 mb-3">

                    <div class="requisition-section h-100 mb-0">

                        <div class="requisition-section-heading">
                            <div class="requisition-section-icon">
                                <i class="fa fa-ban"></i>
                            </div>
                        </div>

                        <div class="mt-3">
                            <div class="requisition-list-meta">Revoked</div>

                            <div class="requisition-page-title" style="font-size:26px;">
                                {{ number_format($counts['revoked'] ?? 0) }}
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
    DEVICE FILTERS
    ========================================================= --}}
    <div class="requisition-section donation-filters-section">

        <details class="donation-filters-details"
            {{ request()->filled('status') ? 'open' : '' }}>

            <summary class="requisition-section-header donation-filters-summary">

                <div class="requisition-section-heading">

                    <div class="requisition-section-icon">
                        <i class="fa fa-filter"></i>
                    </div>

                    <div>
                        <h5>Search & Filter Collector Devices</h5>

                        <p>
                            Filter registered devices by their approval status.
                        </p>
                    </div>

                </div>

                <i class="fa fa-chevron-down donation-filters-chevron"></i>

            </summary>

            <div class="requisition-details-body donation-filters-body">

                <div class="requisition-section">

                    <div class="requisition-details-body">

                        <form method="GET"
                            action="{{ route('admin.mpesa-collector.devices.index') }}">

                            <div class="row mb-3">

                                <div class="col-md-3">

                                    <label class="requisition-field-label">
                                        Device Status
                                    </label>

                                    <select name="status" class="requisition-input">

                                        <option value="">All Devices</option>

                                        <option value="pending"
                                            @selected(request('status') === 'pending')>
                                            Pending Approval
                                        </option>

                                        <option value="approved"
                                            @selected(request('status') === 'approved')>
                                            Approved
                                        </option>

                                        <option value="suspended"
                                            @selected(request('status') === 'suspended')>
                                            Suspended
                                        </option>

                                        <option value="rejected"
                                            @selected(request('status') === 'rejected')>
                                            Rejected
                                        </option>

                                        <option value="revoked"
                                            @selected(request('status') === 'revoked')>
                                            Revoked
                                        </option>

                                    </select>

                                </div>

                            </div>

                            <div class="d-flex flex-wrap justify-content-end"
                                style="gap:10px;">

                                <button type="submit" class="requisition-save-button">
                                    <i class="fa fa-search"></i>
                                    Apply Filters
                                </button>

                                <a href="{{ route('admin.mpesa-collector.devices.index') }}"
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


    {{-- =========================================================
    REGISTERED DEVICES
    ========================================================= --}}
    <div class="requisition-section">

        {{-- TABLE HEADER --}}
        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fa fa-mobile-alt"></i>
                </div>

                <div>
                    <h5>Registered Collector Devices</h5>

                    <p>
                        {{ number_format($devices->total()) }}
                        {{ $devices->total() === 1 ? 'device' : 'devices' }}

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

                        <th style="min-width:170px;">
                            Device
                        </th>

                        <th style="min-width:130px;">
                            Location
                        </th>

                        <th style="min-width:130px;">
                            Department
                        </th>

                        <th style="min-width:130px;">
                            Status
                        </th>

                        <th style="min-width:140px;">
                            Approved By
                        </th>

                        <th style="min-width:130px;">
                            Last Sync
                        </th>

                        <th style="min-width:150px;">
    Last Seen
</th>


                        <th style="min-width:130px;">
                            Registered
                        </th>

                        <th style="width:180px;">
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($devices as $device)

                        @php

                            $statusClasses = [

                                'pending' =>
                                'requisition-list-status-pending',

                                'approved' =>
                                'requisition-list-status-approved',

                                'rejected' =>
                                'requisition-list-status-rejected',

                                'suspended' =>
                                'requisition-list-status-pending',

                                'revoked' =>
                                'requisition-list-status-rejected',

                            ];

                            $statusLabels = [

                                'pending' => 'Pending Approval',
                                'approved' => 'Approved',
                                'rejected' => 'Rejected',
                                'suspended' => 'Suspended',
                                'revoked' => 'Revoked',

                            ];

                        @endphp

                        <tr>

                            {{-- DEVICE --}}
                            <td>

                                <div class="requisition-list-number">
                                    {{ $device->device_name }}
                                </div>

                                <div class="requisition-list-meta">
                                    {{ $device->device_id }}
                                </div>

                            </td>


                            {{-- LOCATION --}}
                            <td>

                                <span class="requisition-list-department">
                                    {{ $device->location ?: '—' }}
                                </span>

                            </td>


                            {{-- DEPARTMENT --}}
                            <td>

                                <span class="requisition-list-department">
                                    {{ $device->department ?: '—' }}
                                </span>

                            </td>


                            {{-- STATUS --}}
                            <td>

                                <span class="requisition-list-status {{ $statusClasses[$device->status] ?? 'requisition-list-status-draft' }}">

                                    <span class="requisition-list-status-dot"></span>

                                    {{ $statusLabels[$device->status] ?? ucfirst($device->status) }}

                                </span>

                            </td>


                            {{-- APPROVER --}}
                            <td>

                                <span class="requisition-list-department">
                                    {{ optional($device->approver)->name ?? '—' }}
                                </span>

                            </td>


                            {{-- LAST SYNC --}}
                            <td>

                                @if($device->last_sync_at)

                                    <span class="requisition-list-date">
                                        {{ \Illuminate\Support\Carbon::parse($device->last_sync_at)->format('d M Y') }}
                                    </span>

                                    <div class="requisition-list-meta">
                                        {{ \Illuminate\Support\Carbon::parse($device->last_sync_at)->format('h:i A') }}
                                    </div>

                                @else

                                    <span class="requisition-list-meta">
                                        Never synced
                                    </span>

                                @endif

                            </td>

                            {{-- LAST SEEN --}}
<td>

    @if($device->last_seen_at)

        <span class="requisition-list-date">
            {{ \Illuminate\Support\Carbon::parse($device->last_seen_at)->format('d M Y') }}
        </span>

        <div class="requisition-list-meta">
            {{ \Illuminate\Support\Carbon::parse($device->last_seen_at)->format('h:i A') }}
        </div>

        <div class="requisition-list-meta">
            {{ \Illuminate\Support\Carbon::parse($device->last_seen_at)->diffForHumans() }}
        </div>

    @else

        <span class="requisition-list-meta">
            Never connected
        </span>

    @endif

</td>



                            {{-- REGISTERED --}}
                            <td>

                                <span class="requisition-list-date">
                                    {{ $device->created_at->format('d M Y') }}
                                </span>

                                <div class="requisition-list-meta">
                                    {{ $device->created_at->format('h:i A') }}
                                </div>

                            </td>


                            {{-- ACTIONS --}}
                            <td>

                                <div class="requisition-list-actions">

                                    {{-- APPROVE AND REJECT --}}
                                    @if($device->status === 'pending')

                                        <form method="POST"
                                            action="{{ route('admin.mpesa-collector.devices.approve', $device->id) }}"
                                            onsubmit="return confirm('Approve this collector device?');">

                                            @csrf
                                            @method('PATCH')

                                            <button type="submit"
                                                class="requisition-list-action primary"
                                                title="Approve device">

                                                <i class="fa fa-check"></i>

                                            </button>

                                        </form>


                                        <form method="POST"
                                            action="{{ route('admin.mpesa-collector.devices.reject', $device->id) }}"
                                            onsubmit="return confirm('Reject this device registration?');">

                                            @csrf
                                            @method('PATCH')

                                            <button type="submit"
                                                class="requisition-list-action"
                                                title="Reject device">

                                                <i class="fa fa-times"></i>

                                            </button>

                                        </form>

                                    {{-- SUSPEND AND REVOKE --}}
                                    @elseif($device->status === 'approved')

                                        <form method="POST"
                                            action="{{ route('admin.mpesa-collector.devices.suspend', $device->id) }}"
                                            onsubmit="return confirm('Suspend this collector device?');">

                                            @csrf
                                            @method('PATCH')

                                            <button type="submit"
                                                class="requisition-list-action"
                                                title="Suspend device">

                                                <i class="fa fa-pause"></i>

                                            </button>

                                        </form>


                                        <form method="POST"
                                            action="{{ route('admin.mpesa-collector.devices.revoke', $device->id) }}"
                                            onsubmit="return confirm('Revoke access to this device?');">

                                            @csrf
                                            @method('PATCH')

                                            <button type="submit"
                                                class="requisition-list-action"
                                                title="Revoke device">

                                                <i class="fa fa-ban"></i>

                                            </button>

                                        </form>

                                    {{-- REVOKE SUSPENDED DEVICE --}}
                                    @elseif($device->status === 'suspended')

                                        <form method="POST"
                                            action="{{ route('admin.mpesa-collector.devices.revoke', $device->id) }}"
                                            onsubmit="return confirm('Revoke access to this device?');">

                                            @csrf
                                            @method('PATCH')

                                            <button type="submit"
                                                class="requisition-list-action"
                                                title="Revoke device">

                                                <i class="fa fa-ban"></i>

                                            </button>

                                        </form>

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="9">

                                <div class="requisition-table-empty">

                                    <div class="requisition-empty-icon">
                                        <i class="fa fa-mobile-alt"></i>
                                    </div>

                                    <h5>
                                        No collector devices found
                                    </h5>

                                    <p>
                                        Devices registered through the Teule M-Pesa Collector
                                        Android application will appear here.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- =====================================================
        SCROLL INDICATOR
        ===================================================== --}}
        @if($devices->count() > 8)

            <div class="requisition-scroll-hint">

                <i class="fa fa-arrows-alt-v"></i>

                Scroll to view more devices

            </div>

        @endif


        {{-- =====================================================
        PAGINATION
        ===================================================== --}}
        @if($devices->total() > 0)

            <div class="stores-pagination-section">

                <div class="stores-pagination-info">

                    <div class="stores-pagination-info-icon">
                        <i class="fa fa-layer-group"></i>
                    </div>

                    <div class="stores-pagination-info-text">

                        <span class="stores-pagination-info-label">
                            Showing devices
                        </span>

                        <strong>
                            {{ $devices->firstItem() ?? 0 }}
                            –
                            {{ $devices->lastItem() ?? 0 }}
                        </strong>

                        <span class="stores-pagination-info-total">
                            of {{ number_format($devices->total()) }} total
                        </span>

                    </div>

                </div>

                @if($devices->hasPages())

                    <div class="stores-pagination-controls">

                        {{ $devices->onEachSide(2)->withQueryString()->links('pagination::bootstrap-5') }}

                    </div>

                @endif

            </div>

        @endif

    </div>

</div>


{{-- =========================================================
TOKEN ACTIONS
========================================================= --}}
<script>
    function toggleMpesaToken() {

        const token = document.getElementById('mpesaDeviceToken');

        if (!token) return;

        token.type = token.type === 'password' ? 'text' : 'password';
    }

    function copyMpesaToken() {

        const token = document.getElementById('mpesaDeviceToken');

        if (!token) return;

        token.type = 'text';
        token.select();
        token.setSelectionRange(0, 99999);

        if (navigator.clipboard && window.isSecureContext) {

            navigator.clipboard.writeText(token.value)
                .then(() => alert('Device API token copied successfully.'))
                .catch(() => alert('Unable to copy token. Please select and copy it manually.'));

        } else {

            try {
                document.execCommand('copy');
                alert('Device API token copied successfully.');
            } catch (error) {
                alert('Unable to copy token. Please select and copy it manually.');
            }

        }

        token.type = 'password';
    }
</script>

@endsection
