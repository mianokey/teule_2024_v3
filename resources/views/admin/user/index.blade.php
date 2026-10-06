@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/stores.css') }}">
@endpush

@section('content')

<div class="requisition-page">

    {{-- ============================================================
        PAGE HEADER
    ============================================================ --}}
    <div class="requisition-page-header">

        <div class="requisition-header-content">

            <div class="requisition-title-row">

                <div class="requisition-header-icon">
                    <i class="fa fa-users"></i>
                </div>

                <div>

                    <div class="requisition-breadcrumb">
                        Administration / Users
                    </div>

                    <h1 class="requisition-page-title">
                        Users
                    </h1>

                    <p class="requisition-page-subtitle">
                        Manage system users, roles and access permissions.
                    </p>

                </div>

            </div>


            <div class="requisition-header-right">

                <div class="requisition-page-actions">

                    <a
                        href="{{ route('admin.user.create') }}"
                        class="requisition-add-button"
                    >
                        <i class="fa fa-plus"></i>
                        Add User
                    </a>

                </div>

            </div>

        </div>

    </div>


    {{-- ============================================================
        MESSAGES
    ============================================================ --}}
    <x-message></x-message>


    {{-- ============================================================
        USERS LIST
    ============================================================ --}}
    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fa fa-users"></i>
                </div>

                <div>

                    <h5>
                        System Users
                    </h5>

                    <p>
                        Users registered in the Teule Kenya system.
                    </p>

                </div>

            </div>


            <span class="requisition-count-badge">
                {{ $users->count() }}
            </span>

        </div>


        <div class="requisition-list-table-wrapper">

            <table
                id="datatable"
                class="requisition-list-table data-table"
            >

                <thead>

                    <tr>

                        <th>Name</th>

                        <th>Email</th>

                        <th>Position</th>

                        <th>Role</th>

                        <th>Image</th>

                        <th>Actions</th>

                    </tr>

                </thead>


                <tbody>

                    @forelse ($users as $user)

                        @php

                            $position = optional(
                                $user->details
                                    ->where('key', 'position')
                                    ->first()
                            )->value;

                            $imgUrl = optional(
                                $user->details
                                    ->where('key', 'img_url')
                                    ->first()
                            )->value;

                            $userRole = $user->roles->first();

                        @endphp


                        <tr>

                            {{-- ====================================================
                                NAME
                            ==================================================== --}}
                            <td>

                                <div class="requisition-table-primary">

                                    {{ $user->name }}

                                </div>

                            </td>


                            {{-- ====================================================
                                EMAIL
                            ==================================================== --}}
                            <td>

                                <div class="requisition-table-secondary">

                                    {{ $user->email }}

                                </div>

                            </td>


                            {{-- ====================================================
                                POSITION
                            ==================================================== --}}
                            <td>

                                @if($position)

                                    <span class="requisition-status">
                                        {{ $position }}
                                    </span>

                                @else

                                    <span class="requisition-status">
                                        No position
                                    </span>

                                @endif

                            </td>


                            {{-- ====================================================
                                ROLE
                            ==================================================== --}}
                            <td>

                                @if($userRole)

                                    <span class="requisition-status primary">
                                        {{ $userRole->name }}
                                    </span>

                                @else

                                    <span class="requisition-status danger">
                                        No role
                                    </span>

                                @endif

                            </td>


                            {{-- ====================================================
                                IMAGE
                            ==================================================== --}}
                            <td>

                                @if($imgUrl)

                                    <div>

                                        <button
                                            type="button"
                                            class="requisition-add-button primary toggle-img-btn"
                                            data-user-id="{{ $user->id }}"
                                        >
                                            <i class="fa fa-eye"></i>
                                            View
                                        </button>


                                        <div
                                            id="user-img-{{ $user->id }}"
                                            style="display:none;"
                                        >

                                            <img
                                                src="{{ asset($imgUrl) }}"
                                                height="90"
                                                class="rounded mt-2"
                                                alt="{{ $user->name }} image"
                                            >

                                        </div>

                                    </div>

                                @else

                                    <span class="requisition-table-secondary">
                                        No image
                                    </span>

                                @endif

                            </td>


                            {{-- ====================================================
                                ACTIONS
                            ==================================================== --}}
                            <td>

                                <div class="requisition-list-actions">

                                    {{-- VIEW DETAILS --}}
                                    <button
                                        type="button"
                                        class="requisition-add-button primary view-details-btn"
                                        data-name="{{ $user->name }}"
                                        data-email="{{ $user->email }}"
                                        data-details="{{ json_encode($user->details->pluck('value', 'key')) }}"
                                        title="View Details"
                                    >
                                        <i class="fa fa-eye"></i>
                                        Details
                                    </button>


                                    {{-- MANAGE ROLE + DIRECT PERMISSIONS --}}
                                    <a
                                        href="{{ route('admin.users.roles.edit', ['id' => $user->id]) }}"
                                        class="requisition-add-button warning"
                                        title="Manage Role and Permissions"
                                    >
                                        <i class="fa fa-shield"></i>
                                        Access
                                    </a>


                                    {{-- EDIT USER --}}
                                    <a
                                        href="{{ route('admin.user.edit', ['id' => $user->id]) }}"
                                        class="requisition-add-button primary"
                                        title="Edit User"
                                    >
                                        <i class="fa fa-pencil"></i>
                                        Edit
                                    </a>


                                    {{-- DELETE USER --}}
                                    <form
                                        action="{{ route('admin.user.delete', ['id' => $user->id]) }}"
                                        method="POST"
                                        class="d-inline"
                                    >

                                        @csrf

                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="requisition-add-button danger"
                                            title="Delete User"
                                            onclick="return confirm('Are you sure you want to delete this user?')"
                                        >
                                            <i class="fa fa-trash"></i>
                                            Delete
                                        </button>

                                    </form>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6">

                                <div class="requisition-table-empty">

                                    <div class="requisition-empty-icon">
                                        <i class="fa fa-users"></i>
                                    </div>

                                    <strong>
                                        No users found
                                    </strong>

                                    <p>
                                        There are currently no users registered in the system.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


{{-- ================================================================
    USER DETAILS MODAL
================================================================ --}}
<div
    class="modal fade"
    id="userDetailsModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    User Details
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                ></button>

            </div>


            <div class="modal-body">

                <div class="requisition-details-grid">

                    <div class="requisition-detail-item">

                        <div class="requisition-detail-label">
                            Name
                        </div>

                        <div
                            class="requisition-detail-value"
                            id="modal-user-name"
                        ></div>

                    </div>


                    <div class="requisition-detail-item">

                        <div class="requisition-detail-label">
                            Email
                        </div>

                        <div
                            class="requisition-detail-value"
                            id="modal-user-email"
                        ></div>

                    </div>

                </div>


                <div class="requisition-section mt-3">

                    <div class="requisition-section-header">

                        <div class="requisition-section-heading">

                            <div class="requisition-section-icon">
                                <i class="fa fa-info-circle"></i>
                            </div>

                            <div>

                                <h5>
                                    Additional Details
                                </h5>

                                <p>
                                    Information associated with this user.
                                </p>

                            </div>

                        </div>

                    </div>


                    <div
                        id="modal-user-details"
                        class="requisition-details-grid"
                    ></div>

                </div>

            </div>

        </div>

    </div>

</div>


{{-- ================================================================
    JAVASCRIPT
================================================================ --}}
@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {


    /*
    |--------------------------------------------------------------------------
    | Toggle User Image
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll('.toggle-img-btn')
        .forEach(function (button) {

            button.addEventListener('click', function () {

                const userId = this.dataset.userId;

                const imageContainer =
                    document.getElementById(
                        'user-img-' + userId
                    );

                if (!imageContainer) {
                    return;
                }


                const isHidden =
                    imageContainer.style.display === 'none' ||
                    imageContainer.style.display === '';


                imageContainer.style.display =
                    isHidden ? 'block' : 'none';


                if (isHidden) {

                    this.innerHTML =
                        '<i class="fa fa-eye-slash"></i> Hide';

                } else {

                    this.innerHTML =
                        '<i class="fa fa-eye"></i> View';

                }

            });

        });


    /*
    |--------------------------------------------------------------------------
    | View User Details
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll('.view-details-btn')
        .forEach(function (button) {

            button.addEventListener('click', function () {

                const name =
                    this.dataset.name || '';

                const email =
                    this.dataset.email || '';


                let details = {};


                try {

                    details = JSON.parse(
                        this.dataset.details || '{}'
                    );

                } catch (error) {

                    details = {};

                }


                /*
                |--------------------------------------------------------------------------
                | Basic Details
                |--------------------------------------------------------------------------
                */

                document
                    .getElementById('modal-user-name')
                    .textContent = name;


                document
                    .getElementById('modal-user-email')
                    .textContent = email;


                /*
                |--------------------------------------------------------------------------
                | Additional Details
                |--------------------------------------------------------------------------
                */

                const detailsContainer =
                    document.getElementById(
                        'modal-user-details'
                    );


                detailsContainer.innerHTML = '';


                const excludedKeys = [
                    'position',
                    'img_url'
                ];


                let hasDetails = false;


                Object.keys(details).forEach(function (key) {

                    if (excludedKeys.includes(key)) {
                        return;
                    }


                    hasDetails = true;


                    const item =
                        document.createElement('div');

                    item.className =
                        'requisition-detail-item';


                    const label =
                        document.createElement('div');

                    label.className =
                        'requisition-detail-label';


                    label.textContent =
                        key
                            .replace(/_/g, ' ')
                            .replace(/\b\w/g, function (letter) {
                                return letter.toUpperCase();
                            });


                    const value =
                        document.createElement('div');

                    value.className =
                        'requisition-detail-value';


                    value.textContent =
                        details[key] || '-';


                    item.appendChild(label);

                    item.appendChild(value);

                    detailsContainer.appendChild(item);

                });


                /*
                |--------------------------------------------------------------------------
                | No Additional Details
                |--------------------------------------------------------------------------
                */

                if (!hasDetails) {

                    detailsContainer.innerHTML = `

                        <div class="requisition-table-empty">

                            <div class="requisition-empty-icon">
                                <i class="fa fa-info-circle"></i>
                            </div>

                            <strong>
                                No additional details
                            </strong>

                            <p>
                                No additional information is available for this user.
                            </p>

                        </div>

                    `;

                }


                /*
                |--------------------------------------------------------------------------
                | Show Modal
                |--------------------------------------------------------------------------
                */

                const modalElement =
                    document.getElementById(
                        'userDetailsModal'
                    );


                const modal =
                    new bootstrap.Modal(modalElement);


                modal.show();

            });

        });

});

</script>

@endpush

@endsection