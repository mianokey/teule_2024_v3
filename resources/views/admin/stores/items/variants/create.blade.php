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
                    <span>Add</span>

                </div>

                <h1 class="requisition-page-title">
                    Add Item Variant
                </h1>

                <p class="requisition-page-subtitle">
                    Add a variant for {{ $storeItem->name }}.
                </p>

            </div>

        </div>

        <div class="requisition-header-right">

            <a href="{{ route('admin.store-item-variants.index', $storeItem) }}"
               class="requisition-list-action primary">

                <i class="fa fa-list"></i>
                Variants

            </a>

        </div>

    </div>


    {{-- ============================================================
         MESSAGES
         ============================================================ --}}

    <x-message></x-message>


    {{-- ============================================================
         VARIANT FORM
         ============================================================ --}}

    <form method="POST"
          action="{{ route('admin.store-item-variants.store', $storeItem) }}">

        @csrf


        <div class="requisition-section">

            <div class="requisition-section-header">

                <div class="requisition-section-heading">

                    <div class="requisition-section-icon">
                        <i class="fa fa-code-fork"></i>
                    </div>

                    <div>

                        <h5>Variant Details</h5>

                        <p>
                            Enter the details for this item variant.
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
                                   value="{{ old('name') }}"
                                   class="requisition-input @error('name') is-invalid @enderror"
                                   placeholder="e.g. Rosecoco"
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
                                   value="{{ old('code') }}"
                                   class="requisition-input @error('code') is-invalid @enderror"
                                   placeholder="e.g. ROSECOCO">

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
                                      class="requisition-input @error('description') is-invalid @enderror"
                                      placeholder="Optional description">{{ old('description') }}</textarea>

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
                Save Variant

            </button>

        </div>

    </form>

</div>

@endsection