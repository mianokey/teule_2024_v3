@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h6 class="card-title">Assign Role to User</h6>
    </div>
    <x-message></x-message>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.users.roles.update', $user->id) }}">
            @csrf
            @method('PUT')
            <h6>User: {{ $user->name }}</h6>
            <div class="form-floating mb-3">
                <select class="form-control" id="role" name="role">
                    <option selected disabled>Choose Role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                <label for="role">Select Role</label>
            </div>
            <button type="submit" class="btn btn-primary">Assign Role</button>
        </form>
    </div>
</div>
@endsection
