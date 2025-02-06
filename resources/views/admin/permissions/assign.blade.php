@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h6 class="card-title">Assign Permissions to Role</h6>
    </div>
    <x-message></x-message>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.roles.permissions.update', $role->id) }}">
            @csrf
            @method('PUT')
            <h6>Role: {{ $role->name }}</h6>
            <div class="row">
                @foreach($permissions as $permission)
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->id }}"
                                id="permission-{{ $permission->id }}" 
                                {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                            <label class="form-check-label" for="permission-{{ $permission->id }}">
                                {{ $permission->name }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
            <button type="submit" class="btn btn-primary mt-3">Update Permissions</button>
        </form>
    </div>
</div>
@endsection
