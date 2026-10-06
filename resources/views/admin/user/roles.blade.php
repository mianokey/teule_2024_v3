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
                    <i class="fa fa-shield"></i>
                </div>

                <div>
                    <div class="requisition-breadcrumb">
                        Administration / Users / Access
                    </div>

                    <h1 class="requisition-page-title">
                        User Access
                    </h1>

                    <p class="requisition-page-subtitle">
                        Manage this user's role and direct permissions.
                    </p>
                </div>

            </div>

            <div class="requisition-header-right">
                <div class="requisition-page-actions">

                    <a href="{{ route('admin.user.index') }}"
                       class="requisition-cancel-button">
                        <i class="fa fa-arrow-left"></i>
                        Back to Users
                    </a>

                </div>
            </div>

        </div>
    </div>

    <x-message></x-message>


    {{-- ============================================================
         USER INFORMATION
         ============================================================ --}}
    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fa fa-user"></i>
                </div>

                <div>
                    <h5>User Information</h5>
                    <p>
                        The account whose access is being managed.
                    </p>
                </div>

            </div>

        </div>

        <div class="requisition-details-body">

            <div class="requisition-details-grid">

                <div class="requisition-detail-item">
                    <div class="requisition-detail-label">
                        Name
                    </div>

                    <div class="requisition-detail-value">
                        {{ $user->name }}
                    </div>
                </div>

                <div class="requisition-detail-item">
                    <div class="requisition-detail-label">
                        Email
                    </div>

                    <div class="requisition-detail-value">
                        {{ $user->email }}
                    </div>
                </div>

                <div class="requisition-detail-item">
                    <div class="requisition-detail-label">
                        Current Role
                    </div>

                    <div class="requisition-detail-value">
                        @if($user->roles->first())
                            {{ $user->roles->first()->name }}
                        @else
                            No role assigned
                        @endif
                    </div>
                </div>

            </div>

        </div>

    </div>


    {{-- ============================================================
         ROLE + PERMISSIONS FORM
         ============================================================ --}}
    <form method="POST"
          action="{{ route('admin.users.roles.update', $user->id) }}">

        @csrf
        @method('PUT')


        {{-- ========================================================
             ROLE
             ======================================================== --}}
        <div class="requisition-section">

            <div class="requisition-section-header">

                <div class="requisition-section-heading">

                    <div class="requisition-section-icon">
                        <i class="fa fa-shield"></i>
                    </div>

                    <div>
                        <h5>User Role</h5>
                        <p>
                            Select the role assigned to this user.
                        </p>
                    </div>

                </div>

            </div>

            <div class="requisition-details-body">

                <div class="requisition-field">

                    <label for="role" class="requisition-field-label">
                        Role
                    </label>

                    <select name="role"
                            id="role"
                            class="requisition-input"
                            required>

                        <option value="">
                            Select Role
                        </option>

                        @foreach($roles as $role)

                            <option value="{{ $role->name }}"
                                {{ $user->hasRole($role->name) ? 'selected' : '' }}>

                                {{ $role->name }}

                            </option>

                        @endforeach

                    </select>

                    @error('role')
                        <div class="text-danger mt-1">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>

        </div>


        {{-- ========================================================
             DIRECT PERMISSIONS
             ======================================================== --}}
        <div class="requisition-section">

            <div class="requisition-section-header">

                <div class="requisition-section-heading">

                    <div class="requisition-section-icon">
                        <i class="fa fa-key"></i>
                    </div>

                    <div>
                        <h5>Direct User Permissions</h5>
                        <p>
                            These permissions are assigned directly to this
                            user and are separate from permissions inherited
                            through their role.
                        </p>
                    </div>

                </div>

                <span class="requisition-count-badge">
                    {{ $permissions->count() }}
                </span>

            </div>


            <div class="requisition-list-table-wrapper">

                <table class="requisition-list-table">

                    <thead>
                        <tr>
                            <th style="width: 70px;">Select</th>
                            <th>Permission</th>
                            <th>Description</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse($permissions as $permission)

                            <tr>

                                <td>
                                    <input type="checkbox"
                                           name="permissions[]"
                                           value="{{ $permission->id }}"
                                           {{ in_array($permission->id, $directPermissionIds) ? 'checked' : '' }}>
                                </td>

                                <td>
                                    {{ $permission->name }}
                                </td>

                                <td>
                                    <span class="requisition-table-secondary">
                                        {{ $permission->name }}
                                    </span>
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="3">

                                    <div class="requisition-table-empty">

                                        <div class="requisition-empty-icon">
                                            <i class="fa fa-key"></i>
                                        </div>

                                        <strong>
                                            No permissions found
                                        </strong>

                                        <p>
                                            There are currently no permissions
                                            available for assignment.
                                        </p>

                                    </div>

                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

        </div>


        {{-- ========================================================
             SAVE / CANCEL
             ======================================================== --}}
        <div class="requisition-bottom-actions">

            <a href="{{ route('admin.user.index') }}"
               class="requisition-cancel-button">

                <i class="fa fa-times"></i>
                Cancel

            </a>

            <button type="submit"
                    class="requisition-save-button">

                <i class="fa fa-save"></i>
                Save Access

            </button>

        </div>

    </form>

</div>

@endsection