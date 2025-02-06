@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h6 class="card-title">Edit Permission: {{ $permission->name }}</h6>
    </div>
    <x-message></x-message>
    <div class="card-body">
        <form action="{{ route('admin.permissions.update', $permission->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-12">
                    <div class="form-floating mb-3">
                        <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $permission->name) }}" placeholder="Permission Name" required>
                        <label for="name">Permission Name</label>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary btn-block">Update Permission</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
