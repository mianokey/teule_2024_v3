@extends('layouts.admin')

@section('content')

<div class="card">

    <div class="card-header">
        <h6 class="card-title mb-0">
            Edit Variant — {{ $storeItem->name }}
        </h6>
    </div>

    <div class="card-body">

        <x-message></x-message>

        <form method="POST"
              action="{{ route('admin.store-item-variants.update', [$storeItem, $variant]) }}">

            @csrf
            @method('PUT')

            <div class="row">

                <div class="col-md-8 mb-3">

                    <label class="form-label">
                        Variant Name <span class="text-danger">*</span>
                    </label>

                    <input type="text"
                           name="name"
                           value="{{ old('name', $variant->name) }}"
                           class="form-control @error('name') is-invalid @enderror"
                           required>

                    @error('name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                <div class="col-md-4 mb-3">

                    <label class="form-label">
                        Variant Code
                    </label>

                    <input type="text"
                           name="code"
                           value="{{ old('code', $variant->code) }}"
                           class="form-control @error('code') is-invalid @enderror">

                    @error('code')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

                <div class="col-12 mb-3">

                    <label class="form-label">
                        Description
                    </label>

                    <textarea name="description"
                              rows="4"
                              class="form-control @error('description') is-invalid @enderror">{{ old('description', $variant->description) }}</textarea>

                    @error('description')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>

            <div class="d-flex justify-content-between">

                <a href="{{ route('admin.store-item-variants.index', $storeItem) }}"
                   class="btn btn-secondary">
                    Cancel
                </a>

                <button type="submit"
                        class="btn btn-primary">
                    Update Variant
                </button>

            </div>

        </form>

    </div>

</div>

@endsection