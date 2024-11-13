@extends('layouts.admin')

@section('content')
    <div class="card">
        <div class="card-header">
            <h6 class="card-title">All Children List</h6>
        </div>
        <x-message></x-message>

        <div class="table-responsive p-4 col-md-12">
            <table id="datatable" class="table data-table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Date of Birth</th>
                        <th>Sponsors</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($children as $child)
                        <tr>
                            <td>{{ $child->name }}</td>
                            <td>{{ $child->dob }}</td>
                            <td>{{ $child->details->firstWhere('key', 'sponsors')->value ?? 'NOT STATED' }}</td>
                            <td>{{ $child->status }}</td>
                            <td>
                                <button class="btn btn-info btn-sm mr-2 view-details-btn" data-name="{{ $child->name }}" data-dob="{{ $child->dob }}" data-status="{{ $child->status }}" data-details="{{ json_encode($child->details->pluck('value', 'key')) }}">View Details</button>
                                <a href="{{ route('child.edit.index', ['id' => $child->id]) }}" class="btn btn-primary btn-sm mr-2">Edit</a>
                                <button class="btn btn-secondary btn-sm generate-url-btn" data-id="{{ $child->encoded_id }}">Copy Profile URL</button>
                                <form action="{{ route('child_delete', ['id' => $child->id]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this child?')">Delete</button>
                                 </form>
                            </td>
                            
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>


    <!-- Hidden popup div to show child details -->
    <div id="popup-details" class="popup-details">
        <div class="popup-content">
            <span class="close-popup">&times;</span>
            <p><strong>Name:</strong> <span id="popup-name"></span></p>
            <p><strong>Date of Birth:</strong> <span id="popup-dob"></span></p>
            <p><strong>Status:</strong> <span id="popup-status"></span></p>
            <p><strong>Other Details:</strong></p>
            <ul id="popup-other-details"></ul>
            <!-- Add more details here as needed -->
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
            background-color: rgba(0,0,0,0.4);
            padding-top: 80px; /* Increased padding */
        }

        .popup-content {
            background-color: #fefefe;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #888;
            width: 60%; /* Reduced width */
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

        .close-popup:hover,
        .close-popup:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>

    <!-- Toast for displaying "URL copied" message -->
<div class="toast" id="copyToast" role="alert" aria-live="assertive" aria-atomic="true" style="position: fixed; top: 0; right:0%;z-index:1050;">
    <div class="toast-header">
        <strong class="me-auto">URL copied</strong>
        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body">
        The URL has been copied to the clipboard.
    </div>
</div>


    <script>
       document.addEventListener('DOMContentLoaded', function() {
    var generateUrlBtns = document.querySelectorAll('.generate-url-btn');

    generateUrlBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var url = "{{ route('sponsorship_card', ':encoded_id') }}".replace(':encoded_id', this.getAttribute('data-id'));

            // Create a temporary input element
            var tempInput = document.createElement('input');
            tempInput.value = url;
            document.body.appendChild(tempInput);

            // Select and copy the URL
            tempInput.select();
            document.execCommand('copy');

            // Remove the temporary input element
            document.body.removeChild(tempInput);

            // Show the toast
            var copyToast = new bootstrap.Toast(document.getElementById('copyToast'));
            copyToast.show();
        });
    });
});

    </script>

<script>
    // JavaScript to handle showing/hiding the popup
    var viewDetailsBtns = document.querySelectorAll('.view-details-btn');

    viewDetailsBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            var name = this.getAttribute('data-name');
            var dob = this.getAttribute('data-dob');
            var status = this.getAttribute('data-status');
            var details = JSON.parse(this.getAttribute('data-details'));

            document.getElementById('popup-name').textContent = name;
            document.getElementById('popup-dob').textContent = dob;
            document.getElementById('popup-status').textContent = status;

            var popupOtherDetails = document.getElementById('popup-other-details');
            popupOtherDetails.innerHTML = ''; // Clear previous details

            // Loop through details and add them to the popup
            for (var key in details) {
                var listItem = document.createElement('li');
                listItem.innerHTML = '<strong>' + key + ':</strong> ' + details[key];
                popupOtherDetails.appendChild(listItem);
            }

            document.getElementById('popup-details').style.display = 'block';
        });
    });

    // Close the popup when the close button is clicked
    document.querySelector('.close-popup').addEventListener('click', function() {
        document.getElementById('popup-details').style.display = 'none';
    });
</script>

    
@endsection
