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
            <a href="{{ route('admin.store-items.create') }}" class="requisition-add-button">
                <i class="fa fa-plus"></i>
                Add Store Item
            </a>
        </div>

    </div>

    {{-- ============================================================
    MESSAGES
    ============================================================ --}}
    <x-message></x-message>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    {{-- ============================================================
    STORE ITEMS SECTION
    ============================================================ --}}
    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fa fa-cubes"></i>
                </div>

                <div>
                    <h5>Store Items</h5>

                    <p>
                        Master list of items available for store
                        management and requisitions.
                    </p>
                </div>

            </div>

            <div class="requisition-count-badge">
                {{ $items->total() }}
                {{ $items->total() === 1 ? 'item' : 'items' }}
            </div>

        </div>

        {{-- ========================================================
        SEARCH AND FILTERS
        ======================================================== --}}
        <div class="requisition-details-body">

            <form method="GET" action="{{ route('admin.store-items.index') }}">

                <div class="row g-3 align-items-end">

                    {{-- SEARCH --}}
                    <div class="col-lg-4 col-md-6">
                        <label class="requisition-field-label">
                            Search Items
                        </label>

                        <input type="text" name="search" value="{{ request('search') }}" class="requisition-input"
                            placeholder="Search name, SKU or description...">
                    </div>

                    {{-- CATEGORY --}}
                    <div class="col-lg-2 col-md-6">
                        <label class="requisition-field-label">
                            Category
                        </label>

                        <select name="category_id" class="requisition-input">

                            <option value="">All categories</option>

                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) request('category_id')===(string)
                                $category->id)
                                >
                                {{ $category->name }}
                            </option>
                            @endforeach

                        </select>
                    </div>

                    {{-- ITEM TYPE --}}
                    <div class="col-lg-2 col-md-6">
                        <label class="requisition-field-label">
                            Item Type
                        </label>

                        <select name="item_type" class="requisition-input">

                            <option value="">All types</option>

                            <option value="CONSUMABLE" @selected(request('item_type')==='CONSUMABLE' )>
                                Consumable
                            </option>

                            <option value="RETURNABLE" @selected(request('item_type')==='RETURNABLE' )>
                                Returnable
                            </option>

                            <option value="ASSET" @selected(request('item_type')==='ASSET' )>
                                Asset
                            </option>

                        </select>
                    </div>

                    {{-- STATUS --}}
                    <div class="col-lg-2 col-md-6">
                        <label class="requisition-field-label">
                            Status
                        </label>

                        <select name="status" class="requisition-input">

                            <option value="">All statuses</option>

                            <option value="active" @selected(request('status')==='active' )>
                                Active
                            </option>

                            <option value="inactive" @selected(request('status')==='inactive' )>
                                Inactive
                            </option>

                        </select>
                    </div>

                    {{-- FILTER BUTTONS --}}
                    <div class="col-lg-2 col-md-6">
                        <div class="d-flex gap-2">

                            <button type="submit" class="requisition-save-button">
                                <i class="fa fa-search"></i>
                                Filter
                            </button>

                            <a href="{{ route('admin.store-items.index') }}" class="requisition-cancel-button">
                                Clear
                            </a>

                        </div>
                    </div>

                </div>

            </form>

        </div>

        {{-- ========================================================
        ITEMS TABLE
        ======================================================== --}}
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
                        $totalStock = $item->stocks->sum(
                        fn ($stock) => (float) $stock->quantity
                        );
                        @endphp

                        <tr>

                            {{-- NUMBER --}}
                            <td>
                                <span class="requisition-list-row-number">
                                    {{ $items->firstItem() + $loop->index }}
                                </span>
                            </td>

                            {{-- ITEM --}}
                            <td>
                                <strong>{{ $item->name }}</strong>

                                @if($item->description)
                                <div class="requisition-field-help">
                                    {{ \Illuminate\Support\Str::limit($item->description, 60) }}
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
                                    <form method="POST" action="{{ route('admin.store-items.toggle-status', $item) }}"
                                        style="display:inline;">
                                        @csrf
                                        @method('PATCH')

                                        @if($item->is_active)
                                        <button type="submit" class="requisition-add-button danger"
                                            onclick="return confirm('Disable this store item?');">
                                            <i class="fa fa-ban"></i>
                                            Disable
                                        </button>
                                        @else
                                        <button type="submit" class="requisition-add-button success"
                                            onclick="return confirm('Enable this store item?');">
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

            @if($items->total() > 0)

            <div class="stores-pagination-section">

                <div class="stores-pagination-info">

                    <div class="stores-pagination-info-icon">
                        <i class="fa fa-layer-group"></i>
                    </div>

                    <div class="stores-pagination-info-text">

                        <span class="stores-pagination-info-label">
                            Showing requisitions
                        </span>

                        <strong>
                            {{ $items->firstItem() ?? 0 }}
                            –
                            {{ $items->lastItem() ?? 0 }}
                        </strong>

                        <span class="stores-pagination-info-total">
                            of {{ number_format($items->total()) }} total
                        </span>

                    </div>

                </div>

                @if($items->hasPages())

                <div class="stores-pagination-controls">
                    {{ $items->onEachSide(2)->withQueryString()->links('pagination::bootstrap-5') }}
                </div>

                @endif

            </div>

            @endif

            @else

            {{-- EMPTY STATE --}}
            <div class="requisition-table-empty">

                <div class="requisition-empty-icon">
                    <i class="fa fa-cubes"></i>
                </div>

                <h5>
                    {{ request()->hasAny(['search', 'category_id', 'item_type', 'status'])
                    ? 'No Matching Store Items'
                    : 'No Store Items Yet' }}
                </h5>

                <p>
                    {{ request()->hasAny(['search', 'category_id', 'item_type', 'status'])
                    ? 'Try changing your search or filter selections.'
                    : 'Start by adding the first item to your Store Items master list.' }}
                </p>

                @if(request()->hasAny(['search', 'category_id', 'item_type', 'status']))
                <a href="{{ route('admin.store-items.index') }}" class="requisition-cancel-button">
                    Clear Filters
                </a>
                @else
                <a href="{{ route('admin.store-items.create') }}" class="requisition-add-button">
                    <i class="fa fa-plus"></i>
                    Add Store Item
                </a>
                @endif

            </div>

            @endif

        </div>

    </div>

</div>

@endsection