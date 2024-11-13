<!-- admin/system_details.blade.php -->

@extends('layouts.admin')

@section('content')
    <div class="card">
        <div class="card-header">
            <h6 class="card-title">System Details</h6>
        </div>
        <x-message></x-message>
        <div class="card-body">
            <table id="datatable" class="table data-table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Key</th>
                        <th>Value</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($systemDetails as $systemDetail)
                        <tr>
                            <td>{{ $systemDetail->id }}</td>
                            <td>{{ $systemDetail->key }}</td>
                            <td>{{ $systemDetail->value }}</td>
                            <td>
                                 <a href="{{ route('admin.system_details.edit', ['id' => $systemDetail->id]) }}" class="btn btn-info btn-sm">Edit</a>
                                <form action="{{ route('admin.system_details.destroy', ['id' => $systemDetail->id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this system detail?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection