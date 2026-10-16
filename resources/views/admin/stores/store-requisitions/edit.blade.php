
@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/stores.css') }}">
@endpush

@section('content')

@php
    $status = strtolower(trim($storeRequisition->status ?? 'draft'));
    $isReturned = $status === 'returned';

    $requisitionType = old(
        'requisition_type',
        $storeRequisition->requisition_type ?? 'ITEM'
    );

    $sourceStoreId = old(
        'source_store_id',
        $storeRequisition->source_store_id
    );

    $destinationStoreId = old(
        'destination_store_id',
        $storeRequisition->destination_store_id
    );

    /*
    |--------------------------------------------------------------------------
    | VARIANTS FOR JAVASCRIPT
    |--------------------------------------------------------------------------
    */

    $variantData = $items->mapWithKeys(function ($item) {
        return [
            $item->id => $item->variants->map(function ($variant) {
                return [
                    'id' => $variant->id,
                    'name' => $variant->name,
                    'code' => $variant->code,
                ];
            })->values()->toArray(),
        ];
    });

    /*
    |--------------------------------------------------------------------------
    | PREFILL EXISTING ITEMS
    |--------------------------------------------------------------------------
    | Old submitted input takes priority after a validation failure.
    | Otherwise, load the saved items and their assigned children.
    |--------------------------------------------------------------------------
    */

    if (is_array(old('items'))) {
        $existingItems = collect(old('items'))->map(function ($item) {
            return [
                'store_item_id' => $item['store_item_id'] ?? '',
                'variant_id' => $item['variant_id'] ?? '',
                'requested_quantity' => $item['requested_quantity'] ?? '',
                'notes' => $item['notes'] ?? '',
                'child_ids' => array_values($item['child_ids'] ?? []),
            ];
        })->values();
    } else {
        $existingItems = $storeRequisition->items->map(function ($item) {
            return [
                'store_item_id' => $item->store_item_id,
                'variant_id' => $item->variant_id,
                'requested_quantity' => $item->requested_quantity,
                'notes' => $item->notes,
                'child_ids' => $item->children
                    ->pluck('id')
                    ->values()
                    ->toArray(),
            ];
        })->values();
    }
@endphp

<div class="container-fluid requisition-page store-requisition-page">

    {{-- =====================================================
         PAGE HEADER
         ====================================================== --}}

    <div class="requisition-page-header">
        <div>
            <div class="text-muted mb-1">
                <small>
                    <a href="{{ route('admin.stores.store-requisitions.index') }}">
                        Stores
                    </a>
                    /
                    <a href="{{ route('admin.stores.store-requisitions.index') }}">
                        Requisitions
                    </a>
                    / Edit
                </small>
            </div>

            <div class="requisition-title-row">
                <h1 class="requisition-page-title">
                    Edit Store Requisition
                </h1>

                <span class="requisition-status
                    {{ $isReturned
                        ? 'requisition-status-returned'
                        : 'requisition-status-draft' }}">
                    {{ $isReturned ? 'Returned' : ucfirst($status) }}
                </span>
            </div>

            <div class="requisition-number">
                Requisition No:
                <strong>{{ $storeRequisition->requisition_number }}</strong>
            </div>
        </div>

        <div class="requisition-page-actions">
            <a href="{{ route('admin.stores.store-requisitions.show', $storeRequisition) }}"
               class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-eye me-1"></i>
                View Requisition
            </a>

            <a href="{{ route('admin.stores.store-requisitions.index') }}"
               class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>
                Back to Requisitions
            </a>
        </div>
    </div>

    <x-message />

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Please correct the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if ($isReturned)
        <div class="requisition-notice requisition-notice-info">
            <i class="fas fa-undo-alt"></i>
            <div>
                <strong>This requisition was returned for correction.</strong>
                Please review the saved information, make the necessary changes,
                and select <strong>Resubmit Requisition</strong> to send it back
                through the HOD approval stage.
            </div>
        </div>
    @endif

    {{-- =====================================================
         EDIT FORM
         ====================================================== --}}

    <form id="requisitionForm"
          method="POST"
          action="{{ route('admin.stores.store-requisitions.update', $storeRequisition) }}">

        @csrf
        @method('PUT')

        {{-- =====================================================
             REQUEST SETUP
             ====================================================== --}}

        <section class="requisition-section">

            <div class="requisition-section-header">
                <div>
                    <div class="requisition-section-title">
                        <i class="fas fa-sliders-h me-1"></i>
                        Request Setup
                    </div>

                    <div class="requisition-section-subtitle">
                        Requisition type and stores involved.
                    </div>
                </div>
            </div>

            <div class="requisition-section-body">
                <div class="row g-3">

                    <div class="col-md-4">
                        <label for="requisition_type" class="form-label">
                            Request Type <span class="text-danger">*</span>
                        </label>

                        <select name="requisition_type"
                                id="requisition_type"
                                class="form-select @error('requisition_type') is-invalid @enderror"
                                required>
                            <option value="ITEM"
                                {{ $requisitionType === 'ITEM' ? 'selected' : '' }}>
                                Item Requisition
                            </option>

                            <option value="TRANSFER"
                                {{ $requisitionType === 'TRANSFER' ? 'selected' : '' }}>
                                Store Transfer
                            </option>
                        </select>

                        @error('requisition_type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="source_store_id" class="form-label">
                            Source Store <span class="text-danger">*</span>
                        </label>

                        <select name="source_store_id"
                                id="source_store_id"
                                class="form-select @error('source_store_id') is-invalid @enderror"
                                required>
                            <option value="">Select source store</option>

                            @foreach ($stores as $store)
                                <option value="{{ $store->id }}"
                                    {{ (string) $sourceStoreId === (string) $store->id ? 'selected' : '' }}>
                                    {{ $store->name }}
                                </option>
                            @endforeach
                        </select>

                        @error('source_store_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4" id="destinationStoreWrapper">
                        <label for="destination_store_id" class="form-label">
                            Destination Store
                            <span id="destinationRequired" class="text-danger">*</span>
                        </label>

                        <select name="destination_store_id"
                                id="destination_store_id"
                                class="form-select @error('destination_store_id') is-invalid @enderror">
                            <option value="">Select destination store</option>

                            @foreach ($stores as $store)
                                <option value="{{ $store->id }}"
                                    {{ (string) $destinationStoreId === (string) $store->id ? 'selected' : '' }}>
                                    {{ $store->name }}
                                </option>
                            @endforeach
                        </select>

                        <small class="text-muted">
                            Required only for a store transfer.
                        </small>

                        @error('destination_store_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>
        </section>

        {{-- =====================================================
             REQUESTED ITEMS
             ====================================================== --}}

        <section class="requisition-section">

            <div class="requisition-section-header">
                <div>
                    <div class="requisition-section-title">
                        <i class="fas fa-boxes me-1"></i>
                        Requested Items
                    </div>

                    <div class="requisition-section-subtitle">
                        Edit the items, variants, quantities and child assignments.
                    </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                    <span class="requisition-item-count">
                        <span id="itemCount">{{ $existingItems->count() }}</span>
                        item(s)
                    </span>

                    <button type="button"
                            id="addItemBtn"
                            class="btn btn-sm btn-primary">
                        <i class="fas fa-plus me-1"></i>
                        Add Item
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table requisition-table" id="itemsTable">
                    <thead>
                        <tr>
                            <th style="width:45px;">#</th>
                            <th style="min-width:210px;">Item</th>
                            <th style="min-width:160px;">Variant</th>
                            <th style="min-width:115px;">Quantity</th>
                            <th style="min-width:75px;">UOM</th>
                            <th style="min-width:160px;">For Child / Children</th>
                            <th style="min-width:160px;">Notes</th>
                            <th style="width:50px;"></th>
                        </tr>
                    </thead>

                    <tbody id="itemsContainer">
                        <tr id="emptyItemsRow"
                            style="{{ $existingItems->isEmpty() ? '' : 'display:none;' }}">
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-box-open d-block mb-2"></i>
                                No items added. Select Add Item to begin.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </section>

        {{-- =====================================================
             REQUEST DETAILS
             ====================================================== --}}

        <section class="requisition-section">

            <div class="requisition-section-header">
                <div>
                    <div class="requisition-section-title">
                        <i class="fas fa-clipboard-list me-1"></i>
                        Request Details
                    </div>

                    <div class="requisition-section-subtitle">
                        Department, purpose and supporting notes.
                    </div>
                </div>
            </div>

            <div class="requisition-section-body">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label for="department" class="form-label">
                            Department <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               name="department"
                               id="department"
                               class="form-control @error('department') is-invalid @enderror"
                               value="{{ old('department', $storeRequisition->department) }}"
                               maxlength="255"
                               required>

                        @error('department')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="purpose" class="form-label">Purpose</label>

                        <textarea name="purpose"
                                  id="purpose"
                                  rows="3"
                                  maxlength="2000"
                                  class="form-control @error('purpose') is-invalid @enderror">{{ old('purpose', $storeRequisition->purpose) }}</textarea>

                        @error('purpose')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="submission_notes" class="form-label">
                            Submission Notes
                        </label>

                        <textarea name="submission_notes"
                                  id="submission_notes"
                                  rows="2"
                                  maxlength="2000"
                                  class="form-control @error('submission_notes') is-invalid @enderror"
                                  placeholder="Additional information about this requisition...">{{ old('submission_notes', $storeRequisition->submission_notes) }}</textarea>

                        @error('submission_notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>
            </div>
        </section>

        {{-- =====================================================
             BOTTOM ACTIONS
             ====================================================== --}}

        <div class="requisition-bottom-actions">

            <div class="requisition-bottom-actions-left">
                <a href="{{ route('admin.stores.store-requisitions.show', $storeRequisition) }}"
                   class="btn btn-outline-secondary">
                    <i class="fas fa-times me-1"></i>
                    Cancel
                </a>
            </div>

            <div class="requisition-bottom-actions-right">
                <button type="submit"
                        id="saveRequisitionBtn"
                        class="btn btn-primary">
                    <i class="fas {{ $isReturned ? 'fa-paper-plane' : 'fa-save' }} me-1"></i>

                    <span>
                        {{ $isReturned ? 'Resubmit Requisition' : 'Save Changes' }}
                    </span>
                </button>
            </div>

        </div>

    </form>
</div>


{{-- =========================================================
     ITEM ROW TEMPLATE
     ========================================================= --}}

<template id="itemRowTemplate">
    <tr class="requisition-item-row">

        <td class="text-center">
            <span class="row-number"></span>
        </td>

        <td>
            <select class="form-select item-select"
                    data-field="store_item_id"
                    required>
                <option value="">Select item</option>

                @foreach ($items as $item)
                    <option value="{{ $item->id }}"
                            data-uom="{{ $item->unit?->name ?? '—' }}">
                        {{ $item->name }}
                        @if ($item->sku)
                            ({{ $item->sku }})
                        @endif
                    </option>
                @endforeach
            </select>
        </td>

        <td>
            <select class="form-select variant-select"
                    data-field="variant_id">
                <option value="">No variant</option>
            </select>
        </td>

        <td>
            <input type="number"
                   class="form-control quantity-input"
                   data-field="requested_quantity"
                   min="0.001"
                   step="0.001"
                   required>
        </td>

        <td>
            <span class="requisition-uom uom-display">—</span>
        </td>

        <td>
            <button type="button"
                    class="requisition-child-button assign-children-btn">
                <i class="fas fa-users me-1"></i>
                Assign Children
            </button>

            <div class="requisition-child-summary child-summary">
                General
            </div>

            <div class="requisition-child-badges child-badges"></div>

            <div class="child-inputs"></div>
        </td>

        <td>
            <input type="text"
                   class="form-control notes-input"
                   data-field="notes"
                   maxlength="1000"
                   placeholder="Optional notes">
        </td>

        <td class="text-center">
            <button type="button"
                    class="btn btn-sm btn-outline-danger remove-item-btn"
                    title="Remove item">
                <i class="fas fa-trash"></i>
            </button>
        </td>

    </tr>
</template>


{{-- =========================================================
     CHILD ASSIGNMENT MODAL
     ========================================================= --}}

<div class="modal fade"
     id="assignChildrenModal"
     tabindex="-1"
     aria-labelledby="assignChildrenModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <div>
                    <h5 class="modal-title" id="assignChildrenModalLabel">
                        Assign Children
                    </h5>

                    <small class="text-muted">
                        Select the children this item is intended for.
                    </small>
                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>

            <div class="modal-body">

                <div class="mb-3">
                    <input type="search"
                           id="childSearch"
                           class="form-control"
                           placeholder="Search children by name...">
                </div>

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="text-muted">
                        <span id="selectedChildrenCount">0</span> selected
                    </small>

                    <button type="button"
                            id="clearChildrenBtn"
                            class="btn btn-sm btn-outline-secondary">
                        Clear Selection
                    </button>
                </div>

                <div class="requisition-child-list" id="childrenList">

                    @foreach ($children as $child)
                        <label class="requisition-child-option child-option"
                               data-child-name="{{ strtolower($child->name) }}">

                            <input type="checkbox"
                                   class="form-check-input child-checkbox"
                                   value="{{ $child->id }}"
                                   data-name="{{ $child->name }}">

                            <span>
                                <span class="requisition-child-option-name d-block">
                                    {{ $child->name }}
                                </span>

                                @if (!empty($child->admission_number))
                                    <span class="requisition-child-option-meta">
                                        {{ $child->admission_number }}
                                    </span>
                                @endif
                            </span>
                        </label>
                    @endforeach

                    <div id="noChildrenFound"
                         class="text-center text-muted p-3"
                         style="display:none;">
                        No children match your search.
                    </div>

                </div>

            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-outline-secondary"
                        data-bs-dismiss="modal">
                    Cancel
                </button>

                <button type="button"
                        id="assignSelectedChildrenBtn"
                        class="btn btn-primary">
                    <i class="fas fa-check me-1"></i>
                    Apply Selection
                </button>
            </div>

        </div>
    </div>
</div>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    'use strict';

    const variantData = @json($variantData);
    const existingItems = @json($existingItems);

    const itemsContainer = document.getElementById('itemsContainer');
    const emptyItemsRow = document.getElementById('emptyItemsRow');
    const itemRowTemplate = document.getElementById('itemRowTemplate');

    const addItemBtn = document.getElementById('addItemBtn');
    const itemCount = document.getElementById('itemCount');

    const requisitionType = document.getElementById('requisition_type');
    const sourceStore = document.getElementById('source_store_id');
    const destinationStore = document.getElementById('destination_store_id');
    const destinationStoreWrapper = document.getElementById('destinationStoreWrapper');
    const destinationRequired = document.getElementById('destinationRequired');

    const requisitionForm = document.getElementById('requisitionForm');
    const saveButton = document.getElementById('saveRequisitionBtn');

    const modalElement = document.getElementById('assignChildrenModal');
    const childSearch = document.getElementById('childSearch');
    const childrenList = document.getElementById('childrenList');
    const noChildrenFound = document.getElementById('noChildrenFound');
    const selectedChildrenCount = document.getElementById('selectedChildrenCount');

    let rowIndex = 0;
    let activeRow = null;
    let modalInstance = null;

    if (window.bootstrap && modalElement) {
        modalInstance = bootstrap.Modal.getOrCreateInstance(modalElement);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE EMPTY STATE AND ROW NUMBERS
    |--------------------------------------------------------------------------
    */

    function updateItemRows() {
        const rows = itemsContainer.querySelectorAll('.requisition-item-row');

        rows.forEach(function (row, index) {
            row.querySelector('.row-number').textContent = index + 1;
        });

        itemCount.textContent = rows.length;
        emptyItemsRow.style.display = rows.length ? 'none' : '';

        return rows;
    }

    /*
    |--------------------------------------------------------------------------
    | REQUEST TYPE / DESTINATION STORE
    |--------------------------------------------------------------------------
    */

    function updateDestinationStore() {
        const isTransfer = requisitionType.value === 'TRANSFER';

        destinationStoreWrapper.style.display = isTransfer ? '' : 'none';
        destinationStore.required = isTransfer;
        destinationRequired.style.display = isTransfer ? '' : 'none';

        if (!isTransfer) {
            destinationStore.value = '';
        }
    }

    function validateDifferentStores() {
        const isTransfer = requisitionType.value === 'TRANSFER';

        destinationStore.setCustomValidity('');

        if (
            isTransfer &&
            sourceStore.value &&
            destinationStore.value &&
            sourceStore.value === destinationStore.value
        ) {
            destinationStore.setCustomValidity(
                'The source and destination stores must be different.'
            );
        }
    }

    requisitionType.addEventListener('change', updateDestinationStore);

    sourceStore.addEventListener('change', validateDifferentStores);
    destinationStore.addEventListener('change', validateDifferentStores);

    /*
    |--------------------------------------------------------------------------
    | VARIANTS
    |--------------------------------------------------------------------------
    */

    function populateVariants(row, selectedVariantId) {
        const itemSelect = row.querySelector('.item-select');
        const variantSelect = row.querySelector('.variant-select');

        const itemId = itemSelect.value;
        const variants = variantData[itemId] || [];

        variantSelect.innerHTML = '';

        const noVariantOption = document.createElement('option');
        noVariantOption.value = '';
        noVariantOption.textContent = 'No variant';
        variantSelect.appendChild(noVariantOption);

        variants.forEach(function (variant) {
            const option = document.createElement('option');

            option.value = variant.id;

            option.textContent = variant.code
                ? variant.name + ' (' + variant.code + ')'
                : variant.name;

            if (
                selectedVariantId !== null &&
                selectedVariantId !== undefined &&
                String(selectedVariantId) === String(variant.id)
            ) {
                option.selected = true;
            }

            variantSelect.appendChild(option);
        });

        if (
            selectedVariantId &&
            !variants.some(function (variant) {
                return String(variant.id) === String(selectedVariantId);
            })
        ) {
            variantSelect.value = '';
        }
    }

    /*
    |--------------------------------------------------------------------------
    | UNIT OF MEASURE
    |--------------------------------------------------------------------------
    */

    function updateUom(row) {
        const itemSelect = row.querySelector('.item-select');
        const selectedOption = itemSelect.options[itemSelect.selectedIndex];
        const uomDisplay = row.querySelector('.uom-display');

        uomDisplay.textContent =
            selectedOption && selectedOption.dataset.uom
                ? selectedOption.dataset.uom
                : '—';
    }

    /*
    |--------------------------------------------------------------------------
    | CHILD ASSIGNMENTS
    |--------------------------------------------------------------------------
    */

    function getSelectedChildren(row) {
        const inputs = row.querySelectorAll(
            '.child-inputs input[name$="[child_ids][]"]'
        );

        return Array.from(inputs).map(function (input) {
            return String(input.value);
        });
    }

    function renderChildAssignments(row, childIds) {
        const inputContainer = row.querySelector('.child-inputs');
        const badgeContainer = row.querySelector('.child-badges');
        const summary = row.querySelector('.child-summary');

        inputContainer.innerHTML = '';
        badgeContainer.innerHTML = '';

        const uniqueIds = Array.from(
            new Set((childIds || []).map(String))
        );

        uniqueIds.forEach(function (childId) {
            const hiddenInput = document.createElement('input');

            hiddenInput.type = 'hidden';
            hiddenInput.name = 'items[' + row.dataset.index + '][child_ids][]';
            hiddenInput.value = childId;

            inputContainer.appendChild(hiddenInput);

            const checkbox = Array.from(
                modalElement.querySelectorAll('.child-checkbox')
            ).find(function (element) {
                return String(element.value) === childId;
            });

            if (checkbox) {
                const badge = document.createElement('span');
                badge.className = 'requisition-child-badge';
                badge.textContent = checkbox.dataset.name || 'Selected child';

                badgeContainer.appendChild(badge);
            }
        });

        if (uniqueIds.length === 0) {
            summary.textContent = 'General';
        } else if (uniqueIds.length === 1) {
            summary.textContent = '1 child assigned';
        } else {
            summary.textContent = uniqueIds.length + ' children assigned';
        }
    }

    function updateModalSelectedCount() {
        const count = modalElement.querySelectorAll(
            '.child-checkbox:checked'
        ).length;

        selectedChildrenCount.textContent = count;
    }

    function filterChildren() {
        const query = childSearch.value.trim().toLowerCase();
        let visibleCount = 0;

        modalElement.querySelectorAll('.child-option').forEach(function (option) {
            const name = option.dataset.childName || '';
            const visible = name.includes(query);

            option.style.display = visible ? '' : 'none';

            if (visible) {
                visibleCount++;
            }
        });

        noChildrenFound.style.display = visibleCount ? 'none' : '';
    }

    function openChildAssignment(row) {
        if (!modalInstance) {
            alert('The child assignment modal could not be opened. Please check that Bootstrap JavaScript is loaded.');
            return;
        }

        activeRow = row;

        const assignedIds = getSelectedChildren(row);

        modalElement.querySelectorAll('.child-checkbox').forEach(function (checkbox) {
            checkbox.checked = assignedIds.includes(String(checkbox.value));
        });

        childSearch.value = '';
        filterChildren();
        updateModalSelectedCount();

        modalInstance.show();
    }

    childSearch.addEventListener('input', filterChildren);

    modalElement.addEventListener('change', function (event) {
        if (event.target.classList.contains('child-checkbox')) {
            updateModalSelectedCount();
        }
    });

    document.getElementById('clearChildrenBtn').addEventListener('click', function () {
        modalElement.querySelectorAll('.child-checkbox').forEach(function (checkbox) {
            checkbox.checked = false;
        });

        updateModalSelectedCount();
    });

    document.getElementById('assignSelectedChildrenBtn').addEventListener('click', function () {
        if (!activeRow) {
            return;
        }

        const selectedIds = Array.from(
            modalElement.querySelectorAll('.child-checkbox:checked')
        ).map(function (checkbox) {
            return String(checkbox.value);
        });

        renderChildAssignments(activeRow, selectedIds);

        if (modalInstance) {
            modalInstance.hide();
        }
    });

    /*
    |--------------------------------------------------------------------------
    | ADD AN ITEM ROW
    |--------------------------------------------------------------------------
    */

    function addItemRow(data) {
        data = data || {};

        const fragment = itemRowTemplate.content.cloneNode(true);
        const row = fragment.querySelector('.requisition-item-row');

        row.dataset.index = rowIndex;

        const itemSelect = row.querySelector('.item-select');
        const variantSelect = row.querySelector('.variant-select');
        const quantityInput = row.querySelector('.quantity-input');
        const notesInput = row.querySelector('.notes-input');

        itemSelect.name = 'items[' + rowIndex + '][store_item_id]';
        variantSelect.name = 'items[' + rowIndex + '][variant_id]';
        quantityInput.name = 'items[' + rowIndex + '][requested_quantity]';
        notesInput.name = 'items[' + rowIndex + '][notes]';

        itemSelect.value = data.store_item_id || '';

        populateVariants(row, data.variant_id || '');
        updateUom(row);

        quantityInput.value =
            data.requested_quantity !== null &&
            data.requested_quantity !== undefined
                ? data.requested_quantity
                : '';

        notesInput.value = data.notes || '';

        renderChildAssignments(row, data.child_ids || []);

        itemSelect.addEventListener('change', function () {
            populateVariants(row, '');
            updateUom(row);
        });

        row.querySelector('.assign-children-btn').addEventListener('click', function () {
            openChildAssignment(row);
        });

        row.querySelector('.remove-item-btn').addEventListener('click', function () {
            row.remove();
            updateItemRows();
        });

        itemsContainer.appendChild(fragment);

        rowIndex++;

        updateItemRows();
    }

    addItemBtn.addEventListener('click', function () {
        addItemRow({
            store_item_id: '',
            variant_id: '',
            requested_quantity: '',
            notes: '',
            child_ids: []
        });
    });

    /*
    |--------------------------------------------------------------------------
    | RESTORE SAVED ROWS
    |--------------------------------------------------------------------------
    */

    if (Array.isArray(existingItems) && existingItems.length > 0) {
        existingItems.forEach(function (item) {
            addItemRow(item);
        });
    } else {
        addItemRow();
    }

    /*
    |--------------------------------------------------------------------------
    | FORM VALIDATION
    |--------------------------------------------------------------------------
    */

    requisitionForm.addEventListener('submit', function (event) {
        const rows = updateItemRows();

        validateDifferentStores();

        if (rows.length === 0) {
            event.preventDefault();
            alert('Please add at least one item to the requisition.');
            return;
        }

        if (
            requisitionType.value === 'TRANSFER' &&
            !destinationStore.value
        ) {
            event.preventDefault();
            destinationStore.reportValidity();
            return;
        }

        if (
            requisitionType.value === 'TRANSFER' &&
            sourceStore.value === destinationStore.value
        ) {
            event.preventDefault();
            destinationStore.setCustomValidity(
                'The source and destination stores must be different.'
            );
            destinationStore.reportValidity();
            return;
        }

        for (const row of rows) {
            const itemSelect = row.querySelector('.item-select');
            const quantityInput = row.querySelector('.quantity-input');

            if (!itemSelect.value) {
                event.preventDefault();
                itemSelect.reportValidity();
                return;
            }

            if (
                !quantityInput.value ||
                Number(quantityInput.value) < 0.001
            ) {
                event.preventDefault();
                quantityInput.reportValidity();
                return;
            }
        }

        if (saveButton) {
            saveButton.disabled = true;

            const label = saveButton.querySelector('span');

            if (label) {
                label.textContent = 'Saving...';
            }
        }
    });

    /*
    |--------------------------------------------------------------------------
    | INITIAL DISPLAY
    |--------------------------------------------------------------------------
    */

    updateDestinationStore();
    validateDifferentStores();
    updateItemRows();
});
</script>
@endpush

@endsection
