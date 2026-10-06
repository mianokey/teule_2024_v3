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
                    <span>View</span>

                </div>

                <h1 class="requisition-page-title">
                    {{ $variant->name }}
                </h1>

                <p class="requisition-page-subtitle">
                    Variant details and stock information.
                </p>

            </div>

        </div>

        <div class="requisition-header-right">

            <a href="{{ route('admin.store-item-variants.edit', [$storeItem, $variant]) }}"
               class="requisition-list-action warning">

                <i class="fa fa-pencil"></i>
                Edit

            </a>

        </div>

    </div>


    <x-message></x-message>


    {{-- ============================================================
         VARIANT DETAILS
         ============================================================ --}}

    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fa fa-info"></i>
                </div>

                <div>

                    <h5>Variant Details</h5>

                    <p>
                        Basic information about this item variant.
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


                {{-- VARIANT --}}

                <div class="col-lg-3 col-md-6 mb-3">

                    <div class="d-flex align-items-center">

                        <label class="requisition-field-label mb-0 mr-2">
                            Variant:
                        </label>

                        <div class="requisition-input flex-grow-1">
                            {{ $variant->name }}
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
                            {{ $variant->code ?: '—' }}
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

                            @if($variant->is_active)

                                <span class="requisition-list-status requisition-list-status-approved">

                                    <span class="requisition-list-status-dot"></span>

                                    Active

                                </span>

                            @else

                                <span class="requisition-list-status requisition-list-status-cancelled">

                                    <span class="requisition-list-status-dot"></span>

                                    Inactive

                                </span>

                            @endif

                        </div>

                    </div>

                </div>

            </div>


            @if($variant->description)

                <div class="mt-2">

                    <label class="requisition-field-label">
                        Description
                    </label>

                    <div class="requisition-input">
                        {{ $variant->description }}
                    </div>

                </div>

            @endif

        </div>

    </div>


    {{-- ============================================================
         STOCK BY STORE
         ============================================================ --}}

    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fa fa-database"></i>
                </div>

                <div>

                    <h5>Stock by Store</h5>

                    <p>
                        Current stock balance for this variant.
                    </p>

                </div>

            </div>

        </div>


        <div class="requisition-details-body">

            @if($variant->stocks->count())

                <div class="requisition-list-table-wrapper">

                    <table class="requisition-list-table">

                        <thead>

                            <tr>
                                <th>Store</th>
                                <th>Code</th>
                                <th>Quantity</th>
                                <th>Last Movement</th>
                            </tr>

                        </thead>

                        <tbody>

                            @foreach($variant->stocks as $stock)

                                <tr>

                                    <td>
                                        {{ $stock->store->name ?? '—' }}
                                    </td>

                                    <td>
                                        {{ $stock->store->code ?? '—' }}
                                    </td>

                                    <td>
                                        <strong>
                                            {{ number_format((float) $stock->quantity, 3) }}
                                        </strong>
                                    </td>

                                    <td>

                                        {{ $stock->last_movement_at
                                            ? $stock->last_movement_at->format('d M Y H:i')
                                            : '—'
                                        }}

                                    </td>

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
                        No stock has been received for this variant yet.
                    </p>

                </div>

            @endif

        </div>

    </div>


    {{-- ============================================================
         BOTTOM ACTIONS
         ============================================================ --}}

    <div class="requisition-bottom-actions">

        <a href="{{ route('admin.store-item-variants.index', $storeItem) }}"
           class="requisition-cancel-button">

            <i class="fa fa-arrow-left"></i>
            Back to Variants

        </a>


        <form method="POST"
              action="{{ route('admin.store-item-variants.toggle-status', [$storeItem, $variant]) }}">

            @csrf
            @method('PATCH')

            @if($variant->is_active)

                <button type="submit"
                        class="requisition-list-action danger"
                        onclick="return confirm('Deactivate this variant?')">

                    <i class="fa fa-ban"></i>
                    Deactivate Variant

                </button>

            @else

                <button type="submit"
                        class="requisition-list-action success"
                        onclick="return confirm('Activate this variant?')">

                    <i class="fa fa-check"></i>
                    Activate Variant

                </button>

            @endif

        </form>

    </div>

</div>

@endsection