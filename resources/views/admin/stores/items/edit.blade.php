@extends('layouts.admin')

@push('styles') <link rel="stylesheet" href="{{ asset('assets/css/stores.css') }}">
@endpush

@section('content')

<div class="store-requisition-page">


{{-- ============================================================
     PAGE HEADER
     ============================================================ --}}
<div class="requisition-page-header">

    <div>
        <h2 class="requisition-page-title">
            Edit Store Item
        </h2>

        <div class="requisition-page-subtitle">
            Update the details of this store item.
        </div>
    </div>

    <div class="requisition-page-header-actions">
        <a
            href="{{ route('admin.store-items.show', $storeItem) }}"
            class="requisition-add-button secondary"
        >
            <i class="fa fa-arrow-left"></i>
            Back
        </a>
    </div>

</div>


{{-- ============================================================
     MESSAGES
     ============================================================ --}}
<x-message></x-message>


{{-- ============================================================
     FORM
     ============================================================ --}}
<form
    method="POST"
    action="{{ route('admin.store-items.update', $storeItem) }}"
>
    @csrf
    @method('PUT')


    {{-- ========================================================
         ITEM DETAILS
         ======================================================== --}}
    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">
                <span class="requisition-section-icon">
                    <i class="fa fa-cube"></i>
                </span>

                <span>
                    Item Details
                </span>
            </div>

        </div>


        <div class="requisition-details-body">

            <div class="row">

                {{-- ==================================================
                     CATEGORY
                     ================================================== --}}
                <div class="col-md-6 mb-3">

                    <label class="requisition-field-label">
                        Category
                        <span class="text-danger">*</span>
                    </label>

                    <select
                        name="category_id"
                        class="requisition-input @error('category_id') is-invalid @enderror"
                        required
                    >
                        <option value="">
                            Select Category
                        </option>

                        @foreach($categories as $category)

                            <option
                                value="{{ $category->id }}"
                                {{ old('category_id', $storeItem->category_id) == $category->id ? 'selected' : '' }}
                            >
                                {{ $category->name }}
                            </option>

                        @endforeach

                    </select>

                    @error('category_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- ==================================================
                     UNIT
                     ================================================== --}}
                <div class="col-md-6 mb-3">

                    <label class="requisition-field-label">
                        Unit
                        <span class="text-danger">*</span>
                    </label>

                    <select
                        name="unit_id"
                        class="requisition-input @error('unit_id') is-invalid @enderror"
                        required
                    >
                        <option value="">
                            Select Unit
                        </option>

                        @foreach($units as $unit)

                            <option
                                value="{{ $unit->id }}"
                                {{ old('unit_id', $storeItem->unit_id) == $unit->id ? 'selected' : '' }}
                            >
                                {{ $unit->name }} ({{ $unit->code }})
                            </option>

                        @endforeach

                    </select>

                    @error('unit_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- ==================================================
                     ITEM NAME
                     ================================================== --}}
                <div class="col-md-8 mb-3">

                    <label class="requisition-field-label">
                        Item Name
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name', $storeItem->name) }}"
                        class="requisition-input @error('name') is-invalid @enderror"
                        required
                    >

                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- ==================================================
                     SKU
                     ================================================== --}}
                <div class="col-md-4 mb-3">

                    <label class="requisition-field-label">
                        SKU
                    </label>

                    <input
                        type="text"
                        name="sku"
                        value="{{ old('sku', $storeItem->sku) }}"
                        class="requisition-input @error('sku') is-invalid @enderror"
                    >

                    @error('sku')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- ==================================================
                     ITEM TYPE
                     ================================================== --}}
                <div class="col-md-6 mb-3">

                    <label class="requisition-field-label">
                        Item Type
                        <span class="text-danger">*</span>
                    </label>

                    <select
                        name="item_type"
                        class="requisition-input @error('item_type') is-invalid @enderror"
                        required
                    >

                        <option
                            value="CONSUMABLE"
                            {{ old('item_type', $storeItem->item_type) === 'CONSUMABLE' ? 'selected' : '' }}
                        >
                            Consumable
                        </option>

                        <option
                            value="RETURNABLE"
                            {{ old('item_type', $storeItem->item_type) === 'RETURNABLE' ? 'selected' : '' }}
                        >
                            Returnable
                        </option>

                        <option
                            value="ASSET"
                            {{ old('item_type', $storeItem->item_type) === 'ASSET' ? 'selected' : '' }}
                        >
                            Asset
                        </option>

                    </select>

                    @error('item_type')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- ==================================================
                     REORDER LEVEL
                     ================================================== --}}
                <div class="col-md-6 mb-3">

                    <label class="requisition-field-label">
                        Reorder Level
                        <span class="text-danger">*</span>
                    </label>

                    <input
                        type="number"
                        name="reorder_level"
                        value="{{ old('reorder_level', $storeItem->reorder_level) }}"
                        class="requisition-input @error('reorder_level') is-invalid @enderror"
                        min="0"
                        step="0.001"
                        required
                    >

                    @error('reorder_level')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- ==================================================
                     DESCRIPTION
                     ================================================== --}}
                <div class="col-12 mb-3">

                    <label class="requisition-field-label">
                        Description
                    </label>

                    <textarea
                        name="description"
                        rows="4"
                        class="requisition-input @error('description') is-invalid @enderror"
                    >{{ old('description', $storeItem->description) }}</textarea>

                    @error('description')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>

        </div>

    </div>


    {{-- ============================================================
         ACTIONS
         ============================================================ --}}
    <div class="requisition-bottom-actions">

        <a
            href="{{ route('admin.store-items.show', $storeItem) }}"
            class="requisition-add-button secondary"
        >
            <i class="fa fa-times"></i>
            Cancel
        </a>

        <button
            type="submit"
            class="requisition-add-button primary"
        >
            <i class="fa fa-save"></i>
            Update Store Item
        </button>

    </div>

</form>


</div>

@endsection
