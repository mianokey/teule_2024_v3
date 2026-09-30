@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/stores.css') }}">
@endpush

@section('content')

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
                </div>

                <h1 class="requisition-page-title">
                    Create Requisition
                </h1>

                <p class="requisition-page-subtitle">
                    Request items from Stores for your department or specific children.
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
                title="Saving a requisition does not deduct stock. Stock is deducted only when Stores issues items."
            >
                <i class="fa fa-question"></i>
            </span>

        </div>

    </div>


    <x-message></x-message>


    {{-- ============================================================
        ENTIRE REQUISITION FORM
        IMPORTANT:
        This remains completely hidden until transaction setup
        has been completed.
    ============================================================= --}}
    <div
        id="requisition-form"
        style="display:none;"
    >

        <form
            id="requisitionForm"
            method="POST"
            action="{{ route('admin.stores.store-requisitions.store') }}"
        >

            @csrf

            {{-- ====================================================
                TRANSACTION SETUP VALUES
            ===================================================== --}}

            <input
                type="hidden"
                name="requisition_type"
                id="requisition_type"
                value="{{ old('requisition_type') }}"
            >

            <input
                type="hidden"
                name="source_store_id"
                id="source_store_id"
                value="{{ old('source_store_id') }}"
            >

            <input
                type="hidden"
                name="destination_store_id"
                id="destination_store_id"
                value="{{ old('destination_store_id') }}"
            >


            {{-- ====================================================
                REQUESTED ITEMS
            ===================================================== --}}

            <section class="requisition-section">

                <div class="requisition-details-header">

                    <strong>
                        Requested Items
                    </strong>

                    <span>
                        Add the items and quantities required.
                    </span>

                </div>

                <div class="requisition-details-body">

                    <div class="table-responsive">

                        <table class="table align-middle mb-0">

                            <thead>

                                <tr>

                                    <th style="width:60px;">
                                        #
                                    </th>

                                    <th>
                                        Item
                                    </th>

                                    <th>
                                        Variant
                                    </th>

                                    <th style="width:150px;">
                                        Quantity
                                    </th>

                                    <th style="width:120px;">
                                        Unit
                                    </th>

                                    <th style="width:220px;">
                                        Child
                                    </th>

                                    <th>
                                        Notes
                                    </th>

                                    <th style="width:60px;">
                                    </th>

                                </tr>

                            </thead>

                            <tbody id="requisitionItemsBody">

                            </tbody>

                        </table>

                    </div>

                    <div class="mt-3">

                        <button
                            type="button"
                            id="addItemButton"
                            class="requisition-save-button"
                        >
                            <i class="fa fa-plus me-1"></i>
                            Add Item
                        </button>

                    </div>

                </div>

            </section>


            {{-- ====================================================
                REQUEST DETAILS
            ===================================================== --}}

<section class="requisition-section requisition-details-section">

    <div class="requisition-details-header">

        <strong>
            Request Details
        </strong>

        <span>
            Provide additional information about this request.
        </span>

    </div>

    <div class="requisition-details-body">

        <div class="row g-3">

            <div class="col-6">

                <label
                    for="department"
                    class="requisition-field-label"
                >
                    Department
                </label>

                <input
                    type="text"
                    name="department"
                    id="department"
                    class="form-control requisition-input"
                    value="{{ old('department') }}"
                    placeholder="Enter department"
                >

            </div>

            <div class="col-6">

                <label
                    for="purpose"
                    class="requisition-field-label"
                >
                    Purpose
                </label>

                <INPUT
                    name="purpose"
                    id="purpose"
                    class="form-control requisition-input"
                    placeholder="Explain what the requested items will be used for."
                >{{ old('purpose') }}</textarea>

            </div>

        </div>

    </div>

</section>


            {{-- ====================================================
                ACTION BAR
            ===================================================== --}}

            <div class="requisition-action-bar">

                <a
                    href="{{ route('admin.stores.store-requisitions.index') }}"
                    class="requisition-cancel-button"
                >
                    <i class="fa fa-arrow-left me-1"></i>
                    Cancel
                </a>

                <button
                    type="button"
                    id="changeTransactionSetup"
                    class="requisition-cancel-button"
                >
                    <i class="fa fa-exchange-alt me-1"></i>
                    Change Transaction
                </button>

                <button
                    type="button"
                    id="saveRequisitionButton"
                    class="requisition-save-button"
                >
                    <i class="fa fa-save me-1"></i>
                    Save Requisition
                </button>

            </div>

        </form>

    </div>


    {{-- ============================================================
        ITEM ROW TEMPLATE
    ============================================================= --}}

    <template id="itemRowTemplate">

        <tr
            class="requisition-item-row"
            data-row-index="__INDEX__"
        >

            <td>

                <span class="requisition-row-number item-number">
                    1
                </span>

            </td>


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


            <td>

                <div class="requisition-uom-display">

                    <span class="uom-value requisition-uom">
                        —
                    </span>

                </div>

            </td>


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


                    <div class="requisition-child-inputs"></div>

                </div>

            </td>


            <td>

                <input
                    type="text"
                    name="items[__INDEX__][notes]"
                    class="form-control requisition-table-input"
                    placeholder="Optional"
                >

            </td>


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


    {{-- ============================================================
        ASSIGN CHILDREN MODAL
    ============================================================= --}}

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

                    <div class="requisition-child-search">

                        <i class="fa fa-search"></i>

                        <input
                            type="text"
                            id="childSearchInput"
                            class="form-control requisition-input"
                            placeholder="Search children..."
                        >

                    </div>


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


    {{-- ============================================================
        SAVE REQUISITION MODAL
    ============================================================= --}}

    <div
        class="modal fade"
        id="requisitionConfirmModal"
        tabindex="-1"
        aria-hidden="true"
    >

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content border-0 shadow">

                <div class="modal-header border-0">

                    <h5 class="modal-title">
                        Save Requisition
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>

                </div>


                <div class="modal-body">

                    <div class="requisition-confirm-icon">
                        <i class="fa fa-clipboard-check"></i>
                    </div>

                    <h6>
                        Save this requisition as a draft?
                    </h6>

                    <p>
                        Saving the requisition will not deduct any stock.
                        Stock is only deducted when Stores issues the items.
                    </p>


                    <div class="mt-4">

                        <label
                            for="submission_notes"
                            class="requisition-field-label"
                        >
                            Submission Notes

                            <span class="optional-label">
                                optional
                            </span>

                        </label>

                        <textarea
                            name="submission_notes"
                            id="submission_notes"
                            class="form-control requisition-input requisition-textarea"
                            rows="3"
                            maxlength="2000"
                            placeholder="Add any additional information for the approver or Stores team."
                        >{{ old('submission_notes') }}</textarea>

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

                    <button
                        type="button"
                        id="confirmSaveRequisition"
                        class="requisition-save-button"
                    >
                        <i class="fa fa-save"></i>
                        Save Draft
                    </button>

                </div>

            </div>

        </div>

    </div>


    {{-- ============================================================
        TRANSACTION SETUP MODAL
        THIS OPENS FIRST
    ============================================================= --}}

    <div
        class="modal fade"
        id="transactionSetupModal"
        tabindex="-1"
        aria-labelledby="transactionSetupModalLabel"
        aria-hidden="true"
    >

        <div class="modal-dialog modal-dialog-centered">

            <div class="modal-content border-0 shadow">

                <div class="modal-header">

                    <div>

                        <h5
                            class="modal-title mb-1"
                            id="transactionSetupModalLabel"
                        >
                            New Requisition
                        </h5>

                        <div class="requisition-modal-subtitle">
                            First, tell us what kind of stock movement you want to make.
                        </div>

                    </div>


                    <button
                        type="button"
                        class="btn-close"
                        id="closeTransactionSetup"
                        aria-label="Close"
                    ></button>

                </div>


                <div class="modal-body">


                    {{-- =================================================
                        MOVEMENT VISUAL
                    ================================================== --}}

                    <div
                        id="transactionMovementVisual"
                        class="text-center mb-4"
                    >

                        {{-- ITEM REQUISITION VISUAL --}}

                        <div
                            id="itemMovementVisual"
                            style="display:none;"
                        >

                            <div class="d-flex align-items-center justify-content-center gap-3">

                                <div class="text-center">

                                    <div class="requisition-header-icon mx-auto mb-2">
                                        <i class="fa fa-store"></i>
                                    </div>

                                    <div class="small fw-semibold">
                                        Store
                                    </div>

                                </div>


                                <div class="px-2">

                                    <i
                                        class="fa fa-arrow-right fa-beat-fade fa-2x"
                                        aria-hidden="true"
                                    ></i>

                                </div>


                                <div class="text-center">

                                    <div class="requisition-header-icon mx-auto mb-2">
                                        <i class="fa fa-user"></i>
                                    </div>

                                    <div class="small fw-semibold">
                                        Person
                                    </div>

                                </div>

                            </div>


                            <div class="requisition-modal-subtitle mt-3">

                                Items move from a store
                                <strong>to the person requesting them.</strong>

                            </div>

                        </div>


                        {{-- TRANSFER VISUAL --}}

                        <div
                            id="transferMovementVisual"
                            style="display:none;"
                        >

                            <div class="d-flex align-items-center justify-content-center gap-3">

                                <div class="text-center">

                                    <div class="requisition-header-icon mx-auto mb-2">
                                        <i class="fa fa-store"></i>
                                    </div>

                                    <div
                                        id="movementSourceName"
                                        class="small fw-semibold"
                                    >
                                        Source Store
                                    </div>

                                </div>


                                <div class="px-2">

                                    <i
                                        class="fa fa-arrow-right fa-beat-fade fa-2x"
                                        aria-hidden="true"
                                    ></i>

                                </div>


                                <div class="text-center">

                                    <div class="requisition-header-icon mx-auto mb-2">
                                        <i class="fa fa-store"></i>
                                    </div>

                                    <div
                                        id="movementDestinationName"
                                        class="small fw-semibold"
                                    >
                                        Destination Store
                                    </div>

                                </div>

                            </div>


                            <div class="requisition-modal-subtitle mt-3">

                                Stock moves from one store
                                <strong>to another store.</strong>

                            </div>

                        </div>


                        {{-- DEFAULT VISUAL --}}

                        <div
                            id="defaultMovementVisual"
                        >

                            <div class="d-flex align-items-center justify-content-center gap-3">

                                <div class="requisition-header-icon">
                                    <i class="fa fa-box"></i>
                                </div>

                                <i
                                    class="fa fa-arrow-right fa-2x"
                                    aria-hidden="true"
                                ></i>

                                <div class="requisition-header-icon">
                                    <i class="fa fa-question"></i>
                                </div>

                            </div>

                            <div class="requisition-modal-subtitle mt-3">
                                Choose a transaction type to see how the stock will move.
                            </div>

                        </div>

                    </div>


                    {{-- =================================================
                        TRANSACTION TYPE
                    ================================================== --}}

                    <div class="mb-3">

                        <label
                            for="setup_requisition_type"
                            class="requisition-field-label"
                        >
                            Transaction Type
                            <span class="required-mark">*</span>
                        </label>


                        <select
                            id="setup_requisition_type"
                            class="form-select requisition-input"
                        >

                            <option value="">
                                Select transaction type
                            </option>

                            <option value="ITEM">
                                Item Requisition
                            </option>

                            <option value="TRANSFER">
                                Stock Transfer
                            </option>

                        </select>

                    </div>


                    {{-- =================================================
                        SOURCE STORE
                    ================================================== --}}

                    <div class="mb-3">

                        <label
                            for="setup_source_store_id"
                            class="requisition-field-label"
                        >
                            Source Store
                            <span class="required-mark">*</span>
                        </label>


                        <select
                            id="setup_source_store_id"
                            class="form-select requisition-input"
                        >

                            <option value="">
                                Select source store
                            </option>

                            @foreach($stores as $store)

                                <option value="{{ $store->id }}">
                                    {{ $store->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- =================================================
                        DESTINATION STORE
                    ================================================== --}}

                    <div
                        id="setupDestinationGroup"
                        class="mb-3"
                        style="display:none;"
                    >

                        <label
                            for="setup_destination_store_id"
                            class="requisition-field-label"
                        >
                            Destination Store
                            <span class="required-mark">*</span>
                        </label>


                        <select
                            id="setup_destination_store_id"
                            class="form-select requisition-input"
                            disabled
                        >

                            <option value="">
                                Select destination store
                            </option>

                            @foreach($stores as $store)

                                <option value="{{ $store->id }}">
                                    {{ $store->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- =================================================
                        VALIDATION MESSAGE
                    ================================================== --}}

                    <div
                        id="transactionSetupMessage"
                        class="alert alert-danger mb-0"
                        style="display:none;"
                    ></div>

                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        id="cancelTransactionSetup"
                        class="requisition-cancel-button"
                    >
                        Cancel
                    </button>


                    <button
                        type="button"
                        id="continueTransactionSetup"
                        class="requisition-save-button"
                    >
                        Continue
                        <i class="fa fa-arrow-right ms-1"></i>
                    </button>

                </div>

            </div>

        </div>

    </div>

</div>


{{-- ================================================================
    VARIANT DATA
    Kept outside the JS expression to avoid Blade parser problems.
================================================================ --}}

@php

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

@endphp


<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | Transaction Setup
    |--------------------------------------------------------------------------
    */

    const transactionSetupElement =
        document.getElementById('transactionSetupModal');

    const transactionSetupModal =
        new bootstrap.Modal(
            transactionSetupElement,
            {
                backdrop: 'static',
                keyboard: false
            }
        );


    const requisitionFormWrapper =
        document.getElementById('requisition-form');

    const requisitionForm =
        document.getElementById('requisitionForm');


    const setupType =
        document.getElementById('setup_requisition_type');

    const setupSource =
        document.getElementById('setup_source_store_id');

    const setupDestination =
        document.getElementById('setup_destination_store_id');

    const setupDestinationGroup =
        document.getElementById('setupDestinationGroup');

    const setupMessage =
        document.getElementById('transactionSetupMessage');


    const itemMovementVisual =
        document.getElementById('itemMovementVisual');

    const transferMovementVisual =
        document.getElementById('transferMovementVisual');

    const defaultMovementVisual =
        document.getElementById('defaultMovementVisual');

    const movementSourceName =
        document.getElementById('movementSourceName');

    const movementDestinationName =
        document.getElementById('movementDestinationName');


    const hiddenType =
        document.getElementById('requisition_type');

    const hiddenSource =
        document.getElementById('source_store_id');

    const hiddenDestination =
        document.getElementById('destination_store_id');


    /*
    |--------------------------------------------------------------------------
    | Store Names
    |--------------------------------------------------------------------------
    */

    const storeNames =
        @json($stores->pluck('name', 'id'));


    /*
    |--------------------------------------------------------------------------
    | Existing Setup Values
    |--------------------------------------------------------------------------
    */

    const oldType =
        @json(old('requisition_type'));

    const oldSource =
        @json(old('source_store_id'));

    const oldDestination =
        @json(old('destination_store_id'));


    /*
    |--------------------------------------------------------------------------
    | Setup State
    |--------------------------------------------------------------------------
    */

    let transactionSetupComplete = false;


    /*
    |--------------------------------------------------------------------------
    | Visual
    |--------------------------------------------------------------------------
    */

    function updateMovementVisual() {

        const type = setupType.value;

        itemMovementVisual.style.display = 'none';
        transferMovementVisual.style.display = 'none';
        defaultMovementVisual.style.display = 'none';


        if (type === 'ITEM') {

            itemMovementVisual.style.display = 'block';

            return;
        }


        if (type === 'TRANSFER') {

            transferMovementVisual.style.display = 'block';

            const sourceId =
                setupSource.value;

            const destinationId =
                setupDestination.value;


            movementSourceName.textContent =
                sourceId && storeNames[sourceId]
                    ? storeNames[sourceId]
                    : 'Source Store';


            movementDestinationName.textContent =
                destinationId && storeNames[destinationId]
                    ? storeNames[destinationId]
                    : 'Destination Store';

            return;
        }


        defaultMovementVisual.style.display = 'block';
    }


    /*
    |--------------------------------------------------------------------------
    | Transaction Type Change
    |--------------------------------------------------------------------------
    */

    setupType.addEventListener('change', function () {

        setupMessage.style.display = 'none';
        setupMessage.textContent = '';


        if (this.value === 'TRANSFER') {

            setupDestinationGroup.style.display = 'block';

            setupDestination.disabled = false;

        } else {

            setupDestinationGroup.style.display = 'none';

            setupDestination.disabled = true;

            setupDestination.value = '';

        }


        updateMovementVisual();

    });


    /*
    |--------------------------------------------------------------------------
    | Source Store Change
    |--------------------------------------------------------------------------
    */

    setupSource.addEventListener('change', function () {

        setupMessage.style.display = 'none';
        setupMessage.textContent = '';

        updateMovementVisual();

    });


    /*
    |--------------------------------------------------------------------------
    | Destination Store Change
    |--------------------------------------------------------------------------
    */

    setupDestination.addEventListener('change', function () {

        setupMessage.style.display = 'none';
        setupMessage.textContent = '';

        updateMovementVisual();

    });


    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */

    function validateTransactionSetup() {

        setupMessage.style.display = 'none';
        setupMessage.textContent = '';


        if (!setupType.value) {

            setupMessage.textContent =
                'Please select the transaction type.';

            setupMessage.style.display = 'block';

            return false;
        }


        if (!setupSource.value) {

            setupMessage.textContent =
                'Please select the source store.';

            setupMessage.style.display = 'block';

            return false;
        }


        if (setupType.value === 'TRANSFER') {

            if (!setupDestination.value) {

                setupMessage.textContent =
                    'Please select the destination store.';

                setupMessage.style.display = 'block';

                return false;
            }


            if (
                setupSource.value ===
                setupDestination.value
            ) {

                setupMessage.textContent =
                    'The source and destination stores must be different.';

                setupMessage.style.display = 'block';

                return false;
            }

        }


        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | Continue
    |--------------------------------------------------------------------------
    */

    document
        .getElementById('continueTransactionSetup')
        .addEventListener('click', function () {

            if (!validateTransactionSetup()) {
                return;
            }


            hiddenType.value =
                setupType.value;

            hiddenSource.value =
                setupSource.value;


            if (setupType.value === 'TRANSFER') {

                hiddenDestination.value =
                    setupDestination.value;

            } else {

                hiddenDestination.value = '';

            }


            transactionSetupComplete = true;


            /*
            |--------------------------------------------------------------------------
            | Show the actual form only AFTER setup is complete
            |--------------------------------------------------------------------------
            */

            requisitionFormWrapper.style.display = '';


            transactionSetupModal.hide();


            /*
            |--------------------------------------------------------------------------
            | Add first item automatically
            |--------------------------------------------------------------------------
            */

            if (
                document.querySelectorAll(
                    '#requisitionItemsBody .requisition-item-row'
                ).length === 0
            ) {

                addItemRow();

            }

        });


    /*
    |--------------------------------------------------------------------------
    | Leave Setup
    |--------------------------------------------------------------------------
    */

    function leaveTransactionSetup() {

        window.location.href =
            "{{ route('admin.stores.store-requisitions.index') }}";

    }


    document
        .getElementById('cancelTransactionSetup')
        .addEventListener(
            'click',
            leaveTransactionSetup
        );


    document
        .getElementById('closeTransactionSetup')
        .addEventListener(
            'click',
            leaveTransactionSetup
        );


    /*
    |--------------------------------------------------------------------------
    | Change Transaction
    |--------------------------------------------------------------------------
    */

    document
        .getElementById('changeTransactionSetup')
        .addEventListener('click', function () {

            transactionSetupComplete = false;

            transactionSetupModal.show();

        });


    /*
    |--------------------------------------------------------------------------
    | Restore Old Values
    |--------------------------------------------------------------------------
    */

    if (oldType && oldSource) {

        setupType.value =
            oldType;

        setupSource.value =
            oldSource;


        if (oldType === 'TRANSFER') {

            setupDestinationGroup.style.display =
                'block';

            setupDestination.disabled =
                false;

            if (oldDestination) {

                setupDestination.value =
                    oldDestination;

            }

        }


        hiddenType.value =
            oldType;

        hiddenSource.value =
            oldSource;

        hiddenDestination.value =
            oldDestination || '';


        transactionSetupComplete =
            true;


        requisitionFormWrapper.style.display =
            '';

        updateMovementVisual();

    } else {

        /*
        |--------------------------------------------------------------------------
        | IMPORTANT:
        | Form stays completely hidden.
        |--------------------------------------------------------------------------
        */

        requisitionFormWrapper.style.display =
            'none';


        updateMovementVisual();


        setTimeout(function () {

            transactionSetupModal.show();

        }, 150);

    }


    /*
    |--------------------------------------------------------------------------
    | Requisition Items
    |--------------------------------------------------------------------------
    */

    const itemsBody =
        document.getElementById('requisitionItemsBody');

    const itemRowTemplate =
        document.getElementById('itemRowTemplate');


    let rowIndex = 0;


    /*
    |--------------------------------------------------------------------------
    | Variant Data
    |--------------------------------------------------------------------------
    */

    const variantData =
        @json($variantData);


    /*
    |--------------------------------------------------------------------------
    | Add Item Row
    |--------------------------------------------------------------------------
    */

    function addItemRow() {

        const template =
            itemRowTemplate.innerHTML.replace(
                /__INDEX__/g,
                rowIndex
            );


        itemsBody.insertAdjacentHTML(
            'beforeend',
            template
        );


        rowIndex++;

        updateRowNumbers();

    }


    document
        .getElementById('addItemButton')
        .addEventListener(
            'click',
            addItemRow
        );


    /*
    |--------------------------------------------------------------------------
    | Update Row Numbers
    |--------------------------------------------------------------------------
    */

    function updateRowNumbers() {

        const rows =
            itemsBody.querySelectorAll(
                '.requisition-item-row'
            );


        rows.forEach(function (row, index) {

            const number =
                row.querySelector('.item-number');


            if (number) {

                number.textContent =
                    index + 1;

            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | Item Selection
    |--------------------------------------------------------------------------
    */

    itemsBody.addEventListener(
        'change',
        function (event) {

            if (
                !event.target.classList.contains(
                    'requisition-item-select'
                )
            ) {

                return;

            }


            const itemSelect =
                event.target;

            const row =
                itemSelect.closest(
                    '.requisition-item-row'
                );


            if (!row) {
                return;
            }


            const variantSelect =
                row.querySelector(
                    '.requisition-variant-select'
                );

            const uomDisplay =
                row.querySelector(
                    '.requisition-uom'
                );


            const itemId =
                itemSelect.value;


            /*
            |--------------------------------------------------------------------------
            | Unit
            |--------------------------------------------------------------------------
            */

            const selectedOption =
                itemSelect.options[
                    itemSelect.selectedIndex
                ];


            if (selectedOption) {

                uomDisplay.textContent =
                    selectedOption.dataset.uom || '—';

            } else {

                uomDisplay.textContent =
                    '—';

            }


            /*
            |--------------------------------------------------------------------------
            | Variants
            |--------------------------------------------------------------------------
            */

            variantSelect.innerHTML =
                '<option value="">No variant</option>';


            variantSelect.disabled =
                true;


            if (
                itemId &&
                Array.isArray(variantData[itemId]) &&
                variantData[itemId].length > 0
            ) {

                variantData[itemId].forEach(
                    function (variant) {

                        const option =
                            document.createElement('option');

                        option.value =
                            variant.id;

                        option.textContent =
                            variant.name +
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
                    false;

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Remove Item
    |--------------------------------------------------------------------------
    */

    itemsBody.addEventListener(
        'click',
        function (event) {

            const removeButton =
                event.target.closest(
                    '.requisition-remove-item'
                );


            if (!removeButton) {
                return;
            }


            const row =
                removeButton.closest(
                    '.requisition-item-row'
                );


            if (!row) {
                return;
            }


            row.remove();

            updateRowNumbers();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Assign Children
    |--------------------------------------------------------------------------
    */

    let activeChildRow = null;


    const assignChildrenModalElement =
        document.getElementById(
            'assignChildrenModal'
        );


    const assignChildrenModal =
        new bootstrap.Modal(
            assignChildrenModalElement
        );


    const childSearchInput =
        document.getElementById(
            'childSearchInput'
        );


    const selectedChildrenCount =
        document.getElementById(
            'selectedChildrenCount'
        );


    function updateSelectedChildrenCount() {

        const selected =
            document.querySelectorAll(
                '.requisition-child-checkbox:checked'
            );


        selectedChildrenCount.textContent =
            selected.length + ' selected';

    }


    itemsBody.addEventListener(
        'click',
        function (event) {

            const button =
                event.target.closest(
                    '[data-action="assign-children"]'
                );


            if (!button) {
                return;
            }


            activeChildRow =
                button.closest(
                    '.requisition-item-row'
                );


            if (!activeChildRow) {
                return;
            }


            document
                .querySelectorAll(
                    '.requisition-child-checkbox'
                )
                .forEach(function (checkbox) {

                    checkbox.checked =
                        false;

                });


            const existingInputs =
                activeChildRow.querySelectorAll(
                    '.requisition-child-input'
                );


            existingInputs.forEach(
                function (input) {

                    const checkbox =
                        document.querySelector(
                            '.requisition-child-checkbox[value="' +
                            input.value +
                            '"]'
                        );


                    if (checkbox) {

                        checkbox.checked =
                            true;

                    }

                }
            );


            updateSelectedChildrenCount();

            childSearchInput.value =
                '';


            document
                .querySelectorAll(
                    '.requisition-child-option'
                )
                .forEach(function (option) {

                    option.style.display =
                        '';

                });


            assignChildrenModal.show();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Child Search
    |--------------------------------------------------------------------------
    */

    childSearchInput.addEventListener(
        'input',
        function () {

            const search =
                this.value
                    .trim()
                    .toLowerCase();


            document
                .querySelectorAll(
                    '.requisition-child-option'
                )
                .forEach(function (option) {

                    const name =
                        option.dataset.childName || '';


                    option.style.display =
                        name.includes(search)
                            ? ''
                            : 'none';

                });

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Child Checkbox Change
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'change',
        function (event) {

            if (
                event.target.classList.contains(
                    'requisition-child-checkbox'
                )
            ) {

                updateSelectedChildrenCount();

            }

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Assign Selected Children
    |--------------------------------------------------------------------------
    */

    document
        .getElementById('assignSelectedChildren')
        .addEventListener(
            'click',
            function () {

                if (!activeChildRow) {
                    return;
                }


                const container =
                    activeChildRow.querySelector(
                        '.requisition-child-inputs'
                    );


                const display =
                    activeChildRow.querySelector(
                        '.requisition-selected-children'
                    );


                const label =
                    activeChildRow.querySelector(
                        '.child-assignment-label'
                    );


                container.innerHTML =
                    '';

                display.innerHTML =
                    '';


                const selected =
                    document.querySelectorAll(
                        '.requisition-child-checkbox:checked'
                    );


                if (selected.length === 0) {

                    display.innerHTML =
                        '<span class="requisition-no-child-label">General</span>';

                    label.textContent =
                        '+ Assign';

                } else {

                    selected.forEach(
                        function (checkbox) {

                            const input =
                                document.createElement(
                                    'input'
                                );

                            input.type =
                                'hidden';

                            input.name =
                                checkbox.value
                                    ? (
                                        checkbox.closest(
                                            '.requisition-child-option'
                                        )
                                            ? 'items[' +
                                                activeChildRow.dataset.rowIndex +
                                                '][child_ids][]'
                                            : ''
                                    )
                                    : '';

                            input.value =
                                checkbox.value;

                            input.className =
                                'requisition-child-input';

                            if (input.name) {

                                container.appendChild(
                                    input
                                );

                            }


                            const childName =
                                document.createElement(
                                    'span'
                                );

                            childName.className =
                                'badge bg-light text-dark me-1 mb-1';

                            childName.textContent =
                                checkbox.dataset.childName;

                            display.appendChild(
                                childName
                            );

                        }
                    );


                    label.textContent =
                        selected.length +
                        ' Assigned';

                }


                assignChildrenModal.hide();

            }
        );


    /*
    |--------------------------------------------------------------------------
    | Save Requisition
    |--------------------------------------------------------------------------
    */

    const saveRequisitionButton =
        document.getElementById(
            'saveRequisitionButton'
        );


    const requisitionConfirmModalElement =
        document.getElementById(
            'requisitionConfirmModal'
        );


    const requisitionConfirmModal =
        new bootstrap.Modal(
            requisitionConfirmModalElement
        );


    saveRequisitionButton.addEventListener(
        'click',
        function () {

            if (!transactionSetupComplete) {

                transactionSetupModal.show();

                return;

            }


            if (
                !requisitionForm.checkValidity()
            ) {

                requisitionForm.reportValidity();

                return;

            }


            requisitionConfirmModal.show();

        }
    );


    /*
    |--------------------------------------------------------------------------
    | Confirm Save
    |--------------------------------------------------------------------------
    */

    document
        .getElementById('confirmSaveRequisition')
        .addEventListener(
            'click',
            function () {

                requisitionForm.submit();

            }
        );

});

</script>

@endsection