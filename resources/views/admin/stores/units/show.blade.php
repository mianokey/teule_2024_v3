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
                    <span>View</span>
                </div>

                <h1 class="requisition-page-title">
                    {{ $storeUnit->name }}
                </h1>

                <p class="requisition-page-subtitle">
                    Store unit details and usage information.
                </p>

            </div>

        </div>

        <div class="requisition-header-right">

            <a href="{{ route('admin.store-units.edit', $storeUnit) }}"
               class="requisition-add-button warning">

                <i class="fa fa-edit"></i>
                Edit

            </a>

        </div>

    </div>


    {{-- ============================================================
         MESSAGES
         ============================================================ --}}

    <x-message></x-message>


    {{-- ============================================================
         UNIT DETAILS
         ============================================================ --}}

    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fa fa-info"></i>
                </div>

                <div>

                    <h5>Unit Details</h5>

                    <p>
                        Basic information about this store unit.
                    </p>

                </div>

            </div>

        </div>


        <div class="requisition-details-body">

            <div class="row">

                {{-- UNIT NAME --}}

                <div class="col-lg-3 col-md-6 mb-3">

                    <div class="d-flex align-items-center">

                        <label class="requisition-field-label mb-0 mr-2">
                            Unit:
                        </label>

                        <div class="requisition-input flex-grow-1">
                            {{ $storeUnit->name }}
                        </div>

                    </div>

                </div>


                {{-- CODE --}}

                <div class="col-lg-3 col-md-6 mb-3">

                    <div class="d-flex align-items-center">

                        <label class="requisition-field-label mb-0 mr-2">
                            Code:
                        </label>

                        <div class="requisition-input flex-grow-1">
                            {{ $storeUnit->code }}
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
                            {{ $storeUnit->items_count }}
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
                            {{ $storeUnit->is_active ? 'Active' : 'Inactive' }}
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
                    {{ $storeUnit->description ?: 'No description provided.' }}
                </div>

            </div>

        </div>

    </div>


    {{-- ============================================================
         ITEMS USING THIS UNIT
         ============================================================ --}}

    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fa fa-cubes"></i>
                </div>

                <div>

                    <h5>Items Using This Unit</h5>

                    <p>
                        Number of store items currently using this unit.
                    </p>

                </div>

            </div>

        </div>


        <div class="requisition-details-body">

            <div class="requisition-input">

                {{ $storeUnit->items_count }}

                {{ $storeUnit->items_count == 1 ? 'item' : 'items' }}

            </div>

        </div>

    </div>


    {{-- ============================================================
         BOTTOM ACTIONS
         ============================================================ --}}

    <div class="requisition-bottom-actions">

        <a href="{{ route('admin.store-units.index') }}"
           class="requisition-cancel-button">

            <i class="fa fa-arrow-left"></i>
            Back to Store Units

        </a>


        <form method="POST"
              action="{{ route('admin.store-units.toggle-status', $storeUnit) }}">

            @csrf
            @method('PATCH')

            @if($storeUnit->is_active)

                <button type="submit"
                        class="requisition-add-button danger"
                        onclick="return confirm('Deactivate this unit?')">

                    <i class="fa fa-ban"></i>
                    Deactivate

                </button>

            @else

                <button type="submit"
                        class="requisition-add-button success"
                        onclick="return confirm('Activate this unit?')">

                    <i class="fa fa-check"></i>
                    Activate

                </button>

            @endif

        </form>

    </div>

</div>

@endsection