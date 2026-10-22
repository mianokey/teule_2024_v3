@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/stores.css') }}">
@endpush

@section('content')

<div class="store-requisition-page">

    {{-- =========================================================
         PAGE HEADER
         ========================================================= --}}
    <div class="requisition-page-header">

        <div class="requisition-header-content">

            <div class="requisition-header-icon">
                <i class="fa fa-chart-bar"></i>
            </div>

            <div>
                <div class="requisition-breadcrumb">
                    <span>Stores</span>
                    <span>/</span>
                    <span>Reports &amp; Insights</span>
                </div>

                <h1 class="requisition-page-title">
                    Stores Reports
                </h1>

                <p class="requisition-page-subtitle">
                    Analyse stock balances, review movements, and export reports.
                </p>
            </div>

        </div>

        <div class="requisition-header-right">

            <a
                href="{{ route('admin.store-stock.index') }}"
                class="requisition-cancel-button"
            >
                <i class="fa fa-arrow-left"></i>
                Current Stock
            </a>

        </div>

    </div>


    {{-- =========================================================
         REPORT FILTERS
         ========================================================= --}}
    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fa fa-sliders-h"></i>
                </div>

                <div>
                    <h5>
                        Report Configuration
                    </h5>

                    <p>
                        Select a report and apply filters to refine the results.
                    </p>
                </div>

            </div>

            <div class="requisition-count-badge">
                Filters
            </div>

        </div>


        <div class="requisition-details-body">

            <form
                method="GET"
                action="{{ route('admin.store-reports.index') }}"
            >

                <div class="row g-3 align-items-end">

                    {{-- REPORT TYPE --}}
                    <div class="col-lg-4 col-md-6">

                        <label for="report" class="requisition-field-label">
                            Report Type
                        </label>

                        <select
                            name="report"
                            id="report"
                            class="requisition-input"
                        >

                            <option
                                value="current-stock"
                                @selected($report === 'current-stock')
                            >
                                Current Stock Balances
                            </option>

                            <option
                                value="stock-movements"
                                @selected($report === 'stock-movements')
                            >
                                Stock Movement History
                            </option>

                        </select>

                    </div>


                    {{-- STORE --}}
                    <div class="col-lg-4 col-md-6">

                        <label for="store_id" class="requisition-field-label">
                            Store
                        </label>

                        <select
                            name="store_id"
                            id="store_id"
                            class="requisition-input"
                        >

                            <option value="">
                                All Stores
                            </option>

                            @foreach($stores as $store)

                                <option
                                    value="{{ $store->id }}"
                                    @selected(request('store_id') == $store->id)
                                >
                                    {{ $store->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- ITEM --}}
                    <div class="col-lg-4 col-md-6">

                        <label for="item_id" class="requisition-field-label">
                            Item
                        </label>

                        <select
                            name="item_id"
                            id="item_id"
                            class="requisition-input"
                        >

                            <option value="">
                                All Items
                            </option>

                            @foreach($items as $item)

                                <option
                                    value="{{ $item->id }}"
                                    @selected(request('item_id') == $item->id)
                                >
                                    {{ $item->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- VARIANT --}}
                    <div class="col-lg-4 col-md-6">

                        <label for="variant_id" class="requisition-field-label">
                            Item Variant
                        </label>

                        <select
                            name="variant_id"
                            id="variant_id"
                            class="requisition-input"
                        >

                            <option value="">
                                All Variants
                            </option>

                            @foreach($variants as $variant)

                                <option
                                    value="{{ $variant->id }}"
                                    @selected(request('variant_id') == $variant->id)
                                >
                                    {{ $variant->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- MOVEMENT TYPE --}}
                    <div
                        class="col-lg-4 col-md-6"
                        id="movement-type-field"
                    >

                        <label for="movement_type" class="requisition-field-label">
                            Movement Type
                        </label>

                        <select
                            name="movement_type"
                            id="movement_type"
                            class="requisition-input"
                        >

                            <option value="">
                                All Movement Types
                            </option>

                            @foreach([
                                'RECEIPT',
                                'ISSUE',
                                'RETURN_IN',
                                'RETURN_OUT',
                                'TRANSFER_IN',
                                'TRANSFER_OUT',
                                'ADJUSTMENT'
                            ] as $type)

                                <option
                                    value="{{ $type }}"
                                    @selected(request('movement_type') === $type)
                                >
                                    {{ str_replace('_', ' ', $type) }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- DATE FROM --}}
                    <div
                        class="col-lg-2 col-md-6"
                        id="date-from-field"
                    >

                        <label for="date_from" class="requisition-field-label">
                            Date From
                        </label>

                        <input
                            type="date"
                            name="date_from"
                            id="date_from"
                            class="requisition-input"
                            value="{{ request('date_from') }}"
                        >

                    </div>


                    {{-- DATE TO --}}
                    <div
                        class="col-lg-2 col-md-6"
                        id="date-to-field"
                    >

                        <label for="date_to" class="requisition-field-label">
                            Date To
                        </label>

                        <input
                            type="date"
                            name="date_to"
                            id="date_to"
                            class="requisition-input"
                            value="{{ request('date_to') }}"
                        >

                    </div>


                    {{-- FILTER ACTIONS --}}
                    <div class="col-12">

                        <div class="d-flex flex-wrap gap-2 mt-2">

                            <button
                                type="submit"
                                class="requisition-save-button"
                            >
                                <i class="fa fa-filter"></i>
                                Apply Filters
                            </button>

                            <a
                                href="{{ route('admin.store-reports.index', ['report' => $report]) }}"
                                class="requisition-cancel-button"
                            >
                                <i class="fa fa-undo"></i>
                                Reset Filters
                            </a>

                        </div>

                    </div>

                </div>

            </form>

        </div>

    </div>


    {{-- =========================================================
         REPORT RESULTS
         ========================================================= --}}
    <div class="requisition-section">

        {{-- SECTION HEADER --}}
        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fa fa-table"></i>
                </div>

                <div>

                    <h5>
                        {{ $report === 'current-stock'
                            ? 'Current Stock Balances'
                            : 'Stock Movement History'
                        }}
                    </h5>

                    <p>
                        {{ number_format($rows->count()) }}
                        {{ $rows->count() === 1 ? 'record' : 'records' }}

                        <span class="mx-1">·</span>

                        Generated {{ now()->format('d M Y, H:i') }}
                    </p>

                </div>

            </div>


            {{-- EXPORT ACTIONS --}}
            <div class="d-flex flex-wrap gap-2">

                @foreach([
                    'pdf' => 'PDF',
                    'xlsx' => 'Excel',
                    'csv' => 'CSV'
                ] as $format => $label)

                    <a
                        href="{{ route('admin.store-reports.export', array_merge(
                            ['format' => $format],
                            request()->except('page')
                        )) }}"
                        class="{{ $format === 'xlsx'
                            ? 'requisition-save-button'
                            : 'requisition-cancel-button'
                        }}"
                    >

                        @if($format === 'pdf')
                            <i class="fa fa-file-pdf"></i>
                        @elseif($format === 'xlsx')
                            <i class="fa fa-file-excel"></i>
                        @else
                            <i class="fa fa-file-csv"></i>
                        @endif

                        Export {{ $label }}

                    </a>

                @endforeach

            </div>

        </div>


        {{-- =====================================================
             RESULTS TABLE
             ===================================================== --}}
        <div class="requisition-list-table-wrapper">

            <table class="requisition-list-table">

                <thead>

                    <tr>

                        @if($report === 'current-stock')

                            <th style="min-width: 140px;">
                                Store
                            </th>

                            <th style="min-width: 180px;">
                                Item
                            </th>

                            <th style="min-width: 120px;">
                                Variant
                            </th>

                            <th style="min-width: 85px;">
                                Unit
                            </th>

                            <th style="min-width: 120px; text-align: right;">
                                Current Stock
                            </th>

                            <th style="min-width: 155px;">
                                Last Movement
                            </th>

                        @else

                            <th style="min-width: 150px;">
                                Date
                            </th>

                            <th style="min-width: 130px;">
                                Store
                            </th>

                            <th style="min-width: 165px;">
                                Item
                            </th>

                            <th style="min-width: 110px;">
                                Variant
                            </th>

                            <th style="min-width: 145px;">
                                Movement Type
                            </th>

                            <th style="min-width: 100px; text-align: right;">
                                Quantity
                            </th>

                            <th style="min-width: 110px; text-align: right;">
                                Balance After
                            </th>

                            <th style="min-width: 150px;">
                                Reference
                            </th>

                            <th style="min-width: 130px;">
                                Recorded By
                            </th>

                            <th style="min-width: 180px;">
                                Notes
                            </th>

                        @endif

                    </tr>

                </thead>


                <tbody>

                    @forelse($rows as $row)

                        <tr>

                            @foreach($row as $key => $value)

                                <td
                                    @if(in_array($key, [
                                        'Current Stock',
                                        'Quantity',
                                        'Balance After'
                                    ], true))
                                        style="text-align: right; font-variant-numeric: tabular-nums;"
                                    @endif
                                >
                                    {{ $value ?? '—' }}
                                </td>

                            @endforeach

                        </tr>

                    @empty

                        <tr>

                            <td colspan="{{ $report === 'current-stock' ? 6 : 10 }}">

                                <div class="requisition-table-empty">

                                    <div class="requisition-empty-icon">
                                        <i class="fa fa-chart-bar"></i>
                                    </div>

                                    <h5>
                                        No Matching Records
                                    </h5>

                                    <p>
                                        No data was found for the selected filters.
                                        Try adjusting your report criteria.
                                    </p>

                                    <a
                                        href="{{ route('admin.store-reports.index', ['report' => $report]) }}"
                                        class="requisition-cancel-button"
                                    >
                                        <i class="fa fa-undo"></i>
                                        Clear Filters
                                    </a>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</div>


{{-- =============================================================
     REPORT FILTER BEHAVIOUR
     ============================================================= --}}

<script>
document.addEventListener('DOMContentLoaded', function () {

    const report = document.getElementById('report');
    const item = document.getElementById('item_id');
    const variant = document.getElementById('variant_id');

    const movementFields = [
        document.getElementById('movement-type-field'),
        document.getElementById('date-from-field'),
        document.getElementById('date-to-field')
    ];

    function updateReportFields() {
        const isMovementReport =
            report.value === 'stock-movements';

        movementFields.forEach(function (field) {
            if (field) {
                field.style.display =
                    isMovementReport ? '' : 'none';
            }
        });
    }

    report.addEventListener('change', updateReportFields);
    updateReportFields();

    // Reload the report when the selected item changes.
    // The controller will then load that item's variants.
    item.addEventListener('change', function () {
        if (variant) {
            variant.value = '';
        }

        item.form.requestSubmit();
    });

});
</script>



@endsection