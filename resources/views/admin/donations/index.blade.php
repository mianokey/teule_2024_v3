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

            <a
                href="{{ route('admin.donations.create') }}"
                class="requisition-add-button"
            >
                <i class="fa fa-plus"></i>
                Record Donation
            </a>

        </div>

    </div>



    {{-- =========================================================
         MESSAGES
         ========================================================= --}}
    <x-message></x-message>



    {{-- =========================================================
         SEARCH SECTION
         ========================================================= --}}
    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fa fa-search"></i>
                </div>

                <div>

                    <h5>
                        Search Donations
                    </h5>

                    <p>
                        Search by donation number, donor, purpose, source, type or classification.
                    </p>

                </div>

            </div>

        </div>


        <div class="requisition-details-body">

            <form
                method="GET"
                action="{{ route('admin.donations.index') }}"
            >

                <div class="row">

                    <div class="col-md-10">

                        <label class="requisition-field-label">
                            Search
                        </label>

                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            class="requisition-input"
                            placeholder="Search donation number, donor, purpose, source, type or classification..."
                        >

                    </div>


                    <div
                        class="col-md-2"
                        style="
                            display:flex;
                            align-items:flex-end;
                            gap:8px;
                        "
                    >

                        <button
                            type="submit"
                            class="requisition-save-button"
                            style="width:100%;"
                        >
                            <i class="fa fa-search"></i>
                            Search
                        </button>


                        @if(request('search'))

                            <a
                                href="{{ route('admin.donations.index') }}"
                                class="requisition-cancel-button"
                                title="Clear search"
                                style="
                                    display:inline-flex;
                                    align-items:center;
                                    justify-content:center;
                                    white-space:nowrap;
                                "
                            >
                                <i class="fa fa-times"></i>
                                Clear
                            </a>

                        @endif

                    </div>

                </div>

            </form>

        </div>

    </div>



    {{-- =========================================================
         DONATION LIST
         ========================================================= --}}
    <div class="requisition-section">


        {{-- =====================================================
             SECTION HEADER
             ===================================================== --}}
        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fa fa-hand-holding-heart"></i>
                </div>

                <div>

                    <h5>
                        Donation Records
                    </h5>

                    <p>

                        @if(request('search'))

                            Showing donations matching
                            "<strong>{{ request('search') }}</strong>".

                        @else

                            View all recorded donations and their details.

                        @endif

                    </p>

                </div>

            </div>


            <div class="requisition-count-badge">

                {{ $donations->count() }}

                {{ $donations->count() === 1 ? 'Record' : 'Records' }}

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
                            Source
                        </th>

                        <th style="min-width:130px;">
                            Status
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

                                    <a
                                        href="{{ route(
                                            'admin.donations.show',
                                            $donation
                                        ) }}"
                                    >
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

                                        <a
                                            href="{{ route(
                                                'admin.donors.show',
                                                $donation->donor
                                            ) }}"
                                        >
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

                                <span
                                    class="requisition-list-status
                                    @if($donation->classification === 'donation')
                                        requisition-list-status-approved
                                    @elseif($donation->classification === 'payment')
                                        requisition-list-status-pending
                                    @elseif($donation->classification === 'refund')
                                        requisition-list-status-rejected
                                    @else
                                        requisition-list-status-draft
                                    @endif"
                                >

                                    <span class="requisition-list-status-dot"></span>

                                    {{ ucfirst(
                                        $donation->classification
                                    ) }}

                                </span>

                            </td>



                            {{-- ACTIONS --}}
                            <td>

                                <div class="requisition-list-actions">


                                    {{-- VIEW --}}
                                    <a
                                        href="{{ route(
                                            'admin.donations.show',
                                            $donation
                                        ) }}"
                                        class="requisition-list-action primary"
                                        title="View donation"
                                    >
                                        <i class="fa fa-eye"></i>
                                    </a>


                                    {{-- EDIT --}}
                                    <a
                                        href="{{ route(
                                            'admin.donations.edit',
                                            $donation
                                        ) }}"
                                        class="requisition-list-action"
                                        title="Edit donation"
                                    >
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

                                        <a
                                            href="{{ route('admin.donations.index') }}"
                                            class="requisition-add-button"
                                        >
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

                                        <a
                                            href="{{ route('admin.donations.create') }}"
                                            class="requisition-add-button"
                                        >
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


    </div>


</div>

@endsection