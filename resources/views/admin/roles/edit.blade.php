@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h6 class="card-title">Edit Role: {{ $role->name }}</h6>
    </div>
    <x-message></x-message>
    <div class="card-body">
        <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-12">
                    <div class="form-floating mb-3">
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $role->name) }}" placeholder="Role Name" required>
                        <label for="name">Role Name</label>
                    </div>
                </div>
            </div>

            <h6 class="mb-3">Permissions</h6>
            <div class="row">
                @foreach ($permissions as $permission)
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permissions[]" id="permission_{{ $permission->id }}" value="{{ $permission->id }}"
                                @if ($role->hasPermissionTo($permission->name)) checked @endif>
                            <label class="form-check-label" for="permission_{{ $permission->id }}">
                                {{ $permission->name }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary btn-block">Update Role</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
