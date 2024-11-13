<!-- admin/add_system_detail.blade.php -->

@extends('layouts.admin')

@section('content')
    <div class="card">
        <div class="card-header">
            <h6 class="card-title">Add New System Detail</h6>
        </div>
        <x-message></x-message>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.system_details.store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="key" name="key" value="{{ old('key') }}"
                                placeholder="Key">
                            <label for="key">Key</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-floating mb-3">
                            <textarea class="form-control" id="value" name="value" rows="3" placeholder="Value">{{ old('value') }}</textarea>
                            <label for="value">Value</label>
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