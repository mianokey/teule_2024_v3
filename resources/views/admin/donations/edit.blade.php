@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">

        <div>
            <h5 class="mb-1">Edit Donation</h5>

            <small class="text-muted">
                {{ $donation->donation_number }}
            </small>
        </div>

        <a href="{{ route('admin.donations.show', $donation) }}"
           class="btn btn-light btn-sm">
            Cancel
        </a>

    </div>

    <x-message></x-message>

    <div id="donation-form-wrapper"
         style="{{ $donation->type ? '' : 'display:none;' }}">

        <div class="card">

            <div class="card-body">

                <div class="d-flex justify-content-between align-items-center mb-4">

                    <div>
                        <strong id="form-title">
                            {{ $donation->type === 'cash' ? 'Cash Donation' : 'In-Kind Donation' }}
                        </strong>

                        <div class="small text-muted">
                            Donation No. {{ $donation->donation_number }}
                        </div>
                    </div>

                    <button type="button"
                            class="btn btn-outline-secondary btn-sm"
                            id="change-type">
                        Change Type
                    </button>

                </div>


                {{-- =====================================================
                     CASH FORM
                ====================================================== --}}

                <form method="POST"
                      action="{{ route('admin.donations.update', $donation) }}"
                      id="cash-form"
                      style="{{ $donation->type === 'cash' ? '' : 'display:none;' }}">

                    @csrf
                    @method('PUT')

                    <input type="hidden"
                           name="type"
                           value="cash">

                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Donor
                            </label>

                            <select name="donor_id"
                                    class="form-control">

                                <option value="">
                                    Anonymous / Not Specified
                                </option>

                                @foreach($donors as $donor)

                                    <option value="{{ $donor->id }}"
                                        {{ old('donor_id', $donation->donor_id) == $donor->id ? 'selected' : '' }}>

                                        {{ $donor->name }}
                                        ({{ $donor->donor_number }})

                                    </option>

                                @endforeach

                            </select>

                        </div>


                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Date
                            </label>

                            <input type="date"
                                   name="donation_date"
                                   class="form-control"
                                   value="{{ old('donation_date', $donation->donation_date?->format('Y-m-d')) }}"
                                   required>

                        </div>


                        <div class="col-md-4 mb-3">

                            <label class="form-label">
                                Source
                            </label>

                            <select name="source"
                                    class="form-control"
                                    required>

                                @foreach([
                                    'manual' => 'Manual',
                                    'mpesa' => 'M-Pesa',
                                    'bank' => 'Bank',
                                    'other' => 'Other',
                                ] as $value => $label)

                                    <option value="{{ $value }}"
                                        {{ old('source', $donation->source) === $value ? 'selected' : '' }}>

                                        {{ $label }}

                                    </option>

                                @endforeach

                            </select>

                        </div>


                        <div class="col-md-5 mb-3">

                            <label class="form-label">
                                Amount
                            </label>

                            <input type="number"
                                   name="amount"
                                   step="0.01"
                                   min="0"
                                   class="form-control"
                                   value="{{ old('amount', $donation->amount) }}"
                                   required>

                        </div>


                        <div class="col-md-3 mb-3">

                            <label class="form-label">
                                Currency
                            </label>

                            <input type="text"
                                   name="currency"
                                   class="form-control"
                                   maxlength="3"
                                   value="{{ old('currency', $donation->currency) }}"
                                   required>

                        </div>


                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Classification
                            </label>

                            <select name="classification"
                                    class="form-control"
                                    required>

                                @foreach([
                                    'donation' => 'Donation',
                                    'payment' => 'Payment',
                                    'refund' => 'Refund',
                                    'other' => 'Other',
                                    'unclassified' => 'Unclassified',
                                ] as $value => $label)

                                    <option value="{{ $value }}"
                                        {{ old('classification', $donation->classification) === $value ? 'selected' : '' }}>

                                        {{ $label }}

                                    </option>

                                @endforeach

                            </select>

                        </div>


                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Purpose
                            </label>

                            <input type="text"
                                   name="purpose"
                                   class="form-control"
                                   value="{{ old('purpose', $donation->purpose) }}">

                        </div>


                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Reference
                            </label>

                            <input type="text"
                                   name="reference"
                                   class="form-control"
                                   value="{{ old('reference', $donation->reference) }}">

                        </div>


                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Payment Reference
                            </label>

                            <input type="text"
                                   name="payment_reference"
                                   class="form-control"
                                   value="{{ old('payment_reference', $donation->payment_reference) }}">

                        </div>


                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Description
                            </label>

                            <textarea name="description"
                                      class="form-control"
                                      rows="3">{{ old('description', $donation->description) }}</textarea>

                        </div>


                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Notes
                            </label>

                            <textarea name="notes"
                                      class="form-control"
                                      rows="3">{{ old('notes', $donation->notes) }}</textarea>

                        </div>

                    </div>

                    <button type="submit"
                            class="btn btn-primary">

                        Update Donation

                    </button>

                </form>


                {{-- =====================================================
                     IN-KIND FORM
                ====================================================== --}}

                <form method="POST"
                      action="{{ route('admin.donations.update', $donation) }}"
                      id="inkind-form"
                      style="{{ $donation->type === 'in_kind' ? '' : 'display:none;' }}">

                    @csrf
                    @method('PUT')

                    <input type="hidden"
                           name="type"
                           value="in_kind">

                    <input type="hidden"
                           name="source"
                           value="manual">

                    <input type="hidden"
                           name="currency"
                           value="KES">


                    <div class="row">

                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Donor
                            </label>

                            <select name="donor_id"
                                    class="form-control">

                                <option value="">
                                    Anonymous / Not Specified
                                </option>

                                @foreach($donors as $donor)

                                    <option value="{{ $donor->id }}"
                                        {{ old('donor_id', $donation->donor_id) == $donor->id ? 'selected' : '' }}>

                                        {{ $donor->name }}
                                        ({{ $donor->donor_number }})

                                    </option>

                                @endforeach

                            </select>

                        </div>


                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Date
                            </label>

                            <input type="date"
                                   name="donation_date"
                                   class="form-control"
                                   value="{{ old('donation_date', $donation->donation_date?->format('Y-m-d')) }}"
                                   required>

                        </div>


                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Classification
                            </label>

                            <select name="classification"
                                    class="form-control"
                                    required>

                                @foreach([
                                    'donation' => 'Donation',
                                    'payment' => 'Payment',
                                    'refund' => 'Refund',
                                    'other' => 'Other',
                                    'unclassified' => 'Unclassified',
                                ] as $value => $label)

                                    <option value="{{ $value }}"
                                        {{ old('classification', $donation->classification) === $value ? 'selected' : '' }}>

                                        {{ $label }}

                                    </option>

                                @endforeach

                            </select>

                        </div>


                        <div class="col-md-6 mb-3">

                            <label class="form-label">
                                Purpose
                            </label>

                            <input type="text"
                                   name="purpose"
                                   class="form-control"
                                   value="{{ old('purpose', $donation->purpose) }}">

                        </div>

                    </div>


                    <div class="d-flex justify-content-between align-items-center mb-2">

                        <strong>
                            Items
                        </strong>

                        <button type="button"
                                class="btn btn-outline-primary btn-sm"
                                id="add-item">

                            + Add Item

                        </button>

                    </div>


                    @php
                        $items = old('items', $donation->items->toArray());
                    @endphp

                    <div id="items-container">

                        @foreach($items as $index => $item)

                            <div class="item-row border rounded p-3 mb-2">

                                <div class="row">

                                    <div class="col-md-3 mb-2">

                                        <label class="small">
                                            Item
                                        </label>

                                        <input type="text"
                                               name="items[{{ $index }}][item]"
                                               class="form-control"
                                               value="{{ $item['item'] ?? '' }}"
                                               required>

                                    </div>


                                    <div class="col-md-2 mb-2">

                                        <label class="small">
                                            Quantity
                                        </label>

                                        <input type="number"
                                               name="items[{{ $index }}][quantity]"
                                               class="form-control"
                                               step="0.01"
                                               min="0"
                                               value="{{ $item['quantity'] ?? 1 }}">

                                    </div>


                                    <div class="col-md-2 mb-2">

                                        <label class="small">
                                            Unit
                                        </label>

                                        <input type="text"
                                               name="items[{{ $index }}][unit]"
                                               class="form-control"
                                               value="{{ $item['unit'] ?? '' }}">

                                    </div>


                                    <div class="col-md-2 mb-2">

                                        <label class="small">
                                            Estimated Value
                                        </label>

                                        <input type="number"
                                               name="items[{{ $index }}][estimated_value]"
                                               class="form-control estimated-value"
                                               step="0.01"
                                               min="0"
                                               value="{{ $item['estimated_value'] ?? '' }}">

                                    </div>


                                    <div class="col-md-2 mb-2">

                                        <label class="small">
                                            Condition
                                        </label>

                                        <input type="text"
                                               name="items[{{ $index }}][condition]"
                                               class="form-control"
                                               value="{{ $item['condition'] ?? '' }}">

                                    </div>


                                    <div class="col-md-1 mb-2 d-flex align-items-end">

                                        <button type="button"
                                                class="btn btn-outline-danger btn-sm remove-item">

                                            ×

                                        </button>

                                    </div>


                                    <div class="col-md-12">

                                        <input type="text"
                                               name="items[{{ $index }}][notes]"
                                               class="form-control"
                                               placeholder="Item notes"
                                               value="{{ $item['notes'] ?? '' }}">

                                    </div>

                                </div>

                            </div>

                        @endforeach

                    </div>


                    <div class="text-end border-top pt-3 mt-3">

                        <strong>
                            Total Estimated Value:
                        </strong>

                        <span class="h5 ms-2"
                              id="estimated-total">

                            KES 0.00

                        </span>

                    </div>


                    <div class="mt-3 mb-3">

                        <label class="form-label">
                            Description
                        </label>

                        <textarea name="description"
                                  class="form-control"
                                  rows="3">{{ old('description', $donation->description) }}</textarea>

                    </div>


                    <div class="mb-3">

                        <label class="form-label">
                            Notes
                        </label>

                        <textarea name="notes"
                                  class="form-control"
                                  rows="3">{{ old('notes', $donation->notes) }}</textarea>

                    </div>


                    <button type="submit"
                            class="btn btn-primary">

                        Update Donation

                    </button>

                </form>

            </div>

        </div>

    </div>

</div>


{{-- =============================================================
     TYPE MODAL
============================================================== --}}

<div class="modal fade"
     id="typeModal"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    Change Donation Type
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>

            </div>

            <div class="modal-body">

                <p class="text-muted">
                    Choose what you are recording.
                </p>

                <div class="row g-3">

                    <div class="col-6">

                        <button type="button"
                                class="btn btn-outline-primary w-100 py-3"
                                id="choose-cash">

                            <strong>Cash</strong>

                            <small class="d-block text-muted mt-1">
                                Money received
                            </small>

                        </button>

                    </div>


                    <div class="col-6">

                        <button type="button"
                                class="btn btn-outline-success w-100 py-3"
                                id="choose-inkind">

                            <strong>In-Kind</strong>

                            <small class="d-block text-muted mt-1">
                                Goods or items received
                            </small>

                        </button>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<style>

    .item-row {
        background: #fafafa;
    }

    .item-row .form-control {
        font-size: 14px;
    }

</style>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const wrapper = document.getElementById('donation-form-wrapper');

    const cashForm = document.getElementById('cash-form');

    const inkindForm = document.getElementById('inkind-form');

    const formTitle = document.getElementById('form-title');

    const typeModalElement = document.getElementById('typeModal');

    const typeModal = new bootstrap.Modal(typeModalElement);

    const changeTypeButton = document.getElementById('change-type');

    const chooseCash = document.getElementById('choose-cash');

    const chooseInkind = document.getElementById('choose-inkind');

    const itemsContainer = document.getElementById('items-container');

    const addItemButton = document.getElementById('add-item');

    const estimatedTotal = document.getElementById('estimated-total');


    let itemIndex = {{ count($items) }};


    /*
    |--------------------------------------------------------------------------
    | Change type
    |--------------------------------------------------------------------------
    */

    changeTypeButton.addEventListener('click', function () {

        typeModal.show();

    });


    chooseCash.addEventListener('click', function () {

        cashForm.style.display = '';

        inkindForm.style.display = 'none';

        formTitle.textContent = 'Cash Donation';

        typeModal.hide();

    });


    chooseInkind.addEventListener('click', function () {

        cashForm.style.display = 'none';

        inkindForm.style.display = '';

        formTitle.textContent = 'In-Kind Donation';

        typeModal.hide();

        calculateTotal();

    });


    /*
    |--------------------------------------------------------------------------
    | Add item
    |--------------------------------------------------------------------------
    */

    addItemButton.addEventListener('click', function () {

        const row = document.createElement('div');

        row.className = 'item-row border rounded p-3 mb-2';

        row.innerHTML = `

            <div class="row">

                <div class="col-md-3 mb-2">

                    <label class="small">
                        Item
                    </label>

                    <input type="text"
                           name="items[${itemIndex}][item]"
                           class="form-control"
                           required>

                </div>

                <div class="col-md-2 mb-2">

                    <label class="small">
                        Quantity
                    </label>

                    <input type="number"
                           name="items[${itemIndex}][quantity]"
                           class="form-control"
                           step="0.01"
                           min="0"
                           value="1">

                </div>

                <div class="col-md-2 mb-2">

                    <label class="small">
                        Unit
                    </label>

                    <input type="text"
                           name="items[${itemIndex}][unit]"
                           class="form-control">

                </div>

                <div class="col-md-2 mb-2">

                    <label class="small">
                        Estimated Value
                    </label>

                    <input type="number"
                           name="items[${itemIndex}][estimated_value]"
                           class="form-control estimated-value"
                           step="0.01"
                           min="0">

                </div>

                <div class="col-md-2 mb-2">

                    <label class="small">
                        Condition
                    </label>

                    <input type="text"
                           name="items[${itemIndex}][condition]"
                           class="form-control">

                </div>

                <div class="col-md-1 mb-2 d-flex align-items-end">

                    <button type="button"
                            class="btn btn-outline-danger btn-sm remove-item">

                        ×

                    </button>

                </div>

                <div class="col-md-12">

                    <input type="text"
                           name="items[${itemIndex}][notes]"
                           class="form-control"
                           placeholder="Item notes">

                </div>

            </div>
        `;

        itemsContainer.appendChild(row);

        itemIndex++;

        calculateTotal();

    });


    /*
    |--------------------------------------------------------------------------
    | Remove item
    |--------------------------------------------------------------------------
    */

    itemsContainer.addEventListener('click', function (event) {

        if (!event.target.classList.contains('remove-item')) {
            return;
        }

        event.target.closest('.item-row').remove();

        calculateTotal();

    });


    /*
    |--------------------------------------------------------------------------
    | Calculate estimated total
    |--------------------------------------------------------------------------
    */

    function calculateTotal() {

        let total = 0;

        document.querySelectorAll('.estimated-value').forEach(function (input) {

            total += parseFloat(input.value) || 0;

        });

        estimatedTotal.textContent =
            'KES ' +
            total.toLocaleString('en-KE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

    }


    itemsContainer.addEventListener('input', function (event) {

        if (event.target.classList.contains('estimated-value')) {

            calculateTotal();

        }

    });


    calculateTotal();

});

</script>

@endsection