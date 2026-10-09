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
                <i class="fa fa-folder"></i>
            </div>

            <div>

                <div class="requisition-breadcrumb">
                    <span>Stores</span>
                    <span>/</span>
                    <span>Item Categories</span>
                </div>

                <h1 class="requisition-page-title">
                    Store Item Categories
                </h1>

                <p class="requisition-page-subtitle">
                    Manage categories used to organize store items.
                </p>

            </div>

        </div>

        <div class="requisition-header-right">

            <a href="{{ route('admin.store-categories.create') }}"
               class="requisition-add-button">

                <i class="fa fa-plus"></i>
                Add Category

            </a>

        </div>

    </div>


    {{-- ============================================================
         MESSAGES
         ============================================================ --}}

    <x-message></x-message>


    {{-- ============================================================
         CATEGORIES
         ============================================================ --}}

    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fa fa-folder"></i>
                </div>

                <div>

                    <h5>Item Categories</h5>

                    <p>
                        Categories available for store items.
                    </p>

                </div>

            </div>

            @if($categories->count())

                <div class="requisition-count-badge">
                    {{ $categories->count() }}
                </div>

            @endif

        </div>


        <div class="requisition-details-body">

            @if($categories->count())

                <div class="requisition-list-table-wrapper">

                    <table class="requisition-list-table">

                        <thead>

                            <tr>
                                <th>#</th>
                                <th style="width: 40%">Category</th>
                                <th style="width: 20%">Description</th>
                                <th style="width: 20%">Items</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>

                        </thead>

                        <tbody>

                            @foreach($categories as $category)

                                <tr>

                                    {{-- NUMBER --}}

                                    <td>
                                        {{ $loop->iteration }}
                                    </td>


                                    {{-- CATEGORY --}}

                                    <td>

                                        <strong>
                                            {{ $category->name }}
                                        </strong>

                                    </td>


                                    {{-- DESCRIPTION --}}

                                    <td>
                                        {{ $category->description ?: '—' }}
                                    </td>


                                    {{-- ITEMS --}}

                                    <td>
                                        {{ $category->items_count }}
                                    </td>


                                    {{-- STATUS --}}

                                    <td>

                                        @if($category->is_active)

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

                                            <a href="{{ route('admin.store-categories.show', $category) }}"
                                               class="requisition-add-button primary"
                                               title="View Category">

                                                <i class="fa fa-eye"></i>
                                                View

                                            </a>


                                            {{-- EDIT --}}

                                            <a href="{{ route('admin.store-categories.edit', $category) }}"
                                               class="requisition-add-button warning"
                                               title="Edit Category">

                                                <i class="fa fa-pencil"></i>
                                                Edit

                                            </a>


                                            {{-- ACTIVATE / DEACTIVATE --}}

                                            <form action="{{ route('admin.store-categories.toggle-status', $category) }}"
                                                  method="POST"
                                                  style="display:inline;">

                                                @csrf
                                                @method('PATCH')

                                                @if($category->is_active)

                                                    <button type="submit"
                                                            class="requisition-add-button danger"
                                                            title="Deactivate Category"
                                                            onclick="return confirm('Deactivate this category?')">

                                                        <i class="fa fa-ban"></i>
                                                        Deactivate

                                                    </button>

                                                @else

                                                    <button type="submit"
                                                            class="requisition-add-button success"
                                                            title="Activate Category"
                                                            onclick="return confirm('Activate this category?')">

                                                        <i class="fa fa-check"></i>
                                                        Activate

                                                    </button>

                                                @endif

                                            </form>


                                            {{-- DELETE --}}

                                            @if($category->items_count == 0)

                                                <form action="{{ route('admin.store-categories.destroy', $category) }}"
                                                      method="POST"
                                                      style="display:inline;">

                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                            class="requisition-add-button danger"
                                                            title="Delete Category"
                                                            onclick="return confirm('Delete this category?')">

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
                        <i class="fa fa-folder-open"></i>
                    </div>

                    <h5>
                        No Store Item Categories
                    </h5>

                    <p>
                        Create your first store item category.
                    </p>

                    <a href="{{ route('admin.store-categories.create') }}"
                       class="requisition-add-button">

                        <i class="fa fa-plus"></i>
                        Add Category

                    </a>

                </div>

            @endif

        </div>

    </div>

</div>

@endsection