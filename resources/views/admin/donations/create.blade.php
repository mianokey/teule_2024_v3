@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/stores.css') }}">
@endpush

@section('content')

<div class="store-requisition-page">

    {{-- =========================================================
    PAGE HEADER
    ========================================================= --}}

    <div class="requisition-page-header">

        <div class="requisition-header-content">

            <div class="requisition-header-icon">
                <i class="fa fa-hand-holding-heart"></i>
            </div>

            <div>

                <div class="requisition-breadcrumb">
                    <span>Donations</span>
                    <span>/</span>
                    <span>New Donation</span>
                </div>

                <h1 class="requisition-page-title">
                    Record Donation
                </h1>

                <p class="requisition-page-subtitle">
                    Record cash or in-kind support received by Teule Kenya.
                </p>

            </div>

        </div>

        <div class="requisition-header-right">

            <a href="{{ route('admin.donations.index') }}" class="stores-btn-light">
                <i class="fa fa-arrow-left"></i>
                Back
            </a>

        </div>

    </div>

    <x-message></x-message>


    {{-- =========================================================
    INITIAL CONTINUE SCREEN
    ========================================================= --}}

    <div id="donationStartScreen">

        <div class="requisition-section">

            <div class="requisition-section-body">

                <div class="text-center py-5">

                    <div class="requisition-header-icon mx-auto mb-3">
                        <i class="fa fa-hand-holding-heart"></i>
                    </div>

                    <h4 class="mb-2">
                        Record a New Donation
                    </h4>

                    <p class="text-muted mb-4">
                        Start by choosing whether you are receiving cash
                        or an in-kind donation.
                    </p>

                    <button type="button" id="continueDonationButton" class="requisition-save-button">
                        Continue
                        <i class="fa fa-arrow-right ms-1"></i>
                    </button>

                </div>

            </div>

        </div>

    </div>


    {{-- =========================================================
    CASH FORM
    ========================================================= --}}

    <div id="cash-form" style="display:none;">

        <form action="{{ route('admin.donations.store') }}" method="POST">

            @csrf

            <input type="hidden" name="type" value="cash">

            <input type="hidden" name="currency" value="KES">


            {{-- =================================================
            CASH DONATION DETAILS
            ================================================= --}}

            <div class="requisition-section">

                <div class="requisition-section-header">

                    <div class="requisition-section-heading">

                        <div class="requisition-section-icon">
                            <i class="fa fa-money-bill-wave"></i>
                        </div>

                        <div>

                            <h5>
                                Cash Donation
                            </h5>

                            <p>
                                Record money received from a donor.
                            </p>

                        </div>

                    </div>

                    <button type="button" class="requisition-cancel-button change-type">
                        <i class="fa fa-exchange-alt"></i>
                        Change Type
                    </button>

                </div>


                {{-- USE THE SAME BODY SPACING AS STORES --}}

                <div class="requisition-details-body">

                    <div class="row g-3">

                        {{-- DONOR --}}

                        <div class="col-md-6">

                            <label class="requisition-field-label">
                                Donor
                            </label>

                            <select name="donor_id" class="form-select requisition-input">

                                <option value="">
                                    Anonymous / Not Specified
                                </option>

                                @foreach($donors as $donor)

                                <option value="{{ $donor->id }}" {{ old('donor_id')==$donor->id ? 'selected' : '' }}
                                    >
                                    {{ $donor->donor_number }}
                                    - {{ $donor->name }}
                                </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- DATE --}}

                        <div class="col-md-3">

                            <label class="requisition-field-label">
                                Date
                                <span class="required-mark">*</span>
                            </label>

                            <input type="date" name="donation_date" class="form-control requisition-input"
                                value="{{ old('donation_date', now()->format('Y-m-d')) }}" required>

                        </div>


                        {{-- SOURCE --}}

                        <div class="col-md-3">

                            <label class="requisition-field-label">
                                Source
                                <span class="required-mark">*</span>
                            </label>

                            <select name="source" class="form-select requisition-input" required>

                                <option value="manual" {{ old('source', 'manual' )==='manual' ? 'selected' : '' }}>
                                    Cash / Manual
                                </option>

                                <option value="bank" {{ old('source')==='bank' ? 'selected' : '' }}>
                                    Bank
                                </option>

                                <option value="mpesa" {{ old('source')==='mpesa' ? 'selected' : '' }}>
                                    M-Pesa
                                </option>

                                <option value="paypal" {{ old('source')==='paypal' ? 'selected' : '' }}> Paypal</option>

                                <option value="other" {{ old('source')==='other' ? 'selected' : '' }}>
                                    Other
                                </option>

                            </select>

                        </div>


                        {{-- AMOUNT --}}



                        <div class="col-md-2">

                            <label class="requisition-field-label">
                                Classification
                            </label>
                            <select name="currency" class="form-select requisition-input" required>
                                <option value="KES" {{ old('currency', 'KES' )==='KES' ? 'selected' : '' }}>
                                    KES
                                </option>

                                <option value="USD" {{ old('currency')==='USD' ? 'selected' : '' }}>
                                    USD
                                </option>

                                <option value="EUR" {{ old('currency')==='EUR' ? 'selected' : '' }}>
                                    EUR
                                </option>

                                <option value="GBP" {{ old('currency')==='GBP' ? 'selected' : '' }}>
                                    GBP
                                </option>

                                <option value="CAD" {{ old('currency')==='CAD' ? 'selected' : '' }}>
                                    CAD
                                </option>

                                <option value="AUD" {{ old('currency')==='AUD' ? 'selected' : '' }}>
                                    AUD
                                </option>

                                <option value="CHF" {{ old('currency')==='CHF' ? 'selected' : '' }}>
                                    CHF
                                </option>

                                <option value="ZAR" {{ old('currency')==='ZAR' ? 'selected' : '' }}>
                                    ZAR
                                </option>

                                <option value="UGX" {{ old('currency')==='UGX' ? 'selected' : '' }}>
                                    UGX
                                </option>

                                <option value="TZS" {{ old('currency')==='TZS' ? 'selected' : '' }}>
                                    TZS
                                </option>

                                <option value="RWF" {{ old('currency')==='RWF' ? 'selected' : '' }}>
                                    RWF
                                </option>

                                <option value="NGN" {{ old('currency')==='NGN' ? 'selected' : '' }}>
                                    NGN
                                </option>

                                <option value="GHS" {{ old('currency')==='GHS' ? 'selected' : '' }}>
                                    GHS
                                </option>

                                <option value="AED" {{ old('currency')==='AED' ? 'selected' : '' }}>
                                    AED
                                </option>

                                <option value="SAR" {{ old('currency')==='SAR' ? 'selected' : '' }}>
                                    SAR
                                </option>

                                <option value="JPY" {{ old('currency')==='JPY' ? 'selected' : '' }}>
                                    JPY
                                </option>

                                <option value="CNY" {{ old('currency')==='CNY' ? 'selected' : '' }}>
                                    CNY
                                </option>

                                <option value="INR" {{ old('currency')==='INR' ? 'selected' : '' }}>
                                    INR
                                </option>

                            </select>


                        </div>
                        <div class="col-md-3">

                            <label class="requisition-field-label">
                                Amount
                            </label>

                            <input type="number" name="amount"
                                class="form-control form-control-sm requisition-input" step="0.01" min="0"
                                value="{{ old('amount') }}">

                        </div>


                        {{-- CLASSIFICATION --}}

                        <div class="col-md-4">

                            <label class="requisition-field-label">
                                Classification
                            </label>

                            <select name="classification" class="form-select requisition-input" required>

                                <option value="donation" {{ old('classification', 'donation' )==='donation' ? 'selected'
                                    : '' }}>
                                    Donation
                                </option>

                                <option value="payment" {{ old('classification')==='payment' ? 'selected' : '' }}>
                                    Payment
                                </option>

                                <option value="refund" {{ old('classification')==='refund' ? 'selected' : '' }}>
                                    Refund
                                </option>

                                <option value="other" {{ old('classification')==='other' ? 'selected' : '' }}>
                                    Other
                                </option>

                                <option value="unclassified" {{ old('classification')==='unclassified' ? 'selected' : ''
                                    }}>
                                    Unclassified
                                </option>

                            </select>

                        </div>


                        {{-- PURPOSE --}}

                        <div class="col-md-3">

                            <label class="requisition-field-label">
                                Purpose
                            </label>

                            <select name="purpose" class="form-select requisition-input">

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


                        {{-- REFERENCE --}}

                        <div class="col-md-6">

                            <label class="requisition-field-label">
                                Reference
                            </label>

                            <input type="text" name="reference" class="form-control requisition-input"
                                value="{{ old('reference') }}" placeholder="Receipt / cheque reference">

                        </div>


                        {{-- PAYMENT REFERENCE --}}

                        <div class="col-md-6">

                            <label class="requisition-field-label">
                                Payment Reference
                            </label>

                            <input type="text" name="payment_reference" class="form-control requisition-input"
                                value="{{ old('payment_reference') }}"
                                placeholder="M-Pesa / bank transaction reference">

                        </div>


                        {{-- DESCRIPTION --}}

                        <div class="col-md-12">

                            <label class="requisition-field-label">
                                Description
                            </label>

                            <input type="text" name="description" class="form-control requisition-input"
                                value="{{ old('description') }}" placeholder="Brief description">

                        </div>


                        {{-- NOTES --}}

                        <div class="col-md-12">

                            <label class="requisition-field-label">
                                Notes
                            </label>

                            <textarea name="notes" class="form-control requisition-input requisition-textarea"
                                rows="3">{{ old('notes') }}</textarea>

                        </div>

                    </div>

                </div>

            </div>


            {{-- CASH ACTION BAR --}}

            <div class="requisition-action-bar">

                <div class="requisition-action-hint">
                    Review the donation details before recording it.
                </div>

                <div class="requisition-action-right">

                    <button type="button" class="requisition-cancel-button change-type">
                        <i class="fa fa-exchange-alt"></i>
                        Change Type
                    </button>

                    <button type="submit" class="requisition-save-button">
                        <i class="fa fa-save"></i>
                        Save Donation
                    </button>

                </div>

            </div>

        </form>

    </div>


    {{-- =========================================================
    IN-KIND FORM
    ========================================================= --}}

    <div id="inkind-form" style="display:none;">

        <form action="{{ route('admin.donations.store') }}" method="POST" id="inkindDonationForm">

            @csrf

            <input type="hidden" name="type" value="in_kind">

            <input type="hidden" name="source" value="manual">

            <input type="hidden" name="currency" value="KES">


            {{-- =================================================
            DONATION DETAILS
            ================================================= --}}

            <div class="requisition-section">

                <div class="requisition-section-header">

                    <div class="requisition-section-heading">

                        <div class="requisition-section-icon requisition-items-icon">
                            <i class="fa fa-box-open"></i>
                        </div>

                        <div>

                            <h5>
                                In-kind Donation
                            </h5>

                            <p>
                                Record goods, supplies or equipment received.
                            </p>

                        </div>

                    </div>

                    <button type="button" class="requisition-cancel-button change-type">
                        <i class="fa fa-exchange-alt"></i>
                        Change Type
                    </button>

                </div>


                <div class="requisition-details-body">

                    <div class="row g-3">

                        {{-- DONOR --}}

                        <div class="col-md-6">

                            <label class="requisition-field-label">
                                Donor
                            </label>

                            <select name="donor_id" class="form-select requisition-input">

                                <option value="">
                                    Anonymous / Not Specified
                                </option>

                                @foreach($donors as $donor)

                                <option value="{{ $donor->id }}" {{ old('donor_id')==$donor->id ? 'selected' : '' }}
                                    >
                                    {{ $donor->donor_number }}
                                    - {{ $donor->name }}
                                </option>

                                @endforeach

                            </select>

                        </div>


                        {{-- DATE --}}

                        <div class="col-md-3">

                            <label class="requisition-field-label">
                                Date
                                <span class="required-mark">*</span>
                            </label>

                            <input type="date" name="donation_date" class="form-control form-control-sm requisition-input"
                                value="{{ old('donation_date', now()->format('Y-m-d')) }}" required>

                        </div>


                        {{-- CLASSIFICATION --}}

                        <div class="col-md-3">

                            <label class="requisition-field-label">
                                Classification
                            </label>

                            <select name="classification" class="form-select requisition-input" required>

                                <option value="donation" {{ old('classification', 'donation' )==='donation' ? 'selected'
                                    : '' }}>
                                    Donation
                                </option>

                                <option value="other" {{ old('classification')==='other' ? 'selected' : '' }}>
                                    Other
                                </option>

                            </select>

                        </div>

                    </div>

                </div>

            </div>


            {{-- =================================================
            ITEMS RECEIVED
            ================================================= --}}

            <div class="requisition-section">

                <div class="requisition-section-header">

                    <div class="requisition-section-heading">

                        <div class="requisition-section-icon requisition-items-icon">
                            <i class="fa fa-box-open"></i>
                        </div>

                        <div>

                            <h5>
                                Items Received
                            </h5>

                            <p>
                                Add each item received as part of this in-kind donation.
                            </p>

                        </div>

                    </div>

                    <div class="requisition-header-right">

                        <span class="requisition-count-badge" id="item-count">
                            0 items
                        </span>

                        <button type="button" id="add-item" class="requisition-save-button">
                            <i class="fa fa-plus"></i>
                            Add Item
                        </button>

                    </div>

                </div>


                {{-- SAME INNER SPACING AS THE STORES PAGE --}}

                <div class="requisition-details-body">

                    <div class="requisition-list-table-wrapper">

                        <table class="requisition-list-table">

                            <thead>

                                <tr>

                                    <th style="width:45px;">
                                        #
                                    </th>

                                    <th>
                                        Item
                                    </th>

                                    <th>
                                        Variant
                                    </th>

                                    <th>
                                        Quantity
                                    </th>

                                    <th>
                                        Unit
                                    </th>

                                    <th>
                                        Estimated Value
                                    </th>

                                    <th style="width:70px;">
                                        Action
                                    </th>

                                </tr>

                            </thead>

                            <tbody id="items-container">
                            </tbody>

                        </table>

                    </div>


                    {{-- EMPTY STATE --}}

                    <div id="items-empty-state" class="text-center py-5">

                        <div class="requisition-header-icon mx-auto mb-3">
                            <i class="fa fa-box-open"></i>
                        </div>

                        <h6 class="mb-1">
                            No items added
                        </h6>

                        <p class="text-muted mb-3">
                            Add the goods or supplies received from the donor.
                        </p>

                        <button type="button" class="requisition-save-button add-item-trigger">
                            <i class="fa fa-plus"></i>
                            Add Item
                        </button>

                    </div>


                    {{-- TOTAL --}}

                    <div class="d-flex justify-content-end mt-4">

                        <div class="text-end">

                            <span class="stores-detail-label">
                                Total Estimated Value
                            </span>

                            <strong class="stores-detail-value d-block" style="font-size:18px;">
                                KES

                                <span id="estimated-total">
                                    0.00
                                </span>

                            </strong>

                        </div>

                    </div>

                </div>

            </div>


            {{-- =================================================
            OTHER DETAILS
            ================================================= --}}

            <div class="requisition-section">

                <div class="requisition-section-header">

                    <div class="requisition-section-heading">

                        <div class="requisition-section-icon">
                            <i class="fa fa-file-alt"></i>
                        </div>

                        <div>

                            <h5>
                                Donation Details
                            </h5>

                            <p>
                                Add any additional information about the donation.
                            </p>

                        </div>

                    </div>

                </div>


                <div class="requisition-details-body">

                    <div class="row g-3">

                        {{-- PURPOSE --}}

                        <div class="col-md-6">

                            <label class="requisition-field-label">
                                Purpose
                            </label>

                            <select name="purpose" class="form-select requisition-input">

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


                        {{-- DESCRIPTION --}}

                        <div class="col-md-6">

                            <label class="requisition-field-label">
                                Description
                            </label>

                            <input type="text" name="description" class="form-control requisition-input"
                                value="{{ old('description') }}" placeholder="Brief description">

                        </div>


                        {{-- NOTES --}}

                        <div class="col-md-12">

                            <label class="requisition-field-label">
                                Notes
                            </label>

                            <textarea name="notes" class="form-control requisition-input requisition-textarea"
                                rows="3">{{ old('notes') }}</textarea>

                        </div>

                    </div>

                </div>

            </div>


            {{-- =================================================
            IN-KIND ACTION BAR
            ================================================= --}}

            <div class="requisition-action-bar">

                <div class="requisition-action-hint">
                    Add all items received and enter their estimated values.
                </div>

                <div class="requisition-action-right">

                    <button type="button" class="requisition-cancel-button change-type">
                        <i class="fa fa-exchange-alt"></i>
                        Change Type
                    </button>

                    <button type="submit" class="requisition-save-button">
                        <i class="fa fa-save"></i>
                        Save Donation
                    </button>

                </div>

            </div>

        </form>

    </div>

</div>


{{-- =============================================================
DONATION TYPE MODAL
============================================================= --}}

<div class="modal fade" id="donationTypeModal" tabindex="-1" aria-labelledby="donationTypeModalLabel"
    aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content border-0 shadow">

            <div class="modal-header">

                <div>

                    <h5 class="modal-title mb-1" id="donationTypeModalLabel">
                        New Donation
                    </h5>

                    <div class="requisition-modal-subtitle">
                        First, tell us what kind of donation you want to record.
                    </div>

                </div>

                <button type="button" class="btn-close" id="closeDonationType" aria-label="Close"></button>

            </div>


            <div class="modal-body">

                <div id="donationTypeVisual" class="text-center mb-4">

                    <div id="defaultDonationVisual">

                        <div class="d-flex align-items-center justify-content-center gap-3">

                            <div class="requisition-header-icon">
                                <i class="fa fa-hand-holding-heart"></i>
                            </div>

                            <i class="fa fa-arrow-right fa-2x" aria-hidden="true"></i>

                            <div class="requisition-header-icon">
                                <i class="fa fa-question"></i>
                            </div>

                        </div>

                        <div class="requisition-modal-subtitle mt-3">
                            Choose a donation type to see how the support will be recorded.
                        </div>

                    </div>


                    <div id="cashDonationVisual" style="display:none;">

                        <div class="d-flex align-items-center justify-content-center gap-3">

                            <div class="text-center">

                                <div class="requisition-header-icon mx-auto mb-2">
                                    <i class="fa fa-user"></i>
                                </div>

                                <div class="small fw-semibold">
                                    Donor
                                </div>

                            </div>

                            <div class="px-2">

                                <i class="fa fa-arrow-right fa-beat-fade fa-2x" aria-hidden="true"></i>

                            </div>

                            <div class="text-center">

                                <div class="requisition-header-icon mx-auto mb-2">
                                    <i class="fa fa-money-bill-wave"></i>
                                </div>

                                <div class="small fw-semibold">
                                    Cash
                                </div>

                            </div>

                        </div>

                        <div class="requisition-modal-subtitle mt-3">

                            Money received from the donor

                            <strong>
                                will be recorded as a cash donation.
                            </strong>

                        </div>

                    </div>


                    <div id="inkindDonationVisual" style="display:none;">

                        <div class="d-flex align-items-center justify-content-center gap-3">

                            <div class="text-center">

                                <div class="requisition-header-icon mx-auto mb-2">
                                    <i class="fa fa-user"></i>
                                </div>

                                <div class="small fw-semibold">
                                    Donor
                                </div>

                            </div>

                            <div class="px-2">

                                <i class="fa fa-arrow-right fa-beat-fade fa-2x" aria-hidden="true"></i>

                            </div>

                            <div class="text-center">

                                <div class="requisition-header-icon mx-auto mb-2">
                                    <i class="fa fa-box-open"></i>
                                </div>

                                <div class="small fw-semibold">
                                    In-kind
                                </div>

                            </div>

                        </div>

                        <div class="requisition-modal-subtitle mt-3">

                            Goods, supplies or equipment received from the donor

                            <strong>
                                will be recorded as an in-kind donation.
                            </strong>

                        </div>

                    </div>

                </div>


                <div class="mb-3">

                    <label for="setup_donation_type" class="requisition-field-label">
                        Donation Type
                        <span class="required-mark">*</span>
                    </label>

                    <select id="setup_donation_type" class="form-select requisition-input">

                        <option value="">
                            Select donation type
                        </option>

                        <option value="cash">
                            Cash Donation
                        </option>

                        <option value="in_kind">
                            In-kind Donation
                        </option>

                    </select>

                </div>


                <div id="donationTypeMessage" class="requisition-modal-subtitle">
                    Select a donation type to continue.
                </div>

            </div>


            <div class="modal-footer">

                <button type="button" id="cancelDonationType" class="requisition-cancel-button">
                    Cancel
                </button>

                <button type="button" id="continueDonationType" class="requisition-save-button">
                    Continue
                    <i class="fa fa-arrow-right ms-1"></i>
                </button>

            </div>

        </div>

    </div>

</div>


{{-- =============================================================
IN-KIND ITEM SELECTION MODAL
============================================================= --}}

<div class="modal fade" id="storeItemSelectionModal" tabindex="-1" aria-labelledby="storeItemSelectionModalLabel"
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

                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

            </div>


            <div class="modal-body">

                <div class="requisition-child-search mb-3">

                    <i class="fa fa-search"></i>

                    <input type="text" id="storeItemSearch" class="form-control requisition-input"
                        placeholder="Search items..." autocomplete="off">

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


                            <tr class="store-item-option" tabindex="0" title="Double-click to select this item"
                                data-search="{{ strtolower(
                                        $storeItem->name
                                        . ' '
                                        . ($storeItem->sku ?? '')
                                        . ' '
                                        . $unitName
                                    ) }}" data-store-item-id="{{ $storeItem->id }}"
                                data-store-item-name="{{ $storeItem->name }}" data-store-item-unit="{{ $unitName }}">

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


                <div id="storeItemNoResults" class="requisition-no-children-found" style="display:none;">
                    No items match your search.
                </div>

            </div>


            <div class="modal-footer">

                <button type="button" class="requisition-cancel-button" data-bs-dismiss="modal">
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
    | MODALS
    |--------------------------------------------------------------------------
    */

    const donationTypeModalElement =
        document.getElementById('donationTypeModal');

    const donationTypeModal =
        donationTypeModalElement
            ? new bootstrap.Modal(
                donationTypeModalElement,
                {
                    backdrop: 'static',
                    keyboard: false
                }
            )
            : null;


    const storeItemSelectionModalElement =
        document.getElementById('storeItemSelectionModal');

    const storeItemSelectionModal =
        storeItemSelectionModalElement
            ? new bootstrap.Modal(
                storeItemSelectionModalElement
            )
            : null;


    /*
    |--------------------------------------------------------------------------
    | MAIN ELEMENTS
    |--------------------------------------------------------------------------
    */

    const startScreen =
        document.getElementById('donationStartScreen');

    const cashForm =
        document.getElementById('cash-form');

    const inkindForm =
        document.getElementById('inkind-form');

    const continueButton =
        document.getElementById('continueDonationButton');

    const changeTypeButtons =
        document.querySelectorAll('.change-type');

    const itemsContainer =
        document.getElementById('items-container');

    const emptyState =
        document.getElementById('items-empty-state');

    const itemCount =
        document.getElementById('item-count');

    const estimatedTotal =
        document.getElementById('estimated-total');

    const storeItemSearch =
        document.getElementById('storeItemSearch');

    const storeItemNoResults =
        document.getElementById('storeItemNoResults');


    /*
    |--------------------------------------------------------------------------
    | DONATION TYPE MODAL
    |--------------------------------------------------------------------------
    */

    const donationTypeSelect =
        document.getElementById('setup_donation_type');

    const donationTypeMessage =
        document.getElementById('donationTypeMessage');

    const defaultDonationVisual =
        document.getElementById('defaultDonationVisual');

    const cashDonationVisual =
        document.getElementById('cashDonationVisual');

    const inkindDonationVisual =
        document.getElementById('inkindDonationVisual');

    const continueDonationType =
        document.getElementById('continueDonationType');

    const closeDonationType =
        document.getElementById('closeDonationType');

    const cancelDonationType =
        document.getElementById('cancelDonationType');


    /*
    |--------------------------------------------------------------------------
    | STORE ITEM / VARIANT DATA
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
    | STATE
    |--------------------------------------------------------------------------
    */

    let itemIndex = 0;

    let activeItemRow = null;


    /*
    |--------------------------------------------------------------------------
    | RESET DONATION TYPE MODAL
    |--------------------------------------------------------------------------
    */

    function resetDonationTypeModal() {

        if (donationTypeSelect) {
            donationTypeSelect.value = '';
        }

        if (defaultDonationVisual) {
            defaultDonationVisual.style.display = 'block';
        }

        if (cashDonationVisual) {
            cashDonationVisual.style.display = 'none';
        }

        if (inkindDonationVisual) {
            inkindDonationVisual.style.display = 'none';
        }

        if (donationTypeMessage) {
            donationTypeMessage.textContent =
                'Select a donation type to continue.';
        }

    }


    /*
    |--------------------------------------------------------------------------
    | OPEN DONATION TYPE MODAL
    |--------------------------------------------------------------------------
    */

    if (continueButton && donationTypeModal) {

        continueButton.addEventListener('click', function () {

            resetDonationTypeModal();

            donationTypeModal.show();

        });

    }


    /*
    |--------------------------------------------------------------------------
    | CLOSE DONATION TYPE MODAL
    |--------------------------------------------------------------------------
    */

    if (closeDonationType && donationTypeModal) {

        closeDonationType.addEventListener('click', function () {

            donationTypeModal.hide();

        });

    }


    if (cancelDonationType && donationTypeModal) {

        cancelDonationType.addEventListener('click', function () {

            donationTypeModal.hide();

        });

    }


    /*
    |--------------------------------------------------------------------------
    | DONATION TYPE SELECTION
    |--------------------------------------------------------------------------
    */

    if (donationTypeSelect) {

        donationTypeSelect.addEventListener('change', function () {

            const selectedType =
                this.value;


            if (defaultDonationVisual) {
                defaultDonationVisual.style.display = 'none';
            }

            if (cashDonationVisual) {
                cashDonationVisual.style.display = 'none';
            }

            if (inkindDonationVisual) {
                inkindDonationVisual.style.display = 'none';
            }


            if (!selectedType) {

                if (defaultDonationVisual) {
                    defaultDonationVisual.style.display = 'block';
                }

                if (donationTypeMessage) {
                    donationTypeMessage.textContent =
                        'Select a donation type to continue.';
                }

                return;

            }


            if (selectedType === 'cash') {

                if (cashDonationVisual) {
                    cashDonationVisual.style.display = 'block';
                }

                if (donationTypeMessage) {
                    donationTypeMessage.innerHTML =
                        'You are recording <strong>money received</strong> from a donor.';
                }

                return;

            }


            if (selectedType === 'in_kind') {

                if (inkindDonationVisual) {
                    inkindDonationVisual.style.display = 'block';
                }

                if (donationTypeMessage) {
                    donationTypeMessage.innerHTML =
                        'You are recording <strong>goods, supplies or equipment</strong> received from a donor.';
                }

            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | CONTINUE FROM DONATION TYPE MODAL
    |--------------------------------------------------------------------------
    */

    if (continueDonationType) {

        continueDonationType.addEventListener('click', function () {

            const selectedType =
                donationTypeSelect
                    ? donationTypeSelect.value
                    : '';


            if (!selectedType) {

                if (donationTypeSelect) {
                    donationTypeSelect.focus();
                }

                if (donationTypeMessage) {
                    donationTypeMessage.textContent =
                        'Please select a donation type before continuing.';
                }

                return;

            }


            if (donationTypeModal) {
                donationTypeModal.hide();
            }


            if (startScreen) {
                startScreen.style.display = 'none';
            }

            if (cashForm) {
                cashForm.style.display = 'none';
            }

            if (inkindForm) {
                inkindForm.style.display = 'none';
            }


            if (selectedType === 'cash') {

                if (cashForm) {
                    cashForm.style.display = 'block';
                }

            }


            if (selectedType === 'in_kind') {

                if (inkindForm) {
                    inkindForm.style.display = 'block';
                }

            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | CHANGE TYPE
    |--------------------------------------------------------------------------
    */

    changeTypeButtons.forEach(function (button) {

        button.addEventListener('click', function () {

            if (cashForm) {
                cashForm.style.display = 'none';
            }

            if (inkindForm) {
                inkindForm.style.display = 'none';
            }

            resetDonationTypeModal();

            if (donationTypeModal) {
                donationTypeModal.show();
            }

        });

    });


    /*
    |--------------------------------------------------------------------------
    | ADD ITEM BUTTON
    |--------------------------------------------------------------------------
    */

    const addItemButton =
        document.getElementById('add-item');

    if (addItemButton) {

        addItemButton.addEventListener('click', function () {

            addItemRow();

        });

    }


    /*
    |--------------------------------------------------------------------------
    | ADD ITEM EMPTY STATE BUTTON
    |--------------------------------------------------------------------------
    */

    const addItemTrigger =
        document.querySelector('.add-item-trigger');

    if (addItemTrigger) {

        addItemTrigger.addEventListener('click', function () {

            addItemRow();

        });

    }


    /*
    |--------------------------------------------------------------------------
    | ADD ITEM ROW
    |--------------------------------------------------------------------------
    */

    function addItemRow() {

        if (!itemsContainer) {
            return;
        }


        const index =
            itemIndex++;


        const row =
            document.createElement('tr');


        row.className =
            'donation-item-row';


        row.dataset.index =
            index;


        row.innerHTML = `

            <td>

                <span class="requisition-row-number">
                    ${document.querySelectorAll('.donation-item-row').length + 1}
                </span>

            </td>


            <td>

                <button
                    type="button"
                    class="requisition-lpo-picker store-item-picker"
                >

                    <span class="requisition-lpo-picker-icon">
                        <i class="fas fa-box-open"></i>
                    </span>

                    <span class="requisition-lpo-picker-text store-item-picker-text">

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
                    name="items[${index}][store_item_id]"
                    class="store-item-id"
                >


                <input
                    type="hidden"
                    name="items[${index}][item]"
                    class="legacy-item"
                >

            </td>


            <td>

                <select
                    name="items[${index}][variant_id]"
                    class="form-select requisition-input item-variant"
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
                    name="items[${index}][quantity]"
                    class="form-control requisition-input item-quantity"
                    value="1"
                    step="0.001"
                    min="0.001"
                    required
                >

            </td>


            <td>

                <input
                    type="hidden"
                    name="items[${index}][unit]"
                    class="item-unit"
                >

                <input
                    type="text"
                    class="form-control requisition-input item-unit-display"
                    value="—"
                    readonly
                >

            </td>


            <td>

              <div class="input-group" style="display: flex; flex-wrap: nowrap; align-items: stretch;">
    <span class="input-group-text" style="white-space: nowrap;">
        KES
    </span>

    <input
        type="number"
        name="items[${index}][estimated_value]"
        class="form-control requisition-input estimated-value"
        value="0"
        step="0.01"
        min="0"
        style="width: 1%; flex: 1 1 auto;"
    >
</div>

            </td>


            <td class="text-center">

                <button
                    type="button"
                    class="requisition-list-action remove-item"
                    title="Remove item"
                >

                    <i class="fa fa-trash"></i>

                </button>

            </td>

        `;


        itemsContainer.appendChild(row);


        updateItemState();

        calculateTotal();


        activeItemRow =
            row;


        if (storeItemSearch) {
            storeItemSearch.value = '';
        }


        filterStoreItems('');


        if (storeItemSelectionModal) {
            storeItemSelectionModal.show();
        }

    }


    /*
    |--------------------------------------------------------------------------
    | ITEM PICKER
    |--------------------------------------------------------------------------
    */

    document.addEventListener('click', function (event) {

        const picker =
            event.target.closest('.store-item-picker');


        if (!picker) {
            return;
        }


        activeItemRow =
            picker.closest('.donation-item-row');


        if (storeItemSearch) {
            storeItemSearch.value = '';
        }


        filterStoreItems('');


        if (storeItemSelectionModal) {
            storeItemSelectionModal.show();
        }

    });


    /*
    |--------------------------------------------------------------------------
    | DOUBLE-CLICK ITEM ROW TO SELECT
    |--------------------------------------------------------------------------
    */

    document.addEventListener('dblclick', function (event) {

        const itemRow =
            event.target.closest('.store-item-option');


        if (!itemRow || !activeItemRow) {
            return;
        }


        selectStoreItem(itemRow);

    });


    /*
    |--------------------------------------------------------------------------
    | SELECT STORE ITEM
    |--------------------------------------------------------------------------
    */

    function selectStoreItem(itemRow) {

        if (!activeItemRow) {
            return;
        }


        const itemId =
            itemRow.dataset.storeItemId || '';

        const itemName =
            itemRow.dataset.storeItemName || '';

        const itemUnit =
            itemRow.dataset.storeItemUnit || '';


        if (!itemId) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Hidden Values
        |--------------------------------------------------------------------------
        */

        const storeItemIdInput =
            activeItemRow.querySelector('.store-item-id');

        const legacyItemInput =
            activeItemRow.querySelector('.legacy-item');

        const unitInput =
            activeItemRow.querySelector('.item-unit');

        const unitDisplay =
            activeItemRow.querySelector('.item-unit-display');


        if (storeItemIdInput) {
            storeItemIdInput.value = itemId;
        }


        if (legacyItemInput) {
            legacyItemInput.value = itemName;
        }


        if (unitInput) {
            unitInput.value = itemUnit;
        }


        if (unitDisplay) {
            unitDisplay.value = itemUnit || '—';
        }


        /*
        |--------------------------------------------------------------------------
        | Picker Display
        |--------------------------------------------------------------------------
        */

        const pickerText =
            activeItemRow.querySelector(
                '.store-item-picker-text'
            );


        if (pickerText) {

            pickerText.innerHTML = `

                <strong>
                    ${escapeHtml(itemName)}
                </strong>

                ${
                    itemUnit
                        ? `<small>${escapeHtml(itemUnit)}</small>`
                        : ''
                }

            `;

        }


        /*
        |--------------------------------------------------------------------------
        | Variants
        |--------------------------------------------------------------------------
        */

        const variantSelect =
            activeItemRow.querySelector('.item-variant');


        if (variantSelect) {

            variantSelect.innerHTML = `

                <option value="">
                    No variant
                </option>

            `;


            const variants =
                variantData[itemId] || [];


            variants.forEach(function (variant) {

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


                variantSelect.appendChild(option);

            });


            variantSelect.disabled =
                variants.length === 0;

        }


        /*
        |--------------------------------------------------------------------------
        | Close Modal
        |--------------------------------------------------------------------------
        */

        if (storeItemSelectionModal) {
            storeItemSelectionModal.hide();
        }

    }


    /*
    |--------------------------------------------------------------------------
    | REMOVE ITEM
    |--------------------------------------------------------------------------
    */

    document.addEventListener('click', function (event) {

        const removeButton =
            event.target.closest('.remove-item');


        if (!removeButton) {
            return;
        }


        const row =
            removeButton.closest('.donation-item-row');


        if (!row) {
            return;
        }


        row.remove();


        updateItemState();

        calculateTotal();

    });


    /*
    |--------------------------------------------------------------------------
    | SEARCH ITEMS
    |--------------------------------------------------------------------------
    */

    if (storeItemSearch) {

        storeItemSearch.addEventListener(
            'input',
            function () {

                filterStoreItems(this.value);

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
            .querySelectorAll('.store-item-option')
            .forEach(function (row) {

                const haystack =
                    row.dataset.search || '';


                const matches =
                    haystack.includes(search);


                row.style.display =
                    matches
                        ? ''
                        : 'none';


                if (matches) {
                    visibleCount++;
                }

            });


        if (storeItemNoResults) {

            storeItemNoResults.style.display =
                visibleCount === 0
                    ? 'block'
                    : 'none';

        }

    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE ITEM COUNT / EMPTY STATE
    |--------------------------------------------------------------------------
    */

    function updateItemState() {

        const rows =
            document.querySelectorAll(
                '.donation-item-row'
            );


        const count =
            rows.length;


        if (itemCount) {

            itemCount.textContent =
                count === 1
                    ? '1 item'
                    : count + ' items';

        }


        if (emptyState) {

            emptyState.style.display =
                count === 0
                    ? 'block'
                    : 'none';

        }


        rows.forEach(function (row, index) {

            const number =
                row.querySelector(
                    '.requisition-row-number'
                );


            if (number) {
                number.textContent = index + 1;
            }

        });

    }


    /*
    |--------------------------------------------------------------------------
    | CALCULATE TOTAL
    |--------------------------------------------------------------------------
    */

    function calculateTotal() {

        let total =
            0;


        document
            .querySelectorAll('.estimated-value')
            .forEach(function (input) {

                total +=
                    parseFloat(input.value) || 0;

            });


        if (estimatedTotal) {

            estimatedTotal.textContent =
                total.toLocaleString(
                    'en-KE',
                    {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }
                );

        }

    }


    /*
    |--------------------------------------------------------------------------
    | ESTIMATED VALUE CHANGES
    |--------------------------------------------------------------------------
    */

    document.addEventListener(
        'input',
        function (event) {

            if (
                event.target.classList.contains(
                    'estimated-value'
                )
            ) {

                calculateTotal();

            }

        }
    );


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
    | INITIAL STATE
    |--------------------------------------------------------------------------
    */

    if (startScreen) {
        startScreen.style.display = 'block';
    }


    if (cashForm) {
        cashForm.style.display = 'none';
    }


    if (inkindForm) {
        inkindForm.style.display = 'none';
    }


    updateItemState();

    calculateTotal();


    /*
    |--------------------------------------------------------------------------
    | RESTORE FORM AFTER VALIDATION ERROR
    |--------------------------------------------------------------------------
    */

    const oldDonationType =
        @json(old('type'));


    if (oldDonationType === 'cash') {

        if (startScreen) {
            startScreen.style.display = 'none';
        }

        if (cashForm) {
            cashForm.style.display = 'block';
        }

    }


    if (oldDonationType === 'in_kind') {

        if (startScreen) {
            startScreen.style.display = 'none';
        }

        if (inkindForm) {
            inkindForm.style.display = 'block';
        }

    }

});

</script>

@endpush

@endsection