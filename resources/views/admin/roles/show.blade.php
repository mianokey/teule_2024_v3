<!-- resources/views/admin/roles_permissions/show.blade.php -->

@extends('layouts.admin')

@section('content')
    <div class="container">
        <h2>Role Details</h2>

        <div class="card">
            <div class="card-header">
                <h5>{{ $role->name }}</h5>
            </div>
            <div class="card-body">
                <p><strong>Role Name:</strong> {{ $role->name }}</p>
                <p><strong>Created At:</strong> {{ $role->created_at }}</p>
                <p><strong>Updated At:</strong> {{ $role->updated_at }}</p>

                <!-- List permissions for this role -->
                <h5>Permissions:</h5>
                <ul>
                    @foreach($role->permissions as $permission)
                        <li>{{ $permission->name }}</li>
                    @endforeach
                </ul>

                <a href="{{ route('admin.roles.index') }}" class="btn btn-primary">Back to Roles</a>
            </div>
        </div>
    </div>
@endsection
