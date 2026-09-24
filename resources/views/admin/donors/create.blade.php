@extends('layouts.admin')

@section('content')

<div class="container-fluid px-0">

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">

        <h4 class="mb-0">
            Add Donor
        </h4>

        <a href="{{ route('admin.donors.index') }}"
           class="btn btn-light border btn-sm">
            <i class="fas fa-arrow-left me-1"></i>
            Back
        </a>

    </div>

    <x-message></x-message>


    {{-- Donor Form --}}
    <div class="card border-0 shadow-sm">

        <div class="card-header bg-white">

            <strong>
                <i class="fas fa-user-plus text-primary me-2"></i>
                Donor Details
            </strong>

        </div>


        <form action="{{ route('admin.donors.store') }}"
              method="POST">

            @csrf

            <div class="card-body p-4">

                <div class="row g-3">

                    {{-- Donor Name --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            Donor Name
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            value="{{ old('name') }}"
                            placeholder="Full name"
                            required
                        >

                    </div>


                    {{-- Organization --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            Organization
                        </label>

                        <input
                            type="text"
                            name="organization"
                            class="form-control"
                            value="{{ old('organization') }}"
                            placeholder="Organization name"
                        >

                    </div>


                    {{-- Phone --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            Phone
                        </label>

                        <input
                            type="text"
                            name="phone"
                            class="form-control"
                            value="{{ old('phone') }}"
                            placeholder="+254..."
                        >

                    </div>


                    {{-- Email --}}
                    <div class="col-md-6">

                        <label class="form-label">
                            Email
                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="{{ old('email') }}"
                            placeholder="donor@example.com"
                        >

                    </div>


                    {{-- Address --}}
                    <div class="col-md-12">

                        <label class="form-label">
                            Address
                        </label>

                        <textarea
                            name="address"
                            class="form-control"
                            rows="2"
                            placeholder="Postal or physical address"
                        >{{ old('address') }}</textarea>

                    </div>


                    {{-- Notes --}}
                    <div class="col-md-12">

                        <label class="form-label">
                            Notes
                        </label>

                        <textarea
                            name="notes"
                            class="form-control"
                            rows="2"
                            placeholder="Additional information"
                        >{{ old('notes') }}</textarea>

                    </div>

                </div>

            </div>


            {{-- Actions --}}
            <div class="card-footer bg-white text-end">

                <a href="{{ route('admin.donors.index') }}"
                   class="btn btn-light border me-2">
                    Cancel
                </a>

                <button type="submit"
                        class="btn btn-primary px-4">

                    <i class="fas fa-save me-1"></i>
                    Save Donor

                </button>

            </div>

        </form>

    </div>

</div>


<style>

.form-label {
    font-size: 13px;
    font-weight: 500;
    margin-bottom: 5px;
}

.card-header {
    min-height: 55px;
    display: flex;
    align-items: center;
}

.form-control {
    border-radius: 5px;
}

</style>

@endsection