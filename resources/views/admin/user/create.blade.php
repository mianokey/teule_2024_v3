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
                <i class="fa fa-user-plus"></i>
            </div>

            <div>

                <div class="requisition-breadcrumb">
                    <span>Administration</span>
                    <span>/</span>
                    <span>Users</span>
                    <span>/</span>
                    <span>Create</span>
                </div>

                <h1 class="requisition-page-title">
                    Add New User
                </h1>

                <p class="requisition-page-subtitle">
                    Create a system user and assign their initial role.
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
        action="{{ route('admin.user.store') }}"
        enctype="multipart/form-data"
    >

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
                            Basic information used to identify and access the account.
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
                            value="{{ old('name') }}"
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
                            value="{{ old('email') }}"
                            class="requisition-input"
                            placeholder="Enter user's email address"
                            required
                        >

                    </div>


                    {{-- PASSWORD --}}
                    <div class="col-lg-6">

                        <label
                            for="password"
                            class="requisition-field-label"
                        >
                            Password
                        </label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="requisition-input"
                            placeholder="Enter password"
                            required
                        >

                    </div>


                    {{-- CONFIRM PASSWORD --}}
                    <div class="col-lg-6">

                        <label
                            for="password_confirmation"
                            class="requisition-field-label"
                        >
                            Confirm Password
                        </label>

                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="requisition-input"
                            placeholder="Confirm password"
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
                            Additional information about the user's role within Teule Kenya.
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
                            value="{{ old('position') }}"
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
                                Select Role
                            </option>

                            @foreach($roles as $role)

                                <option
                                    value="{{ $role->name }}"
                                    @selected(
                                        old('role') === $role->name ||
                                        (
                                            !old('role') &&
                                            $role->name === 'normal_user'
                                        )
                                    )
                                >
                                    {{ $role->name }}
                                </option>

                            @endforeach

                        </select>

                        <small class="text-muted">
                            New users normally start with the
                            <strong>normal_user</strong> role.
                        </small>

                    </div>

                </div>

            </div>

        </div>


        {{-- =====================================================
             PROFILE IMAGE
             ===================================================== --}}
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
                            Upload an optional profile image for this user.
                        </p>

                    </div>

                </div>

            </div>


            <div class="requisition-details-body">

                <div class="row g-3">

                    <div class="col-lg-6">

                        <label
                            for="image"
                            class="requisition-field-label"
                        >
                            Upload Image
                        </label>

                        <input
                            type="file"
                            id="image"
                            name="image"
                            class="requisition-input"
                            accept="image/jpeg,image/png,image/jpg,image/webp"
                        >

                        <small class="text-muted">
                            Recommended formats: JPG, JPEG, PNG or WEBP.
                        </small>

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
                Create User
            </button>

        </div>

    </form>

</div>

@endsection
```

### One important controller point

Your existing `user_create()` currently needs to provide `$roles`. If it doesn't already, change it to:

```php
public function user_create()
{
    $roles = Role::where('guard_name', 'web')
        ->orderBy('name')
        ->get();

    return view('admin.user.create', compact('roles'));
}