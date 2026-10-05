@extends('layouts.admin')

@push('styles') <link rel="stylesheet" href="{{ asset('assets/css/stores.css') }}">
@endpush

@section('content')

<div class="store-requisition-page">


{{-- ============================================================
     PAGE HEADER
     ============================================================ --}}
<div class="requisition-page-header">

    <div class="requisition-header-content">

        <div class="requisition-header-icon">
            <i class="fas fa-warehouse"></i>
        </div>

        <div>
            <div class="requisition-breadcrumb">
                <span>Stores</span>
                <i class="fas fa-chevron-right"></i>
                <span>Store Management</span>
            </div>

            <h1 class="requisition-page-title">
                Stores
            </h1>

            <p class="requisition-page-subtitle">
                Manage stores, stock records and store status.
            </p>
        </div>

    </div>

    <div class="requisition-header-right">

        <a href="{{ route('admin.stores.create') }}"
           class="requisition-add-button">
            <i class="fas fa-plus"></i>
            <span>Add Store</span>
        </a>

    </div>

</div>


<x-message></x-message>


{{-- ============================================================
     STORE LIST
     ============================================================ --}}
<div class="requisition-section">

    <div class="requisition-section-header">

        <div class="requisition-section-heading">

            <div class="requisition-section-icon">
                <i class="fas fa-warehouse"></i>
            </div>

            <div>
                <h5>
                    Store List
                </h5>

                <p>
                    Stores currently configured in the system.
                </p>
            </div>

        </div>

        @if($stores->count())
            <span class="requisition-count-badge">
                {{ $stores->count() }}
                {{ $stores->count() === 1 ? 'Store' : 'Stores' }}
            </span>
        @endif

    </div>


    @if($stores->count())

        <div class="requisition-list-table-wrapper">

            <table class="requisition-list-table">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Store</th>
                        <th>Code</th>
                        <th>Description</th>
                        <th>Stock Records</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                    @foreach($stores as $store)

                        <tr>

                            {{-- NUMBER --}}
                            <td>
                                <span class="requisition-list-row-number">
                                    {{ $loop->iteration }}
                                </span>
                            </td>


                            {{-- STORE --}}
                            <td>
                                <div class="requisition-list-number">
                                    {{ $store->name }}
                                </div>
                            </td>


                            {{-- CODE --}}
                            <td>
                                <div class="requisition-list-meta">
                                    {{ $store->code }}
                                </div>
                            </td>


                            {{-- DESCRIPTION --}}
                            <td>
                                <div class="requisition-list-purpose">
                                    {{ $store->description ?: '—' }}
                                </div>
                            </td>


                            {{-- STOCK RECORDS --}}
                            <td>
                                <div class="requisition-list-number">
                                    {{ $store->stocks_count }}
                                </div>
                            </td>


                            {{-- STATUS --}}
                            <td>

                                @if($store->is_active)

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

                                    {{-- VIEW --}}
                                    <a href="{{ route('admin.stores.show', $store) }}"
                                       class="requisition-list-action primary"
                                       title="View Store">

                                        <i class="fas fa-eye"></i>

                                    </a>


                                    {{-- EDIT --}}
                                    <a href="{{ route('admin.stores.edit', $store) }}"
                                       class="requisition-list-action"
                                       title="Edit Store">

                                        <i class="fas fa-edit"></i>

                                    </a>


                                    {{-- TOGGLE STATUS --}}
                                    <form action="{{ route('admin.stores.toggle-status', $store) }}"
                                          method="POST">

                                        @csrf
                                        @method('PATCH')

                                        <button type="submit"
                                                class="requisition-list-action"
                                                title="{{ $store->is_active ? 'Deactivate Store' : 'Activate Store' }}"
                                                onclick="return confirm('{{ $store->is_active ? 'Deactivate this store?' : 'Activate this store?' }}')">

                                            <i class="fas {{ $store->is_active ? 'fa-ban' : 'fa-check' }}"></i>

                                        </button>

                                    </form>


                                    {{-- DELETE --}}
                                    @if($store->stocks_count == 0)

                                        <form action="{{ route('admin.stores.destroy', $store) }}"
                                              method="POST">

                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="requisition-list-action"
                                                    title="Delete Store"
                                                    onclick="return confirm('Delete this store?')">

                                                <i class="fas fa-trash"></i>

                                            </button>

                                        </form>

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

        <div class="requisition-scroll-hint">
            <i class="fas fa-arrows-alt-h"></i>
            <span>
                Scroll horizontally to view all store details.
            </span>
        </div>


        {{-- PAGINATION --}}
        @if(method_exists($stores, 'links'))
            <div class="requisition-pagination">
                {{ $stores->links() }}
            </div>
        @endif


    @else

        <div class="requisition-table-empty">

            <div class="requisition-empty-icon">
                <i class="fas fa-warehouse"></i>
            </div>

            <h5>
                No Stores Yet
            </h5>

            <p>
                No stores have been configured in the system yet.
            </p>

            <a href="{{ route('admin.stores.create') }}"
               class="requisition-add-button">

                <i class="fas fa-plus"></i>
                <span>Add First Store</span>

            </a>

        </div>

    @endif

</div>


</div>

@endsection
