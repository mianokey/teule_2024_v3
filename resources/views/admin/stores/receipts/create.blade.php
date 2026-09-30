@extends('layouts.admin')

@section('content')

<div class="card">

    <div class="card-header">
        <h6 class="card-title mb-0">
            New Store Receipt
        </h6>

        <small class="text-muted">
            Record goods received into a physical Store.
        </small>
    </div>

    <x-message></x-message>

    <div class="card-body">

        <form action="{{ route('admin.store-receipts.store') }}"
              method="POST">

            @csrf

            <div class="row">

                {{-- STORE --}}
                <div class="col-md-6 mb-3">

                    <label for="store_id" class="form-label">
                        Store <span class="text-danger">*</span>
                    </label>

                    <select
                        name="store_id"
                        id="store_id"
                        class="form-select @error('store_id') is-invalid @enderror"
                        required
                    >

                        <option value="">
                            Select Store
                        </option>

                        @foreach($stores as $store)

                            <option
                                value="{{ $store->id }}"
                                {{ old('store_id') == $store->id ? 'selected' : '' }}
                            >
                                {{ $store->name }}
                                @if($store->code)
                                    ({{ $store->code }})
                                @endif
                            </option>

                        @endforeach

                    </select>

                    @error('store_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- SOURCE TYPE --}}
                <div class="col-md-6 mb-3">

                    <label for="source_type" class="form-label">
                        Source <span class="text-danger">*</span>
                    </label>

                    <select
                        name="source_type"
                        id="source_type"
                        class="form-select @error('source_type') is-invalid @enderror"
                        required
                    >

                        <option value="">
                            Select Source
                        </option>

                        <option
                            value="PURCHASE"
                            {{ old('source_type') === 'PURCHASE' ? 'selected' : '' }}
                        >
                            Purchase
                        </option>

                        <option
                            value="DONATION"
                            {{ old('source_type') === 'DONATION' ? 'selected' : '' }}
                        >
                            Donation
                        </option>

                    </select>

                    @error('source_type')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- SUPPLIER --}}
                <div
                    class="col-md-6 mb-3"
                    id="supplier_fields"
                >

                    <label for="supplier_name" class="form-label">
                        Supplier Name
                    </label>

                    <input
                        type="text"
                        name="supplier_name"
                        id="supplier_name"
                        value="{{ old('supplier_name') }}"
                        class="form-control @error('supplier_name') is-invalid @enderror"
                        maxlength="255"
                        placeholder="Enter supplier name"
                    >

                    @error('supplier_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- SUPPLIER REFERENCE --}}
                <div
                    class="col-md-6 mb-3"
                    id="supplier_reference_field"
                >

                    <label for="supplier_reference" class="form-label">
                        Supplier / Delivery Reference
                    </label>

                    <input
                        type="text"
                        name="supplier_reference"
                        id="supplier_reference"
                        value="{{ old('supplier_reference') }}"
                        class="form-control @error('supplier_reference') is-invalid @enderror"
                        maxlength="255"
                        placeholder="Invoice, delivery note, etc."
                    >

                    @error('supplier_reference')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- DONATION --}}
                <div
                    class="col-md-6 mb-3"
                    id="donation_field"
                >

                    <label for="donation_id" class="form-label">
                        Donation
                    </label>

                    <input
                        type="number"
                        name="donation_id"
                        id="donation_id"
                        value="{{ old('donation_id') }}"
                        class="form-control @error('donation_id') is-invalid @enderror"
                        min="1"
                        placeholder="Enter existing Donation ID"
                    >

                    <small class="text-muted">
                        Link this receipt to an existing donation record.
                    </small>

                    @error('donation_id')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- RECEIVED DATE --}}
                <div class="col-md-6 mb-3">

                    <label for="received_date" class="form-label">
                        Received Date <span class="text-danger">*</span>
                    </label>

                    <input
                        type="date"
                        name="received_date"
                        id="received_date"
                        value="{{ old(
                            'received_date',
                            now()->format('Y-m-d')
                        ) }}"
                        class="form-control @error('received_date') is-invalid @enderror"
                        required
                    >

                    @error('received_date')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>


                {{-- NOTES --}}
                <div class="col-12 mb-3">

                    <label for="notes" class="form-label">
                        Notes
                    </label>

                    <textarea
                        name="notes"
                        id="notes"
                        rows="4"
                        class="form-control @error('notes') is-invalid @enderror"
                        maxlength="5000"
                        placeholder="Any additional receiving information..."
                    >{{ old('notes') }}</textarea>

                    @error('notes')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror

                </div>

            </div>


            <div class="d-flex justify-content-between mt-3">

                <a
                    href="{{ route('admin.store-receipts.index') }}"
                    class="btn btn-secondary"
                >
                    <i class="fas fa-arrow-left me-1"></i>
                    Cancel
                </a>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    <i class="fas fa-save me-1"></i>
                    Create Receipt
                </button>

            </div>

        </form>

    </div>

</div>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const sourceType = document.getElementById('source_type');

    const supplierFields =
        document.getElementById('supplier_fields');

    const supplierReferenceField =
        document.getElementById('supplier_reference_field');

    const donationField =
        document.getElementById('donation_field');

    const supplierName =
        document.getElementById('supplier_name');

    const supplierReference =
        document.getElementById('supplier_reference');

    const donationId =
        document.getElementById('donation_id');


    function updateSourceFields() {

        const source = sourceType.value;

        if (source === 'DONATION') {

            supplierFields.style.display = 'none';
            supplierReferenceField.style.display = 'none';

            donationField.style.display = '';

            supplierName.value = '';
            supplierReference.value = '';

        } else {

            supplierFields.style.display = '';
            supplierReferenceField.style.display = '';

            donationField.style.display = 'none';

            donationId.value = '';
        }
    }


    sourceType.addEventListener(
        'change',
        updateSourceFields
    );

    updateSourceFields();

});
</script>

@endsection