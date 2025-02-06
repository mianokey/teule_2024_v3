@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="card-title mb-0">Manage Permissions</h6>
        <a href="{{ route('admin.permissions.create') }}" class="btn btn-primary">Create New Permission</a>
    </div>
    <x-message></x-message>
    <div class="card-body">
        <div class="table-responsive p-4 col-md-12">
            <table id="datatable" class="table data-table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Permission Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($permissions as $permission)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $permission->name }}</td>
                            <td>
                                <a href="{{ route('admin.permissions.edit', $permission->id) }}" class="btn btn-sm btn-info">Edit</a>
                                <form action="{{ route('admin.permissions.destroy', $permission->id) }}" method="POST" style="display:inline;">
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
