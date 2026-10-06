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
                </div>

                <h1 class="requisition-page-title">
                    Store Units
                </h1>

                <p class="requisition-page-subtitle">
                    Manage measurement units used by store items.
                </p>

            </div>

        </div>

        <div class="requisition-header-right">

            <a href="{{ route('admin.store-units.create') }}"
               class="requisition-add-button">

                <i class="fa fa-plus"></i>
                Add Unit

            </a>

        </div>

    </div>


    {{-- ============================================================
         MESSAGES
         ============================================================ --}}

    <x-message></x-message>


    {{-- ============================================================
         STORE UNITS
         ============================================================ --}}

    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fa fa-balance-scale"></i>
                </div>

                <div>

                    <h5>Store Units</h5>

                    <p>
                        Measurement units available for store items.
                    </p>

                </div>

            </div>

            @if($units->count())

                <div class="requisition-count-badge">
                    {{ $units->count() }}
                </div>

            @endif

        </div>


        <div class="requisition-details-body">

            @if($units->count())

                <div class="requisition-list-table-wrapper">

                    <table class="requisition-list-table">

                        <thead>

                            <tr>
                                <th>#</th>
                                <th>Unit</th>
                                <th>Code</th>
                                <th>Description</th>
                                <th>Items</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>

                        </thead>

                        <tbody>

                            @foreach($units as $unit)

                                <tr>

                                    {{-- NUMBER --}}

                                    <td>
                                        {{ $loop->iteration }}
                                    </td>


                                    {{-- UNIT --}}

                                    <td>

                                        <strong>
                                            {{ $unit->name }}
                                        </strong>

                                    </td>


                                    {{-- CODE --}}

                                    <td>

                                        <strong>
                                            {{ $unit->code }}
                                        </strong>

                                    </td>


                                    {{-- DESCRIPTION --}}

                                    <td>

                                        {{ $unit->description ?: '—' }}

                                    </td>


                                    {{-- ITEMS --}}

                                    <td>

                                        {{ $unit->items_count }}

                                    </td>


                                    {{-- STATUS --}}

                                    <td>

                                        @if($unit->is_active)

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

                                            <a href="{{ route('admin.store-units.show', $unit) }}"
                                               class="requisition-add-button primary"
                                               title="View Unit">

                                                <i class="fa fa-eye"></i>
                                                View

                                            </a>


                                            {{-- EDIT --}}

                                            <a href="{{ route('admin.store-units.edit', $unit) }}"
                                               class="requisition-add-button warning"
                                               title="Edit Unit">

                                                <i class="fa fa-pencil"></i>
                                                Edit

                                            </a>


                                            {{-- ACTIVATE / DEACTIVATE --}}

                                            <form action="{{ route('admin.store-units.toggle-status', $unit) }}"
                                                  method="POST"
                                                  style="display:inline;">

                                                @csrf
                                                @method('PATCH')

                                                @if($unit->is_active)

                                                    <button type="submit"
                                                            class="requisition-add-button danger"
                                                            title="Deactivate Unit"
                                                            onclick="return confirm('Deactivate this unit?')">

                                                        <i class="fa fa-ban"></i>
                                                        Deactivate

                                                    </button>

                                                @else

                                                    <button type="submit"
                                                            class="requisition-list-action success"
                                                            title="Activate Unit"
                                                            onclick="return confirm('Activate this unit?')">

                                                        <i class="fa fa-check"></i>
                                                        Activate

                                                    </button>

                                                @endif

                                            </form>


                                            {{-- DELETE --}}

                                            @if($unit->items_count == 0)

                                                <form action="{{ route('admin.store-units.destroy', $unit) }}"
                                                      method="POST"
                                                      style="display:inline;">

                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                            class="requisition-list-action danger"
                                                            title="Delete Unit"
                                                            onclick="return confirm('Delete this unit?')">

                                                        <i class="fa fa-trash"></i>
                                                        Delete

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

            @else

                {{-- ====================================================
                     EMPTY STATE
                     ==================================================== --}}

                <div class="requisition-table-empty">

                    <div class="requisition-empty-icon">
                        <i class="fa fa-balance-scale"></i>
                    </div>

                    <h5>
                        No Store Units
                    </h5>

                    <p>
                        Create your first store unit.
                    </p>

                    <a href="{{ route('admin.store-units.create') }}"
                       class="requisition-add-button">

                        <i class="fa fa-plus"></i>
                        Add Unit

                    </a>

                </div>

            @endif

        </div>

    </div>

</div>

@endsection