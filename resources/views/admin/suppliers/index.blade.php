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
                <i class="fas fa-truck"></i>
            </div>

            <div>

                <div class="requisition-breadcrumb">

                    <span>Stores</span>

                    <i class="fas fa-chevron-right"></i>

                    <span>Suppliers</span>

                </div>

                <h1 class="requisition-page-title">
                    Suppliers
                </h1>

                <p class="requisition-page-subtitle">
                    Manage suppliers used for purchasing and store LPOs.
                </p>

            </div>

        </div>

        <div class="requisition-header-right">

            <a href="{{ route('admin.suppliers.create') }}" class="requisition-add-button">

                <i class="fas fa-plus"></i>

                <span>
                    Add Supplier
                </span>

            </a>

        </div>

    </div>


    {{-- ============================================================
    FLASH / SYSTEM MESSAGES
    ============================================================ --}}

    <x-message></x-message>


    {{-- ============================================================
    SUPPLIERS SECTION
    ============================================================ --}}

    <div class="requisition-section">

        {{-- Section Header --}}

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">

                    <i class="fas fa-truck"></i>

                </div>

                <div>

                    <h5>
                        Suppliers
                    </h5>

                    <p>
                        Suppliers available for purchasing and store LPOs.
                    </p>

                </div>

            </div>

            @if($suppliers->count())

            <span class="requisition-count-badge">

                {{ $suppliers->count() }}

                {{ $suppliers->count() === 1 ? 'Supplier' : 'Suppliers' }}

            </span>

            @endif

        </div>


        {{-- ========================================================
        SUPPLIER TABLE
        ======================================================== --}}

        @if($suppliers->count())

        <div class="requisition-list-table-wrapper">

            <table class="requisition-list-table">

                <thead>

                    <tr>

                        <th>
                            #
                        </th>

                        <th>
                            Supplier
                        </th>

                        <th>
                            Contact
                        </th>

                        <th>
                            Payment Terms
                        </th>

                        <th>
                            Credit Limit
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Actions
                        </th>

                    </tr>

                </thead>

                <tbody>

                    @foreach($suppliers as $supplier)

                    <tr>

                        {{-- =================================================
                        ROW NUMBER
                        ================================================= --}}

                        <td>

                            <span class="requisition-list-row-number">

                                {{ $suppliers->firstItem() + $loop->index }}

                            </span>

                        </td>


                        {{-- =================================================
                        SUPPLIER
                        ================================================= --}}

                        <td>

                            <div class="requisition-list-number">

                                <a href="{{ route(
                                        'admin.suppliers.show',
                                        $supplier
                                    ) }}">

                                    {{ $supplier->name }}

                                </a>

                                <div class="requisition-list-meta">

                                    {{ $supplier->supplier_code }}

                                </div>

                            </div>

                        </td>


                        {{-- =================================================
                        CONTACT
                        ================================================= --}}

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

                            @if(
                            !$supplier->contact_person &&
                            !$supplier->phone &&
                            !$supplier->email
                            )

                            <span class="requisition-list-time">
                                —
                            </span>

                            @endif

                        </td>


                        {{-- =================================================
                        PAYMENT TERMS
                        ================================================= --}}

                        <td>

                            @if($supplier->payment_terms)

                            <span class="requisition-list-purpose">

                                {{ $supplier->payment_terms }}

                            </span>

                            @else

                            <span class="requisition-list-time">
                                —
                            </span>

                            @endif

                        </td>


                        {{-- =================================================
                        CREDIT LIMIT
                        ================================================= --}}

                        <td>

                            @if($supplier->credit_limit !== null)

                            <div class="requisition-list-purpose">

                                KES
                                {{ number_format(
                                (float) $supplier->credit_limit,
                                2
                                ) }}

                            </div>

                            @else

                            <span class="requisition-list-time">
                                —
                            </span>

                            @endif

                        </td>


                        {{-- =================================================
                        STATUS
                        ================================================= --}}

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


                        {{-- =================================================
                        ACTIONS
                        ================================================= --}}

                        <td>

                            <div class="requisition-list-actions">

                                {{-- View --}}

                                <a href="{{ route(
                                        'admin.suppliers.show',
                                        $supplier
                                    ) }}" class="requisition-list-action primary" title="View Supplier"
                                    aria-label="View Supplier">

                                    <i class="fas fa-eye"></i>

                                </a>


                                {{-- Edit --}}

                                <a href="{{ route(
                                        'admin.suppliers.edit',
                                        $supplier
                                    ) }}" class="requisition-list-action" title="Edit Supplier"
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


        {{-- ========================================================
        HORIZONTAL SCROLL HINT
        ======================================================== --}}

        <div class="requisition-scroll-hint">

            <i class="fas fa-arrows-alt-h"></i>

            <span>
                Scroll horizontally to view all supplier details.
            </span>

        </div>



        @else


        {{-- ========================================================
        EMPTY STATE
        ======================================================== --}}

        <div class="requisition-table-empty">

            <div class="requisition-empty-icon">

                <i class="fas fa-truck"></i>

            </div>

            <h5>
                No Suppliers Yet
            </h5>

            <p>
                No suppliers have been added to the Stores system yet.
            </p>

            <a href="{{ route('admin.suppliers.create') }}" class="requisition-add-button">

                <i class="fas fa-plus"></i>

                <span>
                    Add First Supplier
                </span>

            </a>

        </div>

        @endif

    </div>


</div>

@endsection