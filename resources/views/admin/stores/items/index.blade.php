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
                <i class="fa fa-cubes"></i>
            </div>

            <div>

                <div class="requisition-breadcrumb">
                    <span>Stores</span>
                    <span>/</span>
                    <span>Store Items</span>
                </div>

                <h1 class="requisition-page-title">
                    Store Items
                </h1>

                <p class="requisition-page-subtitle">
                    Manage items held in Teule Stores.
                </p>

            </div>

        </div>

        <div class="requisition-header-right">

            <a href="{{ route('admin.store-items.create') }}"
               class="requisition-add-button">

                <i class="fa fa-plus"></i>
                Add Store Item

            </a>

        </div>

    </div>


    {{-- ============================================================
         MESSAGES
         ============================================================ --}}

    <x-message></x-message>


    {{-- ============================================================
         STORE ITEMS
         ============================================================ --}}

    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fa fa-cubes"></i>
                </div>

                <div>

                    <h5>
                        Store Items
                    </h5>

                    <p>
                        Master list of items available for store management and requisitions.
                    </p>

                </div>

            </div>

            @if($items->count())

                <div class="requisition-count-badge">
                    {{ $items->count() }}
                    {{ Str::plural('item', $items->count()) }}
                </div>

            @endif

        </div>


        <div class="requisition-details-body">

            @if($items->count())

                <div class="requisition-list-table-wrapper">

                    <table class="requisition-list-table">

                        <thead>

                            <tr>

                                <th>#</th>

                                <th>Item</th>

                                <th>Category</th>

                                <th>Unit</th>

                                <th>Type</th>

                                <th>SKU</th>

                                <th>Stock</th>

                                <th>Reorder Level</th>

                                <th>Status</th>

                                <th>Actions</th>

                            </tr>

                        </thead>

                        <tbody>

                            @foreach($items as $item)

                                @php

                                    $totalStock = $item->stocks->sum(function ($stock) {
                                        return (float) $stock->quantity;
                                    });

                                @endphp

                                <tr>

                                    {{-- NUMBER --}}

                                    <td>
                                        {{ $loop->iteration }}
                                    </td>


                                    {{-- ITEM --}}

                                    <td>

                                        <strong>
                                            {{ $item->name }}
                                        </strong>

                                        @if($item->description)

                                            <div class="requisition-field-help">
                                                {{ Str::limit($item->description, 60) }}
                                            </div>

                                        @endif

                                    </td>


                                    {{-- CATEGORY --}}

                                    <td>

                                        {{ $item->category->name ?? '—' }}

                                    </td>


                                    {{-- UNIT --}}

                                    <td>

                                        {{ $item->unit->code ?? $item->unit->name ?? '—' }}

                                    </td>


                                    {{-- TYPE --}}

                                    <td>

                                        @if($item->item_type === 'CONSUMABLE')

                                            <span class="requisition-list-status requisition-list-status-info">

                                                <span class="requisition-list-status-dot"></span>

                                                Consumable

                                            </span>

                                        @elseif($item->item_type === 'RETURNABLE')

                                            <span class="requisition-list-status requisition-list-status-warning">

                                                <span class="requisition-list-status-dot"></span>

                                                Returnable

                                            </span>

                                        @elseif($item->item_type === 'ASSET')

                                            <span class="requisition-list-status requisition-list-status-primary">

                                                <span class="requisition-list-status-dot"></span>

                                                Asset

                                            </span>

                                        @else

                                            <span class="requisition-list-status">

                                                <span class="requisition-list-status-dot"></span>

                                                {{ $item->item_type ?? '—' }}

                                            </span>

                                        @endif

                                    </td>


                                    {{-- SKU --}}

                                    <td>

                                        {{ $item->sku ?: '—' }}

                                    </td>


                                    {{-- STOCK --}}

                                    <td>

                                        <strong>
                                            {{ number_format($totalStock, 3) }}
                                        </strong>

                                    </td>


                                    {{-- REORDER LEVEL --}}

                                    <td>

                                        {{ number_format((float) $item->reorder_level, 3) }}

                                    </td>


                                    {{-- STATUS --}}

                                    <td>

                                        @if($item->is_active)

                                            <span class="requisition-list-status requisition-list-status-success">

                                                <span class="requisition-list-status-dot"></span>

                                                Active

                                            </span>

                                        @else

                                            <span class="requisition-list-status requisition-list-status-danger">

                                                <span class="requisition-list-status-dot"></span>

                                                Inactive

                                            </span>

                                        @endif

                                    </td>


                                    {{-- ACTIONS --}}

                                    <td>

                                        <div class="requisition-list-actions">

                                            {{-- VIEW --}}

                                            <a href="{{ route('admin.store-items.show', $item) }}"
                                               class="requisition-add-button primary">

                                                <i class="fa fa-eye"></i>
                                                View

                                            </a>


                                            {{-- EDIT --}}

                                            <a href="{{ route('admin.store-items.edit', $item) }}"
                                               class="requisition-add-button warning">

                                                <i class="fa fa-pencil"></i>
                                                Edit

                                            </a>


                                            {{-- ENABLE / DISABLE --}}

                                            <form method="POST"
                                                  action="{{ route('admin.store-items.toggle-status', $item) }}"
                                                  style="display:inline;">

                                                @csrf
                                                @method('PATCH')

                                                @if($item->is_active)

                                                    <button type="submit"
                                                            class="requisition-add-button danger">

                                                        <i class="fa fa-ban"></i>
                                                        Disable

                                                    </button>

                                                @else

                                                    <button type="submit"
                                                            class="requisition-add-button success">

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

                {{-- =================================================
                     EMPTY STATE
                     ================================================= --}}

                <div class="requisition-table-empty">

                    <div class="requisition-empty-icon">
                        <i class="fa fa-cubes"></i>
                    </div>

                    <h5>
                        No Store Items Yet
                    </h5>

                    <p>
                        Start by adding the first item to your Store Items
                        master list.
                    </p>

                    <a href="{{ route('admin.store-items.create') }}"
                       class="requisition-add-button">

                        <i class="fa fa-plus"></i>
                        Add Store Item

                    </a>

                </div>

            @endif

        </div>

    </div>

</div>

@endsection