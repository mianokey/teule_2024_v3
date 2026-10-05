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
                <i class="fas fa-truck"></i>
            </div>

            <div>

                <div class="requisition-breadcrumb">
                    <span>Stores</span>
                    <i class="fas fa-chevron-right"></i>
                    <span>Suppliers</span>
                    <i class="fas fa-chevron-right"></i>
                    <span>Add Supplier</span>
                </div>

                <h1 class="requisition-page-title">
                    Add Supplier
                </h1>

                <p class="requisition-page-subtitle">
                    Create a supplier record for purchasing and store LPOs.
                </p>

            </div>

        </div>

        <div class="requisition-page-actions">

            <a href="{{ route('admin.suppliers.index') }}"
               class="requisition-cancel-button">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Suppliers</span>
            </a>

        </div>

    </div>


    {{-- ============================================================
         MESSAGES
         ============================================================ --}}
    <x-message></x-message>


    {{-- ============================================================
         VALIDATION ERRORS
         ============================================================ --}}
    @if($errors->any())

        <div class="requisition-state-panel">

            <div class="requisition-state-main">

                <div class="requisition-state-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>

                <div class="requisition-state-text">

                    <strong>
                        Please correct the following errors
                    </strong>

                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>

                </div>

            </div>

        </div>

    @endif


    <form method="POST"
          action="{{ route('admin.suppliers.store') }}">

        @csrf


        {{-- ========================================================
             SUPPLIER INFORMATION
             ======================================================== --}}
        <div class="requisition-section">

            <div class="requisition-section-header">

                <div class="requisition-section-heading">

                    <div class="requisition-section-icon">
                        <i class="fas fa-building"></i>
                    </div>

                    <div>
                        <h5>Supplier Information</h5>
                        <p>Basic supplier and contact details.</p>
                    </div>

                </div>

            </div>


            <div class="row">

                {{-- Supplier Name --}}
                <div class="col-md-3">

                    <div class="requisition-detail-item">

                        <label for="name"
                               class="requisition-detail-label">
                            Supplier Name
                            <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               id="name"
                               name="name"
                               value="{{ old('name') }}"
                               class="form-control requisition-input @error('name') is-invalid @enderror"
                               placeholder="Supplier name"
                               required>

                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>


                {{-- Contact Person --}}
                <div class="col-md-3">

                    <div class="requisition-detail-item">

                        <label for="contact_person"
                               class="requisition-detail-label">
                            Contact Person
                        </label>

                        <input type="text"
                               id="contact_person"
                               name="contact_person"
                               value="{{ old('contact_person') }}"
                               class="form-control requisition-input @error('contact_person') is-invalid @enderror"
                               placeholder="Contact person's name">

                        @error('contact_person')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>


                {{-- Phone --}}
                <div class="col-md-3">

                    <div class="requisition-detail-item">

                        <label for="phone"
                               class="requisition-detail-label">
                            Phone
                        </label>

                        <input type="text"
                               id="phone"
                               name="phone"
                               value="{{ old('phone') }}"
                               class="form-control requisition-input @error('phone') is-invalid @enderror"
                               placeholder="Phone number">

                        @error('phone')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>


                {{-- Email --}}
                <div class="col-md-3">

                    <div class="requisition-detail-item">

                        <label for="email"
                               class="requisition-detail-label">
                            Email
                        </label>

                        <input type="email"
                               id="email"
                               name="email"
                               value="{{ old('email') }}"
                               class="form-control requisition-input @error('email') is-invalid @enderror"
                               placeholder="Email address">

                        @error('email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>


                {{-- Address --}}
                <div class="col-md-8">

                    <div class="requisition-detail-item">

                        <label for="address"
                               class="requisition-detail-label">
                            Address
                        </label>

                        <input type="text"
                               id="address"
                               name="address"
                               value="{{ old('address') }}"
                               class="form-control requisition-input @error('address') is-invalid @enderror"
                               placeholder="Physical or postal address">

                        @error('address')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>


                {{-- Tax PIN --}}
                <div class="col-md-4">

                    <div class="requisition-detail-item">

                        <label for="tax_pin"
                               class="requisition-detail-label">
                            Tax PIN
                        </label>

                        <input type="text"
                               id="tax_pin"
                               name="tax_pin"
                               value="{{ old('tax_pin') }}"
                               class="form-control requisition-input @error('tax_pin') is-invalid @enderror"
                               placeholder="KRA PIN">

                        @error('tax_pin')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================
             PAYMENT & ACCOUNT
             ======================================================== --}}
        <div class="requisition-section">

            <div class="requisition-section-header">

                <div class="requisition-section-heading">

                    <div class="requisition-section-icon">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>

                    <div>
                        <h5>Payment & Account</h5>
                        <p>Payment terms and account opening information.</p>
                    </div>

                </div>

            </div>


            <div class="row">

                {{-- Payment Terms --}}
                <div class="col-md-4">

                    <div class="requisition-detail-item">

                        <label for="payment_terms"
                               class="requisition-detail-label">
                            Payment Terms
                        </label>

                        <input type="text"
                               id="payment_terms"
                               name="payment_terms"
                               value="{{ old('payment_terms') }}"
                               class="form-control requisition-input @error('payment_terms') is-invalid @enderror"
                               placeholder="e.g. Cash, 30 Days">

                        @error('payment_terms')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>


                {{-- Credit Limit --}}
                <div class="col-md-4">

                    <div class="requisition-detail-item">

                        <label for="credit_limit"
                               class="requisition-detail-label">
                            Credit Limit
                        </label>

                        <input type="number"
                               id="credit_limit"
                               name="credit_limit"
                               value="{{ old('credit_limit', 0) }}"
                               class="form-control requisition-input @error('credit_limit') is-invalid @enderror"
                               min="0"
                               step="0.01"
                               placeholder="0.00">

                        @error('credit_limit')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>


                {{-- Opening Balance --}}
                <div class="col-md-4">

                    <div class="requisition-detail-item">

                        <label for="opening_balance"
                               class="requisition-detail-label">
                            Opening Balance
                        </label>

                        <input type="number"
                               id="opening_balance"
                               name="opening_balance"
                               value="{{ old('opening_balance', 0) }}"
                               class="form-control requisition-input @error('opening_balance') is-invalid @enderror"
                               min="0"
                               step="0.01"
                               placeholder="0.00">

                        @error('opening_balance')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================
             ADDITIONAL INFORMATION
             ======================================================== --}}
        <div class="requisition-section">

            <div class="requisition-section-header">

                <div class="requisition-section-heading">

                    <div class="requisition-section-icon">
                        <i class="fas fa-sticky-note"></i>
                    </div>

                    <div>
                        <h5>Additional Information</h5>
                        <p>Optional notes and supplier status.</p>
                    </div>

                </div>

            </div>


            <div class="row">

                {{-- Notes --}}
                <div class="col-md-8">

                    <div class="requisition-detail-item">

                        <label for="notes"
                               class="requisition-detail-label">
                            Notes
                        </label>

                        <textarea id="notes"
                                  name="notes"
                                  rows="2"
                                  class="form-control requisition-input @error('notes') is-invalid @enderror"
                                  placeholder="Additional supplier information">{{ old('notes') }}</textarea>

                        @error('notes')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>


                {{-- Status --}}
                <div class="col-md-4">

                    <div class="requisition-detail-item">

                        <span class="requisition-detail-label">
                            Supplier Status
                        </span>

                        <div class="requisition-state-panel">

                            <div class="requisition-state-main">

                                <div class="requisition-state-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>

                                <div class="requisition-state-text">

                                    <strong>
                                        Active
                                    </strong>

                                    <span>
                                        Available for LPOs and receipts.
                                    </span>

                                </div>

                            </div>

                            <div class="requisition-state-actions">

                                <label>
                                    <input type="checkbox"
                                           id="is_active"
                                           name="is_active"
                                           value="1"
                                           {{ old('is_active', true) ? 'checked' : '' }}>

                                    <span>
                                        Keep active
                                    </span>
                                </label>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================
             BOTTOM ACTIONS
             ======================================================== --}}
        <div class="requisition-bottom-actions">

            <div class="requisition-bottom-actions-left">

                <a href="{{ route('admin.suppliers.index') }}"
                   class="requisition-cancel-button">

                    <i class="fas fa-times"></i>
                    <span>Cancel</span>

                </a>

            </div>


            <div class="requisition-bottom-actions-right">

                <button type="submit"
                        class="requisition-add-button">

                    <i class="fas fa-save"></i>
                    <span>Save Supplier</span>

                </button>

            </div>

        </div>

    </form>

</div>

@endsection