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
                <i class="fa fa-chart-bar"></i>
            </div>

            <div>
                <div class="requisition-breadcrumb">
                    <span>Stores</span>
                    <span>/</span>
                    <span>Reports &amp; Insights</span>
                </div>

                <h1 class="requisition-page-title">
                    Stores Reports
                </h1>

                <p class="requisition-page-subtitle">
                    Analyse stock balances, review movements, and export reports.
                </p>
            </div>

        </div>

        <div class="requisition-header-right">

            <a
                href="{{ route('admin.store-stock.index') }}"
                class="requisition-cancel-button"
            >
                <i class="fa fa-arrow-left"></i>
                Current Stock
            </a>

        </div>

    </div>


    {{-- =========================================================
         REPORT FILTERS
         ========================================================= --}}
    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fa fa-sliders-h"></i>
                </div>

                <div>
                    <h5>
                        Report Configuration
                    </h5>

                    <p>
                        Select a report and apply filters to refine the results.
                    </p>
                </div>

            </div>

            <div class="requisition-count-badge">
                Filters
            </div>

        </div>


        <div class="requisition-details-body">

            <form
                method="GET"
                action="{{ route('admin.store-reports.index') }}"
            >

                <div class="row g-3 align-items-end">

                    {{-- REPORT TYPE --}}
                    <div class="col-lg-4 col-md-6">

                        <label for="report" class="requisition-field-label">
                            Report Type
                        </label>

                        <select
                            name="report"
                            id="report"
                            class="requisition-input"
                        >

                            <option
                                value="current-stock"
                                @selected($report === 'current-stock')
                            >
                                Current Stock Balances
                            </option>

                            <option
                                value="stock-movements"
                                @selected($report === 'stock-movements')
                            >
                                Stock Movement History
                            </option>

                        </select>

                    </div>


                    {{-- STORE --}}
                    <div class="col-lg-4 col-md-6">

                        <label for="store_id" class="requisition-field-label">
                            Store
                        </label>

                        <select
                            name="store_id"
                            id="store_id"
                            class="requisition-input"
                        >

                            <option value="">
                                All Stores
                            </option>

                            @foreach($stores as $store)

                                <option
                                    value="{{ $store->id }}"
                                    @selected(request('store_id') == $store->id)
                                >
                                    {{ $store->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>

                    




{{-- MULTI-ITEM FILTER --}}
<div class="col-lg-4 col-md-6">

    <label class="requisition-field-label" for="openReportItemPicker">
        Items
    </label>

    <button type="button"
            id="openReportItemPicker"
            class="requisition-lpo-picker requisition-input w-100"
            style="height: 36px;"
            title="All Items">

        <span class="requisition-lpo-picker-icon">
            <i class="fa fa-search"></i>
        </span>

        <span class="requisition-lpo-picker-text text-start">
            <strong id="reportItemPickerLabel">All Items</strong>
        </span>

        <span class="requisition-lpo-picker-arrow">
            <i class="fa fa-chevron-down"></i>
        </span>

    </button>

    {{-- Selected item names appear on hover --}}
    <div id="reportSelectedItemsTooltip"
         role="tooltip"
         style="display:none;">
    </div>

    {{-- Required by JavaScript --}}
    <div id="selectedReportItems" style="display:none;"></div>

    <div id="reportItemHiddenInputs">
        @foreach((array) request('item_ids', request('item_id') ? [request('item_id')] : []) as $selectedItemId)
            <input type="hidden"
                   name="item_ids[]"
                   value="{{ $selectedItemId }}">
        @endforeach
    </div>

</div>



                    {{-- VARIANT --}}
                    <div class="col-lg-4 col-md-6">

                        <label for="variant_id" class="requisition-field-label">
                            Item Variant
                        </label>

                        <select
                            name="variant_id"
                            id="variant_id"
                            class="requisition-input"
                        >

                            <option value="">
                                All Variants
                            </option>

                            @foreach($variants as $variant)

                                <option
                                    value="{{ $variant->id }}"
                                    @selected(request('variant_id') == $variant->id)
                                >
                                    {{ $variant->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- MOVEMENT TYPE --}}
                    <div
                        class="col-lg-4 col-md-6"
                        id="movement-type-field"
                    >

                        <label for="movement_type" class="requisition-field-label">
                            Movement Type
                        </label>

                        <select
                            name="movement_type"
                            id="movement_type"
                            class="requisition-input"
                        >

                            <option value="">
                                All Movement Types
                            </option>

                            @foreach([
                                'RECEIPT',
                                'ISSUE',
                                'RETURN_IN',
                                'RETURN_OUT',
                                'TRANSFER_IN',
                                'TRANSFER_OUT',
                                'ADJUSTMENT'
                            ] as $type)

                                <option
                                    value="{{ $type }}"
                                    @selected(request('movement_type') === $type)
                                >
                                    {{ str_replace('_', ' ', $type) }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- DATE FROM --}}
                    <div
                        class="col-lg-2 col-md-6"
                        id="date-from-field"
                    >

                        <label for="date_from" class="requisition-field-label">
                            Date From
                        </label>

                        <input
                            type="date"
                            name="date_from"
                            id="date_from"
                            class="requisition-input"
                            value="{{ request('date_from') }}"
                        >

                    </div>


                    {{-- DATE TO --}}
                    <div
                        class="col-lg-2 col-md-6"
                        id="date-to-field"
                    >

                        <label for="date_to" class="requisition-field-label">
                            Date To
                        </label>

                        <input
                            type="date"
                            name="date_to"
                            id="date_to"
                            class="requisition-input"
                            value="{{ request('date_to') }}"
                        >

                    </div>


                    {{-- FILTER ACTIONS --}}
                    <div class="col-12">

                        <div class="d-flex flex-wrap gap-2 mt-2">

                            <button
                                type="submit"
                                class="requisition-save-button"
                            >
                                <i class="fa fa-filter"></i>
                                Apply Filters
                            </button>

                            <a
                                href="{{ route('admin.store-reports.index', ['report' => $report]) }}"
                                class="requisition-cancel-button"
                            >
                                <i class="fa fa-undo"></i>
                                Reset Filters
                            </a>

                        </div>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- =========================================================
         REPORT RESULTS
         ========================================================= --}}
    <div class="requisition-section">

        {{-- SECTION HEADER --}}
        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fa fa-table"></i>
                </div>

                <div>

                    <h5>
                        {{ $report === 'current-stock'
                            ? 'Current Stock Balances'
                            : 'Stock Movement History'
                        }}
                    </h5>

                    <p>
                        {{ number_format($rows->count()) }}
                        {{ $rows->count() === 1 ? 'record' : 'records' }}

                        <span class="mx-1">·</span>

                        Generated {{ now()->format('d M Y, H:i') }}
                    </p>

                </div>

            </div>


            {{-- EXPORT ACTIONS --}}
            <div class="d-flex flex-wrap gap-2">

                @foreach([
                    'pdf' => 'PDF',
                    'xlsx' => 'Excel',
                    'csv' => 'CSV'
                ] as $format => $label)

                    <a
                        href="{{ route('admin.store-reports.export', array_merge(
                            ['format' => $format],
                            request()->except('page')
                        )) }}"
                        class="{{ $format === 'xlsx'
                            ? 'requisition-save-button'
                            : 'requisition-cancel-button'
                        }}"
                    >

                        @if($format === 'pdf')
                            <i class="fa fa-file-pdf"></i>
                        @elseif($format === 'xlsx')
                            <i class="fa fa-file-excel"></i>
                        @else
                            <i class="fa fa-file-csv"></i>
                        @endif

                        Export {{ $label }}

                    </a>

                @endforeach

            </div>

        </div>


        {{-- =====================================================
             RESULTS TABLE
             ===================================================== --}}
        <div class="requisition-list-table-wrapper">

            <table class="requisition-list-table">

                <thead>

                    <tr>

                        @if($report === 'current-stock')

                            <th style="min-width: 140px;">
                                Store
                            </th>

                            <th style="min-width: 180px;">
                                Item
                            </th>

                            <th style="min-width: 120px;">
                                Variant
                            </th>

                            <th style="min-width: 85px;">
                                Unit
                            </th>

                            <th style="min-width: 120px; text-align: right;">
                                Current Stock
                            </th>

                            <th style="min-width: 155px;">
                                Last Movement
                            </th>

                        @else

                            <th style="min-width: 150px;">
                                Date
                            </th>

                            <th style="min-width: 130px;">
                                Store
                            </th>

                            <th style="min-width: 165px;">
                                Item
                            </th>

                            <th style="min-width: 110px;">
                                Variant
                            </th>

                            <th style="min-width: 145px;">
                                Movement Type
                            </th>

                            <th style="min-width: 100px; text-align: right;">
                                Quantity
                            </th>

                            <th style="min-width: 110px; text-align: right;">
                                Balance After
                            </th>

                            <th style="min-width: 150px;">
                                Reference
                            </th>

                            <th style="min-width: 130px;">
                                Recorded By
                            </th>

                            <th style="min-width: 180px;">
                                Notes
                            </th>

                        @endif

                    </tr>

                </thead>


                <tbody>

                    @forelse($rows as $row)

                        <tr>

                            @foreach($row as $key => $value)

                                <td
                                    @if(in_array($key, [
                                        'Current Stock',
                                        'Quantity',
                                        'Balance After'
                                    ], true))
                                        style="text-align: right; font-variant-numeric: tabular-nums;"
                                    @endif
                                >
                                    {{ $value ?? '—' }}
                                </td>

                            @endforeach

                        </tr>

                    @empty

                        <tr>

                            <td colspan="{{ $report === 'current-stock' ? 6 : 10 }}">

                                <div class="requisition-table-empty">

                                    <div class="requisition-empty-icon">
                                        <i class="fa fa-chart-bar"></i>
                                    </div>

                                    <h5>
                                        No Matching Records
                                    </h5>

                                    <p>
                                        No data was found for the selected filters.
                                        Try adjusting your report criteria.
                                    </p>

                                    <a
                                        href="{{ route('admin.store-reports.index', ['report' => $report]) }}"
                                        class="requisition-cancel-button"
                                    >
                                        <i class="fa fa-undo"></i>
                                        Clear Filters
                                    </a>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


{{-- =============================================================
     REPORT FILTER BEHAVIOUR
     ============================================================= --}}

<script>

document.addEventListener('DOMContentLoaded', function () {



    const report = document.getElementById('report');

    const movementFields = [
        document.getElementById('movement-type-field'),
        document.getElementById('date-from-field'),
        document.getElementById('date-to-field')
    ];

    function updateReportFields() {
        const isMovementReport =
            report && report.value === 'stock-movements';

        movementFields.forEach(function (field) {
            if (field) {
                field.style.display =
                    isMovementReport ? '' : 'none';
            }
        });
    }

    if (report) {
        report.addEventListener('change', updateReportFields);
        updateReportFields();
    }

    const modalElement =
        document.getElementById('reportItemSelectionModal');

    if (!modalElement) return;

    const openButton =
        document.getElementById('openReportItemPicker');

    const searchInput =
        document.getElementById('reportItemSearch');

    const hiddenInputs =
        document.getElementById('reportItemHiddenInputs');

    const selectedContainer =
        document.getElementById('selectedReportItems');

    const pickerLabel =
        document.getElementById('reportItemPickerLabel');

    const selectedCount =
        document.getElementById('reportSelectedItemCount');

    const noResults =
        document.getElementById('reportItemNoResults');

    const modal = new bootstrap.Modal(modalElement);

    const selectedItems = new Map();

    const itemRows = Array.from(
        document.querySelectorAll('.report-item-option')
    );

    // Restore selected items from the request.
    hiddenInputs.querySelectorAll('input[name="item_ids[]"]')
        .forEach(function (input) {
            const id = String(input.value);
            const row = itemRows.find(function (itemRow) {
                return String(itemRow.dataset.itemId) === id;
            });

            if (row) {
                selectedItems.set(id, {
                    id: id,
                    name: row.dataset.itemName
                });
            }
        });

    function escapeHtml(value) {
        const element = document.createElement('div');
        element.textContent = value || '';
        return element.innerHTML;
    }

function renderSelectedItems() {
    hiddenInputs.innerHTML = '';
    selectedContainer.innerHTML = '';

    const selectedNames = [];

    selectedItems.forEach(function (item) {
        const hidden = document.createElement('input');

        hidden.type = 'hidden';
        hidden.name = 'item_ids[]';
        hidden.value = item.id;

        hiddenInputs.appendChild(hidden);
        selectedNames.push(item.name);
    });

    if (selectedItems.size === 0) {
        pickerLabel.textContent = 'All Items';
    } else {
        pickerLabel.textContent =
            selectedItems.size + ' ' +
            (selectedItems.size === 1 ? 'item selected' : 'items selected');
    }

    const tooltip = document.getElementById('reportSelectedItemsTooltip');

    tooltip.textContent = selectedNames.length
        ? selectedNames.join(', ')
        : '';

    tooltip.style.display = 'none';

    selectedCount.textContent = selectedItems.size;

    itemRows.forEach(function (row) {
        const checkbox = row.querySelector('.report-item-checkbox');

        if (checkbox) {
            checkbox.checked = selectedItems.has(String(checkbox.value));
        }
    });
}


    function filterItems() {
        const query = (searchInput.value || '').trim().toLowerCase();
        let visible = 0;

        itemRows.forEach(function (row) {
            const matches =
                (row.dataset.search || '').includes(query);

            row.style.display = matches ? '' : 'none';

            if (matches) visible++;
        });

        noResults.style.display = visible ? 'none' : 'block';
    }

    openButton.addEventListener('click', function () {
        filterItems();
        modal.show();

        setTimeout(function () {
            searchInput.focus();
        }, 150);
    });

    searchInput.addEventListener('input', filterItems);

    itemRows.forEach(function (row) {
        const checkbox = row.querySelector('.report-item-checkbox');

        checkbox.addEventListener('change', function () {
            const id = String(this.value);

            if (this.checked) {
                selectedItems.set(id, {
                    id: id,
                    name: row.dataset.itemName
                });
            } else {
                selectedItems.delete(id);
            }

            selectedCount.textContent = selectedItems.size;
        });

        row.addEventListener('click', function (event) {
            if (event.target.closest('input')) return;

            checkbox.checked = !checkbox.checked;
            checkbox.dispatchEvent(new Event('change'));
        });
    });

    document.getElementById('applyReportItemSelection')
        .addEventListener('click', function () {
            renderSelectedItems();
            modal.hide();
        });

    document.getElementById('clearReportItemSelection')
        .addEventListener('click', function () {
            selectedItems.clear();

            itemRows.forEach(function (row) {
                row.querySelector('.report-item-checkbox').checked = false;
            });

            renderSelectedItems();
        });

    selectedContainer.addEventListener('click', function (event) {
        const button = event.target.closest('.remove-report-item');

        if (!button) return;

        selectedItems.delete(String(button.dataset.itemId));
        renderSelectedItems();
    });

    renderSelectedItems();
    filterItems();

});

</script>


{{-- REPORT ITEM SELECTION MODAL --}}
<div class="modal fade"
     id="reportItemSelectionModal"
     tabindex="-1"
     aria-labelledby="reportItemSelectionModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content border-0 shadow">

            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-1"
                        id="reportItemSelectionModalLabel">
                        Select Report Items
                    </h5>

                    <div class="requisition-modal-subtitle">
                        Search and tick all the items you want to include.
                    </div>
                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">x</button>
            </div>

            <div class="modal-body">

                <div class="requisition-child-search mb-3">
                    <i class="fa fa-search"></i>

                    <input type="text"
                           id="reportItemSearch"
                           class="form-control requisition-input"
                           placeholder="Search items by name or code..."
                           autocomplete="off">
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-muted small">
                        <span id="reportSelectedItemCount">0</span> selected
                    </span>

                    <button type="button"
                            id="clearReportItemSelection"
                            class="requisition-cancel-button">
                        Clear Selection
                    </button>
                </div>

                <div class="requisition-list-table-wrapper">
                    <table class="requisition-list-table">

                        <thead>
                            <tr>
                                <th style="width:55px;">Select</th>
                                <th>Item</th>
                                <th>Code</th>
                                <th>Unit</th>
                            </tr>
                        </thead>

                        <tbody id="reportItemSelectionBody">

                            @forelse($items as $item)

                                @php
                                    $unitName = $item->unit->code
                                        ?? $item->unit->name
                                        ?? '';
                                @endphp

                                <tr class="report-item-option"
                                    data-item-id="{{ $item->id }}"
                                    data-item-name="{{ $item->name }}"
                                    data-search="{{ strtolower($item->name . ' ' . ($item->sku ?? '') . ' ' . $unitName) }}">

                                    <td>
                                        <input type="checkbox"
                                               class="report-item-checkbox"
                                               value="{{ $item->id }}"
                                               aria-label="Select {{ $item->name }}">
                                    </td>

                                    <td>
                                        <strong>{{ $item->name }}</strong>
                                    </td>

                                    <td>{{ $item->sku ?? '—' }}</td>

                                    <td>{{ $unitName ?: '—' }}</td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        No items available.
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>
                </div>

                <div id="reportItemNoResults"
                     class="requisition-no-children-found"
                     style="display:none;">
                    No items match your search.
                </div>

            </div>

            <div class="modal-footer">

                <button type="button"
                        class="requisition-cancel-button"
                        data-bs-dismiss="modal">
                    Cancel
                </button>

                <button type="button"
                        id="applyReportItemSelection"
                        class="requisition-save-button">
                    <i class="fa fa-check"></i>
                    Apply Selection
                </button>

            </div>

        </div>

    </div>

</div>



@endsection