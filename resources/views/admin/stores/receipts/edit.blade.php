@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/stores.css') }}">
@endpush

@section('content')

<div class="store-requisition-page">

    <div class="requisition-page-header">
        <div class="requisition-header-content">
            <div class="requisition-header-icon">
                <i class="fas fa-truck-loading"></i>
            </div>

            <div>
                <div class="requisition-breadcrumb">
                    Stores
                    <span>/</span>
                    Receiving
                    <span>/</span>
                    Edit Receipt
                </div>

                <h1 class="requisition-page-title">
                    Edit Store Receipt
                </h1>

                <p class="requisition-page-subtitle">
                    Edit this draft receipt before posting it into store stock.
                </p>
            </div>
        </div>

        <div class="requisition-header-right">
            <a href="{{ route('admin.store-receipts.show', $storeReceipt) }}"
               class="requisition-add-button">
                <i class="fas fa-arrow-left me-1"></i>
                Back
            </a>
        </div>
    </div>

    <x-message></x-message>

    <div class="requisition-state-panel">
        <div class="requisition-state-main">
            <div class="requisition-state-icon">
                <i class="fas fa-pencil-alt"></i>
            </div>

            <div class="requisition-state-text">
                <strong>
                    Draft Receipt
                </strong>

                <span>
                    This receipt can still be edited before posting.
                </span>
            </div>
        </div>

        <div class="requisition-state-actions">
            <span class="requisition-status requisition-status-draft">
                DRAFT
            </span>
        </div>
    </div>

    <form method="POST"
          action="{{ route('admin.store-receipts.update', $storeReceipt) }}"
          id="receiptEditForm">

        @csrf
        @method('PUT')

        <div class="requisition-section">
            <div class="requisition-section-header">
                <div class="requisition-section-heading">

                    <div class="requisition-section-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>

                    <div>
                        <h5>
                            Receipt Information
                        </h5>

                        <p>
                            Basic information about this receiving transaction.
                        </p>
                    </div>

                </div>
            </div>

            <div class="p-3">
                <div class="row g-3">

                    <div class="col-md-3 mb-3">
                        <label class="requisition-field-label">
                            Receipt Number
                        </label>

                        <input type="text"
                               class="form-control requisition-input"
                               value="{{ $storeReceipt->receipt_number }}"
                               readonly>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="requisition-field-label">
                            Store <span class="text-danger">*</span>
                        </label>

                        <select name="store_id"
                                id="store_id"
                                class="form-select requisition-input"
                                required>

                            <option value="">
                                Select Store
                            </option>

                             @foreach($stores as $store)
                            @php
                            $receivePermission = 'RECEIVE ITEMS TO ' . strtoupper(trim($store->name));
                            @endphp

                            @can($receivePermission)
                            <option value="{{ $store->id }}" {{ old('store_id')==$store->id ? 'selected' : '' }}>
                                {{ $store->name }}
                            </option>
                            @endcan
                            @endforeach

                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="requisition-field-label">
                            Source <span class="text-danger">*</span>
                        </label>

                        <select name="source_type"
                                id="source_type"
                                class="form-select requisition-input"
                                required>

                            <option value="">
                                Select Source
                            </option>

                            <option value="PURCHASE"
                                {{ old('source_type', $storeReceipt->source_type) === 'PURCHASE' ? 'selected' : '' }}>
                                Purchase
                            </option>

                            <option value="DONATION"
                                {{ old('source_type', $storeReceipt->source_type) === 'DONATION' ? 'selected' : '' }}>
                                Donation
                            </option>

                            @can('RECEIVE ITEMS FROM FARM SUPPLIES')
    <option value="FARM_SUPPLIES"
        {{ old('source_type', $storeReceipt->source_type) === 'FARM_SUPPLIES' ? 'selected' : '' }}>
        Farm Supplies
    </option>
@endcan

@can('RECEIVE ITEMS FROM DAIRY SUPPLIES')
    <option value="DAIRY_SUPPLIES"
        {{ old('source_type', $storeReceipt->source_type) === 'DAIRY_SUPPLIES' ? 'selected' : '' }}>
        Dairy Supplies
    </option>
@endcan



                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label class="requisition-field-label">
                            Received Date <span class="text-danger">*</span>
                        </label>

                        <input type="date"
                               name="received_date"
                               id="received_date"
                               class="form-control requisition-input"
                               value="{{ old('received_date', $receivedDateValue) }}"
                               required>
                    </div>

                </div>
            </div>
        </div>

        <div class="requisition-section mt-3"
             id="purchaseSection">

            <div class="requisition-section-header">
                <div class="requisition-section-heading">

                    <div class="requisition-section-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>

                    <div>
                        <h5>
                            Purchase Information
                        </h5>

                        <p>
                            Select the supplier and LPO associated with this receipt.
                        </p>
                    </div>

                </div>
            </div>

            <div class="p-3">

                <div class="row g-3">

                    <div class="col-md-4 mb-3">

                        <label class="requisition-field-label">
                            Supplier <span class="text-danger">*</span>
                        </label>

                        <input type="hidden"
                               name="supplier_id"
                               id="supplier_id"
                               value="{{ old('supplier_id', $storeReceipt->supplier_id) }}">

                        <button type="button"
                                class="requisition-lpo-picker"
                                id="supplierPickerButton"
                                data-bs-toggle="modal"
                                data-bs-target="#supplierSelectionModal">

                            <span class="requisition-lpo-picker-icon">
                                <i class="fas fa-building"></i>
                            </span>

                            <span class="requisition-lpo-picker-text">

                                <strong id="supplierPickerTitle">
                                    Select Supplier
                                </strong>

                                <small id="supplierPickerSubtitle">
                                    Choose supplier
                                </small>

                            </span>

                            <span class="requisition-lpo-picker-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </span>

                        </button>

                        <div class="requisition-lpo-selected mt-2"
                             id="selectedSupplierCard"
                             style="display:none;">

                            <div class="requisition-lpo-selected-main">

                                <div class="requisition-lpo-selected-icon">
                                    <i class="fas fa-building"></i>
                                </div>

                                <div>

                                    <div class="requisition-lpo-selected-number"
                                         id="selectedSupplierName">
                                    </div>

                                    <div class="requisition-lpo-selected-meta"
                                         id="selectedSupplierMeta">
                                    </div>

                                </div>

                            </div>

                            <button type="button"
                                    class="requisition-lpo-change"
                                    id="changeSupplierButton">
                                Change
                            </button>

                        </div>

                    </div>

                    <div class="col-md-4 mb-3">

                        <label class="requisition-field-label">
                            LPO <span class="text-danger">*</span>
                        </label>

                        <input type="hidden"
                               name="store_lpo_id"
                               id="store_lpo_id"
                               value="{{ old('store_lpo_id', $storeReceipt->store_lpo_id) }}">

                        <button type="button"
                                class="requisition-lpo-picker"
                                id="lpoPickerButton"
                                data-bs-toggle="modal"
                                data-bs-target="#lpoSelectionModal">

                            <span class="requisition-lpo-picker-icon">
                                <i class="fas fa-file-invoice"></i>
                            </span>

                            <span class="requisition-lpo-picker-text">

                                <strong id="lpoPickerTitle">
                                    Select LPO
                                </strong>

                                <small id="lpoPickerSubtitle">
                                    Choose purchase order
                                </small>

                            </span>

                            <span class="requisition-lpo-picker-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </span>

                        </button>

                        <div class="requisition-lpo-selected mt-2"
                             id="selectedLpoCard"
                             style="display:none;">

                            <div class="requisition-lpo-selected-main">

                                <div class="requisition-lpo-selected-icon">
                                    <i class="fas fa-file-invoice"></i>
                                </div>

                                <div>

                                    <div class="requisition-lpo-selected-number"
                                         id="selectedLpoNumber">
                                    </div>

                                    <div class="requisition-lpo-selected-meta"
                                         id="selectedLpoMeta">
                                    </div>

                                </div>

                            </div>

                            <button type="button"
                                    class="requisition-lpo-change"
                                    id="changeLpoButton">
                                Change
                            </button>

                        </div>

                    </div>

                    <div class="col-md-4 mb-3">

                        <label class="requisition-field-label">
                            Delivery Reference
                        </label>

                        <input type="text"
                               name="supplier_reference"
                               class="form-control requisition-input"
                               value="{{ old('supplier_reference', $storeReceipt->supplier_reference) }}"
                               placeholder="Delivery note / invoice">

                    </div>

                </div>

                <div class="row g-3">

                    <div class="col-md-6 mb-3">

                        <label class="requisition-field-label">
                            Supplier Name
                        </label>

                        <input type="text"
                               name="supplier_name"
                               class="form-control requisition-input"
                               value="{{ old('supplier_name', $storeReceipt->supplier_name ?? '') }}"
                               placeholder="Optional supplier name">

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="requisition-field-label">
                            Receiving
                        </label>

                        <div class="requisition-notice requisition-notice-info mb-0">
                            <i class="fas fa-info-circle me-1"></i>
                            Select the LPO, then enter the quantity actually received for each item.
                        </div>

                    </div>

                </div>

            </div>
        </div>

        <div class="requisition-section mt-3"
             id="donationSection">

            <div class="requisition-section-header">
                <div class="requisition-section-heading">

                    <div class="requisition-section-icon">
                        <i class="fas fa-hand-holding-heart"></i>
                    </div>

                    <div>
                        <h5>
                            Donation Information
                        </h5>

                        <p>
                            Select the existing in-kind donation linked to this receipt.
                        </p>
                    </div>

                </div>
            </div>

            <div class="p-3">

                <div class="row g-3">

                    <div class="col-md-6 mb-3">

                        <label class="requisition-field-label">
                            Donation <span class="text-danger">*</span>
                        </label>

                        <input type="hidden"
                               name="donation_id"
                               id="donation_id"
                               value="{{ old('donation_id', $storeReceipt->donation_id) }}">

                        <button type="button"
                                class="requisition-lpo-picker"
                                id="donationPickerButton"
                                data-bs-toggle="modal"
                                data-bs-target="#donationSelectionModal">

                            <span class="requisition-lpo-picker-icon">
                                <i class="fas fa-hand-holding-heart"></i>
                            </span>

                            <span class="requisition-lpo-picker-text">

                                <strong id="donationPickerTitle">
                                    Select Donation
                                </strong>

                                <small id="donationPickerSubtitle">
                                    Choose donor / donation
                                </small>

                            </span>

                            <span class="requisition-lpo-picker-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </span>

                        </button>

                        <div class="requisition-lpo-selected mt-2"
                             id="selectedDonationCard"
                             style="display:none;">

                            <div class="requisition-lpo-selected-main">

                                <div class="requisition-lpo-selected-icon">
                                    <i class="fas fa-hand-holding-heart"></i>
                                </div>

                                <div>

                                    <div class="requisition-lpo-selected-number"
                                         id="selectedDonationNumber">
                                    </div>

                                    <div class="requisition-lpo-selected-meta"
                                         id="selectedDonationMeta">
                                    </div>

                                </div>

                            </div>

                            <button type="button"
                                    class="requisition-lpo-change"
                                    id="changeDonationButton">
                                Change
                            </button>

                        </div>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="requisition-field-label">
                            Donation Source
                        </label>

                        <div class="requisition-notice requisition-notice-info mb-0">
                            <i class="fas fa-link me-1"></i>
                            Linked to the existing donation record. Donation quantities cannot be changed here.
                        </div>

                    </div>

                </div>

            </div>
        </div>

        <div class="requisition-section mt-3">

            <div class="requisition-section-header">

                <div class="requisition-section-heading">

                    <div class="requisition-section-icon">
                        <i class="fas fa-boxes"></i>
                    </div>

                    <div>

                        <h5>
                            Items Received
                        </h5>

                        <p id="itemsSectionDescription">
                            Select a purchase LPO or donation to load the items.
                        </p>

                    </div>

                </div>

            </div>

            <div class="p-3">

                <div id="productionItemsControls"
     class="mb-3"
     style="display:none;">

    <div class="requisition-notice requisition-notice-info mb-3">
        <i class="fas fa-info-circle me-1"></i>
        Select items from the existing master item list, then enter the quantity received.
        Stock will only change when this receipt is posted.
    </div>

    <button type="button"
            class="requisition-add-button"
            id="openProductionItemModal"
            data-bs-toggle="modal"
            data-bs-target="#productionItemSelectionModal">
        <i class="fas fa-plus me-1"></i>
        Select Items
    </button>
</div>



                <div id="purchaseItemsNotice"
                     class="requisition-notice requisition-notice-info mb-3"
                     style="display:none;">

                    <i class="fas fa-info-circle me-1"></i>

                    For purchases, the LPO quantity is the quantity ordered.
                    Enter the quantity actually received.
                    For example, if the LPO says 9 and you received 4, enter 4.

                </div>

                <div id="donationItemsNotice"
                     class="requisition-notice requisition-notice-info mb-3"
                     style="display:none;">

                    <i class="fas fa-lock me-1"></i>

                    Donation quantities are taken directly from the donation record and cannot be edited here.

                </div>

                <div class="requisition-list-table-wrapper">

                    <table class="requisition-list-table"
                           id="receiptItemsTable">

                        <thead>

                            <tr>

                                <th style="width:40px;">
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

                                <th class="text-end">
                                    LPO Qty
                                </th>

                                <th class="text-end">
                                    Received Qty
                                </th>

                                <th>
                                    Notes
                                </th>

                                <th class="text-end">
                                    Action
                                </th>

                            </tr>

                        </thead>

                        <tbody id="receiptItemsBody">

                        </tbody>

                    </table>

                </div>

                <div id="itemsEmptyState"
                     class="requisition-table-empty mt-3">

                    <div class="requisition-empty-icon">
                        <i class="fas fa-box-open"></i>
                    </div>

                    <strong>
                        No items selected
                    </strong>

                    <div>
                        Select an LPO or donation above.
                    </div>

                </div>

            </div>

        </div>

        <div class="requisition-section mt-3">

            <div class="requisition-section-header">

                <div class="requisition-section-heading">

                    <div class="requisition-section-icon">
                        <i class="fas fa-sticky-note"></i>
                    </div>

                    <div>

                        <h5>
                            Notes
                        </h5>

                        <p>
                            Additional information about this receipt.
                        </p>

                    </div>

                </div>

            </div>

            <div class="p-3">

                <textarea name="notes"
                          rows="3"
                          class="form-control requisition-input"
                          placeholder="Optional receiving notes...">{{ old('notes', $storeReceipt->notes) }}</textarea>

            </div>

        </div>

        <div class="requisition-bottom-actions mt-3">

            <div class="requisition-bottom-actions-left">

            </div>

            <div class="requisition-bottom-actions-right">

                <a href="{{ route('admin.store-receipts.show', $storeReceipt) }}"
                   class="requisition-cancel-button">

                    <i class="fas fa-times"></i>
                    Cancel

                </a>

                <button type="submit"
                        class="requisition-add-button">

                    <i class="fas fa-save me-1"></i>
                    Save Changes

                </button>

            </div>

        </div>

    </form>

</div>


<div class="modal fade"
     id="supplierSelectionModal"
     tabindex="-1"
     aria-labelledby="supplierSelectionModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-scrollable">

        <div class="modal-content">

            <div class="modal-header">

                <div>

                    <h5 class="modal-title"
                        id="supplierSelectionModalLabel">

                        Select Supplier

                    </h5>

                    <small class="text-muted">
                        Select the supplier associated with this purchase receipt.
                    </small>

                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                </button>

            </div>

            <div class="modal-body">

                <div class="row g-2 mb-3">

                    <div class="col-md-8">

                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>

                            <input type="text"
                                   id="supplierSearch"
                                   class="form-control"
                                   placeholder="Search supplier name, code, contact, phone...">

                        </div>

                    </div>

                    <div class="col-md-4">

                        <select id="supplierStatusFilter"
                                class="form-select">

                            <option value="">
                                All Suppliers
                            </option>

                            <option value="active">
                                Active
                            </option>

                            <option value="inactive">
                                Inactive
                            </option>

                        </select>

                    </div>

                </div>

                <div class="requisition-list-table-wrapper">

                    <table class="requisition-list-table"
                           id="supplierSelectionTable">

                        <thead>

                            <tr>

                                <th>Supplier</th>
                                <th>Code</th>
                                <th>Contact</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($suppliers as $supplier)

                                <tr class="supplier-selection-row"
                                    data-supplier-id="{{ $supplier->id }}"
                                    data-name="{{ strtolower($supplier->name) }}"
                                    data-code="{{ strtolower($supplier->supplier_code ?? '') }}"
                                    data-contact="{{ strtolower($supplier->contact_person ?? '') }}"
                                    data-phone="{{ strtolower($supplier->phone ?? '') }}"
                                    data-email="{{ strtolower($supplier->email ?? '') }}"
                                    data-status="{{ $supplier->is_active ? 'active' : 'inactive' }}">

                                    <td>

                                        <strong>
                                            {{ $supplier->name }}
                                        </strong>

                                        @if($supplier->tax_pin)

                                            <div class="requisition-list-meta">
                                                PIN: {{ $supplier->tax_pin }}
                                            </div>

                                        @endif

                                    </td>

                                    <td>
                                        {{ $supplier->supplier_code ?? '—' }}
                                    </td>

                                    <td>
                                        {{ $supplier->contact_person ?: '—' }}
                                    </td>

                                    <td>
                                        {{ $supplier->phone ?: '—' }}
                                    </td>

                                    <td>
                                        {{ $supplier->email ?: '—' }}
                                    </td>

                                    <td>

                                        @if($supplier->is_active)

                                            <span class="requisition-list-status requisition-list-status-approved">

                                                <span class="requisition-list-status-dot"></span>

                                                Active

                                            </span>

                                        @else

                                            <span class="requisition-list-status requisition-list-status-cancelled">

                                                <span class="requisition-list-status-dot"></span>

                                                Inactive

                                            </span>

                                        @endif

                                    </td>

                                    <td class="text-end">

                                        <button type="button"
                                                class="requisition-add-button primary select-supplier-btn"
                                                data-supplier-id="{{ $supplier->id }}"
                                                data-supplier-name="{{ $supplier->name }}"
                                                data-supplier-code="{{ $supplier->supplier_code ?? '' }}"
                                                data-supplier-contact="{{ $supplier->contact_person ?? '' }}">

                                            <i class="fas fa-check me-1"></i>
                                            Select

                                        </button>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="7"
                                        class="requisition-table-empty">

                                        <div class="requisition-empty-icon">
                                            <i class="fas fa-building"></i>
                                        </div>

                                        <strong>
                                            No suppliers found
                                        </strong>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>


<div class="modal fade"
     id="productionItemSelectionModal"
     tabindex="-1"
     aria-labelledby="productionItemSelectionModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">

            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="productionItemSelectionModalLabel">
                        Select Master Items
                    </h5>
                    <small class="text-muted">
                        Select items received from Farm or Dairy production.
                    </small>
                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>

            <div class="modal-body">

                <div class="input-group mb-3">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>

                    <input type="search"
                           id="productionItemSearch"
                           class="form-control"
                           placeholder="Search item name or SKU...">
                </div>

                <div class="requisition-list-table-wrapper">
                    <table class="requisition-list-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>SKU</th>
                                <th>Unit</th>
                                <th>Variant</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>

                        <tbody id="productionItemSelectionBody">
                            @foreach($items as $item)
                                <tr class="production-item-option"
                                    data-item-id="{{ $item->id }}"
                                    data-search="{{ strtolower($item->name . ' ' . ($item->sku ?? '')) }}">

                                    <td>
                                        <strong>{{ $item->name }}</strong>
                                    </td>

                                    <td>{{ $item->sku ?: '—' }}</td>

                                    <td>
                                        {{ $item->unit->name ?? $item->unit->code ?? '—' }}
                                    </td>

                                    <td>
                                        <select class="form-select form-select-sm production-item-variant"
                                                aria-label="Variant for {{ $item->name }}">
                                            <option value="">Default / No variant</option>

                                            @foreach($item->variants as $variant)
                                                <option value="{{ $variant->id }}">
                                                    {{ $variant->name }}
                                                    {{ $variant->code ? ' (' . $variant->code . ')' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>

                                    <td class="text-end">
                                        <button type="button"
                                                class="requisition-list-action primary add-production-item"
                                                data-item-id="{{ $item->id }}">
                                            <i class="fas fa-plus me-1"></i>
                                            Add
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>


<div class="modal fade"
     id="lpoSelectionModal"
     tabindex="-1"
     aria-labelledby="lpoSelectionModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-scrollable">

        <div class="modal-content">

            <div class="modal-header">

                <div>

                    <h5 class="modal-title"
                        id="lpoSelectionModalLabel">

                        Select LPO

                    </h5>

                    <small class="text-muted">
                        Select an approved or partially received purchase order.
                    </small>

                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                </button>

            </div>

            <div class="modal-body">

                <div class="row g-2 mb-3">

                    <div class="col-md-7">

                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>

                            <input type="text"
                                   id="lpoSearch"
                                   class="form-control"
                                   placeholder="Search LPO number or supplier...">

                        </div>

                    </div>

                    <div class="col-md-2">

                        <select id="lpoStatusFilter"
                                class="form-select">

                            <option value="">
                                All Status
                            </option>

                            <option value="APPROVED">
                                Approved
                            </option>

                            <option value="PARTIALLY_RECEIVED">
                                Partially Received
                            </option>

                        </select>

                    </div>

                    <div class="col-md-3">

                        <select id="lpoSupplierFilter"
                                class="form-select">

                            <option value="">
                                All Suppliers
                            </option>

                            @foreach($suppliers as $supplier)

                                <option value="{{ $supplier->id }}">
                                    {{ $supplier->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>

                <div class="requisition-list-table-wrapper">

                    <table class="requisition-list-table"
                           id="lpoSelectionTable">

                        <thead>

                            <tr>

                                <th>Purchase Order</th>
                                <th>Supplier</th>
                                <th>Store</th>
                                <th>LPO Date</th>
                                <th>Expected</th>
                                <th>Items</th>
                                <th>Value</th>
                                <th>Status</th>
                                <th class="text-end">Action</th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($lpos as $lpo)

                                <tr class="lpo-selection-row"
                                    data-lpo-id="{{ $lpo->id }}"
                                    data-lpo-number="{{ strtolower($lpo->lpo_number) }}"
                                    data-supplier="{{ strtolower(optional($lpo->supplier)->name ?? '') }}"
                                    data-store="{{ strtolower(optional($lpo->store)->name ?? '') }}"
                                    data-supplier-id="{{ $lpo->supplier_id }}"
                                    data-store-id="{{ $lpo->store_id }}"
                                    data-status="{{ strtoupper($lpo->status) }}">

                                    <td>
                                        <strong>
                                            {{ $lpo->lpo_number }}
                                        </strong>
                                    </td>

                                    <td>
                                        {{ optional($lpo->supplier)->name ?? '—' }}
                                    </td>

                                    <td>
                                        {{ optional($lpo->store)->name ?? '—' }}
                                    </td>

                                    <td>
                                        {{ optional($lpo->lpo_date)->format('d M Y') }}
                                    </td>

                                    <td>
                                        {{ $lpo->expected_delivery_date
                                            ? $lpo->expected_delivery_date->format('d M Y')
                                            : '—' }}
                                    </td>

                                    <td>
                                        {{ $lpo->items->count() }}
                                    </td>

                                    <td>
                                        KES {{ number_format((float) $lpo->total, 2) }}
                                    </td>

                                    <td>

                                        @if($lpo->status === 'APPROVED')

                                            <span class="requisition-list-status requisition-list-status-approved">
                                                <span class="requisition-list-status-dot"></span>
                                                Approved
                                            </span>

                                        @elseif($lpo->status === 'PARTIALLY_RECEIVED')

                                            <span class="requisition-list-status requisition-list-status-pending">
                                                <span class="requisition-list-status-dot"></span>
                                                Partially Received
                                            </span>

                                        @else

                                            <span class="requisition-list-status requisition-list-status-draft">
                                                <span class="requisition-list-status-dot"></span>
                                                {{ $lpo->status }}
                                            </span>

                                        @endif

                                    </td>

                                    <td class="text-end">

                                        <button type="button"
                                                class="requisition-add-button primary select-lpo-btn"
                                                data-lpo-id="{{ $lpo->id }}"
                                                data-lpo-number="{{ $lpo->lpo_number }}"
                                                data-supplier-id="{{ $lpo->supplier_id }}"
                                                data-supplier-name="{{ optional($lpo->supplier)->name ?? '' }}"
                                                data-store-id="{{ $lpo->store_id }}"
                                                data-store-name="{{ optional($lpo->store)->name ?? '' }}"
                                                data-items="{{ $lpo->items->count() }}"
                                                data-status="{{ $lpo->status }}">

                                            <i class="fas fa-check me-1"></i>
                                            Select

                                        </button>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="9"
                                        class="requisition-table-empty">

                                        <div class="requisition-empty-icon">
                                            <i class="fas fa-file-invoice"></i>
                                        </div>

                                        <strong>
                                            No LPOs available
                                        </strong>

                                        <div>
                                            Approved or partially received LPOs will appear here.
                                        </div>

                                    </td>

                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>

<div class="modal fade"
     id="donationSelectionModal"
     tabindex="-1"
     aria-labelledby="donationSelectionModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-xl modal-dialog-scrollable">

        <div class="modal-content">

            <div class="modal-header">

                <div>

                    <h5 class="modal-title"
                        id="donationSelectionModalLabel">

                        Select Donation

                    </h5>

                    <small class="text-muted">
                        Select the existing donation that brought these goods into the store.
                    </small>

                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close">
                </button>

            </div>

            <div class="modal-body">

                {{-- ============================================================
                     SEARCH
                     ============================================================ --}}

                <div class="row g-2 mb-3">

                    <div class="col-md-12">

                        <div class="input-group">

                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>

                            <input type="text"
                                   id="donationSearch"
                                   class="form-control"
                                   placeholder="Search donor number, donor name or donation..."
                                   autocomplete="off">

                        </div>

                    </div>

                </div>

                {{-- ============================================================
                     TABLE
                     ============================================================ --}}

                <div class="requisition-list-table-wrapper">

                    <table class="requisition-list-table"
                           id="donationSelectionTable">

                        <thead>

                            <tr>

                                <th>Donation</th>
                                <th>Donor</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Classification</th>
                                <th class="text-end">Action</th>

                            </tr>

                        </thead>

                        <tbody id="donationSelectionTableBody">

                            @forelse($donations as $donation)

                                @php
                                    /*
                                     * A donation is considered already attached
                                     * when it has a Store Receipt.
                                     *
                                     * We use the already-loaded receipt relationship
                                     * if available.
                                     */
                                    $isAlreadyReceived = false;

                                    if (
                                        isset($usedDonationIds) &&
                                        in_array(
                                            (int) $donation->id,
                                            $usedDonationIds,
                                            true
                                        )
                                    ) {
                                        $isAlreadyReceived = true;
                                    }

                                    /*
                                     * Keep the current donation selectable while
                                     * editing its existing draft receipt.
                                     */
                                    if (
                                        isset($storeReceipt) &&
                                        $storeReceipt->donation_id &&
                                        (int) $storeReceipt->donation_id === (int) $donation->id
                                    ) {
                                        $isAlreadyReceived = false;
                                    }
                                @endphp

                                <tr class="donation-selection-row {{ $isAlreadyReceived ? 'donation-already-received' : '' }}"
                                    data-donation-id="{{ $donation->id }}"
                                    data-donation-number="{{ strtolower($donation->donation_number ?? '') }}"
                                    data-donor-number="{{ strtolower(optional($donation->donor)->donor_number ?? '') }}"
                                    data-donor-name="{{ strtolower(optional($donation->donor)->name ?? '') }}"
                                    data-type="{{ strtolower($donation->type ?? '') }}"
                                    data-classification="{{ strtolower($donation->classification ?? '') }}"
                                    data-date="{{ strtolower(optional($donation->donation_date)->format('d M Y') ?? '') }}"
                                    data-already-received="{{ $isAlreadyReceived ? '1' : '0' }}">

                                    {{-- Donation --}}

                                    <td>

                                        <strong>
                                            {{ $donation->donation_number ?? 'DON-' . $donation->id }}
                                        </strong>

                                        @if($isAlreadyReceived)

                                            <div class="requisition-list-meta text-danger">
                                                <i class="fas fa-lock me-1"></i>
                                                Already received into Stores
                                            </div>

                                        @endif

                                    </td>

                                    {{-- Donor --}}

                                    <td>

                                        @if($donation->donor)

                                            <strong>
                                                {{ $donation->donor->name }}
                                            </strong>

                                            <div class="requisition-list-meta">
                                                {{ $donation->donor->donor_number }}
                                            </div>

                                        @else

                                            <span class="text-muted">
                                                Anonymous / Not Specified
                                            </span>

                                        @endif

                                    </td>

                                    {{-- Type --}}

                                    <td>

                                        {{ ucfirst(
                                            str_replace(
                                                '_',
                                                ' ',
                                                $donation->type ?? '—'
                                            )
                                        ) }}

                                    </td>

                                    {{-- Date --}}

                                    <td>

                                        {{ optional($donation->donation_date)->format('d M Y') }}

                                    </td>

                                    {{-- Classification --}}

                                    <td>

                                        {{ $donation->classification ?? '—' }}

                                    </td>

                                    {{-- Action --}}

                                    <td class="text-end">

                                        @if($isAlreadyReceived)

                                            <button type="button"
                                                    class="requisition-list-action"
                                                    disabled
                                                    title="This donation has already been attached to a Store Receipt">

                                                <i class="fas fa-lock me-1"></i>
                                                Already Received

                                            </button>

                                        @else

<button type="button" class="requisition-add-button select-donation-btn"
                                        data-donation-id="{{ $donation->id }}"
                                        data-donation-number="{{ $donation->donation_number ?? 'DON-' . $donation->id }}"
                                        data-donor-name="{{ optional($donation->donor)->name ?? 'Anonymous / Not Specified' }}"
                                        data-donor-number="{{ optional($donation->donor)->donor_number ?? '' }}"
                                        data-type="{{ $donation->type ?? '' }}"
                                        data-date="{{ optional($donation->donation_date)->format('d M Y') }}">
                                        <i class="fas fa-check me-1"></i>
                                        Select
                                    </button>

                                        @endif

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="6"
                                        class="requisition-table-empty">

                                        <div class="requisition-empty-icon">
                                            <i class="fas fa-hand-holding-heart"></i>
                                        </div>

                                        <strong>
                                            No donations found
                                        </strong>

                                        <div>
                                            Donations recorded through the donations workflow will appear here.
                                        </div>

                                    </td>

                                </tr>

                            @endforelse

                            {{-- Search empty state --}}

                            <tr id="donationSearchEmpty"
                                style="display:none;">

                                <td colspan="6"
                                    class="requisition-table-empty">

                                    <div class="requisition-empty-icon">
                                        <i class="fas fa-search"></i>
                                    </div>

                                    <strong>
                                        No matching donations
                                    </strong>

                                    <div>
                                        Try a different donor number, donor name or donation number.
                                    </div>

                                </td>

                            </tr>

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>


{{-- ================================================================
     DONATION SEARCH
     ================================================================ --}}

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    const itemData = @json($itemData ?? []);
    const lpoData = @json($lpoData ?? []);
    const donationData = @json($donationData ?? []);
    const receiptItemData = @json($receiptItemData ?? []);
    const oldItems = @json(old('items', []));

    const originalSource = @json($storeReceipt->source_type);
    const originalDonationId = @json($storeReceipt->donation_id);
    const originalLpoId = @json($storeReceipt->store_lpo_id);
    const originalSupplierId = @json($storeReceipt->supplier_id);

    const sourceType = document.getElementById('source_type');

    const purchaseSection = document.getElementById('purchaseSection');
    const donationSection = document.getElementById('donationSection');
    const purchaseItemsNotice = document.getElementById('purchaseItemsNotice');
    const donationItemsNotice = document.getElementById('donationItemsNotice');
    const itemsSectionDescription = document.getElementById('itemsSectionDescription');
    const itemsBody = document.getElementById('receiptItemsBody');
    const itemsEmptyState = document.getElementById('itemsEmptyState');
    const storeSelect = document.getElementById('store_id');

    const supplierIdInput = document.getElementById('supplier_id');
    const lpoIdInput = document.getElementById('store_lpo_id');
    const donationIdInput = document.getElementById('donation_id');

    const productionItemsControls =
        document.getElementById('productionItemsControls');

    const supplierModalElement =
        document.getElementById('supplierSelectionModal');

    const lpoModalElement =
        document.getElementById('lpoSelectionModal');

    const donationModalElement =
        document.getElementById('donationSelectionModal');

    const supplierModal = supplierModalElement
        ? bootstrap.Modal.getOrCreateInstance(supplierModalElement)
        : null;

    const lpoModal = lpoModalElement
        ? bootstrap.Modal.getOrCreateInstance(lpoModalElement)
        : null;

    const donationModal = donationModalElement
        ? bootstrap.Modal.getOrCreateInstance(donationModalElement)
        : null;

    const supplierSearch = document.getElementById('supplierSearch');
    const supplierStatusFilter =
        document.getElementById('supplierStatusFilter');

    const lpoSearch = document.getElementById('lpoSearch');
    const lpoStatusFilter = document.getElementById('lpoStatusFilter');
    const lpoSupplierFilter =
        document.getElementById('lpoSupplierFilter');

    const donationSearch = document.getElementById('donationSearch');

    const productionItemSearch =
        document.getElementById('productionItemSearch');

    const productionItemSelectionModal =
        document.getElementById('productionItemSelectionModal');


    /*
    |--------------------------------------------------------------------------
    | Source helpers
    |--------------------------------------------------------------------------
    */

    function isProductionSource(source = sourceType.value) {
        return source === 'FARM_SUPPLIES' ||
               source === 'DAIRY_SUPPLIES';
    }


    /*
    |--------------------------------------------------------------------------
    | General helpers
    |--------------------------------------------------------------------------
    */

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }


    function formatNumber(value) {
        const number = parseFloat(value);

        if (Number.isNaN(number)) {
            return '0';
        }

        return Number.isInteger(number)
            ? String(number)
            : number.toFixed(2).replace(/\.?0+$/, '');
    }


    function getItem(id) {
        return itemData.find(function (item) {
            return String(item.id) === String(id);
        });
    }


    function getLpo(id) {
        return lpoData.find(function (lpo) {
            return String(lpo.id) === String(id);
        });
    }


    function getDonation(id) {
        return donationData.find(function (donation) {
            return String(donation.id) === String(id);
        });
    }


    function getVariant(item, variantId) {
        if (!item || !Array.isArray(item.variants)) {
            return null;
        }

        return item.variants.find(function (variant) {
            return String(variant.id) === String(variantId);
        }) || null;
    }


    /*
    |--------------------------------------------------------------------------
    | Selected supplier card
    |--------------------------------------------------------------------------
    */

    function selectedSupplierCard(id) {
        const button = document.querySelector(
            '.select-supplier-btn[data-supplier-id="' + id + '"]'
        );

        if (!button) {
            return;
        }

        const supplierName = button.dataset.supplierName || '';
        const supplierCode = button.dataset.supplierCode || '';
        const supplierContact = button.dataset.supplierContact || '';

        const title = document.getElementById('supplierPickerTitle');
        const subtitle = document.getElementById('supplierPickerSubtitle');
        const name = document.getElementById('selectedSupplierName');
        const meta = document.getElementById('selectedSupplierMeta');
        const card = document.getElementById('selectedSupplierCard');

        if (title) {
            title.textContent = supplierName || 'Select Supplier';
        }

        if (subtitle) {
            subtitle.textContent = supplierCode || 'Choose supplier';
        }

        if (name) {
            name.textContent = supplierName;
        }

        if (meta) {
            meta.textContent = supplierCode +
                (supplierContact ? ' · ' + supplierContact : '');
        }

        if (card) {
            card.style.display = 'flex';
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Selected LPO card
    |--------------------------------------------------------------------------
    */

    function selectedLpoCard(id) {
        const button = document.querySelector(
            '.select-lpo-btn[data-lpo-id="' + id + '"]'
        );

        if (!button) {
            return;
        }

        const lpoNumber = button.dataset.lpoNumber || '';
        const supplierName = button.dataset.supplierName || '';
        const storeName = button.dataset.storeName || '';
        const itemCount = button.dataset.items || '';
        const status = button.dataset.status || '';

        const title = document.getElementById('lpoPickerTitle');
        const subtitle = document.getElementById('lpoPickerSubtitle');
        const number = document.getElementById('selectedLpoNumber');
        const meta = document.getElementById('selectedLpoMeta');
        const card = document.getElementById('selectedLpoCard');

        if (title) {
            title.textContent = lpoNumber || 'Select LPO';
        }

        if (subtitle) {
            subtitle.textContent = supplierName || 'Choose purchase order';
        }

        if (number) {
            number.textContent = lpoNumber;
        }

        if (meta) {
            meta.textContent = supplierName +
                ' · ' + storeName +
                ' · ' + itemCount + ' item(s)' +
                ' · ' + status;
        }

        if (card) {
            card.style.display = 'flex';
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Selected donation card
    |--------------------------------------------------------------------------
    */

    function selectedDonationCard(id) {
        const button = document.querySelector(
            '.select-donation-btn[data-donation-id="' + id + '"]'
        );

        if (!button) {
            return;
        }

        const donationNumber = button.dataset.donationNumber || '';
        const donorName = button.dataset.donorName || '';
        const donorNumber = button.dataset.donorNumber || '';
        const type = button.dataset.type || '';
        const date = button.dataset.date || '';

        const title = document.getElementById('donationPickerTitle');
        const subtitle = document.getElementById('donationPickerSubtitle');
        const number = document.getElementById('selectedDonationNumber');
        const meta = document.getElementById('selectedDonationMeta');
        const card = document.getElementById('selectedDonationCard');

        if (title) {
            title.textContent = donationNumber || 'Select Donation';
        }

        if (subtitle) {
            subtitle.textContent = donorName || 'Choose donor / donation';
        }

        if (number) {
            number.textContent = donationNumber;
        }

        if (meta) {
            meta.textContent = donorName +
                (donorNumber ? ' · ' + donorNumber : '') +
                (type ? ' · ' + type : '') +
                (date ? ' · ' + date : '');
        }

        if (card) {
            card.style.display = 'flex';
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Clear selections
    |--------------------------------------------------------------------------
    */

    function clearPurchaseSelection() {
        supplierIdInput.value = '';
        lpoIdInput.value = '';

        const supplierCard =
            document.getElementById('selectedSupplierCard');

        const lpoCard =
            document.getElementById('selectedLpoCard');

        if (supplierCard) {
            supplierCard.style.display = 'none';
        }

        if (lpoCard) {
            lpoCard.style.display = 'none';
        }

        filterLpos();
    }


    function clearDonationSelection() {
        donationIdInput.value = '';

        const donationCard =
            document.getElementById('selectedDonationCard');

        if (donationCard) {
            donationCard.style.display = 'none';
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Filter LPOs
    |
    | An LPO must match the supplier selected on the receipt.
    | The supplier filter inside the LPO modal is an additional filter.
    |--------------------------------------------------------------------------
    */

    function filterLpos() {
        const search = (lpoSearch?.value || '')
            .toLowerCase()
            .trim();

        const status = (lpoStatusFilter?.value || '')
            .toUpperCase();

        const selectedSupplier = String(
            supplierIdInput?.value || ''
        );

        const modalSupplier = String(
            lpoSupplierFilter?.value || ''
        );

        let visibleRows = 0;

        document.querySelectorAll('.lpo-selection-row').forEach(function (row) {
            const searchable = [
                row.dataset.lpoNumber,
                row.dataset.supplier,
                row.dataset.store
            ].join(' ').toLowerCase();

            const rowSupplier = String(row.dataset.supplierId || '');
            const rowStatus = String(row.dataset.status || '').toUpperCase();

            const matchesSearch =
                !search || searchable.includes(search);

            const matchesStatus =
                !status || rowStatus === status;

            // Do not show any LPO until a supplier has been selected.
            const matchesSelectedSupplier =
                selectedSupplier !== '' &&
                rowSupplier === selectedSupplier;

            // Optional supplier filter inside the LPO modal.
            const matchesModalSupplier =
                !modalSupplier || rowSupplier === modalSupplier;

            const visible =
                matchesSearch &&
                matchesStatus &&
                matchesSelectedSupplier &&
                matchesModalSupplier;

            row.style.display = visible ? '' : 'none';

            if (visible) {
                visibleRows++;
            }
        });

        const emptyRow = document.getElementById('lpoSearchEmpty');

        if (emptyRow) {
            emptyRow.style.display = visibleRows === 0 ? '' : 'none';

            const message = emptyRow.querySelector('td');

            if (message) {
                if (!selectedSupplier) {
                    message.textContent =
                        'Select a supplier on the receipt before choosing an LPO.';
                } else {
                    message.textContent =
                        'No matching LPOs are available for this supplier.';
                }
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Update source sections
    |--------------------------------------------------------------------------
    */

    function updateSourceUI() {
        const source = sourceType.value;
        const isPurchase = source === 'PURCHASE';
        const isDonation = source === 'DONATION';
        const isProduction = isProductionSource(source);

        if (purchaseSection) {
            purchaseSection.style.display = isPurchase ? '' : 'none';
        }

        if (donationSection) {
            donationSection.style.display = isDonation ? '' : 'none';
        }

        if (purchaseItemsNotice) {
            purchaseItemsNotice.style.display = isPurchase ? '' : 'none';
        }

        if (donationItemsNotice) {
            donationItemsNotice.style.display = isDonation ? '' : 'none';
        }

        if (productionItemsControls) {
            productionItemsControls.style.display =
                isProduction ? '' : 'none';
        }

        if (isPurchase) {
            itemsSectionDescription.textContent =
                'Enter the quantities actually received against the selected LPO.';

            clearDonationSelection();

        } else if (isDonation) {
            itemsSectionDescription.textContent =
                'Donation items and quantities are taken directly from the donation record.';

            clearPurchaseSelection();

        } else if (isProduction) {
            itemsSectionDescription.textContent =
                'Select items from the master list and enter the quantities received from production.';

            clearDonationSelection();
            clearPurchaseSelection();

        } else {
            itemsSectionDescription.textContent =
                'Select a source to load or add the items.';
        }

        filterLpos();
    }


    /*
    |--------------------------------------------------------------------------
    | Render receipt item rows
    |--------------------------------------------------------------------------
    */

    function renderRows(rows) {
        itemsBody.innerHTML = '';

        if (!rows.length) {
            itemsEmptyState.style.display = '';
            return;
        }

        itemsEmptyState.style.display = 'none';

        rows.forEach(function (row, index) {
            const item = getItem(row.store_item_id);

            const variant = getVariant(
                item,
                row.variant_id
            );

            const isDonation = sourceType.value === 'DONATION';

            const lpoQuantity =
                row.lpo_quantity !== undefined &&
                row.lpo_quantity !== null
                    ? row.lpo_quantity
                    : '';

            const quantity =
                row.quantity !== undefined &&
                row.quantity !== null
                    ? row.quantity
                    : '';

            const itemName =
                item?.name || row.item_name || 'Item';

            const sku = item?.sku || '';
            const unit = item?.unit || row.unit || '';
            const variantName = variant?.name || row.variant_name || '—';
            const notes = row.notes || '';

            const lpoDisplay = isDonation
                ? '—'
                : formatNumber(lpoQuantity);

            const quantityInput = isDonation
                ? `
                    <input type="number"
                           class="form-control requisition-input text-end donation-quantity"
                           name="items[${index}][quantity]"
                           value="${escapeHtml(formatNumber(quantity))}"
                           readonly
                           tabindex="-1">
                  `
                : `
                    <input type="number"
                           step="0.01"
                           min="0"
                           class="form-control requisition-input text-end received-quantity"
                           name="items[${index}][quantity]"
                           value="${escapeHtml(quantity)}"
                           data-max="${escapeHtml(lpoQuantity)}"
                           required>
                  `;

            const hiddenFields = `
                <input type="hidden"
                       name="items[${index}][store_item_id]"
                       value="${escapeHtml(row.store_item_id || '')}">

                <input type="hidden"
                       name="items[${index}][variant_id]"
                       value="${escapeHtml(row.variant_id || '')}">

                <input type="hidden"
                       name="items[${index}][lpo_item_id]"
                       value="${escapeHtml(row.lpo_item_id || '')}">
            `;

            const notesInput = `
                <input type="text"
                       class="form-control requisition-input"
                       name="items[${index}][notes]"
                       value="${escapeHtml(notes)}"
                       placeholder="Optional">
            `;

            const action = isDonation
                ? `
                    <span class="requisition-list-meta">
                        <i class="fas fa-lock"></i>
                    </span>
                  `
                : `
                    <button type="button"
                            class="requisition-list-action remove-receipt-row">
                        <i class="fas fa-trash"></i>
                    </button>
                  `;

            const rowHtml = `
                <tr data-index="${index}">
                    <td>
                        ${index + 1}
                        ${hiddenFields}
                    </td>

                    <td>
                        <strong>${escapeHtml(itemName)}</strong>
                        ${
                            sku
                                ? `<div class="requisition-list-meta">
                                       SKU: ${escapeHtml(sku)}
                                   </div>`
                                : ''
                        }
                    </td>

                    <td>${escapeHtml(variantName)}</td>

                    <td>${escapeHtml(unit || '—')}</td>

                    <td class="text-end">
                        ${escapeHtml(lpoDisplay)}
                    </td>

                    <td class="text-end">
                        ${quantityInput}
                    </td>

                    <td>${notesInput}</td>

                    <td class="text-end">
                        ${action}
                    </td>
                </tr>
            `;

            itemsBody.insertAdjacentHTML('beforeend', rowHtml);
        });

        bindRowEvents();
    }


    /*
    |--------------------------------------------------------------------------
    | Receipt item row events
    |--------------------------------------------------------------------------
    */

    function bindRowEvents() {
        document.querySelectorAll('.remove-receipt-row')
            .forEach(function (button) {
                button.addEventListener('click', function () {
                    const row = this.closest('tr');

                    if (row) {
                        row.remove();
                    }

                    renumberRows();
                });
            });

        document.querySelectorAll('.received-quantity')
            .forEach(function (input) {
                input.addEventListener('input', function () {
                    const max = parseFloat(this.dataset.max);
                    const value = parseFloat(this.value);

                    if (
                        !Number.isNaN(max) &&
                        !Number.isNaN(value) &&
                        value > max
                    ) {
                        this.value = max;
                    }

                    if (value < 0) {
                        this.value = 0;
                    }
                });
            });
    }


    function renumberRows() {
        document.querySelectorAll('#receiptItemsBody tr')
            .forEach(function (row, index) {
                const numberCell = row.querySelector('td:first-child');

                if (numberCell) {
                    const hidden = numberCell.innerHTML
                        .replace(/^[\s\S]*?(?=<input)/, '');

                    numberCell.innerHTML = (index + 1) + hidden;
                }

                row.querySelectorAll('[name]').forEach(function (input) {
                    input.name = input.name.replace(
                        /items\[\d+\]/,
                        'items[' + index + ']'
                    );
                });
            });

        if (!document.querySelector('#receiptItemsBody tr')) {
            itemsEmptyState.style.display = '';
        }
    }


    /*
    |--------------------------------------------------------------------------
    | Load LPO items
    |--------------------------------------------------------------------------
    */

    function loadLpoItems(lpoId) {
        const lpo = getLpo(lpoId);

        if (!lpo) {
            renderRows([]);
            return;
        }

        const rows = (lpo.items || []).map(function (lpoItem) {
            const current = receiptItemData.find(function (receiptItem) {
                return String(receiptItem.lpo_item_id) ===
                    String(lpoItem.id);
            });

            return {
                store_item_id: lpoItem.store_item_id,
                variant_id: lpoItem.variant_id,

                quantity: current
                    ? current.quantity
                    : '',

                notes: current
                    ? current.notes
                    : '',

                lpo_item_id: lpoItem.id,
                lpo_quantity: lpoItem.quantity
            };
        });

        renderRows(rows);
    }


    /*
    |--------------------------------------------------------------------------
    | Load donation items
    |--------------------------------------------------------------------------
    */

    function loadDonationItems(donationId) {
        const donation = getDonation(donationId);

        if (!donation) {
            renderRows([]);
            return;
        }

        const rows = (donation.items || []).map(function (donationItem) {
            const current = receiptItemData.find(function (receiptItem) {
                return String(receiptItem.store_item_id) ===
                        String(donationItem.store_item_id) &&
                    String(receiptItem.variant_id || '') ===
                        String(donationItem.variant_id || '');
            });

            return {
                store_item_id: donationItem.store_item_id,
                variant_id: donationItem.variant_id,
                quantity: donationItem.quantity,

                notes: current
                    ? current.notes
                    : donationItem.notes || '',

                lpo_item_id: '',
                lpo_quantity: ''
            };
        });

        renderRows(rows);
    }


    /*
    |--------------------------------------------------------------------------
    | Load items already on the receipt
    |--------------------------------------------------------------------------
    */

    function loadInitialItems() {
        if (oldItems && Object.keys(oldItems).length) {
            const rows = Object.values(oldItems).map(function (row) {
                const matchingReceipt = receiptItemData.find(
                    function (receiptItem) {
                        return String(receiptItem.store_item_id) ===
                                String(row.store_item_id) &&
                            String(receiptItem.variant_id || '') ===
                                String(row.variant_id || '');
                    }
                );

                const lpo = getLpo(originalLpoId);
                let lpoQuantity = '';

                if (lpo && row.lpo_item_id) {
                    const lpoItem = (lpo.items || []).find(
                        function (item) {
                            return String(item.id) ===
                                String(row.lpo_item_id);
                        }
                    );

                    if (lpoItem) {
                        lpoQuantity = lpoItem.quantity;
                    }
                }

                return {
                    store_item_id: row.store_item_id,
                    variant_id: row.variant_id,

                    quantity: row.quantity ??
                        matchingReceipt?.quantity ??
                        '',

                    notes: row.notes ??
                        matchingReceipt?.notes ??
                        '',

                    lpo_item_id: row.lpo_item_id || '',
                    lpo_quantity: lpoQuantity
                };
            });

            renderRows(rows);
            return;
        }

        if (sourceType.value === 'PURCHASE' && originalLpoId) {
            loadLpoItems(originalLpoId);
            return;
        }

        if (sourceType.value === 'DONATION' && originalDonationId) {
            loadDonationItems(originalDonationId);
            return;
        }

        renderRows([]);
    }


    /*
    |--------------------------------------------------------------------------
    | Source change
    |--------------------------------------------------------------------------
    */

    sourceType.addEventListener('change', function () {
        updateSourceUI();
        renderRows([]);
    });


    /*
    |--------------------------------------------------------------------------
    | Supplier selection
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll('.select-supplier-btn')
        .forEach(function (button) {
            button.addEventListener('click', function () {
                const newSupplierId = String(
                    this.dataset.supplierId || ''
                );

                const currentSupplierId = String(
                    supplierIdInput.value || ''
                );

                // If the supplier changes, the previous LPO is no longer valid.
                if (newSupplierId !== currentSupplierId) {
                    lpoIdInput.value = '';

                    const lpoCard =
                        document.getElementById('selectedLpoCard');

                    if (lpoCard) {
                        lpoCard.style.display = 'none';
                    }

                    renderRows([]);
                }

                supplierIdInput.value = newSupplierId;

                selectedSupplierCard(newSupplierId);

                // Filter the LPO list immediately after supplier selection.
                filterLpos();

                if (supplierModal) {
                    supplierModal.hide();
                }
            });
        });


    /*
    |--------------------------------------------------------------------------
    | Change supplier
    |--------------------------------------------------------------------------
    */

    const changeSupplierButton =
        document.getElementById('changeSupplierButton');

    if (changeSupplierButton) {
        changeSupplierButton.addEventListener('click', function () {
            // Clear the previous LPO and its item rows before changing supplier.
            lpoIdInput.value = '';

            const lpoCard =
                document.getElementById('selectedLpoCard');

            if (lpoCard) {
                lpoCard.style.display = 'none';
            }

            renderRows([]);

            if (supplierModal) {
                supplierModal.show();
            }
        });
    }


    /*
    |--------------------------------------------------------------------------
    | LPO selection
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll('.select-lpo-btn')
        .forEach(function (button) {
            button.addEventListener('click', function () {
                const lpoId = this.dataset.lpoId;
                const supplierId = String(
                    this.dataset.supplierId || ''
                );
                const storeId = this.dataset.storeId;

                // Prevent selecting an LPO belonging to another supplier.
                if (
                    !supplierId ||
                    supplierId !== String(supplierIdInput.value || '')
                ) {
                    alert(
                        'Please select the supplier associated with this LPO first.'
                    );

                    filterLpos();
                    return;
                }

                lpoIdInput.value = lpoId;

                selectedLpoCard(lpoId);

                if (storeId && storeSelect) {
                    const option = Array.from(
                        storeSelect.options
                    ).find(function (option) {
                        return String(option.value) === String(storeId);
                    });

                    if (option) {
                        storeSelect.value = String(storeId);
                    }
                }

                loadLpoItems(lpoId);

                if (lpoModal) {
                    lpoModal.hide();
                }
            });
        });


    /*
    |--------------------------------------------------------------------------
    | Change LPO
    |--------------------------------------------------------------------------
    */

    const changeLpoButton =
        document.getElementById('changeLpoButton');

    if (changeLpoButton) {
        changeLpoButton.addEventListener('click', function () {
            if (!supplierIdInput.value) {
                alert('Please select a supplier before choosing an LPO.');
                return;
            }

            filterLpos();

            if (lpoModal) {
                lpoModal.show();
            }
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Donation selection
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll('.select-donation-btn')
        .forEach(function (button) {
            button.addEventListener('click', function () {
                const donationId = this.dataset.donationId;

                donationIdInput.value = donationId;

                selectedDonationCard(donationId);
                loadDonationItems(donationId);

                if (donationModal) {
                    donationModal.hide();
                }
            });
        });


    const changeDonationButton =
        document.getElementById('changeDonationButton');

    if (changeDonationButton) {
        changeDonationButton.addEventListener('click', function () {
            if (donationModal) {
                donationModal.show();
            }
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Supplier search and status filter
    |--------------------------------------------------------------------------
    */

    function filterSuppliers() {
        const search = (supplierSearch?.value || '')
            .toLowerCase()
            .trim();

        const status = (supplierStatusFilter?.value || '')
            .toLowerCase();

        document.querySelectorAll('.supplier-selection-row')
            .forEach(function (row) {
                const searchable = [
                    row.dataset.name,
                    row.dataset.code,
                    row.dataset.contact,
                    row.dataset.phone,
                    row.dataset.email
                ].join(' ').toLowerCase();

                const rowStatus = row.dataset.status || '';

                const matchesSearch =
                    !search || searchable.includes(search);

                const matchesStatus =
                    !status || rowStatus === status;

                row.style.display =
                    matchesSearch && matchesStatus ? '' : 'none';
            });
    }


    if (supplierSearch) {
        supplierSearch.addEventListener('input', filterSuppliers);
    }

    if (supplierStatusFilter) {
        supplierStatusFilter.addEventListener(
            'change',
            filterSuppliers
        );
    }


    /*
    |--------------------------------------------------------------------------
    | LPO search and filters
    |--------------------------------------------------------------------------
    */

    if (lpoSearch) {
        lpoSearch.addEventListener('input', filterLpos);
    }

    if (lpoStatusFilter) {
        lpoStatusFilter.addEventListener('change', filterLpos);
    }

    if (lpoSupplierFilter) {
        lpoSupplierFilter.addEventListener('change', filterLpos);
    }


    /*
    |--------------------------------------------------------------------------
    | Donation search
    |--------------------------------------------------------------------------
    */

    if (donationSearch) {
        donationSearch.addEventListener('input', function () {
            const search = this.value.toLowerCase().trim();
            const tableBody =
                document.getElementById('donationSelectionTableBody');

            const emptyRow =
                document.getElementById('donationSearchEmpty');

            if (!tableBody) {
                return;
            }

            const rows = tableBody.querySelectorAll(
                'tr.donation-selection-row'
            );

            let visibleRows = 0;

            rows.forEach(function (row) {
                const searchable = [
                    row.dataset.donationNumber,
                    row.dataset.donorNumber,
                    row.dataset.donorName,
                    row.dataset.type,
                    row.dataset.classification,
                    row.dataset.date,
                    row.cells[4]?.textContent || ''
                ].join(' ').toLowerCase();

                const matches =
                    !search || searchable.includes(search);

                row.style.display = matches ? '' : 'none';

                if (matches) {
                    visibleRows++;
                }
            });

            if (emptyRow) {
                emptyRow.style.display =
                    visibleRows === 0 && search !== '' ? '' : 'none';
            }
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Validate receipt before submitting
    |--------------------------------------------------------------------------
    */

    const receiptEditForm =
        document.getElementById('receiptEditForm');

    if (receiptEditForm) {
        receiptEditForm.addEventListener('submit', function (event) {
            const source = sourceType.value;

            const rows = Array.from(
                document.querySelectorAll('#receiptItemsBody tr')
            );

            if (!rows.length) {
                event.preventDefault();

                alert(
                    'Please select the LPO or donation and ensure there is at least one item.'
                );

                return;
            }


            if (source === 'PURCHASE') {
                if (!supplierIdInput.value) {
                    event.preventDefault();

                    alert('Please select a supplier.');

                    return;
                }

                if (!lpoIdInput.value) {
                    event.preventDefault();

                    alert('Please select an LPO.');

                    return;
                }

                const selectedLpo = getLpo(lpoIdInput.value);

                if (
                    !selectedLpo ||
                    String(selectedLpo.supplier_id) !==
                        String(supplierIdInput.value)
                ) {
                    event.preventDefault();

                    alert(
                        'The selected LPO does not belong to the selected supplier. Please select the correct LPO.'
                    );

                    return;
                }

                let invalid = false;

                document.querySelectorAll('.received-quantity')
                    .forEach(function (input) {
                        const value = parseFloat(input.value);
                        const max = parseFloat(input.dataset.max);

                        if (
                            Number.isNaN(value) ||
                            value <= 0 ||
                            (!Number.isNaN(max) && value > max)
                        ) {
                            invalid = true;
                        }
                    });

                if (invalid) {
                    event.preventDefault();

                    alert(
                        'Please enter a valid received quantity. It cannot be greater than the LPO quantity.'
                    );

                    return;
                }
            }


            if (source === 'DONATION') {
                if (!donationIdInput.value) {
                    event.preventDefault();

                    alert('Please select a donation.');

                    return;
                }

                const donation = getDonation(donationIdInput.value);

                if (!donation) {
                    event.preventDefault();

                    alert(
                        'The selected donation could not be found. Please select it again.'
                    );

                    return;
                }

                const donationItems = donation.items || [];

                if (rows.length !== donationItems.length) {
                    event.preventDefault();

                    alert(
                        'Donation items cannot be added or removed. They must match the selected donation.'
                    );

                    return;
                }

                let mismatch = false;

                rows.forEach(function (row, index) {
                    const quantityInput =
                        row.querySelector('.donation-quantity');

                    if (!quantityInput) {
                        mismatch = true;
                        return;
                    }

                    const receivedQuantity =
                        parseFloat(quantityInput.value);

                    const donationItem = donationItems[index];

                    if (!donationItem) {
                        mismatch = true;
                        return;
                    }

                    if (
                        receivedQuantity !==
                        parseFloat(donationItem.quantity)
                    ) {
                        mismatch = true;
                    }
                });

                if (mismatch) {
                    event.preventDefault();

                    alert(
                        'Donation quantities must remain exactly as recorded in the donation.'
                    );

                    return;
                }
            }
        });
    }


    /*
    |--------------------------------------------------------------------------
    | Initialise edit form
    |--------------------------------------------------------------------------
    */

    updateSourceUI();

    if (originalSupplierId) {
        selectedSupplierCard(originalSupplierId);
    }

    if (originalLpoId && sourceType.value === 'PURCHASE') {
        selectedLpoCard(originalLpoId);
    }

    if (originalDonationId && sourceType.value === 'DONATION') {
        selectedDonationCard(originalDonationId);
    }

    // Apply the selected supplier filter when the page first loads.
    filterLpos();

    loadInitialItems();

});

</script>

@endpush

@endsection