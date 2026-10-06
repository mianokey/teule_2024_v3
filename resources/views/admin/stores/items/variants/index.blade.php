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

                </div>

                <h1 class="requisition-page-title">
                    {{ $storeItem->name }} — Variants
                </h1>

                <p class="requisition-page-subtitle">
                    Manage variations of this store item.
                </p>

            </div>

        </div>

        <div class="requisition-header-right">

            <a href="{{ route('admin.store-items.show', $storeItem) }}"
               class="requisition-list-action primary">

                <i class="fa fa-arrow-left"></i>
                Back to Item

            </a>

            <a href="{{ route('admin.store-item-variants.create', $storeItem) }}"
               class="requisition-add-button">

                <i class="fa fa-plus"></i>
                Add Variant

            </a>

        </div>

    </div>


    {{-- ============================================================
         MESSAGES
         ============================================================ --}}

    <x-message></x-message>


    {{-- ============================================================
         VARIANTS
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
                        Variations available for {{ $storeItem->name }}.
                    </p>

                </div>

            </div>

            @if($variants->count())

                <div class="requisition-count-badge">
                    {{ $variants->count() }}
                </div>

            @endif

        </div>


        <div class="requisition-details-body">

            @if($variants->count())

                <div class="requisition-list-table-wrapper">

                    <table class="requisition-list-table">

                        <thead>

                            <tr>
                                <th>#</th>
                                <th>Variant</th>
                                <th>Code</th>
                                <th>Stock Records</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>

                        </thead>

                        <tbody>

                            @foreach($variants as $variant)

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

                                                {{ Str::limit($variant->description, 70) }}

                                            </div>

                                        @endif

                                    </td>


                                    {{-- CODE --}}

                                    <td>
                                        {{ $variant->code ?: '—' }}
                                    </td>


                                    {{-- STOCK RECORDS --}}

                                    <td>
                                        {{ $variant->stocks_count }}
                                    </td>


                                    {{-- STATUS --}}

                                    <td>

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

                                    </td>


                                    {{-- ACTIONS --}}

                                    <td>

                                        <div class="requisition-list-actions">

                                            {{-- VIEW --}}

                                            <a href="{{ route('admin.store-item-variants.show', [$storeItem, $variant]) }}"
                                               class="requisition-list-action primary">

                                                <i class="fa fa-eye"></i>
                                                View

                                            </a>


                                            {{-- EDIT --}}

                                            <a href="{{ route('admin.store-item-variants.edit', [$storeItem, $variant]) }}"
                                               class="requisition-list-action warning">

                                                <i class="fa fa-pencil"></i>
                                                Edit

                                            </a>


                                            {{-- ENABLE / DISABLE --}}

                                            <form method="POST"
                                                  action="{{ route('admin.store-item-variants.toggle-status', [$storeItem, $variant]) }}"
                                                  style="display:inline;">

                                                @csrf
                                                @method('PATCH')

                                                @if($variant->is_active)

                                                    <button type="submit"
                                                            class="requisition-list-action danger"
                                                            onclick="return confirm('Disable this variant?')">

                                                        <i class="fa fa-ban"></i>
                                                        Disable

                                                    </button>

                                                @else

                                                    <button type="submit"
                                                            class="requisition-list-action success"
                                                            onclick="return confirm('Enable this variant?')">

                                                        <i class="fa fa-check"></i>
                                                        Enable

                                                    </button>

                                                @endif

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                {{-- ====================================================
                     EMPTY STATE
                     ==================================================== --}}

                <div class="requisition-table-empty">

                    <div class="requisition-empty-icon">
                        <i class="fa fa-code-fork"></i>
                    </div>

                    <h5>
                        No Variants Yet
                    </h5>

                    <p>
                        This item does not have any variants.
                    </p>

                    <a href="{{ route('admin.store-item-variants.create', $storeItem) }}"
                       class="requisition-add-button">

                        <i class="fa fa-plus"></i>
                        Add First Variant

                    </a>

                </div>

            @endif

        </div>

    </div>


    {{-- ============================================================
         BOTTOM ACTION
         ============================================================ --}}

    <div class="requisition-bottom-actions">

        <a href="{{ route('admin.store-items.show', $storeItem) }}"
           class="requisition-cancel-button">

            <i class="fa fa-arrow-left"></i>
            Back to Store Item

        </a>

    </div>

</div>

@endsection