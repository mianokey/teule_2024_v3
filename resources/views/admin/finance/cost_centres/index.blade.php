
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
                <i class="fas fa-wallet"></i>
            </div>

            <div>
                <div class="requisition-breadcrumb">
                    <span>Finance</span>
                    <i class="fas fa-chevron-right"></i>
                    <span>Cost Centres</span>
                </div>

                <h1 class="requisition-page-title">
                    Cost Centres
                </h1>

                <p class="requisition-page-subtitle">
                    Create and manage departmental cost centres for budgeting and expenditure tracking.
                </p>
            </div>

        </div>

        <div class="requisition-header-right">

            <a href="{{ route('admin.finance.cost-centres.create') }}"
               class="requisition-add-button">
                <i class="fas fa-plus"></i>
                <span>New Cost Centre</span>
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
                        Find cost centres by name, code or department.
                    </p>
                </div>

            </div>

        </div>

        <div class="requisition-section-body">

            <form method="GET"
                  action="{{ route('admin.finance.cost-centres.index') }}">

                <div class="row">

                    {{-- Search --}}
                    <div class="col-md-5">

                        <div class="requisition-detail-item">

                            <label class="requisition-detail-label">
                                Search
                            </label>

                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                class="form-control requisition-input"
                                placeholder="Cost centre name or code"
                            >

                        </div>

                    </div>

                    {{-- Department --}}
                    <div class="col-md-3">

                        <div class="requisition-detail-item">

                            <label class="requisition-detail-label">
                                Department
                            </label>

                            <select
                                name="department_id"
                                class="form-control requisition-input"
                            >

                                <option value="">
                                    All Departments
                                </option>

                                @foreach($departments as $department)

                                    <option
                                        value="{{ $department->id }}"
                                        {{ (string) request('department_id') === (string) $department->id ? 'selected' : '' }}
                                    >
                                        {{ $department->name }}
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

                                <option value="active"
                                    {{ request('status') === 'active' ? 'selected' : '' }}>
                                    Active
                                </option>

                                <option value="inactive"
                                    {{ request('status') === 'inactive' ? 'selected' : '' }}>
                                    Inactive
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
                                    href="{{ route('admin.finance.cost-centres.index') }}"
                                    class="requisition-cancel-button"
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
         COST CENTRE LIST
         ============================================================ --}}
    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fas fa-list-alt"></i>
                </div>

                <div>
                    <h5>Cost Centre List</h5>

                    <p>
                        Cost centres currently registered under the organisation's departments.
                    </p>
                </div>

            </div>

            @if($costCentres->count())

                <span class="requisition-count-badge">
                    {{ $costCentres->total() }}
                    {{ $costCentres->total() === 1 ? 'Cost Centre' : 'Cost Centres' }}
                </span>

            @endif

        </div>

        @if($costCentres->count())

            <div class="requisition-list-table-wrapper">

                <table class="requisition-list-table">

                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Cost Centre</th>
                            <th>Code</th>
                            <th>Department</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($costCentres as $index => $costCentre)

                            <tr>

                                {{-- Number --}}
                                <td>
                                    <div class="requisition-list-row-number">
                                        {{ $costCentres->firstItem() + $index }}
                                    </div>
                                </td>

                                {{-- Cost Centre --}}
                                <td>

                                    <div class="requisition-list-number">
                                        {{ $costCentre->name }}
                                    </div>

                                    <div class="requisition-list-meta">
                                        Created {{ $costCentre->created_at?->format('d M Y') ?? '—' }}
                                    </div>

                                </td>

                                {{-- Code --}}
                                <td>
                                    <strong>{{ $costCentre->code }}</strong>
                                </td>

                                {{-- Department --}}
                                <td>
                                    <div class="requisition-list-purpose">
                                        {{ $costCentre->department->name ?? '—' }}
                                    </div>
                                </td>

                                {{-- Description --}}
                                <td>
                                    {{ $costCentre->description ?: '—' }}
                                </td>

                                {{-- Status --}}
                                <td>

                                    @if($costCentre->is_active)

                                        <span class="stores-badge stores-badge-success">
                                            Active
                                        </span>

                                    @else

                                        <span class="stores-badge stores-badge-secondary">
                                            Inactive
                                        </span>

                                    @endif

                                </td>

                                {{-- Actions --}}
                                <td>

                                    <div class="requisition-list-actions">

                                        {{-- Edit --}}
                                        <a
                                            href="{{ route('admin.finance.cost-centres.edit', $costCentre) }}"
                                            class="requisition-list-action"
                                            title="Edit Cost Centre"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        {{-- Activate / Deactivate --}}
                                        <form
                                            method="POST"
                                            action="{{ route('admin.finance.cost-centres.toggle-status', $costCentre) }}"
                                            class="d-inline"
                                        >

                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="requisition-list-action"
                                                title="{{ $costCentre->is_active ? 'Deactivate Cost Centre' : 'Activate Cost Centre' }}"
                                                onclick="return confirm('{{ $costCentre->is_active ? 'Deactivate' : 'Activate' }} this cost centre?');"
                                            >

                                                @if($costCentre->is_active)
                                                    <i class="fas fa-toggle-on"></i>
                                                @else
                                                    <i class="fas fa-toggle-off"></i>
                                                @endif

                                            </button>

                                        </form>

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
            @if($costCentres->total() > 0)

                <div class="stores-pagination-section">

                    <div class="stores-pagination-info">

                        <div class="stores-pagination-info-icon">
                            <i class="fa fa-layer-group"></i>
                        </div>

                        <div class="stores-pagination-info-text">

                            <span class="stores-pagination-info-label">
                                Showing cost centres
                            </span>

                            <strong>
                                {{ $costCentres->firstItem() ?? 0 }}
                                –
                                {{ $costCentres->lastItem() ?? 0 }}
                            </strong>

                            <span class="stores-pagination-info-total">
                                of {{ number_format($costCentres->total()) }} total
                            </span>

                        </div>

                    </div>

                    @if($costCentres->hasPages())

                        <div class="stores-pagination-controls">
                            {{ $costCentres->onEachSide(2)->withQueryString()->links('pagination::bootstrap-5') }}
                        </div>

                    @endif

                </div>

            @endif

        @else

            <div class="requisition-table-empty">

                <div class="requisition-empty-icon">
                    <i class="fas fa-wallet"></i>
                </div>

                <h5>No Cost Centres Found</h5>

                <p>
                    No cost centres match your current search or filter.
                </p>

                <a
                    href="{{ route('admin.finance.cost-centres.create') }}"
                    class="requisition-add-button"
                >
                    <i class="fas fa-plus"></i>
                    Create First Cost Centre
                </a>

            </div>

        @endif

    </div>

</div>

@endsection
