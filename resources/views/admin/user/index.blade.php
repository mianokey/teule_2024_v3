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
                <i class="fa fa-users"></i>
            </div>

            <div>
                <div class="requisition-breadcrumb">
                    <span>Administration</span>
                    <span>/</span>
                    <span>Users</span>
                </div>

                <h1 class="requisition-page-title">
                    Users
                </h1>

                <p class="requisition-page-subtitle">
                    Manage system users, roles and access permissions.
                </p>
            </div>

        </div>

        {{-- PAGE ACTION --}}
        <div class="requisition-header-right">

            <a
                href="{{ route('admin.user.create') }}"
                class="requisition-add-button"
            >
                <i class="fa fa-plus"></i>
                Add User
            </a>

        </div>

    </div>


    {{-- =========================================================
         SUCCESS / ERROR MESSAGES
         ========================================================= --}}
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif


    {{-- =========================================================
         MAIN SECTION
         ========================================================= --}}
    <div class="requisition-section">

        {{-- SECTION HEADER --}}
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

            <div class="requisition-count-badge">
                {{ $users->count() }}
                {{ $users->count() === 1 ? 'User' : 'Users' }}
            </div>

        </div>


        {{-- =====================================================
             TABLE
             ===================================================== --}}
        <div class="requisition-list-table-wrapper">

            <table
                id="datatable"
                class="requisition-list-table data-table"
            >

                <thead>
                    <tr>

                        <th style="min-width: 170px;">
                            Name
                        </th>

                        <th style="min-width: 210px;">
                            Email
                        </th>

                        <th style="min-width: 140px;">
                            Position
                        </th>

                        <th style="min-width: 130px;">
                            Role
                        </th>

                        <th style="width: 90px;">
                            Image
                        </th>

                        <th style="width: 190px;">
                            Actions
                        </th>

                    </tr>
                </thead>

                <tbody>

                    @forelse($users as $user)

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

                            {{-- NAME --}}
                            <td>

                                <div class="requisition-list-number">
                                    {{ $user->name }}
                                </div>

                            </td>


                            {{-- EMAIL --}}
                            <td>

                                <div class="requisition-list-meta">
                                    {{ $user->email }}
                                </div>

                            </td>


                            {{-- POSITION --}}
                            <td>

                                @if($position)

                                    <span class="requisition-list-department">
                                        {{ $position }}
                                    </span>

                                @else

                                    <span class="requisition-list-meta">
                                        —
                                    </span>

                                @endif

                            </td>


                            {{-- ROLE --}}
                            <td>

                                @if($userRole)

                                    <span class="requisition-list-status requisition-list-status-approved">
                                        <span class="requisition-list-status-dot"></span>
                                        {{ $userRole->name }}
                                    </span>

                                @else

                                    <span class="requisition-list-status requisition-list-status-cancelled">
                                        <span class="requisition-list-status-dot"></span>
                                        No Role
                                    </span>

                                @endif

                            </td>


                            {{-- IMAGE --}}
                            <td>

                                @if($imgUrl)

                                    <button
                                        type="button"
                                        class="requisition-list-action primary toggle-img-btn"
                                        data-user-id="{{ $user->id }}"
                                        title="View image"
                                    >
                                        <i class="fa fa-eye"></i>
                                    </button>

                                    <div
                                        id="user-img-{{ $user->id }}"
                                        style="display:none;"
                                    >
                                        <img
                                            src="{{ asset($imgUrl) }}"
                                            height="80"
                                            class="rounded mt-2"
                                            alt="{{ $user->name }} image"
                                        >
                                    </div>

                                @else

                                    <span class="requisition-list-meta">
                                        —
                                    </span>

                                @endif

                            </td>


                            {{-- ACTIONS --}}
                            <td>

                                <div class="requisition-list-actions">

                                    {{-- VIEW DETAILS --}}
                                    <button
                                        type="button"
                                        class="requisition-list-action primary view-details-btn"
                                        data-name="{{ $user->name }}"
                                        data-email="{{ $user->email }}"
                                        data-details="{{ json_encode($user->details->pluck('value', 'key')) }}"
                                        title="View details"
                                    >
                                        <i class="fa fa-eye"></i>
                                    </button>


                                    {{-- ACCESS --}}
                                    <a
                                        href="{{ route(
                                            'admin.users.roles.edit',
                                            ['id' => $user->id]
                                        ) }}"
                                        class="requisition-list-action warning"
                                        title="Manage role and permissions"
                                    >
                                        <i class="fa fa-lock"></i>
                                    </a>


                                    {{-- EDIT --}}
                                    <a
                                        href="{{ route(
                                            'admin.user.edit',
                                            ['id' => $user->id]
                                        ) }}"
                                        class="requisition-list-action"
                                        title="Edit user"
                                    >
                                        <i class="fa fa-pen"></i>
                                    </a>


                                    {{-- DELETE --}}
                                    <form
                                        action="{{ route(
                                            'admin.user.delete',
                                            ['id' => $user->id]
                                        ) }}"
                                        method="POST"
                                        class="d-inline"
                                        onsubmit="return confirm(
                                            'Are you sure you want to delete this user?'
                                        );"
                                    >

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            class="requisition-list-action danger"
                                            title="Delete user"
                                        >
                                            <i class="fa fa-trash"></i>
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

                                    <h5>
                                        No users found
                                    </h5>

                                    <p>
                                        There are currently no users registered
                                        in the system.
                                    </p>

                                    <a
                                        href="{{ route('admin.user.create') }}"
                                        class="requisition-add-button"
                                    >
                                        <i class="fa fa-plus"></i>
                                        Add User
                                    </a>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


{{-- =============================================================
     USER DETAILS MODAL
     ============================================================= --}}
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

                {{-- BASIC DETAILS --}}
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


                {{-- ADDITIONAL DETAILS --}}
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


{{-- =============================================================
     JAVASCRIPT
     ============================================================= --}}
@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | IMAGE TOGGLE
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

                this.innerHTML = isHidden
                    ? '<i class="fa fa-eye-slash"></i>'
                    : '<i class="fa fa-eye"></i>';

                this.title = isHidden
                    ? 'Hide image'
                    : 'View image';

            });

        });


    /*
    |--------------------------------------------------------------------------
    | USER DETAILS MODAL
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


                document
                    .getElementById('modal-user-name')
                    .textContent = name;


                document
                    .getElementById('modal-user-email')
                    .textContent = email;


                const detailsContainer =
                    document.getElementById(
                        'modal-user-details'
                    );

                detailsContainer.innerHTML = '';


                /*
                |--------------------------------------------------------------------------
                | Do not show these in Additional Details
                |--------------------------------------------------------------------------
                */

                const excludedKeys = [
                    'position',
                    'img_url'
                ];


                let hasDetails = false;


                Object.keys(details)
                    .forEach(function (key) {

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
                                .replace(
                                    /\b\w/g,
                                    function (letter) {
                                        return letter.toUpperCase();
                                    }
                                );


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
                | No additional details
                |--------------------------------------------------------------------------
                */

                if (!hasDetails) {

                    detailsContainer.innerHTML = `
                        <div class="requisition-table-empty">

                            <div class="requisition-empty-icon">
                                <i class="fa fa-info-circle"></i>
                            </div>

                            <h5>
                                No additional details
                            </h5>

                            <p>
                                No additional information is available
                                for this user.
                            </p>

                        </div>
                    `;

                }


                /*
                |--------------------------------------------------------------------------
                | Show Bootstrap Modal
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