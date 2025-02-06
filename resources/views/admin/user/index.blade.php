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
                    <td>
                        @if ($user->details && $user->details->where('key', 'position')->first())
                        {{ $user->details->where('key', 'position')->first()->value }}
                        @else
                        No position found
                        @endif
                    </td>
                    <td>
                        <!-- Image visibility toggle -->
                        <button class="btn btn-sm btn-info toggle-img-btn" data-user-id="{{ $user->id }}">
                            <i class="fa fa-eye"></i> View Image
                        </button>
                        <div id="user-img-{{ $user->id }}" style="display: none;">
                            @php
                            $imgUrl = $user->details->where('key', 'img_url')->first();
                            @endphp
                            <img src="{{ asset($imgUrl->value) }}" height="90" alt="{{ $user->name }} image">
                        </div>
                    </td>
                    <td>
                        <select class="form-control change-role" data-user-id="{{ $user->id }}" 
                            @if(!auth()->user()->can('MANAGE PERMISSIONS')) disabled @endif>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" @if($user->hasRole($role->name)) selected @endif>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </select>
                    
                    </td>
                    <td>
                        <button class="btn btn-info btn-sm mr-2 view-details-btn" data-name="{{ $user->name }}"
                            data-email="{{ $user->email }}"
                            data-details="{{ json_encode($user->details->pluck('value', 'key')) }}">
                            View Details
                        </button>
                        <a href="{{ route('admin.user.edit', ['id' => $user->id]) }}"
                            class="btn btn-primary btn-sm mr-2">Edit</a>
                        <form action="{{ route('admin.user.delete', ['id' => $user->id]) }}" method="POST"
                            class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm"
                                onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Hidden popup div to show user details -->
<div id="popup-details" class="popup-details">
    <div class="popup-content">
        <span class="close-popup">&times;</span>
        <div class="row">
            <div class="col-md-12">
                <p><strong>Name:</strong> <span id="popup-name"></span></p>
                <p><strong>Email:</strong> <span id="popup-email"></span></p>
                <p><strong>Position:</strong> <span id="popup-position"></span></p>
                <p><strong>Other Details:</strong></p>
                <ul id="popup-other-details"></ul>
            </div>
        </div>
    </div>
</div>

<style>
    .popup-details {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0, 0, 0, 0.4);
        padding-top: 80px;
    }

    .popup-content {
        background-color: #fefefe;
        margin: 0 auto;
        padding: 20px;
        border: 1px solid #888;
        width: 60%;
        position: relative;
    }

    .close-popup {
        color: #aaa;
        position: absolute;
        top: 10px;
        right: 10px;
        font-size: 28px;
        font-weight: bold;
        cursor: pointer;
    }

    .close-popup:hover {
        color: black;
    }

    .toggle-img-btn {
        background-color: #007bff;
        color: white;
        border: none;
        cursor: pointer;
    }

    .toggle-img-btn i {
        margin-right: 5px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.toggle-img-btn').forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-user-id');
            const imgDiv = document.getElementById('user-img-' + userId);
            if (imgDiv.style.display === 'none') {
                imgDiv.style.display = 'block';
                this.innerHTML = '<i class="fa fa-eye-slash"></i> Hide Image';
            } else {
                imgDiv.style.display = 'none';
                this.innerHTML = '<i class="fa fa-eye"></i> View Image';
            }
        });
    });

    document.querySelectorAll('.change-role').forEach(select => {
        select.addEventListener('change', function() {
            const userId = this.getAttribute('data-user-id');
            const roleId = this.value;
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                console.error('CSRF token meta tag not found.');
                alert('An error occurred. Please reload the page and try again.');
                return;
            }
            fetch(`/admin/user/${userId}/assign-role`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
            },
            body: JSON.stringify({ role_id: roleId })
        })

            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Role updated successfully');
                } else {
                    alert(data.message || 'Failed to update role');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the role.');
            });
        });
    });
});
</script>
@endsection