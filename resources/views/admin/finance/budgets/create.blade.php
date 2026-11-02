@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/stores.css?v=2.0.0') }}">
@endpush

@section('content')

<div class="store-requisition-page">

    <div class="requisition-page-header">
        <div class="requisition-header-content">
            <div class="requisition-header-icon">
                <i class="fas fa-plus-circle"></i>
            </div>

            <div>
                <div class="requisition-breadcrumb">
                    Finance <span>/</span>
                    <a href="{{ route('admin.finance.budgets.index') }}">Monthly Budgets</a>
                    <span>/</span> Create
                </div>

                <h1 class="requisition-page-title">Create Monthly Budget</h1>

                <p class="requisition-page-subtitle">
                    Enter the requested budget for a cost centre and financial period.
                </p>
            </div>
        </div>

        <div class="requisition-header-right">
            <a href="{{ route('admin.finance.budgets.index') }}"
               class="requisition-add-button">
                <i class="fas fa-arrow-left"></i> Back to Budgets
            </a>
        </div>
    </div>

    <x-message />

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

    @if($financePeriods->isEmpty())
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            There are no open financial periods available.
            Please create an open financial period before entering a budget.
        </div>
    @endif

    @if($costCentres->isEmpty())
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            There are no active cost centres available.
            Please create or activate a cost centre before entering a budget.
        </div>
    @endif

    <form method="POST" action="{{ route('admin.finance.budgets.store') }}">
        @csrf

        <div class="requisition-section">

            <div class="requisition-section-header">
                <div class="requisition-section-heading">
                    <div class="requisition-section-icon">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </div>

                    <div>
                        <h5>Budget Information</h5>
                        <p>
                            Specify the financial period, cost centre and requested amount.
                        </p>
                    </div>
                </div>
            </div>

            <div class="requisition-section-body">
                <div class="row">

                    <div class="col-md-3">
                        <div class="requisition-detail-item">
                            <label class="requisition-detail-label">
                                Financial Period <span class="text-danger">*</span>
                            </label>

                            <select name="finance_period_id"
                                    class="form-control requisition-input @error('finance_period_id') is-invalid @enderror"
                                    required>
                                <option value="">Select Financial Period</option>

                                @foreach($financePeriods as $period)
                                    <option value="{{ $period->id }}"
                                        {{ old('finance_period_id', request('finance_period_id')) == $period->id ? 'selected' : '' }}>
                                        {{ $period->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('finance_period_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="requisition-detail-item">
                            <label class="requisition-detail-label">
                                Cost Centre <span class="text-danger">*</span>
                            </label>

                            <select name="cost_centre_id"
                                    class="form-control requisition-input @error('cost_centre_id') is-invalid @enderror"
                                    required>
                                <option value="">Select Cost Centre</option>

                                @foreach($costCentres as $costCentre)
                                    <option value="{{ $costCentre->id }}"
                                        {{ old('cost_centre_id') == $costCentre->id ? 'selected' : '' }}>
                                        {{ $costCentre->department->name ?? 'No Department' }}
                                        - {{ $costCentre->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('cost_centre_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="requisition-detail-item">
                            <label class="requisition-detail-label">
                                Requested Budget Amount (KES) <span class="text-danger">*</span>
                            </label>

                            <input type="number"
                                   name="requested_amount"
                                   class="form-control requisition-input @error('requested_amount') is-invalid @enderror"
                                   value="{{ old('requested_amount') }}"
                                   min="0.01"
                                   step="0.01"
                                   placeholder="Enter requested amount"
                                   required>

                            @error('requested_amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="requisition-detail-item">
                            <label class="requisition-detail-label">Notes</label>

                            <textarea name="notes"
                                      class="form-control requisition-input @error('notes') is-invalid @enderror"
                                      rows="4"
                                      placeholder="Explain the purpose or justification for this budget...">{{ old('notes') }}</textarea>

                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="requisition-section">
            <div class="requisition-section-header">
                <div class="requisition-section-heading">
                    <div class="requisition-section-icon">
                        <i class="fas fa-info-circle"></i>
                    </div>

                    <div>
                        <h5>Budget Approval Information</h5>
                        <p>This entry will initially be saved as a <strong>Draft</strong>.
                    The requested amount is not yet an approved budget allocation.
                    It will only become approved budget authority after the required approval process.
                    Actual funds received will be recorded and tracked separately.</p>
                    </div>
                </div>
            </div>

           
        </div>


                <div class="requisition-bottom-actions">

            <div class="requisition-bottom-actions-right">

                <a href="{{ route('admin.finance.budgets.index') }}"
               class="requisition-cancel-button">
                <i class="fas fa-times"></i> Cancel
            </a>

            </div>


            <div class="requisition-bottom-actions-right">

                <button type="submit"
                    class="requisition-add-button"
                    {{ $financePeriods->isEmpty() || $costCentres->isEmpty() ? 'disabled' : '' }}>
                <i class="fas fa-save"></i> Save Budget
            </button>
            </div>

        </div>


       
    </form>

</div>

@endsection