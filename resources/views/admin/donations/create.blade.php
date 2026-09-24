@extends('layouts.admin')

@section('content')

<div class="container-fluid px-0">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h4 class="mb-1">Record Donation</h4>
            <small class="text-muted">
                Record a cash or in-kind contribution.
            </small>
        </div>

        <a href="{{ route('admin.donations.index') }}"
           class="btn btn-light border">
            <i class="fas fa-arrow-left me-1"></i>
            Back
        </a>

    </div>

    <x-message></x-message>


    {{-- =========================================================
         CASH FORM
    ========================================================== --}}
    <div id="cash-form" style="display:none;">

        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white d-flex justify-content-between align-items-center">

                <div>
                    <h6 class="mb-1">
                        <i class="fas fa-money-bill-wave text-success me-2"></i>
                        Cash Donation
                    </h6>

                    <small class="text-muted">
                        Record money received from a donor.
                    </small>
                </div>

                <button type="button"
                        class="btn btn-light btn-sm change-type">
                    Change Type
                </button>

            </div>


            <form action="{{ route('admin.donations.store') }}"
                  method="POST">

                @csrf

                <input type="hidden"
                       name="type"
                       value="cash">

                <input type="hidden"
                       name="currency"
                       value="KES">

                <div class="card-body p-4">

                    <div class="row g-3">

                        {{-- Donor --}}
                        <div class="col-md-6">

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
                                        {{ old('donor_id') == $donor->id ? 'selected' : '' }}>

                                        {{ $donor->donor_number }}
                                        - {{ $donor->name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Date --}}
                        <div class="col-md-3">

                            <label class="form-label">
                                Date <span class="text-danger">*</span>
                            </label>

                            <input type="date"
                                   name="donation_date"
                                   class="form-control"
                                   value="{{ old('donation_date', now()->format('Y-m-d')) }}"
                                   required>

                        </div>


                        {{-- Source --}}
                        <div class="col-md-3">

                            <label class="form-label">
                                Source <span class="text-danger">*</span>
                            </label>

                            <select name="source"
                                    class="form-control"
                                    required>

                                <option value="manual">
                                    Cash / Manual
                                </option>

                                <option value="bank"
                                    {{ old('source') === 'bank' ? 'selected' : '' }}>
                                    Bank
                                </option>

                                <option value="mpesa"
                                    {{ old('source') === 'mpesa' ? 'selected' : '' }}>
                                    M-Pesa
                                </option>

                                <option value="other"
                                    {{ old('source') === 'other' ? 'selected' : '' }}>
                                    Other
                                </option>

                            </select>

                        </div>


                        {{-- Amount --}}
                        <div class="col-md-4">

                            <label class="form-label">
                                Amount <span class="text-danger">*</span>
                            </label>

                            <div class="input-group">

                                <span class="input-group-text">
                                    KES
                                </span>

                                <input type="number"
                                       name="amount"
                                       class="form-control"
                                       value="{{ old('amount') }}"
                                       step="0.01"
                                       min="0"
                                       required>

                            </div>

                        </div>


                        {{-- Classification --}}
                        <div class="col-md-4">

                            <label class="form-label">
                                Classification
                            </label>

                            <select name="classification"
                                    class="form-control"
                                    required>

                                <option value="donation">
                                    Donation
                                </option>

                                <option value="payment">
                                    Payment
                                </option>

                                <option value="refund">
                                    Refund
                                </option>

                                <option value="other">
                                    Other
                                </option>

                                <option value="unclassified">
                                    Unclassified
                                </option>

                            </select>

                        </div>


                        {{-- Purpose --}}
                        <div class="col-md-4">

                            <label class="form-label">
                                Purpose
                            </label>

                            <select name="purpose"
                                    class="form-control">

                                <option value="">
                                    Select purpose
                                </option>

                                <option>General Support</option>
                                <option>Education</option>
                                <option>Child Sponsorship</option>
                                <option>Food & Nutrition</option>
                                <option>Medical Care</option>
                                <option>School Bus</option>
                                <option>Family Empowerment</option>
                                <option>Agriculture</option>
                                <option>Infrastructure</option>
                                <option>Emergency Support</option>
                                <option>Other</option>

                            </select>

                        </div>


                        {{-- Reference --}}
                        <div class="col-md-6">

                            <label class="form-label">
                                Reference
                            </label>

                            <input type="text"
                                   name="reference"
                                   class="form-control"
                                   placeholder="Receipt / cheque reference">

                        </div>


                        {{-- Payment Reference --}}
                        <div class="col-md-6">

                            <label class="form-label">
                                Payment Reference
                            </label>

                            <input type="text"
                                   name="payment_reference"
                                   class="form-control"
                                   placeholder="M-Pesa / bank transaction reference">

                        </div>


                        {{-- Description --}}
                        <div class="col-md-12">

                            <label class="form-label">
                                Description
                            </label>

                            <input type="text"
                                   name="description"
                                   class="form-control"
                                   placeholder="Brief description">

                        </div>


                        {{-- Notes --}}
                        <div class="col-md-12">

                            <label class="form-label">
                                Notes
                            </label>

                            <textarea name="notes"
                                      class="form-control"
                                      rows="3"></textarea>

                        </div>

                    </div>

                </div>


                <div class="card-footer bg-white text-end">

                    <button type="submit"
                            class="btn btn-primary px-4">

                        <i class="fas fa-save me-1"></i>
                        Save Donation

                    </button>

                </div>

            </form>

        </div>

    </div>


    {{-- =========================================================
         IN-KIND FORM
    ========================================================== --}}
    <div id="inkind-form" style="display:none;">

        <div class="card border-0 shadow-sm">

            <div class="card-header bg-white d-flex justify-content-between align-items-center">

                <div>
                    <h6 class="mb-1">
                        <i class="fas fa-box-open text-primary me-2"></i>
                        In-Kind Donation
                    </h6>

                    <small class="text-muted">
                        Record goods, supplies or equipment received.
                    </small>
                </div>

                <button type="button"
                        class="btn btn-light btn-sm change-type">
                    Change Type
                </button>

            </div>


            <form action="{{ route('admin.donations.store') }}"
                  method="POST">

                @csrf

                <input type="hidden"
                       name="type"
                       value="in_kind">

                <input type="hidden"
                       name="source"
                       value="manual">

                <input type="hidden"
                       name="currency"
                       value="KES">

                <div class="card-body p-4">

                    <div class="row g-3">

                        {{-- Donor --}}
                        <div class="col-md-6">

                            <label class="form-label">
                                Donor
                            </label>

                            <select name="donor_id"
                                    class="form-control">

                                <option value="">
                                    Anonymous / Not Specified
                                </option>

                                @foreach($donors as $donor)

                                    <option value="{{ $donor->id }}">

                                        {{ $donor->donor_number }}
                                        - {{ $donor->name }}

                                    </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- Date --}}
                        <div class="col-md-3">

                            <label class="form-label">
                                Date <span class="text-danger">*</span>
                            </label>

                            <input type="date"
                                   name="donation_date"
                                   class="form-control"
                                   value="{{ now()->format('Y-m-d') }}"
                                   required>

                        </div>


                        {{-- Classification --}}
                        <div class="col-md-3">

                            <label class="form-label">
                                Classification
                            </label>

                            <select name="classification"
                                    class="form-control"
                                    required>

                                <option value="donation">
                                    Donation
                                </option>

                                <option value="other">
                                    Other
                                </option>

                            </select>

                        </div>

                    </div>


                    <hr class="my-4">


                    {{-- Items --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">

                        <div>
                            <h6 class="mb-1">
                                Items Received
                            </h6>

                            <small class="text-muted">
                                Add each item and its estimated value.
                            </small>
                        </div>

                        <button type="button"
                                id="add-item"
                                class="btn btn-primary btn-sm">

                            <i class="fas fa-plus me-1"></i>
                            Add Item

                        </button>

                    </div>


                    <div id="items-container">

                        <div class="item-row border rounded p-3 mb-3">

                            <div class="row g-3">

                                <div class="col-md-4">

                                    <label class="form-label">
                                        Item
                                    </label>

                                    <input type="text"
                                           name="items[0][item]"
                                           class="form-control"
                                           placeholder="e.g. Maize Flour"
                                           required>

                                </div>

                                <div class="col-md-2">

                                    <label class="form-label">
                                        Quantity
                                    </label>

                                    <input type="number"
                                           name="items[0][quantity]"
                                           class="form-control"
                                           value="1"
                                           step="0.01"
                                           min="0">

                                </div>

                                <div class="col-md-2">

                                    <label class="form-label">
                                        Unit
                                    </label>

                                    <input type="text"
                                           name="items[0][unit]"
                                           class="form-control"
                                           placeholder="bags">

                                </div>

                                <div class="col-md-3">

                                    <label class="form-label">
                                        Estimated Value
                                    </label>

                                    <div class="input-group">

                                        <span class="input-group-text">
                                            KES
                                        </span>

                                        <input type="number"
                                               name="items[0][estimated_value]"
                                               class="form-control estimated-value"
                                               value="0"
                                               step="0.01"
                                               min="0">

                                    </div>

                                </div>

                                <div class="col-md-1 d-flex align-items-end">

                                    <button type="button"
                                            class="btn btn-outline-danger remove-item"
                                            style="display:none;">

                                        <i class="fas fa-trash"></i>

                                    </button>

                                </div>

                            </div>

                        </div>

                    </div>


                    {{-- Total --}}
                    <div class="d-flex justify-content-end mb-4">

                        <div class="text-end">

                            <small class="text-muted">
                                Total Estimated Value
                            </small>

                            <h4 class="mb-0">
                                KES
                                <span id="estimated-total">
                                    0.00
                                </span>
                            </h4>

                        </div>

                    </div>


                    {{-- Purpose --}}
                    <div class="row g-3">

                        <div class="col-md-6">

                            <label class="form-label">
                                Purpose
                            </label>

                            <select name="purpose"
                                    class="form-control">

                                <option value="">
                                    Select purpose
                                </option>

                                <option>General Support</option>
                                <option>Education</option>
                                <option>Child Sponsorship</option>
                                <option>Food & Nutrition</option>
                                <option>Medical Care</option>
                                <option>School Bus</option>
                                <option>Family Empowerment</option>
                                <option>Agriculture</option>
                                <option>Infrastructure</option>
                                <option>Emergency Support</option>
                                <option>Other</option>

                            </select>

                        </div>


                        <div class="col-md-6">

                            <label class="form-label">
                                Description
                            </label>

                            <input type="text"
                                   name="description"
                                   class="form-control">

                        </div>


                        <div class="col-md-12">

                            <label class="form-label">
                                Notes
                            </label>

                            <textarea name="notes"
                                      class="form-control"
                                      rows="3"></textarea>

                        </div>

                    </div>

                </div>


                <div class="card-footer bg-white text-end">

                    <button type="submit"
                            class="btn btn-primary px-4">

                        <i class="fas fa-save me-1"></i>
                        Save Donation

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


{{-- =============================================================
     TYPE SELECTION MODAL
============================================================== --}}

<div class="modal fade"
     id="donationTypeModal"
     tabindex="-1"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content border-0 shadow">

            <div class="modal-header border-0">

                <div>
                    <h5 class="modal-title mb-1">
                        What are you receiving?
                    </h5>

                    <small class="text-muted">
                        Choose the type of contribution.
                    </small>
                </div>

            </div>

            <div class="modal-body p-4">

                <div class="row g-3">

                    <div class="col-6">

                        <button type="button"
                                class="type-choice"
                                data-type="cash">

                            <div class="choice-icon text-success">
                                <i class="fas fa-money-bill-wave"></i>
                            </div>

                            <strong>
                                Cash
                            </strong>

                            <small>
                                Money received
                            </small>

                        </button>

                    </div>

                    <div class="col-6">

                        <button type="button"
                                class="type-choice"
                                data-type="in_kind">

                            <div class="choice-icon text-primary">
                                <i class="fas fa-box-open"></i>
                            </div>

                            <strong>
                                In-Kind
                            </strong>

                            <small>
                                Goods or items
                            </small>

                        </button>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


<style>

.type-choice {
    width: 100%;
    min-height: 150px;
    border: 1px solid #dee2e6;
    background: #fff;
    border-radius: 12px;
    padding: 20px;
    text-align: center;
    transition: .2s;
}

.type-choice:hover {
    border-color: #0d6efd;
    background: #f8fbff;
    transform: translateY(-2px);
}

.choice-icon {
    font-size: 32px;
    margin-bottom: 12px;
}

.type-choice strong,
.type-choice small {
    display: block;
}

.type-choice small {
    color: #6c757d;
    margin-top: 5px;
}

.item-row {
    background: #fafafa;
}

</style>


<script>

document.addEventListener('DOMContentLoaded', function () {

    const modalElement =
        document.getElementById('donationTypeModal');

    const modal =
        new bootstrap.Modal(modalElement, {
            backdrop: 'static',
            keyboard: false
        });

    const cashForm =
        document.getElementById('cash-form');

    const inkindForm =
        document.getElementById('inkind-form');

    const choices =
        document.querySelectorAll('.type-choice');

    const changeButtons =
        document.querySelectorAll('.change-type');

    const itemsContainer =
        document.getElementById('items-container');

    const estimatedTotal =
        document.getElementById('estimated-total');

    let itemIndex = 1;


    /*
     * Open the type selector when the page loads.
     */
    modal.show();


    /*
     * Select donation type.
     */
    choices.forEach(function (choice) {

        choice.addEventListener('click', function () {

            const type =
                this.dataset.type;

            cashForm.style.display =
                type === 'cash' ? 'block' : 'none';

            inkindForm.style.display =
                type === 'in_kind' ? 'block' : 'none';

            modal.hide();

        });

    });


    /*
     * Allow user to change type.
     */
    changeButtons.forEach(function (button) {

        button.addEventListener('click', function () {

            modal.show();

        });

    });


    /*
     * Calculate total estimated value.
     */
    function calculateTotal() {

        let total = 0;

        document.querySelectorAll('.estimated-value')
            .forEach(function (input) {

                total +=
                    parseFloat(input.value) || 0;

            });

        estimatedTotal.textContent =
            total.toLocaleString('en-KE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

    }


    /*
     * Recalculate when values change.
     */
    itemsContainer.addEventListener('input', function (event) {

        if (
            event.target.classList.contains('estimated-value')
        ) {
            calculateTotal();
        }

    });


    /*
     * Add item.
     */
    document.getElementById('add-item')
        .addEventListener('click', function () {

            const row =
                document.createElement('div');

            row.className =
                'item-row border rounded p-3 mb-3';

            row.innerHTML = `

                <div class="row g-3">

                    <div class="col-md-4">

                        <label class="form-label">
                            Item
                        </label>

                        <input type="text"
                               name="items[${itemIndex}][item]"
                               class="form-control"
                               placeholder="e.g. Maize Flour"
                               required>

                    </div>

                    <div class="col-md-2">

                        <label class="form-label">
                            Quantity
                        </label>

                        <input type="number"
                               name="items[${itemIndex}][quantity]"
                               class="form-control"
                               value="1"
                               step="0.01"
                               min="0">

                    </div>

                    <div class="col-md-2">

                        <label class="form-label">
                            Unit
                        </label>

                        <input type="text"
                               name="items[${itemIndex}][unit]"
                               class="form-control"
                               placeholder="bags">

                    </div>

                    <div class="col-md-3">

                        <label class="form-label">
                            Estimated Value
                        </label>

                        <div class="input-group">

                            <span class="input-group-text">
                                KES
                            </span>

                            <input type="number"
                                   name="items[${itemIndex}][estimated_value]"
                                   class="form-control estimated-value"
                                   value="0"
                                   step="0.01"
                                   min="0">

                        </div>

                    </div>

                    <div class="col-md-1 d-flex align-items-end">

                        <button type="button"
                                class="btn btn-outline-danger remove-item">

                            <i class="fas fa-trash"></i>

                        </button>

                    </div>

                </div>
            `;

            itemsContainer.appendChild(row);

            itemIndex++;

        });


    /*
     * Remove item.
     */
    itemsContainer.addEventListener('click', function (event) {

        const button =
            event.target.closest('.remove-item');

        if (!button) {
            return;
        }

        button.closest('.item-row').remove();

        calculateTotal();

    });


    calculateTotal();

});

</script>

@endsection