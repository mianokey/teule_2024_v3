```blade
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
                <i class="fa fa-users"></i>
            </div>

            <div>

                <div class="requisition-breadcrumb">
                    <span>Donations</span>
                    <span>/</span>
                    <span>Donors</span>
                </div>

                <h1 class="requisition-page-title">
                    All Donors
                </h1>

                <p class="requisition-page-subtitle">
                    View and manage donor records and their donation history.
                </p>

            </div>

        </div>


        <div class="requisition-header-right">

            <a
                href="{{ route('admin.donors.create') }}"
                class="requisition-add-button"
            >
                <i class="fa fa-plus"></i>
                Add Donor
            </a>

        </div>

    </div>


    {{-- =========================================================
         SUCCESS / ERROR MESSAGES
         ========================================================= --}}
    @if(session('success'))

        <div class="alert alert-success">
            {{ session('success') }}
        </div>

    @endif


    @if(session('error'))

        <div class="alert alert-danger">
            {{ session('error') }}
        </div>

    @endif


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
                        Search Donors
                    </h5>

                    <p>
                        Search by donor number, name, phone, email or organization.
                    </p>

                </div>

            </div>

        </div>


        <div class="requisition-details-body">

            <form
                method="GET"
                action="{{ route('admin.donors.index') }}"
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
                            placeholder="Search donor number, name, phone, email or organization..."
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
                                href="{{ route('admin.donors.index') }}"
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
         MAIN SECTION
         ========================================================= --}}
    <div class="requisition-section">


        {{-- =====================================================
             SECTION HEADER
             ===================================================== --}}
        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fa fa-users"></i>
                </div>

                <div>

                    <h5>
                        Donor List
                    </h5>

                    <p>

                        @if(request('search'))

                            Showing donors matching
                            "<strong>{{ request('search') }}</strong>".

                        @else

                            View registered donors and their donation records.

                        @endif

                    </p>

                </div>

            </div>


            <div class="requisition-count-badge">

                {{ $donors->count() }}

                {{ $donors->count() === 1 ? 'Donor' : 'Donors' }}

            </div>

        </div>



        {{-- =====================================================
             TABLE
             ===================================================== --}}
        <div class="requisition-list-table-wrapper">

            <table class="requisition-list-table">

                <thead>

                    <tr>

                        <th style="min-width: 140px;">
                            Donor Number
                        </th>

                        <th style="min-width: 180px;">
                            Name
                        </th>

                        <th style="min-width: 140px;">
                            Phone
                        </th>

                        <th style="min-width: 220px;">
                            Email
                        </th>

                        <th style="min-width: 180px;">
                            Organization
                        </th>

                        <th style="width: 110px;">
                            Donations
                        </th>

                        <th style="width: 130px;">
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse ($donors as $donor)

                        <tr>


                            {{-- DONOR NUMBER --}}
                            <td>

                                <div class="requisition-list-number">

                                    <a
                                        href="{{ route(
                                            'admin.donors.show',
                                            $donor
                                        ) }}"
                                    >
                                        {{ $donor->donor_number }}
                                    </a>

                                </div>

                            </td>



                            {{-- NAME --}}
                            <td>

                                <div class="requisition-list-number">

                                    <a
                                        href="{{ route(
                                            'admin.donors.show',
                                            $donor
                                        ) }}"
                                    >
                                        {{ $donor->name }}
                                    </a>

                                </div>

                            </td>



                            {{-- PHONE --}}
                            <td>

                                <span class="requisition-list-department">

                                    {{ $donor->phone ?: 'NOT STATED' }}

                                </span>

                            </td>



                            {{-- EMAIL --}}
                            <td>

                                <span class="requisition-list-purpose">

                                    {{ $donor->email ?: 'NOT STATED' }}

                                </span>

                            </td>



                            {{-- ORGANIZATION --}}
                            <td>

                                <span class="requisition-list-department">

                                    {{ $donor->organization ?: 'NOT STATED' }}

                                </span>

                            </td>



                            {{-- DONATIONS --}}
                            <td>

                                <span class="requisition-list-item-count">

                                    {{ $donor->donations_count }}

                                    {{ $donor->donations_count == 1
                                        ? 'Donation'
                                        : 'Donations'
                                    }}

                                </span>

                            </td>



                            {{-- ACTIONS --}}
                            <td>

                                <div class="requisition-list-actions">


                                    {{-- VIEW --}}
                                    <a
                                        href="{{ route(
                                            'admin.donors.show',
                                            $donor
                                        ) }}"
                                        class="requisition-list-action primary"
                                        title="View donor"
                                    >
                                        <i class="fa fa-eye"></i>
                                    </a>


                                    {{-- EDIT --}}
                                    <a
                                        href="{{ route(
                                            'admin.donors.edit',
                                            $donor
                                        ) }}"
                                        class="requisition-list-action"
                                        title="Edit donor"
                                    >
                                        <i class="fa fa-pen"></i>
                                    </a>

                                </div>

                            </td>

                        </tr>


                    @empty

                        <tr>

                            <td colspan="7">

                                <div class="requisition-table-empty">

                                    <div class="requisition-empty-icon">
                                        <i class="fa fa-users"></i>
                                    </div>


                                    @if(request('search'))

                                        <h5>
                                            No donors found
                                        </h5>

                                        <p>
                                            No donor records match
                                            "{{ request('search') }}".
                                        </p>

                                        <a
                                            href="{{ route('admin.donors.index') }}"
                                            class="requisition-add-button"
                                        >
                                            <i class="fa fa-times"></i>
                                            Clear Search
                                        </a>

                                    @else

                                        <h5>
                                            No donors found
                                        </h5>

                                        <p>
                                            There are currently no donor records.
                                        </p>

                                        <a
                                            href="{{ route('admin.donors.create') }}"
                                            class="requisition-add-button"
                                        >
                                            <i class="fa fa-plus"></i>
                                            Add Donor
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
        @if($donors->count() > 8)

            <div class="requisition-scroll-hint">

                <i class="fa fa-arrows-alt-v"></i>

                Scroll to view more donors

            </div>

        @endif


    </div>


</div>

@endsection