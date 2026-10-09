@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/stores.css') }}">
@endpush

@section('content')

<div class="store-requisition-page">

    {{-- =========================================================
         PAGE HEADER
    ========================================================== --}}
    <div class="requisition-page-header">

        <div class="requisition-header-content">

            <div class="requisition-header-icon">
                <i class="fa fa-boxes"></i>
            </div>

            <div>

                <div class="requisition-breadcrumb">
                    <span>Stores</span>
                    <span>/</span>
                    <span>Inventory</span>
                </div>

                <h1 class="requisition-page-title">
                    Current Stock
                </h1>

                <p class="requisition-page-subtitle">
                    Monitor stock balances across all stores and view movement history.
                </p>

            </div>

        </div>

    </div>


    {{-- =========================================================
         FILTERS
    ========================================================== --}}
    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fa fa-filter"></i>
                </div>

                <div>

                    <h5>
                        Stock Filters
                    </h5>

                    <p>
                        Filter stock balances by store, item or search term.
                    </p>

                </div>

            </div>

        </div>


        <div class="requisition-details-body">

            <form method="GET"
                  action="{{ route('admin.store-stock.index') }}">

                <div class="row g-3 align-items-end">

                    {{-- STORE --}}
                    <div class="col-lg-3">

                        <label class="requisition-field-label">
                            Store
                        </label>

                        <select name="store_id"
                                class="requisition-input">

                            <option value="">
                                All Stores
                            </option>

                            @foreach($stores as $store)

                                <option value="{{ $store->id }}"
                                    @selected(request('store_id') == $store->id)>

                                    {{ $store->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- ITEM --}}
                    <div class="col-lg-3">

                        <label class="requisition-field-label">
                            Item
                        </label>

                        <select name="item_id"
                                class="requisition-input">

                            <option value="">
                                All Items
                            </option>

                            @foreach($items as $item)

                                <option value="{{ $item->id }}"
                                    @selected(request('item_id') == $item->id)>

                                    {{ $item->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- SEARCH --}}
                    <div class="col-lg-4">

                        <label class="requisition-field-label">
                            Search
                        </label>

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            class="requisition-input"
                            placeholder="Search item name..."
                        >

                    </div>


                    {{-- FILTER BUTTONS --}}
                    <div class="col-lg-2">

                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                class="requisition-save-button"
                            >

                                <i class="fa fa-search"></i>
                                Filter

                            </button>

                            <a
                                href="{{ route('admin.store-stock.index') }}"
                                class="requisition-cancel-button"
                            >
                                Clear
                            </a>

                        </div>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- =========================================================
         STOCK LIST
    ========================================================== --}}
    <div class="requisition-section">

        {{-- SECTION HEADER --}}
        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fa fa-boxes"></i>
                </div>

                <div>

                    <h5>
                        Current Stock
                    </h5>

                    <p>
                        Current stock balances and reorder levels.
                    </p>

                </div>

            </div>


            <div class="requisition-count-badge">

                {{ $stocks->total() }}

                {{ $stocks->total() === 1 ? 'Stock Record' : 'Stock Records' }}

            </div>

        </div>


        {{-- =====================================================
             TABLE
        ====================================================== --}}
        <div class="requisition-list-table-wrapper">

            <table class="requisition-list-table">

                <thead>

                    <tr>

                        <th style="width: 55px;">
                            #
                        </th>

                        <th style="min-width: 150px;">
                            Store
                        </th>

                        <th style="min-width: 170px;">
                            Item
                        </th>

                        <th style="min-width: 120px;">
                            Variant
                        </th>

                        <th style="width: 90px;">
                            Unit
                        </th>

                        <th style="width: 125px;">
                            Current Stock
                        </th>

                        <th style="width: 125px;">
                            Reorder Level
                        </th>

                        <th style="width: 135px;">
                            Status
                        </th>

                        <th style="width: 145px;">
                            Last Movement
                        </th>

                        <th style="width: 100px;">
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($stocks as $stock)

                        @php

                            $quantity =
                                (float) $stock->quantity;

                            $reorderLevel =
                                (float) $stock->item->reorder_level;


                            if ($quantity <= 0) {

                                $status = 'out';
                                $statusLabel = 'Out of Stock';

                            } elseif (
                                $reorderLevel > 0 &&
                                $quantity <= $reorderLevel
                            ) {

                                $status = 'low';
                                $statusLabel = 'Low Stock';

                            } else {

                                $status = 'normal';
                                $statusLabel = 'In Stock';

                            }

                        @endphp


                        <tr>

                            {{-- NUMBER --}}
                            <td>

                                <span class="requisition-list-row-number">

                                    {{ $stocks->firstItem() + $loop->index }}

                                </span>

                            </td>


                            {{-- STORE --}}
                            <td>

                                <div class="requisition-list-number">

                                    {{ $stock->store->name }}

                                </div>

                            </td>


                            {{-- ITEM --}}
                            <td>

                                <div class="requisition-list-number">

                                    {{ $stock->item->name }}

                                </div>

                            </td>


                            {{-- VARIANT --}}
                            <td>

                                @if($stock->variant)

                                    <span class="requisition-list-department">

                                        {{ $stock->variant->name }}

                                    </span>

                                @else

                                    <span class="text-muted">
                                        —
                                    </span>

                                @endif

                            </td>


                            {{-- UNIT --}}
                            <td>

                                <span class="requisition-list-item-count">

                                    {{ $stock->item->unit->code }}

                                </span>

                            </td>


                            {{-- CURRENT STOCK --}}
                            <td>

                                <div class="requisition-list-item-count">

                                    {{ number_format($quantity, 3) }}

                                </div>

                                <div class="requisition-list-meta">

                                    {{ $stock->item->unit->code }}

                                </div>

                            </td>


                            {{-- REORDER LEVEL --}}
                            <td>

                                <div class="requisition-list-item-count">

                                    {{ number_format($reorderLevel, 3) }}

                                </div>

                                <div class="requisition-list-meta">

                                    {{ $stock->item->unit->code }}

                                </div>

                            </td>


                            {{-- STATUS --}}
                            <td>

                                <span
                                    class="
                                        requisition-list-status
                                        requisition-list-status-{{ $status }}
                                    "
                                >

                                    <span class="requisition-list-status-dot"></span>

                                    {{ $statusLabel }}

                                </span>

                            </td>


                            {{-- LAST MOVEMENT --}}
                            <td>

                                @if($stock->last_movement_at)

                                    <div class="requisition-list-date">

                                        {{ $stock->last_movement_at->format('d M Y') }}

                                    </div>

                                    <div class="requisition-list-time">

                                        {{ $stock->last_movement_at->format('H:i') }}

                                    </div>

                                @else

                                    <span class="text-muted">
                                        Never
                                    </span>

                                @endif

                            </td>


                            {{-- ACTION --}}
                            <td>

                                <div class="requisition-list-actions">

                                    <button
                                        type="button"
                                        class="requisition-list-action primary"
                                        onclick="openLedger({{ $stock->id }})"
                                        title="View stock ledger"
                                    >

                                        <i class="fa fa-history"></i>

                                    </button>

                                </div>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td colspan="10">

                                <div class="requisition-table-empty">

                                    <div class="requisition-empty-icon">

                                        <i class="fa fa-box-open"></i>

                                    </div>

                                    <h5>
                                        No stock records found
                                    </h5>

                                    <p>
                                        Try changing your filters.
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
        ====================================================== --}}
        @if($stocks->count() > 8)

            <div class="requisition-scroll-hint">

                <i class="fa fa-arrows-alt-v"></i>

                Scroll to view more stock records

            </div>

        @endif


    </div>

</div>


{{-- =========================================================
     LEDGER MODAL
========================================================== --}}
<div
    class="modal fade"
    id="ledgerModal"
    tabindex="-1"
    aria-labelledby="ledgerModalLabel"
    aria-hidden="true"
>

    <div class="modal-dialog modal-xl modal-dialog-scrollable">

        <div class="modal-content">

            {{-- MODAL HEADER --}}
            <div class="modal-header">

                <div>

                    <h5
                        class="modal-title"
                        id="ledgerModalLabel"
                    >

                        <i class="fa fa-history me-2"></i>

                        Stock Ledger

                    </h5>

                    <div
                        id="ledgerSummary"
                        class="small mt-1"
                    ></div>

                </div>


                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>

            </div>


            {{-- MODAL BODY --}}
            <div class="modal-body">

                {{-- LOADING --}}
                <div
                    id="ledgerLoading"
                    class="text-center py-5"
                >

                    <div class="spinner-border"
                         role="status">

                        <span class="visually-hidden">
                            Loading...
                        </span>

                    </div>

                    <div class="mt-3">
                        Loading stock movement history...
                    </div>

                </div>


                {{-- ERROR --}}
                <div
                    id="ledgerError"
                    class="alert alert-danger d-none"
                ></div>


                {{-- CONTENT --}}
                <div
                    id="ledgerContent"
                    class="d-none"
                >

                    <div class="requisition-list-table-wrapper">

                        <table class="requisition-list-table">

                            <thead>

                                <tr>

                                    <th>
                                        Date
                                    </th>

                                    <th>
                                        Movement
                                    </th>

                                    <th>
                                        Quantity
                                    </th>

                                    <th>
                                        Balance
                                    </th>

                                    <th>
                                        Reference
                                    </th>

                                    <th>
                                        Created By
                                    </th>

                                    <th>
                                        Notes
                                    </th>

                                </tr>

                            </thead>


                            <tbody id="ledgerRows">
                            </tbody>

                        </table>

                    </div>

                </div>

            </div>


            {{-- MODAL FOOTER --}}
            <div class="modal-footer">

                <button
                    type="button"
                    class="requisition-cancel-button"
                    data-bs-dismiss="modal"
                >
                    Close
                </button>

            </div>

        </div>

    </div>

</div>


<script>

function openLedger(stockId)
{
    const modalElement =
        document.getElementById('ledgerModal');

    const modal =
        new bootstrap.Modal(modalElement);

    const loading =
        document.getElementById('ledgerLoading');

    const error =
        document.getElementById('ledgerError');

    const content =
        document.getElementById('ledgerContent');

    const rows =
        document.getElementById('ledgerRows');

    const summary =
        document.getElementById('ledgerSummary');


    loading.classList.remove('d-none');

    error.classList.add('d-none');

    content.classList.add('d-none');

    rows.innerHTML = '';

    summary.innerHTML = '';


    modal.show();


    fetch(
        "{{ url('admin/store-stock') }}/" +
        stockId +
        "/ledger",
        {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        }
    )

    .then(response => {

        if (!response.ok) {

            throw new Error(
                'Unable to load the stock ledger.'
            );

        }

        return response.json();

    })

    .then(data => {

        loading.classList.add('d-none');


        const stock = data.stock;


        let variant = stock.variant
            ? ' / ' + stock.variant
            : '';


        summary.innerHTML =
            '<strong>' +
            escapeHtml(stock.store) +
            '</strong>' +
            ' &nbsp;•&nbsp; ' +
            escapeHtml(stock.item) +
            escapeHtml(variant) +
            ' &nbsp;•&nbsp; ' +
            'Current Stock: <strong>' +
            Number(stock.quantity).toFixed(3) +
            ' ' +
            escapeHtml(stock.unit) +
            '</strong>';


        if (!data.movements.length) {

            rows.innerHTML =

                '<tr>' +

                '<td colspan="7">' +

                '<div class="requisition-table-empty">' +

                '<div class="requisition-empty-icon">' +

                '<i class="fa fa-history"></i>' +

                '</div>' +

                '<h5>' +

                'No stock movements found' +

                '</h5>' +

                '<p>' +

                'There are no recorded movements for this stock item.' +

                '</p>' +

                '</div>' +

                '</td>' +

                '</tr>';

        } else {

            data.movements.forEach(function (movement) {

                const quantity =
                    Number(movement.quantity);


                let sign = '';


                if (
                    [
                        'RECEIPT',
                        'RETURN_IN',
                        'TRANSFER_IN'
                    ].includes(movement.type)
                ) {

                    sign = '+';

                } else if (
                    [
                        'ISSUE',
                        'RETURN_OUT',
                        'TRANSFER_OUT'
                    ].includes(movement.type)
                ) {

                    sign = '-';

                }


                let reference = '—';


                if (movement.reference_type) {

                    reference =
                        escapeHtml(
                            movement.reference_type
                        );


                    if (movement.reference_id) {

                        reference +=
                            ' #' +
                            movement.reference_id;

                    }

                }


                rows.innerHTML +=

                    '<tr>' +

                    '<td>' +
                    escapeHtml(
                        movement.date ?? '—'
                    ) +
                    '</td>' +

                    '<td>' +

                    '<span class="requisition-list-status">' +

                    '<span class="requisition-list-status-dot"></span>' +

                    escapeHtml(movement.type) +

                    '</span>' +

                    '</td>' +

                    '<td>' +
                    sign +
                    Number(quantity).toFixed(3) +
                    '</td>' +

                    '<td>' +
                    Number(
                        movement.balance_after
                    ).toFixed(3) +
                    '</td>' +

                    '<td>' +
                    reference +
                    '</td>' +

                    '<td>' +
                    escapeHtml(
                        movement.created_by ?? '—'
                    ) +
                    '</td>' +

                    '<td>' +
                    escapeHtml(
                        movement.notes ?? '—'
                    ) +
                    '</td>' +

                    '</tr>';

            });

        }


        content.classList.remove('d-none');

    })

    .catch(function (err) {

        loading.classList.add('d-none');

        error.textContent =
            err.message ||
            'Unable to load the stock ledger.';

        error.classList.remove('d-none');

    });
}


/**
 * Escape server-generated values before
 * inserting them into the DOM.
 */
function escapeHtml(value)
{
    return String(value)

        .replace(/&/g, '&amp;')

        .replace(/</g, '&lt;')

        .replace(/>/g, '&gt;')

        .replace(/"/g, '&quot;')

        .replace(/'/g, '&#039;');
}

</script>

@endsection

