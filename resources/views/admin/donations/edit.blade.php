@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/stores.css') }}">
@endpush

@section('content')

@php
    $items = old('items', $donation->items->toArray());

    $currentType = old('type', $donation->type);

    $purposes = [
        'General Support',
        'Education',
        'Child Sponsorship',
        'Food & Nutrition',
        'Medical Care',
        'School Bus',
        'Family Empowerment',
        'Agriculture',
        'Infrastructure',
        'Emergency Support',
        'Other',
    ];

    $currencies = [
        'KES', 'USD', 'EUR', 'GBP', 'CAD', 'AUD', 'CHF',
        'ZAR', 'UGX', 'TZS', 'RWF', 'NGN', 'GHS', 'AED',
        'SAR', 'JPY', 'CNY', 'INR',
    ];

    $sourceLabels = [
        'manual' => 'Cash / Manual',
        'bank' => 'Bank',
        'mpesa' => 'M-Pesa',
        'paypal' => 'PayPal',
        'other' => 'Other',
    ];

    $classifications = [
        'donation' => 'Donation',
        'payment' => 'Payment',
        'refund' => 'Refund',
        'other' => 'Other',
        'unclassified' => 'Unclassified',
    ];
@endphp

<div class="store-requisition-page">

    {{-- PAGE HEADER --}}

    <div class="requisition-page-header">

        <div class="requisition-header-content">

            <div class="requisition-header-icon">
                <i class="fa fa-hand-holding-heart"></i>
            </div>

            <div>
                <div class="requisition-breadcrumb">
                    <span>Donations</span>
                    <span>/</span>
                    <span>Edit Donation</span>
                </div>

                <h1 class="requisition-page-title">
                    Edit Donation
                </h1>

                <p class="requisition-page-subtitle">
                    Update cash or in-kind support recorded by Teule Kenya.
                </p>

                <div class="small text-muted mt-1">
                    Donation No. {{ $donation->donation_number }}
                </div>
            </div>

        </div>

        <div class="requisition-header-right">
            <a href="{{ route('admin.donations.show', $donation) }}"
               class="stores-btn-light">
                <i class="fa fa-arrow-left"></i>
                Back
            </a>
        </div>

    </div>

    <x-message></x-message>

    {{-- DONATION TYPE AND FORM --}}

    <div id="donation-form-wrapper">

        {{-- CASH FORM --}}

        <div id="cash-form"
             style="{{ $currentType === 'cash' ? '' : 'display:none;' }}">

            <form method="POST"
                  action="{{ route('admin.donations.update', $donation) }}">

                @csrf
                @method('PUT')

                <input type="hidden" name="type" value="cash">

                <div class="requisition-section">

                    <div class="requisition-section-header">

                        <div class="requisition-section-heading">

                            <div class="requisition-section-icon">
                                <i class="fa fa-money-bill-wave"></i>
                            </div>

                            <div>
                                <h5>Cash Donation</h5>
                                <p>
                                    Update the donor, payment and classification details.
                                </p>
                            </div>

                        </div>

                        <button type="button"
                                class="requisition-cancel-button change-type">
                            <i class="fa fa-exchange-alt"></i>
                            Change Type
                        </button>

                    </div>

                    <div class="requisition-details-body">

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="requisition-field-label">Donor</label>

                                <select name="donor_id"
                                        class="form-select requisition-input">

                                    <option value="">
                                        Anonymous / Not Specified
                                    </option>

                                    @foreach($donors as $donor)
                                        <option value="{{ $donor->id }}"
                                            @selected(old('donor_id', $donation->donor_id) == $donor->id)>
                                            {{ $donor->donor_number }} - {{ $donor->name }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="requisition-field-label">
                                    Date <span class="required-mark">*</span>
                                </label>

                                <input type="date"
                                       name="donation_date"
                                       class="form-control form-control-sm requisition-input"
                                       value="{{ old('donation_date', $donation->donation_date?->format('Y-m-d')) }}"
                                       required>
                            </div>

                            <div class="col-md-3">
                                <label class="requisition-field-label">
                                    Source <span class="required-mark">*</span>
                                </label>

                                <select name="source"
                                        class="form-select requisition-input"
                                        required>

                                    @foreach($sourceLabels as $value => $label)
                                        <option value="{{ $value }}"
                                            @selected(old('source', $donation->source) === $value)>
                                            {{ $label }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="requisition-field-label">
                                    Currency <span class="required-mark">*</span>
                                </label>

                                <select name="currency"
                                        class="form-select requisition-input"
                                        required>

                                    @foreach($currencies as $currency)
                                        <option value="{{ $currency }}"
                                            @selected(old('currency', $donation->currency ?: 'KES') === $currency)>
                                            {{ $currency }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="requisition-field-label">
                                    Amount <span class="required-mark">*</span>
                                </label>

                                <input type="number"
                                       name="amount"
                                       class="form-control requisition-input"
                                       step="0.01"
                                       min="0"
                                       value="{{ old('amount', $donation->amount) }}"
                                       required>
                            </div>

                            <div class="col-md-3">
                                <label class="requisition-field-label">
                                    Classification <span class="required-mark">*</span>
                                </label>

                                <select name="classification"
                                        class="form-select requisition-input"
                                        required>

                                    @foreach($classifications as $value => $label)
                                        <option value="{{ $value }}"
                                            @selected(old('classification', $donation->classification ?: 'donation') === $value)>
                                            {{ $label }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="requisition-field-label">Purpose</label>

                                <select name="purpose" class="form-select requisition-input">
                                    <option value="">Select purpose</option>

                                    @foreach($purposes as $purpose)
                                        <option value="{{ $purpose }}"
                                            @selected(old('purpose', $donation->purpose) === $purpose)>
                                            {{ $purpose }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="requisition-field-label">Reference</label>

                                <input type="text"
                                       name="reference"
                                       class="form-control requisition-input"
                                       value="{{ old('reference', $donation->reference) }}"
                                       placeholder="Receipt / cheque reference">
                            </div>

                            <div class="col-md-6">
                                <label class="requisition-field-label">Payment Reference</label>

                                <input type="text"
                                       name="payment_reference"
                                       class="form-control requisition-input"
                                       value="{{ old('payment_reference', $donation->payment_reference) }}"
                                       placeholder="M-Pesa / bank transaction reference">
                            </div>

                            <div class="col-md-12">
                                <label class="requisition-field-label">Description</label>

                                <input type="text"
                                       name="description"
                                       class="form-control requisition-input"
                                       value="{{ old('description', $donation->description) }}"
                                       placeholder="Brief description">
                            </div>

                            <div class="col-md-12">
                                <label class="requisition-field-label">Notes</label>

                                <textarea name="notes"
                                          class="form-control requisition-input requisition-textarea"
                                          rows="3">{{ old('notes', $donation->notes) }}</textarea>
                            </div>

                        </div>

                    </div>

                </div>

                <div class="requisition-action-bar">

                    <div class="requisition-action-hint">
                        Review the details before saving your changes.
                    </div>

                    <div class="requisition-action-right">

                        <button type="button"
                                class="requisition-cancel-button change-type">
                            <i class="fa fa-exchange-alt"></i>
                            Change Type
                        </button>

                        <button type="submit" class="requisition-save-button">
                            <i class="fa fa-save"></i>
                            Update Donation
                        </button>

                    </div>

                </div>

            </form>

        </div>

        {{-- IN-KIND FORM --}}

        <div id="inkind-form"
             style="{{ $currentType === 'in_kind' ? '' : 'display:none;' }}">

            <form method="POST"
                  action="{{ route('admin.donations.update', $donation) }}"
                  id="inkindDonationForm">

                @csrf
                @method('PUT')

                <input type="hidden" name="type" value="in_kind">
                <input type="hidden" name="source" value="manual">
                <input type="hidden" name="currency" value="KES">

                <div class="requisition-section">

                    <div class="requisition-section-header">

                        <div class="requisition-section-heading">

                            <div class="requisition-section-icon requisition-items-icon">
                                <i class="fa fa-box-open"></i>
                            </div>

                            <div>
                                <h5>In-kind Donation</h5>
                                <p>
                                    Update the donor and details of the goods received.
                                </p>
                            </div>

                        </div>

                        <button type="button"
                                class="requisition-cancel-button change-type">
                            <i class="fa fa-exchange-alt"></i>
                            Change Type
                        </button>

                    </div>

                    <div class="requisition-details-body">

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="requisition-field-label">Donor</label>

                                <select name="donor_id"
                                        class="form-select requisition-input">

                                    <option value="">
                                        Anonymous / Not Specified
                                    </option>

                                    @foreach($donors as $donor)
                                        <option value="{{ $donor->id }}"
                                            @selected(old('donor_id', $donation->donor_id) == $donor->id)>
                                            {{ $donor->donor_number }} - {{ $donor->name }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="requisition-field-label">
                                    Date <span class="required-mark">*</span>
                                </label>

                                <input type="date"
                                       name="donation_date"
                                       class="form-control form-control-sm requisition-input"
                                       value="{{ old('donation_date', $donation->donation_date?->format('Y-m-d')) }}"
                                       required>
                            </div>

                            <div class="col-md-3">
                                <label class="requisition-field-label">
                                    Classification <span class="required-mark">*</span>
                                </label>

                                <select name="classification"
                                        class="form-select requisition-input"
                                        required>

                                    @foreach(['donation' => 'Donation', 'other' => 'Other'] as $value => $label)
                                        <option value="{{ $value }}"
                                            @selected(old('classification', $donation->classification ?: 'donation') === $value)>
                                            {{ $label }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                        </div>

                    </div>

                </div>

                {{-- ITEMS RECEIVED --}}

                <div class="requisition-section">

                    <div class="requisition-section-header">

                        <div class="requisition-section-heading">

                            <div class="requisition-section-icon requisition-items-icon">
                                <i class="fa fa-box-open"></i>
                            </div>

                            <div>
                                <h5>Items Received</h5>
                                <p>
                                    Edit existing items, change variants or add more items.
                                </p>
                            </div>

                        </div>

                        <div class="requisition-header-right">

                            <span class="requisition-count-badge" id="item-count">
                                {{ count($items) }} items
                            </span>

                            <button type="button"
                                    id="add-item"
                                    class="requisition-save-button">
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
                                        <th style="width:45px;">#</th>
                                        <th>Item</th>
                                        <th>Variant</th>
                                        <th>Quantity</th>
                                        <th>Unit</th>
                                        <th>Estimated Value (KES)</th>
                                        <th>Condition</th>
                                        <th>Item Notes</th>
                                        <th style="width:70px;">Action</th>
                                    </tr>
                                </thead>

                                <tbody id="items-container">

                                    @foreach($items as $index => $item)

                                        @php
                                            $storeItemId = data_get($item, 'store_item_id');
                                            $variantId = data_get($item, 'variant_id');
                                            $itemName = data_get($item, 'item', data_get($item, 'name', ''));

                                            $matchedStoreItem = isset($storeItems)
                                                ? $storeItems->firstWhere('id', $storeItemId)
                                                : null;

                                            $displayItemName = $matchedStoreItem->name ?? $itemName;

                                            $unitName = data_get($item, 'unit', '');

                                            if ($unitName === '' && $matchedStoreItem) {
                                                $unitName = $matchedStoreItem->unit->code
                                                    ?? $matchedStoreItem->unit->name
                                                    ?? '';
                                            }
                                        @endphp

                                        <tr class="donation-item-row"
                                            data-index="{{ $index }}"
                                            data-selected-variant="{{ $variantId }}">

                                            <td>
                                                <span class="requisition-row-number">
                                                    {{ $loop->iteration }}
                                                </span>
                                            </td>

                                            <td>
                                                <button type="button"
                                                        class="requisition-lpo-picker store-item-picker">

                                                    <span class="requisition-lpo-picker-icon">
                                                        <i class="fas fa-box-open"></i>
                                                    </span>

                                                    <span class="requisition-lpo-picker-text store-item-picker-text">
                                                        @if($displayItemName)
                                                            <strong>{{ $displayItemName }}</strong>

                                                            @if($unitName)
                                                                <small>{{ $unitName }}</small>
                                                            @endif
                                                        @else
                                                            <span class="text-muted">Select Item</span>
                                                        @endif
                                                    </span>

                                                    <span class="requisition-lpo-picker-arrow">
                                                        <i class="fas fa-chevron-right"></i>
                                                    </span>

                                                </button>

                                                <input type="hidden"
                                                       name="items[{{ $index }}][store_item_id]"
                                                       class="store-item-id"
                                                       value="{{ $storeItemId }}">

                                                <input type="hidden"
                                                       name="items[{{ $index }}][item]"
                                                       class="legacy-item"
                                                       value="{{ $itemName }}">
                                            </td>

                                            <td>
                                                <select name="items[{{ $index }}][variant_id]"
                                                        class="form-select requisition-input item-variant"
                                                        data-selected-variant="{{ $variantId }}"
                                                        {{ $storeItemId ? '' : 'disabled' }}>
                                                    <option value="">No variant</option>
                                                </select>
                                            </td>

                                            <td>
                                                <input type="number"
                                                       name="items[{{ $index }}][quantity]"
                                                       class="form-control requisition-input item-quantity"
                                                       value="{{ data_get($item, 'quantity', 1) }}"
                                                       step="0.001"
                                                       min="0.001"
                                                       required>
                                            </td>

                                            <td>
                                                <input type="hidden"
                                                       name="items[{{ $index }}][unit]"
                                                       class="item-unit"
                                                       value="{{ $unitName }}">

                                                <input type="text"
                                                       class="form-control requisition-input item-unit-display"
                                                       value="{{ $unitName ?: '—' }}"
                                                       readonly>
                                            </td>

                                            <td>
                                                 <div class="input-group" style="display: flex; flex-wrap: nowrap; align-items: stretch;">
    <span class="input-group-text" style="white-space: nowrap;">
        KES
    </span>


                                                    <input type="number"
                                                           name="items[{{ $index }}][estimated_value]"
                                                           class="form-control requisition-input estimated-value"
                                                           value="{{ data_get($item, 'estimated_value', 0) }}"
                                                           step="0.01"
                                                           min="0">
                                                </div>
                                            </td>

                                            <td>
                                                <input type="text"
                                                       name="items[{{ $index }}][condition]"
                                                       class="form-control requisition-input"
                                                       value="{{ data_get($item, 'condition', '') }}">
                                            </td>

                                            <td>
                                                <input type="text"
                                                       name="items[{{ $index }}][notes]"
                                                       class="form-control requisition-input"
                                                       value="{{ data_get($item, 'notes', '') }}"
                                                       placeholder="Item notes">
                                            </td>

                                            <td class="text-center">
                                                <button type="button"
                                                        class="requisition-list-action remove-item"
                                                        title="Remove item">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>

                                        </tr>

                                    @endforeach

                                </tbody>

                            </table>

                        </div>

                        <div id="items-empty-state"
                             class="text-center py-5"
                             style="{{ count($items) ? 'display:none;' : '' }}">

                            <div class="requisition-header-icon mx-auto mb-3">
                                <i class="fa fa-box-open"></i>
                            </div>

                            <h6 class="mb-1">No items added</h6>

                            <p class="text-muted mb-3">
                                Add the goods or supplies received from the donor.
                            </p>

                            <button type="button"
                                    class="requisition-save-button add-item-trigger">
                                <i class="fa fa-plus"></i>
                                Add Item
                            </button>

                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <div class="text-end">
                                <span class="stores-detail-label">
                                    Total Estimated Value
                                </span>

                                <strong class="stores-detail-value d-block"
                                        style="font-size:18px;">
                                    KES <span id="estimated-total">0.00</span>
                                </strong>
                            </div>
                        </div>

                    </div>

                </div>

                {{-- OTHER DETAILS --}}

                <div class="requisition-section">

                    <div class="requisition-section-header">

                        <div class="requisition-section-heading">

                            <div class="requisition-section-icon">
                                <i class="fa fa-file-alt"></i>
                            </div>

                            <div>
                                <h5>Donation Details</h5>
                                <p>
                                    Additional information about this donation.
                                </p>
                            </div>

                        </div>

                    </div>

                    <div class="requisition-details-body">

                        <div class="row g-3">

                            <div class="col-md-6">
                                <label class="requisition-field-label">Purpose</label>

                                <select name="purpose" class="form-select requisition-input">
                                    <option value="">Select purpose</option>

                                    @foreach($purposes as $purpose)
                                        <option value="{{ $purpose }}"
                                            @selected(old('purpose', $donation->purpose) === $purpose)>
                                            {{ $purpose }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="requisition-field-label">Description</label>

                                <input type="text"
                                       name="description"
                                       class="form-control requisition-input"
                                       value="{{ old('description', $donation->description) }}"
                                       placeholder="Brief description">
                            </div>

                            <div class="col-md-12">
                                <label class="requisition-field-label">Notes</label>

                                <textarea name="notes"
                                          class="form-control requisition-input requisition-textarea"
                                          rows="3">{{ old('notes', $donation->notes) }}</textarea>
                            </div>

                        </div>

                    </div>

                </div>

                <div class="requisition-action-bar">

                    <div class="requisition-action-hint">
                        Review all items and estimated values before updating.
                    </div>

                    <div class="requisition-action-right">

                        <button type="button"
                                class="requisition-cancel-button change-type">
                            <i class="fa fa-exchange-alt"></i>
                            Change Type
                        </button>

                        <button type="submit" class="requisition-save-button">
                            <i class="fa fa-save"></i>
                            Update Donation
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>

{{-- DONATION TYPE MODAL --}}

<div class="modal fade"
     id="donationTypeModal"
     tabindex="-1"
     aria-labelledby="donationTypeModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content border-0 shadow">

            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-1" id="donationTypeModalLabel">
                        Change Donation Type
                    </h5>

                    <div class="requisition-modal-subtitle">
                        Choose how this donation should be recorded.
                    </div>
                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>

            <div class="modal-body">

                <div id="donationTypeVisual" class="text-center mb-4">

                    <div id="defaultDonationVisual">
                        <div class="d-flex align-items-center justify-content-center gap-3">
                            <div class="requisition-header-icon">
                                <i class="fa fa-hand-holding-heart"></i>
                            </div>

                            <i class="fa fa-arrow-right fa-2x"></i>

                            <div class="requisition-header-icon">
                                <i class="fa fa-question"></i>
                            </div>
                        </div>

                        <div class="requisition-modal-subtitle mt-3">
                            Select a donation type to continue.
                        </div>
                    </div>

                    <div id="cashDonationVisual" style="display:none;">
                        <div class="d-flex align-items-center justify-content-center gap-3">
                            <div class="text-center">
                                <div class="requisition-header-icon mx-auto mb-2">
                                    <i class="fa fa-user"></i>
                                </div>
                                <div class="small fw-semibold">Donor</div>
                            </div>

                            <i class="fa fa-arrow-right fa-2x"></i>

                            <div class="text-center">
                                <div class="requisition-header-icon mx-auto mb-2">
                                    <i class="fa fa-money-bill-wave"></i>
                                </div>
                                <div class="small fw-semibold">Cash</div>
                            </div>
                        </div>

                        <div class="requisition-modal-subtitle mt-3">
                            Money received from the donor.
                        </div>
                    </div>

                    <div id="inkindDonationVisual" style="display:none;">
                        <div class="d-flex align-items-center justify-content-center gap-3">
                            <div class="text-center">
                                <div class="requisition-header-icon mx-auto mb-2">
                                    <i class="fa fa-user"></i>
                                </div>
                                <div class="small fw-semibold">Donor</div>
                            </div>

                            <i class="fa fa-arrow-right fa-2x"></i>

                            <div class="text-center">
                                <div class="requisition-header-icon mx-auto mb-2">
                                    <i class="fa fa-box-open"></i>
                                </div>
                                <div class="small fw-semibold">In-kind</div>
                            </div>
                        </div>

                        <div class="requisition-modal-subtitle mt-3">
                            Goods, supplies or equipment received.
                        </div>
                    </div>

                </div>

                <label for="setup_donation_type"
                       class="requisition-field-label">
                    Donation Type <span class="required-mark">*</span>
                </label>

                <select id="setup_donation_type"
                        class="form-select requisition-input">

                    <option value="">Select donation type</option>
                    <option value="cash">Cash Donation</option>
                    <option value="in_kind">In-kind Donation</option>

                </select>

                <div id="donationTypeMessage"
                     class="requisition-modal-subtitle mt-3">
                    Select a donation type to continue.
                </div>

                <p class="small text-muted mt-3 mb-0">
                    Changing the type changes which form is displayed.
                    Review the fields carefully before saving.
                </p>

            </div>

            <div class="modal-footer">
                <button type="button"
                        id="cancelDonationType"
                        class="requisition-cancel-button">
                    Cancel
                </button>

                <button type="button"
                        id="continueDonationType"
                        class="requisition-save-button">
                    Continue <i class="fa fa-arrow-right ms-1"></i>
                </button>
            </div>

        </div>

    </div>

</div>

{{-- ITEM SELECTION MODAL --}}

<div class="modal fade"
     id="storeItemSelectionModal"
     tabindex="-1"
     aria-labelledby="storeItemSelectionModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg">

        <div class="modal-content border-0 shadow">

            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-1" id="storeItemSelectionModalLabel">
                        Select Item
                    </h5>

                    <div class="requisition-modal-subtitle">
                        Double-click an item row to select it.
                    </div>
                </div>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"></button>
            </div>

            <div class="modal-body">

                <div class="requisition-child-search mb-3">
                    <i class="fa fa-search"></i>

                    <input type="text"
                           id="storeItemSearch"
                           class="form-control requisition-input"
                           placeholder="Search items..."
                           autocomplete="off">
                </div>

                <div class="requisition-list-table-wrapper">

                    <table class="requisition-list-table">

                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Code</th>
                                <th>Unit</th>
                                <th>Variants</th>
                            </tr>
                        </thead>

                        <tbody id="storeItemSelectionBody">

                            @forelse($storeItems as $storeItem)

                                @php
                                    $unitName = $storeItem->unit->code
                                        ?? $storeItem->unit->name
                                        ?? '';
                                @endphp

                                <tr class="store-item-option"
                                    tabindex="0"
                                    title="Double-click to select this item"
                                    data-search="{{ strtolower($storeItem->name . ' ' . ($storeItem->sku ?? '') . ' ' . $unitName) }}"
                                    data-store-item-id="{{ $storeItem->id }}"
                                    data-store-item-name="{{ $storeItem->name }}"
                                    data-store-item-unit="{{ $unitName }}">

                                    <td>
                                        <div class="stores-item-name">
                                            {{ $storeItem->name }}
                                        </div>
                                    </td>

                                    <td>
                                        @if($storeItem->sku)
                                            <div class="stores-code">{{ $storeItem->sku }}</div>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>

                                    <td>{{ $unitName ?: '—' }}</td>

                                    <td>
                                        @if($storeItem->variants->count())
                                            <span class="stores-variant">
                                                {{ $storeItem->variants->count() }} available
                                            </span>
                                        @else
                                            <span class="text-muted">None</span>
                                        @endif
                                    </td>

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <span class="text-muted">
                                            No active items are available.
                                        </span>
                                    </td>
                                </tr>

                            @endforelse

                        </tbody>

                    </table>

                </div>

                <div id="storeItemNoResults"
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
            </div>

        </div>

    </div>

</div>

@endsection

@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    const cashForm = document.getElementById('cash-form');
    const inkindForm = document.getElementById('inkind-form');

    const typeModalElement = document.getElementById('donationTypeModal');
    const itemModalElement = document.getElementById('storeItemSelectionModal');

    const typeModal = typeModalElement
        ? new bootstrap.Modal(typeModalElement, {
            backdrop: 'static',
            keyboard: false
        })
        : null;

    const itemModal = itemModalElement
        ? new bootstrap.Modal(itemModalElement)
        : null;

    const typeSelect = document.getElementById('setup_donation_type');
    const typeMessage = document.getElementById('donationTypeMessage');

    const defaultVisual = document.getElementById('defaultDonationVisual');
    const cashVisual = document.getElementById('cashDonationVisual');
    const inkindVisual = document.getElementById('inkindDonationVisual');

    const continueTypeButton = document.getElementById('continueDonationType');
    const cancelTypeButton = document.getElementById('cancelDonationType');

    const itemsContainer = document.getElementById('items-container');
    const addItemButton = document.getElementById('add-item');
    const addItemTrigger = document.querySelector('.add-item-trigger');

    const itemCount = document.getElementById('item-count');
    const emptyState = document.getElementById('items-empty-state');
    const estimatedTotal = document.getElementById('estimated-total');

    const itemSearch = document.getElementById('storeItemSearch');
    const noItemResults = document.getElementById('storeItemNoResults');

    let activeItemRow = null;
    let itemIndex = 0;

    /*
    |--------------------------------------------------------------------------
    | STORE ITEM AND VARIANT DATA
    |--------------------------------------------------------------------------
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
    | DONATION TYPE MODAL
    |--------------------------------------------------------------------------
    */

    function resetTypeModal() {
        if (typeSelect) typeSelect.value = '';

        if (defaultVisual) defaultVisual.style.display = 'block';
        if (cashVisual) cashVisual.style.display = 'none';
        if (inkindVisual) inkindVisual.style.display = 'none';

        if (typeMessage) {
            typeMessage.textContent = 'Select a donation type to continue.';
        }
    }

    function showTypeVisual(type) {
        if (defaultVisual) defaultVisual.style.display = type ? 'none' : 'block';
        if (cashVisual) cashVisual.style.display = type === 'cash' ? 'block' : 'none';
        if (inkindVisual) inkindVisual.style.display = type === 'in_kind' ? 'block' : 'none';

        if (typeMessage) {
            if (type === 'cash') {
                typeMessage.innerHTML =
                    'You are editing <strong>money received</strong> from a donor.';
            } else if (type === 'in_kind') {
                typeMessage.innerHTML =
                    'You are editing <strong>goods, supplies or equipment</strong> received.';
            } else {
                typeMessage.textContent = 'Select a donation type to continue.';
            }
        }
    }

    if (typeSelect) {
        typeSelect.addEventListener('change', function () {
            showTypeVisual(this.value);
        });
    }

    document.querySelectorAll('.change-type').forEach(function (button) {
        button.addEventListener('click', function () {
            resetTypeModal();

            const currentType = cashForm && cashForm.style.display !== 'none'
                ? 'cash'
                : 'in_kind';

            if (typeSelect) {
                typeSelect.value = currentType;
                showTypeVisual(currentType);
            }

            if (typeModal) typeModal.show();
        });
    });

    if (cancelTypeButton) {
        cancelTypeButton.addEventListener('click', function () {
            if (typeModal) typeModal.hide();
        });
    }

    if (continueTypeButton) {
        continueTypeButton.addEventListener('click', function () {
            const selectedType = typeSelect ? typeSelect.value : '';

            if (!selectedType) {
                if (typeMessage) {
                    typeMessage.textContent =
                        'Please select a donation type before continuing.';
                }

                if (typeSelect) typeSelect.focus();
                return;
            }

            /*
             * Changing the type only changes the displayed form.
             * The selected form submits its own type field when saved.
             */
            if (cashForm) {
                cashForm.style.display = selectedType === 'cash' ? 'block' : 'none';
            }

            if (inkindForm) {
                inkindForm.style.display = selectedType === 'in_kind' ? 'block' : 'none';
            }

            if (typeModal) typeModal.hide();

            if (selectedType === 'in_kind') {
                calculateTotal();
                updateItemState();
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | ITEM SEARCH
    |--------------------------------------------------------------------------
    */

    function filterStoreItems(value) {
        const search = String(value || '').trim().toLowerCase();
        let visibleCount = 0;

        document.querySelectorAll('.store-item-option').forEach(function (row) {
            const matches = (row.dataset.search || '').includes(search);

            row.style.display = matches ? '' : 'none';

            if (matches) visibleCount++;
        });

        if (noItemResults) {
            noItemResults.style.display = visibleCount === 0 ? 'block' : 'none';
        }
    }

    if (itemSearch) {
        itemSearch.addEventListener('input', function () {
            filterStoreItems(this.value);
        });
    }

    /*
    |--------------------------------------------------------------------------
    | OPEN ITEM PICKER
    |--------------------------------------------------------------------------
    */

    function openItemPicker(row) {
        activeItemRow = row;

        if (itemSearch) itemSearch.value = '';

        filterStoreItems('');

        if (itemModal) itemModal.show();
    }

    if (addItemButton) {
        addItemButton.addEventListener('click', addItemRow);
    }

    if (addItemTrigger) {
        addItemTrigger.addEventListener('click', addItemRow);
    }

    document.addEventListener('click', function (event) {
        const picker = event.target.closest('.store-item-picker');

        if (!picker) return;

        const row = picker.closest('.donation-item-row');

        if (row) openItemPicker(row);
    });

    /*
    |--------------------------------------------------------------------------
    | ADD ITEM ROW
    |--------------------------------------------------------------------------
    */

    function addItemRow() {
        if (!itemsContainer) return;

        const index = itemIndex++;
        const row = document.createElement('tr');

        row.className = 'donation-item-row';
        row.dataset.index = index;
        row.dataset.selectedVariant = '';

        row.innerHTML = `
            <td>
                <span class="requisition-row-number"></span>
            </td>

            <td>
                <button type="button" class="requisition-lpo-picker store-item-picker">
                    <span class="requisition-lpo-picker-icon">
                        <i class="fas fa-box-open"></i>
                    </span>
                    <span class="requisition-lpo-picker-text store-item-picker-text">
                        <span class="text-muted">Select Item</span>
                    </span>
                    <span class="requisition-lpo-picker-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </span>
                </button>

                <input type="hidden"
                       name="items[${index}][store_item_id]"
                       class="store-item-id">

                <input type="hidden"
                       name="items[${index}][item]"
                       class="legacy-item">
            </td>

            <td>
                <select name="items[${index}][variant_id]"
                        class="form-select requisition-input item-variant"
                        disabled>
                    <option value="">No variant</option>
                </select>
            </td>

            <td>
                <input type="number"
                       name="items[${index}][quantity]"
                       class="form-control requisition-input item-quantity"
                       value="1"
                       step="0.001"
                       min="0.001"
                       required>
            </td>

            <td>
                <input type="hidden"
                       name="items[${index}][unit]"
                       class="item-unit">

                <input type="text"
                       class="form-control requisition-input item-unit-display"
                       value="—"
                       readonly>
            </td>

            <td>
                <div class="input-group">
                    <span class="input-group-text">KES</span>
                    <input type="number"
                           name="items[${index}][estimated_value]"
                           class="form-control requisition-input estimated-value"
                           value="0"
                           step="0.01"
                           min="0">
                </div>
            </td>

            <td>
                <input type="text"
                       name="items[${index}][condition]"
                       class="form-control requisition-input">
            </td>

            <td>
                <input type="text"
                       name="items[${index}][notes]"
                       class="form-control requisition-input"
                       placeholder="Item notes">
            </td>

            <td class="text-center">
                <button type="button"
                        class="requisition-list-action remove-item"
                        title="Remove item">
                    <i class="fa fa-trash"></i>
                </button>
            </td>
        `;

        itemsContainer.appendChild(row);

        updateItemState();
        calculateTotal();
        openItemPicker(row);
    }

    /*
    |--------------------------------------------------------------------------
    | SELECT ITEM AND LOAD VARIANTS
    |--------------------------------------------------------------------------
    */

    function escapeHtml(value) {
        const element = document.createElement('div');
        element.textContent = value ?? '';
        return element.innerHTML;
    }

    function loadVariants(row, selectedVariantId) {
        const itemIdInput = row.querySelector('.store-item-id');
        const variantSelect = row.querySelector('.item-variant');

        if (!variantSelect) return;

        const itemId = itemIdInput ? itemIdInput.value : '';
        const variants = variantData[itemId] || [];

        variantSelect.innerHTML = '<option value="">No variant</option>';

        variants.forEach(function (variant) {
            const option = document.createElement('option');

            option.value = variant.id;
            option.textContent = variant.name +
                (variant.code ? ' (' + variant.code + ')' : '');

            if (String(variant.id) === String(selectedVariantId || '')) {
                option.selected = true;
            }

            variantSelect.appendChild(option);
        });

        variantSelect.disabled = !itemId || variants.length === 0;
    }

    function selectStoreItem(itemRow) {
        if (!activeItemRow) return;

        const row = activeItemRow;
        const itemId = itemRow.dataset.storeItemId || '';
        const itemName = itemRow.dataset.storeItemName || '';
        const itemUnit = itemRow.dataset.storeItemUnit || '';

        if (!itemId) return;

        const itemIdInput = row.querySelector('.store-item-id');
        const legacyItemInput = row.querySelector('.legacy-item');
        const unitInput = row.querySelector('.item-unit');
        const unitDisplay = row.querySelector('.item-unit-display');
        const pickerText = row.querySelector('.store-item-picker-text');

        if (itemIdInput) itemIdInput.value = itemId;
        if (legacyItemInput) legacyItemInput.value = itemName;
        if (unitInput) unitInput.value = itemUnit;
        if (unitDisplay) unitDisplay.value = itemUnit || '—';

        if (pickerText) {
            pickerText.innerHTML =
                '<strong>' + escapeHtml(itemName) + '</strong>' +
                (itemUnit ? '<small>' + escapeHtml(itemUnit) + '</small>' : '');
        }

        // Selecting a different item clears the old variant selection.
        row.dataset.selectedVariant = '';
        loadVariants(row, '');

        if (itemModal) itemModal.hide();
    }

    document.addEventListener('dblclick', function (event) {
        const itemRow = event.target.closest('.store-item-option');

        if (!itemRow || !activeItemRow) return;

        selectStoreItem(itemRow);
    });

    document.addEventListener('keydown', function (event) {
        const itemRow = event.target.closest('.store-item-option');

        if (!itemRow || !['Enter', ' '].includes(event.key)) return;

        event.preventDefault();

        if (activeItemRow) selectStoreItem(itemRow);
    });

    /*
    |--------------------------------------------------------------------------
    | REMOVE ITEM
    |--------------------------------------------------------------------------
    */

    if (itemsContainer) {
        itemsContainer.addEventListener('click', function (event) {
            const removeButton = event.target.closest('.remove-item');

            if (!removeButton) return;

            const row = removeButton.closest('.donation-item-row');

            if (row) row.remove();

            updateItemState();
            calculateTotal();
        });
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE ITEM COUNT AND ROW NUMBERS
    |--------------------------------------------------------------------------
    */

    function updateItemState() {
        if (!itemsContainer) return;

        const rows = itemsContainer.querySelectorAll('.donation-item-row');
        const count = rows.length;

        if (itemCount) {
            itemCount.textContent = count === 1 ? '1 item' : count + ' items';
        }

        if (emptyState) {
            emptyState.style.display = count === 0 ? 'block' : 'none';
        }

        rows.forEach(function (row, index) {
            const number = row.querySelector('.requisition-row-number');

            if (number) number.textContent = index + 1;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | CALCULATE ESTIMATED VALUE TOTAL
    |--------------------------------------------------------------------------
    */

    function calculateTotal() {
        let total = 0;

        document.querySelectorAll('#items-container .estimated-value')
            .forEach(function (input) {
                total += parseFloat(input.value) || 0;
            });

        if (estimatedTotal) {
            estimatedTotal.textContent = total.toLocaleString('en-KE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
    }

    if (itemsContainer) {
        itemsContainer.addEventListener('input', function (event) {
            if (event.target.classList.contains('estimated-value')) {
                calculateTotal();
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | RESTORE EXISTING ITEM VARIANTS
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll('#items-container .donation-item-row')
        .forEach(function (row) {
            const variantSelect = row.querySelector('.item-variant');

            const selectedVariant =
                row.dataset.selectedVariant ||
                (variantSelect ? variantSelect.dataset.selectedVariant : '') ||
                '';

            loadVariants(row, selectedVariant);
        });

    /*
    |--------------------------------------------------------------------------
    | INITIAL STATE
    |--------------------------------------------------------------------------
    */

    updateItemState();
    calculateTotal();

});
</script>

@endpush

