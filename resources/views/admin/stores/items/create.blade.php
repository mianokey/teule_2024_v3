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
                <i class="fa fa-cube"></i>
            </div>

            <div>
                <div class="requisition-breadcrumb">
                    <span>Stores</span>
                    <span>/</span>
                    <span>Store Items</span>
                    <span>/</span>
                    <span>Add</span>
                </div>

                <h1 class="requisition-page-title">
                    Add Store Item
                </h1>

                <p class="requisition-page-subtitle">
                    Create a new item for the store inventory.
                </p>
            </div>

        </div>

        <div class="requisition-header-right">

            <a
                href="{{ route('admin.store-items.index') }}"
                class="requisition-cancel-button"
            >
                <i class="fa fa-arrow-left"></i>
                Back to Store Items
            </a>

        </div>

    </div>


    {{-- ============================================================
         MESSAGES
         ============================================================ --}}
    <x-message></x-message>

    @if($errors->any())

        <div class="alert alert-danger">

            <strong>Please correct the following:</strong>

            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>

        </div>

    @endif


    <form
        method="POST"
        action="{{ route('admin.store-items.store') }}"
    >

        @csrf


        {{-- ========================================================
             ITEM CLASSIFICATION
             ======================================================== --}}
        <div class="requisition-section">

            <div class="requisition-section-header">

                <div class="requisition-section-heading">

                    <div class="requisition-section-icon">
                        <i class="fa fa-tags"></i>
                    </div>

                    <div>
                        <h5>Item Classification</h5>

                        <p>
                            Select the category and measurement unit for this item.
                        </p>
                    </div>

                </div>

            </div>


            <div class="requisition-details-body">

                <div class="requisition-form-row">


                    {{-- CATEGORY --}}
                    <div class="requisition-form-group requisition-form-group-half">

                        <label
                            for="category_id"
                            class="requisition-field-label"
                        >
                            Category
                            <span class="text-danger">*</span>
                        </label>

                        <select
                            name="category_id"
                            id="category_id"
                            class="requisition-input"
                            required
                        >
                            <option value="">
                                Select Category
                            </option>

                            @foreach($categories as $category)

                                <option
                                    value="{{ $category->id }}"
                                    @selected(
                                        old('category_id') == $category->id
                                    )
                                >
                                    {{ $category->name }}
                                </option>

                            @endforeach

                        </select>

                        @error('category_id')
                            <div class="text-danger mt-1">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- UNIT --}}
                    <div class="requisition-form-group requisition-form-group-half">

                        <label
                            for="unit_id"
                            class="requisition-field-label"
                        >
                            Unit
                            <span class="text-danger">*</span>
                        </label>

                        <select
                            name="unit_id"
                            id="unit_id"
                            class="requisition-input"
                            required
                        >
                            <option value="">
                                Select Unit
                            </option>

                            @foreach($units as $unit)

                                <option
                                    value="{{ $unit->id }}"
                                    @selected(
                                        old('unit_id') == $unit->id
                                    )
                                >
                                    {{ $unit->name }} ({{ $unit->code }})
                                </option>

                            @endforeach

                        </select>

                        @error('unit_id')
                            <div class="text-danger mt-1">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================
             ITEM INFORMATION
             ======================================================== --}}
        <div class="requisition-section">

            <div class="requisition-section-header">

                <div class="requisition-section-heading">

                    <div class="requisition-section-icon">
                        <i class="fa fa-cube"></i>
                    </div>

                    <div>
                        <h5>Item Information</h5>

                        <p>
                            Enter the name and identification details of the store item.
                        </p>
                    </div>

                </div>

            </div>


            <div class="requisition-details-body">

                <div class="requisition-form-row">


                    {{-- ITEM NAME --}}
                    <div class="requisition-form-group requisition-form-group-wide">

                        <label
                            for="name"
                            class="requisition-field-label"
                        >
                            Item Name
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="name"
                            id="name"
                            class="requisition-input"
                            value="{{ old('name') }}"
                            placeholder="e.g. A4 Printing Paper"
                            maxlength="255"
                            required
                        >

                        @error('name')
                            <div class="text-danger mt-1">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- SKU --}}
                    <div class="requisition-form-group requisition-form-group-narrow">

                        <label
                            for="sku"
                            class="requisition-field-label"
                        >
                            SKU
                        </label>

                        <input
                            type="text"
                            name="sku"
                            id="sku"
                            class="requisition-input"
                            value="{{ old('sku') }}"
                            placeholder="e.g. PAP-A4-001"
                            maxlength="255"
                        >

                        <div class="requisition-field-help">
                            Optional internal item code.
                        </div>

                        @error('sku')
                            <div class="text-danger mt-1">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================
             STOCK SETTINGS
             ======================================================== --}}
        <div class="requisition-section">

            <div class="requisition-section-header">

                <div class="requisition-section-heading">

                    <div class="requisition-section-icon">
                        <i class="fa fa-sliders"></i>
                    </div>

                    <div>
                        <h5>Stock Settings</h5>

                        <p>
                            Define how this item should be handled and monitored in stock.
                        </p>
                    </div>

                </div>

            </div>


            <div class="requisition-details-body">

                <div class="requisition-form-row">


                    {{-- ITEM TYPE --}}
                    <div class="requisition-form-group requisition-form-group-half">

                        <label
                            for="item_type"
                            class="requisition-field-label"
                        >
                            Item Type
                            <span class="text-danger">*</span>
                        </label>

                        <select
                            name="item_type"
                            id="item_type"
                            class="requisition-input"
                            required
                        >

                            <option value="">
                                Select Type
                            </option>

                            <option
                                value="CONSUMABLE"
                                @selected(
                                    old('item_type', 'CONSUMABLE') === 'CONSUMABLE'
                                )
                            >
                                Consumable
                            </option>

                            <option
                                value="RETURNABLE"
                                @selected(
                                    old('item_type') === 'RETURNABLE'
                                )
                            >
                                Returnable
                            </option>

                            <option
                                value="ASSET"
                                @selected(
                                    old('item_type') === 'ASSET'
                                )
                            >
                                Asset
                            </option>

                        </select>

                        @error('item_type')
                            <div class="text-danger mt-1">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>


                    {{-- REORDER LEVEL --}}
                    <div class="requisition-form-group requisition-form-group-half">

                        <label
                            for="reorder_level"
                            class="requisition-field-label"
                        >
                            Reorder Level
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="number"
                            name="reorder_level"
                            id="reorder_level"
                            class="requisition-input"
                            value="{{ old('reorder_level', 0) }}"
                            min="0"
                            step="0.001"
                            required
                        >

                        <div class="requisition-field-help">
                            Alert level for future stock monitoring.
                        </div>

                        @error('reorder_level')
                            <div class="text-danger mt-1">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================
             DESCRIPTION
             ======================================================== --}}
        <div class="requisition-section">

            <div class="requisition-section-header">

                <div class="requisition-section-heading">

                    <div class="requisition-section-icon">
                        <i class="fa fa-align-left"></i>
                    </div>

                    <div>
                        <h5>Description</h5>

                        <p>
                            Add any additional information about this store item.
                        </p>
                    </div>

                </div>

            </div>


            <div class="requisition-details-body">

                <div class="requisition-form-group">

                    <label
                        for="description"
                        class="requisition-field-label"
                    >
                        Description
                    </label>

                    <textarea
                        name="description"
                        id="description"
                        class="requisition-input"
                        rows="5"
                        maxlength="2000"
                        placeholder="Optional description of this item"
                    >{{ old('description') }}</textarea>

                    @error('description')
                        <div class="text-danger mt-1">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>

        </div>


        {{-- ========================================================
             ACTIONS
             ======================================================== --}}
        <div class="requisition-bottom-actions">

            <a
                href="{{ route('admin.store-items.index') }}"
                class="requisition-cancel-button"
            >
                <i class="fa fa-times"></i>
                Cancel
            </a>

            <button
                type="submit"
                class="requisition-save-button"
            >
                <i class="fa fa-save"></i>
                Save Store Item
            </button>

        </div>

    </form>

</div>

@endsection