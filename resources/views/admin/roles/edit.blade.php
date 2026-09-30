@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/stores.css') }}">
@endpush

@section('content')

<div class="card">

    {{-- HEADER --}}
    <div class="card-header">
        <h6 class="card-title mb-0">
            Edit Role: {{ $role->name }}
        </h6>
    </div>

    <x-message></x-message>

    <div class="card-body">

        <form
            action="{{ route('admin.roles.update', $role->id) }}"
            method="POST"
            id="rolePermissionsForm"
        >

            @csrf
            @method('PUT')


            {{-- =====================================================
                 ROLE NAME
                 ===================================================== --}}
            <div class="row">

                <div class="col-md-12">

                    <div class="form-floating mb-4">

                        <input
                            type="text"
                            name="name"
                            id="name"
                            class="form-control"
                            value="{{ old('name', $role->name) }}"
                            placeholder="Role Name"
                            required
                        >

                        <label for="name">
                            Role Name
                        </label>

                    </div>

                </div>

            </div>


            {{-- =====================================================
                 PERMISSIONS TOOLBAR
                 ===================================================== --}}
            <div class="app-toolbar">

                <div class="app-toolbar-left">

                    <span class="app-toolbar-title">
                        Permissions
                    </span>

                    <span
                        class="app-selection-count"
                        id="selectedPermissionCount"
                    >
                        0 selected
                    </span>

                </div>

                <div class="app-toolbar-actions">

                    <button
                        type="button"
                        class="btn btn-sm btn-outline-primary"
                        id="selectAllPermissions"
                    >
                        <i class="fa fa-check-square-o"></i>
                        Select All
                    </button>

                    <button
                        type="button"
                        class="btn btn-sm btn-outline-secondary"
                        id="deselectAllPermissions"
                    >
                        <i class="fa fa-square-o"></i>
                        Deselect All
                    </button>

                </div>

            </div>


            {{-- =====================================================
                 PERMISSIONS LIST
                 ===================================================== --}}
            <div class="app-list">

                @forelse ($permissions as $permission)

                    @php
                        $hasPermission = $role->hasPermissionTo($permission->name);
                    @endphp

                    <label
                        class="app-list-item {{ $hasPermission ? 'is-selected' : '' }}"
                        for="permission_{{ $permission->id }}"
                    >

                        {{-- CHECKBOX --}}
                        <input
                            type="checkbox"
                            class="permission-checkbox permission-input"
                            name="permissions[]"
                            id="permission_{{ $permission->id }}"
                            value="{{ $permission->id }}"
                            {{ $hasPermission ? 'checked' : '' }}
                        >


                        {{-- PERMISSION DETAILS --}}
                        <span class="app-list-item-content">

                            <span class="app-list-item-title">
                                {{ $permission->name }}
                            </span>

                            <span class="app-list-item-meta">
                                Users assigned to this role will have this permission.
                            </span>

                        </span>


                        {{-- STATUS --}}
                        <span
                            class="app-list-item-status
                                {{ $hasPermission ? 'is-active' : 'is-inactive' }}"
                        >
                            {{ $hasPermission ? 'Enabled' : 'Disabled' }}
                        </span>

                    </label>

                @empty

                    <div class="alert alert-warning">

                        <i class="fa fa-exclamation-triangle"></i>

                        No permissions have been created yet.

                    </div>

                @endforelse

            </div>


            {{-- =====================================================
                 FORM ACTIONS
                 ===================================================== --}}
            <div class="permissions-footer">

                <div class="permissions-footer-inner">

                    <a
                        href="{{ route('admin.roles.index') }}"
                        class="btn btn-secondary"
                    >
                        <i class="fa fa-arrow-left"></i>
                        Back to Roles
                    </a>

                    <button
                        type="submit"
                        class="btn btn-primary"
                    >
                        <i class="fa fa-save"></i>
                        Update Role
                    </button>

                </div>

            </div>

        </form>

    </div>

</div>


{{-- =============================================================
     PERMISSION SELECTION JAVASCRIPT
     ============================================================= --}}
<script>

document.addEventListener('DOMContentLoaded', function () {

    const checkboxes =
        document.querySelectorAll('.permission-input');

    const selectedCount =
        document.getElementById('selectedPermissionCount');

    const selectAllButton =
        document.getElementById('selectAllPermissions');

    const deselectAllButton =
        document.getElementById('deselectAllPermissions');


    /*
     * Update selected count and visual state.
     */
    function updatePermissionUI() {

        let selected = 0;

        checkboxes.forEach(function (checkbox) {

            const row =
                checkbox.closest('.app-list-item');

            const status =
                row.querySelector('.app-list-item-status');


            if (checkbox.checked) {

                selected++;

                row.classList.add('is-selected');

                status.textContent = 'Enabled';

                status.classList.remove('is-inactive');

                status.classList.add('is-active');

            } else {

                row.classList.remove('is-selected');

                status.textContent = 'Disabled';

                status.classList.remove('is-active');

                status.classList.add('is-inactive');

            }

        });


        selectedCount.textContent =
            selected + (selected === 1 ? ' selected' : ' selected');
    }


    /*
     * Individual permission selection.
     */
    checkboxes.forEach(function (checkbox) {

        checkbox.addEventListener('change', function () {

            updatePermissionUI();

        });

    });


    /*
     * Select all.
     */
    selectAllButton.addEventListener('click', function () {

        checkboxes.forEach(function (checkbox) {

            checkbox.checked = true;

        });

        updatePermissionUI();

    });


    /*
     * Deselect all.
     */
    deselectAllButton.addEventListener('click', function () {

        checkboxes.forEach(function (checkbox) {

            checkbox.checked = false;

        });

        updatePermissionUI();

    });


    /*
     * Initialise.
     */
    updatePermissionUI();

});

</script>

@endsection