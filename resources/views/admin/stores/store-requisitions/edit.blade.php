@extends('layouts.admin')

@push('styles') <link rel="stylesheet" href="{{ asset('assets/css/stores.css') }}">
@endpush

@section('content')

@php
/*
|--------------------------------------------------------------------------
| Variant data
|--------------------------------------------------------------------------
*/
$variantData = $items->mapWithKeys(function ($item) {
$variantList = [];


    foreach ($item->variants as $variant) {
        $variantList[] = [
            'id' => $variant->id,
            'name' => $variant->name,
            'code' => $variant->code,
        ];
    }

    return [
        $item->id => $variantList,
    ];
})->toArray();

/*
|--------------------------------------------------------------------------
| Existing requisition items
|--------------------------------------------------------------------------
*/
$existingItems = $storeRequisition->items->map(function ($item) {
    return [
        'id' => $item->id,
        'store_item_id' => $item->store_item_id,
        'variant_id' => $item->variant_id,
        'requested_quantity' => $item->requested_quantity,
        'notes' => $item->notes,
        'child_ids' => $item->children
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->values()
            ->toArray(),
    ];
})->values()->toArray();

/*
|--------------------------------------------------------------------------
| Existing request type
|--------------------------------------------------------------------------
*/
$requisitionType = old(
    'requisition_type',
    $storeRequisition->requisition_type ?? 'ITEM'
);

/*
|--------------------------------------------------------------------------
| Existing stores
|--------------------------------------------------------------------------
*/
$sourceStoreId = old(
    'source_store_id',
    $storeRequisition->source_store_id
);

$destinationStoreId = old(
    'destination_store_id',
    $storeRequisition->destination_store_id
);


@endphp

<div class="store-requisition-page">


{{-- ============================================================
    PAGE HEADER
============================================================= --}}
<div class="requisition-page-header">

    <div class="requisition-header-content">

        <div class="requisition-header-icon">
            <i class="fa fa-clipboard-list"></i>
        </div>

        <div>
            <div class="requisition-breadcrumb">
                Stores
                <span>/</span>
                Requisitions
                <span>/</span>
                Edit
            </div>

            <h1 class="requisition-page-title">
                Edit Requisition
            </h1>

            <p class="requisition-page-subtitle">
                {{ $storeRequisition->requisition_number }}
                &nbsp;—&nbsp;
                Update your draft requisition.
            </p>
        </div>

    </div>

    <div class="requisition-header-right">

        <span class="requisition-draft-badge">
            <span class="requisition-status-dot"></span>
            Draft
        </span>

        <span
            class="requisition-help-icon"
            title="You can edit this requisition because it has not yet been submitted."
        >
            <i class="fa fa-question"></i>
        </span>

    </div>

</div>


{{-- ============================================================
    MESSAGES
============================================================= --}}
<x-message></x-message>


{{-- ============================================================
    MAIN FORM
============================================================= --}}
<form
    id="requisitionForm"
    method="POST"
    action="{{ route('admin.stores.store-requisitions.update', $storeRequisition) }}"
>

    @csrf
    @method('PUT')


    {{-- ========================================================
        REQUEST TYPE / STORES
    ========================================================= --}}
    <section class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fa fa-exchange-alt"></i>
                </div>

                <div>
                    <h5>
                        Request Setup
                    </h5>

                    <p>
                        Choose the transaction type and the stores involved.
                    </p>
                </div>

            </div>

        </div>


        <div class="requisition-details-body">

            <div class="row">

                {{-- =================================================
                    REQUEST TYPE
                ================================================== --}}
                <div class="col-md-4 mb-3 mb-md-0">

                    <label
                        for="requisition_type"
                        class="requisition-field-label"
                    >
                        Request Type
                        <span class="required-mark">*</span>
                    </label>

                    <select
                        name="requisition_type"
                        id="requisition_type"
                        class="form-select requisition-table-input requisition-input"
                        required
                    >
                        <option
                            value="ITEM"
                            {{ $requisitionType === 'ITEM' ? 'selected' : '' }}
                        >
                            Item Issue
                        </option>

                        <option
                            value="TRANSFER"
                            {{ $requisitionType === 'TRANSFER' ? 'selected' : '' }}
                        >
                            Stock Transfer
                        </option>
                    </select>

                    <div class="small text-muted mt-1">
                        Item Issue is for normal consumption.
                        Transfer moves stock between stores.
                    </div>

                    @error('requisition_type')
                        <div class="text-danger small mt-1">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- =================================================
                    SOURCE STORE
                ================================================== --}}
                <div class="col-md-4 mb-3 mb-md-0">

                    <label
                        for="source_store_id"
                        class="requisition-field-label"
                    >
                        Source Store
                        <span class="required-mark">*</span>
                    </label>

                    <select
                        name="source_store_id"
                        id="source_store_id"
                        class="form-select requisition-table-input requisition-input"
                        required
                    >

                        <option value="">
                            Select source store
                        </option>

                        @foreach($stores as $store)

                            <option
                                value="{{ $store->id }}"
                                {{ (string) $sourceStoreId === (string) $store->id ? 'selected' : '' }}
                            >
                                {{ $store->name }}
                                @if($store->code)
                                    ({{ $store->code }})
                                @endif
                            </option>

                        @endforeach

                    </select>

                    <div class="small text-muted mt-1">
                        Stock will be issued from this store.
                    </div>

                    @error('source_store_id')
                        <div class="text-danger small mt-1">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- =================================================
                    DESTINATION STORE
                ================================================== --}}
                <div
                    class="col-md-4"
                    id="destinationStoreWrapper"
                >

                    <label
                        for="destination_store_id"
                        class="requisition-field-label"
                    >
                        Destination Store
                        <span
                            id="destinationRequiredMark"
                            class="required-mark"
                        >
                            *
                        </span>
                    </label>

                    <select
                        name="destination_store_id"
                        id="destination_store_id"
                        class="form-select requisition-table-input requisition-input"
                    >

                        <option value="">
                            Select destination store
                        </option>

                        @foreach($stores as $store)

                            <option
                                value="{{ $store->id }}"
                                {{ (string) $destinationStoreId === (string) $store->id ? 'selected' : '' }}
                            >
                                {{ $store->name }}
                                @if($store->code)
                                    ({{ $store->code }})
                                @endif
                            </option>

                        @endforeach

                    </select>

                    <div class="small text-muted mt-1">
                        Required only for stock transfers.
                    </div>

                    @error('destination_store_id')
                        <div class="text-danger small mt-1">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>

        </div>

    </section>


    {{-- ========================================================
        REQUESTED ITEMS
    ========================================================= --}}
    <section class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon requisition-items-icon">
                    <i class="fa fa-boxes"></i>
                </div>

                <div>
                    <h5>
                        Requested Items
                    </h5>

                    <p>
                        Update the items required from Stores.
                    </p>
                </div>

            </div>

            <span
                id="itemCount"
                class="requisition-count-badge"
            >
                0 items
            </span>

        </div>


        {{-- ====================================================
            TABLE
        ===================================================== --}}
        <div class="requisition-table-wrapper">

            <table class="table requisition-items-table">

                <thead>

                    <tr>

                        <th style="width: 45px;">
                            #
                        </th>

                        <th style="min-width: 220px;">
                            Item
                        </th>

                        <th style="min-width: 180px;">
                            Variant
                        </th>

                        <th style="width: 120px;">
                            Quantity
                        </th>

                        <th style="width: 100px;">
                            UOM
                        </th>

                        <th style="min-width: 220px;">
                            For Child/Children
                        </th>

                        <th style="min-width: 180px;">
                            Notes
                        </th>

                        <th style="width: 50px;">
                        </th>

                    </tr>

                </thead>


                <tbody id="requisitionItemsContainer">
                    {{-- Rows inserted by JavaScript --}}
                </tbody>

            </table>


            {{-- =================================================
                EMPTY STATE
            ================================================== --}}
            <div
                id="emptyItemsState"
                class="requisition-table-empty"
            >

                <div class="requisition-empty-icon">
                    <i class="fa fa-box-open"></i>
                </div>

                <strong>
                    No items added
                </strong>

                <span>
                    Add an item to continue editing your requisition.
                </span>

            </div>

        </div>


        {{-- ====================================================
            ADD ITEM
        ===================================================== --}}
        <div class="requisition-add-item-area">

            <button
                type="button"
                id="addItemButton"
                class="requisition-add-button"
            >
                <i class="fa fa-plus"></i>
                Add Item
            </button>

        </div>

    </section>


    {{-- ========================================================
        REQUEST DETAILS
    ========================================================= --}}
    <section class="requisition-section requisition-details-section">

        <div class="requisition-details-header">

            <strong>
                Request Details
            </strong>

            <span>
                Update the department and purpose for this requisition.
            </span>

        </div>


        <div class="requisition-details-body">

            <div class="row">

                {{-- =================================================
                    DEPARTMENT
                ================================================== --}}
                <div class="col-md-6 mb-3 mb-md-0">

                    <label
                        for="department"
                        class="requisition-field-label"
                    >
                        Department

                        <span class="required-mark">
                            *
                        </span>
                    </label>


                    <input
                        type="text"
                        name="department"
                        id="department"
                        class="form-control requisition-input"
                        value="{{ old('department', $storeRequisition->department) }}"
                        placeholder="e.g. School, Children's Home, Administration"
                        required
                    >


                    @error('department')
                        <div class="text-danger small mt-1">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- =================================================
                    PURPOSE
                ================================================== --}}
                <div class="col-md-6">

                    <label
                        for="purpose"
                        class="requisition-field-label"
                    >
                        Purpose

                        <span class="required-mark">
                            *
                        </span>
                    </label>


                    <textarea
                        name="purpose"
                        id="purpose"
                        class="form-control requisition-input requisition-textarea"
                        placeholder="Explain why these items are required."
                        required
                    >{{ old('purpose', $storeRequisition->purpose) }}</textarea>


                    @error('purpose')
                        <div class="text-danger small mt-1">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>

        </div>

    </section>


    {{-- ========================================================
        ACTION BAR
    ========================================================= --}}
    <div class="requisition-action-bar">

        <a
            href="{{ route('admin.stores.store-requisitions.index') }}"
            class="requisition-cancel-button"
        >
            <i class="fa fa-arrow-left"></i>
            Cancel
        </a>


        <div class="requisition-action-right">

            <span class="requisition-action-hint">
                Changes will remain in draft.
            </span>


            <button
                type="submit"
                class="requisition-save-button"
            >
                <i class="fa fa-save"></i>
                Save Changes
            </button>

        </div>

    </div>

</form>


</div>

{{-- =================================================================
ITEM ROW TEMPLATE
================================================================= --}} <template id="itemRowTemplate">


<tr
    class="requisition-item-row"
    data-row-index="__INDEX__"
>

    {{-- Number --}}
    <td>

        <span class="requisition-row-number item-number">
            1
        </span>

    </td>


    {{-- Item --}}
    <td>

        <select
            name="items[__INDEX__][store_item_id]"
            class="form-select requisition-table-input requisition-item-select"
            required
        >

            <option value="">
                Select item
            </option>

            @foreach($items as $item)

                <option
                    value="{{ $item->id }}"
                    data-uom="{{ $item->unit?->name ?? '—' }}"
                >
                    {{ $item->name }}

                    @if($item->sku)
                        ({{ $item->sku }})
                    @endif
                </option>

            @endforeach

        </select>

    </td>


    {{-- Variant --}}
    <td>

        <select
            name="items[__INDEX__][variant_id]"
            class="form-select requisition-table-input requisition-variant-select"
            disabled
        >

            <option value="">
                No variant
            </option>

        </select>

    </td>


    {{-- Quantity --}}
    <td>

        <input
            type="number"
            name="items[__INDEX__][requested_quantity]"
            class="form-control requisition-table-input requisition-quantity"
            min="0.001"
            step="0.001"
            placeholder="0"
            required
        >

    </td>


    {{-- UOM --}}
    <td>

        <div class="requisition-uom-display">

            <span class="uom-value requisition-uom">
                —
            </span>

        </div>

    </td>


    {{-- Children --}}
    <td>

        <div class="requisition-child-assignment">

            <button
                type="button"
                class="requisition-assign-child-button"
                data-action="assign-children"
            >

                <i class="fa fa-user-plus"></i>

                <span class="child-assignment-label">
                    + Assign
                </span>

            </button>


            <div class="requisition-selected-children">

                <span class="requisition-no-child-label">
                    General
                </span>

            </div>


            <div class="requisition-child-inputs">
            </div>

        </div>

    </td>


    {{-- Notes --}}
    <td>

        <input
            type="text"
            name="items[__INDEX__][notes]"
            class="form-control requisition-table-input"
            placeholder="Optional"
        >

    </td>


    {{-- Remove --}}
    <td class="text-center">

        <button
            type="button"
            class="requisition-row-remove requisition-remove-item"
            title="Remove item"
        >

            <i class="fa fa-trash"></i>

        </button>

    </td>

</tr>


</template>

{{-- =================================================================
ASSIGN CHILDREN MODAL
================================================================= --}}

<div
    class="modal fade requisition-child-modal"
    id="assignChildrenModal"
    tabindex="-1"
    aria-hidden="true"
>


<div class="modal-dialog modal-dialog-centered modal-lg">

    <div class="modal-content border-0 shadow">

        <div class="modal-header">

            <div>

                <h5 class="modal-title mb-1">
                    Assign Item to Child/Children
                </h5>

                <div class="requisition-modal-subtitle">
                    Select one or more children for this item.
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

            {{-- Search --}}
            <div class="requisition-child-search">

                <i class="fa fa-search"></i>

                <input
                    type="text"
                    id="childSearchInput"
                    class="form-control requisition-input"
                    placeholder="Search children..."
                >

            </div>


            {{-- Children list --}}
            <div
                id="childSelectionList"
                class="requisition-child-selection-list"
            >

                @forelse($children as $child)

                    <label
                        class="requisition-child-option"
                        data-child-name="{{ strtolower($child->name) }}"
                    >

                        <input
                            type="checkbox"
                            class="requisition-child-checkbox"
                            value="{{ $child->id }}"
                            data-child-name="{{ $child->name }}"
                        >

                        <span class="requisition-child-checkmark">
                            <i class="fa fa-check"></i>
                        </span>

                        <span class="requisition-child-name">
                            {{ $child->name }}
                        </span>

                    </label>

                @empty

                    <div class="requisition-no-children-found">
                        No children found.
                    </div>

                @endforelse

            </div>

        </div>


        <div class="modal-footer">

            <span
                id="selectedChildrenCount"
                class="requisition-selected-count"
            >
                0 selected
            </span>


            <button
                type="button"
                class="requisition-cancel-button"
                data-bs-dismiss="modal"
            >
                Cancel
            </button>


            <button
                type="button"
                id="assignSelectedChildren"
                class="requisition-save-button"
            >

                <i class="fa fa-check"></i>
                Assign Selected

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
    | ELEMENTS
    |--------------------------------------------------------------------------
    */

    const form =
        document.getElementById('requisitionForm');

    const itemsContainer =
        document.getElementById(
            'requisitionItemsContainer'
        );

    const emptyItemsState =
        document.getElementById(
            'emptyItemsState'
        );

    const addItemButton =
        document.getElementById(
            'addItemButton'
        );

    const itemCount =
        document.getElementById(
            'itemCount'
        );

    const itemRowTemplate =
        document.getElementById(
            'itemRowTemplate'
        );

    const assignChildrenModalElement =
        document.getElementById(
            'assignChildrenModal'
        );

    const requisitionType =
        document.getElementById(
            'requisition_type'
        );

    const sourceStore =
        document.getElementById(
            'source_store_id'
        );

    const destinationStore =
        document.getElementById(
            'destination_store_id'
        );

    const destinationStoreWrapper =
        document.getElementById(
            'destinationStoreWrapper'
        );


    /*
    |--------------------------------------------------------------------------
    | DATA
    |--------------------------------------------------------------------------
    */

    const variants =
        @json($variantData);

    const existingItems =
        @json($existingItems);


    /*
    |--------------------------------------------------------------------------
    | STATE
    |--------------------------------------------------------------------------
    */

    let rowIndex = 0;

    let currentChildAssignmentRow = null;

    let assignChildrenModal = null;


    /*
    |--------------------------------------------------------------------------
    | CHILD MODAL
    |--------------------------------------------------------------------------
    */

    if (
        assignChildrenModalElement &&
        typeof bootstrap !== 'undefined'
    ) {

        assignChildrenModal =
            new bootstrap.Modal(
                assignChildrenModalElement
            );

    }


    /*
    |--------------------------------------------------------------------------
    | REQUEST TYPE / DESTINATION STORE
    |--------------------------------------------------------------------------
    */

    function updateRequestTypeUI()
    {
        if (
            !requisitionType ||
            !destinationStore ||
            !destinationStoreWrapper
        ) {
            return;
        }

        const isTransfer =
            requisitionType.value === 'TRANSFER';


        if (isTransfer) {

            destinationStoreWrapper.style.display =
                '';

            destinationStore.required =
                true;

        } else {

            destinationStoreWrapper.style.display =
                '';

            destinationStore.required =
                false;

            destinationStore.value =
                '';

        }


        updateDestinationOptions();
    }


    /*
    |--------------------------------------------------------------------------
    | PREVENT SAME SOURCE / DESTINATION
    |--------------------------------------------------------------------------
    */

    function updateDestinationOptions()
    {
        if (
            !sourceStore ||
            !destinationStore
        ) {
            return;
        }

        const sourceId =
            sourceStore.value;


        Array.from(
            destinationStore.options
        ).forEach(function (option) {

            if (!option.value) {
                option.disabled = false;
                return;
            }

            option.disabled =
                sourceId &&
                String(option.value) ===
                String(sourceId);

        });


        if (
            destinationStore.value &&
            String(destinationStore.value) ===
            String(sourceId)
        ) {

            destinationStore.value = '';

        }
    }


    if (requisitionType) {

        requisitionType.addEventListener(
            'change',
            function () {
                updateRequestTypeUI();
            }
        );

    }


    if (sourceStore) {

        sourceStore.addEventListener(
            'change',
            function () {
                updateDestinationOptions();
            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | INITIAL REQUEST TYPE STATE
    |--------------------------------------------------------------------------
    */

    updateRequestTypeUI();


    /*
    |--------------------------------------------------------------------------
    | UPDATE ITEM COUNT
    |--------------------------------------------------------------------------
    */

    function updateItemCount()
    {
        const rows =
            itemsContainer.querySelectorAll(
                '.requisition-item-row'
            );

        const count =
            rows.length;


        itemCount.textContent =
            count === 1
                ? '1 item'
                : count + ' items';


        emptyItemsState.style.display =
            count === 0
                ? 'flex'
                : 'none';


        rows.forEach(function (row, index) {

            const numberCell =
                row.querySelector(
                    '.item-number'
                );

            if (numberCell) {

                numberCell.textContent =
                    index + 1;

            }

        });
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE UOM
    |--------------------------------------------------------------------------
    */

    function updateUom(row)
    {
        const itemSelect =
            row.querySelector(
                '.requisition-item-select'
            );

        const uomElement =
            row.querySelector(
                '.requisition-uom'
            );


        if (
            !itemSelect ||
            !uomElement
        ) {
            return;
        }


        const selectedOption =
            itemSelect.options[
                itemSelect.selectedIndex
            ];


        if (
            selectedOption &&
            selectedOption.value
        ) {

            uomElement.textContent =
                selectedOption.dataset.uom ||
                '—';

        } else {

            uomElement.textContent =
                '—';

        }
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE VARIANTS
    |--------------------------------------------------------------------------
    */

    function updateVariantOptions(
        row,
        selectedVariantId = null
    ) {

        const itemSelect =
            row.querySelector(
                '.requisition-item-select'
            );

        const variantSelect =
            row.querySelector(
                '.requisition-variant-select'
            );


        if (
            !itemSelect ||
            !variantSelect
        ) {
            return;
        }


        const itemId =
            itemSelect.value;


        variantSelect.innerHTML =
            '<option value="">No variant</option>';


        if (!itemId) {

            variantSelect.disabled =
                true;

            return;
        }


        const itemVariants =
            variants[itemId] || [];


        if (itemVariants.length === 0) {

            variantSelect.disabled =
                true;

            return;
        }


        itemVariants.forEach(function (variant) {

            const option =
                document.createElement(
                    'option'
                );


            option.value =
                variant.id;


            option.textContent =
                variant.name +
                (
                    variant.code
                        ? ' (' + variant.code + ')'
                        : ''
                );


            if (
                selectedVariantId &&
                String(selectedVariantId) ===
                String(variant.id)
            ) {

                option.selected =
                    true;

            }


            variantSelect.appendChild(
                option
            );

        });


        variantSelect.disabled =
            false;
    }


    /*
    |--------------------------------------------------------------------------
    | DISPLAY ASSIGNED CHILDREN
    |--------------------------------------------------------------------------
    */

    function displayAssignedChildren(row)
    {
        const selectedContainer =
            row.querySelector(
                '.requisition-selected-children'
            );

        const label =
            row.querySelector(
                '.child-assignment-label'
            );

        const inputsContainer =
            row.querySelector(
                '.requisition-child-inputs'
            );


        if (
            !selectedContainer ||
            !inputsContainer
        ) {
            return;
        }


        const selectedIds =
            Array.from(
                inputsContainer.querySelectorAll(
                    'input[name$="[child_ids][]"]'
                )
            ).map(function (input) {

                return String(
                    input.value
                );

            });


        selectedContainer.innerHTML =
            '';


        /*
        |--------------------------------------------------------------------------
        | NO CHILD ASSIGNED
        |--------------------------------------------------------------------------
        */

        if (selectedIds.length === 0) {

            const general =
                document.createElement(
                    'span'
                );

            general.className =
                'requisition-no-child-label';

            general.textContent =
                'General';

            selectedContainer.appendChild(
                general
            );


            if (label) {

                label.textContent =
                    '+ Assign';

            }

            return;
        }


        /*
        |--------------------------------------------------------------------------
        | CHILDREN ASSIGNED
        |--------------------------------------------------------------------------
        */

        selectedIds.forEach(function (childId) {

            const checkbox =
                document.querySelector(
                    '.requisition-child-checkbox[value="' +
                    childId +
                    '"]'
                );


            if (!checkbox) {
                return;
            }


            const childName =
                checkbox.dataset.childName ||
                'Child';


            const badge =
                document.createElement(
                    'span'
                );

            badge.className =
                'requisition-child-badge';

            badge.textContent =
                childName;


            selectedContainer.appendChild(
                badge
            );

        });


        /*
        |--------------------------------------------------------------------------
        | UPDATE BUTTON LABEL
        |--------------------------------------------------------------------------
        */

        if (label) {

            label.textContent =
                selectedIds.length === 1
                    ? '1 Child'
                    : selectedIds.length +
                        ' Children';

        }
    }


    /*
    |--------------------------------------------------------------------------
    | ADD ITEM ROW
    |--------------------------------------------------------------------------
    */

    function addItemRow(data = null)
    {
        const currentIndex =
            rowIndex;


        let html =
            itemRowTemplate.innerHTML;


        html =
            html.replace(
                /__INDEX__/g,
                currentIndex
            );


        itemsContainer.insertAdjacentHTML(
            'beforeend',
            html
        );


        const row =
            itemsContainer.lastElementChild;


        if (!row) {
            return;
        }


        row.dataset.rowIndex =
            currentIndex;


        /*
        |--------------------------------------------------------------------------
        | ELEMENTS
        |--------------------------------------------------------------------------
        */

        const itemSelect =
            row.querySelector(
                '.requisition-item-select'
            );

        const variantSelect =
            row.querySelector(
                '.requisition-variant-select'
            );

        const quantityInput =
            row.querySelector(
                '.requisition-quantity'
            );


        /*
        |--------------------------------------------------------------------------
        | LOAD EXISTING DATA
        |--------------------------------------------------------------------------
        */

        if (data) {

            itemSelect.value =
                data.store_item_id || '';


            updateUom(row);


            updateVariantOptions(
                row,
                data.variant_id || null
            );


            quantityInput.value =
                data.requested_quantity ?? '';


            /*
            |--------------------------------------------------------------------------
            | NOTES
            |--------------------------------------------------------------------------
            */

            const actualNotesInput =
                row.querySelector(
                    'input[name="items[' +
                    currentIndex +
                    '][notes]"]'
                );


            if (actualNotesInput) {

                actualNotesInput.value =
                    data.notes || '';

            }


            /*
            |--------------------------------------------------------------------------
            | CHILD ASSIGNMENTS
            |--------------------------------------------------------------------------
            */

            const inputsContainer =
                row.querySelector(
                    '.requisition-child-inputs'
                );


            if (
                inputsContainer &&
                Array.isArray(data.child_ids)
            ) {

                data.child_ids.forEach(
                    function (childId) {

                        const input =
                            document.createElement(
                                'input'
                            );


                        input.type =
                            'hidden';


                        input.name =
                            'items[' +
                            currentIndex +
                            '][child_ids][]';


                        input.value =
                            childId;


                        inputsContainer.appendChild(
                            input
                        );

                    }
                );

            }


            /*
            |--------------------------------------------------------------------------
            | DISPLAY CHILDREN
            |--------------------------------------------------------------------------
            */

            displayAssignedChildren(row);

        } else {

            updateUom(row);

            updateVariantOptions(row);

            displayAssignedChildren(row);

        }


        /*
        |--------------------------------------------------------------------------
        | ITEM CHANGE
        |--------------------------------------------------------------------------
        */

        if (itemSelect) {

            itemSelect.addEventListener(
                'change',
                function () {

                    updateUom(row);

                    updateVariantOptions(row);

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | REMOVE
        |--------------------------------------------------------------------------
        */

        const removeButton =
            row.querySelector(
                '.requisition-remove-item'
            );


        if (removeButton) {

            removeButton.addEventListener(
                'click',
                function () {

                    row.remove();

                    updateItemCount();

                }
            );

        }


        /*
        |--------------------------------------------------------------------------
        | ASSIGN CHILDREN
        |--------------------------------------------------------------------------
        */

        const assignButton =
            row.querySelector(
                '[data-action="assign-children"]'
            );


        if (assignButton) {

            assignButton.addEventListener(
                'click',
                function () {

                    openChildAssignmentModal(
                        row
                    );

                }
            );

        }


        rowIndex++;

        updateItemCount();
    }


    /*
    |--------------------------------------------------------------------------
    | ADD ITEM BUTTON
    |--------------------------------------------------------------------------
    */

    if (addItemButton) {

        addItemButton.addEventListener(
            'click',
            function () {

                addItemRow();

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | OPEN CHILD ASSIGNMENT MODAL
    |--------------------------------------------------------------------------
    */

    function openChildAssignmentModal(row)
    {
        if (!assignChildrenModal) {
            return;
        }


        currentChildAssignmentRow =
            row;


        const inputsContainer =
            row.querySelector(
                '.requisition-child-inputs'
            );


        const selectedIds = [];


        if (inputsContainer) {

            const existingInputs =
                inputsContainer.querySelectorAll(
                    'input[name$="[child_ids][]"]'
                );


            existingInputs.forEach(
                function (input) {

                    selectedIds.push(
                        String(
                            input.value
                        )
                    );

                }
            );

        }


        const checkboxes =
            document.querySelectorAll(
                '.requisition-child-checkbox'
            );


        checkboxes.forEach(
            function (checkbox) {

                checkbox.checked =
                    selectedIds.includes(
                        String(
                            checkbox.value
                        )
                    );

            }
        );


        updateSelectedChildrenCount();


        const searchInput =
            document.getElementById(
                'childSearchInput'
            );


        if (searchInput) {

            searchInput.value =
                '';

            filterChildren();

        }


        assignChildrenModal.show();
    }


    /*
    |--------------------------------------------------------------------------
    | FILTER CHILDREN
    |--------------------------------------------------------------------------
    */

    function filterChildren()
    {
        const searchInput =
            document.getElementById(
                'childSearchInput'
            );


        const search =
            searchInput
                ? searchInput.value
                    .toLowerCase()
                    .trim()
                : '';


        const options =
            document.querySelectorAll(
                '.requisition-child-option'
            );


        let visibleCount = 0;


        options.forEach(
            function (option) {

                const childName =
                    option.dataset.childName ||
                    '';


                const visible =
                    childName.includes(
                        search
                    );


                option.style.display =
                    visible
                        ? 'flex'
                        : 'none';


                if (visible) {
                    visibleCount++;
                }

            }
        );


        let noResults =
            document.querySelector(
                '.requisition-no-children-found'
            );


        if (
            visibleCount === 0 &&
            options.length > 0
        ) {

            if (!noResults) {

                noResults =
                    document.createElement(
                        'div'
                    );

                noResults.className =
                    'requisition-no-children-found';

                noResults.textContent =
                    'No children match your search.';


                const selectionList =
                    document.getElementById(
                        'childSelectionList'
                    );


                if (selectionList) {

                    selectionList.appendChild(
                        noResults
                    );

                }

            }


            noResults.style.display =
                'block';

        } else if (noResults) {

            if (options.length > 0) {

                noResults.style.display =
                    'none';

            }

        }
    }


    /*
    |--------------------------------------------------------------------------
    | CHILD SEARCH
    |--------------------------------------------------------------------------
    */

    const childSearchInput =
        document.getElementById(
            'childSearchInput'
        );


    if (childSearchInput) {

        childSearchInput.addEventListener(
            'input',
            function () {

                filterChildren();

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | SELECTED CHILD COUNT
    |--------------------------------------------------------------------------
    */

    function updateSelectedChildrenCount()
    {
        const selected =
            document.querySelectorAll(
                '.requisition-child-checkbox:checked'
            );


        const count =
            selected.length;


        const countElement =
            document.getElementById(
                'selectedChildrenCount'
            );


        if (countElement) {

            countElement.textContent =
                count === 1
                    ? '1 selected'
                    : count + ' selected';

        }
    }


    /*
    |--------------------------------------------------------------------------
    | CHECKBOX EVENTS
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll(
            '.requisition-child-checkbox'
        )
        .forEach(
            function (checkbox) {

                checkbox.addEventListener(
                    'change',
                    function () {

                        updateSelectedChildrenCount();

                    }
                );

            }
        );


    /*
    |--------------------------------------------------------------------------
    | ASSIGN SELECTED CHILDREN
    |--------------------------------------------------------------------------
    */

    const assignSelectedChildren =
        document.getElementById(
            'assignSelectedChildren'
        );


    if (assignSelectedChildren) {

        assignSelectedChildren.addEventListener(
            'click',
            function () {

                if (!currentChildAssignmentRow) {
                    return;
                }


                const selected =
                    Array.from(
                        document.querySelectorAll(
                            '.requisition-child-checkbox:checked'
                        )
                    );


                const selectedChildren =
                    selected.map(
                        function (checkbox) {

                            return {

                                id:
                                    checkbox.value,

                                name:
                                    checkbox.dataset.childName

                            };

                        }
                    );


                /*
                |--------------------------------------------------------------------------
                | DISPLAY CHILDREN
                |--------------------------------------------------------------------------
                */

                const selectedChildrenContainer =
                    currentChildAssignmentRow.querySelector(
                        '.requisition-selected-children'
                    );


                if (selectedChildrenContainer) {

                    selectedChildrenContainer.innerHTML =
                        '';


                    if (
                        selectedChildren.length === 0
                    ) {

                        const general =
                            document.createElement(
                                'span'
                            );


                        general.className =
                            'requisition-no-child-label';


                        general.textContent =
                            'General';


                        selectedChildrenContainer.appendChild(
                            general
                        );

                    } else {

                        selectedChildren.forEach(
                            function (child) {

                                const badge =
                                    document.createElement(
                                        'span'
                                    );


                                badge.className =
                                    'requisition-child-badge';


                                badge.textContent =
                                    child.name;


                                selectedChildrenContainer.appendChild(
                                    badge
                                );

                            }
                        );

                    }

                }


                /*
                |--------------------------------------------------------------------------
                | BUTTON LABEL
                |--------------------------------------------------------------------------
                */

                const label =
                    currentChildAssignmentRow.querySelector(
                        '.child-assignment-label'
                    );


                if (label) {

                    if (
                        selectedChildren.length === 0
                    ) {

                        label.textContent =
                            '+ Assign';

                    } else {

                        label.textContent =
                            selectedChildren.length === 1
                                ? '1 Child'
                                : selectedChildren.length +
                                    ' Children';

                    }

                }


                /*
                |--------------------------------------------------------------------------
                | HIDDEN INPUTS
                |--------------------------------------------------------------------------
                */

                const inputsContainer =
                    currentChildAssignmentRow.querySelector(
                        '.requisition-child-inputs'
                    );


                if (inputsContainer) {

                    inputsContainer.innerHTML =
                        '';


                    const currentRowIndex =
                        currentChildAssignmentRow.dataset.rowIndex;


                    selectedChildren.forEach(
                        function (child) {

                            const input =
                                document.createElement(
                                    'input'
                                );


                            input.type =
                                'hidden';


                            input.name =
                                'items[' +
                                currentRowIndex +
                                '][child_ids][]';


                            input.value =
                                child.id;


                            inputsContainer.appendChild(
                                input
                            );

                        }
                    );

                }


                assignChildrenModal.hide();

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | FORM VALIDATION
    |--------------------------------------------------------------------------
    */

    if (form) {

        form.addEventListener(
            'submit',
            function (event) {

                const rows =
                    itemsContainer.querySelectorAll(
                        '.requisition-item-row'
                    );


                /*
                |--------------------------------------------------------------
                | At least one item
                |--------------------------------------------------------------
                */

                if (rows.length === 0) {

                    event.preventDefault();

                    alert(
                        'Please add at least one item to the requisition.'
                    );

                    return;

                }


                /*
                |--------------------------------------------------------------
                | Transfer destination
                |--------------------------------------------------------------
                */

                if (
                    requisitionType &&
                    requisitionType.value === 'TRANSFER'
                ) {

                    if (
                        !destinationStore ||
                        !destinationStore.value
                    ) {

                        event.preventDefault();

                        alert(
                            'Please select a destination store for a stock transfer.'
                        );

                        if (destinationStore) {
                            destinationStore.focus();
                        }

                        return;

                    }


                    if (
                        sourceStore &&
                        sourceStore.value ===
                        destinationStore.value
                    ) {

                        event.preventDefault();

                        alert(
                            'Source and destination stores cannot be the same.'
                        );

                        destinationStore.focus();

                        return;

                    }

                }

            }
        );

    }


    /*
    |--------------------------------------------------------------------------
    | INITIAL LOAD
    |--------------------------------------------------------------------------
    */

    if (existingItems.length > 0) {

        existingItems.forEach(
            function (item) {

                addItemRow(item);

            }
        );

    } else {

        addItemRow();

    }


    updateItemCount();

});

</script>

@endpush

@endsection
