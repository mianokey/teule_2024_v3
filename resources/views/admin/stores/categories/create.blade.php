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
                <i class="fa fa-folder-plus"></i>
            </div>

            <div>
                <div class="requisition-breadcrumb">
                    <span>Stores</span>
                    <span>/</span>
                    <span>Categories</span>
                    <span>/</span>
                    <span>Add</span>
                </div>

                <h1 class="requisition-page-title">
                    Add Store Item Category
                </h1>

                <p class="requisition-page-subtitle">
                    Create a category for organizing store items.
                </p>
            </div>

        </div>

        <div class="requisition-header-right">

            <a
                href="{{ route('admin.store-categories.index') }}"
                class="requisition-cancel-button"
            >
                <i class="fa fa-arrow-left"></i>
                Back to Categories
            </a>

        </div>

    </div>


    {{-- ============================================================
         VALIDATION ERRORS
         ============================================================ --}}
    @if($errors->any())

        <div class="alert alert-danger">

            <strong>Please correct the following:</strong>

            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>

    @endif


    {{-- ============================================================
         CATEGORY DETAILS
         ============================================================ --}}
    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fa fa-folder"></i>
                </div>

                <div>
                    <h5>Category Details</h5>

                    <p>
                        Enter the basic information for this store item category.
                    </p>
                </div>

            </div>

        </div>


        <div class="requisition-details-body">

            <form
                action="{{ route('admin.store-categories.store') }}"
                method="POST"
            >

                @csrf


                {{-- CATEGORY NAME --}}
                <div class="requisition-form-group">

                    <label
                        for="name"
                        class="requisition-field-label"
                    >
                        Category Name
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        name="name"
                        id="name"
                        class="requisition-input"
                        value="{{ old('name') }}"
                        maxlength="255"
                        required
                        autofocus
                        placeholder="Enter category name"
                    >

                    @error('name')
                        <div class="text-danger mt-1">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- DESCRIPTION --}}
                <div class="requisition-form-group mt-3">

                    <label
                        for="description"
                        class="requisition-field-label"
                    >
                        Description
                    </label>

                    <textarea
                        name="description"
                        id="description"
                        class="requisition-input"
                        rows="5"
                        maxlength="2000"
                        placeholder="Enter a description for this category (optional)"
                    >{{ old('description') }}</textarea>

                    @error('description')
                        <div class="text-danger mt-1">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- ====================================================
                     FORM ACTIONS
                     ==================================================== --}}
                <div class="requisition-bottom-actions">

                    <a
                        href="{{ route('admin.store-categories.index') }}"
                        class="requisition-cancel-button"
                    >
                        <i class="fa fa-times"></i>
                        Cancel
                    </a>

                    <button
                        type="submit"
                        class="requisition-save-button"
                    >
                        <i class="fa fa-save"></i>
                        Save Category
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection