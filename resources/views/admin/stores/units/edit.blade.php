@extends('layouts.admin')

@section('content')

<div class="card">

    <div class="card-header">
        <h6 class="card-title mb-0">
            Edit Store Unit
        </h6>
    </div>

    <div class="card-body">

        @if($errors->any())

            <div class="alert alert-danger">

                <strong>Please correct the following:</strong>

                <ul class="mb-0 mt-2">

                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach

                </ul>

            </div>

        @endif

        <form action="{{ route('admin.store-units.update', $storeUnit) }}"
              method="POST">

            @csrf
            @method('PUT')

            <div class="form-group">

                <label for="name">
                    Unit Name <span class="text-danger">*</span>
                </label>

                <input type="text"
                       name="name"
                       id="name"
                       class="form-control"
                       value="{{ old('name', $storeUnit->name) }}"
                       required
                       maxlength="255">

            </div>

            <div class="form-group">

                <label for="code">
                    Unit Code <span class="text-danger">*</span>
                </label>

                <input type="text"
                       name="code"
                       id="code"
                       class="form-control"
                       value="{{ old('code', $storeUnit->code) }}"
                       required
                       maxlength="20">

                <small class="text-muted">
                    The code must be unique.
                </small>

            </div>

            <div class="form-group">

                <label for="description">
                    Description
                </label>

                <textarea name="description"
                          id="description"
                          class="form-control"
                          rows="4"
                          maxlength="2000">{{ old('description', $storeUnit->description) }}</textarea>

            </div>

            <div class="mt-4">

                <button type="submit"
                        class="btn btn-primary">

                    <i class="fa fa-save"></i>
                    Update Unit

                </button>

                <a href="{{ route('admin.store-units.index') }}"
                   class="btn btn-secondary">

                    Cancel

                </a>

            </div>

        </form>

    </div>

</div>

@endsection