
@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/stores.css') }}">
@endpush

@section('content')

<div class="store-requisition-page">

    {{-- PAGE HEADER --}}
    <div class="requisition-page-header">

        <div class="requisition-header-content">

            <div class="requisition-header-icon">
                <i class="fas fa-truck"></i>
            </div>

            <div>
                <div class="requisition-breadcrumb">
                    <span>Stores</span>
                    <i class="fas fa-chevron-right"></i>
                    <span>Suppliers</span>
                </div>

                <h1 class="requisition-page-title">Suppliers</h1>

                <p class="requisition-page-subtitle">
                    Manage suppliers used for purchasing and store LPOs.
                </p>
            </div>

        </div>

        <div class="requisition-header-right">
            <a href="{{ route('admin.suppliers.create') }}"
               class="requisition-add-button">
                <i class="fas fa-plus"></i>
                <span>Add Supplier</span>
            </a>
        </div>

    </div>

    <x-message></x-message>


{{-- ADVANCED SUPPLIER FILTERS --}}
<div class="requisition-section donation-filters-section">

    <details class="donation-filters-details"
        {{ request()->hasAny(['search', 'category', 'status', 'payment_terms']) ? 'open' : '' }}>

        <summary class="requisition-section-header donation-filters-summary">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fa fa-filter"></i>
                </div>

                <div>
                    <h5>Search & Filter Suppliers</h5>
                    <p>Click to expand or collapse the supplier filters.</p>
                </div>

            </div>

            <i class="fa fa-chevron-down donation-filters-chevron"></i>

        </summary>

        <div class="requisition-details-body donation-filters-body">

            <div class="requisition-section">

                <div class="requisition-details-body">

                    <form method="GET" action="{{ route('admin.suppliers.index') }}">

                        {{-- GENERAL SEARCH --}}
                        <div class="row mb-3">

                            <div class="col-md-4">

                                <label class="requisition-field-label">
                                    Search
                                </label>

                                <input
                                    type="text"
                                    name="search"
                                    value="{{ request('search') }}"
                                    class="requisition-input"
                                    placeholder="Supplier name, code, contact, phone, email or PIN"
                                >

                            </div>

                            {{-- CATEGORY --}}
                            <div class="col-md-3">

                                <label class="requisition-field-label">
                                    Supplier Category
                                </label>

                                <select name="category" class="requisition-input">

                                    <option value="">All Categories</option>

                                    @foreach($categories as $value => $label)

                                        <option
                                            value="{{ $value }}"
                                            @selected(request('category') === $value)
                                        >
                                            {{ $label }}
                                        </option>

                                    @endforeach

                                </select>

                            </div>

                            {{-- STATUS --}}
                            <div class="col-md-2">

                                <label class="requisition-field-label">
                                    Status
                                </label>

                                <select name="status" class="requisition-input">

                                    <option value="">All Statuses</option>

                                    <option value="active"
                                        @selected(request('status') === 'active')>
                                        Active
                                    </option>

                                    <option value="inactive"
                                        @selected(request('status') === 'inactive')>
                                        Inactive
                                    </option>

                                </select>

                            </div>

                            {{-- PAYMENT TERMS --}}
                            <div class="col-md-3">

                                <label class="requisition-field-label">
                                    Payment Terms
                                </label>

                                <input
                                    type="text"
                                    name="payment_terms"
                                    value="{{ request('payment_terms') }}"
                                    class="requisition-input"
                                    placeholder="Cash, 30 Days, 60 Days..."
                                >

                            </div>

                        </div>

                        {{-- ACTIONS --}}
                        <div class="d-flex flex-wrap justify-content-end"
                             style="gap: 10px;">

                            <button type="submit"
                                    class="requisition-save-button">

                                <i class="fa fa-search"></i>
                                Apply Filters

                            </button>

                            <a
                                href="{{ route('admin.suppliers.index') }}"
                                class="requisition-cancel-button"
                                style="display:inline-flex;align-items:center;justify-content:center;gap:6px;"
                            >

                                <i class="fa fa-times"></i>
                                Clear Filters

                            </a>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </details>

</div>



    {{-- SUPPLIERS TABLE --}}
    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">
                <div class="requisition-section-icon">
                    <i class="fas fa-truck"></i>
                </div>

                <div>
                    <h5>Suppliers</h5>
                    <p>Suppliers available for purchasing and store LPOs.</p>
                </div>
            </div>

            @if($suppliers->total())
                <span class="requisition-count-badge">
                    {{ $suppliers->total() }}
                    {{ $suppliers->total() === 1 ? 'Supplier' : 'Suppliers' }}
                </span>
            @endif

        </div>

        @if($suppliers->count())

            <div class="requisition-list-table-wrapper">

                <table class="requisition-list-table">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Supplier</th>
                            <th>Category</th>
                            <th>Contact</th>
                            <th>Payment Terms</th>
                            <th>Credit Limit</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($suppliers as $supplier)

                            <tr>

                                {{-- NUMBER --}}
                                <td>
                                    <span class="requisition-list-row-number">
                                        {{ $suppliers->firstItem() + $loop->index }}
                                    </span>
                                </td>

                                {{-- SUPPLIER --}}
                                <td>
                                    <div class="requisition-list-number">

                                        <a href="{{ route('admin.suppliers.show', $supplier) }}">
                                            {{ $supplier->name }}
                                        </a>

                                        <div class="requisition-list-meta">
                                            {{ $supplier->supplier_code }}
                                        </div>

                                    </div>
                                </td>

                                {{-- CATEGORY --}}
                                <td>
                                    @if($supplier->category && isset($categories[$supplier->category]))

                                        <span class="requisition-list-purpose">
                                            {{ $categories[$supplier->category] }}
                                        </span>

                                    @elseif($supplier->category)

                                        <span class="requisition-list-purpose">
                                            {{ \Illuminate\Support\Str::headline($supplier->category) }}
                                        </span>

                                    @else

                                        <span class="requisition-list-time">
                                            Not assigned
                                        </span>

                                    @endif
                                </td>

                                {{-- CONTACT --}}
                                <td>

                                    @if($supplier->contact_person)
                                        <div class="requisition-list-purpose">
                                            {{ $supplier->contact_person }}
                                        </div>
                                    @endif

                                    @if($supplier->phone)
                                        <div class="requisition-list-time">
                                            {{ $supplier->phone }}
                                        </div>
                                    @endif

                                    @if($supplier->email)
                                        <div class="requisition-list-time">
                                            {{ $supplier->email }}
                                        </div>
                                    @endif

                                    @if(!$supplier->contact_person && !$supplier->phone && !$supplier->email)
                                        <span class="requisition-list-time">—</span>
                                    @endif

                                </td>

                                {{-- PAYMENT TERMS --}}
                                <td>
                                    @if($supplier->payment_terms)

                                        <span class="requisition-list-purpose">
                                            {{ $supplier->payment_terms }}
                                        </span>

                                    @else

                                        <span class="requisition-list-time">—</span>

                                    @endif
                                </td>

                                {{-- CREDIT LIMIT --}}
                                <td>
                                    <div class="requisition-list-purpose">
                                        KES {{ number_format((float) $supplier->credit_limit, 2) }}
                                    </div>
                                </td>

                                {{-- STATUS --}}
                                <td>

                                    @if($supplier->is_active)

                                        <span class="requisition-list-status requisition-list-status-completed">
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

                                        <a href="{{ route('admin.suppliers.show', $supplier) }}"
                                           class="requisition-list-action primary"
                                           title="View Supplier"
                                           aria-label="View Supplier">

                                            <i class="fas fa-eye"></i>

                                        </a>

                                        <a href="{{ route('admin.suppliers.edit', $supplier) }}"
                                           class="requisition-list-action"
                                           title="Edit Supplier"
                                           aria-label="Edit Supplier">

                                            <i class="fas fa-edit"></i>

                                        </a>

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

            {{-- PAGINATION --}}
{{-- SUPPLIERS PAGINATION --}}
@if($suppliers->total() > 0)

    <div class="stores-pagination-section">

        <div class="stores-pagination-info">

            <div class="stores-pagination-info-icon">
                <i class="fa fa-layer-group"></i>
            </div>

            <div class="stores-pagination-info-text">

                <span class="stores-pagination-info-label">
                    Showing suppliers
                </span>

                <strong>
                    {{ $suppliers->firstItem() ?? 0 }}
                    –
                    {{ $suppliers->lastItem() ?? 0 }}
                </strong>

                <span class="stores-pagination-info-total">
                    of {{ number_format($suppliers->total()) }} total
                </span>

            </div>

        </div>

        @if($suppliers->hasPages())

            <div class="stores-pagination-controls">
                {{ $suppliers->onEachSide(2)->withQueryString()->links('pagination::bootstrap-5') }}
            </div>

        @endif

    </div>

@endif


        @else

            {{-- EMPTY STATE --}}
            <div class="requisition-table-empty">

                <div class="requisition-empty-icon">
                    <i class="fas fa-truck"></i>
                </div>

                <h5>No Suppliers Found</h5>

                <p>
                    @if(request()->hasAny(['search', 'category', 'status', 'payment_terms']))
                        No suppliers match your selected filters.
                    @else
                        No suppliers have been added to the Stores system yet.
                    @endif
                </p>

                @if(request()->hasAny(['search', 'category', 'status', 'payment_terms']))

                    <a href="{{ route('admin.suppliers.index') }}"
                       class="requisition-cancel-button">

                        <i class="fas fa-undo"></i>
                        Clear Filters

                    </a>

                @else

                    <a href="{{ route('admin.suppliers.create') }}"
                       class="requisition-add-button">

                        <i class="fas fa-plus"></i>
                        <span>Add First Supplier</span>

                    </a>

                @endif

            </div>

        @endif

    </div>

</div>

@endsection