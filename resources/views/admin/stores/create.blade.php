@extends('layouts.admin')

@push('styles') <link rel="stylesheet" href="{{ asset('assets/css/stores.css') }}">
@endpush

@section('content')

<div class="store-requisition-page">


{{-- ============================================================
     PAGE HEADER
     ============================================================ --}}
<div class="requisition-page-header">

    <div class="requisition-header-content">

        <div class="requisition-header-icon">
            <i class="fas fa-warehouse"></i>
        </div>

        <div>

            <div class="requisition-breadcrumb">
                <span>Stores</span>
                <i class="fas fa-chevron-right"></i>
                <span>Store Management</span>
                <i class="fas fa-chevron-right"></i>
                <span>Add Store</span>
            </div>

            <h1 class="requisition-page-title">
                Add Store
            </h1>

            <p class="requisition-page-subtitle">
                Create a new store for managing stock and inventory.
            </p>

        </div>

    </div>

    <div class="requisition-page-actions">

        <a href="{{ route('admin.stores.index') }}"
           class="requisition-cancel-button">

            <i class="fas fa-arrow-left"></i>
            <span>Back to Stores</span>

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

                        <li>
                            {{ $error }}
                        </li>

                    @endforeach

                </ul>

            </div>

        </div>

    </div>

@endif


<form method="POST"
      action="{{ route('admin.stores.store') }}">

    @csrf


    {{-- ========================================================
         STORE INFORMATION
         ======================================================== --}}
    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fas fa-warehouse"></i>
                </div>

                <div>

                    <h5>
                        Store Information
                    </h5>

                    <p>
                        Enter the basic details for this store.
                    </p>

                </div>

            </div>

        </div>


        <div class="row">

            {{-- STORE NAME --}}
            <div class="col-md-6">

                <div class="requisition-detail-item">

                    <label for="name"
                           class="requisition-detail-label">

                        Store Name
                        <span class="text-danger">*</span>

                    </label>

                    <input type="text"
                           id="name"
                           name="name"
                           value="{{ old('name') }}"
                           class="form-control requisition-input @error('name') is-invalid @enderror"
                           placeholder="e.g. Main Store"
                           maxlength="255"
                           required>

                    @error('name')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>

            </div>


            {{-- STORE CODE --}}
            <div class="col-md-6">

                <div class="requisition-detail-item">

                    <label for="code"
                           class="requisition-detail-label">

                        Store Code
                        <span class="text-danger">*</span>

                    </label>

                    <input type="text"
                           id="code"
                           name="code"
                           value="{{ old('code') }}"
                           class="form-control requisition-input @error('code') is-invalid @enderror"
                           placeholder="e.g. MAIN"
                           maxlength="50"
                           required>

                    @error('code')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>

            </div>


            {{-- DESCRIPTION --}}
            <div class="col-md-12">

                <div class="requisition-detail-item">

                    <label for="description"
                           class="requisition-detail-label">

                        Description

                    </label>

                    <textarea id="description"
                              name="description"
                              rows="4"
                              maxlength="2000"
                              class="form-control requisition-input @error('description') is-invalid @enderror"
                              placeholder="Describe the purpose or function of this store">{{ old('description') }}</textarea>

                    @error('description')

                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>

                    @enderror

                </div>

            </div>

        </div>

    </div>


    {{-- ========================================================
         BOTTOM ACTIONS
         ======================================================== --}}
    <div class="requisition-bottom-actions">

        <div class="requisition-bottom-actions-left">

            <a href="{{ route('admin.stores.index') }}"
               class="requisition-cancel-button">

                <i class="fas fa-times"></i>
                <span>Cancel</span>

            </a>

        </div>


        <div class="requisition-bottom-actions-right">

            <button type="submit"
                    class="requisition-add-button">

                <i class="fas fa-save"></i>
                <span>Save Store</span>

            </button>

        </div>

    </div>

</form>


</div>

@endsection
