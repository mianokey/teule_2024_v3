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
                    <span>Access Control</span>
                </div>

                <h1 class="requisition-page-title">
                    Permissions
                </h1>

                <p class="requisition-page-subtitle">
                    Manage individual permissions available to the system.
                </p>

            </div>

        </div>

        <div class="requisition-page-actions">

            <a href="{{ route('admin.permissions.create') }}"
               class="requisition-add-button">
                <i class="fa fa-plus"></i>
                Create Permission
            </a>

        </div>

    </div>

    <x-message></x-message>

    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fa fa-key"></i>
                </div>

                <div>

                    <h5>System Permissions</h5>

                    <p>
                        Permissions are assigned to roles or directly to users.
                    </p>

                </div>

            </div>

            <span class="requisition-count-badge">
                {{ $permissions->count() }} permissions
            </span>

        </div>

        <div class="requisition-list-table-wrapper">

            <table class="requisition-list-table">

                <thead>
                    <tr>
                        <th>#</th>
                        <th>Permission</th>
                        <th>Guard</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>

                @forelse($permissions as $permission)

                    <tr>

                        <td>
                            <span class="requisition-list-row-number">
                                {{ $loop->iteration }}
                            </span>
                        </td>

                        <td>

                            <div class="requisition-list-number">
                                {{ $permission->name }}
                            </div>

                            <div class="requisition-list-meta">
                                {{ $permission->description ?? 'System permission' }}
                            </div>

                        </td>

                        <td>
                            <span class="requisition-list-item-count">
                                {{ $permission->guard_name }}
                            </span>
                        </td>

                        <td>
                            <span class="requisition-list-date">
                                {{ $permission->created_at?->format('d M Y') }}
                            </span>
                        </td>

                        <td>

                            <div class="requisition-list-actions">

                                <a href="{{ route('admin.permissions.edit', $permission->id) }}"
                                   class="requisition-list-action primary"
                                   title="Edit permission">
                                    <i class="fa fa-pencil"></i>
                                </a>

                                <form method="POST"
                                      action="{{ route('admin.permissions.destroy', $permission->id) }}"
                                      onsubmit="return confirm('Delete this permission?');"
                                      style="display:inline;">

                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="requisition-list-action danger"
                                            title="Delete permission">
                                        <i class="fa fa-trash"></i>
                                    </button>

                                </form>

                            </div>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="5">

                            <div class="requisition-table-empty">

                                <div class="requisition-empty-icon">
                                    <i class="fa fa-key"></i>
                                </div>

                                <h5>No permissions found</h5>

                                <p>
                                    Create the first system permission.
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

</div>

@endsection