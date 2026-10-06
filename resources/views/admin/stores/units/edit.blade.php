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
                    <span>Edit</span>
                </div>

                <h1 class="requisition-page-title">
                    Edit Store Unit
                </h1>

                <p class="requisition-page-subtitle">
                    Update the details of {{ $storeUnit->name }}.
                </p>

            </div>

        </div>

        <div class="requisition-header-right">

            <a href="{{ route('admin.store-units.show', $storeUnit) }}"
               class="requisition-add-button  primary">

                <i class="fa fa-eye"></i>
                View

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
         EDIT FORM
         ============================================================ --}}

    <form action="{{ route('admin.store-units.update', $storeUnit) }}"
          method="POST">

        @csrf
        @method('PUT')


        <div class="requisition-section">

            <div class="requisition-section-header">

                <div class="requisition-section-heading">

                    <div class="requisition-section-icon">
                        <i class="fa fa-pencil"></i>
                    </div>

                    <div>

                        <h5>Unit Details</h5>

                        <p>
                            Update the store unit information below.
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
                                   value="{{ old('name', $storeUnit->name) }}"
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
                                   value="{{ old('code', $storeUnit->code) }}"
                                   required
                                   maxlength="20">

                            <div class="requisition-field-help">
                                The code must be unique.
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
                                      maxlength="2000">{{ old('description', $storeUnit->description) }}</textarea>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================
             FORM ACTIONS
             ======================================================== --}}

        <div class="requisition-bottom-actions">

            <a href="{{ route('admin.store-units.show', $storeUnit) }}"
               class="requisition-cancel-button">

                <i class="fa fa-arrow-left"></i>
                Cancel

            </a>

            <button type="submit"
                    class="requisition-save-button">

                <i class="fa fa-save"></i>
                Update Unit

            </button>

        </div>

    </form>

</div>

@endsection