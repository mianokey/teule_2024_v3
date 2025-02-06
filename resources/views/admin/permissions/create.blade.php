@extends('layouts.admin')

@section('content')
    <h1>Create Permission</h1>

    <form action="{{ route('admin.permissions.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="name">Permission Name</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
        </div>

        <button type="submit" class="btn btn-primary">Create Permission</button>
    </form>
@endsection
