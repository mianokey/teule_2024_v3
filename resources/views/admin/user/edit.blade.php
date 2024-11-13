@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h6 class="card-title">Edit User</h6>
    </div>
    <x-message></x-message>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.user.update', $user->id) }}" enctype="multipart/form-data">
            @method('PATCH')
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="name" name="name"
                            value="{{ old('name', $user->name) }}" placeholder="Name">
                        <label for="name">Name</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="email" name="email"
                            value="{{ old('email', $user->email) }}" placeholder="Email">
                        <label for="email">Email</label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="position" class="form-control" id="position" name="position"
                            value="{{ old('position', $details['position'] ?? null) }}" placeholder="Position">
                        <label for="position">Position</label>
                    </div>
                </div>
                <div class="row col-md-6">
                    <div class="col-md-6">
                        <div class="text-center form-floating mb-3">
                            @php
                            $imgUrl = $user->details->where('key', 'img_url')->first();
                            @endphp

                            @if($imgUrl)
                            <img src="{{ url('storage/' . $details['img_url'] ?? null) }}" height="90"
                                alt="{{ $user->name }} image" />
                            @endif


                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input type="file" class="form-control" id="image" name="image">
                            <label for="image">Upload New Image</label>
                        </div>
                    </div>

                </div>

                
            </div>

            <hr>
            <h6><b>Change Password</b></h6>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="current_password"
                            name="current_password" placeholder="Confirm Current Password">
                        <label for="current_password">Confirm Current Password(Change)</label>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="password_confirmation"
                            name="password_confirmation" placeholder="Confirm Password">
                        <label for="password_confirmation">New Password(Change)</label>
                    </div>
                </div>



            </div>

            <div class="row">
                <div class="col-md-12">
                    <button type="submit" class="btn btn-primary btn-block">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection