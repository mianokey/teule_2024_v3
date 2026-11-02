<div class="row g-3">

    <div class="col-md-6">
        <label class="form-label">
            Department <span class="text-danger">*</span>
        </label>

        <select name="department_id"
                class="form-select @error('department_id') is-invalid @enderror"
                required>
            <option value="">Select department</option>

            @foreach($departments as $department)
                <option value="{{ $department->id }}"
                    @selected(old(
                        'department_id',
                        isset($costCentre) ? $costCentre->department_id : ''
                    ) == $department->id)>
                    {{ $department->name }}
                </option>
            @endforeach
        </select>

        @error('department_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">
            Cost Centre Name <span class="text-danger">*</span>
        </label>

        <input type="text"
               name="name"
               class="form-control @error('name') is-invalid @enderror"
               value="{{ old('name', $costCentre->name ?? '') }}"
               placeholder="e.g. Primary School Fees"
               required>

        @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">
            Cost Centre Code <span class="text-danger">*</span>
        </label>

        <input type="text"
               name="code"
               class="form-control @error('code') is-invalid @enderror"
               value="{{ old('code', $costCentre->code ?? '') }}"
               placeholder="e.g. PROG-001"
               required>

        @error('code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Description</label>

        <textarea name="description"
                  rows="3"
                  class="form-control @error('description') is-invalid @enderror"
                  placeholder="Briefly describe what this cost centre covers">{{ old('description', $costCentre->description ?? '') }}</textarea>

        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

</div>
