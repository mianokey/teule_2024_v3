@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h6 class="card-title">Add New Child</h6>
    </div>
    <x-message></x-message>
    <div class="card-body">
        <form method="POST" action="{{ route('newchild.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}"
                            placeholder="Name">
                        <label for="name">Name</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="date" class="form-control" id="dob" name="dob" value="{{ old('dob') }}"
                            placeholder="Date of Birth">
                        <label for="dob">Date of Birth</label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="hobbies" name="hobbies" value="{{ old('hobbies') }}"
                            placeholder="Hobbies">
                        <label for="hobbies">Hobbies</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="current_grade" value="{{ old('current_grade') }}"
                            name="current_grade" placeholder="Current Grade">
                        <label for="current_grade">Current Grade</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="aspirations" name="aspirations"
                            value="{{ old('aspirations') }}" placeholder="Aspirations">
                        <label for="aspirations">Aspirations</label>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <select class="form-select form-control" id="status" name="status"
                            aria-label="Floating label select example">
                            <option value="">Select One...</option>
                            <option value="Inactive">Inactive(NOT IN CURRENT POPULATION)</option>
                            <option value="Residential">Residential</option>
                            <option value="Community Based care">Community Based Care</option>
                            <option value="Temporal Placement">Temporal Placement</option>

                        </select>
                        <label for="aspirations">Status</label>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="sponsors" name="sponsors"
                            value="{{ old('sponsors', 0) }}" placeholder="Number of Sponsors">
                        <label for="sponsors">Sponsors</label>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="file" class="form-control" id="image" name="image">
                        <label for="img_url">UPLOAD IMAGE</label>
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