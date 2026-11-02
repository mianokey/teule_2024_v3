@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/stores.css?v=2.0.0') }}">
@endpush

@section('content')

<div class="store-requisition-page">

    {{-- ============================================================
        PAGE HEADER
    ============================================================ --}}
    <div class="requisition-page-header">

        <div class="requisition-header-content">

            <div class="requisition-header-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>

            <div>
                <div class="requisition-breadcrumb">
                    <span>Finance</span>
                    <i class="fas fa-chevron-right"></i>
                    <span>Finance Periods</span>
                </div>

                <h1 class="requisition-page-title">
                    Finance Periods
                </h1>

                <p class="requisition-page-subtitle">
                    Create and manage monthly financial periods for budgeting and expenditure tracking.
                </p>
            </div>

        </div>

        <div class="requisition-header-right">

            <a href="{{ route('admin.finance.periods.create') }}"
               class="requisition-add-button">
                <i class="fas fa-plus"></i>
                <span>New Finance Period</span>
            </a>

        </div>

    </div>

    {{-- ============================================================
        MESSAGES
    ============================================================ --}}
    <x-message></x-message>

    {{-- ============================================================
        FILTERS
    ============================================================ --}}
    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fas fa-filter"></i>
                </div>

                <div>
                    <h5>Search &amp; Filter</h5>
                    <p>
                        Find financial periods by year, month or status.
                    </p>
                </div>

            </div>

        </div>

        <div class="requisition-section-body">

            <form method="GET"
                  action="{{ route('admin.finance.periods.index') }}">

                <div class="row">

                    {{-- Search --}}
                    <div class="col-md-6">

                        <div class="requisition-detail-item">

                            <label class="requisition-detail-label">
                                Search
                            </label>

                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                class="form-control requisition-input"
                                placeholder="Search year or notes"
                            >

                        </div>

                    </div>

                    {{-- Status --}}
                    <div class="col-md-3">

                        <div class="requisition-detail-item">

                            <label class="requisition-detail-label">
                                Status
                            </label>

                            <select
                                name="status"
                                class="form-control requisition-input"
                            >

                                <option value="">All Statuses</option>

                                <option value="open"
                                    {{ request('status') === 'open' ? 'selected' : '' }}>
                                    Open
                                </option>

                                <option value="closed"
                                    {{ request('status') === 'closed' ? 'selected' : '' }}>
                                    Closed
                                </option>

                            </select>

                        </div>

                    </div>

                    {{-- Actions --}}
                    <div class="col-md-3">

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
                                    href="{{ route('admin.finance.periods.index') }}"
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

    {{-- ============================================================
        FINANCE PERIOD LIST
    ============================================================ --}}
    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>

                <div>
                    <h5>Financial Period List</h5>

                    <p>
                        Monthly financial periods registered in the system.
                    </p>
                </div>

            </div>

            @if($financePeriods->count())

                <span class="requisition-count-badge">
                    {{ number_format($financePeriods->total()) }}
                    {{ $financePeriods->total() === 1 ? 'Period' : 'Periods' }}
                </span>

            @endif

        </div>

        @if($financePeriods->count())

            <div class="requisition-list-table-wrapper">

                <table class="requisition-list-table">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Financial Period</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Cost Centre Budgets</th>
                            <th>Created By</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($financePeriods as $index => $period)

                            <tr>

                                {{-- Number --}}
                                <td>
                                    <div class="requisition-list-row-number">
                                        {{ $financePeriods->firstItem() + $index }}
                                    </div>
                                </td>

                                {{-- Financial Period --}}
                                <td>

                                    <div class="requisition-list-number">
                                        {{ \Carbon\Carbon::create($period->year, $period->month, 1)->format('F Y') }}
                                    </div>

                                    <div class="requisition-list-meta">
                                        Created {{ $period->created_at?->format('d M Y') ?? '—' }}
                                    </div>

                                </td>

                                {{-- Start Date --}}
                                <td>
                                    {{ $period->starts_on?->format('d M Y') ?? '—' }}
                                </td>

                                {{-- End Date --}}
                                <td>
                                    {{ $period->ends_on?->format('d M Y') ?? '—' }}
                                </td>

                                {{-- Budgets --}}
                                <td>
                                    <strong>
                                        {{ number_format($period->budgets_count) }}
                                    </strong>
                                </td>

                                {{-- Created By --}}
                                <td>
                                    <div class="requisition-list-purpose">
                                        {{ $period->creator?->name ?? '—' }}
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td>

                                    @if($period->status === 'open')

                                        <span class="stores-badge stores-badge-success">
                                            Open
                                        </span>

                                    @else

                                        <span class="stores-badge stores-badge-secondary">
                                            Closed
                                        </span>

                                    @endif

                                </td>

                                {{-- Actions --}}
                                <td>

                                    <div class="requisition-list-actions">

                                        @if($period->status === 'open')

                                            {{-- Close Period --}}
                                            <form
                                                method="POST"
                                                action="{{ route('admin.finance.periods.close', $period) }}"
                                                class="d-inline"
                                            >

                                                @csrf
                                                @method('PATCH')

                                                <button
                                                    type="submit"
                                                    class="requisition-list-action"
                                                    title="Close Finance Period"
                                                    onclick="return confirm('Are you sure you want to close this finance period?');"
                                                >
                                                    <i class="fas fa-lock"></i>
                                                </button>

                                            </form>

                                        @else

                                            {{-- Reopen Period --}}
                                            <form
                                                method="POST"
                                                action="{{ route('admin.finance.periods.reopen', $period) }}"
                                                class="d-inline"
                                            >

                                                @csrf
                                                @method('PATCH')

                                                <button
                                                    type="submit"
                                                    class="requisition-list-action"
                                                    title="Reopen Finance Period"
                                                    onclick="return confirm('Reopen this finance period?');"
                                                >
                                                    <i class="fas fa-lock-open"></i>
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

            {{-- ====================================================
                PAGINATION
            ==================================================== --}}
            @if($financePeriods->total() > 0)

                <div class="stores-pagination-section">

                    <div class="stores-pagination-info">

                        <div class="stores-pagination-info-icon">
                            <i class="fa fa-layer-group"></i>
                        </div>

                        <div class="stores-pagination-info-text">

                            <span class="stores-pagination-info-label">
                                Showing financial periods
                            </span>

                            <strong>
                                {{ $financePeriods->firstItem() ?? 0 }}
                                –
                                {{ $financePeriods->lastItem() ?? 0 }}
                            </strong>

                            <span class="stores-pagination-info-total">
                                of {{ number_format($financePeriods->total()) }} total
                            </span>

                        </div>

                    </div>

                    @if($financePeriods->hasPages())

                        <div class="stores-pagination-controls">
                            {{ $financePeriods->onEachSide(2)->withQueryString()->links('pagination::bootstrap-5') }}
                        </div>

                    @endif

                </div>

            @endif

        @else

            <div class="requisition-table-empty">

                <div class="requisition-empty-icon">
                    <i class="fas fa-calendar-alt"></i>
                </div>

                <h5>No Finance Periods Found</h5>

                <p>
                    No financial periods match your current search or filter.
                </p>

                <a
                    href="{{ route('admin.finance.periods.create') }}"
                    class="requisition-add-button"
                >
                    <i class="fas fa-plus"></i>
                    Create First Finance Period
                </a>

            </div>

        @endif

    </div>

</div>

@endsection