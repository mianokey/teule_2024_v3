
@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/stores.css') }}">
@endpush

@section('content')

<div class="store-requisition-page">

    {{-- ============================================================
         PAGE HEADER
         ============================================================ --}}
    <div class="requisition-page-header">

        <div class="requisition-header-content">

            <div class="requisition-header-icon">
                <i class="fas fa-wallet"></i>
            </div>

            <div>

                <div class="requisition-breadcrumb">
                    <span>Finance</span>
                    <i class="fas fa-chevron-right"></i>
                    <span>Cost Centres</span>
                    <i class="fas fa-chevron-right"></i>
                    <span>Add Cost Centre</span>
                </div>

                <h1 class="requisition-page-title">
                    Add Cost Centre
                </h1>

                <p class="requisition-page-subtitle">
                    Create a departmental cost centre for budgeting and expenditure tracking.
                </p>

            </div>

        </div>

        <div class="requisition-page-actions">

            <a href="{{ route('admin.finance.cost-centres.index') }}"
               class="requisition-add-button">

                <i class="fas fa-arrow-left"></i>
                <span>Back to Cost Centres</span>

            </a>

        </div>

    </div>


    {{-- ============================================================
         MESSAGES
         ============================================================ --}}
    <x-message></x-message>


    {{-- ============================================================
         VALIDATION ERRORS
         ============================================================ --}}
    @if($errors->any())

        <div class="requisition-state-panel">

            <div class="requisition-state-main">

                <div class="requisition-state-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>

                <div class="requisition-state-text">

                    <strong>
                        Please correct the following errors
                    </strong>

                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>

                </div>

            </div>

        </div>

    @endif


    <form method="POST"
          action="{{ route('admin.finance.cost-centres.store') }}">

        @csrf


        {{-- ========================================================
             COST CENTRE INFORMATION
             ======================================================== --}}
        <div class="requisition-section">

            <div class="requisition-section-header">

                <div class="requisition-section-heading">

                    <div class="requisition-section-icon">
                        <i class="fas fa-building"></i>
                    </div>

                    <div>
                        <h5>Cost Centre Information</h5>
                        <p>Department and cost centre identification details.</p>
                    </div>

                </div>

            </div>


            <div class="row">

                {{-- Department --}}
                <div class="col-md-4">

                    <div class="requisition-detail-item">

                        <label for="department_id"
                               class="requisition-detail-label">
                            Department
                            <span class="text-danger">*</span>
                        </label>

                        <select
                            id="department_id"
                            name="department_id"
                            class="form-control requisition-input @error('department_id') is-invalid @enderror"
                            required
                        >

                            <option value="">
                                Select Department
                            </option>

                            @foreach($departments as $department)

                                <option
                                    value="{{ $department->id }}"
                                    {{ (string) old('department_id') === (string) $department->id ? 'selected' : '' }}
                                >
                                    {{ $department->name }}
                                </option>

                            @endforeach

                        </select>

                        @error('department_id')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>


                {{-- Cost Centre Name --}}
                <div class="col-md-4">

                    <div class="requisition-detail-item">

                        <label for="name"
                               class="requisition-detail-label">
                            Cost Centre Name
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name') }}"
                            class="form-control requisition-input @error('name') is-invalid @enderror"
                            placeholder="e.g. Primary School Fees"
                            required
                        >

                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>


                {{-- Cost Centre Code --}}
                <div class="col-md-4">

                    <div class="requisition-detail-item">

                        <label for="code"
                               class="requisition-detail-label">
                            Cost Centre Code
                            <span class="text-danger">*</span>
                        </label>

                        <input
                            type="text"
                            id="code"
                            name="code"
                            value="{{ old('code') }}"
                            class="form-control requisition-input @error('code') is-invalid @enderror"
                            placeholder="e.g. PROG-001"
                            required
                        >

                        @error('code')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================
             DESCRIPTION
             ======================================================== --}}
        <div class="requisition-section">

            <div class="requisition-section-header">

                <div class="requisition-section-heading">

                    <div class="requisition-section-icon">
                        <i class="fas fa-align-left"></i>
                    </div>

                    <div>
                        <h5>Description</h5>
                        <p>Explain the purpose and scope of this cost centre.</p>
                    </div>

                </div>

            </div>


            <div class="row">

                <div class="col-md-12">

                    <div class="requisition-detail-item">

                        <label for="description"
                               class="requisition-detail-label">
                            Description
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            rows="3"
                            class="form-control requisition-input @error('description') is-invalid @enderror"
                            placeholder="Describe the activities or expenses covered by this cost centre."
                        >{{ old('description') }}</textarea>

                        @error('description')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================
             ADDITIONAL INFORMATION
             ======================================================== --}}
        <div class="requisition-section">

            <div class="requisition-section-header">

                <div class="requisition-section-heading">

                    <div class="requisition-section-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>

                    <div>
                        <h5>Additional Information</h5>
                        <p>Set the availability of this cost centre.</p>
                    </div>

                </div>

            </div>


            <div class="row">

                {{-- Status --}}
                <div class="col-md-4">

                    <div class="requisition-detail-item">

                        <span class="requisition-detail-label">
                            Cost Centre Status
                        </span>

                        <div class="requisition-state-panel">

                            <div class="requisition-state-main">

                                <div class="requisition-state-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>

                                <div class="requisition-state-text">

                                    <strong>
                                        Active
                                    </strong>

                                    <span>
                                        Available for budgeting and expenditure allocations.
                                    </span>

                                </div>

                            </div>

                            <div class="requisition-state-actions">

                                <label>

                                    <input
                                        type="checkbox"
                                        id="is_active"
                                        name="is_active"
                                        value="1"
                                        {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                                    >

                                    <span>
                                        Keep active
                                    </span>

                                </label>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>


        {{-- ========================================================
             BOTTOM ACTIONS
             ======================================================== --}}
        <div class="requisition-bottom-actions">

            <div class="requisition-bottom-actions-left">

                <a href="{{ route('admin.finance.cost-centres.index') }}"
                   class="requisition-cancel-button">

                    <i class="fas fa-times"></i>
                    <span>Cancel</span>

                </a>

            </div>


            <div class="requisition-bottom-actions-right">

                <button type="submit"
                        class="requisition-add-button">

                    <i class="fas fa-save"></i>
                    <span>Save Cost Centre</span>

                </button>

            </div>

        </div>

    </form>

</div>

@endsection