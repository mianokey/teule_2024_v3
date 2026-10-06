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
                    <span>Create</span>
                </div>

                <h1 class="requisition-page-title">
                    Create Permission
                </h1>

                <p class="requisition-page-subtitle">
                    Create an individual capability that can be assigned to roles or users.
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
                    Use a clear, consistent permission name.
                </div>

            </div>

        </div>

        <div class="requisition-details-body">

            <form method="POST"
                  action="{{ route('admin.permissions.store') }}">

                @csrf

                <div class="row">

                    <div class="col-md-7">

                        <label class="requisition-field-label"
                               for="name">
                            Permission Name
                        </label>

                        <input type="text"
                               name="name"
                               id="name"
                               class="requisition-input"
                               value="{{ old('name') }}"
                               placeholder="e.g. approve pettycash"
                               required>

                    </div>

                    <div class="col-md-5">

                        <label class="requisition-field-label"
                               for="guard_name">
                            Guard
                        </label>

                        <input type="text"
                               name="guard_name"
                               id="guard_name"
                               class="requisition-input"
                               value="{{ old('guard_name', 'web') }}"
                               readonly>

                    </div>

                </div>

                <div class="row mt-3">

                    <div class="col-md-12">

                        <label class="requisition-field-label"
                               for="description">
                            Description
                        </label>

                        <textarea name="description"
                                  id="description"
                                  class="requisition-input"
                                  rows="3"
                                  placeholder="Explain what this permission allows the user to do.">{{ old('description') }}</textarea>

                    </div>

                </div>

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
                            Create Permission
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection