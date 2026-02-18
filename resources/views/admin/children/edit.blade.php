@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h6 class="card-title">EDIT <b>{{ $child->name }}</b> INFO</h6>
    </div>

    <x-message></x-message>

    <div class="card-body">
        <form method="POST" action="{{ route('child_update', $child->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- BASIC INFO --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="name" name="name"
                               value="{{ $child->name }}" placeholder="Name">
                        <label for="name">Name</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="date" class="form-control" id="dob" name="dob"
                               value="{{ $child->dob }}" placeholder="Date of Birth">
                        <label for="dob">Date of Birth</label>
                    </div>
                </div>
            </div>

            {{-- PROTECTED DETAILS --}}
            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" name="hobbies"
                               value="{{ $child->details->where('key','hobbies')->first()->value ?? '' }}"
                               placeholder="Hobbies">
                        <label>Hobbies</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" name="current_grade"
                               value="{{ $child->details->where('key','current_grade')->first()->value ?? '' }}"
                               placeholder="Current Grade">
                        <label>Current Grade</label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" name="aspirations"
                               value="{{ $child->details->where('key','aspirations')->first()->value ?? '' }}"
                               placeholder="Aspirations">
                        <label>Aspirations</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <select class="form-select" name="status">
                            <option value="">Select One...</option>
                            <option value="Inactive" {{ $child->status == 'Inactive' ? 'selected' : '' }}>
                                Inactive (NOT IN CURRENT POPULATION)
                            </option>
                            <option value="Residential" {{ $child->status == 'Residential' ? 'selected' : '' }}>
                                Residential
                            </option>
                            <option value="Community Based Care" {{ $child->status == 'Community Based Care' ? 'selected' : '' }}>
                                Community Based Care
                            </option>
                            <option value="Temporal Placement" {{ $child->status == 'Temporal Placement' ? 'selected' : '' }}>
                                Temporal Placement
                            </option>
                        </select>
                        <label>Status</label>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" name="sponsors"
                               value="{{ $child->details->where('key','sponsors')->first()->value ?? '0' }}"
                               placeholder="Sponsors">
                        <label>Sponsors</label>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-floating mb-3">
                        <input type="file" class="form-control" name="image">
                        <label>Upload New Image</label>
                    </div>
                </div>
            </div>

            {{-- IMAGE (HIDDEN UNTIL VIEW CLICKED) --}}
            <div class="row mb-4">
                <div class="col-md-6">

                    <button type="button"
                            class="btn btn-outline-primary mb-3"
                            id="toggleImageBtn">
                        👁 View Image
                    </button>

                    <div id="childImageWrapper" style="display:none;">
                        <img src="{{ asset($child->img_url) }}"
                             height="180"
                             class="img-thumbnail d-block mb-2"
                             alt="{{ $child->name }} image">
                    </div>

                </div>
            </div>

            {{-- DYNAMIC DETAILS --}}
            <hr>
            <h6 class="mb-3">Additional Details</h6>

            <div id="details-wrapper">

                @foreach($child->details as $detail)
                    @if(!in_array($detail->key, ['hobbies','current_grade','aspirations','sponsors']))
                        <div class="row detail-row mb-2">
                            <div class="col-md-5">
                                <input type="text"
                                       name="detail_keys[]"
                                       class="form-control"
                                       value="{{ $detail->key }}"
                                       placeholder="Key">
                            </div>

                            <div class="col-md-5">
                                <input type="text"
                                       name="detail_values[]"
                                       class="form-control"
                                       value="{{ $detail->value }}"
                                       placeholder="Value">
                            </div>

                            <div class="col-md-2">
                                <button type="button"
                                        class="btn btn-danger remove-detail w-100">
                                    Remove
                                </button>
                            </div>
                        </div>
                    @endif
                @endforeach

            </div>

            <div class="mb-4">
                <button type="button"
                        class="btn btn-success"
                        id="add-detail">
                    + Add More Detail
                </button>
            </div>

            {{-- SUBMIT --}}
            <div class="row">
                <div class="col-md-12">
                    <button type="submit"
                            class="btn btn-primary w-100">
                        Update
                    </button>
                </div>
            </div>

        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ADD DETAIL
document.getElementById('add-detail').addEventListener('click', function () {

    let wrapper = document.getElementById('details-wrapper');

    let newRow = document.createElement('div');
    newRow.classList.add('row','detail-row','mb-2');

    newRow.innerHTML = `
        <div class="col-md-5">
            <input type="text" name="detail_keys[]" class="form-control" placeholder="Key">
        </div>
        <div class="col-md-5">
            <input type="text" name="detail_values[]" class="form-control" placeholder="Value">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger remove-detail w-100">Remove</button>
        </div>
    `;

    wrapper.appendChild(newRow);
});

// REMOVE DETAIL
document.addEventListener('click', function(e){
    if(e.target.classList.contains('remove-detail')){
        e.target.closest('.detail-row').remove();
    }
});

// TOGGLE IMAGE
let toggleBtn = document.getElementById('toggleImageBtn');
let imageWrapper = document.getElementById('childImageWrapper');

toggleBtn.addEventListener('click', function () {
    if (imageWrapper.style.display === "none") {
        imageWrapper.style.display = "block";
        toggleBtn.innerHTML = "🙈 Hide Image";
    } else {
        imageWrapper.style.display = "none";
        toggleBtn.innerHTML = "👁 View Image";
    }
});
</script>
@endpush