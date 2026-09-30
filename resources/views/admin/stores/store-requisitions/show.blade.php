@extends('layouts.admin')

@section('content')

@php
    /*
    |--------------------------------------------------------------------------
    | BASIC STATE
    |--------------------------------------------------------------------------
    */

    $status = strtolower($storeRequisition->status ?? 'draft');

    $approvalStage = strtolower(
        $storeRequisition->approval_stage ?? 'none'
    );

    $isDraft = $status === 'draft';

    $isPending = in_array(
        $status,
        ['pending', 'submitted'],
        true
    );

    $isApproved =
        $status === 'approved'
        && $approvalStage === 'approved';

    $isRejected = $status === 'rejected';

    $isReturned = $status === 'returned';

    $isRequester =
        (int) $storeRequisition->requested_by
        === (int) auth()->id();


    /*
    |--------------------------------------------------------------------------
    | STATUS
    |--------------------------------------------------------------------------
    */

    $statusClass = match ($status) {

        'draft' =>
            'requisition-status-draft',

        'pending',
        'submitted' =>
            'requisition-status-pending',

        'approved' =>
            'requisition-status-approved',

        'returned' =>
            'requisition-status-returned',

        'rejected' =>
            'requisition-status-rejected',

        default =>
            'requisition-status-draft',
    };


    $statusLabel = ucfirst(
        str_replace('_', ' ', $status)
    );


    /*
    |--------------------------------------------------------------------------
    | USER PERMISSIONS
    |--------------------------------------------------------------------------
    */

    $user = auth()->user();

    $canApproveGeneral =
        $user->can(
            'APPROVE STORE REQUISITIONS'
        );

    $canApproveHod =
        $user->can(
            'APPROVE STORE REQUISITIONS - HOD'
        );

    $canApproveManagement =
        $user->can(
            'APPROVE STORE REQUISITIONS - MANAGEMENT'
        );

    $canApproveStores =
        $user->can(
            'APPROVE STORE REQUISITIONS - STORES'
        );


    /*
    |--------------------------------------------------------------------------
    | CURRENT STAGE APPROVAL AUTHORITY
    |--------------------------------------------------------------------------
    */

    $canApproveCurrentStage = false;

    if ($approvalStage === 'hod') {

        $canApproveCurrentStage =
            $canApproveGeneral
            || $canApproveHod;

    } elseif ($approvalStage === 'management') {

        $canApproveCurrentStage =
            $canApproveGeneral
            || $canApproveManagement;

    } elseif ($approvalStage === 'stores') {

        $canApproveCurrentStage =
            $canApproveGeneral
            || $canApproveStores;

    }


    /*
    |--------------------------------------------------------------------------
    | APPROVAL ACTIONS
    |--------------------------------------------------------------------------
    */

    $showApprovalActions =
        $isPending
        && $canApproveCurrentStage;


    /*
    |--------------------------------------------------------------------------
    | APPROVAL STAGE LABEL
    |--------------------------------------------------------------------------
    */

    $stageLabel = match ($approvalStage) {

        'hod' =>
            'HOD Approval',

        'management' =>
            'Management Approval',

        'stores' =>
            'Stores Approval',

        'approved' =>
            'Fully Approved',

        default =>
            'Not Submitted',

    };


    /*
    |--------------------------------------------------------------------------
    | FULFILLMENT STATE
    |--------------------------------------------------------------------------
    */

    $hasOutstanding = false;

    $hasIssued = false;

    foreach ($storeRequisition->items as $requisitionItem) {

        if (
            (float) $requisitionItem->issued_quantity > 0
        ) {
            $hasIssued = true;
        }

        if ($isApproved) {

            $approvedQuantity =
                (float) $requisitionItem->approved_quantity;

            $issuedQuantity =
                (float) $requisitionItem->issued_quantity;

            $outstandingQuantity =
                max(
                    0,
                    $approvedQuantity - $issuedQuantity
                );

            if ($outstandingQuantity > 0) {
                $hasOutstanding = true;
            }
        }
    }


    $fulfillmentLabel = match (
        $storeRequisition->fulfillment_status
    ) {

        'fully_issued' =>
            'Fully Fulfilled',

        'partially_issued' =>
            'Partially Fulfilled',

        default =>
            'Not Issued',
    };


    $fulfillmentBadge = match (
        $storeRequisition->fulfillment_status
    ) {

        'fully_issued' =>
            'stores-badge-success',

        'partially_issued' =>
            'stores-badge-warning',

        default =>
            'stores-badge-muted',
    };


    /*
    |--------------------------------------------------------------------------
    | TOTALS
    |--------------------------------------------------------------------------
    */

    $totalRequested = 0;
    $totalApproved = 0;
    $totalIssued = 0;
    $totalOutstanding = 0;

    foreach ($storeRequisition->items as $item) {

        $totalRequested +=
            (float) $item->requested_quantity;

        if ($isApproved) {

            $totalApproved +=
                (float) $item->approved_quantity;

            $totalIssued +=
                (float) $item->issued_quantity;

            $totalOutstanding += max(
                0,
                (float) $item->approved_quantity
                -
                (float) $item->issued_quantity
            );
        }
    }

@endphp


{{-- =========================================================
     PAGE HEADER
     ========================================================= --}}

<div class="requisition-page-header">

    <div class="requisition-header-content">

        <div class="requisition-header-icon">

            <i class="fas fa-file-invoice"></i>

        </div>

        <div>

            <div class="requisition-breadcrumb">

                Stores

                <span>/</span>

                Requisitions

                <span>/</span>

                Details

            </div>

            <h1 class="requisition-page-title">

                {{ $storeRequisition->requisition_number }}

            </h1>

            <p class="requisition-page-subtitle">

                Store requisition

            </p>

        </div>

    </div>


    <div class="requisition-header-right">

        {{-- =================================================
             DRAFT ACTIONS
             ================================================= --}}

        @if(
            $isDraft
            && $isRequester
        )

            <a
                href="{{ route(
                    'admin.stores.store-requisitions.edit',
                    $storeRequisition
                ) }}"
                class="stores-btn-light"
            >

                <i class="fas fa-pen me-1"></i>

                Edit

            </a>

            <form
                method="POST"
                action="{{ route(
                    'admin.stores.store-requisitions.submit',
                    $storeRequisition
                ) }}"
                class="d-inline"
                onsubmit="return confirm(
                    'Submit this requisition for approval? You will not be able to edit it while it is under approval.'
                );"
            >

                @csrf

                <button
                    type="submit"
                    class="stores-btn-primary"
                >

                    <i class="fas fa-paper-plane me-1"></i>

                    Submit

                </button>

            </form>

        @endif


        {{-- =================================================
             STATUS
             ================================================= --}}

        <span class="requisition-list-status {{ $statusClass }}">

            <span class="requisition-list-status-dot"></span>

            {{ $statusLabel }}

        </span>

    </div>

</div>


{{-- =========================================================
     FLASH MESSAGES
     ========================================================= --}}

@if(session('success'))

    <div class="alert alert-success">

        <i class="fas fa-check-circle me-1"></i>

        {{ session('success') }}

    </div>

@endif


@if(session('error'))

    <div class="alert alert-danger">

        <i class="fas fa-exclamation-circle me-1"></i>

        {{ session('error') }}

    </div>

@endif


@if($errors->any())

    <div class="alert alert-danger">

        <strong>
            Please correct the following:
        </strong>

        <ul class="mb-0 mt-2">

            @foreach($errors->all() as $error)

                <li>
                    {{ $error }}
                </li>

            @endforeach

        </ul>

    </div>

@endif


{{-- =========================================================
     MINIMAL TOP CARD
     ========================================================= --}}

<div class="requisition-section">

    <div class="requisition-section-header">

        <div class="requisition-section-heading">

            <div class="requisition-section-icon">

                <i class="fas fa-file-alt"></i>

            </div>

            <div>

                <h5>
                    Requisition
                </h5>

                <p>
                    {{ $stageLabel }}
                </p>

            </div>

        </div>


        <div class="d-flex align-items-center gap-2">

            {{-- DETAILS MODAL --}}

            <button
                type="button"
                class="requisition-list-action"
                data-toggle="modal"
                data-target="#requisitionDetailsModal"
                title="View requisition details"
            >

                <i class="fas fa-info-circle"></i>

            </button>


            {{-- APPROVAL HISTORY --}}

            @if(
                $storeRequisition->approvals->count()
            )

                <button
                    type="button"
                    class="requisition-list-action"
                    data-toggle="modal"
                    data-target="#approvalHistoryModal"
                    title="Approval history"
                >

                    <i class="fas fa-history"></i>

                </button>

            @endif


            {{-- FULFILLMENT HISTORY --}}

            @if(
                $isApproved
                && $storeRequisition->fulfillments->count()
            )

                <button
                    type="button"
                    class="requisition-list-action"
                    data-toggle="modal"
                    data-target="#fulfillmentHistoryModal"
                    title="Fulfillment history"
                >

                    <i class="fas fa-exchange-alt"></i>

                </button>

            @endif

        </div>

    </div>


    <div class="requisition-section-body">

        {{-- =================================================
             COMPACT SUMMARY
             ================================================= --}}

        <div class="stores-detail-grid">

            <div class="stores-detail">

                <span class="stores-detail-label">
                    Requested By
                </span>

                <span class="stores-detail-value">

                    {{
                        optional(
                            $storeRequisition->requester
                        )->name
                        ?? '—'
                    }}

                </span>

            </div>


            <div class="stores-detail">

                <span class="stores-detail-label">
                    Department
                </span>

                <span class="stores-detail-value">

                    {{
                        $storeRequisition->department
                        ?: '—'
                    }}

                </span>

            </div>


            <div class="stores-detail">

                <span class="stores-detail-label">
                    Type
                </span>

                <span class="stores-detail-value">

                    @if(
                        $storeRequisition->requisition_type
                        === 'TRANSFER'
                    )

                        <span class="stores-badge stores-badge-info">

                            <i class="fas fa-exchange-alt"></i>

                            Transfer

                        </span>

                    @else

                        <span class="stores-badge stores-badge-primary">

                            <i class="fas fa-box"></i>

                            Item Issue

                        </span>

                    @endif

                </span>

            </div>


            <div class="stores-detail">

                <span class="stores-detail-label">
                    Source Store
                </span>

                <span class="stores-detail-value">

                    {{
                        optional(
                            $storeRequisition->sourceStore
                        )->name
                        ?? '—'
                    }}

                </span>

            </div>


            @if(
                $storeRequisition->requisition_type
                === 'TRANSFER'
            )

                <div class="stores-detail">

                    <span class="stores-detail-label">
                        Destination
                    </span>

                    <span class="stores-detail-value">

                        {{
                            optional(
                                $storeRequisition->destinationStore
                            )->name
                            ?? '—'
                        }}

                    </span>

                </div>

            @endif


            <div class="stores-detail">

                <span class="stores-detail-label">
                    Approval Stage
                </span>

                <span class="stores-detail-value">

                    {{ $stageLabel }}

                </span>

            </div>

        </div>


        {{-- =================================================
             APPROVAL ACTION BAR
             ================================================= --}}




        {{-- =================================================
             APPROVED SUMMARY
             ================================================= --}}

        @if($isApproved)

            <div class="stores-divider"></div>

            <div class="d-flex align-items-center justify-content-between flex-wrap">

                <div>

                    <span class="stores-detail-label">
                        Fulfillment
                    </span>

                    <div class="mt-1">

                        <span class="stores-badge {{ $fulfillmentBadge }}">

                            @if(
                                $storeRequisition->fulfillment_status
                                === 'fully_issued'
                            )

                                <i class="fas fa-check-circle"></i>

                            @else

                                <i class="fas fa-box-open"></i>

                            @endif

                            {{ $fulfillmentLabel }}

                        </span>

                    </div>

                </div>


                @if($hasOutstanding)

                    <button
                        type="button"
                        class="stores-btn-primary"
                        data-toggle="modal"
                        data-target="#fulfillmentModal"
                    >

                        <i class="fas fa-box-open me-1"></i>

                        Fulfill Outstanding

                    </button>

                @else

                    <span class="stores-badge stores-badge-success">

                        <i class="fas fa-check-circle"></i>

                        Nothing Outstanding

                    </span>

                @endif

            </div>

        @endif

    </div>

</div>


{{-- =========================================================
     ITEMS
     ========================================================= --}}

<div class="requisition-section">

    <div class="requisition-section-header">

        <div class="requisition-section-heading">

            <div class="requisition-section-icon requisition-items-icon">

                <i class="fas fa-boxes"></i>

            </div>

            <div>

                <h5>
                    Requisition Items
                </h5>

                <p>
                    Quantities and fulfillment status
                </p>

            </div>

        </div>


        <div class="d-flex align-items-center gap-2">

            <span class="requisition-count-badge">

                {{ $storeRequisition->items->count() }}

                {{
                    Str::plural(
                        'item',
                        $storeRequisition->items->count()
                    )
                }}

            </span>

        </div>

    </div>


    {{-- =====================================================
         QUANTITY SUMMARY
         ===================================================== --}}

    <div class="requisition-section-body pb-0">

        <div class="stores-detail-grid">

            <div class="stores-detail">

                <span class="stores-detail-label">
                    Requested
                </span>

                <span class="stores-detail-value">

                    {{
                        number_format(
                            $totalRequested,
                            3
                        )
                    }}

                </span>

            </div>


            <div class="stores-detail">

                <span class="stores-detail-label">
                    Approved
                </span>

                <span class="stores-detail-value">

                    {{
                        number_format(
                            $totalApproved,
                            3
                        )
                    }}

                </span>

            </div>


            <div class="stores-detail">

                <span class="stores-detail-label">
                    Issued
                </span>

                <span class="stores-detail-value">

                    {{
                        number_format(
                            $totalIssued,
                            3
                        )
                    }}

                </span>

            </div>


            <div class="stores-detail">

                <span class="stores-detail-label">
                    Outstanding
                </span>

                <span class="stores-detail-value">

                    @if(
                        !$isApproved
                    )

                        0.000

                    @elseif(
                        $totalOutstanding > 0
                    )

                        <span class="stores-quantity outstanding">

                            {{
                                number_format(
                                    $totalOutstanding,
                                    3
                                )
                            }}

                        </span>

                    @else

                        <span class="stores-quantity complete">

                            <i class="fas fa-check"></i>

                            0.000

                        </span>

                    @endif

                </span>

            </div>

        </div>

    </div>


    <div class="stores-table-wrapper">

        <table class="stores-table requisition-list-table">

            <thead>

                <tr>

                    <th style="width:45px;">
                        #
                    </th>

                    <th>
                        Item
                    </th>

                    <th>
                        Variant
                    </th>

                    <th>
                        Unit
                    </th>

                    <th class="text-end">
                        Requested
                    </th>

                    <th class="text-end">
                        Approved
                    </th>

                    <th class="text-end">
                        Issued
                    </th>

                    <th class="text-end">
                        Outstanding
                    </th>

                </tr>

            </thead>


            <tbody>

                @forelse(
                    $storeRequisition->items as $item
                )

                    @php

                        $requested =
                            (float)
                            $item->requested_quantity;

                        /*
                        |--------------------------------------------------------------------------
                        | BEFORE APPROVAL
                        |--------------------------------------------------------------------------
                        |
                        | Approved = 0
                        | Issued = 0
                        | Outstanding = 0
                        |
                        |--------------------------------------------------------------------------
                        | AFTER APPROVAL
                        |--------------------------------------------------------------------------
                        |
                        | Approved = approved_quantity
                        | Issued = issued_quantity
                        | Outstanding = approved - issued
                        |
                        */

                        if ($isApproved) {

                            $approved =
                                (float)
                                $item->approved_quantity;

                            $issued =
                                (float)
                                $item->issued_quantity;

                            $outstanding =
                                max(
                                    0,
                                    $approved - $issued
                                );

                        } else {

                            $approved = 0;

                            $issued = 0;

                            $outstanding = 0;

                        }

                    @endphp


                    <tr>

                        {{-- NUMBER --}}

                        <td>

                            <span class="requisition-row-number">

                                {{ $loop->iteration }}

                            </span>

                        </td>


                        {{-- ITEM --}}

                        <td>

                            <div class="stores-item-name">

                                {{
                                    optional(
                                        $item->item
                                    )->name
                                    ??
                                    'Item #'
                                    .
                                    $item->store_item_id
                                }}

                            </div>


                            @if(
                                $item->item?->item_code
                            )

                                <div class="stores-code">

                                    {{
                                        $item->item->item_code
                                    }}

                                </div>

                            @endif

                        </td>


                        {{-- VARIANT --}}

                        <td>

                            @if(
                                $item->variant
                            )

                                <span class="stores-variant">

                                    {{
                                        $item->variant->name
                                    }}

                                </span>

                            @else

                                <span class="text-muted">
                                    —
                                </span>

                            @endif

                        </td>


                        {{-- UNIT --}}

                        <td>

                            {{
                                optional(
                                    $item->item?->unit
                                )->name
                                ?? '—'
                            }}

                        </td>


                        {{-- REQUESTED --}}

                        <td class="text-end">

                            <strong>

                                {{
                                    number_format(
                                        $requested,
                                        3
                                    )
                                }}

                            </strong>

                        </td>


                        {{-- APPROVED --}}

                        <td class="text-end">

                            @if($isApproved)

                                <span class="stores-quantity approved">

                                    {{
                                        number_format(
                                            $approved,
                                            3
                                        )
                                    }}

                                </span>

                            @else

                                <span class="text-muted">
                                    0.000
                                </span>

                            @endif

                        </td>


                        {{-- ISSUED --}}

                        <td class="text-end">

                            @if(
                                $isApproved
                                && $issued > 0
                            )

                                <span class="stores-quantity issued">

                                    {{
                                        number_format(
                                            $issued,
                                            3
                                        )
                                    }}

                                </span>

                            @else

                                <span class="text-muted">
                                    0.000
                                </span>

                            @endif

                        </td>


                        {{-- OUTSTANDING --}}

                        <td class="text-end">

                            @if(!$isApproved)

                                <span class="text-muted">
                                    0.000
                                </span>

                            @elseif(
                                $outstanding > 0
                            )

                                <span class="stores-quantity outstanding">

                                    {{
                                        number_format(
                                            $outstanding,
                                            3
                                        )
                                    }}

                                </span>

                            @else

                                <span class="stores-quantity complete">

                                    <i class="fas fa-check"></i>

                                    0.000

                                </span>

                            @endif

                        </td>

                    </tr>


                    {{-- ITEM NOTES --}}

                    @if($item->notes)

                        <tr>

                            <td></td>

                            <td colspan="7">

                                <div class="stores-item-meta">

                                    <i class="fas fa-info-circle"></i>

                                    {{ $item->notes }}

                                </div>

                            </td>

                        </tr>

                    @endif

                @empty

                    <tr>

                        <td
                            colspan="8"
                            class="text-center py-5"
                        >

                            <div class="text-muted">

                                <i
                                    class="fas fa-box-open fa-2x mb-2"
                                ></i>

                                <div>
                                    No items found.
                                </div>

                            </div>

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>


    {{-- =====================================================
         APPROVAL QUANTITY MESSAGE
         ===================================================== --}}

    @if(!$isApproved)

        <div class="requisition-info-banner">

            <div class="requisition-info-banner-icon">

                <i class="fas fa-info-circle"></i>

            </div>


@if($showApprovalActions)

    <div class="stores-divider"></div>

    <div class="requisition-approval-panel"
         style="display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; gap:16px;">

        <div class="requisition-approval-panel-content"
             style="display:flex; flex-direction:column; align-items:center; justify-content:center;">

            <div class="requisition-approval-panel-icon">
                <i class="fas fa-user-check"></i>
            </div>

            <div class="mt-2 mb-4">
                <strong>
                    Action Required
                </strong>

                <div class="text-muted">
                    Awaiting your {{ strtolower($stageLabel) }}
                    decision.
                </div>
            </div>

        </div>

        <div class="requisition-approval-actions mb-5"
             style="display:flex; align-items:center; justify-content:center; gap:10px;">

            {{-- APPROVE --}}
            <form
                method="POST"
                action="{{ route(
                    'admin.stores.store-requisitions.approve',
                    $storeRequisition
                ) }}"
                class="d-inline"
                onsubmit="return confirm(
                    'Approve this requisition? The requested quantities will become the approved quantities.'
                );"
            >
                @csrf

                <button
                    type="submit"
                    class="stores-btn-primary"
                >
                    <i class="fas fa-check me-1"></i>
                    Approve
                </button>
            </form>

            {{-- SEND BACK --}}
            <button
                type="button"
                class="stores-btn-light"
                data-toggle="modal"
                data-target="#sendBackModal"
            >
                <i class="fas fa-undo me-1"></i>
                Send Back
            </button>

            {{-- REJECT --}}
            <button
                type="button"
                class="stores-btn-light requisition-danger-action"
                data-toggle="modal"
                data-target="#rejectModal"
            >
                <i class="fas fa-times me-1"></i>
                Reject
            </button>

        </div>

    </div>

@elseif($isPending)

    <div class="stores-divider"></div>

    <div class="d-flex align-items-center justify-content-center">
        <span class="stores-badge stores-badge-warning">
            <i class="fas fa-hourglass-half"></i>
            Awaiting {{ $stageLabel }}
        </span>
    </div>

@endif


        </div>

    @endif


    {{-- =====================================================
         FULLY FULFILLED
         ===================================================== --}}

    @if(
        $isApproved
        && !$hasOutstanding
    )

        <div class="requisition-success-banner">

            <div class="requisition-success-banner-icon">

                <i class="fas fa-check-circle"></i>

            </div>

            <div>

                <strong>
                    Requisition fully fulfilled
                </strong>

                <div>

                    All approved quantities have been physically
                    issued or transferred.

                </div>

            </div>

        </div>

    @endif

</div>


{{-- =========================================================
     REQUISITION DETAILS MODAL
     ========================================================= --}}

<div
    class="modal fade stores-modal"
    id="requisitionDetailsModal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="requisitionDetailsModalLabel"
    aria-hidden="true"
>

    <div
        class="modal-dialog modal-lg modal-dialog-centered"
        role="document"
    >

        <div class="modal-content">

            <div class="modal-header">

                <div>

                    <h5
                        class="modal-title"
                        id="requisitionDetailsModalLabel"
                    >

                        <i class="fas fa-info-circle me-2"></i>

                        Requisition Details

                    </h5>

                    <div class="small opacity-75 mt-1">

                        {{ $storeRequisition->requisition_number }}

                    </div>

                </div>


                <button
                    type="button"
                    class="btn-close"
                    data-dismiss="modal"
                    aria-label="Close"
                ></button>

            </div>


            <div class="modal-body">

                <div class="stores-detail-grid">

                    {{-- NUMBER --}}

                    <div class="stores-detail">

                        <span class="stores-detail-label">
                            Requisition Number
                        </span>

                        <span class="stores-detail-value">
                            {{ $storeRequisition->requisition_number }}
                        </span>

                    </div>


                    {{-- REQUESTER --}}

                    <div class="stores-detail">

                        <span class="stores-detail-label">
                            Requested By
                        </span>

                        <span class="stores-detail-value">

                            {{
                                optional(
                                    $storeRequisition->requester
                                )->name
                                ?? '—'
                            }}

                        </span>

                    </div>


                    {{-- DEPARTMENT --}}

                    <div class="stores-detail">

                        <span class="stores-detail-label">
                            Department
                        </span>

                        <span class="stores-detail-value">

                            {{
                                $storeRequisition->department
                                ?: '—'
                            }}

                        </span>

                    </div>


                    {{-- TYPE --}}

                    <div class="stores-detail">

                        <span class="stores-detail-label">
                            Request Type
                        </span>

                        <span class="stores-detail-value">

                            @if(
                                $storeRequisition->requisition_type
                                === 'TRANSFER'
                            )

                                <span class="stores-badge stores-badge-info">

                                    <i class="fas fa-exchange-alt"></i>

                                    Stock Transfer

                                </span>

                            @else

                                <span class="stores-badge stores-badge-primary">

                                    <i class="fas fa-box"></i>

                                    Item Issue

                                </span>

                            @endif

                        </span>

                    </div>


                    {{-- SOURCE --}}

                    <div class="stores-detail">

                        <span class="stores-detail-label">
                            Source Store
                        </span>

                        <span class="stores-detail-value">

                            {{
                                optional(
                                    $storeRequisition->sourceStore
                                )->name
                                ?? '—'
                            }}

                        </span>

                    </div>


                    {{-- DESTINATION --}}

                    @if(
                        $storeRequisition->requisition_type
                        === 'TRANSFER'
                    )

                        <div class="stores-detail">

                            <span class="stores-detail-label">
                                Destination Store
                            </span>

                            <span class="stores-detail-value">

                                {{
                                    optional(
                                        $storeRequisition
                                            ->destinationStore
                                    )->name
                                    ?? '—'
                                }}

                            </span>

                        </div>

                    @endif


                    {{-- CREATED --}}

                    <div class="stores-detail">

                        <span class="stores-detail-label">
                            Created
                        </span>

                        <span class="stores-detail-value">

                            @if(
                                $storeRequisition->created_at
                            )

                                {{
                                    $storeRequisition
                                        ->created_at
                                        ->format(
                                            'd M Y, H:i'
                                        )
                                }}

                            @else

                                —

                            @endif

                        </span>

                    </div>


                    {{-- SUBMITTED --}}

                    <div class="stores-detail">

                        <span class="stores-detail-label">
                            Submitted
                        </span>

                        <span class="stores-detail-value">

                            @if(
                                $storeRequisition->submitted_at
                            )

                                {{
                                    $storeRequisition
                                        ->submitted_at
                                        ->format(
                                            'd M Y, H:i'
                                        )
                                }}

                            @else

                                <span class="text-muted">
                                    Not submitted
                                </span>

                            @endif

                        </span>

                    </div>


                    {{-- APPROVAL STAGE --}}

                    <div class="stores-detail">

                        <span class="stores-detail-label">
                            Approval Stage
                        </span>

                        <span class="stores-detail-value">
                            {{ $stageLabel }}
                        </span>

                    </div>


                    {{-- APPROVED AT --}}

                    <div class="stores-detail">

                        <span class="stores-detail-label">
                            Approved At
                        </span>

                        <span class="stores-detail-value">

                            @if(
                                $storeRequisition->approved_at
                            )

                                {{
                                    $storeRequisition
                                        ->approved_at
                                        ->format(
                                            'd M Y, H:i'
                                        )
                                }}

                            @else

                                <span class="text-muted">
                                    Not approved
                                </span>

                            @endif

                        </span>

                    </div>

                </div>


                {{-- PURPOSE --}}

                @if(
                    $storeRequisition->purpose
                )

                    <div class="stores-divider"></div>

                    <div>

                        <span class="stores-detail-label">
                            Purpose
                        </span>

                        <div class="mt-1">

                            {{ $storeRequisition->purpose }}

                        </div>

                    </div>

                @endif


                {{-- SUBMISSION NOTES --}}

                @if(
                    $storeRequisition->submission_notes
                )

                    <div class="stores-divider"></div>

                    <div>

                        <span class="stores-detail-label">
                            Submission Notes
                        </span>

                        <div class="mt-1">

                            {{ $storeRequisition->submission_notes }}

                        </div>

                    </div>

                @endif

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn stores-btn-light"
                    data-dismiss="modal"
                >
                    Close
                </button>

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     APPROVAL HISTORY MODAL
     ========================================================= --}}

@if(
    $storeRequisition->approvals->count()
)

<div
    class="modal fade stores-modal"
    id="approvalHistoryModal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="approvalHistoryModalLabel"
    aria-hidden="true"
>

    <div
        class="modal-dialog modal-lg modal-dialog-centered"
        role="document"
    >

        <div class="modal-content">

            <div class="modal-header">

                <div>

                    <h5
                        class="modal-title"
                        id="approvalHistoryModalLabel"
                    >

                        <i class="fas fa-history me-2"></i>

                        Approval History

                    </h5>

                    <div class="small opacity-75 mt-1">

                        {{ $storeRequisition->requisition_number }}

                    </div>

                </div>


                <button
                    type="button"
                    class="btn-close"
                    data-dismiss="modal"
                    aria-label="Close"
                ></button>

            </div>


            <div class="modal-body">

                <div class="stores-timeline">

                    @foreach(
                        $storeRequisition
                            ->approvals
                            ->sortBy('decided_at')
                        as $approval
                    )

                        <div class="stores-timeline-item">

                            <div class="stores-timeline-dot">

                                @if(
                                    $approval->decision
                                    === 'approved'
                                )

                                    <i class="fas fa-check"></i>

                                @elseif(
                                    $approval->decision
                                    === 'rejected'
                                )

                                    <i class="fas fa-times"></i>

                                @else

                                    <i class="fas fa-undo"></i>

                                @endif

                            </div>


                            <div>

                                <div class="stores-timeline-title">

                                    {{
                                        ucfirst(
                                            $approval
                                                ->approval_level
                                        )
                                    }}

                                    —

                                    {{
                                        ucfirst(
                                            $approval->decision
                                        )
                                    }}

                                </div>


                                <div class="stores-timeline-meta">

                                    {{
                                        optional(
                                            $approval
                                                ->approver
                                        )->name
                                        ?? 'Unknown user'
                                    }}

                                    @if(
                                        $approval->decided_at
                                    )

                                        <span>
                                            •
                                        </span>

                                        {{
                                            $approval
                                                ->decided_at
                                                ->format(
                                                    'd M Y, H:i'
                                                )
                                        }}

                                    @endif

                                </div>


                                @if(
                                    $approval->comments
                                )

                                    <div class="mt-2">

                                        {{ $approval->comments }}

                                    </div>

                                @endif

                            </div>

                        </div>

                    @endforeach

                </div>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn stores-btn-light"
                    data-dismiss="modal"
                >
                    Close
                </button>

            </div>

        </div>

    </div>

</div>

@endif


{{-- =========================================================
     SEND BACK MODAL
     ========================================================= --}}

@if($showApprovalActions)

<div
    class="modal fade stores-modal"
    id="sendBackModal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="sendBackModalLabel"
    aria-hidden="true"
>

    <div
        class="modal-dialog modal-dialog-centered"
        role="document"
    >

        <div class="modal-content">

            <div class="modal-header">

                <h5
                    class="modal-title"
                    id="sendBackModalLabel"
                >

                    <i class="fas fa-undo me-2"></i>

                    Send Requisition Back

                </h5>


                <button
                    type="button"
                    class="btn-close"
                    data-dismiss="modal"
                    aria-label="Close"
                ></button>

            </div>


            <form
                method="POST"
                action="{{ route(
                    'admin.stores.store-requisitions.send-back',
                    $storeRequisition
                ) }}"
            >

                @csrf

                <div class="modal-body">

                    <p class="mb-3">

                        Send this requisition back to the requester
                        for correction?

                    </p>


                    <label
                        for="sendBackComments"
                        class="form-label fw-semibold"
                    >
                        Reason
                    </label>


                    <textarea
                        name="comments"
                        id="sendBackComments"
                        class="form-control"
                        rows="4"
                        maxlength="2000"
                        required
                        placeholder="Explain what needs to be corrected..."
                    ></textarea>

                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn stores-btn-light"
                        data-dismiss="modal"
                    >
                        Cancel
                    </button>


                    <button
                        type="submit"
                        class="stores-btn-primary"
                    >

                        <i class="fas fa-undo me-1"></i>

                        Send Back

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endif


{{-- =========================================================
     REJECT MODAL
     ========================================================= --}}

@if($showApprovalActions)

<div
    class="modal fade stores-modal"
    id="rejectModal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="rejectModalLabel"
    aria-hidden="true"
>

    <div
        class="modal-dialog modal-dialog-centered"
        role="document"
    >

        <div class="modal-content">

            <div class="modal-header">

                <h5
                    class="modal-title"
                    id="rejectModalLabel"
                >

                    <i class="fas fa-times me-2"></i>

                    Reject Requisition

                </h5>


                <button
                    type="button"
                    class="btn-close"
                    data-dismiss="modal"
                    aria-label="Close"
                ></button>

            </div>


            <form
                method="POST"
                action="{{ route(
                    'admin.stores.store-requisitions.reject',
                    $storeRequisition
                ) }}"
            >

                @csrf

                <div class="modal-body">

                    <p class="mb-3">

                        Reject this requisition?

                    </p>


                    <label
                        for="rejectComments"
                        class="form-label fw-semibold"
                    >
                        Reason
                    </label>


                    <textarea
                        name="comments"
                        id="rejectComments"
                        class="form-control"
                        rows="4"
                        maxlength="2000"
                        required
                        placeholder="Enter the reason for rejection..."
                    ></textarea>

                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn stores-btn-light"
                        data-dismiss="modal"
                    >
                        Cancel
                    </button>


                    <button
                        type="submit"
                        class="stores-btn-primary"
                    >

                        <i class="fas fa-times me-1"></i>

                        Reject Requisition

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endif


{{-- =========================================================
     FULFILLMENT HISTORY MODAL
     ========================================================= --}}

@if(
    $isApproved
    && $storeRequisition->fulfillments->count()
)

<div
    class="modal fade stores-modal"
    id="fulfillmentHistoryModal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="fulfillmentHistoryModalLabel"
    aria-hidden="true"
>

    <div
        class="modal-dialog modal-xl modal-dialog-centered"
        role="document"
    >

        <div class="modal-content">

            <div class="modal-header">

                <div>

                    <h5
                        class="modal-title"
                        id="fulfillmentHistoryModalLabel"
                    >

                        <i class="fas fa-exchange-alt me-2"></i>

                        Fulfillment History

                    </h5>

                    <div class="small opacity-75 mt-1">

                        {{ $storeRequisition->requisition_number }}

                    </div>

                </div>


                <button
                    type="button"
                    class="btn-close"
                    data-dismiss="modal"
                    aria-label="Close"
                ></button>

            </div>


            <div class="modal-body p-0">

                <div class="stores-table-wrapper">

                    <table class="stores-table">

                        <thead>

                            <tr>

                                <th>
                                    Transaction
                                </th>

                                <th>
                                    Type
                                </th>

                                <th>
                                    Source
                                </th>

                                <th>
                                    Destination
                                </th>

                                <th>
                                    Processed By
                                </th>

                                <th>
                                    Date
                                </th>

                                <th class="text-end">
                                    Items
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach(
                                $storeRequisition->fulfillments
                                as $fulfillment
                            )

                                <tr>

                                    <td>

                                        <span class="stores-code">

                                            {{
                                                $fulfillment
                                                    ->transaction_number
                                            }}

                                        </span>

                                    </td>


                                    <td>

                                        @if(
                                            $fulfillment
                                                ->transaction_type
                                            === 'TRANSFER'
                                        )

                                            <span class="stores-badge stores-badge-info">

                                                <i class="fas fa-exchange-alt"></i>

                                                Transfer

                                            </span>

                                        @else

                                            <span class="stores-badge stores-badge-primary">

                                                <i class="fas fa-arrow-down"></i>

                                                Issue

                                            </span>

                                        @endif

                                    </td>


                                    <td>

                                        {{
                                            optional(
                                                $fulfillment
                                                    ->sourceStore
                                            )->name
                                            ?? '—'
                                        }}

                                    </td>


                                    <td>

                                        @if(
                                            $fulfillment
                                                ->destinationStore
                                        )

                                            {{
                                                $fulfillment
                                                    ->destinationStore
                                                    ->name
                                            }}

                                        @else

                                            <span class="text-muted">
                                                Consumption / Issue
                                            </span>

                                        @endif

                                    </td>


                                    <td>

                                        {{
                                            optional(
                                                $fulfillment
                                                    ->processor
                                            )->name
                                            ?? '—'
                                        }}

                                    </td>


                                    <td>

                                        {{
                                            $fulfillment->created_at
                                                ? $fulfillment
                                                    ->created_at
                                                    ->format(
                                                        'd M Y, H:i'
                                                    )
                                                : '—'
                                        }}

                                    </td>


                                    <td class="text-end">

                                        {{
                                            $fulfillment
                                                ->items
                                                ->count()
                                        }}

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn stores-btn-light"
                    data-dismiss="modal"
                >
                    Close
                </button>

            </div>

        </div>

    </div>

</div>

@endif


{{-- =========================================================
     PHYSICAL FULFILLMENT MODAL
     ========================================================= --}}

@if(
    $isApproved
    && $hasOutstanding
)

<div
    class="modal fade stores-modal"
    id="fulfillmentModal"
    tabindex="-1"
    role="dialog"
    aria-labelledby="fulfillmentModalLabel"
    aria-hidden="true"
>

    <div
        class="modal-dialog modal-xl modal-dialog-centered"
        role="document"
    >

        <div class="modal-content">

            <div class="modal-header">

                <div>

                    <h5
                        class="modal-title"
                        id="fulfillmentModalLabel"
                    >

                        <i class="fas fa-box-open me-2"></i>

                        Physical Fulfillment

                    </h5>


                    <div class="small opacity-75 mt-1">

                        {{ $storeRequisition->requisition_number }}

                    </div>

                </div>


                <button
                    type="button"
                    class="btn-close"
                    data-dismiss="modal"
                    aria-label="Close"
                ></button>

            </div>


            <form
                method="POST"
                action="{{ route(
                    'admin.stores.store-requisitions.fulfill',
                    $storeRequisition
                ) }}"
                id="fulfillmentForm"
            >

                @csrf


                <div class="modal-body">

                    <div class="requisition-info-banner mb-3">

                        <div class="requisition-info-banner-icon">

                            <i class="fas fa-info-circle"></i>

                        </div>


                        <div>

                            <strong>
                                Outstanding quantities
                            </strong>

                            <div>

                                <strong>Issue Now</strong>
                                defaults to the full outstanding
                                approved quantity. Reduce it when
                                making a partial physical issue.

                            </div>

                        </div>

                    </div>


                    <div class="stores-table-wrapper">

                        <table class="stores-table">

                            <thead>

                                <tr>

                                    <th>
                                        Item
                                    </th>

                                    <th>
                                        Variant
                                    </th>

                                    <th>
                                        Unit
                                    </th>

                                    <th class="text-end">
                                        Approved
                                    </th>

                                    <th class="text-end">
                                        Issued
                                    </th>

                                    <th class="text-end">
                                        Outstanding
                                    </th>

                                    <th style="width:160px;">
                                        Issue Now
                                    </th>

                                </tr>

                            </thead>


                            <tbody>

                                @foreach(
                                    $storeRequisition->items
                                    as $item
                                )

                                    @php

                                        $approved =
                                            (float)
                                            $item->approved_quantity;

                                        $issued =
                                            (float)
                                            $item->issued_quantity;

                                        $outstanding =
                                            max(
                                                0,
                                                $approved - $issued
                                            );

                                    @endphp


                                    @if(
                                        $outstanding > 0
                                    )

                                        <tr>

                                            {{-- ITEM --}}

                                            <td>

                                                <div class="stores-item-name">

                                                    {{
                                                        optional(
                                                            $item->item
                                                        )->name
                                                        ??
                                                        'Item #'
                                                        .
                                                        $item->store_item_id
                                                    }}

                                                </div>


                                                @if(
                                                    $item->item?->item_code
                                                )

                                                    <div class="stores-code">

                                                        {{
                                                            $item
                                                                ->item
                                                                ->item_code
                                                        }}

                                                    </div>

                                                @endif

                                            </td>


                                            {{-- VARIANT --}}

                                            <td>

                                                @if(
                                                    $item->variant
                                                )

                                                    <span class="stores-variant">

                                                        {{
                                                            $item
                                                                ->variant
                                                                ->name
                                                        }}

                                                    </span>

                                                @else

                                                    <span class="text-muted">
                                                        —
                                                    </span>

                                                @endif

                                            </td>


                                            {{-- UNIT --}}

                                            <td>

                                                {{
                                                    optional(
                                                        $item->item?->unit
                                                    )->name
                                                    ?? '—'
                                                }}

                                            </td>


                                            {{-- APPROVED --}}

                                            <td class="text-end">

                                                {{
                                                    number_format(
                                                        $approved,
                                                        3
                                                    )
                                                }}

                                            </td>


                                            {{-- ISSUED --}}

                                            <td class="text-end">

                                                {{
                                                    number_format(
                                                        $issued,
                                                        3
                                                    )
                                                }}

                                            </td>


                                            {{-- OUTSTANDING --}}

                                            <td class="text-end">

                                                <span class="stores-quantity outstanding">

                                                    {{
                                                        number_format(
                                                            $outstanding,
                                                            3
                                                        )
                                                    }}

                                                </span>

                                            </td>


                                            {{-- ISSUE NOW --}}

                                            <td>

                                                <input
                                                    type="hidden"
                                                    name="items[{{ $loop->index }}][requisition_item_id]"
                                                    value="{{ $item->id }}"
                                                >


                                                <input
                                                    type="number"
                                                    name="items[{{ $loop->index }}][quantity]"
                                                    class="form-control fulfillment-quantity"
                                                    value="{{
                                                        number_format(
                                                            $outstanding,
                                                            3,
                                                            '.',
                                                            ''
                                                        )
                                                    }}"
                                                    min="0.001"
                                                    max="{{ $outstanding }}"
                                                    step="0.001"
                                                    data-outstanding="{{ $outstanding }}"
                                                    required
                                                >

                                            </td>

                                        </tr>

                                    @endif

                                @endforeach

                            </tbody>

                        </table>

                    </div>


                    {{-- NOTES --}}

                    <div class="mt-4">

                        <label
                            for="fulfillmentNotes"
                            class="form-label fw-semibold"
                        >
                            Fulfillment Notes
                        </label>


                        <textarea
                            name="notes"
                            id="fulfillmentNotes"
                            class="form-control"
                            rows="3"
                            maxlength="2000"
                            placeholder="Optional notes about this physical issue or transfer..."
                        ></textarea>

                    </div>

                </div>


                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn stores-btn-light"
                        data-dismiss="modal"
                    >
                        Cancel
                    </button>


                    <button
                        type="submit"
                        class="stores-btn-primary"
                        id="fulfillmentSubmitButton"
                    >

                        <i class="fas fa-check me-1"></i>

                        Process Fulfillment

                    </button>

                </div>

            </form>

        </div>

    </div>

</div>

@endif


{{-- =========================================================
     JAVASCRIPT
     ========================================================= --}}

@push('scripts')

<script>

document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | FULFILLMENT FORM
    |--------------------------------------------------------------------------
    */

    const fulfillmentForm =
        document.getElementById(
            'fulfillmentForm'
        );


    if (fulfillmentForm) {

        fulfillmentForm.addEventListener(
            'submit',
            function (event) {

                const quantities =
                    fulfillmentForm.querySelectorAll(
                        '.fulfillment-quantity'
                    );


                let hasQuantity = false;


                quantities.forEach(function (input) {

                    const value =
                        parseFloat(
                            input.value || 0
                        );


                    const max =
                        parseFloat(
                            input.dataset.outstanding || 0
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | VALIDATE CLIENT SIDE
                    |--------------------------------------------------------------------------
                    */

                    if (
                        isNaN(value)
                        || value <= 0
                    ) {

                        input.disabled = true;

                        const row =
                            input.closest('tr');

                        const hidden =
                            row
                                ? row.querySelector(
                                    'input[name*="[requisition_item_id]"]'
                                )
                                : null;

                        if (hidden) {
                            hidden.disabled = true;
                        }

                        return;
                    }


                    if (
                        value > max
                    ) {

                        event.preventDefault();

                        input.disabled = false;

                        alert(
                            'The issue quantity cannot exceed the outstanding approved quantity.'
                        );

                        input.focus();

                        return false;
                    }


                    hasQuantity = true;

                });


                /*
                |--------------------------------------------------------------------------
                | REQUIRE AT LEAST ONE ITEM
                |--------------------------------------------------------------------------
                */

                if (!hasQuantity) {

                    event.preventDefault();


                    quantities.forEach(
                        function (input) {

                            input.disabled = false;

                        }
                    );


                    alert(
                        'Enter at least one quantity to process.'
                    );


                    return false;
                }


                /*
                |--------------------------------------------------------------------------
                | CONFIRM
                |--------------------------------------------------------------------------
                */

                if (
                    !confirm(
                        'Process this physical stock transaction now?'
                    )
                ) {

                    event.preventDefault();


                    quantities.forEach(
                        function (input) {

                            input.disabled = false;

                        }
                    );


                    return false;
                }


                /*
                |--------------------------------------------------------------------------
                | PREVENT DOUBLE SUBMISSION
                |--------------------------------------------------------------------------
                */

                const submitButton =
                    document.getElementById(
                        'fulfillmentSubmitButton'
                    );


                if (submitButton) {

                    submitButton.disabled = true;

                    submitButton.innerHTML =
                        '<i class="fas fa-spinner fa-spin me-1"></i> Processing...';

                }

            }
        );

    }

});

</script>

@endpush


@endsection