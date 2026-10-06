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
                <i class="fa fa-code-fork"></i>
            </div>

            <div>

                <div class="requisition-breadcrumb">

                    <span>Stores</span>
                    <span>/</span>
                    <span>Store Items</span>
                    <span>/</span>
                    <span>{{ $storeItem->name }}</span>
                    <span>/</span>
                    <span>Variants</span>
                    <span>/</span>
                    <span>Edit</span>

                </div>

                <h1 class="requisition-page-title">
                    Edit Item Variant
                </h1>

                <p class="requisition-page-subtitle">
                    Update the {{ $variant->name }} variant for {{ $storeItem->name }}.
                </p>

            </div>

        </div>

        <div class="requisition-header-right">

            <a href="{{ route('admin.store-item-variants.show', [$storeItem, $variant]) }}"
               class="requisition-list-action primary">

                <i class="fa fa-eye"></i>
                View

            </a>

        </div>

    </div>


    {{-- ============================================================
         MESSAGES
         ============================================================ --}}

    <x-message></x-message>


    {{-- ============================================================
         EDIT VARIANT FORM
         ============================================================ --}}

    <form method="POST"
          action="{{ route('admin.store-item-variants.update', [$storeItem, $variant]) }}">

        @csrf
        @method('PUT')


        <div class="requisition-section">

            <div class="requisition-section-header">

                <div class="requisition-section-heading">

                    <div class="requisition-section-icon">
                        <i class="fa fa-pencil"></i>
                    </div>

                    <div>

                        <h5>Variant Details</h5>

                        <p>
                            Update the details for this item variant.
                        </p>

                    </div>

                </div>

            </div>


            <div class="requisition-details-body">

                <div class="row">

                    {{-- VARIANT NAME --}}

                    <div class="col-lg-8 col-md-7 mb-3">

                        <div class="requisition-form-group">

                            <label for="name"
                                   class="requisition-field-label">

                                Variant Name
                                <span class="text-danger">*</span>

                            </label>

                            <input type="text"
                                   name="name"
                                   id="name"
                                   value="{{ old('name', $variant->name) }}"
                                   class="requisition-input @error('name') is-invalid @enderror"
                                   required>

                            @error('name')

                                <div class="requisition-field-help text-danger">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>

                    </div>


                    {{-- VARIANT CODE --}}

                    <div class="col-lg-4 col-md-5 mb-3">

                        <div class="requisition-form-group">

                            <label for="code"
                                   class="requisition-field-label">

                                Variant Code

                            </label>

                            <input type="text"
                                   name="code"
                                   id="code"
                                   value="{{ old('code', $variant->code) }}"
                                   class="requisition-input @error('code') is-invalid @enderror">

                            <div class="requisition-field-help">
                                Optional internal code.
                            </div>

                            @error('code')

                                <div class="requisition-field-help text-danger">
                                    {{ $message }}
                                </div>

                            @enderror

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
                                      rows="4"
                                      class="requisition-input @error('description') is-invalid @enderror">{{ old('description', $variant->description) }}</textarea>

                            @error('description')

                                <div class="requisition-field-help text-danger">
                                    {{ $message }}
                                </div>

                            @enderror

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================
             FORM ACTIONS
             ======================================================== --}}

        <div class="requisition-bottom-actions">

            <a href="{{ route('admin.store-item-variants.index', $storeItem) }}"
               class="requisition-cancel-button">

                <i class="fa fa-arrow-left"></i>
                Cancel

            </a>

            <button type="submit"
                    class="requisition-save-button">

                <i class="fa fa-save"></i>
                Update Variant

            </button>

        </div>

    </form>

</div>

@endsection