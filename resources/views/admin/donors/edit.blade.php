@extends('layouts.admin')

@section('content')

<div class="card">

    <div class="card-header">
        <h6 class="card-title mb-0">
            Edit Donor
        </h6>
    </div>

    <x-message></x-message>

    <div class="card-body">

        <form action="{{ route('admin.donors.update', $donor) }}"
              method="POST">

            @csrf
            @method('PUT')

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        Donor Number
                    </label>

                    <input type="text"
                           class="form-control"
                           value="{{ $donor->donor_number }}"
                           readonly>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        Donor Name <span class="text-danger">*</span>
                    </label>

                    <input type="text"
                           name="name"
                           class="form-control"
                           value="{{ old('name', $donor->name) }}"
                           required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        Phone
                    </label>

                    <input type="text"
                           name="phone"
                           class="form-control"
                           value="{{ old('phone', $donor->phone) }}"
                           placeholder="+254...">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        Email
                    </label>

                    <input type="email"
                           name="email"
                           class="form-control"
                           value="{{ old('email', $donor->email) }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">
                        Organization
                    </label>

                    <input type="text"
                           name="organization"
                           class="form-control"
                           value="{{ old('organization', $donor->organization) }}">
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">
                        Address
                    </label>

                    <textarea name="address"
                              class="form-control"
                              rows="2">{{ old('address', $donor->address) }}</textarea>
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">
                        Notes
                    </label>

                    <textarea name="notes"
                              class="form-control"
                              rows="3">{{ old('notes', $donor->notes) }}</textarea>
                </div>

            </div>

            <div class="mt-3">

                <button type="submit"
                        class="btn btn-primary">
                    Update Donor
                </button>

                <a href="{{ route('admin.donors.show', $donor) }}"
                   class="btn btn-secondary">
                    Cancel
                </a>

            </div>

        </form>

    </div>

</div>

@endsection
