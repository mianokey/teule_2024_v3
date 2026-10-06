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
                    <span>View</span>
                </div>

                <h1 class="requisition-page-title">
                    {{ $storeCategory->name }}
                </h1>

                <p class="requisition-page-subtitle">
                    Store item category details and usage information.
                </p>

            </div>

        </div>

        <div class="requisition-header-right">

            <a href="{{ route('admin.store-categories.edit', $storeCategory) }}"
               class="requisition-list-action warning">

                <i class="fa fa-pencil"></i>
                Edit

            </a>

        </div>

    </div>


    {{-- ============================================================
         MESSAGES
         ============================================================ --}}

    <x-message></x-message>


    {{-- ============================================================
         CATEGORY DETAILS
         ============================================================ --}}

    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fa fa-info"></i>
                </div>

                <div>

                    <h5>Category Details</h5>

                    <p>
                        Basic information about this store item category.
                    </p>

                </div>

            </div>

        </div>


        <div class="requisition-details-body">

            <div class="row">

                {{-- CATEGORY --}}

                <div class="col-lg-3 col-md-6 mb-3">

                    <div class="d-flex align-items-center">

                        <label class="requisition-field-label mb-0 mr-2">
                            Category:
                        </label>

                        <div class="requisition-input flex-grow-1">
                            {{ $storeCategory->name }}
                        </div>

                    </div>

                </div>


                {{-- ITEMS --}}

                <div class="col-lg-3 col-md-6 mb-3">

                    <div class="d-flex align-items-center">

                        <label class="requisition-field-label mb-0 mr-2">
                            Items:
                        </label>

                        <div class="requisition-input flex-grow-1">
                            {{ $storeCategory->items_count }}
                        </div>

                    </div>

                </div>


                {{-- STATUS --}}

                <div class="col-lg-3 col-md-6 mb-3">

                    <div class="d-flex align-items-center">

                        <label class="requisition-field-label mb-0 mr-2">
                            Status:
                        </label>

                        <div class="requisition-input flex-grow-1">
                            {{ $storeCategory->is_active ? 'Active' : 'Inactive' }}
                        </div>

                    </div>

                </div>

            </div>


            {{-- DESCRIPTION --}}

            <div class="mt-2">

                <label class="requisition-field-label">
                    Description
                </label>

                <div class="requisition-input">
                    {{ $storeCategory->description ?: 'No description provided.' }}
                </div>

            </div>

        </div>

    </div>


    {{-- ============================================================
         ITEMS USING THIS CATEGORY
         ============================================================ --}}

    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fa fa-cubes"></i>
                </div>

                <div>

                    <h5>Items Using This Category</h5>

                    <p>
                        Number of store items assigned to this category.
                    </p>

                </div>

            </div>

        </div>


        <div class="requisition-details-body">

            <div class="requisition-input">

                {{ $storeCategory->items_count }}

                {{ $storeCategory->items_count == 1 ? 'item' : 'items' }}

            </div>

        </div>

    </div>


    {{-- ============================================================
         BOTTOM ACTIONS
         ============================================================ --}}

    <div class="requisition-bottom-actions">

        <a href="{{ route('admin.store-categories.index') }}"
           class="requisition-cancel-button">

            <i class="fa fa-arrow-left"></i>
            Back to Categories

        </a>


        <form method="POST"
              action="{{ route('admin.store-categories.toggle-status', $storeCategory) }}">

            @csrf
            @method('PATCH')

            @if($storeCategory->is_active)

                <button type="submit"
                        class="requisition-list-action danger"
                        onclick="return confirm('Deactivate this category?')">

                    <i class="fa fa-ban"></i>
                    Deactivate

                </button>

            @else

                <button type="submit"
                        class="requisition-list-action success"
                        onclick="return confirm('Activate this category?')">

                    <i class="fa fa-check"></i>
                    Activate

                </button>

            @endif

        </form>

    </div>

</div>

@endsection