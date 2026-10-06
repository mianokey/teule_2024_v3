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
                <i class="fa fa-balance-scale"></i>
            </div>

            <div>

                <div class="requisition-breadcrumb">
                    <span>Stores</span>
                    <span>/</span>
                    <span>Store Units</span>
                    <span>/</span>
                    <span>Add</span>
                </div>

                <h1 class="requisition-page-title">
                    Add Store Unit
                </h1>

                <p class="requisition-page-subtitle">
                    Create a new measurement unit for store items.
                </p>

            </div>

        </div>

        <div class="requisition-header-right">

            <a href="{{ route('admin.store-units.index') }}"
               class="requisition-list-action primary">

                <i class="fa fa-list"></i>
                Store Units

            </a>

        </div>

    </div>


    {{-- ============================================================
         VALIDATION ERRORS
         ============================================================ --}}

    @if($errors->any())

        <div class="requisition-section">

            <div class="requisition-details-body">

                <div class="alert alert-danger">

                    <strong>Please correct the following:</strong>

                    <ul class="mb-0 mt-2">

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


    {{-- ============================================================
         STORE UNIT FORM
         ============================================================ --}}

    <form action="{{ route('admin.store-units.store') }}"
          method="POST">

        @csrf


        <div class="requisition-section">

            <div class="requisition-section-header">

                <div class="requisition-section-heading">

                    <div class="requisition-section-icon">
                        <i class="fa fa-balance-scale"></i>
                    </div>

                    <div>

                        <h5>Unit Details</h5>

                        <p>
                            Enter the measurement unit information below.
                        </p>

                    </div>

                </div>

            </div>


            <div class="requisition-details-body">

                <div class="row">

                    {{-- UNIT NAME --}}

                    <div class="col-lg-6 col-md-6 mb-3">

                        <div class="requisition-form-group">

                            <label for="name"
                                   class="requisition-field-label">

                                Unit Name
                                <span class="text-danger">*</span>

                            </label>

                            <input type="text"
                                   name="name"
                                   id="name"
                                   class="requisition-input"
                                   value="{{ old('name') }}"
                                   placeholder="e.g. Piece"
                                   required
                                   maxlength="255">

                        </div>

                    </div>


                    {{-- UNIT CODE --}}

                    <div class="col-lg-6 col-md-6 mb-3">

                        <div class="requisition-form-group">

                            <label for="code"
                                   class="requisition-field-label">

                                Unit Code
                                <span class="text-danger">*</span>

                            </label>

                            <input type="text"
                                   name="code"
                                   id="code"
                                   class="requisition-input"
                                   value="{{ old('code') }}"
                                   placeholder="e.g. PCS"
                                   required
                                   maxlength="20">

                            <div class="requisition-field-help">
                                Use a short unique code such as PCS, KG, LTR or BOX.
                            </div>

                        </div>

                    </div>


                    {{-- DESCRIPTION --}}

                    <div class="col-12 mb-3">

                        <div class="requisition-form-group">

                            <label for="description"
                                   class="requisition-field-label">

                                Description

                            </label>

                            <textarea name="description"
                                      id="description"
                                      class="requisition-input"
                                      rows="4"
                                      maxlength="2000"
                                      placeholder="Optional description of this unit...">{{ old('description') }}</textarea>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================
             FORM ACTIONS
             ======================================================== --}}

        <div class="requisition-bottom-actions">

            <a href="{{ route('admin.store-units.index') }}"
               class="requisition-cancel-button">

                <i class="fa fa-arrow-left"></i>
                Cancel

            </a>

            <button type="submit"
                    class="requisition-save-button">

                <i class="fa fa-save"></i>
                Save Unit

            </button>

        </div>

    </form>

</div>

@endsection