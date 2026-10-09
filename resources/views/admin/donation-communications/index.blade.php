@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/stores.css') }}">
@endpush

@section('content')

<div class="store-requisition-page communication-history-page">

    {{-- =========================================================
         PAGE HEADER
         ========================================================= --}}

    <div class="requisition-page-header">

        <div class="requisition-header-content">

            <div class="requisition-header-icon">
                <i class="fa fa-envelope"></i>
            </div>

            <div>
                <div class="requisition-breadcrumb">
                    <span>Donations</span>
                    <span>/</span>
                    <span>Communications</span>
                </div>

                <h1 class="requisition-page-title">
                    Communication History
                </h1>

                <p class="requisition-page-subtitle">
                    View and track donor communications and delivery status.
                </p>
            </div>

        </div>

        <div class="requisition-header-right">

            <a
                href="{{ route('admin.donations.index') }}"
                class="requisition-cancel-button"
            >
                <i class="fa fa-arrow-left"></i>
                Donations
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


    {{-- =========================================================
         MAIN SECTION
         ========================================================= --}}

    <div class="requisition-section">

        {{-- SECTION HEADER --}}

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fa fa-history"></i>
                </div>

                <div>
                    <h5>
                        Donor Communications
                    </h5>

                    <p>
                        Search, filter and review communication records.
                    </p>
                </div>

            </div>

            <div class="requisition-count-badge">
                {{ method_exists($communications, 'total')
                    ? $communications->total()
                    : $communications->count()
                }}
                {{ (method_exists($communications, 'total')
                    ? $communications->total()
                    : $communications->count()) === 1
                    ? 'Communication'
                    : 'Communications'
                }}
            </div>

        </div>


        {{-- =====================================================
             FILTERS
             ===================================================== --}}

        <div class="requisition-details-body">

            <form
                method="GET"
                action="{{ route('admin.donation-communications.index') }}"
            >

                <div class="row g-3 align-items-end">

                    {{-- SEARCH --}}

                    <div class="col-lg-5">

                        <label
                            for="communication-search"
                            class="requisition-field-label"
                        >
                            Search
                        </label>

                        <input
                            type="text"
                            id="communication-search"
                            name="search"
                            value="{{ request('search') }}"
                            class="requisition-input"
                            placeholder="Search donor, recipient or donation..."
                        >

                    </div>


                    {{-- CHANNEL --}}

                    <div class="col-lg-2">

                        <label
                            for="communication-channel"
                            class="requisition-field-label"
                        >
                            Channel
                        </label>

                        <select
                            id="communication-channel"
                            name="channel"
                            class="requisition-input"
                        >

                            <option value="">
                                All Channels
                            </option>

                            <option
                                value="email"
                                @selected(request('channel') === 'email')
                            >
                                Email
                            </option>

                            <option
                                value="sms"
                                @selected(request('channel') === 'sms')
                            >
                                SMS
                            </option>

                        </select>

                    </div>


                    {{-- STATUS --}}

                    <div class="col-lg-2">

                        <label
                            for="communication-status"
                            class="requisition-field-label"
                        >
                            Status
                        </label>

                        <select
                            id="communication-status"
                            name="status"
                            class="requisition-input"
                        >

                            <option value="">
                                All Statuses
                            </option>

                            @foreach([
                                'pending',
                                'scheduled',
                                'sending',
                                'sent',
                                'delivered',
                                'failed',
                                'cancelled'
                            ] as $filterStatus)

                                <option
                                    value="{{ $filterStatus }}"
                                    @selected(request('status') === $filterStatus)
                                >
                                    {{ \Illuminate\Support\Str::headline($filterStatus) }}
                                </option>

                            @endforeach

                        </select>

                    </div>


                    {{-- FILTER BUTTONS --}}

                    <div class="col-lg-3">

                        <div class="d-flex gap-2">

                            <button
                                type="submit"
                                class="requisition-save-button"
                            >
                                <i class="fa fa-search"></i>
                                Filter
                            </button>

                            <a
                                href="{{ route('admin.donation-communications.index') }}"
                                class="requisition-cancel-button"
                            >
                                Clear
                            </a>

                        </div>

                    </div>

                </div>

            </form>

        </div>


        {{-- =====================================================
             TABLE
             ===================================================== --}}

        <div class="requisition-list-table-wrapper">

            <table class="requisition-list-table">

                <thead>

                    <tr>

                        <th style="width: 55px;">
                            #
                        </th>

                        <th style="min-width: 170px;">
                            Donor
                        </th>

                        <th style="min-width: 155px;">
                            Donation
                        </th>

                        <th style="width: 95px;">
                            Channel
                        </th>

                        <th style="min-width: 190px;">
                            Recipient
                        </th>

                        <th style="min-width: 145px;">
                            Type
                        </th>

                        <th style="min-width: 135px;">
                            Status
                        </th>

                        <th style="min-width: 110px;">
                            Date
                        </th>

                        <th style="width: 90px;">
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @forelse($communications as $communication)

                        @php
                            $status = strtolower(
                                (string) ($communication->status ?? 'pending')
                            );

                            $statusLabel = \Illuminate\Support\Str::headline(
                                $status
                            );

                            $statusClass = match ($status) {
                                'sent', 'delivered' => 'approved',
                                'failed', 'cancelled' => 'rejected',
                                'pending', 'scheduled', 'sending' => 'pending',
                                default => 'pending',
                            };

                            $channel = strtolower(
                                (string) ($communication->channel ?? '')
                            );

                            $donor = $communication->donor ?? null;
                            $donation = $communication->donation ?? null;
                        @endphp


                        <tr>

                            {{-- NUMBER --}}

                            <td>
                                <span class="requisition-list-row-number">
                                    {{ method_exists($communications, 'firstItem')
                                        ? (($communications->firstItem() ?? 1) + $loop->index)
                                        : $loop->iteration
                                    }}
                                </span>
                            </td>


                            {{-- DONOR --}}

                            <td>

                                @if($donor)

                                    <div class="requisition-list-number">

                                        <a href="{{ route('admin.donors.show', $donor) }}">
                                            {{ $donor->name ?? 'Unnamed Donor' }}
                                        </a>

                                    </div>

                                    @if(!empty($donor->donor_number))
                                        <div class="requisition-list-meta">
                                            {{ $donor->donor_number }}
                                        </div>
                                    @endif

                                @else

                                    <div class="requisition-list-number">
                                        {{ $communication->recipient ?: '—' }}
                                    </div>

                                @endif

                            </td>


                            {{-- DONATION --}}

                            <td>

                                @if($donation)

                                    <div class="requisition-list-number">

                                        <a href="{{ route('admin.donations.show', $donation) }}">
                                            {{ $donation->donation_number
                                                ?? ('Donation #' . $donation->id)
                                            }}
                                        </a>

                                    </div>

                                    @if(isset($donation->amount))
                                        <div class="requisition-list-meta">
                                            {{ $donation->currency ?? 'KES' }}
                                            {{ number_format((float) $donation->amount, 2) }}
                                        </div>
                                    @endif

                                @else

                                    <span class="requisition-list-meta">
                                        No linked donation
                                    </span>

                                @endif

                            </td>


                            {{-- CHANNEL --}}

                            <td>

                                <span class="communication-channel">

                                    @if($channel === 'email')
                                        <i class="fa fa-envelope"></i>
                                        Email
                                    @elseif($channel === 'sms')
                                        <i class="fa fa-comment-alt"></i>
                                        SMS
                                    @else
                                        <i class="fa fa-paper-plane"></i>
                                        {{ $communication->channel ?: '—' }}
                                    @endif

                                </span>

                            </td>


                            {{-- RECIPIENT --}}

                            <td>

                                <div
                                    class="requisition-list-purpose communication-recipient"
                                    title="{{ $communication->recipient }}"
                                >
                                    {{ \Illuminate\Support\Str::limit(
                                        $communication->recipient ?? '—',
                                        55
                                    ) }}
                                </div>

                            </td>


                            {{-- TYPE --}}

                            <td>

                                <div class="requisition-list-purpose">
                                    {{ \Illuminate\Support\Str::headline(
                                        (string) ($communication->type ?? '—')
                                    ) }}
                                </div>

                                @if(!empty($communication->subject))
                                    <div
                                        class="requisition-list-meta communication-subject"
                                        title="{{ $communication->subject }}"
                                    >
                                        {{ \Illuminate\Support\Str::limit(
                                            $communication->subject,
                                            45
                                        ) }}
                                    </div>
                                @endif

                            </td>


                            {{-- STATUS --}}

                            <td>

                                <span class="requisition-list-status requisition-list-status-{{ $statusClass }}">

                                    <span class="requisition-list-status-dot"></span>

                                    {{ $statusLabel }}

                                </span>

                                @if($status === 'failed' && !empty($communication->error_message))

                                    <div
                                        class="requisition-list-meta communication-error"
                                        title="{{ $communication->error_message }}"
                                    >
                                        Delivery error
                                    </div>

                                @endif

                            </td>


                            {{-- DATE --}}

                            <td>

                                <div class="requisition-list-date">
                                    {{ $communication->created_at?->format('d M Y') }}
                                </div>

                                <div class="requisition-list-time">
                                    {{ $communication->created_at?->format('H:i') }}
                                </div>

                            </td>


                            {{-- ACTIONS --}}

                            <td>

                                <div class="requisition-list-actions">

                                    <a
                                        href="{{ route('admin.donation-communications.show', $communication) }}"
                                        class="requisition-list-action primary"
                                        title="View communication"
                                    >
                                        <i class="fa fa-eye"></i>
                                    </a>

                                </div>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="9">

                                <div class="requisition-table-empty">

                                    <div class="requisition-empty-icon">
                                        <i class="fa fa-envelope-open"></i>
                                    </div>

                                    <h5>
                                        No communications found
                                    </h5>

                                    <p>
                                        No communication records match your current filters.
                                    </p>

                                    @if(request()->hasAny(['search', 'channel', 'status']))

                                        <a
                                            href="{{ route('admin.donation-communications.index') }}"
                                            class="requisition-add-button"
                                        >
                                            <i class="fa fa-times"></i>
                                            Clear Filters
                                        </a>

                                    @endif

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- SCROLL INDICATOR --}}

        @if($communications->count() > 8)

            <div class="requisition-scroll-hint">
                <i class="fa fa-arrows-alt-v"></i>
                Scroll to view more communications
            </div>

        @endif


        {{-- =====================================================
             PAGINATION
             ===================================================== --}}

        @if(method_exists($communications, 'hasPages') && $communications->hasPages())

            <div class="requisition-pagination">

                {{ $communications->appends(
                    request()->except('page')
                )->links() }}

            </div>

        @endif

    </div>

</div>

@endsection
```
