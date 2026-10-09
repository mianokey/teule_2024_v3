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
                <i class="fa fa-cube"></i>
            </div>

            <div>

                <div class="requisition-breadcrumb">

                    <span>Stores</span>

                    <span>/</span>

                    <span>Store Items</span>

                    <span>/</span>

                    <span>View</span>

                </div>

                <h1 class="requisition-page-title">

                    {{ $storeItem->name }}

                </h1>

                <p class="requisition-page-subtitle">

                    Store item details and stock information.

                </p>

            </div>

        </div>

        <div class="requisition-header-right">

            <a
                href="{{ route('admin.store-items.edit', $storeItem) }}"
                class="requisition-add-button warning"
            >

                <i class="fa fa-edit"></i>

                Edit

            </a>

        </div>

    </div>


    <x-message></x-message>


    {{-- ============================================================
         ITEM DETAILS
         ============================================================ --}}

    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">

                    <i class="fa fa-info"></i>

                </div>

                <div>

                    <h5>Item Details</h5>

                    <p>
                        Basic information about this item.
                    </p>

                </div>

            </div>

        </div>


        <div class="requisition-details-body">

            <div class="row">

                {{-- ITEM --}}

                <div class="col-lg-3 col-md-6 mb-3">

                    <div class="d-flex align-items-center">

                        <label class="requisition-field-label mb-0 mr-2">

                            Item:

                        </label>

                        <div class="requisition-input flex-grow-1">

                            {{ $storeItem->name }}

                        </div>

                    </div>

                </div>


                {{-- CATEGORY --}}

                <div class="col-lg-3 col-md-6 mb-3">

                    <div class="d-flex align-items-center">

                        <label class="requisition-field-label mb-0 mr-2">

                            Category:

                        </label>

                        <div class="requisition-input flex-grow-1">

                            {{ $storeItem->category->name ?? '—' }}

                        </div>

                    </div>

                </div>


                {{-- UNIT --}}

                <div class="col-lg-3 col-md-6 mb-3">

                    <div class="d-flex align-items-center">

                        <label class="requisition-field-label mb-0 mr-2">

                            Unit:

                        </label>

                        <div class="requisition-input flex-grow-1">

                            {{ $storeItem->unit->name ?? '—' }}

                            @if($storeItem->unit)

                                ({{ $storeItem->unit->code }})

                            @endif

                        </div>

                    </div>

                </div>


                {{-- SKU --}}

                <div class="col-lg-3 col-md-6 mb-3">

                    <div class="d-flex align-items-center">

                        <label class="requisition-field-label mb-0 mr-2">

                            SKU:

                        </label>

                        <div class="requisition-input flex-grow-1">

                            {{ $storeItem->sku ?: '—' }}

                        </div>

                    </div>

                </div>


                {{-- ITEM TYPE --}}

                <div class="col-lg-3 col-md-6 mb-3">

                    <div class="d-flex align-items-center">

                        <label class="requisition-field-label mb-0 mr-2">

                            Type:

                        </label>

                        <div class="requisition-input flex-grow-1">

                            {{ ucfirst(strtolower($storeItem->item_type)) }}

                        </div>

                    </div>

                </div>


                {{-- REORDER LEVEL --}}

                <div class="col-lg-3 col-md-6 mb-3">

                    <div class="d-flex align-items-center">

                        <label class="requisition-field-label mb-0 mr-2">

                            Reorder:

                        </label>

                        <div class="requisition-input flex-grow-1">

                            {{ number_format((float) $storeItem->reorder_level, 3) }}

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

                            {{ $storeItem->is_active ? 'Active' : 'Inactive' }}

                        </div>

                    </div>

                </div>

            </div>


            {{-- DESCRIPTION --}}

            @if($storeItem->description)

                <div class="mt-2">

                    <label class="requisition-field-label">

                        Description

                    </label>

                    <div class="requisition-input">

                        {{ $storeItem->description }}

                    </div>

                </div>

            @endif

        </div>

    </div>


    {{-- ============================================================
         CURRENT STOCK
         ============================================================ --}}

    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">

                    <i class="fa fa-database"></i>

                </div>

                <div>

                    <h5>Current Stock</h5>

                    <p>
                        Stock balance by store.
                    </p>

                </div>

            </div>

        </div>


        <div class="requisition-details-body">

            @if($storeItem->stocks->count())

                <div class="requisition-list-table-wrapper">

                    <table class="requisition-list-table">

                        <thead>

                            <tr>

                                <th>Store</th>

                                <th>Code</th>

                                <th>Quantity</th>

                                <th>Last Movement</th>

                                @can('ADJUST STORE STOCK')

                                    <th>Actions</th>

                                @endcan

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($storeItem->stocks as $stock)

                                <tr>

                                    {{-- STORE --}}

                                    <td>

                                        {{ $stock->store->name ?? '—' }}

                                    </td>


                                    {{-- CODE --}}

                                    <td>

                                        {{ $stock->store->code ?? '—' }}

                                    </td>


                                    {{-- QUANTITY --}}

                                    <td>

                                        <strong>

                                            {{ number_format(
                                                (float) $stock->quantity,
                                                3
                                            ) }}

                                        </strong>

                                    </td>


                                    {{-- LAST MOVEMENT --}}

                                    <td>

                                        {{ $stock->last_movement_at
                                            ? $stock->last_movement_at->format('d M Y H\:i')
                                            : '—'
                                        }}

                                    </td>


                                    {{-- STOCK ADJUSTMENT --}}

                                    @can('ADJUST STORE STOCK')

                                        <td>

                                            <div class="requisition-list-actions">

                                               <a
    href="{{ route('admin.store-stock-adjustments.create') }}"
    class="requisition-add-button warning"
>
    <i class="fa fa-adjust"></i>
    Adjust Stock
</a>
                                                   

                                            </div>

                                        </td>

                                    @endcan

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="requisition-table-empty">

                    <div class="requisition-empty-icon">

                        <i class="fa fa-database"></i>

                    </div>

                    <p>

                        No stock has been received for this item yet.

                    </p>

                </div>

            @endif

        </div>

    </div>


    {{-- ============================================================
         ITEM VARIANTS
         ============================================================ --}}

    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">

                    <i class="fa fa-code-fork"></i>

                </div>

                <div>

                    <h5>Item Variants</h5>

                    <p>
                        Optional variations of this item.
                    </p>

                </div>

            </div>


            <div class="requisition-header-right">

                <a
                    href="{{ route(
                        'admin.store-item-variants.create',
                        $storeItem
                    ) }}"
                    class="requisition-add-button"
                >

                    <i class="fa fa-plus"></i>

                    Add Variant

                </a>

            </div>

        </div>


        <div class="requisition-details-body">

            @if($storeItem->variants->count())

                <div class="requisition-list-table-wrapper">

                    <table class="requisition-list-table">

                        <thead>

                            <tr>

                                <th>#</th>

                                <th>Variant</th>

                                <th>Code</th>

                                <th>Status</th>

                                <th>Actions</th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach($storeItem->variants as $variant)

                                <tr>

                                    {{-- NUMBER --}}

                                    <td>

                                        {{ $loop->iteration }}

                                    </td>


                                    {{-- VARIANT --}}

                                    <td>

                                        <strong>

                                            {{ $variant->name }}

                                        </strong>

                                        @if($variant->description)

                                            <div class="requisition-field-help">

                                                {{ Str::limit(
                                                    $variant->description,
                                                    60
                                                ) }}

                                            </div>

                                        @endif

                                    </td>


                                    {{-- CODE --}}

                                    <td>

                                        {{ $variant->code ?: '—' }}

                                    </td>


                                    {{-- STATUS --}}

                                    <td>

                                        {{ $variant->is_active
                                            ? 'Active'
                                            : 'Inactive'
                                        }}

                                    </td>


                                    {{-- ACTIONS --}}

                                    <td>

                                        <div class="requisition-list-actions">

                                            <a
                                                href="{{ route(
                                                    'admin.store-item-variants.show',
                                                    [
                                                        $storeItem,
                                                        $variant
                                                    ]
                                                ) }}"
                                                class="requisition-add-button primary"
                                            >

                                                <i class="fa fa-eye"></i>

                                                View

                                            </a>


                                            <a
                                                href="{{ route(
                                                    'admin.store-item-variants.edit',
                                                    [
                                                        $storeItem,
                                                        $variant
                                                    ]
                                                ) }}"
                                                class="requisition-add-button warning"
                                            >

                                                <i class="fa fa-pencil"></i>

                                                Edit

                                            </a>

                                        </div>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="requisition-table-empty">

                    <div class="requisition-empty-icon">

                        <i class="fa fa-code-fork"></i>

                    </div>

                    <p>

                        No variants have been added.

                    </p>

                    <a
                        href="{{ route(
                            'admin.store-item-variants.create',
                            $storeItem
                        ) }}"
                        class="requisition-add-button"
                    >

                        <i class="fa fa-plus"></i>

                        Add Variant

                    </a>

                </div>

            @endif

        </div>

    </div>


    {{-- ============================================================
         BOTTOM ACTIONS
         ============================================================ --}}

    <div class="requisition-bottom-actions">

        <a
            href="{{ route('admin.store-items.index') }}"
            class="requisition-cancel-button"
        >

            <i class="fa fa-arrow-left"></i>

            Back to Store Items

        </a>


        <form
            method="POST"
            action="{{ route(
                'admin.store-items.toggle-status',
                $storeItem
            ) }}"
        >

            @csrf

            @method('PATCH')

            @if($storeItem->is_active)

                <button
                    type="submit"
                    class="requisition-add-button danger"
                >

                    <i class="fa fa-ban"></i>

                    Deactivate

                </button>

            @else

                <button
                    type="submit"
                    class="requisition-list-action success"
                >

                    <i class="fa fa-check"></i>

                    Activate

                </button>

            @endif

        </form>

    </div>

</div>

@endsection