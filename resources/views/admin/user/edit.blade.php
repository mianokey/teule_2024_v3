@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/stores.css') }}">
@endpush

@section('content')

<div class="store-requisition-page">

    {{-- =========================================================
         PAGE HEADER
         ========================================================= --}}
    <div class="requisition-page-header">

        <div class="requisition-header-content">

            <div class="requisition-header-icon">
                <i class="fa fa-user-edit"></i>
            </div>

            <div>

                <div class="requisition-breadcrumb">
                    <span>Administration</span>
                    <span>/</span>
                    <span>Users</span>
                    <span>/</span>
                    <span>Edit</span>
                </div>

                <h1 class="requisition-page-title">
                    Edit User
                </h1>

                <p class="requisition-page-subtitle">
                    Update {{ $user->name }}'s account and staff information.
                </p>

            </div>

        </div>

        <div class="requisition-header-right">

            <a
                href="{{ route('admin.user.index') }}"
                class="requisition-cancel-button"
            >
                <i class="fa fa-arrow-left"></i>
                Back to Users
            </a>

        </div>

    </div>


    {{-- =========================================================
         MESSAGES
         ========================================================= --}}
    <x-message></x-message>


    {{-- =========================================================
         VALIDATION ERRORS
         ========================================================= --}}
    @if($errors->any())

        <div class="alert alert-danger">

            <strong>
                Please correct the following errors:
            </strong>

            <ul class="mb-0 mt-2">

                @foreach($errors->all() as $error)

                    <li>
                        {{ $error }}
                    </li>

                @endforeach

            </ul>

        </div>

    @endif


    {{-- =========================================================
         USER FORM
         ========================================================= --}}
    <form
        method="POST"
        action="{{ route('admin.user.update', $user->id) }}"
        enctype="multipart/form-data"
    >

        @method('PATCH')
        @csrf


        {{-- =====================================================
             ACCOUNT INFORMATION
             ===================================================== --}}
        <div class="requisition-section">

            <div class="requisition-section-header">

                <div class="requisition-section-heading">

                    <div class="requisition-section-icon">
                        <i class="fa fa-user"></i>
                    </div>

                    <div>

                        <h5>
                            Account Information
                        </h5>

                        <p>
                            Update the user's basic account information.
                        </p>

                    </div>

                </div>

            </div>


            <div class="requisition-details-body">

                <div class="row g-3">

                    {{-- NAME --}}
                    <div class="col-lg-6">

                        <label
                            for="name"
                            class="requisition-field-label"
                        >
                            Full Name
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            class="requisition-input"
                            placeholder="Enter user's full name"
                            required
                        >

                    </div>


                    {{-- EMAIL --}}
                    <div class="col-lg-6">

                        <label
                            for="email"
                            class="requisition-field-label"
                        >
                            Email Address
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            class="requisition-input"
                            placeholder="Enter user's email address"
                            required
                        >

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
             STAFF INFORMATION
             ===================================================== --}}
        <div class="requisition-section mt-3">

            <div class="requisition-section-header">

                <div class="requisition-section-heading">

                    <div class="requisition-section-icon">
                        <i class="fa fa-id-badge"></i>
                    </div>

                    <div>

                        <h5>
                            Staff Information
                        </h5>

                        <p>
                            Update the user's position and system role.
                        </p>

                    </div>

                </div>

            </div>


            <div class="requisition-details-body">

                <div class="row g-3">

                    {{-- POSITION --}}
                    <div class="col-lg-6">

                        <label
                            for="position"
                            class="requisition-field-label"
                        >
                            Position
                        </label>

                        <input
                            type="text"
                            id="position"
                            name="position"
                            value="{{ old(
                                'position',
                                $details['position'] ?? ''
                            ) }}"
                            class="requisition-input"
                            placeholder="e.g. Resource Mobilisation Officer"
                        >

                    </div>


                    {{-- ROLE --}}
                    <div class="col-lg-6">

                        <label
                            for="role"
                            class="requisition-field-label"
                        >
                            System Role
                        </label>

                        <select
                            id="role"
                            name="role"
                            class="requisition-input"
                        >

                            <option value="">
                                Keep Current Role
                            </option>

                            @foreach($roles as $role)

                                <option
                                    value="{{ $role->name }}"
                                    @selected(
                                        old(
                                            'role',
                                            optional($user->roles->first())->name
                                        ) === $role->name
                                    )
                                >
                                    {{ $role->name }}
                                </option>

                            @endforeach

                        </select>

                        <small class="text-muted">
                            Use the Access page to manage direct permissions.
                        </small>

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
             PROFILE IMAGE
             ===================================================== --}}
        @php
            $imgUrl = $details['img_url'] ?? null;
        @endphp

        <div class="requisition-section mt-3">

            <div class="requisition-section-header">

                <div class="requisition-section-heading">

                    <div class="requisition-section-icon">
                        <i class="fa fa-image"></i>
                    </div>

                    <div>

                        <h5>
                            Profile Image
                        </h5>

                        <p>
                            View the current image or upload a replacement.
                        </p>

                    </div>

                </div>

            </div>


            <div class="requisition-details-body">

                <div class="row g-3 align-items-center">

                    {{-- CURRENT IMAGE --}}
                    <div class="col-lg-4">

                        <label class="requisition-field-label">
                            Current Image
                        </label>

                        @if($imgUrl)

                            <div>

                                <img
                                    src="{{ asset($imgUrl) }}"
                                    height="100"
                                    class="rounded"
                                    alt="{{ $user->name }} image"
                                >

                            </div>

                        @else

                            <div class="requisition-table-empty">

                                <div class="requisition-empty-icon">
                                    <i class="fa fa-user"></i>
                                </div>

                                <strong>
                                    No image
                                </strong>

                            </div>

                        @endif

                    </div>


                    {{-- NEW IMAGE --}}
                    <div class="col-lg-8">

                        <label
                            for="image"
                            class="requisition-field-label"
                        >
                            Upload New Image
                        </label>

                        <input
                            type="file"
                            id="image"
                            name="image"
                            class="requisition-input"
                            accept="image/jpeg,image/png,image/jpg,image/webp"
                        >

                        <small class="text-muted">
                            Leave this empty to keep the current image.
                        </small>

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
             CHANGE PASSWORD
             ===================================================== --}}
        <div class="requisition-section mt-3">

            <div class="requisition-section-header">

                <div class="requisition-section-heading">

                    <div class="requisition-section-icon">
                        <i class="fa fa-lock"></i>
                    </div>

                    <div>

                        <h5>
                            Change Password
                        </h5>

                        <p>
                            Leave these fields empty if the password should remain unchanged.
                        </p>

                    </div>

                </div>

            </div>


            <div class="requisition-details-body">

                <div class="row g-3">


                    {{-- NEW PASSWORD --}}
                    <div class="col-lg-6">

                        <label
                            for="password"
                            class="requisition-field-label"
                        >
                            New Password
                        </label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="requisition-input"
                            placeholder="Enter new password"
                            autocomplete="new-password"
                        >

                    </div>


                    {{-- CONFIRM NEW PASSWORD --}}
                    <div class="col-lg-6">

                        <label
                            for="password_confirmation"
                            class="requisition-field-label"
                        >
                            Confirm New Password
                        </label>

                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="requisition-input"
                            placeholder="Confirm new password"
                            autocomplete="new-password"
                        >

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
             FORM ACTIONS
             ===================================================== --}}
        <div class="requisition-bottom-actions">

            <a
                href="{{ route('admin.user.index') }}"
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
                Save Changes
            </button>

        </div>

    </form>

</div>

@endsection