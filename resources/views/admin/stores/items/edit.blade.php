@extends('layouts.admin')

@section('content')

<div class="card">

    <div class="card-header">
        <h6 class="card-title mb-0">
            Edit Store Item
        </h6>
    </div>

    <div class="card-body">

        <x-message></x-message>

        <form method="POST"
              action="{{ route('admin.store-items.update', $storeItem) }}">

            @csrf
            @method('PUT')

            <div class="row">

                {{-- Category --}}
                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Category <span class="text-danger">*</span>
                    </label>

                    <select name="category_id"
                            class="form-select @error('category_id') is-invalid @enderror"
                            required>

                        <option value="">Select Category</option>

                        @foreach($categories as $category)

                            <option value="{{ $category->id }}"
                                {{ old('category_id', $storeItem->category_id) == $category->id ? 'selected' : '' }}>

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


                {{-- Unit --}}
                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Unit <span class="text-danger">*</span>
                    </label>

                    <select name="unit_id"
                            class="form-select @error('unit_id') is-invalid @enderror"
                            required>

                        <option value="">Select Unit</option>

                        @foreach($units as $unit)

                            <option value="{{ $unit->id }}"
                                {{ old('unit_id', $storeItem->unit_id) == $unit->id ? 'selected' : '' }}>

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


                {{-- Item Name --}}
                <div class="col-md-8 mb-3">

                    <label class="form-label">
                        Item Name <span class="text-danger">*</span>
                    </label>

                    <input type="text"
                           name="name"
                           value="{{ old('name', $storeItem->name) }}"
                           class="form-control @error('name') is-invalid @enderror"
                           required>

                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- SKU --}}
                <div class="col-md-4 mb-3">

                    <label class="form-label">
                        SKU
                    </label>

                    <input type="text"
                           name="sku"
                           value="{{ old('sku', $storeItem->sku) }}"
                           class="form-control @error('sku') is-invalid @enderror">

                    @error('sku')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- Item Type --}}
                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Item Type <span class="text-danger">*</span>
                    </label>

                    <select name="item_type"
                            class="form-select @error('item_type') is-invalid @enderror"
                            required>

                        <option value="CONSUMABLE"
                            {{ old('item_type', $storeItem->item_type) === 'CONSUMABLE' ? 'selected' : '' }}>
                            Consumable
                        </option>

                        <option value="RETURNABLE"
                            {{ old('item_type', $storeItem->item_type) === 'RETURNABLE' ? 'selected' : '' }}>
                            Returnable
                        </option>

                        <option value="ASSET"
                            {{ old('item_type', $storeItem->item_type) === 'ASSET' ? 'selected' : '' }}>
                            Asset
                        </option>

                    </select>

                    @error('item_type')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- Reorder Level --}}
                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Reorder Level <span class="text-danger">*</span>
                    </label>

                    <input type="number"
                           name="reorder_level"
                           value="{{ old('reorder_level', $storeItem->reorder_level) }}"
                           class="form-control @error('reorder_level') is-invalid @enderror"
                           min="0"
                           step="0.001"
                           required>

                    @error('reorder_level')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- Description --}}
                <div class="col-12 mb-3">

                    <label class="form-label">
                        Description
                    </label>

                    <textarea name="description"
                              rows="4"
                              class="form-control @error('description') is-invalid @enderror">{{ old('description', $storeItem->description) }}</textarea>

                    @error('description')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>


            <div class="d-flex justify-content-between">

                <a href="{{ route('admin.store-items.show', $storeItem) }}"
                   class="btn btn-secondary">
                    Cancel
                </a>

                <button type="submit"
                        class="btn btn-primary">
                    Update Store Item
                </button>

            </div>

        </form>

    </div>

</div>

@endsection