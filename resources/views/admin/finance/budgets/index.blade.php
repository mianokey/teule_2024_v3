@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/stores.css?v=2.0.0') }}">
@endpush

@section('content')

<div class="store-requisition-page">

    {{-- PAGE HEADER --}}
    <div class="requisition-page-header">

        <div class="requisition-header-content">

            <div class="requisition-header-icon">
                <i class="fas fa-wallet"></i>
            </div>

            <div>
                <div class="requisition-breadcrumb">
                    <span>Finance</span>
                    <i class="fas fa-chevron-right"></i>
                    <span>Monthly Budgets</span>
                </div>

                <h1 class="requisition-page-title">
                    Monthly Budgets
                </h1>

                <p class="requisition-page-subtitle">
                    Manage monthly budget allocations across organisational cost centres.
                </p>
            </div>

        </div>

        <div class="requisition-header-right">

            <a href="{{ route('admin.finance.budgets.create') }}"
               class="requisition-add-button">
                <i class="fas fa-plus"></i>
                <span>New Budget</span>
            </a>

        </div>

    </div>

    {{-- MESSAGES --}}
    <x-message></x-message>

    {{-- FILTERS --}}
    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fas fa-filter"></i>
                </div>

                <div>
                    <h5>Search &amp; Filter</h5>
                    <p>
                        Find budgets by financial period, cost centre or approval status.
                    </p>
                </div>

            </div>

        </div>

        <div class="requisition-section-body">

            <form method="GET"
                  action="{{ route('admin.finance.budgets.index') }}">

                <div class="row">

                    {{-- Financial Period --}}
                    <div class="col-md-4">

                        <div class="requisition-detail-item">

                            <label class="requisition-detail-label">
                                Financial Period
                            </label>

                            <select
                                name="finance_period_id"
                                class="form-control requisition-input"
                            >

                                <option value="">
                                    All Financial Periods
                                </option>

                                @foreach($financePeriods as $period)

                                    <option
                                        value="{{ $period->id }}"
                                        {{ (string) request('finance_period_id') === (string) $period->id ? 'selected' : '' }}
                                    >
                                        {{ $period->name }}
                                    </option>

                                @endforeach

                            </select>

                        </div>

                    </div>

                    {{-- Cost Centre --}}
                    <div class="col-md-4">

                        <div class="requisition-detail-item">

                            <label class="requisition-detail-label">
                                Cost Centre
                            </label>

                            <select
                                name="cost_centre_id"
                                class="form-control requisition-input"
                            >

                                <option value="">
                                    All Cost Centres
                                </option>

                                @foreach($costCentres as $costCentre)

                                    <option
                                        value="{{ $costCentre->id }}"
                                        {{ (string) request('cost_centre_id') === (string) $costCentre->id ? 'selected' : '' }}
                                    >
                                        {{ $costCentre->name }}
                                        ({{ $costCentre->department->name ?? 'No Department' }})
                                    </option>

                                @endforeach

                            </select>

                        </div>

                    </div>

                    {{-- Status --}}
                    <div class="col-md-2">

                        <div class="requisition-detail-item">

                            <label class="requisition-detail-label">
                                Status
                            </label>

                            <select
                                name="status"
                                class="form-control requisition-input"
                            >

<option value="">All Statuses</option>

<option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>
    Draft
</option>

<option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>
    Submitted
</option>

<option value="returned" {{ request('status') === 'returned' ? 'selected' : '' }}>
    Returned for Adjustment
</option>

<option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>
    Approved
</option>

<option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>
    Rejected
</option>


                            </select>

                        </div>

                    </div>

                    {{-- Actions --}}
                    <div class="col-md-2">

                        <div class="requisition-detail-item">

                            <label class="requisition-detail-label">
                                &nbsp;
                            </label>

                            <div class="d-flex gap-2">

                                <button
                                    type="submit"
                                    class="requisition-add-button"
                                >
                                    <i class="fas fa-search"></i>
                                    Filter
                                </button>

                                <a
                                    href="{{ route('admin.finance.budgets.index') }}"
                                    class="requisition-cancel-button"
                                    title="Clear Filters"
                                >
                                    <i class="fas fa-times"></i>
                                </a>

                            </div>

                        </div>

                    </div>

                </div>

            </form>

        </div>

    </div>

    {{-- MONTHLY BUDGET LIST --}}
    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fas fa-list-alt"></i>
                </div>

                <div>
                    <h5>Monthly Budget List</h5>

                    <p>
                        Monthly budgets registered against the organisation's cost centres.
                    </p>
                </div>

            </div>

            @if($budgets->count())

                <span class="requisition-count-badge">
                    {{ $budgets->total() }}
                    {{ $budgets->total() === 1 ? 'Budget' : 'Budgets' }}
                </span>

            @endif

        </div>

        @if($budgets->count())

            <div class="requisition-list-table-wrapper">

                <table class="requisition-list-table">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Financial Period</th>
                            <th>Cost Centre</th>
                            <th>Requested Amount</th>
                            <th>Approved Amount</th>
                            <th>Carry Forward</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($budgets as $index => $budget)

                        @php
    $status = strtolower($budget->status ?? 'draft');

    $isOwner = (int) $budget->created_by === (int) auth()->id();

    $isEditable = in_array($status, ['draft', 'returned'], true)
        && $isOwner
        && $budget->period?->status === 'open';

    $isSubmittable = $isEditable;

    $stageLabels = [
        'finance' => 'Finance',
        'dcm' => 'DCM',
        'cm' => 'CM',
    ];

    $statusLabel = match ($status) {
        'submitted' => 'Pending ' . ($stageLabels[$budget->approval_stage] ?? 'Approval'),
        'returned' => 'Returned for Adjustment',
        default => ucfirst($status),
    };
@endphp


                            <tr>

                                {{-- Number --}}
                                <td>
                                    <div class="requisition-list-row-number">
                                        {{ $budgets->firstItem() + $index }}
                                    </div>
                                </td>

                                {{-- Financial Period --}}
                                <td>

                                    <div class="requisition-list-number">
                                        {{ $budget->period->name ?? '—' }}
                                    </div>

                                    <div class="requisition-list-meta">
                                        {{ $budget->period?->starts_on?->format('d M Y') ?? '—' }}
                                        –
                                        {{ $budget->period?->ends_on?->format('d M Y') ?? '—' }}
                                    </div>

                                </td>

                                {{-- Cost Centre --}}
                                <td>

                                    <div class="requisition-list-number">
                                        {{ $budget->costCentre->name ?? '—' }}
                                    </div>

                                    <div class="requisition-list-meta">
                                        {{ $budget->costCentre->department->name ?? 'No Department' }}
                                    </div>

                                </td>

                                {{-- Requested Amount --}}
                                <td>
                                    <strong>
                                        KES {{ number_format((float) $budget->requested_amount, 2) }}
                                    </strong>
                                </td>

                                {{-- Approved Amount --}}
                                <td>
                                    KES {{ number_format((float) ($budget->approved_amount ?? 0), 2) }}
                                </td>

                                {{-- Carry Forward --}}
                                <td>
                                    KES {{ number_format((float) $budget->carried_forward_amount, 2) }}
                                </td>

{{-- Status --}}
<td>
    @if($status === 'approved')

        <span class="stores-badge stores-badge-success">
            <i class="fas fa-check-circle mr-1"></i>
            Approved
        </span>

    @elseif($status === 'submitted')

        <span class="stores-badge stores-badge-info">
            <i class="fas fa-clock mr-1"></i>
            {{ $statusLabel }}
        </span>

    @elseif($status === 'returned')

        <span class="stores-badge stores-badge-warning">
            <i class="fas fa-undo mr-1"></i>
            Returned for Adjustment
        </span>

    @elseif($status === 'rejected')

        <span class="stores-badge stores-badge-danger">
            <i class="fas fa-times-circle mr-1"></i>
            Rejected
        </span>

    @elseif($status === 'draft')

        <span class="stores-badge stores-badge-secondary">
            <i class="fas fa-pencil-alt mr-1"></i>
            Draft
        </span>

    @else

        <span class="stores-badge stores-badge-secondary">
            {{ ucfirst($status) }}
        </span>

    @endif
</td>


{{-- Actions --}}
<td>

    <div class="requisition-list-actions">

        {{-- ALWAYS VIEW --}}
        <a
            href="{{ route('admin.finance.budgets.show', $budget) }}"
            class="requisition-list-action primary"
            title="View Budget Details"
        >
            <i class="fas fa-eye"></i>
        </a>


        {{-- EDIT DRAFT OR RETURNED BUDGET --}}
        @if($isEditable)

            <a
                href="{{ route('admin.finance.budgets.edit', $budget) }}"
                class="requisition-list-action"
                title="Edit Budget"
            >
                <i class="fas fa-edit"></i>
            </a>

        @endif


        {{-- SUBMIT DRAFT OR RETURNED BUDGET --}}
        @if($isSubmittable)

            <form
                method="POST"
                action="{{ route('admin.finance.budgets.submit', $budget) }}"
                class="d-inline"
                onsubmit="return confirm(
                    'Submit this monthly budget for approval? You will no longer be able to edit it until it is returned.'
                );"
            >

                @csrf

                <button
                    type="submit"
                    class="requisition-list-action success"
                    title="Submit Budget for Approval"
                >
                    <i class="fas fa-paper-plane"></i>
                </button>

            </form>

        @endif

    </div>

</td>


                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

            {{-- PAGINATION --}}
            @if($budgets->total() > 0)

                <div class="stores-pagination-section">

                    <div class="stores-pagination-info">

                        <div class="stores-pagination-info-icon">
                            <i class="fa fa-layer-group"></i>
                        </div>

                        <div class="stores-pagination-info-text">

                            <span class="stores-pagination-info-label">
                                Showing monthly budgets
                            </span>

                            <strong>
                                {{ $budgets->firstItem() ?? 0 }}
                                –
                                {{ $budgets->lastItem() ?? 0 }}
                            </strong>

                            <span class="stores-pagination-info-total">
                                of {{ number_format($budgets->total()) }} total
                            </span>

                        </div>

                    </div>

                    @if($budgets->hasPages())

                        <div class="stores-pagination-controls">
                            {{ $budgets->onEachSide(2)->withQueryString()->links('pagination::bootstrap-5') }}
                        </div>

                    @endif

                </div>

            @endif

        @else

            {{-- Empty State --}}
            <div class="requisition-table-empty">

                <div class="requisition-empty-icon">
                    <i class="fas fa-wallet"></i>
                </div>

                <h5>No Monthly Budgets Found</h5>

                <p>
                    No budgets match your current search or filter.
                </p>

                <a
                    href="{{ route('admin.finance.budgets.create') }}"
                    class="requisition-add-button"
                >
                    <i class="fas fa-plus"></i>
                    Create First Budget
                </a>

            </div>

        @endif

    </div>

</div>

@endsection
