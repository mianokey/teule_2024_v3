@extends('layouts.admin')

@php
    $lpoItemData = $items->map(function ($item) {
        return [
            'id' => $item->id,
            'name' => $item->name,
            'unit' => $item->unit->name ?? '',
            'variants' => $item->variants->map(function ($variant) {
                return [
                    'id' => $variant->id,
                    'name' => $variant->name,
                ];
            })->values()->all(),
        ];
    })->values()->all();

    $existingLpoItems = $storeLpo->items->map(function ($line) {
        return [
            'store_item_id' => $line->store_item_id,
            'variant_id' => $line->variant_id,
            'description' => $line->description,
            'notes' => $line->notes,
            'ordered_quantity' => $line->ordered_quantity,
            'unit_price' => $line->unit_price,
            'discount' => $line->discount,
            'tax' => $line->tax,
        ];
    })->values()->all();

    $oldLpoItems = old('items', $existingLpoItems);

    if (!is_array($oldLpoItems)) {
        $oldLpoItems = [];
    }
@endphp


@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/stores.css') }}">

<style>
    /* Restrict the height changes to the item-selection modal only. */
    #itemSelectionModal .modal-dialog {
        max-height: 88vh;
    }

    #itemSelectionModal .modal-content {
        max-height: 88vh;
    }

    #itemSelectionModal .modal-body {
        overflow-y: auto;
        min-height: 0;
    }

    #itemSelectionModal .requisition-list-table-wrapper {
        max-height: 42vh;
        overflow-y: auto;
    }

    #itemSelectionModal #itemSelectionTable tbody tr {
        cursor: pointer;
    }
</style>
@endpush

@section('content')

<div class="store-requisition-page">

    <div class="requisition-page-header">
        <div class="requisition-header-content">
            <div class="requisition-header-icon">
                <i class="fas fa-file-invoice"></i>
            </div>

            <div>
                <div class="requisition-breadcrumb">
                    <span>Stores</span>
                    <i class="fas fa-chevron-right"></i>
                    <span>Purchase Orders</span>
                    <i class="fas fa-chevron-right"></i>
                    <span>Edit LPO</span>
                </div>

                <h1 class="requisition-page-title">
                    Edit LPO
                </h1>

                <p class="requisition-page-subtitle">
                    Update the supplier, store, items and purchase order details.
                </p>
            </div>
        </div>

        <div class="requisition-header-right">
            <a
                href="{{ route('admin.store-lpos.index') }}"
                class="requisition-cancel-button"
            >
                <i class="fas fa-arrow-left"></i>
                <span>Back</span>
            </a>
        </div>
    </div>

    <x-message></x-message>

    @if($errors->any())
        <div class="requisition-state-panel requisition-notice-danger mb-3">
            <div class="requisition-state-main">
                <div class="requisition-state-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>

                <div class="requisition-state-text">
                    <strong>Please correct the following errors:</strong>

                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <form
        method="POST"
        action="{{ route('admin.store-lpos.update', $storeLpo->id) }}"
        id="lpoForm"
    >
        @csrf
        @method('PUT')

        {{-- LPO DETAILS --}}
        <div class="requisition-section">
            <div class="requisition-section-header">
                <div class="requisition-section-heading">
                    <div class="requisition-section-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>

                    <div>
                        <h5>LPO Details</h5>
                        <p>Update the supplier, store and purchase order dates.</p>
                    </div>
                </div>
            </div>

            <div class="requisition-section-body">
                <div class="row">

                    <div class="col-md-3">
                        <div class="requisition-detail-item">
                            <label class="requisition-detail-label">
                                Supplier <span class="text-danger">*</span>
                            </label>

                            <select
                                name="supplier_id"
                                class="form-control requisition-input"
                                required
                            >
                                <option value="">Select supplier</option>

                                @foreach($suppliers as $supplier)
                                    <option
                                        value="{{ $supplier->id }}"
                                        {{ old('supplier_id', $storeLpo->supplier_id) == $supplier->id ? 'selected' : '' }}
                                    >
                                        {{ $supplier->name }}
                                        @if($supplier->supplier_code)
                                            — {{ $supplier->supplier_code }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="requisition-detail-item">
                            <label class="requisition-detail-label">
                                Store <span class="text-danger">*</span>
                            </label>

                            <select
                                name="store_id"
                                class="form-control requisition-input"
                                required
                            >
                                <option value="">Select store</option>

                                @foreach($stores as $store)
                                    <option
                                        value="{{ $store->id }}"
                                        {{ old('store_id', $storeLpo->store_id) == $store->id ? 'selected' : '' }}
                                    >
                                        {{ $store->name }}
                                        @if($store->code)
                                            — {{ $store->code }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="requisition-detail-item">
                            <label class="requisition-detail-label">
                                LPO Date <span class="text-danger">*</span>
                            </label>

                            <input
                                type="date"
                                name="lpo_date"
                                value="{{ old('lpo_date', $storeLpo->lpo_date ? \Illuminate\Support\Carbon::parse($storeLpo->lpo_date)->format('Y-m-d') : now()->format('Y-m-d')) }}"
                                class="form-control requisition-input"
                                required
                            >
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="requisition-detail-item">
                            <label class="requisition-detail-label">
                                Expected Delivery Date
                            </label>

                            <input
                                type="date"
                                name="expected_delivery_date"
                                value="{{ old('expected_delivery_date', $storeLpo->expected_delivery_date ? \Illuminate\Support\Carbon::parse($storeLpo->expected_delivery_date)->format('Y-m-d') : '') }}"
                                class="form-control requisition-input"
                            >
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="requisition-detail-item">
                            <label class="requisition-detail-label">Notes</label>

                            <textarea
                                name="notes"
                                rows="3"
                                class="form-control requisition-input"
                                placeholder="Optional notes or purchasing instructions..."
                            >{{ old('notes', $storeLpo->notes) }}</textarea>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- LPO ITEMS --}}
        <div class="requisition-section">
            <div class="requisition-section-header">
                <div class="requisition-section-heading">
                    <div class="requisition-section-icon">
                        <i class="fas fa-boxes"></i>
                    </div>

                    <div>
                        <h5>LPO Items</h5>
                        <p>Update the items and quantities being ordered.</p>
                    </div>
                </div>
            </div>

            <div class="requisition-section-body">
                <div class="requisition-table-wrapper">
                    <table class="table requisition-items-table" id="lpoItemsTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item</th>
                                <th>Variant</th>
                                <th>Description</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Discount</th>
                                <th>Tax</th>
                                <th>Line Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody id="lpoItemsBody"></tbody>
                    </table>
                </div>

                <div class="lpo-add-item-row">
                    <button
                        type="button"
                        class="requisition-add-button"
                        id="addLpoItem"
                    >
                        <i class="fas fa-plus"></i>
                        Add Item
                    </button>
                </div>
            </div>
        </div>

        {{-- TOTALS --}}
        <div class="requisition-section">
            <div class="requisition-section-header">
                <div class="requisition-section-heading">
                    <div class="requisition-section-icon">
                        <i class="fas fa-calculator"></i>
                    </div>

                    <div>
                        <h5>LPO Totals</h5>
                        <p>Review the purchase order totals before saving.</p>
                    </div>
                </div>
            </div>

            <div class="requisition-section-body">
                <div class="row">

                    <div class="col-md-6">
                        <div class="requisition-detail-item">
                            <label class="requisition-detail-label">
                                Header Discount
                            </label>

                            <input
                                type="number"
                                name="discount"
                                id="headerDiscount"
                                value="{{ old('discount', $storeLpo->discount ?? 0) }}"
                                min="0"
                                step="0.01"
                                class="form-control requisition-input"
                            >
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="requisition-detail-item">
                            <label class="requisition-detail-label">
                                Header Tax
                            </label>

                            <input
                                type="number"
                                name="tax"
                                id="headerTax"
                                value="{{ old('tax', $storeLpo->tax ?? 0) }}"
                                min="0"
                                step="0.01"
                                class="form-control requisition-input"
                            >
                        </div>
                    </div>

                </div>

                <div class="lpo-totals">
                    <div class="lpo-total-row">
                        <span class="lpo-total-label">Items Subtotal</span>
                        <span class="lpo-total-value" id="itemsSubtotal">KSh 0.00</span>
                    </div>

                    <div class="lpo-total-row">
                        <span class="lpo-total-label">Header Discount</span>
                        <span class="lpo-total-value" id="displayHeaderDiscount">KSh 0.00</span>
                    </div>

                    <div class="lpo-total-row">
                        <span class="lpo-total-label">Header Tax</span>
                        <span class="lpo-total-value" id="displayHeaderTax">KSh 0.00</span>
                    </div>

                    <div class="lpo-total-row">
                        <span class="lpo-total-label lpo-grand-total">Grand Total</span>
                        <span class="lpo-total-value lpo-grand-total" id="grandTotal">KSh 0.00</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- FORM ACTIONS --}}
        <div class="requisition-bottom-actions">
            <div class="requisition-bottom-actions-left">
                <a
                    href="{{ route('admin.store-lpos.index') }}"
                    class="requisition-cancel-button"
                >
                    <i class="fas fa-times"></i>
                    Cancel
                </a>
            </div>

            <div class="requisition-bottom-actions-right">
                <button type="submit" class="requisition-add-button">
                    <i class="fas fa-save"></i>
                    Update LPO
                </button>
            </div>
        </div>
    </form>
</div>

{{-- ITEM SELECTION MODAL --}}
<div
    class="modal fade stores-modal"
    id="itemSelectionModal"
    tabindex="-1"
    aria-labelledby="itemSelectionModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="itemSelectionModalLabel">
                    <i class="fas fa-boxes me-2"></i>
                    Select Store Item
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>
            </div>

            <div class="modal-body">
                <div class="mb-3">
                    <input
                        type="text"
                        class="form-control"
                        id="itemSearch"
                        placeholder="Search item name or SKU..."
                        autocomplete="off"
                    >
                </div>

                <div class="requisition-list-table-wrapper">
                    <table
                        class="table requisition-list-table"
                        id="itemSelectionTable"
                    >
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Item</th>
                                <th>SKU</th>
                                <th>Unit</th>
                                <th>Variants</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>

                        <tbody id="itemSelectionBody"></tbody>
                    </table>
                </div>

                <div
                    id="itemSelectionEmpty"
                    class="text-center text-muted py-4"
                    style="display:none;"
                >
                    <i class="fas fa-box-open fa-2x mb-2"></i>
                    <div>No matching items found.</div>
                </div>
            </div>

            <div class="modal-footer">
                <button
                    type="button"
                    class="requisition-cancel-button"
                    data-bs-dismiss="modal"
                >
                    <i class="fas fa-times me-1"></i>
                    Close
                </button>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const items = @json($lpoItemData);
    const oldItems = @json($oldLpoItems);

    const tbody = document.getElementById('lpoItemsBody');
    const addButton = document.getElementById('addLpoItem');
    const itemSearch = document.getElementById('itemSearch');
    const itemSelectionBody = document.getElementById('itemSelectionBody');
    const itemSelectionEmpty = document.getElementById('itemSelectionEmpty');
    const itemSelectionModalElement = document.getElementById('itemSelectionModal');

    let activeRow = null;

    function money(value) {
        return 'KSh ' + Number(value || 0).toLocaleString('en-KE', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function getItem(itemId) {
        return items.find(function (item) {
            return String(item.id) === String(itemId);
        });
    }

    function renderItemSelection(searchTerm) {
        const term = String(searchTerm || '').trim().toLowerCase();

        itemSelectionBody.innerHTML = '';

        let visibleCount = 0;

        items.forEach(function (item, index) {
            const name = String(item.name || '').toLowerCase();
            const sku = String(item.sku || '').toLowerCase();

            if (term && !name.includes(term) && !sku.includes(term)) {
                return;
            }

            visibleCount++;

            const row = document.createElement('tr');
            const variantCount = Array.isArray(item.variants) ? item.variants.length : 0;

            row.innerHTML = `
                <td>
                    <span class="requisition-row-number">${index + 1}</span>
                </td>
                <td>
                    <div class="stores-item-name">${escapeHtml(item.name)}</div>
                </td>
                <td>
                    <span class="stores-code">${escapeHtml(item.sku || '—')}</span>
                </td>
                <td>${escapeHtml(item.unit || '—')}</td>
                <td>
                    ${
                        variantCount
                            ? `<span class="stores-variant">${variantCount} ${variantCount === 1 ? 'variant' : 'variants'}</span>`
                            : '<span class="text-muted">No variants</span>'
                    }
                </td>
                <td class="text-end">
                    <button
                        type="button"
                        class="requisition-list-action primary select-item-button"
                        data-item-id="${item.id}"
                        title="Select item"
                    >
                        <i class="fas fa-check"></i>
                    </button>
                </td>
            `;

            itemSelectionBody.appendChild(row);
        });

        itemSelectionEmpty.style.display = visibleCount === 0 ? '' : 'none';
    }

    function openItemModal(row) {
        activeRow = row;
        itemSearch.value = '';
        renderItemSelection('');

        bootstrap.Modal.getOrCreateInstance(itemSelectionModalElement).show();

        setTimeout(function () {
            itemSearch.focus();
        }, 250);
    }

    function closeItemModal() {
        const modal = bootstrap.Modal.getInstance(itemSelectionModalElement);

        if (modal) {
            modal.hide();
        }

        activeRow = null;
    }

    function updateItemPicker(row, itemId) {
        const item = getItem(itemId);
        const picker = row.querySelector('.requisition-lpo-picker');
        const hidden = row.querySelector('.store-item-id');
        const selectedCard = row.querySelector('.requisition-lpo-selected');

        if (!item) {
            hidden.value = '';
            picker.style.display = '';
            selectedCard.style.display = 'none';

            picker.querySelector('.requisition-lpo-picker-text').innerHTML = `
                <strong>Select Item</strong>
                <small>Choose store item</small>
            `;

            return;
        }

        hidden.value = item.id;
        picker.style.display = 'none';
        selectedCard.style.display = '';

        row.querySelector('.requisition-lpo-selected-number').textContent = item.name;

        row.querySelector('.requisition-lpo-selected-meta').textContent =
            (item.sku ? item.sku : 'No SKU') +
            (item.unit ? ' · ' + item.unit : '');
    }

    function updateVariantSelect(row, selectedVariantId) {
        const itemSelect = row.querySelector('.store-item-id');
        const variantSelect = row.querySelector('.lpo-variant-select');
        const selectedItem = getItem(itemSelect.value);

        variantSelect.innerHTML = '';

        if (!selectedItem || !Array.isArray(selectedItem.variants) || selectedItem.variants.length === 0) {
            variantSelect.innerHTML = '<option value="">No variant</option>';
            variantSelect.disabled = true;
            variantSelect.required = false;
            return;
        }

        variantSelect.disabled = false;
        variantSelect.required = true;

        variantSelect.innerHTML = '<option value="">Select variant *</option>';

        selectedItem.variants.forEach(function (variant) {
            const option = document.createElement('option');
            option.value = variant.id;
            option.textContent = variant.name + (variant.code ? ' — ' + variant.code : '');
            option.selected = String(selectedVariantId || '') === String(variant.id);
            variantSelect.appendChild(option);
        });
    }

    function selectItemForRow(row, itemId) {
        const item = getItem(itemId);

        if (!item) {
            return;
        }

        const previousItemId = row.querySelector('.store-item-id').value;
        const itemChanged = String(previousItemId) !== String(item.id);

        updateItemPicker(row, item.id);

        if (itemChanged) {
            updateVariantSelect(row, '');

            // Always refresh the description when a different item is selected.
            row.querySelector('.lpo-item-description').value = item.name;
        } else {
            const currentVariantId = row.querySelector('.lpo-variant-select').value;
            updateVariantSelect(row, currentVariantId);
        }

        calculateRow(row);
    }

    function calculateRow(row) {
        const quantity = parseFloat(row.querySelector('.lpo-quantity').value) || 0;
        const unitPrice = parseFloat(row.querySelector('.lpo-unit-price').value) || 0;
        const discount = parseFloat(row.querySelector('.lpo-discount').value) || 0;
        const tax = parseFloat(row.querySelector('.lpo-tax').value) || 0;

        const lineTotal = Math.max(0, (quantity * unitPrice) - discount + tax);

        row.querySelector('.lpo-line-total').textContent = money(lineTotal);
        row.querySelector('.lpo-line-total-input').value = lineTotal.toFixed(2);

        calculateTotals();
    }

    function calculateTotals() {
        let subtotal = 0;

        tbody.querySelectorAll('tr').forEach(function (row) {
            const quantity = parseFloat(row.querySelector('.lpo-quantity')?.value) || 0;
            const unitPrice = parseFloat(row.querySelector('.lpo-unit-price')?.value) || 0;
            const discount = parseFloat(row.querySelector('.lpo-discount')?.value) || 0;
            const tax = parseFloat(row.querySelector('.lpo-tax')?.value) || 0;

            subtotal += Math.max(0, (quantity * unitPrice) - discount + tax);
        });

        const headerDiscount = parseFloat(document.getElementById('headerDiscount').value) || 0;
        const headerTax = parseFloat(document.getElementById('headerTax').value) || 0;
        const total = Math.max(0, subtotal - headerDiscount + headerTax);

        document.getElementById('itemsSubtotal').textContent = money(subtotal);
        document.getElementById('displayHeaderDiscount').textContent = money(headerDiscount);
        document.getElementById('displayHeaderTax').textContent = money(headerTax);
        document.getElementById('grandTotal').textContent = money(total);
    }

    function addRow(data) {
        data = data || {};

        const index = tbody.querySelectorAll('tr').length;
        const row = document.createElement('tr');

        row.classList.add('requisition-item-row');

        row.innerHTML = `
            <td>
                <span class="requisition-row-number">${index + 1}</span>
            </td>

            <td>
                <input
                    type="hidden"
                    name="items[${index}][store_item_id]"
                    class="store-item-id"
                    value="${escapeHtml(data.store_item_id || '')}"
                    required
                >

                <button type="button" class="requisition-lpo-picker item-picker-button">
                    <span class="requisition-lpo-picker-icon">
                        <i class="fas fa-box"></i>
                    </span>

                    <span class="requisition-lpo-picker-text">
                        <strong>Select Item</strong>
                        <small>Choose store item</small>
                    </span>

                    <span class="requisition-lpo-picker-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </span>
                </button>

                <div class="requisition-lpo-selected mt-2" style="display:none;">
                    <div class="requisition-lpo-selected-main">
                        <div class="requisition-lpo-selected-icon">
                            <i class="fas fa-box"></i>
                        </div>

                        <div>
                            <div class="requisition-lpo-selected-number"></div>
                            <div class="requisition-lpo-selected-meta"></div>
                        </div>
                    </div>

                    <button type="button" class="requisition-lpo-change change-item-button">
                        Change
                    </button>
                </div>
            </td>

            <td>
                <select
                    name="items[${index}][variant_id]"
                    class="form-control requisition-table-input lpo-variant-select"
                >
                    <option value="">No variant</option>
                </select>
            </td>

            <td>
                <input
                    type="text"
                    name="items[${index}][description]"
                    class="form-control requisition-table-input lpo-item-description"
                    value="${escapeHtml(data.description || '')}"
                    placeholder="Optional description"
                >

                <input
                    type="hidden"
                    name="items[${index}][notes]"
                    value="${escapeHtml(data.notes || '')}"
                >
            </td>

            <td>
                <input
                    type="number"
                    name="items[${index}][ordered_quantity]"
                    class="form-control requisition-table-input lpo-quantity"
                    value="${escapeHtml(data.ordered_quantity ?? '')}"
                    min="0.001"
                    step="0.001"
                    required
                >
            </td>

            <td>
                <input
                    type="number"
                    name="items[${index}][unit_price]"
                    class="form-control requisition-table-input lpo-unit-price"
                    value="${escapeHtml(data.unit_price ?? '')}"
                    min="0"
                    step="0.01"
                    required
                >
            </td>

            <td>
                <input
                    type="number"
                    name="items[${index}][discount]"
                    class="form-control requisition-table-input lpo-discount"
                    value="${escapeHtml(data.discount ?? 0)}"
                    min="0"
                    step="0.01"
                >
            </td>

            <td>
                <input
                    type="number"
                    name="items[${index}][tax]"
                    class="form-control requisition-table-input lpo-tax"
                    value="${escapeHtml(data.tax ?? 0)}"
                    min="0"
                    step="0.01"
                >
            </td>

            <td>
                <strong class="lpo-line-total">KSh 0.00</strong>

                <input
                    type="hidden"
                    name="items[${index}][line_total]"
                    class="lpo-line-total-input"
                    value="0.00"
                >
            </td>

            <td class="text-end">
                <button
                    type="button"
                    class="requisition-list-action lpo-remove-item"
                    title="Remove item"
                >
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;

        tbody.appendChild(row);

        row.querySelector('.item-picker-button').addEventListener('click', function () {
            openItemModal(row);
        });

        row.querySelector('.change-item-button').addEventListener('click', function () {
            openItemModal(row);
        });

        row.querySelector('.lpo-variant-select').addEventListener('change', function () {
            const selectedItem = getItem(row.querySelector('.store-item-id').value);

            if (!selectedItem) {
                return;
            }

            const selectedVariant = selectedItem.variants.find(function (variant) {
                return String(variant.id) === String(this.value);
            }, this);

            const description = row.querySelector('.lpo-item-description');

            if (selectedVariant) {
                description.value = selectedItem.name + ' - ' + selectedVariant.name;
            } else {
                description.value = selectedItem.name;
            }
        });

        row.querySelectorAll(
            '.lpo-quantity, .lpo-unit-price, .lpo-discount, .lpo-tax'
        ).forEach(function (input) {
            input.addEventListener('input', function () {
                calculateRow(row);
            });
        });

        row.querySelector('.lpo-remove-item').addEventListener('click', function () {
            row.remove();
            renumberRows();
            calculateTotals();
        });

        if (data.store_item_id) {
            updateItemPicker(row, data.store_item_id);
            updateVariantSelect(row, data.variant_id || '');
        }

        calculateRow(row);
    }

    function renumberRows() {
        tbody.querySelectorAll('tr').forEach(function (row, index) {
            const number = row.querySelector('.requisition-row-number');

            if (number) {
                number.textContent = index + 1;
            }

            row.querySelectorAll('[name]').forEach(function (input) {
                const name = input.getAttribute('name');

                input.setAttribute(
                    'name',
                    name.replace(/items\[\d+\]/, 'items[' + index + ']')
                );
            });
        });
    }

    /*
     * Event delegation ensures both the Select button and double-click
     * work, including after the modal table has been re-rendered by search.
     */
    itemSelectionBody.addEventListener('click', function (event) {
        const button = event.target.closest('.select-item-button');

        if (!button || !activeRow) {
            return;
        }

        selectItemForRow(activeRow, button.dataset.itemId);
        closeItemModal();
    });

    itemSelectionBody.addEventListener('dblclick', function (event) {
        const row = event.target.closest('tr');

        if (!row || !activeRow) {
            return;
        }

        const button = row.querySelector('.select-item-button');

        if (!button) {
            return;
        }

        selectItemForRow(activeRow, button.dataset.itemId);
        closeItemModal();
    });

    itemSearch.addEventListener('input', function () {
        renderItemSelection(itemSearch.value);
    });

    addButton.addEventListener('click', function () {
        addRow();
    });

    document.getElementById('headerDiscount').addEventListener('input', calculateTotals);
    document.getElementById('headerTax').addEventListener('input', calculateTotals);

    if (Array.isArray(oldItems) && oldItems.length > 0) {
        oldItems.forEach(function (item) {
            addRow(item);
        });
    } else {
        addRow();
    }

    /*
     * Prevent submission if an item has variants but no variant was selected.
     */
    document.getElementById('lpoForm').addEventListener('submit', function (event) {
        let invalidVariantRow = null;

        tbody.querySelectorAll('tr').forEach(function (row) {
            const item = getItem(row.querySelector('.store-item-id').value);
            const variantSelect = row.querySelector('.lpo-variant-select');

            if (
                item &&
                Array.isArray(item.variants) &&
                item.variants.length > 0 &&
                !variantSelect.value
            ) {
                variantSelect.required = true;

                if (!invalidVariantRow) {
                    invalidVariantRow = row;
                }
            }
        });

        if (invalidVariantRow) {
            event.preventDefault();

            const variantSelect = invalidVariantRow.querySelector('.lpo-variant-select');
            variantSelect.focus();

            variantSelect.reportValidity();
        }
    });

    calculateTotals();
});
</script>

@endsection