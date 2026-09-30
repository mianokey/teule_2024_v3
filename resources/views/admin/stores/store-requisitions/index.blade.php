@extends('layouts.admin')

@push('styles') <link rel="stylesheet" href="{{ asset('assets/css/stores.css') }}">
@endpush

@section('content')

<div class="store-requisition-page">


{{-- PAGE HEADER --}}
<div class="requisition-page-header">

    <div class="requisition-header-content">

        <div class="requisition-header-icon">
            <i class="fa fa-clipboard-list"></i>
        </div>

        <div>
            <div class="requisition-breadcrumb">
                <span>Stores</span>
                <span>/</span>
                <span>Requisitions</span>
            </div>

            <h1 class="requisition-page-title">
                My Requisitions
            </h1>

            <p class="requisition-page-subtitle">
                View and manage your store requisitions.
            </p>
        </div>

    </div>

    <div class="requisition-header-right">

        <a
            href="{{ route('admin.stores.store-requisitions.create') }}"
            class="requisition-add-button"
        >
            <i class="fa fa-plus"></i>
            New Requisition
        </a>

    </div>

</div>


{{-- SUCCESS / ERROR MESSAGES --}}

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


{{-- REQUISITIONS SECTION --}}

<div class="requisition-section">

    {{-- SECTION HEADER --}}

    <div class="requisition-section-header">

        <div class="requisition-section-heading">

            <div class="requisition-section-icon">
                <i class="fa fa-list"></i>
            </div>

            <div>
                <h5>
                    My Requisitions
                </h5>

                <p>
                    Track your requests and their current status.
                </p>
            </div>

        </div>

        <div class="requisition-count-badge">
            {{ $requisitions->total() }}
            {{ $requisitions->total() === 1 ? 'Requisition' : 'Requisitions' }}
        </div>

    </div>


    {{-- FILTERS --}}

    <div class="requisition-details-body">

        <form
            method="GET"
            action="{{ route('admin.stores.store-requisitions.index') }}"
        >

            <div class="row g-3 align-items-end">

                {{-- SEARCH --}}

                <div class="col-lg-6">

                    <label class="requisition-field-label">
                        Search
                    </label>

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        class="requisition-input"
                        placeholder="Search requisition number, department or purpose..."
                    >

                </div>


                {{-- STATUS --}}

                <div class="col-lg-3">

                    <label class="requisition-field-label">
                        Status
                    </label>

                    <select
                        name="status"
                        class="requisition-input"
                    >

                        <option value="">
                            All statuses
                        </option>

                        <option
                            value="draft"
                            @selected(request('status') === 'draft')
                        >
                            Draft
                        </option>

                        <option
                            value="submitted"
                            @selected(request('status') === 'submitted')
                        >
                            Submitted
                        </option>

                        <option
                            value="approved"
                            @selected(request('status') === 'approved')
                        >
                            Approved
                        </option>

                        <option
                            value="completed"
                            @selected(request('status') === 'completed')
                        >
                            Completed
                        </option>

                    </select>

                </div>


                {{-- FILTER BUTTONS --}}

                <div class="col-lg-3">

                    <div class="d-flex gap-2">

                        <button
                            type="submit"
                            class="requisition-save-button"
                        >
                            <i class="fa fa-search"></i>
                            Filter
                        </button>

                        <a
                            href="{{ route('admin.stores.store-requisitions.index') }}"
                            class="requisition-cancel-button"
                        >
                            Clear
                        </a>

                    </div>

                </div>

            </div>

        </form>

    </div>


    {{-- TABLE --}}

    <div class="requisition-list-table-wrapper">

        <table class="requisition-list-table">

            <thead>

                <tr>

                    <th style="width: 55px;">
                        #
                    </th>

                    <th>
                        Requisition
                    </th>

                    <th>
                        Department
                    </th>

                    <th>
                        Items
                    </th>

                    <th>
                        Purpose
                    </th>

                    <th>
                        Date
                    </th>

                    <th>
                        Status
                    </th>

                    <th style="width: 120px;">
                        Action
                    </th>

                </tr>

            </thead>


            <tbody>

                @forelse($requisitions as $requisition)

                    @php

                        $status = strtolower(
                            $requisition->status ?? 'draft'
                        );

                        $statusLabel = ucfirst(
                            str_replace('_', ' ', $status)
                        );

                    @endphp

                    <tr>

                        {{-- NUMBER --}}

                        <td>

                            <span class="requisition-list-row-number">
                                {{ $requisitions->firstItem() + $loop->index }}
                            </span>

                        </td>


                        {{-- REQUISITION NUMBER --}}

                        <td>

                            <div class="requisition-list-number">
                              <a
    href="{{ route('admin.stores.store-requisitions.show', $requisition) }}"
>
    {{ $requisition->requisition_number }}
</a>

                            </div>

                            @if($requisition->requester)

                                <div class="requisition-list-meta">
                                    {{ $requisition->requester->name }}
                                </div>

                            @endif

                        </td>


                        {{-- DEPARTMENT --}}

                        <td>

                            <span class="requisition-list-department">
                                {{ $requisition->department }}
                            </span>

                        </td>


                        {{-- ITEMS --}}

                        <td>

                            <span class="requisition-list-item-count">

                                {{ $requisition->items->count() }}

                                {{ $requisition->items->count() === 1 ? 'Item' : 'Items' }}

                            </span>

                        </td>


                        {{-- PURPOSE --}}

                        <td>

                            <div class="requisition-list-purpose">

                                {{ \Illuminate\Support\Str::limit(
                                    $requisition->purpose,
                                    65
                                ) }}

                            </div>

                        </td>


                        {{-- DATE --}}

                        <td>

                            <div class="requisition-list-date">

                                {{ $requisition->created_at?->format('d M Y') }}

                            </div>

                            <div class="requisition-list-time">

                                {{ $requisition->created_at?->format('H:i') }}

                            </div>

                        </td>


                        {{-- STATUS --}}

                        <td>

                            <span
                                class="requisition-list-status requisition-list-status-{{ $status }}"
                            >

                                <span class="requisition-list-status-dot"></span>

                                {{ $statusLabel }}

                            </span>

                        </td>


                        {{-- ACTION --}}

{{-- ACTION --}}
<td>
    <div class="requisition-list-actions">

        @if(
            $status === 'draft' &&
            (int) $requisition->requested_by === (int) auth()->id()
        )

            {{-- EDIT --}}
            <a
                href="{{ route(
                    'admin.stores.store-requisitions.edit',
                    $requisition
                ) }}"
                class="requisition-list-action"
                title="Edit requisition"
            >
                <i class="fa fa-pen"></i>
            </a>

            {{-- DELETE --}}
            <form
                method="POST"
                action="{{ route(
                    'admin.stores.store-requisitions.destroy',
                    $requisition
                ) }}"
                class="d-inline"
                onsubmit="return confirm(
                    'Delete this draft requisition? This action cannot be undone.'
                );"
            >
                @csrf
                @method('DELETE')

                <button
                    type="submit"
                    class="requisition-list-action"
                    title="Delete draft requisition"
                >
                    <i class="fa fa-trash"></i>
                </button>
            </form>

        @else

            {{-- VIEW --}}
            <a
                href="{{ route(
                    'admin.stores.store-requisitions.show',
                    $requisition
                ) }}"
                class="requisition-list-action"
                title="View requisition"
            >
                <i class="fa fa-eye"></i>
            </a>

        @endif

    </div>
</td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="8">

                            <div class="requisition-table-empty">

                                <div class="requisition-empty-icon">
                                    <i class="fa fa-clipboard"></i>
                                </div>

                                <h5>
                                    No requisitions found
                                </h5>

                                <p>
                                    You have not created any requisitions yet.
                                </p>

                                <a
                                    href="{{ route('admin.stores.store-requisitions.create') }}"
                                    class="requisition-add-button"
                                >
                                    <i class="fa fa-plus"></i>
                                    Create Requisition
                                </a>

                            </div>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- PAGINATION --}}

    @if($requisitions->hasPages())

        <div class="requisition-pagination">
            {{ $requisitions->links() }}
        </div>

    @endif

</div>


</div>

@endsection
