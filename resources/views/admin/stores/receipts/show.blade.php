@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/stores.css') }}">
@endpush

@section('content')

<div class="store-requisition-page">

    {{-- ============================================================
         PAGE HEADER
         ============================================================ --}}

    <div class="requisition-page-header">

        <div class="requisition-header-content">

            <div class="requisition-header-icon">
                <i class="fas fa-receipt"></i>
            </div>

            <div>

                <div class="requisition-breadcrumb">
                    <span>Stores</span>
                    <i class="fas fa-chevron-right"></i>
                    <span>Receiving</span>
                    <i class="fas fa-chevron-right"></i>
                    <span>Receipt</span>
                </div>

                <h1 class="requisition-page-title">
                    Store Receipt
                </h1>

                <div class="requisition-number">
                    {{ $storeReceipt->receipt_number }}
                </div>

            </div>

        </div>


        {{-- ========================================================
             PAGE ACTIONS
             ======================================================== --}}

        <div class="requisition-page-actions">

            {{-- Receipt Details --}}
            <button type="button"
                    class="requisition-add-button"
                    data-bs-toggle="modal"
                    data-bs-target="#receiptDetailsModal">

                <i class="fas fa-info-circle"></i>

                <span>
                    Receipt Details
                </span>

            </button>


            @if($storeReceipt->status === 'DRAFT')

                {{-- Edit Receipt --}}
                <a href="{{ route(
                    'admin.store-receipts.edit',
                    $storeReceipt
                ) }}"
                   class="requisition-add-button">

                    <i class="fas fa-edit"></i>

                    <span>
                        Edit Receipt
                    </span>

                </a>


                {{-- Post Receipt --}}
                @if($storeReceipt->items->count())

                    <form method="POST"
                          action="{{ route(
                              'admin.store-receipts.post',
                              $storeReceipt
                          ) }}"
                          class="d-inline"
                          onsubmit="return confirm(
                              'Post this receipt? This will increase the store stock and cannot be undone from this screen.'
                          );">

                        @csrf

                        <button type="submit"
                                class="requisition-add-button">

                            <i class="fas fa-check"></i>

                            <span>
                                Post Receipt
                            </span>

                        </button>

                    </form>

                @endif

            @endif


            {{-- Back --}}
            <a href="{{ route('admin.store-receipts.index') }}"
               class="requisition-cancel-button">

                <i class="fas fa-arrow-left"></i>

                <span>
                    Back to Receipts
                </span>

            </a>

        </div>

    </div>


    {{-- ============================================================
         MESSAGES
         ============================================================ --}}

    <x-message></x-message>


    {{-- ============================================================
         RECEIPT STATE / STATUS
         ============================================================ --}}

    <div class="requisition-state-panel">

        <div class="requisition-state-main">

            <div class="requisition-state-icon">

                @if($storeReceipt->status === 'DRAFT')

                    <i class="fas fa-pencil-alt"></i>

                @elseif($storeReceipt->status === 'POSTED')

                    <i class="fas fa-check"></i>

                @else

                    <i class="fas fa-box"></i>

                @endif

            </div>


            <div class="requisition-state-text">

                <strong>
                    Receipt {{ $storeReceipt->receipt_number }}
                </strong>

                <span>

                    {{ $storeReceipt->items->count() }}

                    {{ $storeReceipt->items->count() === 1
                        ? 'item'
                        : 'items'
                    }}

                    recorded

                </span>

            </div>

        </div>


        <div class="requisition-state-actions">

            @if($storeReceipt->status === 'DRAFT')

                <span class="requisition-status requisition-status-draft">
                    DRAFT
                </span>

            @elseif($storeReceipt->status === 'POSTED')

                <span class="requisition-status requisition-status-approved">
                    POSTED
                </span>

            @elseif($storeReceipt->status === 'CANCELLED')

                <span class="requisition-status requisition-status-rejected">
                    CANCELLED
                </span>

            @else

                <span class="requisition-status requisition-status-draft">
                    {{ $storeReceipt->status }}
                </span>

            @endif

        </div>

    </div>


    {{-- ============================================================
         GOODS RECEIVED
         ============================================================ --}}

    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fas fa-boxes"></i>
                </div>

                <div>

                    <h5>
                        Goods Received
                    </h5>

                    <p>
                        Items physically received into this store.
                    </p>

                </div>

            </div>


            <div class="d-flex align-items-center gap-2">

                @if($storeReceipt->items->count())

                    <span class="requisition-count-badge">

                        {{ $storeReceipt->items->count() }}

                        {{ $storeReceipt->items->count() === 1
                            ? 'Item'
                            : 'Items'
                        }}

                    </span>

                @endif


                {{-- Editing is now handled entirely on edit.blade.php --}}
                @if($storeReceipt->status === 'DRAFT')

                    <a href="{{ route(
                        'admin.store-receipts.edit',
                        $storeReceipt
                    ) }}"
                       class="requisition-add-button">

                        <i class="fas fa-edit"></i>

                        <span>
                            Edit Items
                        </span>

                    </a>

                @endif

            </div>

        </div>


        {{-- ========================================================
             ITEMS TABLE
             ======================================================== --}}

        @if($storeReceipt->items->count())

            <div class="requisition-list-table-wrapper">

                <table class="requisition-list-table">

                    <thead>

                        <tr>

                            <th>
                                #
                            </th>

                            <th>
                                Item
                            </th>

                            <th>
                                Variant
                            </th>

                            <th>
                                Unit
                            </th>

                            <th>
                                Quantity
                            </th>

                            <th>
                                Notes
                            </th>

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($storeReceipt->items as $receiptItem)

                            <tr>

                                {{-- =================================================
                                     NUMBER
                                     ================================================= --}}

                                <td class="requisition-list-row-number">

                                    <span class="requisition-list-number">
                                        {{ $loop->iteration }}
                                    </span>

                                </td>


                                {{-- =================================================
                                     ITEM
                                     ================================================= --}}

                                <td>

                                    <div class="requisition-list-department">

                                        {{ $receiptItem->item->name ?? '—' }}

                                    </div>


                                    @if($receiptItem->item->sku ?? null)

                                        <div class="requisition-list-meta">

                                            SKU:
                                            {{ $receiptItem->item->sku }}

                                        </div>

                                    @endif

                                </td>


                                {{-- =================================================
                                     VARIANT
                                     ================================================= --}}

                                <td>

                                    @if($receiptItem->variant)

                                        <div class="requisition-list-department">

                                            {{ $receiptItem->variant->name }}

                                        </div>


                                        @if($receiptItem->variant->code)

                                            <div class="requisition-list-meta">

                                                {{ $receiptItem->variant->code }}

                                            </div>

                                        @endif

                                    @else

                                        <span class="requisition-list-meta">
                                            No Variant
                                        </span>

                                    @endif

                                </td>


                                {{-- =================================================
                                     UNIT
                                     ================================================= --}}

                                <td>

                                    <span class="requisition-list-meta">

                                        {{ $receiptItem->item->unit->code
                                            ?? $receiptItem->item->unit->name
                                            ?? '—'
                                        }}

                                    </span>

                                </td>


                                {{-- =================================================
                                     QUANTITY
                                     ================================================= --}}

                                <td>

                                    <span class="requisition-list-number">

                                        {{ number_format(
                                            (float) $receiptItem->quantity,
                                            3
                                        ) }}

                                    </span>

                                </td>


                                {{-- =================================================
                                     NOTES
                                     ================================================= --}}

                                <td>

                                    @if($receiptItem->notes)

                                        <span class="requisition-list-purpose">
                                            {{ $receiptItem->notes }}
                                        </span>

                                    @else

                                        <span class="requisition-list-meta">
                                            —
                                        </span>

                                    @endif

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
                 ITEMS SUMMARY
                 ======================================================== --}}

            <div class="requisition-summary-strip">

                <div class="requisition-summary-item">

                    <span class="requisition-summary-label">
                        Total Lines
                    </span>

                    <span class="requisition-summary-value">
                        {{ $storeReceipt->items->count() }}
                    </span>

                </div>


                <div class="requisition-summary-item">

                    <span class="requisition-summary-label">
                        Receipt Status
                    </span>

                    <span class="requisition-summary-value">
                        {{ $storeReceipt->status }}
                    </span>

                </div>


                <div class="requisition-summary-item">

                    <span class="requisition-summary-label">
                        Source
                    </span>

                    <span class="requisition-summary-value">
                        {{ $storeReceipt->source_type }}
                    </span>

                </div>


                <div class="requisition-summary-item">

                    <span class="requisition-summary-label">
                        Received Date
                    </span>

                    <span class="requisition-summary-value">

                        {{ optional(
                            $storeReceipt->received_date
                        )->format('d M Y') }}

                    </span>

                </div>

            </div>


        @else

            {{-- ========================================================
                 EMPTY ITEMS STATE
                 ======================================================== --}}

            <div class="requisition-table-empty">

                <div class="requisition-empty-icon">
                    <i class="fas fa-box-open"></i>
                </div>

                <h5>
                    No Goods Added Yet
                </h5>

                <p>
                    No items have been recorded for this receipt.
                </p>


                @if($storeReceipt->status === 'DRAFT')

                    <a href="{{ route(
                        'admin.store-receipts.edit',
                        $storeReceipt
                    ) }}"
                       class="requisition-add-button">

                        <i class="fas fa-plus"></i>

                        <span>
                            Add Items
                        </span>

                    </a>

                @endif

            </div>

        @endif

    </div>


    {{-- ============================================================
         BOTTOM ACTIONS
         ============================================================ --}}

    <div class="requisition-bottom-actions">

        <div class="requisition-bottom-actions-left">

            <a href="{{ route('admin.store-receipts.index') }}"
               class="requisition-cancel-button">

                <i class="fas fa-arrow-left"></i>

                <span>
                    Back to Receipts
                </span>

            </a>

        </div>


        <div class="requisition-bottom-actions-right">

            @if($storeReceipt->status === 'DRAFT')

                {{-- Edit --}}
                <a href="{{ route(
                    'admin.store-receipts.edit',
                    $storeReceipt
                ) }}"
                   class="requisition-add-button">

                    <i class="fas fa-edit"></i>

                    <span>
                        Edit Receipt
                    </span>

                </a>


                {{-- Post --}}
                @if($storeReceipt->items->count())

                    <form method="POST"
                          action="{{ route(
                              'admin.store-receipts.post',
                              $storeReceipt
                          ) }}"
                          class="d-inline"
                          onsubmit="return confirm(
                              'Post this receipt? This will increase the store stock and cannot be undone from this screen.'
                          );">

                        @csrf

                        <button type="submit"
                                class="requisition-add-button">

                            <i class="fas fa-check"></i>

                            <span>
                                Post Receipt
                            </span>

                        </button>

                    </form>

                @endif

            @endif

        </div>

    </div>

</div>


{{-- ================================================================
     RECEIPT DETAILS MODAL
     ================================================================= --}}

<div class="modal fade"
     id="receiptDetailsModal"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <div>

                    <h5 class="modal-title mb-1">

                        <i class="fas fa-info-circle me-2"></i>

                        Receipt Details

                    </h5>

                    <small class="text-muted">
                        Information about this store receipt.
                    </small>

                </div>


                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                </button>

            </div>


            <div class="modal-body">

                <div class="requisition-details-grid">


                    {{-- Receipt Number --}}

                    <div class="requisition-detail-item">

                        <span class="requisition-detail-label">
                            Receipt Number
                        </span>

                        <span class="requisition-detail-value">
                            {{ $storeReceipt->receipt_number }}
                        </span>

                    </div>


                    {{-- Store --}}

                    <div class="requisition-detail-item">

                        <span class="requisition-detail-label">
                            Store
                        </span>

                        <span class="requisition-detail-value">
                            {{ $storeReceipt->store->name ?? '—' }}
                        </span>

                    </div>


                    {{-- Source --}}

                    <div class="requisition-detail-item">

                        <span class="requisition-detail-label">
                            Source
                        </span>

                        <span class="requisition-detail-value">

                            @if($storeReceipt->source_type === 'PURCHASE')

                                <span class="requisition-status requisition-status-pending">
                                    PURCHASE
                                </span>

                            @else

                                <span class="requisition-status requisition-status-approved">
                                    DONATION
                                </span>

                            @endif

                        </span>

                    </div>


                    {{-- Status --}}

                    <div class="requisition-detail-item">

                        <span class="requisition-detail-label">
                            Status
                        </span>

                        <span class="requisition-detail-value">

                            @if($storeReceipt->status === 'DRAFT')

                                <span class="requisition-status requisition-status-draft">
                                    DRAFT
                                </span>

                            @elseif($storeReceipt->status === 'POSTED')

                                <span class="requisition-status requisition-status-approved">
                                    POSTED
                                </span>

                            @elseif($storeReceipt->status === 'CANCELLED')

                                <span class="requisition-status requisition-status-rejected">
                                    CANCELLED
                                </span>

                            @else

                                <span class="requisition-status requisition-status-draft">
                                    {{ $storeReceipt->status }}
                                </span>

                            @endif

                        </span>

                    </div>


                    {{-- Received Date --}}

                    <div class="requisition-detail-item">

                        <span class="requisition-detail-label">
                            Received Date
                        </span>

                        <span class="requisition-detail-value">

                            {{ optional(
                                $storeReceipt->received_date
                            )->format('d M Y') }}

                        </span>

                    </div>


                    {{-- Received By --}}

                    <div class="requisition-detail-item">

                        <span class="requisition-detail-label">
                            Received By
                        </span>

                        <span class="requisition-detail-value">
                            {{ $storeReceipt->receivedBy->name ?? '—' }}
                        </span>

                    </div>


                    {{-- Purchase Details --}}

                    @if($storeReceipt->source_type === 'PURCHASE')

                        <div class="requisition-detail-item">

                            <span class="requisition-detail-label">
                                Supplier
                            </span>

                            <span class="requisition-detail-value">
                                {{ $storeReceipt->supplier_name ?: '—' }}
                            </span>

                        </div>


                        <div class="requisition-detail-item">

                            <span class="requisition-detail-label">
                                Supplier Reference
                            </span>

                            <span class="requisition-detail-value">
                                {{ $storeReceipt->supplier_reference ?: '—' }}
                            </span>

                        </div>

                    @else

                        {{-- Donation --}}

                        <div class="requisition-detail-item">

                            <span class="requisition-detail-label">
                                Donation
                            </span>

                            <span class="requisition-detail-value">

                                @if($storeReceipt->donation)

                                    Donation #{{ $storeReceipt->donation->id }}

                                @else

                                    —

                                @endif

                            </span>

                        </div>

                    @endif

                </div>


                {{-- Notes --}}

                @if($storeReceipt->notes)

                    <div class="mt-3">

                        <span class="requisition-detail-label">
                            Notes
                        </span>

                        <div class="p-2 bg-light border rounded small">

                            {!! nl2br(e($storeReceipt->notes)) !!}

                        </div>

                    </div>

                @endif

            </div>


            <div class="modal-footer">

                <button type="button"
                        class="requisition-cancel-button"
                        data-bs-dismiss="modal">

                    <i class="fas fa-times"></i>

                    <span>
                        Close
                    </span>

                </button>

            </div>

        </div>

    </div>

</div>

@endsection