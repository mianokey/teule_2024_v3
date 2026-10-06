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
                <i class="fa fa-folder"></i>
            </div>

            <div>

                <div class="requisition-breadcrumb">
                    <span>Stores</span>
                    <span>/</span>
                    <span>Item Categories</span>
                    <span>/</span>
                    <span>Edit</span>
                </div>

                <h1 class="requisition-page-title">
                    Edit Store Item Category
                </h1>

                <p class="requisition-page-subtitle">
                    Update the details of {{ $storeCategory->name }}.
                </p>

            </div>

        </div>

        <div class="requisition-header-right">

            <a href="{{ route('admin.store-categories.show', $storeCategory) }}"
               class="requisition-list-action primary">

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

    <form action="{{ route('admin.store-categories.update', $storeCategory) }}"
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

                        <h5>Category Details</h5>

                        <p>
                            Update the store item category information below.
                        </p>

                    </div>

                </div>

            </div>


            <div class="requisition-details-body">

                <div class="row">

                    {{-- CATEGORY NAME --}}

                    <div class="col-lg-6 col-md-6 mb-3">

                        <div class="requisition-form-group">

                            <label for="name"
                                   class="requisition-field-label">

                                Category Name
                                <span class="text-danger">*</span>

                            </label>

                            <input type="text"
                                   name="name"
                                   id="name"
                                   class="requisition-input"
                                   value="{{ old('name', $storeCategory->name) }}"
                                   required
                                   maxlength="255">

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
                                      maxlength="2000">{{ old('description', $storeCategory->description) }}</textarea>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================
             FORM ACTIONS
             ======================================================== --}}

        <div class="requisition-bottom-actions">

            <a href="{{ route('admin.store-categories.show', $storeCategory) }}"
               class="requisition-cancel-button">

                <i class="fa fa-arrow-left"></i>
                Cancel

            </a>

            <button type="submit"
                    class="requisition-save-button">

                <i class="fa fa-save"></i>
                Update Category

            </button>

        </div>

    </form>

</div>

@endsection