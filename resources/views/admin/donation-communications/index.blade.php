@extends('layouts.admin')

@section('content')

<div class="communication-page">

    {{-- Header --}}
    <div class="page-header">

        <div>

            <div class="text-muted small mb-1">
                Donations / Communications
            </div>

            <h5 class="mb-0">
                Communication History
            </h5>

        </div>

        <a href="{{ route('admin.donations.index') }}"
           class="btn btn-light btn-sm">

            <i class="fas fa-arrow-left me-1"></i>

            Donations

        </a>

    </div>


    <x-message></x-message>


    {{-- Communication table --}}
    <div class="simple-card">

        <div class="simple-card-header">

            <div>

                <strong>
                    Messages
                </strong>

                <div class="text-muted small">

                    {{ $communications->count() }}

                    {{ $communications->count() === 1
                        ? 'communication'
                        : 'communications'
                    }}

                </div>

            </div>

        </div>


        <div class="table-responsive">

            <table id="datatable"
                   class="table communications-table mb-0">

                <thead>

                    <tr>

                        <th>
                            Donor
                        </th>

                        <th>
                            Donation
                        </th>

                        <th>
                            Channel
                        </th>

                        <th>
                            Recipient
                        </th>

                        <th>
                            Type
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Date
                        </th>

                        <th class="text-end">
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach($communications as $communication)

                        @php

                            $statusClasses = [

                                'pending'   => 'status-pending',

                                'sending'   => 'status-sending',

                                'sent'      => 'status-sent',

                                'failed'    => 'status-failed',

                                'cancelled' => 'status-cancelled',

                            ];

                            $statusClass =
                                $statusClasses[$communication->status]
                                ?? 'status-pending';

                        @endphp


                        <tr>

                            {{-- Donor --}}
                            <td>

                                @if($communication->donor)

                                    <div class="donor-name">

                                        {{ $communication->donor->name }}

                                    </div>

                                    <small class="text-muted">

                                        {{ $communication->donor->donor_number }}

                                    </small>

                                @else

                                    <span class="text-muted">

                                        Unknown donor

                                    </span>

                                @endif

                            </td>


                            {{-- Donation --}}
                            <td>

                                @if($communication->donation)

                                    <a href="{{ route(
                                        'admin.donations.show',
                                        $communication->donation
                                    ) }}"
                                       class="donation-link">

                                        {{ $communication->donation->donation_number }}

                                    </a>

                                @else

                                    <span class="text-muted">
                                        —
                                    </span>

                                @endif

                            </td>


                            {{-- Channel --}}
                            <td>

                                @if($communication->channel === 'sms')

                                    <span class="channel-badge">

                                        <i class="fas fa-sms me-1"></i>

                                        SMS

                                    </span>

                                @elseif($communication->channel === 'email')

                                    <span class="channel-badge">

                                        <i class="fas fa-envelope me-1"></i>

                                        Email

                                    </span>

                                @else

                                    <span class="channel-badge">

                                        {{ ucfirst($communication->channel) }}

                                    </span>

                                @endif

                            </td>


                            {{-- Recipient --}}
                            <td>

                                {{ $communication->recipient }}

                            </td>


                            {{-- Type --}}
                            <td>

                                {{ ucfirst(str_replace(
                                    '_',
                                    ' ',
                                    $communication->type
                                )) }}

                            </td>


                            {{-- Status --}}
                            <td>

                                <span class="status-badge {{ $statusClass }}">

                                    {{ ucfirst($communication->status) }}

                                </span>

                            </td>


                            {{-- Date --}}
                            <td class="text-nowrap">

                                {{ $communication->created_at?->format(
                                    'd M Y H:i'
                                ) }}

                            </td>


                            {{-- Action --}}
                            <td class="text-end">

                                <a href="{{ route(
                                    'admin.donation-communications.show',
                                    $communication
                                ) }}"
                                   class="btn btn-light btn-sm"
                                   title="View">

                                    <i class="fas fa-eye"></i>

                                </a>

                            </td>

                        </tr>

                    @endforeach

                </tbody>

            </table>

        </div>

    </div>

</div>


<style>

    .communication-page {
        padding-bottom: 30px;
    }


    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
    }


    .simple-card {
        background: #fff;
        border: 1px solid #e6e8eb;
        border-radius: 8px;
        overflow: hidden;
    }


    .simple-card-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 20px;
        border-bottom: 1px solid #e9ecef;
    }


    .communications-table {
        font-size: 13px;
    }


    .communications-table thead th {
        background: #f8f9fa;
        color: #6c757d;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .25px;
        padding: 11px 15px;
        border-bottom: 1px solid #e9ecef;
        white-space: nowrap;
    }


    .communications-table tbody td {
        padding: 13px 15px;
        vertical-align: middle;
        border-bottom: 1px solid #f0f1f2;
    }


    .communications-table tbody tr:last-child td {
        border-bottom: 0;
    }


    .communications-table tbody tr:hover {
        background: #fafbfc;
    }


    .donor-name {
        font-weight: 500;
    }


    .donation-link {
        color: #343a40;
        font-weight: 600;
        text-decoration: none;
    }


    .donation-link:hover {
        text-decoration: underline;
    }


    .channel-badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        background: #f1f3f5;
        color: #495057;
        font-size: 11px;
        font-weight: 500;
        white-space: nowrap;
    }


    .status-badge {
        display: inline-block;
        padding: 5px 9px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 500;
        white-space: nowrap;
    }


    .status-pending {
        background: #fff8e1;
        color: #a36b00;
    }


    .status-sending {
        background: #e3f2fd;
        color: #1565c0;
    }


    .status-sent {
        background: #e8f5e9;
        color: #2e7d32;
    }


    .status-failed {
        background: #ffebee;
        color: #c62828;
    }


    .status-cancelled {
        background: #f1f3f5;
        color: #6c757d;
    }


    @media (max-width: 767px) {

        .page-header {
            align-items: flex-start;
            flex-direction: column;
        }


        .page-header .btn {
            width: 100%;
        }

    }

</style>

@endsection