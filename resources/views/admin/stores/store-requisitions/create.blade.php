blade
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
                <i class="fa fa-clipboard-list"></i>
            </div>

            <div>

                <div class="requisition-breadcrumb">

                    <span>Stores</span>
                    <span>/</span>
                    <span>Requisitions</span>
                    <span>/</span>
                    <span>Create</span>

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
         REQUISITION FORM
    ============================================================ --}}
    <div id="requisition-form" style="display:none;">

        <form
            id="requisitionForm"
            method="POST"
            action="{{ route('admin.stores.store-requisitions.store') }}"
        >

            @csrf


            {{-- ====================================================
                 TRANSACTION VALUES
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
            <div class="requisition-section">

                <div class="requisition-section-header">

                    <div class="requisition-section-heading">

                        <div class="requisition-section-icon">
                            <i class="fa fa-shopping-cart"></i>
                        </div>

                        <div>

                            <h5>
                                Requested Items
                            </h5>

                            <p>
                                Add the items and quantities required.
                            </p>

                        </div>

                    </div>


                    <div class="requisition-header-right">

                        <button
                            type="button"
                            id="addItemButton"
                            class="requisition-add-button"
                        >
                            <i class="fa fa-plus"></i>
                            Add Item
                        </button>

                    </div>

                </div>


                <div class="requisition-details-body">

                    <div class="requisition-list-table-wrapper">

                        <table class="requisition-list-table">

                            <thead>

                                <tr>

                                    <th style="width:45px;">
                                        #
                                    </th>

                                    <th style="min-width:220px;">
                                        Item
                                    </th>

                                    <th style="min-width:140px;">
                                        Variant
                                    </th>

                                    <th style="width:120px;">
                                        Quantity
                                    </th>

                                    <th style="width:90px;">
                                        Unit
                                    </th>

                                    <th style="min-width:240px;">
                                        Child
                                    </th>

                                    <th style="min-width:170px;">
                                        Notes
                                    </th>

                                    <th style="width:55px;">
                                    </th>

                                </tr>

                            </thead>


                            <tbody id="requisitionItemsBody">
                            </tbody>

                        </table>

                    </div>


                    <div
                        id="emptyItemsMessage"
                        class="requisition-table-empty"
                        style="display:none;"
                    >

                        <div class="requisition-empty-icon">
                            <i class="fa fa-shopping-cart"></i>
                        </div>

                        <p>
                            No items have been added to this requisition yet.
                        </p>

                        <button
                            type="button"
                            class="requisition-add-button"
                            onclick="document.getElementById('addItemButton').click()"
                        >
                            <i class="fa fa-plus"></i>
                            Add Item
                        </button>

                    </div>

                </div>

            </div>


            {{-- ====================================================
                 REQUEST DETAILS
            ===================================================== --}}
            <div class="requisition-section">

                <div class="requisition-section-header">

                    <div class="requisition-section-heading">

                        <div class="requisition-section-icon">
                            <i class="fa fa-info"></i>
                        </div>

                        <div>

                            <h5>
                                Request Details
                            </h5>

                            <p>
                                Provide additional information about this request.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="requisition-details-body">

                    <div class="row">

                        {{-- DEPARTMENT --}}
                        <div class="col-lg-6 col-md-6 mb-3">

                            <div class="d-flex align-items-center">

                                <label
                                    for="department"
                                    class="requisition-field-label mb-0 mr-2"
                                    style="min-width:95px;"
                                >
                                    Department:
                                </label>

                                <select
                                    name="department"
                                    id="department"
                                    class="form-select requisition-input flex-grow-1"
                                >

                                    <option value="">
                                        Select department
                                    </option>

                                    @if(isset($departments))

                                        @foreach($departments as $department)

                                            @php

                                                $departmentValue =
                                                    is_object($department)
                                                        ? (
                                                            $department->name
                                                            ?? $department->department_name
                                                            ?? $department->title
                                                            ?? ''
                                                        )
                                                        : $department;

                                            @endphp

                                            @if($departmentValue !== '')

                                                <option
                                                    value="{{ $departmentValue }}"
                                                    {{ old('department') == $departmentValue ? 'selected' : '' }}
                                                >
                                                    {{ $departmentValue }}
                                                </option>

                                            @endif

                                        @endforeach

                                    @endif

                                </select>

                            </div>

                        </div>


                        {{-- PURPOSE --}}
                        <div class="col-lg-6 col-md-6 mb-3">

                            <div class="d-flex align-items-center">

                                <label
                                    for="purpose"
                                    class="requisition-field-label mb-0 mr-2"
                                    style="min-width:70px;"
                                >
                                    Purpose:
                                </label>

                                <input
                                    type="text"
                                    name="purpose"
                                    id="purpose"
                                    class="form-control requisition-input flex-grow-1"
                                    value="{{ old('purpose') }}"
                                    placeholder="Explain what the requested items will be used for."
                                >

                            </div>

                        </div>

                    </div>

                </div>

            </div>


            {{-- ====================================================
                 BOTTOM ACTIONS
            ===================================================== --}}
            <div class="requisition-bottom-actions">

                <a
                    href="{{ route('admin.stores.store-requisitions.index') }}"
                    class="requisition-cancel-button"
                >
                    <i class="fa fa-arrow-left"></i>
                    Cancel
                </a>


                <button
                    type="button"
                    id="changeTransactionSetup"
                    class="requisition-cancel-button"
                >
                    <i class="fa fa-exchange-alt"></i>
                    Change Transaction
                </button>


                <button
                    type="button"
                    id="saveRequisitionButton"
                    class="requisition-add-button"
                >
                    <i class="fa fa-save"></i>
                    Save Requisition
                </button>

            </div>

        </form>

    </div>


    {{-- ============================================================
         ITEM ROW TEMPLATE
    ============================================================ --}}
    <template id="itemRowTemplate">

        <tr
            class="requisition-item-row"
            data-row-index="__INDEX__"
        >

            {{-- NUMBER --}}
            <td>
                <strong class="item-number">
                    1
                </strong>
            </td>


            {{-- ITEM --}}
            <td>

                <input
                    type="hidden"
                    name="items[__INDEX__][store_item_id]"
                    class="requisition-item-id"
                    required
                >

                <button
                    type="button"
                    class="requisition-list-action form-control form-control-sm primary requisition-item-picker"
                    data-action="select-item"
                    title="Select item"
                    style="
                        width:100%;
                        display:flex;
                        align-items:center;
                        justify-content:space-between;
                        gap:8px;
                        text-align:left;
                        overflow:hidden;
                    "
                >

                    <span
                        class="requisition-item-label"
                        style="
                            min-width:0;
                            flex:1 1 auto;
                            overflow:hidden;
                            text-overflow:ellipsis;
                            white-space:nowrap;
                        "
                    >
                        Select item
                    </span>

                    <i
                        class="fa fa-chevron-down"
                        style="flex:0 0 auto;"
                    ></i>

                </button>

            </td>


            {{-- VARIANT --}}
            <td>

                <select
                    name="items[__INDEX__][variant_id]"
                    class="form-select form-select-sm requisition-table-input requisition-variant-select"
                    disabled
                >

                    <option value="">
                        No variant
                    </option>

                </select>

            </td>


            {{-- QUANTITY --}}
            <td>

                <input
                    type="number"
                    name="items[__INDEX__][requested_quantity]"
                    class="form-control form-control-sm requisition-table-input requisition-quantity"
                    min="0.001"
                    step="0.001"
                    placeholder="0"
                    required
                >

            </td>


            {{-- UNIT --}}
<td>

    <div class="requisition-input">

        <span class="requisition-uom">
            —
        </span>

    </div>

</td>



            {{-- CHILD --}}
<td>
    <div
        class="requisition-child-assignment"
        style="
            width:100%;
            min-width:0;
            display:flex;
            align-items:center;
            gap:6px;
        "
    >

        {{-- ASSIGN BUTTON --}}
        <button
            type="button"
            class="requisition-add-button primary"
            data-action="assign-children"
            title="Assign this item to one or more children"
            style="
                flex:0 0 auto;
                display:inline-flex;
                align-items:center;
                justify-content:center;
                gap:5px;
                white-space:nowrap;
                margin:0;
                padding:6px 10px;
            "
        >
            <i class="fa fa-user-plus"></i>
            Assign
        </button>

        {{-- CHILD FIELD --}}
        <div
            class="requisition-selected-children requisition-input"
            title="General"
            style="
                flex:1 1 auto;
                width:auto;
                min-width:0;
                overflow:hidden;
                text-overflow:ellipsis;
                white-space:nowrap;
                cursor:default;
            "
        >
            <span class="requisition-no-child-label">
                General
            </span>
        </div>

        {{-- HIDDEN CHILD IDS --}}
        <div class="requisition-child-inputs"></div>

    </div>
</td>

            {{-- NOTES --}}
            <td>

                <input
                    type="text"
                    name="items[__INDEX__][notes]"
                    class="form-control form-control-sm requisition-table-input"
                    placeholder="Optional"
                >

            </td>


            {{-- REMOVE --}}
            <td class="text-center">

                <button
                    type="button"
                    class="requisition-list-action danger requisition-remove-item"
                    title="Remove item"
                >
                    <i class="fa fa-trash"></i>
                </button>

            </td>

        </tr>

    </template>


    {{-- ============================================================
         ITEM SELECTION MODAL
    ============================================================ --}}
    <div
        class="modal fade"
        id="storeItemSelectionModal"
        tabindex="-1"
        aria-hidden="true"
    >

        <div
            class="modal-dialog modal-dialog-centered modal-lg"
            style="max-width:720px;"
        >

            <div
                class="modal-content border-0 shadow"
                style="overflow:hidden;"
            >

                <div class="modal-header">

                    <div>

                        <h5 class="modal-title mb-1">
                            Select Store Item
                        </h5>

                        <div class="requisition-modal-subtitle">
                            Search and select an item.
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

                    {{-- SEARCH --}}
                    <div
                        style="
                            display:flex;
                            align-items:center;
                            gap:8px;
                            width:100%;
                            margin-bottom:12px;
                        "
                    >

                        <i
                            class="fa fa-search"
                            style="
                                flex:0 0 auto;
                                color:#6c757d;
                            "
                        ></i>

                        <input
                            type="text"
                            id="storeItemSearch"
                            class="form-control requisition-input"
                            placeholder="Search item, SKU or unit..."
                            autocomplete="off"
                            style="
                                width:1%;
                                flex:1 1 auto;
                            "
                        >

                    </div>


                    {{-- ITEM LIST --}}
                    <div
                        id="storeItemSelectionList"
                        style="
                            width:100%;
                            max-height:52vh;
                            overflow-y:auto;
                            overflow-x:hidden;
                            border:1px solid #e9ecef;
                            border-radius:5px;
                        "
                    >

                        @forelse($items as $item)

                            <label
                                class="store-item-option"
                                data-item-name="{{ strtolower($item->name . ' ' . ($item->sku ?? '') . ' ' . ($item->unit?->name ?? '')) }}"
                                style="
                                    display:flex;
                                    align-items:center;
                                    gap:10px;
                                    width:100%;
                                    min-width:0;
                                    box-sizing:border-box;
                                    padding:9px 12px;
                                    margin:0;
                                    border-bottom:1px solid #eeeeee;
                                    cursor:pointer;
                                    overflow:hidden;
                                "
                            >

                                <input
                                    type="radio"
                                    name="selected_store_item"
                                    class="store-item-radio"
                                    value="{{ $item->id }}"
                                    data-item-name="{{ $item->name }}"
                                    data-sku="{{ $item->sku ?? '' }}"
                                    data-uom="{{ $item->unit?->name ?? '—' }}"
                                    style="
                                        flex:0 0 18px;
                                        width:18px;
                                        height:18px;
                                        margin:0;
                                    "
                                >


                                {{-- ITEM --}}
                                <span
                                    style="
                                        flex:1 1 45%;
                                        min-width:0;
                                        overflow:hidden;
                                    "
                                >

                                    <strong
                                        style="
                                            display:block;
                                            overflow:hidden;
                                            text-overflow:ellipsis;
                                            white-space:nowrap;
                                        "
                                    >
                                        {{ $item->name }}
                                    </strong>

                                </span>


                                {{-- SKU --}}
                                <span
                                    style="
                                        flex:0 0 22%;
                                        min-width:0;
                                        overflow:hidden;
                                        text-overflow:ellipsis;
                                        white-space:nowrap;
                                        color:#6c757d;
                                        font-size:12px;
                                    "
                                >
                                    {{ $item->sku ?: '—' }}
                                </span>


                                {{-- UNIT --}}
                                <span
                                    style="
                                        flex:0 0 18%;
                                        min-width:0;
                                        overflow:hidden;
                                        text-overflow:ellipsis;
                                        white-space:nowrap;
                                        color:#6c757d;
                                        font-size:12px;
                                        text-align:right;
                                    "
                                >
                                    {{ $item->unit?->name ?? '—' }}
                                </span>

                            </label>

                        @empty

                            <div class="requisition-table-empty">

                                <div class="requisition-empty-icon">
                                    <i class="fa fa-cube"></i>
                                </div>

                                <p>
                                    No store items found.
                                </p>

                            </div>

                        @endforelse

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


    {{-- ============================================================
         ASSIGN CHILDREN MODAL
    ============================================================ --}}
    <div
        class="modal fade requisition-child-modal"
        id="assignChildrenModal"
        tabindex="-1"
        aria-hidden="true"
    >

        <div class="modal-dialog modal-dialog-centered modal-lg">

            <div
                class="modal-content border-0 shadow"
                style="overflow:hidden;"
            >

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

                    {{-- SEARCH --}}
                    <div
                        style="
                            display:flex;
                            align-items:center;
                            gap:8px;
                            width:100%;
                            margin-bottom:12px;
                        "
                    >

                        <i
                            class="fa fa-search"
                            style="
                                flex:0 0 auto;
                                color:#6c757d;
                            "
                        ></i>

                        <input
                            type="text"
                            id="childSearchInput"
                            class="form-control requisition-input"
                            placeholder="Search children..."
                            autocomplete="off"
                            style="
                                width:1%;
                                flex:1 1 auto;
                            "
                        >

                    </div>


                    {{-- CHILDREN --}}
                    <div
                        id="childSelectionList"
                        style="
                            width:100%;
                            max-height:52vh;
                            overflow-y:auto;
                            overflow-x:hidden;
                        "
                    >

                        @forelse($children as $child)

                            <label
                                class="requisition-child-option"
                                data-child-name="{{ strtolower($child->name) }}"
                                style="
                                    display:flex;
                                    align-items:center;
                                    gap:10px;
                                    width:100%;
                                    min-width:0;
                                    max-width:100%;
                                    box-sizing:border-box;
                                    padding:8px 10px;
                                    margin:0;
                                    cursor:pointer;
                                    overflow:hidden;
                                "
                            >

                                <input
                                    type="checkbox"
                                    class="requisition-child-checkbox"
                                    value="{{ $child->id }}"
                                    data-child-name="{{ $child->name }}"
                                    style="
                                        position:static !important;
                                        display:block !important;
                                        opacity:1 !important;
                                        visibility:visible !important;
                                        flex:0 0 18px !important;
                                        width:18px !important;
                                        min-width:18px !important;
                                        max-width:18px !important;
                                        height:18px !important;
                                        margin:0 !important;
                                        padding:0 !important;
                                        box-sizing:border-box !important;
                                    "
                                >

                                <span
                                    class="requisition-child-name"
                                    style="
                                        min-width:0;
                                        flex:1 1 auto;
                                        overflow:hidden;
                                        text-overflow:ellipsis;
                                        white-space:nowrap;
                                    "
                                    title="{{ $child->name }}"
                                >
                                    {{ $child->name }}
                                </span>

                            </label>

                        @empty

                            <div class="requisition-table-empty">

                                <div class="requisition-empty-icon">
                                    <i class="fa fa-users"></i>
                                </div>

                                <p>
                                    No children found.
                                </p>

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
                        class="requisition-add-button"
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
    ============================================================ --}}
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
                        class="requisition-add-button"
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
    ============================================================ --}}
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

                    {{-- MOVEMENT VISUAL --}}
                    <div
                        id="transactionMovementVisual"
                        class="text-center mb-4"
                    >

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
                                <strong>
                                    to the person requesting them.
                                </strong>

                            </div>

                        </div>


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
                                <strong>
                                    to another store.
                                </strong>

                            </div>

                        </div>


                        <div id="defaultMovementVisual">

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


                    {{-- TRANSACTION TYPE --}}
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


                    {{-- SOURCE STORE --}}
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


                    {{-- DESTINATION STORE --}}
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
                        class="requisition-add-button"
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
            $item->id => $variantList
        ];

    })->toArray();

@endphp


<script>

document.addEventListener('DOMContentLoaded', function () {

    /* ============================================================
       ELEMENTS
    ============================================================ */

    const requisitionForm =
        document.getElementById('requisitionForm');

    const requisitionFormWrapper =
        document.getElementById('requisition-form');

    const itemsBody =
        document.getElementById('requisitionItemsBody');

    const itemRowTemplate =
        document.getElementById('itemRowTemplate');

    const addItemButton =
        document.getElementById('addItemButton');

    const emptyItemsMessage =
        document.getElementById('emptyItemsMessage');


    /* ============================================================
       VARIANT DATA
    ============================================================ */

    const variantData =
        @json($variantData);


    /* ============================================================
       MODALS
    ============================================================ */

    const transactionSetupModal =
        new bootstrap.Modal(
            document.getElementById('transactionSetupModal'),
            {
                backdrop: 'static',
                keyboard: false
            }
        );


    const itemSelectionModal =
        new bootstrap.Modal(
            document.getElementById('storeItemSelectionModal')
        );


    const assignChildrenModal =
        new bootstrap.Modal(
            document.getElementById('assignChildrenModal')
        );


    const requisitionConfirmModal =
        new bootstrap.Modal(
            document.getElementById('requisitionConfirmModal')
        );


    /* ============================================================
       TRANSACTION SETUP
    ============================================================ */

    const setupType =
        document.getElementById('setup_requisition_type');

    const setupSourceStore =
        document.getElementById('setup_source_store_id');

    const setupDestinationStore =
        document.getElementById('setup_destination_store_id');

    const setupDestinationGroup =
        document.getElementById('setupDestinationGroup');

    const transactionSetupMessage =
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


    function updateTransactionVisual() {

        itemMovementVisual.style.display = 'none';

        transferMovementVisual.style.display = 'none';

        defaultMovementVisual.style.display = 'block';

        setupDestinationGroup.style.display = 'none';

        setupDestinationStore.disabled = true;


        if (setupType.value === 'ITEM') {

            itemMovementVisual.style.display = 'block';

            defaultMovementVisual.style.display = 'none';

        }


        if (setupType.value === 'TRANSFER') {

            transferMovementVisual.style.display = 'block';

            defaultMovementVisual.style.display = 'none';

            setupDestinationGroup.style.display = 'block';

            setupDestinationStore.disabled = false;

        }

    }


    setupType.addEventListener(
        'change',
        updateTransactionVisual
    );


    setupSourceStore.addEventListener(
        'change',
        function () {

            const option =
                setupSourceStore.options[
                    setupSourceStore.selectedIndex
                ];

            movementSourceName.textContent =
                option && option.value
                    ? option.text
                    : 'Source Store';

        }
    );


    setupDestinationStore.addEventListener(
        'change',
        function () {

            const option =
                setupDestinationStore.options[
                    setupDestinationStore.selectedIndex
                ];

            movementDestinationName.textContent =
                option && option.value
                    ? option.text
                    : 'Destination Store';

        }
    );


    document
        .getElementById('continueTransactionSetup')
        .addEventListener(
            'click',
            function () {

                transactionSetupMessage.style.display = 'none';

                transactionSetupMessage.textContent = '';


                if (!setupType.value) {

                    transactionSetupMessage.textContent =
                        'Please select a transaction type.';

                    transactionSetupMessage.style.display =
                        'block';

                    return;

                }


                if (!setupSourceStore.value) {

                    transactionSetupMessage.textContent =
                        'Please select the source store.';

                    transactionSetupMessage.style.display =
                        'block';

                    return;

                }


                if (
                    setupType.value === 'TRANSFER' &&
                    !setupDestinationStore.value
                ) {

                    transactionSetupMessage.textContent =
                        'Please select the destination store.';

                    transactionSetupMessage.style.display =
                        'block';

                    return;

                }


                if (
                    setupType.value === 'TRANSFER' &&
                    setupSourceStore.value ===
                    setupDestinationStore.value
                ) {

                    transactionSetupMessage.textContent =
                        'The source and destination stores cannot be the same.';

                    transactionSetupMessage.style.display =
                        'block';

                    return;

                }


                document.getElementById(
                    'requisition_type'
                ).value = setupType.value;


                document.getElementById(
                    'source_store_id'
                ).value = setupSourceStore.value;


                document.getElementById(
                    'destination_store_id'
                ).value =
                    setupDestinationStore.value || '';


                transactionSetupModal.hide();


                requisitionFormWrapper.style.display =
                    'block';


                if (
                    itemsBody.querySelectorAll(
                        '.requisition-item-row'
                    ).length === 0
                ) {

                    addItemRow();

                }

            }
        );


    document
        .getElementById('cancelTransactionSetup')
        .addEventListener(
            'click',
            function () {

                window.location.href =
                    "{{ route('admin.stores.store-requisitions.index') }}";

            }
        );


    document
        .getElementById('closeTransactionSetup')
        .addEventListener(
            'click',
            function () {

                window.location.href =
                    "{{ route('admin.stores.store-requisitions.index') }}";

            }
        );


    document
        .getElementById('changeTransactionSetup')
        .addEventListener(
            'click',
            function () {

                setupType.value =
                    document.getElementById(
                        'requisition_type'
                    ).value || '';


                setupSourceStore.value =
                    document.getElementById(
                        'source_store_id'
                    ).value || '';


                setupDestinationStore.value =
                    document.getElementById(
                        'destination_store_id'
                    ).value || '';


                updateTransactionVisual();


                requisitionFormWrapper.style.display =
                    'none';


                transactionSetupModal.show();

            }
        );


    /* ============================================================
       ITEM ROWS
    ============================================================ */

    let itemRowIndex = 0;


    function updateRowNumbers() {

        itemsBody
            .querySelectorAll(
                '.requisition-item-row'
            )
            .forEach(function (row, index) {

                const number =
                    row.querySelector('.item-number');

                if (number) {

                    number.textContent =
                        index + 1;

                }

            });


        const hasRows =
            itemsBody.querySelectorAll(
                '.requisition-item-row'
            ).length > 0;


        emptyItemsMessage.style.display =
            hasRows
                ? 'none'
                : 'block';

    }


    function addItemRow() {

        const index =
            itemRowIndex++;


        const html =
            itemRowTemplate.innerHTML
                .replaceAll(
                    '__INDEX__',
                    index
                );


        itemsBody.insertAdjacentHTML(
            'beforeend',
            html
        );


        updateRowNumbers();

    }


    addItemButton.addEventListener(
        'click',
        addItemRow
    );


    /* ============================================================
       ITEM SELECTION
    ============================================================ */

    let activeItemRow = null;


    const storeItemSearch =
        document.getElementById(
            'storeItemSearch'
        );


    itemsBody.addEventListener(
        'click',
        function (event) {

            const button =
                event.target.closest(
                    '[data-action="select-item"]'
                );


            if (!button) {
                return;
            }


            activeItemRow =
                button.closest(
                    '.requisition-item-row'
                );


            const currentItemId =
                activeItemRow
                    .querySelector(
                        '.requisition-item-id'
                    )
                    .value;


            document
                .querySelectorAll(
                    '.store-item-radio'
                )
                .forEach(function (radio) {

                    radio.checked =
                        radio.value ===
                        currentItemId;

                });


            storeItemSearch.value =
                '';


            document
                .querySelectorAll(
                    '.store-item-option'
                )
                .forEach(function (option) {

                    option.style.display =
                        'flex';

                });


            itemSelectionModal.show();

        }
    );


    /* ============================================================
       ITEM SEARCH
    ============================================================ */

    storeItemSearch.addEventListener(
        'input',
        function () {

            const search =
                this.value
                    .trim()
                    .toLowerCase();


            document
                .querySelectorAll(
                    '.store-item-option'
                )
                .forEach(function (option) {

                    const name =
                        option.dataset.itemName || '';


                    option.style.display =
                        name.includes(search)
                            ? 'flex'
                            : 'none';

                });

        }
    );


    /* ============================================================
       ITEM SELECTED
    ============================================================ */

document.addEventListener(
    'change',
    function (event) {

        if (
            !event.target.classList.contains(
                'store-item-radio'
            )
        ) {
            return;
        }


        if (!activeItemRow) {
            return;
        }


        const radio =
            event.target;


        const itemId =
            radio.value;


        const itemName =
            radio.dataset.itemName || '';


        const sku =
            radio.dataset.sku || '';


        const uom =
            radio.dataset.uom || '—';


        const hiddenInput =
            activeItemRow.querySelector(
                '.requisition-item-id'
            );


        const itemLabel =
            activeItemRow.querySelector(
                '.requisition-item-label'
            );


        const uomElement =
            activeItemRow.querySelector(
                '.requisition-uom'
            );


        const variantSelect =
            activeItemRow.querySelector(
                '.requisition-variant-select'
            );


        /* ====================================================
           SAVE SELECTED ITEM
        ==================================================== */

        hiddenInput.value =
            itemId;


        itemLabel.textContent =
            sku
                ? itemName + ' (' + sku + ')'
                : itemName;


        itemLabel.title =
            sku
                ? itemName + ' (' + sku + ')'
                : itemName;


        uomElement.textContent =
            uom;


        /* ====================================================
           LOAD VARIANTS
        ==================================================== */

        variantSelect.innerHTML =
            '<option value="">No variant</option>';


        variantSelect.disabled =
            true;


        if (
            Array.isArray(
                variantData[itemId]
            ) &&
            variantData[itemId].length
        ) {

            variantData[itemId]
                .forEach(function (variant) {

                    const option =
                        document.createElement(
                            'option'
                        );


                    option.value =
                        variant.id;


                    option.textContent =
                        variant.code
                            ? variant.name +
                              ' (' +
                              variant.code +
                              ')'
                            : variant.name;


                    variantSelect.appendChild(
                        option
                    );

                });


            variantSelect.disabled =
                false;

        }


        /* ====================================================
           CLOSE ITEM SELECTION MODAL
        ==================================================== */

        itemSelectionModal.hide();


        /*
         * Make sure Bootstrap has time to complete
         * the modal closing animation.
         */
        activeItemRow = null;

    }
);

    /* ============================================================
       REMOVE ITEM
    ============================================================ */

    itemsBody.addEventListener(
        'click',
        function (event) {

            const button =
                event.target.closest(
                    '.requisition-remove-item'
                );


            if (!button) {
                return;
            }


            const row =
                button.closest(
                    '.requisition-item-row'
                );


            if (row) {

                row.remove();

                updateRowNumbers();

            }

        }
    );


    /* ============================================================
       CHILD ASSIGNMENT
    ============================================================ */

    let activeChildRow = null;


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


    /* ============================================================
       OPEN CHILD MODAL
    ============================================================ */

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


            const existingIds = [];


            activeChildRow
                .querySelectorAll(
                    '.requisition-child-inputs input'
                )
                .forEach(function (input) {

                    existingIds.push(
                        String(input.value)
                    );

                });


            document
                .querySelectorAll(
                    '.requisition-child-checkbox'
                )
                .forEach(function (checkbox) {

                    checkbox.checked =
                        existingIds.includes(
                            String(checkbox.value)
                        );

                });


            childSearchInput.value =
                '';


            document
                .querySelectorAll(
                    '.requisition-child-option'
                )
                .forEach(function (option) {

                    option.style.display =
                        'flex';

                });


            updateSelectedChildrenCount();


            assignChildrenModal.show();

        }
    );


    /* ============================================================
       CHILD SEARCH
    ============================================================ */

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
                            ? 'flex'
                            : 'none';

                });

        }
    );


    /* ============================================================
       CHILD CHECKBOX CHANGE
    ============================================================ */

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


    /* ============================================================
       ASSIGN SELECTED CHILDREN
    ============================================================ */

    document
        .getElementById('assignSelectedChildren')
        .addEventListener(
            'click',
            function () {

                if (!activeChildRow) {
                    return;
                }


                const selected =
                    Array.from(
                        document.querySelectorAll(
                            '.requisition-child-checkbox:checked'
                        )
                    );


                const inputsContainer =
                    activeChildRow.querySelector(
                        '.requisition-child-inputs'
                    );


                const displayField =
                    activeChildRow.querySelector(
                        '.requisition-selected-children'
                    );


                const rowIndex =
                    activeChildRow.dataset.rowIndex;


                inputsContainer.innerHTML =
                    '';


                /* GENERAL */
                if (selected.length === 0) {

                    displayField.innerHTML =
                        '<span class="requisition-no-child-label">General</span>';


                    displayField.title =
                        'General';


                    assignChildrenModal.hide();

                    return;

                }


                /* HIDDEN INPUTS */
                selected.forEach(
                    function (checkbox) {

                        const input =
                            document.createElement(
                                'input'
                            );


                        input.type =
                            'hidden';


                        input.name =
                            'items[' +
                            rowIndex +
                            '][child_ids][]';


                        input.value =
                            checkbox.value;


                        inputsContainer.appendChild(
                            input
                        );

                    }
                );


                /* CHILD NAMES */
                const names =
                    selected.map(
                        function (checkbox) {

                            return checkbox.dataset.childName;

                        }
                    );


                const fullNames =
                    names.join(', ');


                displayField.innerHTML =
                    '';


                if (names.length === 1) {

                    displayField.textContent =
                        names[0];

                } else {

                    displayField.textContent =
                        names.length +
                        ' children assigned';

                }


                /*
                 * Full names appear on hover.
                 */
                displayField.title =
                    fullNames;


                assignChildrenModal.hide();

            }
        );


    /* ============================================================
       SAVE
    ============================================================ */

    document
        .getElementById('saveRequisitionButton')
        .addEventListener(
            'click',
            function () {

                if (
                    !requisitionForm.checkValidity()
                ) {

                    requisitionForm.reportValidity();

                    return;

                }


                requisitionConfirmModal.show();

            }
        );


    /* ============================================================
       CONFIRM SAVE
    ============================================================ */

    document
        .getElementById('confirmSaveRequisition')
        .addEventListener(
            'click',
            function () {

                const button =
                    this;


                button.disabled =
                    true;


                button.innerHTML =
                    '<i class="fa fa-spinner fa-spin"></i> Saving...';


                const submissionNotes =
                    document.getElementById(
                        'submission_notes'
                    );


                let hiddenNotes =
                    requisitionForm.querySelector(
                        'input[name="submission_notes"]'
                    );


                if (!hiddenNotes) {

                    hiddenNotes =
                        document.createElement(
                            'input'
                        );


                    hiddenNotes.type =
                        'hidden';


                    hiddenNotes.name =
                        'submission_notes';


                    requisitionForm.appendChild(
                        hiddenNotes
                    );

                }


                hiddenNotes.value =
                    submissionNotes.value;


                requisitionForm.submit();

            }
        );


    /* ============================================================
       INITIAL STATE
    ============================================================ */

    const oldType =
        document.getElementById(
            'requisition_type'
        ).value;


    const oldSource =
        document.getElementById(
            'source_store_id'
        ).value;


    const oldDestination =
        document.getElementById(
            'destination_store_id'
        ).value;


    if (
        oldType &&
        oldSource
    ) {

        setupType.value =
            oldType;


        setupSourceStore.value =
            oldSource;


        setupDestinationStore.value =
            oldDestination;


        updateTransactionVisual();


        requisitionFormWrapper.style.display =
            'block';


        if (
            itemsBody.querySelectorAll(
                '.requisition-item-row'
            ).length === 0
        ) {

            addItemRow();

        }

    } else {

        transactionSetupModal.show();

    }

});

</script>

@endsection

