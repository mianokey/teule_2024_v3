@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="card-title mb-0">Manage Roles</h6>
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">Create New Role</a>
    </div>
    <x-message></x-message>
    <div class="card-body">
        <div class="table-responsive p-4 col-md-12">
            <table id="datatable" class="table data-table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Role Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $role)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $role->name }}</td>
                            <td>
                                <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-info">Edit</a>
                                <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
