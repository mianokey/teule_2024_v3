@extends('layouts.admin')

@section('content')
    <div class="card">
        <div class="card-header">
            <h6 class="card-title">Edit System Detail</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.system_details.update', ['id' => $systemDetail->id]) }}">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label for="key" class="form-label">Key</label>
                    <input type="text" class="form-control" id="key" name="key" value="{{ $systemDetail->key }}">
                </div>
                <div class="mb-3">
                    <label for="value" class="form-label">Value</label>
                    <textarea class="form-control" id="value" name="value" rows="3">{{ $systemDetail->value }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
@endsection
