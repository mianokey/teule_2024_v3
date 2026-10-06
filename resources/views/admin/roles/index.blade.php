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
                    <span>Access Control</span>
                </div>

                <h1 class="requisition-page-title">
                    Roles
                </h1>

                <p class="requisition-page-subtitle">
                    Manage user roles and the permissions assigned to each role.
                </p>
            </div>

        </div>

        <div class="requisition-page-actions">
            <a href="{{ route('admin.roles.create') }}"
               class="requisition-add-button">
                <i class="fa fa-plus"></i>
                Create Role
            </a>
        </div>

    </div>

    <x-message></x-message>

    {{-- ============================================================
         ROLES
         ============================================================ --}}
    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fa fa-shield"></i>
                </div>

                <div>
                    <h5>System Roles</h5>
                    <p>
                        Roles determine the permissions available to users.
                    </p>
                </div>

            </div>

            <span class="requisition-count-badge">
                {{ $roles->count() }} roles
            </span>

        </div>

        <div class="requisition-list-table-wrapper">

            <table class="requisition-list-table">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Role</th>
                        <th>Permissions</th>
                        <th>Users</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                @forelse($roles as $role)

                    <tr>

                        <td>
                            <span class="requisition-list-row-number">
                                {{ $loop->iteration }}
                            </span>
                        </td>

                        <td>
                            <div class="requisition-list-number">
                                {{ $role->name }}
                            </div>

                            <div class="requisition-list-meta">
                                Guard: {{ $role->guard_name }}
                            </div>
                        </td>

                        <td>
                            <span class="requisition-list-item-count">
                                {{ $role->permissions_count ?? $role->permissions->count() }}
                                permissions
                            </span>
                        </td>

                        <td>
                            <span class="requisition-list-item-count">
                                {{ $role->users_count ?? 0 }}
                                users
                            </span>
                        </td>

                        <td>
                            <span class="requisition-list-date">
                                {{ $role->created_at?->format('d M Y') }}
                            </span>
                        </td>

                        <td>

                            <div class="requisition-list-actions">

                                <a href="{{ route('admin.roles.edit', $role->id) }}"
                                   class="requisition-list-action primary"
                                   title="Edit role">
                                    <i class="fa fa-edit"></i>
                                </a>

                                @if($role->name !== 'normal_user')

                                    <form method="POST"
                                          action="{{ route('admin.roles.destroy', $role->id) }}"
                                          onsubmit="return confirm('Delete this role?');"
                                          style="display:inline;">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="requisition-list-action danger"
                                                title="Delete role">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>

                                @endif

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="6">

                            <div class="requisition-table-empty">

                                <div class="requisition-empty-icon">
                                    <i class="fa fa-shield"></i>
                                </div>

                                <h5>No roles found</h5>

                                <p>
                                    Create your first system role.
                                </p>

                                <a href="{{ route('admin.roles.create') }}"
                                   class="requisition-add-button">
                                    <i class="fa fa-plus"></i>
                                    Create Role
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

@endsection