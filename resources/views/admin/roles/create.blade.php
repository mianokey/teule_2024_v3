@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/stores.css') }}">
@endpush

@section('content')

<div class="requisition-page">

    {{-- PAGE HEADER --}}
    <div class="requisition-page-header">

        <div class="requisition-header-content">

            <div class="requisition-header-icon">
                <i class="fa fa-shield"></i>
            </div>

            <div>

                <div class="requisition-breadcrumb">
                    <span>Administration</span>
                    <i class="fa fa-angle-right"></i>
                    <span>Roles</span>
                    <i class="fa fa-angle-right"></i>
                    <span>Create</span>
                </div>

                <h1 class="requisition-page-title">
                    Create Role
                </h1>

                <p class="requisition-page-subtitle">
                    Create a role, then assign permissions to it.
                </p>

            </div>

        </div>

    </div>

    <x-message></x-message>

    <div class="requisition-card">

        <div class="requisition-card-header">

            <div>
                <div class="requisition-card-title">
                    Role Details
                </div>

                <div class="requisition-card-subtitle">
                    Enter the name of the new role.
                </div>
            </div>

        </div>

        <div class="requisition-details-body">

            <form method="POST"
                  action="{{ route('admin.roles.store') }}">

                @csrf

                <div class="row">

                    <div class="col-md-12">

                        <label class="requisition-field-label"
                               for="name">
                            Role Name
                        </label>

                        <input type="text"
                               name="name"
                               id="name"
                               class="requisition-input"
                               value="{{ old('name') }}"
                               placeholder="e.g. stores_officer"
                               required>

                    </div>

                </div>

                <div class="requisition-bottom-actions">

                    <div class="requisition-bottom-actions-left">

                        <a href="{{ route('admin.roles.index') }}"
                           class="requisition-cancel-button">
                            <i class="fa fa-arrow-left"></i>
                            Cancel
                        </a>

                    </div>

                    <div class="requisition-bottom-actions-right">

                        <button type="submit"
                                class="requisition-add-button">
                            <i class="fa fa-save"></i>
                            Create Role
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>

@endsection