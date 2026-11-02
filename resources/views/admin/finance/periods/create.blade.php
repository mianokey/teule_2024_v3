@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/stores.css?v=2.0.0') }}">
@endpush

@section('content')

<div class="store-requisition-page">

    {{-- Page Header --}}
    <div class="requisition-page-header">
        <div class="requisition-header-content">
            <div class="requisition-header-icon">
                <i class="fas fa-calendar-plus"></i>
            </div>

            <div>
                <div class="requisition-breadcrumb">
                    Finance <span>/</span>
                    <a href="{{ route('admin.finance.periods.index') }}">Finance Periods</a>
                    <span>/</span> Create
                </div>

                <h1 class="requisition-page-title">Create Finance Period</h1>

                <p class="requisition-page-subtitle">
                    Set up a new monthly financial period for budgeting and expenditure tracking.
                </p>
            </div>
        </div>

        <div class="requisition-header-right">
            <a href="{{ route('admin.finance.periods.index') }}"
               class="requisition-secondary-button">
                <i class="fas fa-arrow-left"></i>
                Back to Periods
            </a>
        </div>
    </div>

    <x-message />

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Please correct the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.finance.periods.store') }}">
        @csrf

        {{-- Period Information --}}
        <div class="requisition-section">

            <div class="requisition-section-header">
                <div class="requisition-section-heading">
                    <div class="requisition-section-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>

                    <div>
                        <h5>Period Information</h5>
                        <p>
                            Define the month and year covered by this financial period.
                        </p>
                    </div>
                </div>
            </div>

            <div class="requisition-section-body">
                <div class="row">

                    {{-- Financial Year --}}
                    <div class="col-md-6">
                        <div class="requisition-detail-item">
                            <label class="requisition-detail-label">
                                Financial Year <span class="text-danger">*</span>
                            </label>

                            <input type="number"
                                   name="year"
                                   class="form-control requisition-input @error('year') is-invalid @enderror"
                                   min="2020"
                                   max="2100"
                                   value="{{ old('year', now()->year) }}"
                                   placeholder="Enter financial year"
                                   required>

                            @error('year')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Financial Month --}}
                    <div class="col-md-6">
                        <div class="requisition-detail-item">
                            <label class="requisition-detail-label">
                                Financial Month <span class="text-danger">*</span>
                            </label>

                            <select name="month"
                                    class="form-control requisition-input @error('month') is-invalid @enderror"
                                    required>
                                <option value="">Select Month</option>

                                @foreach(range(1, 12) as $month)
                                    <option value="{{ $month }}"
                                        {{ old('month', now()->month) == $month ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::create(null, $month, 1)->format('F') }}
                                    </option>
                                @endforeach
                            </select>

                            @error('month')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    {{-- Notes --}}
                    <div class="col-md-12">
                        <div class="requisition-detail-item">
                            <label class="requisition-detail-label">
                                Notes
                            </label>

                            <textarea name="notes"
                                      class="form-control requisition-input @error('notes') is-invalid @enderror"
                                      rows="4"
                                      placeholder="Enter any optional notes about this financial period...">{{ old('notes') }}</textarea>

                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                </div>
            </div>

        </div>

        {{-- Form Actions --}}
        <div class="requisition-bottom-actions">
            <a href="{{ route('admin.finance.periods.index') }}"
               class="requisition-cancel-button">
                <i class="fas fa-times"></i>
                Cancel
            </a>

            <button type="submit" class="requisition-add-button">
                <i class="fas fa-save"></i>
                Save Finance Period
            </button>
        </div>

    </form>

</div>

@endsection
