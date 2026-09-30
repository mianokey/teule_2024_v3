@extends('layouts.admin')

@push('styles') <link rel="stylesheet" href="{{ asset('assets/css/stores.css') }}">
@endpush

@section('content')

<div class="stores-page">


{{-- PAGE HEADER --}}
<div class="stores-header">

    <div class="stores-header-content">

        <div>
            <h5 class="stores-header-title">
                <i class="fas fa-boxes me-2"></i>
                Current Stock
            </h5>

            <p class="stores-header-subtitle">
                Monitor stock balances across all stores and view movement history.
            </p>
        </div>

    </div>

</div>


{{-- FILTERS --}}
<div class="stores-filter-card">

    <form method="GET"
          action="{{ route('admin.store-stock.index') }}"
          class="row g-3 align-items-end">

        <div class="col-md-3">

            <label class="stores-filter-label">
                Store
            </label>

            <select name="store_id"
                    class="form-select">

                <option value="">
                    All Stores
                </option>

                @foreach($stores as $store)

                    <option value="{{ $store->id }}"
                        {{ request('store_id') == $store->id ? 'selected' : '' }}>

                        {{ $store->name }}

                    </option>

                @endforeach

            </select>

        </div>


        <div class="col-md-3">

            <label class="stores-filter-label">
                Item
            </label>

            <select name="item_id"
                    class="form-select">

                <option value="">
                    All Items
                </option>

                @foreach($items as $item)

                    <option value="{{ $item->id }}"
                        {{ request('item_id') == $item->id ? 'selected' : '' }}>

                        {{ $item->name }}

                    </option>

                @endforeach

            </select>

        </div>


        <div class="col-md-4">

            <label class="stores-filter-label">
                Search
            </label>

            <div class="stores-search">

                <i class="fas fa-search stores-search-icon"></i>

                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       class="form-control"
                       placeholder="Search item name...">

            </div>

        </div>


        <div class="col-md-2 d-flex gap-2">

            <button type="submit"
                    class="btn stores-btn-primary flex-grow-1">

                <i class="fas fa-search me-1"></i>
                Filter

            </button>

            <a href="{{ route('admin.store-stock.index') }}"
               class="btn stores-btn-light">

                <i class="fas fa-redo"></i>

            </a>

        </div>

    </form>

</div>


{{-- STOCK TABLE --}}
<div class="stores-table-card">

    <div class="stores-table-wrapper">

        <table class="stores-table align-middle">

            <thead>

                <tr>

                    <th>Store</th>

                    <th>Item</th>

                    <th>Variant</th>

                    <th>Unit</th>

                    <th class="text-end">
                        Current Stock
                    </th>

                    <th class="text-end">
                        Reorder Level
                    </th>

                    <th>
                        Status
                    </th>

                    <th>
                        Last Movement
                    </th>

                    <th class="text-center">
                        Action
                    </th>

                </tr>

            </thead>


            <tbody>

                @forelse($stocks as $stock)

                    @php

                        $quantity = (float) $stock->quantity;

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

                        {{-- STORE --}}
                        <td>

                            <div class="stores-item-name">
                                {{ $stock->store->name }}
                            </div>

                        </td>


                        {{-- ITEM --}}
                        <td>

                            <div class="stores-item-name">
                                {{ $stock->item->name }}
                            </div>

                        </td>


                        {{-- VARIANT --}}
                        <td>

                            @if($stock->variant)

                                <span class="stores-variant">

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

                            <span class="fw-semibold">

                                {{ $stock->item->unit->code }}

                            </span>

                        </td>


                        {{-- CURRENT STOCK --}}
                        <td class="text-end">

                            <span class="
                                stock-balance
                                @if($status === 'out')
                                    stock-empty
                                @elseif($status === 'low')
                                    stock-low
                                @else
                                    stock-positive
                                @endif
                            ">

                                {{ number_format($quantity, 3) }}

                            </span>

                            <span class="stock-unit">

                                {{ $stock->item->unit->code }}

                            </span>

                        </td>


                        {{-- REORDER --}}
                        <td class="text-end">

                            {{ number_format($reorderLevel, 3) }}

                        </td>


                        {{-- STATUS --}}
                        <td>

                            @if($status === 'out')

                                <span class="stores-badge stores-badge-danger">

                                    <i class="fas fa-times-circle"></i>

                                    {{ $statusLabel }}

                                </span>

                            @elseif($status === 'low')

                                <span class="stores-badge stores-badge-warning">

                                    <i class="fas fa-exclamation-circle"></i>

                                    {{ $statusLabel }}

                                </span>

                            @else

                                <span class="stores-badge stores-badge-success">

                                    <i class="fas fa-check-circle"></i>

                                    {{ $statusLabel }}

                                </span>

                            @endif

                        </td>


                        {{-- LAST MOVEMENT --}}
                        <td>

                            @if($stock->last_movement_at)

                                <div class="stores-item-name">

                                    {{ $stock->last_movement_at->format('d M Y') }}

                                </div>

                                <div class="stores-muted">

                                    {{ $stock->last_movement_at->format('H\:i') }}

                                </div>

                            @else

                                <span class="text-muted">
                                    Never
                                </span>

                            @endif

                        </td>


                        {{-- ACTION --}}
                        <td class="text-center">

                            <button type="button"
                                    class="btn stores-btn-light"
                                    onclick="openLedger({{ $stock->id }})">

                                <i class="fas fa-history me-1"></i>

                                Ledger

                            </button>

                        </td>

                    </tr>


                @empty

                    <tr>

                        <td colspan="9">

                            <div class="stores-empty">

                                <div class="stores-empty-icon">

                                    <i class="fas fa-box-open"></i>

                                </div>

                                <div class="stores-empty-title">

                                    No stock records found

                                </div>

                                <p class="stores-empty-text">

                                    Try changing your filters.

                                </p>

                            </div>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- PAGINATION --}}
    @if($stocks->hasPages())

        <div class="stores-pagination">

            {{ $stocks->links() }}

        </div>

    @endif

</div>


</div>

{{-- =========================================================
LEDGER MODAL
========================================================= --}}

<div class="modal fade stores-modal"
     id="ledgerModal"
     tabindex="-1"
     aria-labelledby="ledgerModalLabel"
     aria-hidden="true">


<div class="modal-dialog modal-xl modal-dialog-scrollable">

    <div class="modal-content">


        {{-- MODAL HEADER --}}
        <div class="modal-header">

            <div>

                <h5 class="modal-title"
                    id="ledgerModalLabel">

                    <i class="fas fa-history me-2"></i>

                    Stock Ledger

                </h5>

                <div id="ledgerSummary"
                     class="small mt-1">

                </div>

            </div>


            <button type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close">
            </button>

        </div>


        {{-- MODAL BODY --}}
        <div class="modal-body">


            {{-- LOADING --}}
            <div id="ledgerLoading"
                 class="stores-loading">

                <div class="stores-spinner"></div>

                <div>
                    Loading stock movement history...
                </div>

            </div>


            {{-- ERROR --}}
            <div id="ledgerError"
                 class="alert alert-danger d-none">
            </div>


            {{-- CONTENT --}}
            <div id="ledgerContent"
                 class="d-none">

                <div class="stores-table-wrapper">

                    <table class="stores-table">

                        <thead>

                            <tr>

                                <th>Date</th>

                                <th>Movement</th>

                                <th class="text-end">
                                    Quantity
                                </th>

                                <th class="text-end">
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

            <button type="button"
                    class="btn stores-btn-light"
                    data-bs-dismiss="modal">

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

                '<div class="stores-empty">' +

                '<div class="stores-empty-icon">' +
                '<i class="fas fa-history"></i>' +
                '</div>' +

                '<div class="stores-empty-title">' +
                'No stock movements found' +
                '</div>' +

                '</div>' +

                '</td>' +
                '</tr>';

        } else {

            data.movements.forEach(function (movement) {

                const quantity =
                    Number(movement.quantity);


                let movementClass =
                    'stores-movement-neutral';

                let sign = '';


                if (
                    [
                        'RECEIPT',
                        'RETURN_IN',
                        'TRANSFER_IN'
                    ].includes(movement.type)
                ) {

                    movementClass =
                        'stores-movement-in';

                    sign = '+';

                } else if (
                    [
                        'ISSUE',
                        'RETURN_OUT',
                        'TRANSFER_OUT'
                    ].includes(movement.type)
                ) {

                    movementClass =
                        'stores-movement-out';

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

                    '<span class="stores-movement ' +
                    movementClass +
                    '">' +

                    escapeHtml(movement.type) +

                    '</span>' +

                    '</td>' +

                    '<td class="text-end">' +

                    sign +

                    Number(quantity).toFixed(3) +

                    '</td>' +

                    '<td class="text-end stores-ledger-balance">' +

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
