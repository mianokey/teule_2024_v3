@extends('layouts.admin')

@section('content')
    <div class="card">
        <div class="card-header">
            <h6 class="card-title">EDIT <b>{{ $child->name }} </b>INFO</h6>
        </div>
        <x-message></x-message>
        <div class="card-body">
            <form method="POST" action="{{ route('child_update', $child->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="name" name="name" value="{{ $child->name }}" placeholder="Name">
                            <label for="name">Name</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input type="date" class="form-control" id="dob" name="dob" value="{{ $child->dob }}" placeholder="Date of Birth">
                            <label for="dob">Date of Birth</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="hobbies" name="hobbies" value="{{ $child->details->where('key', 'hobbies')->first()->value ?? '' }}" placeholder="Hobbies">
                            <label for="hobbies">Hobbies</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="current_grade" name="current_grade" value="{{ $child->details->where('key', 'current_grade')->first()->value ?? '' }}" placeholder="Current Grade">
                            <label for="current grade">Current Grade DFDF</label>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="aspirations" name="aspirations" value="{{ $child->details->where('key', 'aspirations')->first()->value ?? '' }}" placeholder="Aspirations">
                            <label for="aspirations">Aspirations</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <select class="form-select form-control" id="status" name="status" aria-label="Floating label select example">
                                <option value="">Select One...</option>
                                <option value="Inactive" {{ $child->status == 'Inactive' ? 'selected' : '' }}>Inactive (NOT IN CURRENT POPULATION)</option>
                                <option value="Residential" {{ $child->status == 'Residential' ? 'selected' : '' }}>Residential</option>
                                <option value="Community Based Care" {{ $child->status == 'Community Based Care' ? 'selected' : '' }}>Community Based Care</option>
                                <option value="Temporal Placement" {{ $child->status == 'Temporal Placement' ? 'selected' : '' }}>Temporal Placement</option>
                            </select>
                            <label for="status">Status</label>
                        </div>
                    </div>
                    
                    
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="sponsors" name="sponsors" value="{{ $child->details->where('key', 'sponsors')->first()->value ?? '0' }}" placeholder="Sponsors">
                            <label for="Sponsors">Sponsors</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input type="file" class="form-control" id="image" name="image">
                            <label for="image">UPLOAD NEW IMAGE</label>
                        </div>
                    </div>
                   
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            
                            <img src="{{ asset($child->img_url) }}"  height="150" alt="{{ $child->name }} image" />
                             </div>
                    </div>
                    
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary btn-block">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
