@extends('layouts.admin')

@push('styles') <link rel="stylesheet" href="{{ asset('assets/css/stores.css') }}">
@endpush

@section('content')

<div class="store-requisition-page">


{{-- =========================================================
     PAGE HEADER
     ========================================================= --}}
<div class="requisition-page-header">

    <div class="requisition-header-content">

        <div class="requisition-header-icon">
            <i class="fa fa-sliders"></i>
        </div>

        <div>

            <div class="requisition-breadcrumb">
                <span>Stores</span>
                <span>/</span>
                <span>Stock Adjustment</span>
            </div>

            <h1 class="requisition-page-title">
                Adjust Stock
            </h1>

            <p class="requisition-page-subtitle">
                Adjust stock balances and record the reason for the variation.
            </p>

        </div>

    </div>

</div>

<x-message></x-message>


{{-- =========================================================
     MAIN FORM
     ========================================================= --}}
<form
    method="POST"
    action="{{ route('admin.store-stock-adjustments.store') }}"
    id="stockAdjustmentForm"
>

    @csrf


    {{-- =====================================================
         ITEM / STORE SELECTION
         ===================================================== --}}
    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fa fa-database"></i>
                </div>

                <div>

                    <h5>
                        Stock Selection
                    </h5>

                    <p>
                        Select the store, item and optional item variation.
                    </p>

                </div>

            </div>

        </div>


        <div class="requisition-details-body">

            <div class="row g-3">

                {{-- =================================================
                     STORE
                     ================================================= --}}
                <div class="col-lg-4 col-md-6">

                    <label class="requisition-field-label">
                        Store
                        <span class="required-mark">*</span>
                    </label>

                    <button
                        type="button"
                        id="storePicker"
                        class="requisition-lpo-picker w-100"
                    >

                        <span class="requisition-lpo-picker-icon">
                            <i class="fas fa-store"></i>
                        </span>

                        <span
                            id="storePickerText"
                            class="requisition-lpo-picker-text"
                        >
                            <span class="text-muted">
                                Select Store
                            </span>
                        </span>

                        <span class="requisition-lpo-picker-arrow">
                            <i class="fas fa-chevron-right"></i>
                        </span>

                    </button>


                    <input
                        type="hidden"
                        name="store_id"
                        id="store_id"
                        value="{{ old('store_id') }}"
                    >


                    @error('store_id')
                        <div class="text-danger mt-1">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- =================================================
                     STORE ITEM
                     ================================================= --}}
                <div class="col-lg-4 col-md-6">

                    <label class="requisition-field-label">
                        Store Item
                        <span class="required-mark">*</span>
                    </label>

                    <button
                        type="button"
                        id="storeItemPicker"
                        class="requisition-lpo-picker w-100"
                    >

                        <span class="requisition-lpo-picker-icon">
                            <i class="fas fa-box-open"></i>
                        </span>

                        <span
                            id="storeItemPickerText"
                            class="requisition-lpo-picker-text"
                        >
                            <span class="text-muted">
                                Select Item
                            </span>
                        </span>

                        <span class="requisition-lpo-picker-arrow">
                            <i class="fas fa-chevron-right"></i>
                        </span>

                    </button>


                    <input
                        type="hidden"
                        name="store_item_id"
                        id="store_item_id"
                        value="{{ old('store_item_id') }}"
                    >


                    @error('store_item_id')
                        <div class="text-danger mt-1">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- =================================================
                     VARIANT
                     ================================================= --}}
                <div class="col-lg-4 col-md-6">

                    <label
                        for="variant_id"
                        class="requisition-field-label"
                    >
                        Item Variation
                    </label>

                    <select
                        name="variant_id"
                        id="variant_id"
                        class="form-select requisition-input"
                        disabled
                    >

                        <option value="">
                            No variation
                        </option>

                    </select>


                    @error('variant_id')
                        <div class="text-danger mt-1">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>


            {{-- =====================================================
                 SELECTED STOCK DETAILS
                 ===================================================== --}}
            <div
                id="selectedStockDetails"
                class="mt-4"
                style="display: none;"
            >

                <div class="row">

                    <div class="col-lg-4 col-md-6 mb-3">

                        <label class="requisition-field-label">
                            Selected Item
                        </label>

                        <div
                            id="selectedItemDisplay"
                            class="requisition-input"
                        >
                            —
                        </div>

                    </div>


                    <div class="col-lg-4 col-md-6 mb-3">

                        <label class="requisition-field-label">
                            Selected Store
                        </label>

                        <div
                            id="selectedStoreDisplay"
                            class="requisition-input"
                        >
                            —
                        </div>

                    </div>


                    <div class="col-lg-4 col-md-6 mb-3">

                        <label class="requisition-field-label">
                            Current Stock
                        </label>

                        <div
                            id="currentStockDisplay"
                            class="requisition-input"
                        >
                            Select store and item
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         STOCK VARIATION
         ========================================================= --}}
    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fa fa-exchange-alt"></i>
                </div>

                <div>

                    <h5>
                        Stock Variation
                    </h5>

                    <p>
                        Record the stock adjustment and explain why it is required.
                    </p>

                </div>

            </div>

        </div>


        <div class="requisition-details-body">

            <div class="row">

                {{-- =================================================
                     CURRENT STOCK
                     ================================================= --}}
                <div class="col-lg-4 col-md-6 mb-3">

                    <label class="requisition-field-label">
                        Current Stock
                    </label>

                    <div
                        class="requisition-input"
                        id="currentStockFormDisplay"
                    >
                        Select store and item
                    </div>

                </div>


                {{-- =================================================
                     VARIATION
                     ================================================= --}}
                <div class="col-lg-4 col-md-6 mb-3">

                    <label
                        for="variation"
                        class="requisition-field-label"
                    >
                        Variation (use + to add or - to subtract)

                        <span class="required-mark">
                            *
                        </span>

                    </label>

                    <input
                        type="number"
                        step="0.001"
                        name="variation"
                        id="variation"
                        value="{{ old('variation') }}"
                        class="form-control form-control-sm requisition-input @error('variation') is-invalid @enderror"
                        placeholder="e.g. -3 or 5"
                        required
                    >


                    @error('variation')
                        <div class="text-danger mt-1">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- =================================================
                     NEW STOCK
                     ================================================= --}}
                <div class="col-lg-4 col-md-6 mb-3">

                    <label class="requisition-field-label">
                        New Stock
                    </label>

                    <div
                        class="requisition-input"
                        id="newStockDisplay"
                    >
                        —
                    </div>

                </div>

            </div>


            <div class="stores-divider"></div>


            <div class="row">

                {{-- =================================================
                     REASON
                     ================================================= --}}
                <div class="col-lg-6 col-md-12 mb-3">

                    <label
                        for="reason"
                        class="requisition-field-label"
                    >

                        Reason

                        <span class="required-mark">
                            *
                        </span>

                    </label>


                    <select
                        name="reason"
                        id="reason"
                        class="form-select requisition-input @error('reason') is-invalid @enderror"
                        required
                    >

                        <option value="">
                            Select reason
                        </option>

                        <option
                            value="Damaged / Spoiled"
                            {{ old('reason') === 'Damaged / Spoiled' ? 'selected' : '' }}
                        >
                            Damaged / Spoiled
                        </option>

                        <option
                            value="Physical Stock Count Difference"
                            {{ old('reason') === 'Physical Stock Count Difference' ? 'selected' : '' }}
                        >
                            Physical Stock Count Difference
                        </option>

                        <option
                            value="Lost / Missing"
                            {{ old('reason') === 'Lost / Missing' ? 'selected' : '' }}
                        >
                            Lost / Missing
                        </option>

                        <option
                            value="Expired"
                            {{ old('reason') === 'Expired' ? 'selected' : '' }}
                        >
                            Expired
                        </option>

                        <option
                            value="Data Entry Correction"
                            {{ old('reason') === 'Data Entry Correction' ? 'selected' : '' }}
                        >
                            Data Entry Correction
                        </option>

                        <option
                            value="Stock Found"
                            {{ old('reason') === 'Stock Found' ? 'selected' : '' }}
                        >
                            Stock Found
                        </option>

                        <option
                            value="Other"
                            {{ old('reason') === 'Other' ? 'selected' : '' }}
                        >
                            Other
                        </option>

                    </select>


                    @error('reason')
                        <div class="text-danger mt-1">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- =================================================
                     EXPLANATION
                     ================================================= --}}
                <div class="col-lg-6 col-md-12 mb-3">

                    <label
                        for="notes"
                        class="requisition-field-label"
                    >

                        Explanation

                        <span class="required-mark">
                            *
                        </span>

                    </label>


                    {{-- KEEPING EXPLANATION AS INPUT --}}
                    <input
                        type="text"
                        name="notes"
                        id="notes"
                        value="{{ old('notes') }}"
                        class="form-control form-control-sm requisition-input @error('notes') is-invalid @enderror"
                        placeholder="Explain why the stock is being adjusted..."
                        required
                    >


                    @error('notes')
                        <div class="text-danger mt-1">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
         ACTIONS
         ========================================================= --}}
    <div class="requisition-bottom-actions">

        <a
            href="{{ route('admin.store-items.index') }}"
            class="requisition-cancel-button"
        >
            <i class="fa fa-arrow-left"></i>
            Cancel
        </a>


        <button
            type="submit"
            class="requisition-save-button"
            id="applyAdjustmentButton"
            disabled
            onclick="return confirm(
                'Are you sure you want to apply this stock adjustment?'
            );"
        >

            <i class="fa fa-check"></i>

            Apply Stock Adjustment

        </button>

    </div>

</form>


</div>

{{-- =============================================================
STORE SELECTION MODAL
============================================================= --}}

<div
    class="modal fade"
    id="storeSelectionModal"
    tabindex="-1"
    aria-labelledby="storeSelectionModalLabel"
    aria-hidden="true"
>


<div class="modal-dialog modal-dialog-centered modal-lg">

    <div class="modal-content border-0 shadow">

        <div class="modal-header">

            <div>

                <h5
                    class="modal-title mb-1"
                    id="storeSelectionModalLabel"
                >
                    Select Store
                </h5>

                <div class="requisition-modal-subtitle">
                    Double-click a store row to select it.
                </div>

            </div>


            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"
            ></button>

        </div>


        <div class="modal-body">

            <div class="requisition-child-search mb-3">

                <i class="fa fa-search"></i>

                <input
                    type="text"
                    id="storeSearch"
                    class="form-control requisition-input"
                    placeholder="Search stores..."
                    autocomplete="off"
                >

            </div>


            <div class="requisition-list-table-wrapper">

                <table class="requisition-list-table">

                    <thead>

                        <tr>

                            <th>
                                Store
                            </th>

                            <th>
                                Code
                            </th>

                            <th>
                                Description
                            </th>

                        </tr>

                    </thead>


                    <tbody id="storeSelectionBody">

                        @forelse($stores as $store)

                            <tr
                                class="store-option"
                                tabindex="0"
                                title="Double-click to select this store"
                                data-store-id="{{ $store->id }}"
                                data-store-name="{{ $store->name }}"
                                data-store-code="{{ $store->code ?? '' }}"
                                data-search="{{ strtolower(
                                    $store->name
                                    . ' '
                                    . ($store->code ?? '')
                                    . ' '
                                    . ($store->description ?? '')
                                ) }}"
                            >

                                <td>

                                    <strong>
                                        {{ $store->name }}
                                    </strong>

                                </td>

                                <td>
                                    {{ $store->code ?: '—' }}
                                </td>

                                <td>
                                    {{ $store->description ?: '—' }}
                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="3"
                                    class="text-center py-4"
                                >

                                    <span class="text-muted">
                                        No active stores are available.
                                    </span>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            <div
                id="storeNoResults"
                class="requisition-no-children-found"
                style="display: none;"
            >
                No stores match your search.
            </div>

        </div>


        <div class="modal-footer">

            <button
                type="button"
                class="requisition-cancel-button"
                data-bs-dismiss="modal"
            >
                Cancel
            </button>

        </div>

    </div>

</div>


</div>

{{-- =============================================================
STORE ITEM SELECTION MODAL
============================================================= --}}

<div
    class="modal fade"
    id="storeItemSelectionModal"
    tabindex="-1"
    aria-labelledby="storeItemSelectionModalLabel"
    aria-hidden="true"
>


<div class="modal-dialog modal-dialog-centered modal-lg">

    <div class="modal-content border-0 shadow">

        <div class="modal-header">

            <div>

                <h5
                    class="modal-title mb-1"
                    id="storeItemSelectionModalLabel"
                >
                    Select Item
                </h5>

                <div class="requisition-modal-subtitle">
                    Double-click an item row to select it.
                </div>

            </div>


            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"
            ></button>

        </div>


        <div class="modal-body">

            <div class="requisition-child-search mb-3">

                <i class="fa fa-search"></i>

                <input
                    type="text"
                    id="storeItemSearch"
                    class="form-control requisition-input"
                    placeholder="Search items..."
                    autocomplete="off"
                >

            </div>


            <div class="requisition-list-table-wrapper">

                <table class="requisition-list-table">

                    <thead>

                        <tr>

                            <th>
                                Item
                            </th>

                            <th>
                                Code
                            </th>

                            <th>
                                Unit
                            </th>

                            <th>
                                Variants
                            </th>

                        </tr>

                    </thead>


                    <tbody id="storeItemSelectionBody">

                        @forelse($storeItems as $storeItem)

                            @php

                                $unitName =
                                    $storeItem->unit->code
                                    ?? $storeItem->unit->name
                                    ?? '';

                            @endphp


                            <tr
                                class="store-item-option"
                                tabindex="0"
                                title="Double-click to select this item"
                                data-store-item-id="{{ $storeItem->id }}"
                                data-store-item-name="{{ $storeItem->name }}"
                                data-store-item-unit="{{ $unitName }}"
                                data-search="{{ strtolower(
                                    $storeItem->name
                                    . ' '
                                    . ($storeItem->sku ?? '')
                                    . ' '
                                    . $unitName
                                ) }}"
                            >

                                <td>

                                    <div class="stores-item-name">
                                        {{ $storeItem->name }}
                                    </div>

                                </td>


                                <td>

                                    @if($storeItem->sku)

                                        <div class="stores-code">
                                            {{ $storeItem->sku }}
                                        </div>

                                    @else

                                        <span class="text-muted">
                                            —
                                        </span>

                                    @endif

                                </td>


                                <td>
                                    {{ $unitName ?: '—' }}
                                </td>


                                <td>

                                    @if($storeItem->variants->count())

                                        <span class="stores-variant">

                                            {{ $storeItem->variants->count() }}

                                            available

                                        </span>

                                    @else

                                        <span class="text-muted">
                                            None
                                        </span>

                                    @endif

                                </td>

                            </tr>

                        @empty

                            <tr>

                                <td
                                    colspan="4"
                                    class="text-center py-4"
                                >

                                    <span class="text-muted">
                                        No active items are available.
                                    </span>

                                </td>

                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>


            <div
                id="storeItemNoResults"
                class="requisition-no-children-found"
                style="display: none;"
            >
                No items match your search.
            </div>

        </div>


        <div class="modal-footer">

            <button
                type="button"
                class="requisition-cancel-button"
                data-bs-dismiss="modal"
            >
                Cancel
            </button>

        </div>

    </div>

</div>


</div>

@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {


    /*
    |--------------------------------------------------------------------------
    | BOOTSTRAP MODALS
    |--------------------------------------------------------------------------
    */

    const storeModalElement =
        document.getElementById('storeSelectionModal');


    const storeModal =
        storeModalElement
            ? new bootstrap.Modal(storeModalElement)
            : null;


    const itemModalElement =
        document.getElementById('storeItemSelectionModal');


    const itemModal =
        itemModalElement
            ? new bootstrap.Modal(itemModalElement)
            : null;


    /*
    |--------------------------------------------------------------------------
    | MAIN ELEMENTS
    |--------------------------------------------------------------------------
    */

    const storePicker =
        document.getElementById('storePicker');


    const storeItemPicker =
        document.getElementById('storeItemPicker');


    const storeIdInput =
        document.getElementById('store_id');


    const storeItemIdInput =
        document.getElementById('store_item_id');


    const storePickerText =
        document.getElementById('storePickerText');


    const storeItemPickerText =
        document.getElementById('storeItemPickerText');


    const variantSelect =
        document.getElementById('variant_id');


    const variationInput =
        document.getElementById('variation');


    const currentStockDisplay =
        document.getElementById('currentStockDisplay');


    const currentStockFormDisplay =
        document.getElementById('currentStockFormDisplay');


    const newStockDisplay =
        document.getElementById('newStockDisplay');


    const selectedStockDetails =
        document.getElementById('selectedStockDetails');


    const selectedItemDisplay =
        document.getElementById('selectedItemDisplay');


    const selectedStoreDisplay =
        document.getElementById('selectedStoreDisplay');


    const applyAdjustmentButton =
        document.getElementById('applyAdjustmentButton');


    /*
    |--------------------------------------------------------------------------
    | VARIANT DATA
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    | The opening quote around the item ID is required.
    |
    */

    const variantData = {

        @foreach($storeItems as $storeItem)

            "{{ $storeItem->id }}": [

                @foreach($storeItem->variants as $variant)

                    {
                        id: {{ $variant->id }},
                        name: @json($variant->name),
                        code: @json($variant->code)
                    },

                @endforeach

            ],

        @endforeach

    };


    /*
    |--------------------------------------------------------------------------
    | STOCK DATA
    |--------------------------------------------------------------------------
    */

    const stockData = [

        @foreach($storeItems as $storeItem)

            @foreach($storeItem->stocks as $stock)

                {
                    storeId: {{ $stock->store_id }},

                    itemId: {{ $stock->store_item_id }},

                    variantId:
                        {{ $stock->variant_id !== null
                            ? $stock->variant_id
                            : 'null'
                        }},

                    quantity:
                        {{ (float) $stock->quantity }}
                },

            @endforeach

        @endforeach

    ];


    /*
    |--------------------------------------------------------------------------
    | ESCAPE HTML
    |--------------------------------------------------------------------------
    */

    function escapeHtml(value) {

        const element =
            document.createElement('div');


        element.textContent =
            value ?? '';


        return element.innerHTML;

    }


    /*
    |--------------------------------------------------------------------------
    | OPEN STORE MODAL
    |--------------------------------------------------------------------------
    */

    if (storePicker) {

        storePicker.addEventListener(
            'click',
            function () {

                const search =
                    document.getElementById(
                        'storeSearch'
                    );


                if (search) {
                    search.value = '';
                }


                filterStores('');


                if (storeModal) {
                    storeModal.show();
                }

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | OPEN ITEM MODAL
    |--------------------------------------------------------------------------
    */

    if (storeItemPicker) {

        storeItemPicker.addEventListener(
            'click',
            function () {

                const search =
                    document.getElementById(
                        'storeItemSearch'
                    );


                if (search) {
                    search.value = '';
                }


                filterStoreItems('');


                if (itemModal) {
                    itemModal.show();
                }

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | SELECT STORE
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'dblclick',
        function (event) {

            const row =
                event.target.closest(
                    '.store-option'
                );


            if (!row) {
                return;
            }


            const storeId =
                row.dataset.storeId || '';


            const storeName =
                row.dataset.storeName || '';


            const storeCode =
                row.dataset.storeCode || '';


            if (!storeId) {
                return;
            }


            storeIdInput.value =
                storeId;


            storePickerText.innerHTML = `

                <strong>
                    ${escapeHtml(storeName)}
                </strong>

                ${
                    storeCode
                        ? `<small>${escapeHtml(storeCode)}</small>`
                        : ''
                }

            `;


            selectedStoreDisplay.textContent =
                storeName
                +
                (
                    storeCode
                        ? ' (' + storeCode + ')'
                        : ''
                );


            updateStockDisplay();


            if (storeModal) {
                storeModal.hide();
            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | SELECT STORE ITEM
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'dblclick',
        function (event) {

            const row =
                event.target.closest(
                    '.store-item-option'
                );


            if (!row) {
                return;
            }


            const itemId =
                row.dataset.storeItemId || '';


            const itemName =
                row.dataset.storeItemName || '';


            const itemUnit =
                row.dataset.storeItemUnit || '';


            if (!itemId) {
                return;
            }


            storeItemIdInput.value =
                itemId;


            storeItemPickerText.innerHTML = `

                <strong>
                    ${escapeHtml(itemName)}
                </strong>

                ${
                    itemUnit
                        ? `<small>${escapeHtml(itemUnit)}</small>`
                        : ''
                }

            `;


            selectedItemDisplay.textContent =
                itemName;


            populateVariants(itemId);


            updateStockDisplay();


            if (itemModal) {
                itemModal.hide();
            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | POPULATE VARIANTS
    |--------------------------------------------------------------------------
    */

    function populateVariants(itemId) {

        if (!variantSelect) {
            return;
        }


        variantSelect.innerHTML = `

            <option value="">
                No variation
            </option>

        `;


        const variants =
            variantData[itemId] || [];


        variants.forEach(
            function (variant) {

                const option =
                    document.createElement(
                        'option'
                    );


                option.value =
                    variant.id;


                option.textContent =
                    variant.name
                    +
                    (
                        variant.code
                            ? ' (' + variant.code + ')'
                            : ''
                    );


                variantSelect.appendChild(
                    option
                );

            }
        );


        variantSelect.disabled =
            variants.length === 0;


        updateStockDisplay();

    }


    /*
    |--------------------------------------------------------------------------
    | VARIANT CHANGED
    |--------------------------------------------------------------------------
    */

    if (variantSelect) {

        variantSelect.addEventListener(
            'change',
            function () {

                updateStockDisplay();

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | FIND STOCK
    |--------------------------------------------------------------------------
    */

    function findStock() {

        const storeId =
            parseInt(
                storeIdInput.value || 0
            );


        const itemId =
            parseInt(
                storeItemIdInput.value || 0
            );


        if (!storeId || !itemId) {
            return null;
        }


        let variantId = null;


        if (
            variantSelect
            &&
            !variantSelect.disabled
            &&
            variantSelect.value
        ) {

            variantId =
                parseInt(
                    variantSelect.value
                );

        }


        return stockData.find(
            function (stock) {

                return (

                    parseInt(stock.storeId)
                    ===
                    storeId

                    &&

                    parseInt(stock.itemId)
                    ===
                    itemId

                    &&

                    stock.variantId
                    ===
                    variantId

                );

            }
        ) || null;

    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE STOCK DISPLAY
    |--------------------------------------------------------------------------
    */

    function updateStockDisplay() {

        const stock =
            findStock();


        if (
            !storeIdInput.value
            ||
            !storeItemIdInput.value
        ) {

            selectedStockDetails.style.display =
                'none';


            currentStockDisplay.textContent =
                'Select store and item';


            currentStockFormDisplay.textContent =
                'Select store and item';


            newStockDisplay.textContent =
                '—';


            applyAdjustmentButton.disabled =
                true;


            return;

        }


        selectedStockDetails.style.display =
            'block';


        if (!stock) {

            currentStockDisplay.textContent =
                'No stock record';


            currentStockFormDisplay.textContent =
                'No stock record';


            newStockDisplay.textContent =
                '—';


            applyAdjustmentButton.disabled =
                true;


            return;

        }


        const quantity =
            parseFloat(
                stock.quantity
            ) || 0;


        currentStockDisplay.textContent =
            quantity.toFixed(3);


        currentStockFormDisplay.textContent =
            quantity.toFixed(3);


        calculateNewStock();


        applyAdjustmentButton.disabled =
            false;

    }


    /*
    |--------------------------------------------------------------------------
    | CALCULATE NEW STOCK
    |--------------------------------------------------------------------------
    */

    function calculateNewStock() {

        const stock =
            findStock();


        if (!stock) {

            newStockDisplay.textContent =
                '—';

            return;

        }


        const currentStock =
            parseFloat(
                stock.quantity
            ) || 0;


        const variation =
            parseFloat(
                variationInput.value
            ) || 0;


        const newStock =
            currentStock
            +
            variation;


        newStockDisplay.textContent =
            newStock.toFixed(3);

    }


    /*
    |--------------------------------------------------------------------------
    | VARIATION CHANGED
    |--------------------------------------------------------------------------
    */

    if (variationInput) {

        variationInput.addEventListener(
            'input',
            function () {

                calculateNewStock();

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | STORE SEARCH
    |--------------------------------------------------------------------------
    */

    const storeSearch =
        document.getElementById(
            'storeSearch'
        );


    const storeNoResults =
        document.getElementById(
            'storeNoResults'
        );


    if (storeSearch) {

        storeSearch.addEventListener(
            'input',
            function () {

                filterStores(
                    this.value
                );

            }
        );

    }


    function filterStores(value) {

        const search =
            String(value || '')
                .trim()
                .toLowerCase();


        let visibleCount =
            0;


        document
            .querySelectorAll(
                '.store-option'
            )
            .forEach(
                function (row) {

                    const haystack =
                        row.dataset.search || '';


                    const matches =
                        haystack.includes(
                            search
                        );


                    row.style.display =
                        matches
                            ? ''
                            : 'none';


                    if (matches) {
                        visibleCount++;
                    }

                }
            );


        if (storeNoResults) {

            storeNoResults.style.display =
                visibleCount === 0
                    ? 'block'
                    : 'none';

        }

    }


    /*
    |--------------------------------------------------------------------------
    | ITEM SEARCH
    |--------------------------------------------------------------------------
    */

    const storeItemSearch =
        document.getElementById(
            'storeItemSearch'
        );


    const storeItemNoResults =
        document.getElementById(
            'storeItemNoResults'
        );


    if (storeItemSearch) {

        storeItemSearch.addEventListener(
            'input',
            function () {

                filterStoreItems(
                    this.value
                );

            }
        );

    }


    function filterStoreItems(value) {

        const search =
            String(value || '')
                .trim()
                .toLowerCase();


        let visibleCount =
            0;


        document
            .querySelectorAll(
                '.store-item-option'
            )
            .forEach(
                function (row) {

                    const haystack =
                        row.dataset.search || '';


                    const matches =
                        haystack.includes(
                            search
                        );


                    row.style.display =
                        matches
                            ? ''
                            : 'none';


                    if (matches) {
                        visibleCount++;
                    }

                }
            );


        if (storeItemNoResults) {

            storeItemNoResults.style.display =
                visibleCount === 0
                    ? 'block'
                    : 'none';

        }

    }




    const oldStoreId =
        @json(old('store_id'));


    const oldStoreItemId =
        @json(old('store_item_id'));


    const oldVariantId =
        @json(old('variant_id'));



    if (oldStoreId) {

        const oldStoreRow =
            document.querySelector(
                '.store-option[data-store-id="' +
                oldStoreId +
                '"]'
            );


        if (oldStoreRow) {

            storeIdInput.value =
                oldStoreId;


            const oldStoreName =
                oldStoreRow.dataset.storeName || '';


            const oldStoreCode =
                oldStoreRow.dataset.storeCode || '';


            storePickerText.innerHTML = `

                <strong>
                    ${escapeHtml(oldStoreName)}
                </strong>

                ${
                    oldStoreCode
                        ? `<small>${escapeHtml(oldStoreCode)}</small>`
                        : ''
                }

            `;


            selectedStoreDisplay.textContent =
                oldStoreName
                +
                (
                    oldStoreCode
                        ? ' (' + oldStoreCode + ')'
                        : ''
                );

        }

    }



    if (oldStoreItemId) {

        const oldItemRow =
            document.querySelector(
                '.store-item-option[data-store-item-id="' +
                oldStoreItemId +
                '"]'
            );


        if (oldItemRow) {

            storeItemIdInput.value =
                oldStoreItemId;


            const oldItemName =
                oldItemRow.dataset.storeItemName || '';


            const oldItemUnit =
                oldItemRow.dataset.storeItemUnit || '';


            storeItemPickerText.innerHTML = `

                <strong>
                    ${escapeHtml(oldItemName)}
                </strong>

                ${
                    oldItemUnit
                        ? `<small>${escapeHtml(oldItemUnit)}</small>`
                        : ''
                }

            `;


            selectedItemDisplay.textContent =
                oldItemName;


            populateVariants(
                oldStoreItemId
            );


            if (oldVariantId) {

                variantSelect.value =
                    String(oldVariantId);

            }

        }

    }



    updateStockDisplay();

});

</script>

@endpush

@endsection
