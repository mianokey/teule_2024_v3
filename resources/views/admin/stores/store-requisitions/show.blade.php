@extends('layouts.admin')

@section('content')

@php

    /* ==========================================================================
       STATUS
       ========================================================================== */

    $status = strtolower(
        trim($storeRequisition->status ?? 'draft')
    );

    $approvalStage = strtolower(
        trim($storeRequisition->approval_stage ?? 'none')
    );

    $isDraft = $status === 'draft';

    $isPending = in_array(
        $status,
        [
            'pending',
            'submitted',
        ],
        true
    );

    $isApproved =
        $status === 'approved' &&
        $approvalStage === 'approved';

    $isRejected = $status === 'rejected';

    $isReturned = $status === 'returned';

    $isClosed = in_array(
        $status,
        [
            'closed',
            'fulfilled',
            'completed',
        ],
        true
    );

    $isCancelled = $status === 'cancelled';

    $isRequester =
        (int) $storeRequisition->requested_by ===
        (int) auth()->id();

    $isEditable =
        ($isDraft || $isReturned) &&
        $isRequester;


    /* ==========================================================================
       PERMISSIONS
       ========================================================================== */

    $user = auth()->user();

    /*
    |--------------------------------------------------------------------------
    | STRICT STAGE-SPECIFIC APPROVAL PERMISSIONS
    |--------------------------------------------------------------------------
    |
    | HOD:
    | APPROVE STORE REQUISITIONS - HOD
    |
    | Management:
    | APPROVE STORE REQUISITIONS - MANAGEMENT
    |
    | Stores:
    | APPROVE STORE REQUISITIONS - STORES
    |
    | There is NO general approval permission.
    |--------------------------------------------------------------------------
    */

    $canApproveHod = $user->can(
        'APPROVE STORE REQUISITIONS - HOD'
    );

    $canApproveManagement = $user->can(
        'APPROVE STORE REQUISITIONS - MANAGEMENT'
    );

    $canApproveStores = $user->can(
        'APPROVE STORE REQUISITIONS - STORES'
    );


    /*
    |--------------------------------------------------------------------------
    | DETERMINE WHETHER USER CAN APPROVE THE CURRENT STAGE
    |--------------------------------------------------------------------------
    */

    $canApproveCurrentStage = false;

    if ($isPending) {

        if ($approvalStage === 'hod') {

            $canApproveCurrentStage =
                $canApproveHod;

        } elseif ($approvalStage === 'management') {

            $canApproveCurrentStage =
                $canApproveManagement;

        } elseif ($approvalStage === 'stores') {

            $canApproveCurrentStage =
                $canApproveStores;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW APPROVAL ACTIONS
    |--------------------------------------------------------------------------
    |
    | The approval panel can only appear when:
    |
    | 1. The requisition is pending
    | 2. The user has permission for the CURRENT stage
    |
    */

    $showApprovalActions =
        $isPending &&
        $canApproveCurrentStage;


    /* ==========================================================================
       STATUS DISPLAY
       ========================================================================== */

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

        'cancelled' =>
            'requisition-status-rejected',

        'closed',
        'fulfilled',
        'completed' =>
            'requisition-status-closed',

        default =>
            'requisition-status-draft',
    };


    $statusLabel = match ($status) {

        'submitted' =>
            'Pending Approval',

        'cancelled' =>
            'Cancelled',

        default =>
            ucfirst(
                str_replace(
                    '_',
                    ' ',
                    $status
                )
            ),
    };


    /* ==========================================================================
       CURRENT APPROVAL STAGE LABEL
       ========================================================================== */

    $stageLabel = match ($approvalStage) {

        'hod' =>
            'HOD Approval',

        'management' =>
            'Management Approval',

        'stores' =>
            'Stores Approval',

        'approved' =>
            'Fully Approved',

        'rejected' =>
            'Rejected',

        'returned' =>
            'Returned to Requester',

        default =>
            'Not Submitted',
    };


    /* ==========================================================================
       TOTALS
       ========================================================================== */

    $totalRequested = 0;
    $totalApproved = 0;
    $totalIssued = 0;
    $totalOutstanding = 0;

    foreach ($storeRequisition->items as $item) {

        $requested =
            (float) $item->requested_quantity;

        $approved =
            (float) $item->approved_quantity;

        $issued =
            (float) $item->issued_quantity;

        $totalRequested += $requested;

        if ($isApproved || $isClosed) {

            $totalApproved +=
                $approved;

            $totalIssued +=
                $issued;

            $totalOutstanding +=
                max(
                    0,
                    $approved - $issued
                );
        }
    }


    /* ==========================================================================
       FULFILLMENT
       ========================================================================== */

    $fulfillmentStatus = strtolower(
        trim(
            $storeRequisition->fulfillment_status ?? ''
        )
    );

    $fulfillmentLabel = match ($fulfillmentStatus) {

        'fully_issued' =>
            'Fully Fulfilled',

        'partially_issued' =>
            'Partially Fulfilled',

        'closed' =>
            'Closed',

        default =>
            'Not Issued',
    };


    $fulfillmentBadge = match ($fulfillmentStatus) {

        'fully_issued' =>
            'stores-badge-success',

        'partially_issued' =>
            'stores-badge-warning',

        'closed' =>
            'stores-badge-closed',

        default =>
            'stores-badge-muted',
    };


    /* ==========================================================================
       OUTSTANDING
       ========================================================================== */

    $hasOutstanding = false;

    foreach ($storeRequisition->items as $item) {

        if (!$isApproved) {
            continue;
        }

        $approved =
            (float) $item->approved_quantity;

        $issued =
            (float) $item->issued_quantity;

        if (
            max(
                0,
                $approved - $issued
            ) > 0
        ) {
            $hasOutstanding = true;
            break;
        }
    }


    /* ==========================================================================
       LATEST FULFILLMENT
       ========================================================================== */

    $latestFulfillment = null;

    if (
        $storeRequisition->fulfillments &&
        $storeRequisition->fulfillments->count()
    ) {

        $latestFulfillment =
            $storeRequisition
                ->fulfillments
                ->sortByDesc(function ($fulfillment) {

                    return $fulfillment->created_at
                        ? $fulfillment->created_at->timestamp
                        : $fulfillment->id;

                })
                ->first();
    }


    /* ==========================================================================
       SOURCE STORE STOCK
       ========================================================================== */

    $sourceStockByKey = collect();

    if (
        $isApproved &&
        $storeRequisition->source_store_id
    ) {

        $storeItemIds =
            $storeRequisition
                ->items
                ->pluck('store_item_id')
                ->filter()
                ->unique()
                ->values();

        if ($storeItemIds->count()) {

            $sourceStockByKey =
                \App\Models\StoreStock::query()

                    ->where(
                        'store_id',
                        $storeRequisition->source_store_id
                    )

                    ->whereIn(
                        'store_item_id',
                        $storeItemIds
                    )

                    ->get()

                    ->keyBy(function ($stock) {

                        return
                            $stock->store_item_id .
                            '|' .
                            (
                                $stock->variant_id
                                ?? 'null'
                            );
                    });
        }
    }

@endphp


<style>

/* ==========================================================================
   STORE REQUISITION - MINIMAL DETAIL PAGE
   ========================================================================== */

.requisition-page {
    max-width: 1450px;
    margin: 0 auto;
}

.requisition-page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 14px;
}

.requisition-title-row {
    display: flex;
    align-items: center;
    gap: 10px;
}

.requisition-page-title {
    margin: 0;
    font-size: 22px;
    line-height: 1.2;
    font-weight: 700;
    color: #00096A;
}

.requisition-number {
    margin-top: 4px;
    color: #6c757d;
    font-size: 12px;
}

.requisition-page-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 7px;
    flex-wrap: wrap;
}

.requisition-status {
    display: inline-flex;
    align-items: center;
    padding: 6px 11px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .25px;
    white-space: nowrap;
}

.requisition-status-draft {
    background: #f1f3f5;
    color: #495057;
}

.requisition-status-pending {
    background: #fff3cd;
    color: #856404;
}

.requisition-status-approved {
    background: #d1e7dd;
    color: #0f5132;
}

.requisition-status-returned {
    background: #cff4fc;
    color: #055160;
}

.requisition-status-rejected {
    background: #f8d7da;
    color: #842029;
}

.requisition-status-closed {
    background: #d1e7dd;
    color: #0f5132;
}


/* ==========================================================================
   WORKFLOW
   ========================================================================== */

.requisition-workflow {
    background: #fff;
    border: 1px solid #e4e7ec;
    border-radius: 9px;
    padding: 12px 16px;
    margin-bottom: 14px;
    box-shadow: 0 1px 5px rgba(0,0,0,.03);
}

.requisition-workflow-track {
    display: flex;
    align-items: center;
    width: 100%;
}

.workflow-step {
    display: flex;
    align-items: center;
    flex: 1;
    min-width: 0;
}

.workflow-step:last-child {
    flex: 0 0 auto;
}

.workflow-node {
    width: 27px;
    height: 27px;
    min-width: 27px;
    border-radius: 50%;
    border: 2px solid #dfe3e8;
    background: #fff;
    color: #8b949e;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: 700;
}

.workflow-step.active .workflow-node {
    background: #00096A;
    border-color: #00096A;
    color: #fff;
}

.workflow-step.completed .workflow-node {
    background: #198754;
    border-color: #198754;
    color: #fff;
}

.workflow-label {
    margin-left: 7px;
    font-size: 10px;
    font-weight: 700;
    color: #6c757d;
    white-space: nowrap;
}

.workflow-step.active .workflow-label,
.workflow-step.completed .workflow-label {
    color: #212529;
}

.workflow-line {
    height: 2px;
    background: #e9ecef;
    flex: 1;
    margin: 0 8px;
}

.workflow-line.completed {
    background: #198754;
}


/* ==========================================================================
   ACTION / STATE PANEL
   ========================================================================== */

.requisition-state-panel {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 15px;
    padding: 11px 14px;
    margin-bottom: 14px;
    border-radius: 8px;
    border: 1px solid #e4e7ec;
    background: #fff;
}

.requisition-state-main {
    display: flex;
    align-items: center;
    gap: 10px;
    min-width: 0;
}

.requisition-state-icon {
    width: 34px;
    height: 34px;
    min-width: 34px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f1f3f5;
    color: #00096A;
}

.requisition-state-text strong {
    display: block;
    font-size: 13px;
    color: #212529;
}

.requisition-state-text span {
    display: block;
    margin-top: 2px;
    color: #6c757d;
    font-size: 11px;
}

.requisition-state-actions {
    display: flex;
    gap: 7px;
    flex-wrap: wrap;
}


/* ==========================================================================
   ITEMS CARD
   ========================================================================== */

.requisition-card {
    border: 1px solid #e4e7ec;
    border-radius: 9px;
    background: #fff;
    overflow: hidden;
    box-shadow: 0 1px 5px rgba(0,0,0,.03);
    margin-bottom: 14px;
}

.requisition-card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    padding: 10px 14px;
    background: #f8f9fb;
    border-bottom: 1px solid #e4e7ec;
}

.requisition-card-title {
    color: #00096A;
    font-size: 13px;
    font-weight: 700;
}

.requisition-card-subtitle {
    color: #6c757d;
    font-size: 11px;
}

.requisition-table {
    width: 100%;
    margin: 0;
}

.requisition-table th {
    padding: 8px 10px;
    background: #fff;
    color: #6c757d;
    border-bottom: 1px solid #e4e7ec;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .25px;
    white-space: nowrap;
}

.requisition-table td {
    padding: 9px 10px;
    vertical-align: middle;
    border-color: #edf0f3;
    font-size: 12px;
}

.requisition-table tbody tr:last-child td {
    border-bottom: 0;
}

.item-name {
    display: block;
    font-weight: 700;
    color: #212529;
}

.item-variant {
    display: block;
    margin-top: 2px;
    color: #6c757d;
    font-size: 10px;
}

.quantity-value {
    font-weight: 700;
    font-variant-numeric: tabular-nums;
}

.quantity-balance {
    color: #856404;
}

.quantity-zero {
    color: #198754;
}

.stock-available {
    color: #198754;
    font-weight: 700;
}

.stock-insufficient {
    color: #dc3545;
    font-weight: 700;
}


/* ==========================================================================
   SUMMARY STRIP
   ========================================================================== */

.requisition-summary-strip {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    border-top: 1px solid #e4e7ec;
    background: #f8f9fb;
}

.requisition-summary-item {
    padding: 9px 12px;
    border-right: 1px solid #e4e7ec;
}

.requisition-summary-item:last-child {
    border-right: 0;
}

.requisition-summary-label {
    display: block;
    color: #6c757d;
    font-size: 9px;
    text-transform: uppercase;
    font-weight: 700;
    letter-spacing: .3px;
}

.requisition-summary-value {
    display: block;
    margin-top: 2px;
    color: #212529;
    font-size: 14px;
    font-weight: 700;
}


/* ==========================================================================
   HISTORY
   ========================================================================== */

.requisition-history-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
}

.requisition-history-card {
    border: 1px solid #e4e7ec;
    border-radius: 9px;
    background: #fff;
    padding: 12px 14px;
}

.requisition-history-card-title {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    color: #00096A;
    font-size: 12px;
    font-weight: 700;
}

.requisition-history-card-text {
    margin-top: 5px;
    color: #6c757d;
    font-size: 11px;
}


/* ==========================================================================
   NOTICES
   ========================================================================== */

.requisition-notice {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 10px 12px;
    margin-bottom: 14px;
    border-radius: 8px;
    font-size: 12px;
}

.requisition-notice i {
    margin-top: 2px;
}

.requisition-notice-success {
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    color: #166534;
}

.requisition-notice-warning {
    background: #fffaf0;
    border: 1px solid #f6e7c1;
    color: #7c5a00;
}

.requisition-notice-info {
    background: #ecfeff;
    border: 1px solid #a5f3fc;
    color: #155e75;
}

.requisition-notice-danger {
    background: #fff5f5;
    border: 1px solid #f5c2c7;
    color: #842029;
}


/* ==========================================================================
   DETAILS MODAL
   ========================================================================== */

.requisition-details-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 10px;
}

.requisition-detail-item {
    padding: 9px 10px;
    background: #f8f9fb;
    border: 1px solid #edf0f3;
    border-radius: 7px;
}

.requisition-detail-label {
    display: block;
    margin-bottom: 2px;
    color: #6c757d;
    font-size: 9px;
    font-weight: 700;
    text-transform: uppercase;
}

.requisition-detail-value {
    color: #212529;
    font-size: 12px;
    font-weight: 600;
    word-break: break-word;
}


/* ==========================================================================
   APPROVAL PANEL
   ========================================================================== */

.requisition-approval-panel {
    border: 1px solid #dbe4ff;
    background: #f8faff;
    border-radius: 8px;
    padding: 11px 13px;
    margin-bottom: 14px;
}

.requisition-approval-panel-title {
    color: #00096A;
    font-size: 12px;
    font-weight: 700;
    margin-bottom: 8px;
}

.requisition-approval-actions {
    display: flex;
    gap: 7px;
    flex-wrap: wrap;
}


/* ==========================================================================
   TIMELINE
   ========================================================================== */

.timeline {
    position: relative;
    padding-left: 22px;
}

.timeline::before {
    content: "";
    position: absolute;
    top: 5px;
    bottom: 5px;
    left: 6px;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    padding-bottom: 16px;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-dot {
    position: absolute;
    left: -22px;
    top: 2px;
    width: 13px;
    height: 13px;
    border-radius: 50%;
    background: #00096A;
    border: 2px solid #fff;
    box-shadow: 0 0 0 1px #dbe4ff;
}

.timeline-title {
    font-weight: 700;
    font-size: 12px;
}

.timeline-meta {
    color: #6c757d;
    font-size: 10px;
    margin-top: 2px;
}

.timeline-comment {
    margin-top: 5px;
    padding: 7px 9px;
    background: #f8f9fb;
    border-radius: 6px;
    color: #495057;
    font-size: 11px;
}


/* ==========================================================================
   FULFILLMENT
   ========================================================================== */

.fulfillment-rules {
    background: #f8f9fb;
    border: 1px solid #e4e7ec;
    border-radius: 8px;
    padding: 10px 12px;
    color: #495057;
    font-size: 11px;
}

.fulfillment-input {
    min-width: 125px;
}

.fulfillment-input input {
    text-align: right;
}

.fulfillment-stock-label {
    margin-top: 3px;
    font-size: 9px;
}

.fulfillment-unavailable-row {
    background: #fffaf0;
}


/* ==========================================================================
   BOTTOM
   ========================================================================== */

.requisition-bottom-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    margin-top: 14px;
}

.requisition-bottom-actions-left,
.requisition-bottom-actions-right {
    display: flex;
    gap: 7px;
    flex-wrap: wrap;
}


/* ==========================================================================
   RESPONSIVE
   ========================================================================== */

@media (max-width: 992px) {

    .requisition-details-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .requisition-summary-strip {
        grid-template-columns: repeat(2, 1fr);
    }

    .requisition-summary-item:nth-child(2) {
        border-right: 0;
    }

    .requisition-summary-item {
        border-bottom: 1px solid #e4e7ec;
    }

    .requisition-history-grid {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {

    .requisition-page-header {
        align-items: flex-start;
        flex-direction: column;
    }

    .requisition-page-actions {
        justify-content: flex-start;
    }

    .requisition-workflow {
        overflow-x: auto;
    }

    .requisition-workflow-track {
        min-width: 650px;
    }

    .requisition-state-panel {
        align-items: flex-start;
        flex-direction: column;
    }

    .requisition-details-grid {
        grid-template-columns: 1fr;
    }

    .requisition-summary-strip {
        grid-template-columns: 1fr 1fr;
    }

    .requisition-table {
        min-width: 760px;
    }

    .table-responsive {
        overflow-x: auto;
    }
}

</style>


<div class="container-fluid requisition-page">

    {{-- =====================================================
         HEADER
         ====================================================== --}}

    @include(
        'admin.stores.store-requisitions.partials.header'
    )


    {{-- =====================================================
         WORKFLOW
         ====================================================== --}}

    @include(
        'admin.stores.store-requisitions.partials.workflow'
    )


    {{-- =====================================================
         STATE / ACTIONS / NOTICES
         ====================================================== --}}

    @include(
        'admin.stores.store-requisitions.partials.state'
    )


    {{-- =====================================================
         APPROVAL ACTIONS
         ====================================================== --}}

    @include(
        'admin.stores.store-requisitions.partials.approval'
    )


    {{-- =====================================================
         ITEMS
         ====================================================== --}}

    @include(
        'admin.stores.store-requisitions.partials.items'
    )


    {{-- =====================================================
         HISTORY
         ====================================================== --}}

    @include(
        'admin.stores.store-requisitions.partials.history'
    )


    {{-- =====================================================
         BOTTOM ACTIONS
         ====================================================== --}}

    @include(
        'admin.stores.store-requisitions.partials.bottom-actions'
    )

</div>


{{-- =========================================================
     MODALS
     ========================================================= --}}

@include(
    'admin.stores.store-requisitions.partials.modals'
)

@endsection