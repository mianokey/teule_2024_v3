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

            <div class="requisition-header-icon">
                <i class="fa fa-shield"></i>
            </div>

            <div>

                <div class="requisition-breadcrumb">
                    <span>Administration</span>
                    <i class="fa fa-angle-right"></i>
                    <span>Roles</span>
                    <i class="fa fa-angle-right"></i>
                    <span>Edit</span>
                </div>

                <h1 class="requisition-page-title">
                    Edit Role
                </h1>

                <p class="requisition-page-subtitle">
                    Manage the role and the permissions inherited by its users.
                </p>

            </div>

        </div>

        <div class="requisition-page-actions">

            <a href="{{ route('admin.roles.index') }}"
               class="requisition-cancel-button">
                <i class="fa fa-arrow-left"></i>
                Back to Roles
            </a>

        </div>

    </div>

    <x-message></x-message>

    <form method="POST"
          action="{{ route('admin.roles.update', $role->id) }}">

        @csrf
        @method('PUT')

        {{-- ========================================================
             ROLE DETAILS
             ======================================================== --}}
        <div class="requisition-card">

            <div class="requisition-card-header">

                <div>
                    <div class="requisition-card-title">
                        Role Details
                    </div>

                    <div class="requisition-card-subtitle">
                        Basic information about this role.
                    </div>
                </div>

                @if($role->name === 'normal_user')

                    <span class="requisition-list-status requisition-status-approved">
                        Default User Role
                    </span>

                @endif

            </div>

            <div class="requisition-details-body">

                <div class="row">

                    <div class="col-md-12">

                        <label class="requisition-field-label"
                               for="name">
                            Role Name
                        </label>

                        <input type="text"
                               name="name"
                               id="name"
                               class="requisition-input disabled"
                               value="{{ old('name', $role->name) }}"
                               required disabled>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================
             PERMISSIONS
             ======================================================== --}}
        <div class="requisition-card">

            <div class="requisition-card-header">

                <div>

                    <div class="requisition-card-title">
                        Role Permissions
                    </div>

                    <div class="requisition-card-subtitle">
                        Users assigned this role inherit these permissions.
                    </div>

                </div>

                <div class="requisition-page-actions">

                    <button type="button"
                            class="requisition-cancel-button"
                            id="selectAllPermissions">
                        <i class="fa fa-check-square-o"></i>
                        Select All
                    </button>

                    <button type="button"
                            class="requisition-cancel-button"
                            id="deselectAllPermissions">
                        <i class="fa fa-square-o"></i>
                        Clear All
                    </button>

                </div>

            </div>

            <div class="requisition-list-table-wrapper">

                <table class="requisition-list-table">

                    <thead>

                        <tr>
                            <th style="width:50px;">Select</th>
                            <th>Permission</th>
                            <th>Description</th>
                            <th>Status</th>
                        </tr>

                    </thead>

                    <tbody>

                    @forelse($permissions as $permission)

                        @php
                            $hasPermission = $role->hasPermissionTo($permission->name);
                        @endphp

                        <tr class="permission-row">

                            <td>

                                <input type="checkbox"
                                       class="permission-checkbox"
                                       name="permissions[]"
                                       value="{{ $permission->id }}"
                                       {{ $hasPermission ? 'checked' : '' }}>

                            </td>

                            <td>

                                <strong>
                                    {{ $permission->name }}
                                </strong>

                            </td>

                            <td>

                                <span class="requisition-list-meta">
                                    {{ $permission->description ?? 'System permission' }}
                                </span>

                            </td>

                            <td>

                                <span class="requisition-list-status
                                    {{ $hasPermission
                                        ? 'requisition-status-approved'
                                        : 'requisition-status-draft' }}">

                                    {{ $hasPermission ? 'Enabled' : 'Disabled' }}

                                </span>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="4">

                                <div class="requisition-table-empty">

                                    <div class="requisition-empty-icon">
                                        <i class="fa fa-key"></i>
                                    </div>

                                    <h5>No permissions found</h5>

                                    <p>
                                        Create permissions before assigning them to roles.
                                    </p>

                                    <a href="{{ route('admin.permissions.create') }}"
                                       class="requisition-add-button">
                                        <i class="fa fa-plus"></i>
                                        Create Permission
                                    </a>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- ========================================================
             SAVE
             ======================================================== --}}
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
                    Update Role & Permissions
                </button>

            </div>

        </div>

    </form>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const checkboxes = document.querySelectorAll('.permission-checkbox');

    const selectAll = document.getElementById('selectAllPermissions');

    const deselectAll = document.getElementById('deselectAllPermissions');


    selectAll?.addEventListener('click', function () {

        checkboxes.forEach(function (checkbox) {
            checkbox.checked = true;
        });

        updatePermissionRows();

    });


    deselectAll?.addEventListener('click', function () {

        checkboxes.forEach(function (checkbox) {
            checkbox.checked = false;
        });

        updatePermissionRows();

    });


    checkboxes.forEach(function (checkbox) {

        checkbox.addEventListener('change', function () {
            updatePermissionRows();
        });

    });


    function updatePermissionRows() {

        checkboxes.forEach(function (checkbox) {

            const row = checkbox.closest('.permission-row');

            if (!row) {
                return;
            }

            const status = row.querySelector('.requisition-list-status');

            if (!status) {
                return;
            }

            if (checkbox.checked) {

                status.textContent = 'Enabled';

                status.classList.remove(
                    'requisition-status-draft'
                );

                status.classList.add(
                    'requisition-status-approved'
                );

            } else {

                status.textContent = 'Disabled';

                status.classList.remove(
                    'requisition-status-approved'
                );

                status.classList.add(
                    'requisition-status-draft'
                );

            }

        });

    }

});
</script>

@endsection