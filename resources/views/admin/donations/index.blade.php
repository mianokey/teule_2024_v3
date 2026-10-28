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
                <i class="fa fa-hand-holding-heart"></i>
            </div>

            <div>

                <div class="requisition-breadcrumb">
                    <span>Donations</span>
                    <span>/</span>
                    <span>Donation Records</span>
                </div>

                <h1 class="requisition-page-title">
                    All Donations
                </h1>

                <p class="requisition-page-subtitle">
                    View and manage recorded donations and their details.
                </p>

            </div>

        </div>


        <div class="requisition-header-right">

            <a href="{{ route('admin.donations.create') }}" class="requisition-add-button">
                <i class="fa fa-plus"></i>
                Record Donation
            </a>

        </div>

    </div>



    {{-- =========================================================
    MESSAGES
    ========================================================= --}}
    <x-message></x-message>




{{-- ADVANCED DONATION FILTERS --}}
<div class="requisition-section donation-filters-section">

    <details class="donation-filters-details"
        {{ request()->hasAny(['search', 'date_from', 'date_to', 'donor_id', 'type', 'classification', 'source', 'amount_min', 'amount_max', 'currency', 'receiving_status']) ? 'open' : '' }}>

        <summary class="requisition-section-header donation-filters-summary">
            <div class="requisition-section-heading">
                <div class="requisition-section-icon">
                    <i class="fa fa-filter"></i>
                </div>

                <div>
                    <h5>Search & Filter Donations currently</h5>
                    <p>Click to expand or collapse the donation filters.</p>
                </div>
            </div>

            <i class="fa fa-chevron-down donation-filters-chevron"></i>
        </summary>

        <div class="requisition-details-body donation-filters-body">

           <div class="requisition-section">

 

    <div class="requisition-details-body">

        <form method="GET" action="{{ route('admin.donations.index') }}">

            {{-- General search --}}
            <div class="row mb-3">
                <div class="col-md-2">
                    <label class="requisition-field-label">Search</label>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        class="requisition-input"
                        placeholder="Donation number, donor, purpose, source, type..."
                    >
                </div>
                
                <div class="col-md-2">
                    <label class="requisition-field-label">Date From</label>
                    <input
                        type="date"
                        name="date_from"
                        value="{{ request('date_from') }}"
                        class="requisition-input"
                    >
                </div>

                <div class="col-md-2">
                    <label class="requisition-field-label">Date To</label>
                    <input
                        type="date"
                        name="date_to"
                        value="{{ request('date_to') }}"
                        class="requisition-input"
                    >
                </div>
                   <div class="col-md-2">
                    <label class="requisition-field-label">Donor</label>
                    <select name="donor_id" class="requisition-input">
                        <option value="">All Donors</option>

                        @foreach($donors as $donor)
                            <option
                                value="{{ $donor->id }}"
                                @selected((string) request('donor_id') === (string) $donor->id)
                            >
                                {{ $donor->name }}
                                @if($donor->donor_number)
                                    ({{ $donor->donor_number }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="requisition-field-label">Donation Type</label>
                    <select name="type" class="requisition-input">
                        <option value="">All Types</option>
                        <option value="cash" @selected(request('type') === 'cash')>
                            Cash
                        </option>
                        <option value="IN_KIND" @selected(request('type') === 'IN_KIND')>
                            In-Kind
                        </option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="requisition-field-label">Classification</label>
                    <select name="classification" class="requisition-input">
                        <option value="">All Classifications</option>

                        @foreach([
                            'donation' => 'Donation',
                            'payment' => 'Payment',
                            'refund' => 'Refund',
                            'other' => 'Other',
                            'unclassified' => 'Unclassified',
                        ] as $value => $label)
                            <option
                                value="{{ $value }}"
                                @selected(request('classification') === $value)
                            >
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>


            </div>


            {{-- Amount, currency and receiving status --}}
            <div class="row mb-3">

                     <div class="col-md-2">
                    <label class="requisition-field-label">Source</label>
                    <select name="source" class="requisition-input">
                        <option value="">All Sources</option>

                        @foreach($sources as $source)
                            <option
                                value="{{ $source }}"
                                @selected(request('source') === $source)
                            >
                                {{ ucfirst($source) }}
                            </option>
                        @endforeach
                    </select>
                </div>




                <div class="col-md-2">
                    <label class="requisition-field-label">Minimum Amount</label>
                    <input
                        type="number"
                        name="amount_min"
                        min="0"
                        step="0.01"
                        value="{{ request('amount_min') }}"
                        class="requisition-input"
                        placeholder="0.00"
                    >
                </div>

                <div class="col-md-2">
                    <label class="requisition-field-label">Maximum Amount</label>
                    <input
                        type="number"
                        name="amount_max"
                        min="0"
                        step="0.01"
                        value="{{ request('amount_max') }}"
                        class="requisition-input"
                        placeholder="No limit"
                    >
                </div>

                <div class="col-md-2">
                    <label class="requisition-field-label">Currency</label>
                    <select name="currency" class="requisition-input">
                        <option value="">All Currencies</option>

                        @foreach($currencies as $currency)
                            <option
                                value="{{ $currency }}"
                                @selected(request('currency') === $currency)
                            >
                                {{ $currency }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="requisition-field-label">Store Receiving Status</label>
                    <select name="receiving_status" class="requisition-input">
                        <option value="">All Statuses</option>
                        <option value="received" @selected(request('receiving_status') === 'received')>
                            Received in Stores
                        </option>
                        <option value="draft" @selected(request('receiving_status') === 'draft')>
                            Receipt in Draft
                        </option>
                        <option value="not_received" @selected(request('receiving_status') === 'not_received')>
                            Not Received
                        </option>
                        <option value="not_applicable" @selected(request('receiving_status') === 'not_applicable')>
                            Not Applicable
                        </option>
                    </select>
                </div>

            </div>

            {{-- Actions --}}
            <div class="d-flex flex-wrap justify-content-end" style="gap: 10px;">

                <button type="submit" class="requisition-save-button">
                    <i class="fa fa-search"></i>
                    Apply Filters
                </button>
                  <a
                    href="{{ route('admin.donations.index') }}"
                    class="requisition-cancel-button"
                    style="display:inline-flex;align-items:center;justify-content:center;gap:6px;"
                >
                    <i class="fa fa-times"></i>
                    Clear Filters
                </a>

            </div>

        </form>

    </div>
</div>


        </div>
    </details>
</div>


    {{-- =========================================================
    DONATION LIST
    ========================================================= --}}
    <div class="requisition-section">




{{-- DONATION RECORDS HEADER AND EXPORT ACTIONS --}}
<div class="requisition-section-header">

    <div class="requisition-section-heading">
        <div class="requisition-section-icon">
            <i class="fa fa-table"></i>
        </div>

        <div>
            <h5>Donation Records</h5>

            <p>
                {{ number_format($donations->total()) }}
                {{ $donations->total() === 1 ? 'record' : 'records' }}
                <span class="mx-1">·</span>
                Generated {{ now()->format('d M Y, H:i') }}
            </p>
        </div>
    </div>

    {{-- EXPORT ACTIONS --}}
    <div class="d-flex flex-wrap gap-2">

        @foreach([
            'pdf' => ['PDF', 'fa-file-pdf'],
            'xlsx' => ['Excel', 'fa-file-excel'],
            'csv' => ['CSV', 'fa-file-csv'],
        ] as $format => $details)

            <a
                href="{{ route('admin.donations.export', array_merge(
                    request()->except('page', 'format'),
                    ['format' => $format]
                )) }}"
                class="{{ $format === 'xlsx'
                    ? 'requisition-save-button'
                    : 'requisition-cancel-button' }}"
                style="display:inline-flex;align-items:center;gap:7px;"
            >
                <i class="fa {{ $details[1] }}"></i>
                Export {{ $details[0] }}
            </a>

        @endforeach

    </div>

</div>





        {{-- =====================================================
        TABLE
        ===================================================== --}}
        <div class="requisition-list-table-wrapper">

            <table class="requisition-list-table">

                <thead>

                    <tr>

                        <th style="min-width:140px;">
                            Donation
                        </th>

                        <th style="min-width:120px;">
                            Date
                        </th>

                        <th style="min-width:180px;">
                            Donor
                        </th>

                        <th style="min-width:100px;">
                            Type
                        </th>

                       

                        <th style="min-width:150px;">
                            Amount / Value
                        </th>

                        <th style="min-width:180px;">
                            Purpose
                        </th>

                        <th style="min-width:120px;">
                            Type
                        </th>

                        <th style="min-width:120px;">
                            Source
                        </th>

                        <th style="min-width:130px;">
                            Store receiving Status
                        </th>

                        <th style="width:110px;">
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($donations as $donation)

                    @php

                    $classificationClasses = [

                    'donation' =>
                    'badge-success',

                    'payment' =>
                    'badge-info',

                    'refund' =>
                    'badge-warning',

                    'other' =>
                    'badge-secondary',

                    'unclassified' =>
                    'badge-light',

                    ];

                    $classificationClass =
                    $classificationClasses[
                    $donation->classification
                    ] ?? 'badge-light';

                    @endphp


                    <tr>


                        {{-- DONATION NUMBER --}}
                        <td>

                            <div class="requisition-list-number">

                                <a href="{{ route(
                                            'admin.donations.show',
                                            $donation
                                        ) }}">
                                    {{ $donation->donation_number }}
                                </a>

                            </div>

                        </td>



                        {{-- DATE --}}
                        <td>

                            <span class="requisition-list-date">

                                {{ $donation->donation_date?->format('d M Y') }}

                            </span>

                        </td>



                        {{-- DONOR --}}
                        <td>

                            @if($donation->donor)

                            <div class="requisition-list-number">

                                <a href="{{ route(
                                                'admin.donors.show',
                                                $donation->donor
                                            ) }}">
                                    {{ $donation->donor->name }}
                                </a>

                            </div>

                            <div class="requisition-list-meta">

                                {{ $donation->donor->donor_number }}

                            </div>

                            @else

                            <span class="requisition-list-department">
                                Anonymous
                            </span>

                            @endif

                        </td>



                        {{-- TYPE --}}
                        <td>

                            @if($donation->type === 'cash')

                            <span class="requisition-list-status requisition-list-status-approved">

                                <span class="requisition-list-status-dot"></span>

                                Cash

                            </span>

                            @else

                            <span class="requisition-list-status requisition-list-status-pending">

                                <span class="requisition-list-status-dot"></span>

                                In-Kind

                            </span>

                            @endif

                        </td>



                        {{-- AMOUNT --}}
                        <td>

                            @if($donation->amount !== null)

                            <strong>

                                {{ $donation->currency }}

                                {{ number_format(
                                $donation->amount,
                                2
                                ) }}

                            </strong>

                            @else

                            <span class="requisition-list-meta">
                                —
                            </span>

                            @endif

                        </td>



                        {{-- PURPOSE --}}
                        <td>

                            <span class="requisition-list-purpose">

                                {{ $donation->purpose ?: '—' }}

                            </span>

                        </td>



                        {{-- SOURCE --}}
                        <td>

                            @if($donation->type === 'cash')

                            <span class="requisition-list-department">

                                {{ ucfirst(
                                $donation->source
                                ) }}

                            </span>

                            @else

                            <span class="requisition-list-meta">
                                —
                            </span>

                            @endif

                        </td>



                        {{-- CLASSIFICATION --}}
                        <td>

                            <span class="requisition-list-status
                                    @if($donation->classification === 'donation')
                                        requisition-list-status-approved
                                    @elseif($donation->classification === 'payment')
                                        requisition-list-status-pending
                                    @elseif($donation->classification === 'refund')
                                        requisition-list-status-rejected
                                    @else
                                        requisition-list-status-draft
                                    @endif">

                                <span class="requisition-list-status-dot"></span>

                                {{ ucfirst(
                                $donation->classification
                                ) }}

                            </span>

                        </td>


                        {{-- STORES RECEIVING --}}
                        <td>
                            @if(strtoupper($donation->type) !== 'IN_KIND')
                            <span class="requisition-list-meta">
                                Not applicable
                            </span>

                            @elseif(($donation->posted_store_receipts_count ?? 0) > 0)
                            <span class="requisition-list-status requisition-list-status-approved">
                                <span class="requisition-list-status-dot"></span>
                                Received in stores
                            </span>

                            @elseif(($donation->draft_store_receipts_count ?? 0) > 0)
                            <span class="requisition-list-status requisition-list-status-pending">
                                <span class="requisition-list-status-dot"></span>
                                Receipt in Draft
                            </span>

                            @else
                            <span class="requisition-list-status requisition-list-status-draft">
                                <span class="requisition-list-status-dot"></span>
                                Not Received
                            </span>
                            @endif
                        </td>



                        {{-- ACTIONS --}}
                        <td>

                            <div class="requisition-list-actions">


                                {{-- VIEW --}}
                                <a href="{{ route(
                                            'admin.donations.show',
                                            $donation
                                        ) }}" class="requisition-list-action primary" title="View donation">
                                    <i class="fa fa-eye"></i>
                                </a>


                                {{-- EDIT --}}
                                <a href="{{ route(
                                            'admin.donations.edit',
                                            $donation
                                        ) }}" class="requisition-list-action" title="Edit donation">
                                    <i class="fa fa-pen"></i>
                                </a>

                            </div>

                        </td>

                    </tr>


                    @empty

                    <tr>

                        <td colspan="9">

                            <div class="requisition-table-empty">

                                <div class="requisition-empty-icon">
                                    <i class="fa fa-hand-holding-heart"></i>
                                </div>


                                @if(request('search'))

                                <h5>
                                    No donations found
                                </h5>

                                <p>
                                    No donation records match
                                    "{{ request('search') }}".
                                </p>

                                <a href="{{ route('admin.donations.index') }}" class="requisition-add-button">
                                    <i class="fa fa-times"></i>
                                    Clear Search
                                </a>

                                @else

                                <h5>
                                    No donations found
                                </h5>

                                <p>
                                    There are currently no donation records.
                                </p>

                                <a href="{{ route('admin.donations.create') }}" class="requisition-add-button">
                                    <i class="fa fa-plus"></i>
                                    Record Donation
                                </a>

                                @endif

                            </div>

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>



        {{-- =====================================================
        SCROLL INDICATOR
        ===================================================== --}}
        @if($donations->count() > 8)

        <div class="requisition-scroll-hint">

            <i class="fa fa-arrows-alt-v"></i>

            Scroll to view more donations

        </div>

        @endif


@if($donations->total() > 0)

    <div class="stores-pagination-section">

        <div class="stores-pagination-info">

            <div class="stores-pagination-info-icon">
                <i class="fa fa-layer-group"></i>
            </div>

            <div class="stores-pagination-info-text">

                <span class="stores-pagination-info-label">
                    Showing donations
                </span>

                <strong>
                    {{ $donations->firstItem() ?? 0 }}
                    –
                    {{ $donations->lastItem() ?? 0 }}
                </strong>

                <span class="stores-pagination-info-total">
                    of {{ number_format($donations->total()) }} total
                </span>

            </div>

        </div>

        @if($donations->hasPages())

            <div class="stores-pagination-controls">
                {{ $donations->onEachSide(2)->withQueryString()->links('pagination::bootstrap-5') }}
            </div>

        @endif

    </div>

@endif


    </div>


</div>

@endsection