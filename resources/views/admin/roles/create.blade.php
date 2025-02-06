@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h6 class="card-title">Create Role</h6>
    </div>
    <x-message></x-message>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.roles.store') }}">
            @csrf
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="role_name" name="name" value="{{ old('name') }}"
                    placeholder="Role Name">
                <label for="role_name">Role Name</label>
            </div>
            <button type="submit" class="btn btn-primary">Create Role</button>
        </form>
    </div>
</div>
@endsection
