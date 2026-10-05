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
            <i class="fas fa-box-open"></i>
        </div>

        <div>

            <div class="requisition-breadcrumb">
                <span>Stores</span>
                <i class="fas fa-chevron-right"></i>
                <span>Receiving</span>
            </div>

            <h1 class="requisition-page-title">
                Store Receiving
            </h1>

            <p class="requisition-page-subtitle">
                Record and manage goods received into Stores.
            </p>

        </div>

    </div>

    <div class="requisition-header-right">

        <a href="{{ route('admin.store-receipts.create') }}"
           class="requisition-add-button">

            <i class="fas fa-plus"></i>

            <span>
                New Store Receipt
            </span>

        </a>

    </div>

</div>


{{-- ============================================================
     FLASH / SYSTEM MESSAGES
     ============================================================ --}}
<x-message></x-message>


{{-- ============================================================
     RECEIPTS SECTION
     ============================================================ --}}
<div class="requisition-section">

    {{-- Section Header --}}
    <div class="requisition-section-header">

        <div class="requisition-section-heading">

            <div class="requisition-section-icon">
                <i class="fas fa-clipboard-list"></i>
            </div>

            <div>

                <h5>
                    Store Receipts
                </h5>

                <p>
                    Goods received into Stores and their current status.
                </p>

            </div>

        </div>

        @if($receipts->count())

            <span class="requisition-count-badge">
                {{ $receipts->count() }}
                {{ $receipts->count() === 1 ? 'Receipt' : 'Receipts' }}
            </span>

        @endif

    </div>


    {{-- ========================================================
         RECEIPTS TABLE
         ======================================================== --}}
    @if($receipts->count())

        <div class="requisition-list-table-wrapper">

            <table class="requisition-list-table">

                <thead>

                    <tr>
                        <th>#</th>
                        <th>Receipt No.</th>
                        <th>Store</th>
                        <th>Source</th>
                        <th>Supplier / Reference</th>
                        <th>Received Date</th>
                        <th>Status</th>
                        <th>Received By</th>
                        <th>Actions</th>
                    </tr>

                </thead>

                <tbody>

                    @foreach($receipts as $receipt)

                        <tr>

                            {{-- =================================================
                                 ROW NUMBER
                                 ================================================= --}}
                            <td>

                                <span class="requisition-list-row-number">
                                    {{ $loop->iteration }}
                                </span>

                            </td>


                            {{-- =================================================
                                 RECEIPT NUMBER
                                 ================================================= --}}
                            <td>

                                <div class="requisition-list-number">

                                    <a href="{{ route(
                                        'admin.store-receipts.show',
                                        $receipt
                                    ) }}">
                                        {{ $receipt->receipt_number }}
                                    </a>

                                    <div class="requisition-list-meta">
                                        Store Receipt
                                    </div>

                                </div>

                            </td>


                            {{-- =================================================
                                 STORE
                                 ================================================= --}}
                            <td>

                                <span class="requisition-list-department">
                                    {{ $receipt->store->name ?? '—' }}
                                </span>

                            </td>


                            {{-- =================================================
                                 SOURCE
                                 ================================================= --}}
                            <td>

                                @if($receipt->source_type === 'DONATION')

                                    <span class="requisition-list-status requisition-list-status-approved">

                                        <span class="requisition-list-status-dot"></span>

                                        Donation

                                    </span>

                                @else

                                    <span class="requisition-list-status requisition-list-status-draft">

                                        <span class="requisition-list-status-dot"></span>

                                        Purchase

                                    </span>

                                @endif

                            </td>


                            {{-- =================================================
                                 SUPPLIER / REFERENCE
                                 ================================================= --}}
                            <td>

                                @if($receipt->source_type === 'DONATION')

                                    <div class="requisition-list-purpose">

                                        {{ $receipt->donation
                                            ? 'Donation #' . $receipt->donation->id
                                            : '—' }}

                                    </div>

                                @else

                                    <div class="requisition-list-purpose">
                                        {{ $receipt->supplier_name ?: '—' }}
                                    </div>

                                    @if($receipt->supplier_reference)

                                        <div class="requisition-list-time">

                                            Ref:
                                            {{ $receipt->supplier_reference }}

                                        </div>

                                    @endif

                                @endif

                            </td>


                            {{-- =================================================
                                 RECEIVED DATE
                                 ================================================= --}}
                            <td>

                                <div class="requisition-list-date">

                                    {{ $receipt->received_date
                                        ? $receipt->received_date->format('d/m/Y')
                                        : '—' }}

                                </div>

                                @if($receipt->created_at)

                                    <div class="requisition-list-time">

                                        {{ $receipt->created_at->format('H:i') }}

                                    </div>

                                @endif

                            </td>


                            {{-- =================================================
                                 STATUS
                                 ================================================= --}}
                            <td>

                                @switch($receipt->status)

                                    @case('DRAFT')

                                        <span class="requisition-list-status requisition-list-status-draft">

                                            <span class="requisition-list-status-dot"></span>

                                            Draft

                                        </span>

                                        @break


                                    @case('POSTED')

                                        <span class="requisition-list-status requisition-list-status-completed">

                                            <span class="requisition-list-status-dot"></span>

                                            Posted

                                        </span>

                                        @break


                                    @case('CANCELLED')

                                        <span class="requisition-list-status requisition-list-status-cancelled">

                                            <span class="requisition-list-status-dot"></span>

                                            Cancelled

                                        </span>

                                        @break


                                    @default

                                        <span class="requisition-list-status requisition-list-status-draft">

                                            <span class="requisition-list-status-dot"></span>

                                            {{ $receipt->status }}

                                        </span>

                                @endswitch

                            </td>


                            {{-- =================================================
                                 RECEIVED BY
                                 ================================================= --}}
                            <td>

                                <span class="requisition-list-department">

                                    {{ $receipt->receivedBy->name ?? '—' }}

                                </span>

                            </td>


                            {{-- =================================================
                                 ACTIONS
                                 ================================================= --}}
                            <td>

                                <div class="requisition-list-actions">

                                    {{-- View --}}
                                    <a href="{{ route(
                                        'admin.store-receipts.show',
                                        $receipt
                                    ) }}"
                                       class="requisition-list-action primary"
                                       title="View Receipt"
                                       aria-label="View Receipt">

                                        <i class="fas fa-eye"></i>

                                    </a>


                                    @if($receipt->status === 'DRAFT')

                                        {{-- Edit --}}
                                        <a href="{{ route(
                                            'admin.store-receipts.edit',
                                            $receipt
                                        ) }}"
                                           class="requisition-list-action"
                                           title="Edit Receipt"
                                           aria-label="Edit Receipt">

                                            <i class="fas fa-edit"></i>

                                        </a>


                                        {{-- Delete --}}
                                        <form
                                            action="{{ route(
                                                'admin.store-receipts.destroy',
                                                $receipt
                                            ) }}"
                                            method="POST"
                                            class="d-inline"
                                            onsubmit="return confirm(
                                                'Delete this draft store receipt?'
                                            );"
                                        >

                                            @csrf
                                            @method('DELETE')

                                            <button
                                                type="submit"
                                                class="requisition-list-action danger"
                                                title="Delete Receipt"
                                                aria-label="Delete Receipt"
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


        {{-- ========================================================
             HORIZONTAL SCROLL HINT
             ======================================================== --}}
        <div class="requisition-scroll-hint">

            <i class="fas fa-arrows-alt-h"></i>

            <span>
                Scroll horizontally to view all receipt details.
            </span>

        </div>


        {{-- ========================================================
             PAGINATION
             ======================================================== --}}
        @if(method_exists($receipts, 'links'))

            <div class="requisition-pagination">
                {{ $receipts->links() }}
            </div>

        @endif


    @else


        {{-- ========================================================
             EMPTY STATE
             ======================================================== --}}
        <div class="requisition-table-empty">

            <div class="requisition-empty-icon">
                <i class="fas fa-box-open"></i>
            </div>

            <h5>
                No Store Receipts Yet
            </h5>

            <p>
                No goods have been recorded as received into Stores yet.
            </p>

            <a href="{{ route('admin.store-receipts.create') }}"
               class="requisition-add-button">

                <i class="fas fa-plus"></i>

                <span>
                    Create First Store Receipt
                </span>

            </a>

        </div>

    @endif

</div>


</div>

@endsection
