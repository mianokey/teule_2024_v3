@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/stores.css') }}">
@endpush

@section('content')

<div class="requisition-page">

    <div class="requisition-page-header">

        <div class="requisition-header-content">

            <div class="requisition-header-icon">
                <i class="fa fa-key"></i>
            </div>

            <div>

                <div class="requisition-breadcrumb">
                    <span>Administration</span>
                    <i class="fa fa-angle-right"></i>
                    <span>Permissions</span>
                    <i class="fa fa-angle-right"></i>
                    <span>Edit</span>
                </div>

                <h1 class="requisition-page-title">
                    Edit Permission
                </h1>

                <p class="requisition-page-subtitle">
                    Update this system permission.
                </p>

            </div>

        </div>

    </div>

    <x-message></x-message>

    <div class="requisition-card">

        <div class="requisition-card-header">

            <div>

                <div class="requisition-card-title">
                    Permission Details
                </div>

                <div class="requisition-card-subtitle">
                    Changes affect every role or user using this permission.
                </div>

            </div>

        </div>

        <div class="requisition-details-body">

            <form method="POST"
                  action="{{ route('admin.permissions.update', $permission->id) }}">

                @csrf
                @method('PUT')

                <div class="row">

                    <div class="col-md-8">

                        <label class="requisition-field-label"
                               for="name">
                            Permission Name
                        </label>

                        <input type="text"
                               name="name"
                               id="name"
                               class="requisition-input"
                               value="{{ old('name', $permission->name) }}"
                               required>

                    </div>

                    <div class="col-md-4">

                        <label class="requisition-field-label">
                            Guard
                        </label>

                        <input type="text"
                               class="requisition-input"
                               value="{{ $permission->guard_name }}"
                               readonly>

                    </div>

                </div>

                @if(\Schema::hasColumn('permissions', 'description'))

                    <div class="row mt-3">

                        <div class="col-md-12">

                            <label class="requisition-field-label"
                                   for="description">
                                Description
                            </label>

                            <textarea name="description"
                                      id="description"
                                      class="requisition-input"
                                      rows="3">{{ old('description', $permission->description) }}</textarea>

                        </div>

                    </div>

                @endif

                <div class="requisition-bottom-actions">

                    <div class="requisition-bottom-actions-left">

                        <a href="{{ route('admin.permissions.index') }}"
                           class="requisition-cancel-button">
                            <i class="fa fa-arrow-left"></i>
                            Cancel
                        </a>

                    </div>

                    <div class="requisition-bottom-actions-right">

                        <button type="submit"
                                class="requisition-add-button">
                            <i class="fa fa-save"></i>
                            Update Permission
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection