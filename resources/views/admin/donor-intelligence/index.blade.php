@extends('layouts.admin')

@section('content')

<style>
    .di-dashboard {
        color: #263247;
    }

    .di-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 22px;
    }

    .di-header h3 {
        font-weight: 700;
        color: #00096A;
        margin-bottom: 5px;
    }

    .di-muted {
        color: #7b8495;
        font-size: 13px;
    }

    .di-card {
        background: #fff;
        border: 1px solid #e8ebf1;
        border-radius: 12px;
        padding: 18px;
        height: 100%;
        box-shadow: 0 2px 8px rgba(20, 35, 70, .035);
    }

    .di-card h6 {
        color: #707b8c;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .45px;
        margin-bottom: 12px;
    }

    .di-value {
        font-size: 27px;
        line-height: 1.2;
        font-weight: 750;
        color: #00096A;
        overflow-wrap: anywhere;
    }

    .di-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        justify-content: center;
        align-items: center;
        background: #eef0ff;
        color: #00096A;
        font-size: 19px;
        flex-shrink: 0;
    }

    .di-section {
        background: #fff;
        border: 1px solid #e8ebf1;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(20, 35, 70, .025);
    }

    .di-section-title {
        color: #00096A;
        font-size: 16px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .di-section-subtitle {
        font-size: 12px;
        color: #8992a2;
        margin-bottom: 18px;
    }

  

    .di-table {
        width: 100%;
        border-collapse: collapse;
    }

    .di-table th {
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .35px;
        color: #778195;
        background: #f8f9fc;
        padding: 12px;
        white-space: nowrap;
        font-weight: 700;
    }

    .di-table td {
        font-size: 13px;
        padding: 12px;
        border-bottom: 1px solid #edf0f5;
        vertical-align: middle;
    }

    .di-table tr:last-child td {
        border-bottom: 0;
    }

    .di-table a {
        color: #00096A;
        font-weight: 600;
        text-decoration: none;
    }

    .di-table a:hover {
        color: #ef4700;
    }

    .di-pill {
        display: inline-block;
        padding: 4px 9px;
        border-radius: 20px;
        background: #f0f2f7;
        color: #536074;
        font-size: 11px;
        font-weight: 700;
    }

    .di-pill-success {
        background: #e8f7ee;
        color: #18834a;
    }

    .di-pill-warning {
        background: #fff4df;
        color: #a86b00;
    }

    .di-pill-danger {
        background: #fdeaea;
        color: #bd3434;
    }

    .di-link {
        color: #00096A;
        font-size: 12px;
        font-weight: 700;
        text-decoration: none;
    }

    .di-link:hover {
        color: #ef4700;
    }

    .di-chart-wrap {
        position: relative;
        width: 100%;
        height: 290px;
    }

    .di-empty {
        color: #8b94a3;
        font-size: 13px;
        text-align: center;
        padding: 25px 10px;
    }

    .di-export {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .di-export .btn {
        font-size: 12px;
        font-weight: 600;
    }

    .di-contact-bar {
        height: 9px;
        border-radius: 20px;
        background: #edf0f5;
        overflow: hidden;
        margin-top: 7px;
    }

    .di-contact-fill {
        height: 100%;
        border-radius: 20px;
    }

    @media(max-width: 767px) {
        .di-value {
            font-size: 23px;
        }

        .di-section {
            padding: 15px;
        }

        .di-chart-wrap {
            height: 240px;
        }
    }
</style>

@php
    $queryFilters = array_filter($filters, fn($value) => $value !== null && $value !== '');

    $currencyFormat = function ($amount, $currency) {
        return $currency . ' ' . number_format((float) $amount, 2);
    };

    $typeLabels = [
        'cash' => 'Cash',
        'in_kind' => 'In-kind',
    ];

    $sourceLabels = [
        'manual' => 'Manual',
        'mpesa' => 'M-Pesa',
        'bank' => 'Bank',
        'paypal' => 'PayPal',
        'other' => 'Other',
    ];

    $totalContactDonors = array_sum($contactCoverage);

    $contactPercent = function ($value) use ($totalContactDonors) {
        return $totalContactDonors > 0
            ? round(($value / $totalContactDonors) * 100, 1)
            : 0;
    };
@endphp

<div class="container-fluid di-dashboard">

    {{-- PAGE HEADER --}}
    <div class="di-header">
        <div>
            <h3>Donor Intelligence</h3>
            <div class="di-muted">
                Teule Kenya | Donor relationships, giving patterns and communications
            </div>
        </div>

        <div class="di-export">
            <a href="{{ route('admin.donor-intelligence.export', array_merge(['format' => 'csv'], $queryFilters)) }}"
               class="requisition-add-button">
                <i class="fas fa-file-csv"></i> Export CSV
            </a>

            <a href="{{ route('admin.donor-intelligence.export', array_merge(['format' => 'pdf'], $queryFilters)) }}"
               class="requisition-add-button" target="_blank">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
        </div>
    </div>

{{-- ADVANCED DONOR INTELLIGENCE FILTERS --}}
<div class="requisition-section donation-filters-section">

    <details class="donation-filters-details"
        {{ request()->hasAny(['from', 'to', 'type', 'source', 'currency']) ? 'open' : '' }}>

        <summary class="requisition-section-header donation-filters-summary">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">
                    <i class="fa fa-filter"></i>
                </div>

                <div>
                    <h5>Search & Filter Donor Intelligence</h5>
                    <p>
                        Filter donor performance, donation trends and communication records.
                    </p>
                </div>

            </div>

            <i class="fa fa-chevron-down donation-filters-chevron"></i>

        </summary>

        <div class="requisition-details-body donation-filters-body">

            <div class="requisition-section">

                <div class="requisition-details-body">

                    <form method="GET"
                        action="{{ route('admin.donor-intelligence.index') }}">

                        {{-- Date and donation type --}}
                        <div class="row mb-3">

                            <div class="col-md-2">
                                <label class="requisition-field-label">
                                    Date From
                                </label>

                                <input
                                    type="date"
                                    name="from"
                                    value="{{ request('from') }}"
                                    class="requisition-input">
                            </div>

                            <div class="col-md-2">
                                <label class="requisition-field-label">
                                    Date To
                                </label>

                                <input
                                    type="date"
                                    name="to"
                                    value="{{ request('to') }}"
                                    class="requisition-input">
                            </div>

                            <div class="col-md-2">
                                <label class="requisition-field-label">
                                    Donation Type
                                </label>

                                <select name="type" class="requisition-input">

                                    <option value="">All Types</option>

                                    <option value="cash"
                                        @selected(request('type') === 'cash')>
                                        Cash
                                    </option>

                                    <option value="in_kind"
                                        @selected(request('type') === 'in_kind')>
                                        In-Kind
                                    </option>

                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="requisition-field-label">
                                    Source
                                </label>

                                <select name="source" class="requisition-input">

                                    <option value="">All Sources</option>

                                    @foreach([
                                        'manual',
                                        'mpesa',
                                        'bank',
                                        'paypal',
                                        'other'
                                    ] as $source)

                                        <option value="{{ $source }}"
                                            @selected(request('source') === $source)>
                                            {{ ucfirst($source) }}
                                        </option>

                                    @endforeach

                                </select>
                            </div>

                            <div class="col-md-2">
                                <label class="requisition-field-label">
                                    Currency
                                </label>

                                <select name="currency" class="requisition-input">

                                    <option value="">All Currencies</option>

                                    @foreach(['KES', 'USD'] as $currency)

                                        <option value="{{ $currency }}"
                                            @selected(request('currency') === $currency)>
                                            {{ $currency }}
                                        </option>

                                    @endforeach

                                </select>
                            </div>

                        </div>

                        {{-- Filter actions --}}
                        <div class="d-flex flex-wrap justify-content-end"
                            style="gap: 10px;">

                            <button type="submit"
                                class="requisition-save-button">

                                <i class="fa fa-search"></i>
                                Apply Filters

                            </button>

                            <a href="{{ route('admin.donor-intelligence.index') }}"
                                class="requisition-cancel-button"
                                style="display:inline-flex;align-items:center;justify-content:center;gap:6px;">

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


    {{-- DONOR KPI CARDS --}}
    <div class="row mb-3">

        <div class="col-xl-3 col-md-6 mb-3">
            <a href="{{ route('admin.donors.index') }}" class="text-decoration-none">
                <div class="di-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6>Total registered donors</h6>
                            <div class="di-value">{{ number_format($totalDonors) }}</div>
                            <div class="di-muted mt-2">All registered donor records</div>
                        </div>
                        <div class="di-icon"><i class="fas fa-users"></i></div>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="di-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6>New donors</h6>
                        <div class="di-value">{{ number_format($newDonors) }}</div>
                        <div class="di-muted mt-2">Registered in selected period</div>
                    </div>
                    <div class="di-icon"><i class="fas fa-user-plus"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="di-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6>Repeat donors</h6>
                        <div class="di-value">{{ number_format($repeatDonors) }}</div>
                        <div class="di-muted mt-2">At least two recorded donations</div>
                    </div>
                    <div class="di-icon"><i class="fas fa-handshake"></i></div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="di-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6>Repeat donor rate</h6>
                        <div class="di-value">{{ number_format($retentionRate, 1) }}%</div>
                        <div class="di-muted mt-2">Repeat donors among donors who have given</div>
                    </div>
                    <div class="di-icon"><i class="fas fa-chart-line"></i></div>
                </div>
            </div>
        </div>

    </div>

    {{-- DONOR ACTIVITY --}}
    <div class="row mb-3">

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="di-card">
                <h6>Donors with giving history</h6>
                <div class="di-value">{{ number_format($donorsWithGiving) }}</div>
                <div class="di-muted mt-2">Donors with at least one qualifying donation</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="di-card">
                <h6>Active donors</h6>
                <div class="di-value text-success">{{ number_format($activeDonors) }}</div>
                <div class="di-muted mt-2">Last donation within 90 days</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="di-card">
                <h6>Lapsed donors</h6>
                <div class="di-value text-warning">{{ number_format($lapsedDonors) }}</div>
                <div class="di-muted mt-2">Last donation more than 90 days ago</div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-3">
            <div class="di-card">
                <h6>Recorded donations</h6>
                <div class="di-value">{{ number_format($totalDonations) }}</div>
                <div class="di-muted mt-2">Matching the selected donation filters</div>
            </div>
        </div>

    </div>

    {{-- CASH AND IN-KIND SUMMARY --}}
    <div class="row mb-3">

        <div class="col-xl-6 mb-3">
            <div class="di-section h-100">
                <h5 class="di-section-title">Cash donation performance</h5>
                <p class="di-section-subtitle">Cash totals are kept separate by currency.</p>

                @forelse($cashByCurrency as $cash)
                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                        <div>
                            <div class="font-weight-bold">{{ $cash->currency }}</div>
                            <div class="di-muted">{{ number_format($cash->donation_count) }} donations</div>
                        </div>
                        <div class="text-right">
                            <div class="di-value" style="font-size:21px;">
                                {{ $currencyFormat($cash->total_amount, $cash->currency) }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="di-empty">No cash donations match the selected filters.</div>
                @endforelse
            </div>
        </div>

        <div class="col-xl-6 mb-3">
            <div class="di-section h-100">
                <h5 class="di-section-title">In-kind donation performance</h5>
                <p class="di-section-subtitle">Estimated values are calculated from donated items.</p>

                @forelse($inKindByCurrency as $kind)
                    <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                        <div>
                            <div class="font-weight-bold">{{ $kind->currency }}</div>
                            <div class="di-muted">{{ number_format($kind->donation_count) }} donations</div>
                        </div>
                        <div class="text-right">
                            <div class="di-value" style="font-size:21px;">
                                {{ $currencyFormat($kind->estimated_value, $kind->currency) }}
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="di-empty">No in-kind donations match the selected filters.</div>
                @endforelse
            </div>
        </div>

    </div>

    {{-- CHARTS --}}
    <div class="row mb-3">

        <div class="col-xl-8 mb-3">
            <div class="di-section">
                <h5 class="di-section-title">Monthly donation activity</h5>
                <p class="di-section-subtitle">Number of cash and in-kind donation records by month.</p>

                <div class="di-chart-wrap">
                    <canvas id="monthlyDonationChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-4 mb-3">
            <div class="di-section">
                <h5 class="di-section-title">Donation sources</h5>
                <p class="di-section-subtitle">Distribution by recorded source.</p>

                <div class="di-chart-wrap">
                    <canvas id="donationSourceChart"></canvas>
                </div>
            </div>
        </div>

    </div>

    <div class="row mb-3">

        <div class="col-xl-6 mb-3">
            <div class="di-section">
                <h5 class="di-section-title">New donor acquisition</h5>
                <p class="di-section-subtitle">Donor registrations by month.</p>

                <div class="di-chart-wrap">
                    <canvas id="donorAcquisitionChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-6 mb-3">
            <div class="di-section">
                <h5 class="di-section-title">Donor contact coverage</h5>
                <p class="di-section-subtitle">Availability of email addresses and phone numbers.</p>

                @php
                    $contactRows = [
                        ['label' => 'Both email and phone', 'value' => $contactCoverage['both'], 'color' => '#00096A'],
                        ['label' => 'Email only', 'value' => $contactCoverage['email_only'], 'color' => '#4bbf24'],
                        ['label' => 'Phone only', 'value' => $contactCoverage['phone_only'], 'color' => '#ef4700'],
                        ['label' => 'Missing both', 'value' => $contactCoverage['missing_both'], 'color' => '#aab2c0'],
                    ];
                @endphp

                @foreach($contactRows as $contact)
                    <div class="mb-4">
                        <div class="d-flex justify-content-between">
                            <span style="font-size:13px;font-weight:600;">
                                {{ $contact['label'] }}
                            </span>
                            <span style="font-size:13px;font-weight:700;">
                                {{ $contact['value'] }}
                                <span class="di-muted">({{ $contactPercent($contact['value']) }}%)</span>
                            </span>
                        </div>
                        <div class="di-contact-bar">
                            <div class="di-contact-fill"
                                 style="width:{{ $contactPercent($contact['value']) }}%;background:{{ $contact['color'] }};">
                            </div>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>

    </div>

    {{-- DONATION SOURCES TABLE --}}
    <div class="di-section">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
            <div>
                <h5 class="di-section-title">Donation source analysis</h5>
                <p class="di-section-subtitle mb-0">Where recorded donations originate.</p>
            </div>
            <a href="{{ route('admin.donations.index') }}" class="di-link">View donations →</a>
        </div>

        <div class="table-responsive">
            <table class="di-table">
                <thead>
                    <tr>
                        <th>Source</th>
                        <th>Donation records</th>
                        <th>Cash amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($donationSources as $source)
                        <tr>
                            <td>
                                {{ $sourceLabels[$source->source] ?? ucfirst($source->source) }}
                            </td>
                            <td>{{ number_format($source->total) }}</td>
                            <td>{{ number_format((float) $source->cash_amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="di-empty">No donation sources found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="di-muted mt-2">
            Cash amounts in this table are not combined across currencies. See the currency summary above for separate totals.
        </div>
    </div>

    {{-- IN-KIND ITEMS --}}
    <div class="di-section">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
            <div>
                <h5 class="di-section-title">Most donated in-kind items</h5>
                <p class="di-section-subtitle mb-0">Based on item quantities recorded against donations.</p>
            </div>
        </div>

        <div class="table-responsive">
            <table class="di-table">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Unit</th>
                        <th>Total quantity</th>
                        <th>Estimated value</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inKindItems as $item)
                        <tr>
                            <td>{{ $item->item ?: 'Unspecified item' }}</td>
                            <td>{{ $item->unit ?: '—' }}</td>
                            <td>{{ number_format((float) $item->total_quantity, 2) }}</td>
                            <td>{{ number_format((float) $item->total_value, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="di-empty">No in-kind items found for these filters.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- TOP DONORS --}}
    <div class="di-section">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
            <div>
                <h5 class="di-section-title">Top cash donors</h5>
                <p class="di-section-subtitle mb-0">Ranked by cumulative recorded cash giving, separately by currency.</p>
            </div>
            <a href="{{ route('admin.donors.index') }}" class="di-link">View all donors →</a>
        </div>

        <div class="table-responsive">
            <table class="di-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Donor</th>
                        <th>Currency</th>
                        <th>Donation count</th>
                        <th>Total giving</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topDonors as $index => $donor)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <a href="{{ route('admin.donors.show', $donor->id) }}">
                                    {{ $donor->name }}
                                </a>
                            </td>
                            <td><span class="di-pill">{{ $donor->currency }}</span></td>
                            <td>{{ number_format($donor->donation_count) }}</td>
                            <td class="font-weight-bold">
                                {{ $currencyFormat($donor->total_amount, $donor->currency) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="di-empty">No named cash donors found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- COMMUNICATION PERFORMANCE --}}
    <div class="di-section">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
            <div>
                <h5 class="di-section-title">Donor communications performance</h5>
                <p class="di-section-subtitle mb-0">Communication records grouped by channel and recorded status.</p>
            </div>
            <a href="{{ route('admin.donation-communications.index') }}" class="di-link">
                View communications →
            </a>
        </div>

        <div class="row mb-3">
            @forelse($communicationTotals as $channel => $statuses)
                <div class="col-md-4 col-sm-6 mb-3">
                    <div class="di-card">
                        <h6>{{ ucfirst($channel) }} communications</h6>
                        <div class="di-value">
                            {{ number_format($statuses->sum('total')) }}
                        </div>

                        <div class="mt-3">
                            @foreach($statuses as $status)
                                <div class="d-flex justify-content-between py-1">
                                    <span class="di-muted">{{ ucfirst(str_replace('_', ' ', $status->status)) }}</span>
                                    <strong style="font-size:13px;">{{ number_format($status->total) }}</strong>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="di-empty">No communication records found for this period.</div>
                </div>
            @endforelse
        </div>

        <div class="table-responsive">
            <table class="di-table">
                <thead>
                    <tr>
                        <th>Channel</th>
                        <th>Status</th>
                        <th>Records</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($communications as $communication)
                        <tr>
                            <td>{{ ucfirst($communication->channel) }}</td>
                            <td>
                                <span class="di-pill
                                    {{ $communication->status === 'sent' || $communication->status === 'delivered' ? 'di-pill-success' : '' }}
                                    {{ $communication->status === 'failed' ? 'di-pill-danger' : '' }}
                                    {{ $communication->status === 'pending' || $communication->status === 'scheduled' ? 'di-pill-warning' : '' }}">
                                    {{ ucfirst(str_replace('_', ' ', $communication->status)) }}
                                </span>
                            </td>
                            <td>{{ number_format($communication->total) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="di-empty">No communications recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- FOLLOW-UP OPPORTUNITIES --}}
    <div class="di-section">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
            <div>
                <h5 class="di-section-title">Donor follow-up opportunities</h5>
                <p class="di-section-subtitle mb-0">
                    Donors with missing contact information or whose last donation was more than 90 days ago.
                </p>
            </div>
            <span class="di-pill di-pill-warning">
                {{ $followUpDonors->count() }} listed
            </span>
        </div>

        <div class="table-responsive">
            <table class="di-table">
                <thead>
                    <tr>
                        <th>Donor</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Last donation</th>
                        <th>Follow-up reason</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($followUpDonors as $donor)
                        @php
                            $reasons = [];

                            if (empty($donor->email)) {
                                $reasons[] = 'Missing email';
                            }

                            if (empty($donor->phone)) {
                                $reasons[] = 'Missing phone';
                            }

                            if ($donor->last_donation_date &&
                                \Carbon\Carbon::parse($donor->last_donation_date)->lt($activeCutoff)) {
                                $reasons[] = 'Lapsed donor';
                            }
                        @endphp

                        <tr>
                            <td>
                                <a href="{{ route('admin.donors.show', $donor->id) }}">
                                    {{ $donor->name }}
                                </a>
                            </td>
                            <td>{{ $donor->email ?: 'Not provided' }}</td>
                            <td>{{ $donor->phone ?: 'Not provided' }}</td>
                            <td>
                                {{ $donor->last_donation_date
                                    ? \Carbon\Carbon::parse($donor->last_donation_date)->format('d M Y')
                                    : 'No donation recorded' }}
                            </td>
                            <td>
                                @foreach($reasons as $reason)
                                    <span class="di-pill di-pill-warning mb-1">{{ $reason }}</span>
                                @endforeach
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="di-empty">No follow-up opportunities identified.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- RECENT DONATIONS --}}
    <div class="di-section">
        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
            <div>
                <h5 class="di-section-title">Recent donations</h5>
                <p class="di-section-subtitle mb-0">Latest recorded donations matching the selected filters.</p>
            </div>
            <a href="{{ route('admin.donations.index') }}" class="di-link">All donations →</a>
        </div>

        <div class="table-responsive">
            <table class="di-table">
                <thead>
                    <tr>
                        <th>Donation number</th>
                        <th>Date</th>
                        <th>Donor</th>
                        <th>Type</th>
                        <th>Source</th>
                        <th>Amount</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentDonations as $donation)
                        <tr>
                            <td>
                                <a href="{{ route('admin.donations.show', $donation->id) }}">
                                    {{ $donation->donation_number }}
                                </a>
                            </td>
                            <td>{{ $donation->donation_date?->format('d M Y') }}</td>
                            <td>{{ optional($donation->donor)->name ?? 'Anonymous donor' }}</td>
                            <td>
                                <span class="di-pill">
                                    {{ $typeLabels[$donation->type] ?? ucfirst($donation->type) }}
                                </span>
                            </td>
                            <td>{{ $sourceLabels[$donation->source] ?? ucfirst($donation->source) }}</td>
                            <td>
                                @if($donation->type === 'cash' && $donation->amount !== null)
                                    {{ $currencyFormat($donation->amount, $donation->currency) }}
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.donations.receipt', ['donation' => $donation->id, 'type' => 'donor']) }}"
                                   title="Download donor receipt">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="di-empty">No donation records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="text-center di-muted py-3">
        Teule Kenya Donor Intelligence | Generated {{ now()->format('d M Y, H:i') }}
    </div>

</div>

{{-- CHART.JS --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const monthlyRows = @json($monthlyDonations);
    const sourceRows = @json($donationSources);
    const acquisitionRows = @json($monthlyAcquisition);

    /*
     * Monthly donation chart.
     */
    const monthLabels = [...new Set(monthlyRows.map(row => row.month))].sort();

    const cashCounts = monthLabels.map(month =>
        monthlyRows
            .filter(row => row.month === month && row.type === 'cash')
            .reduce((sum, row) => sum + Number(row.total), 0)
    );

    const inKindCounts = monthLabels.map(month =>
        monthlyRows
            .filter(row => row.month === month && row.type === 'in_kind')
            .reduce((sum, row) => sum + Number(row.total), 0)
    );

    const monthlyCanvas = document.getElementById('monthlyDonationChart');

    if (monthlyCanvas) {
        new Chart(monthlyCanvas, {
            type: 'line',
            data: {
                labels: monthLabels,
                datasets: [
                    {
                        label: 'Cash donations',
                        data: cashCounts,
                        borderColor: '#00096A',
                        backgroundColor: 'rgba(0,9,106,.10)',
                        tension: .35,
                        fill: true
                    },
                    {
                        label: 'In-kind donations',
                        data: inKindCounts,
                        borderColor: '#ef4700',
                        backgroundColor: 'rgba(239,71,0,.08)',
                        tension: .35,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { position: 'bottom' }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    }

    /*
     * Donation source chart.
     */
    const sourceCanvas = document.getElementById('donationSourceChart');

    if (sourceCanvas) {
        new Chart(sourceCanvas, {
            type: 'doughnut',
            data: {
                labels: sourceRows.map(row =>
                    row.source.replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase())
                ),
                datasets: [{
                    data: sourceRows.map(row => Number(row.total)),
                    backgroundColor: [
                        '#00096A',
                        '#ef4700',
                        '#4bbf24',
                        '#efdd13',
                        '#8992a2'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    /*
     * Donor acquisition chart.
     */
    const acquisitionCanvas = document.getElementById('donorAcquisitionChart');

    if (acquisitionCanvas) {
        new Chart(acquisitionCanvas, {
            type: 'bar',
            data: {
                labels: acquisitionRows.map(row => row.month),
                datasets: [{
                    label: 'New donors',
                    data: acquisitionRows.map(row => Number(row.total)),
                    backgroundColor: '#00096A',
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    }

});
</script>

@endsection