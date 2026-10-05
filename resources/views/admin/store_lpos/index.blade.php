@extends('layouts.admin')

@push('styles') <link rel="stylesheet" href="{{ asset('assets/css/stores.css') }}">
@endpush

@section('content')

<div class="store-requisition-page">


{{-- ============================================================
     PAGE HEADER
     ============================================================ --}}
<div class="requisition-page-header">

    <div class="requisition-header-content">

        <div class="requisition-header-icon">
            <i class="fas fa-file-invoice"></i>
        </div>

        <div>
            <div class="requisition-breadcrumb">
                <span>Stores</span>
                <i class="fas fa-chevron-right"></i>
                <span>Purchase Orders</span>
            </div>

            <h1 class="requisition-page-title">
                Local Purchase Orders
            </h1>

            <p class="requisition-page-subtitle">
                Create, manage and approve store purchase orders.
            </p>
        </div>

    </div>

    <div class="requisition-header-right">

        <a href="{{ route('admin.store-lpos.create') }}"
           class="requisition-add-button">

            <i class="fas fa-plus"></i>

            <span>New LPO</span>

        </a>

    </div>

</div>


{{-- ============================================================
     MESSAGES
     ============================================================ --}}
<x-message></x-message>


{{-- ============================================================
     FILTERS
     ============================================================ --}}
<div class="requisition-section">

    <div class="requisition-section-header">

        <div class="requisition-section-heading">

            <div class="requisition-section-icon">
                <i class="fas fa-filter"></i>
            </div>

            <div>
                <h5>Search & Filter</h5>

                <p>
                    Find LPOs by number, supplier or workflow status.
                </p>
            </div>

        </div>

    </div>


    <div class="requisition-section-body">

        <form method="GET"
              action="{{ route('admin.store-lpos.index') }}">

            <div class="row">

                {{-- Search --}}
                <div class="col-md-5">

                    <div class="requisition-detail-item">

                        <label class="requisition-detail-label">
                            Search
                        </label>

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            class="form-control requisition-input"
                            placeholder="LPO number or supplier name"
                        >

                    </div>

                </div>


                {{-- Status --}}
                <div class="col-md-3">

                    <div class="requisition-detail-item">

                        <label class="requisition-detail-label">
                            Status
                        </label>

                        <select
                            name="status"
                            class="form-control requisition-input"
                        >
                            <option value="">
                                All Statuses
                            </option>

                            <option value="DRAFT"
                                {{ request('status') === 'DRAFT' ? 'selected' : '' }}>
                                Draft
                            </option>

                            <option value="PENDING"
                                {{ request('status') === 'PENDING' ? 'selected' : '' }}>
                                Pending Approval
                            </option>

                            <option value="RETURNED"
                                {{ request('status') === 'RETURNED' ? 'selected' : '' }}>
                                Returned
                            </option>

                            <option value="REJECTED"
                                {{ request('status') === 'REJECTED' ? 'selected' : '' }}>
                                Rejected
                            </option>

                            <option value="APPROVED"
                                {{ request('status') === 'APPROVED' ? 'selected' : '' }}>
                                Approved
                            </option>

                            <option value="PARTIALLY_RECEIVED"
                                {{ request('status') === 'PARTIALLY_RECEIVED' ? 'selected' : '' }}>
                                Partially Received
                            </option>

                            <option value="FULLY_RECEIVED"
                                {{ request('status') === 'FULLY_RECEIVED' ? 'selected' : '' }}>
                                Fully Received
                            </option>

                        </select>

                    </div>

                </div>


                {{-- Approval Stage --}}
                <div class="col-md-2">

                    <div class="requisition-detail-item">

                        <label class="requisition-detail-label">
                            Approval Stage
                        </label>

                        <select
                            name="approval_stage"
                            class="form-control requisition-input"
                        >

                            <option value="">
                                All Stages
                            </option>

                            <option value="HOD"
                                {{ request('approval_stage') === 'HOD' ? 'selected' : '' }}>
                                HOD
                            </option>

                            <option value="MANAGEMENT"
                                {{ request('approval_stage') === 'MANAGEMENT' ? 'selected' : '' }}>
                                Management
                            </option>

                            <option value="COMPLETED"
                                {{ request('approval_stage') === 'COMPLETED' ? 'selected' : '' }}>
                                Completed
                            </option>

                        </select>

                    </div>

                </div>


                {{-- Actions --}}
                <div class="col-md-2">

                    <div class="requisition-detail-item">

                        <label class="requisition-detail-label">
                            &nbsp;
                        </label>

                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                class="requisition-add-button"
                            >
                                <i class="fas fa-search"></i>
                                Filter
                            </button>

                            <a
                                href="{{ route('admin.store-lpos.index') }}"
                                class="requisition-cancel-button"
                            >
                                <i class="fas fa-times"></i>
                            </a>

                        </div>

                    </div>

                </div>

            </div>

        </form>

    </div>

</div>


{{-- ============================================================
     LPO LIST
     ============================================================ --}}
<div class="requisition-section">

    <div class="requisition-section-header">

        <div class="requisition-section-heading">

            <div class="requisition-section-icon">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>

            <div>

                <h5>
                    LPO List
                </h5>

                <p>
                    Purchase orders currently recorded in the system.
                </p>

            </div>

        </div>


        @if($lpos->count())

            <span class="requisition-count-badge">

                {{ $lpos->total() }}

                {{ $lpos->total() === 1 ? 'LPO' : 'LPOs' }}

            </span>

        @endif

    </div>


    @if($lpos->count())

        <div class="requisition-list-table-wrapper">

            <table class="requisition-list-table">

                <thead>

                    <tr>

                        <th>
                            #
                        </th>

                        <th>
                            LPO
                        </th>

                        <th>
                            Supplier
                        </th>

                        <th>
                            Store
                        </th>

                        <th>
                            LPO Date
                        </th>

                        <th>
                            Expected Delivery
                        </th>

                        <th>
                            Items
                        </th>

                        <th>
                            Total
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Stage
                        </th>

                        <th>
                            Actions
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @foreach($lpos as $index => $lpo)

                        <tr>

                            {{-- Number --}}
                            <td>

                                <div class="requisition-list-row-number">

                                    {{ $lpos->firstItem() + $index }}

                                </div>

                            </td>


                            {{-- LPO --}}
                            <td>

                                <div class="requisition-list-number">

                                    {{ $lpo->lpo_number }}

                                </div>

                                @if($lpo->creator)

                                    <div class="requisition-list-meta">

                                        Created by
                                        {{ $lpo->creator->name ?? $lpo->creator->email }}

                                    </div>

                                @endif

                            </td>


                            {{-- Supplier --}}
                            <td>

                                <div class="requisition-list-purpose">

                                    {{ $lpo->supplier->name ?? '—' }}

                                </div>

                                @if($lpo->supplier?->supplier_code)

                                    <div class="requisition-list-meta">

                                        {{ $lpo->supplier->supplier_code }}

                                    </div>

                                @endif

                            </td>


                            {{-- Store --}}
                            <td>

                                {{ $lpo->store->name ?? '—' }}

                            </td>


                            {{-- LPO Date --}}
                            <td>

                                {{ $lpo->lpo_date?->format('d M Y') ?? '—' }}

                            </td>


                            {{-- Expected Delivery --}}
                            <td>

                                {{ $lpo->expected_delivery_date?->format('d M Y') ?? '—' }}

                            </td>


                            {{-- Items --}}
                            <td>

                                {{ $lpo->items_count }}

                                {{ $lpo->items_count === 1 ? 'item' : 'items' }}

                            </td>


                            {{-- Total --}}
                            <td>

                                <strong>
                                    KSh {{ number_format((float) $lpo->total, 2) }}
                                </strong>

                            </td>


                            {{-- Status --}}
                            <td>

                                @php
                                    $statusClass = match ($lpo->status) {
                                        'DRAFT' => 'stores-badge-secondary',
                                        'PENDING' => 'stores-badge-warning',
                                        'RETURNED' => 'stores-badge-info',
                                        'REJECTED' => 'stores-badge-danger',
                                        'APPROVED' => 'stores-badge-success',
                                        'PARTIALLY_RECEIVED' => 'stores-badge-warning',
                                        'FULLY_RECEIVED' => 'stores-badge-success',
                                        default => 'stores-badge-secondary',
                                    };

                                    $statusLabel = match ($lpo->status) {
                                        'DRAFT' => 'Draft',
                                        'PENDING' => 'Pending Approval',
                                        'RETURNED' => 'Returned',
                                        'REJECTED' => 'Rejected',
                                        'APPROVED' => 'Approved',
                                        'PARTIALLY_RECEIVED' => 'Partially Received',
                                        'FULLY_RECEIVED' => 'Fully Received',
                                        default => ucfirst(strtolower(str_replace('_', ' ', $lpo->status ?? 'Unknown'))),
                                    };
                                @endphp

                                <span class="stores-badge {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>

                            </td>


                            {{-- Approval Stage --}}
                            <td>

                                @php
                                    $stageLabel = match ($lpo->approval_stage) {
                                        'HOD' => 'HOD',
                                        'MANAGEMENT' => 'Management',
                                        'COMPLETED' => 'Completed',
                                        null => '—',
                                        default => ucfirst(strtolower(str_replace('_', ' ', $lpo->approval_stage))),
                                    };
                                @endphp

                                @if($lpo->approval_stage)

                                    <span class="stores-badge stores-badge-light">
                                        {{ $stageLabel }}
                                    </span>

                                @else
                                    —
                                @endif

                            </td>


                            {{-- Actions --}}
                            <td>

                                <div class="requisition-list-actions">

                                    {{-- View --}}
                                    <a
                                        href="{{ route('admin.store-lpos.show', $lpo) }}"
                                        class="requisition-list-action"
                                        title="View LPO"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </a>


                                    {{-- Edit --}}
                                    @if(in_array($lpo->status, ['DRAFT', 'RETURNED'], true))

                                        <a
                                            href="{{ route('admin.store-lpos.edit', $lpo) }}"
                                            class="requisition-list-action"
                                            title="Edit LPO"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </a>

                                    @endif


                                    {{-- Submit --}}
                                    @if($lpo->status === 'DRAFT' && $lpo->items_count > 0)

                                        <form
                                            method="POST"
                                            action="{{ route('admin.store-lpos.submit', $lpo) }}"
                                            class="d-inline"
                                        >

                                            @csrf

                                            <button
                                                type="submit"
                                                class="requisition-list-action"
                                                title="Submit for HOD Approval"
                                                onclick="return confirm('Submit this LPO for HOD approval?');"
                                            >
                                                <i class="fas fa-paper-plane"></i>
                                            </button>

                                        </form>

                                    @endif


                                    {{-- Delete --}}
                                    @if(in_array($lpo->status, ['DRAFT', 'RETURNED', 'REJECTED'], true))

                                        <form
                                            method="POST"
                                            action="{{ route('admin.store-lpos.destroy', $lpo) }}"
                                            class="d-inline"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="requisition-list-action"
                                                title="Delete LPO"
                                                onclick="return confirm('Delete this LPO? This action cannot be undone.');"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </button>

                                        </form>

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>


        <div class="requisition-scroll-hint">

            <i class="fas fa-arrows-alt-h"></i>

            <span>
                Scroll horizontally to view all LPO details.
            </span>

        </div>


    @else

        <div class="requisition-table-empty">

            <div class="requisition-empty-icon">

                <i class="fas fa-file-invoice"></i>

            </div>

            <h5>
                No LPOs Found
            </h5>

            <p>
                No purchase orders match your current search or filter.
            </p>

            <a
                href="{{ route('admin.store-lpos.create') }}"
                class="requisition-add-button"
            >
                <i class="fas fa-plus"></i>
                Create First LPO
            </a>

        </div>

    @endif

</div>


</div>

@endsection
