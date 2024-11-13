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
                        @php
                            $imgUrl = $user->details->where('key', 'img_url')->first();
                        @endphp

                        <img src="{{ asset($imgUrl->value) }}" height="90" alt="{{ $user->name }} image">

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
            <!-- Left column for basic user information -->
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
    /* Styles for the popup */
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
        /* Increased padding */
    }

    .popup-content {
        background-color: #fefefe;
        margin: 0 auto;
        padding: 20px;
        border: 1px solid #888;
        width: 60%;
        /* Reduced width */
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
        z-index: 9999;
    }

    .close-popup:hover,
    .close-popup:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
</style>

<script>
    // JavaScript to handle showing/hiding the popup
    const viewDetailsBtns = document.querySelectorAll('.view-details-btn');

    viewDetailsBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const { name, email } = btn.dataset;
            const details = JSON.parse(btn.getAttribute('data-details'));

            document.getElementById('popup-name').textContent = name;
            document.getElementById('popup-email').textContent = email;
            document.getElementById('popup-position').textContent = details.position;

            const popupOtherDetails = document.getElementById('popup-other-details');
            popupOtherDetails.innerHTML = ''; // Clear previous details

            // Loop through details and add them to the popup
            for (const key in details) {
                if (key !== 'email' && key !== 'position') {
                    const listItem = document.createElement('li');
                    listItem.innerHTML = `<strong>${key}:</strong> ${details[key]}`;
                    popupOtherDetails.appendChild(listItem);
                }
            }

            document.getElementById('popup-details').style.display = 'block';
        });
    });

    // Close the popup when the close button is clicked
    document.querySelector('.close-popup').addEventListener('click', () => {
        document.getElementById('popup-details').style.display = 'none';
    });
</script>
@endsection