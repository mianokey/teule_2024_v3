
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
                <i class="fas fa-truck-loading"></i>
            </div>

            <div>

                <div class="requisition-breadcrumb">
                    Stores
                    <span>/</span>
                    Receiving
                    <span>/</span>
                    New Receipt
                </div>

                <h1 class="requisition-page-title">
                    New Store Receipt
                </h1>

                <p class="requisition-page-subtitle">
                    Start a new goods receiving transaction.
                </p>

            </div>

        </div>

        <div class="requisition-header-right">

            <a href="{{ route('admin.store-receipts.index') }}"
               class="requisition-add-button">

                <i class="fas fa-arrow-left me-1"></i>
                Back

            </a>

        </div>

    </div>


    {{-- =========================================================
    MESSAGES
    ========================================================= --}}
    <x-message></x-message>


    @if($errors->any())

        <div class="requisition-notice requisition-notice-danger mb-3">

            <div>

                <strong>
                    Please correct the following:
                </strong>

                <ul class="mb-0 mt-2">

                    @foreach($errors->all() as $error)

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        </div>

    @endif


    {{-- =========================================================
    CREATE FORM
    ========================================================= --}}
    <form method="POST"
          action="{{ route('admin.store-receipts.store') }}">

        @csrf


        {{-- =====================================================
        RECEIPT INFORMATION
        ===================================================== --}}
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
                            Start the receiving transaction before adding
                            supplier, donation or item details.
                        </p>

                    </div>

                </div>

            </div>


            <div class="p-3">

                <div class="row g-3">

                    {{-- =================================================
                    RECEIPT NUMBER
                    ================================================= --}}
                    <div class="col-md-4">

                        <label class="requisition-field-label">
                            Receipt Number
                        </label>

                        <input type="text"
                               class="form-control requisition-input"
                               value="{{ $receiptNumber ?? 'Will be generated automatically' }}"
                               readonly>

                    </div>


                    {{-- =================================================
                    RECEIVING STORE
                    ================================================= --}}
                    <div class="col-md-4">

                        <label class="requisition-field-label">
                            Receiving Store <span class="text-danger">*</span>
                        </label>

                        <select name="store_id"
                                id="store_id"
                                class="form-select requisition-input"
                                required>

                            <option value="">
                                Select Store
                            </option>

                            @foreach($stores as $store)

                                <option value="{{ $store->id }}"
                                    {{ old('store_id') == $store->id ? 'selected' : '' }}>

                                    {{ $store->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- =================================================
                    RECEIVED DATE
                    ================================================= --}}
                    <div class="col-md-4">

                        <label class="requisition-field-label">
                            Received Date <span class="text-danger">*</span>
                        </label>

                        <input type="date"
                               name="received_date"
                               id="received_date"
                               class="form-control requisition-input"
                               value="{{ old('received_date', now()->format('Y-m-d')) }}"
                               required>

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
        INFORMATION NOTICE
        ===================================================== --}}
        <div class="requisition-notice requisition-notice-info mt-3">

            <i class="fas fa-info-circle me-2"></i>

            <div>

                <strong>Next step</strong>

                <div class="mt-1">
                    After creating this receipt, you will be taken to the
                    receiving screen where you can select the source,
                    supplier or donation, add the received items and
                    complete the receipt.
                </div>

            </div>

        </div>


        {{-- =====================================================
        BOTTOM ACTIONS
        ===================================================== --}}
        <div class="requisition-bottom-actions mt-3">

            <div class="requisition-bottom-actions-left">
            </div>

            <div class="requisition-bottom-actions-right">

                <a href="{{ route('admin.store-receipts.index') }}"
                   class="requisition-cancel-button">

                    <i class="fas fa-times"></i>
                    Cancel

                </a>

                <button type="submit"
                        class="requisition-add-button">

                    <i class="fas fa-arrow-right me-1"></i>
                    Create & Continue

                </button>

            </div>

        </div>

    </form>

</div>

@endsection