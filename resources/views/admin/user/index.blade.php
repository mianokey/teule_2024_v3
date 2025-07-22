@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h6 class="card-title">All Users List</h6>
    </div>
    <x-message></x-message>

    <div class="table-responsive p-4 col-md-12">
        <table id="datatable" class="table data-table table-striped table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Position</th>
                    <th>Image</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ optional($user->details->where('key', 'position')->first())->value ?? 'No position found' }}</td>
                    <td>
                        <button class="btn btn-sm btn-info toggle-img-btn" data-user-id="{{ $user->id }}">
                            <i class="fa fa-eye"></i> View Image
                        </button>
                        <div id="user-img-{{ $user->id }}" style="display: none;">
                            @php
                                $imgUrl = optional($user->details->where('key', 'img_url')->first())->value;
                            @endphp
                            @if($imgUrl)
                                <img src="{{ asset($imgUrl) }}" height="90" alt="{{ $user->name }} image">
                            @else
                                <p>No Image Available</p>
                            @endif
                        </div>
                    </td>
                    <td>
                        <!-- Role section (commented out for now) -->
                    </td>
                    <td>
                        <button class="btn btn-info btn-sm mr-2 view-details-btn" 
                            data-name="{{ $user->name }}"
                            data-email="{{ $user->email }}"
                            data-details="{{ json_encode($user->details->pluck('value', 'key')) }}">
                            View Details
                        </button>
                        <a href="{{ route('admin.user.edit', ['id' => $user->id]) }}" class="btn btn-primary btn-sm mr-2">Edit</a>
                        <form action="{{ route('admin.user.delete', ['id' => $user->id]) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
