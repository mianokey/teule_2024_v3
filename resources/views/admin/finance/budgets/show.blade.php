@extends('layouts.admin')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/stores.css?v=2.0.0') }}">
@endpush

@section('content')

@php

    /*
    |--------------------------------------------------------------------------
    | STATUS
    |--------------------------------------------------------------------------
    */

    $status = strtolower(trim($budget->status ?? 'draft'));

    $approvalStage = strtolower(
        trim($budget->approval_stage ?? 'none')
    );

    $isDraft = $status === 'draft';

    $isReturned = $status === 'returned';

    $isPending = $status === 'submitted';

    $isApproved = $status === 'approved';

    $isRejected = $status === 'rejected';

    $isRequester =
        (int) $budget->created_by === (int) auth()->id();

    $isPeriodClosed =
        optional($budget->period)->status === 'closed';

    $isEditable =
        ($isDraft || $isReturned) &&
        $isRequester &&
        !$isPeriodClosed;


    /*
    |--------------------------------------------------------------------------
    | APPROVAL PERMISSIONS
    |--------------------------------------------------------------------------
    |
    | Finance:
    | APPROVE MONTHLY BUDGETS - FINANCE
    |
    | Deputy Centre Manager:
    | APPROVE MONTHLY BUDGETS - DCM
    |
    | Centre Manager:
    | APPROVE MONTHLY BUDGETS - CM
    |
    | There is NO general approval permission.
    |--------------------------------------------------------------------------
    */

    $user = auth()->user();

    $canApproveFinance = $user->can(
        'APPROVE MONTHLY BUDGETS - FINANCE'
    );

    $canApproveDcm = $user->can(
        'APPROVE MONTHLY BUDGETS - DCM'
    );

    $canApproveCm = $user->can(
        'APPROVE MONTHLY BUDGETS - CM'
    );


    /*
    |--------------------------------------------------------------------------
    | DETERMINE WHETHER USER CAN APPROVE THE CURRENT STAGE
    |--------------------------------------------------------------------------
    */

    $canApproveCurrentStage = false;

    if ($isPending) {

        if ($approvalStage === 'finance') {

            $canApproveCurrentStage = $canApproveFinance;

        } elseif ($approvalStage === 'dcm') {

            $canApproveCurrentStage = $canApproveDcm;

        } elseif ($approvalStage === 'cm') {

            $canApproveCurrentStage = $canApproveCm;
        }
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW APPROVAL ACTIONS
    |--------------------------------------------------------------------------
    */

$showApprovalActions = $isPending;


    /*
    |--------------------------------------------------------------------------
    | APPROVAL STAGE LABELS
    |--------------------------------------------------------------------------
    */

    $stageLabels = [

        'finance'  => 'Finance Approval',

        'dcm'      => 'Deputy Centre Manager Approval',

        'cm'       => 'Centre Manager Approval',

        'approved' => 'Fully Approved',

        'returned' => 'Returned to Requester',

        'rejected' => 'Rejected',
    ];

    $stageLabel =
        $stageLabels[$approvalStage] ?? 'Not Submitted';


    /*
    |--------------------------------------------------------------------------
    | STATUS DISPLAY
    |--------------------------------------------------------------------------
    */

    $statusLabel = match ($status) {

        'draft' =>
            'Draft',

        'submitted' =>
            'Pending ' . $stageLabel,

        'returned' =>
            'Returned for Adjustment',

        'approved' =>
            'Fully Approved',

        'rejected' =>
            'Rejected',

        default =>
            ucfirst(str_replace('_', ' ', $status)),
    };


    $statusClass = match ($status) {

        'draft' =>
            'requisition-status-draft',

        'submitted' =>
            'requisition-status-pending',

        'returned' =>
            'requisition-status-returned',

        'approved' =>
            'requisition-status-approved',

        'rejected' =>
            'requisition-status-rejected',

        default =>
            'requisition-status-draft',
    };


    /*
    |--------------------------------------------------------------------------
    | FINANCIAL SUMMARY
    |--------------------------------------------------------------------------
    */

    $requestedAmount =
        (float) ($budget->requested_amount ?? 0);

    $carriedForwardAmount =
        (float) ($budget->carried_forward_amount ?? 0);

    $approvedAmount =
        (float) ($budget->approved_amount ?? 0);

    $totalRequested =
        $requestedAmount + $carriedForwardAmount;


    /*
    |--------------------------------------------------------------------------
    | APPROVAL HISTORY
    |--------------------------------------------------------------------------
    */

    $approvals = $budget->approvals
        ->sortBy('id')
        ->values();

@endphp


<div class="container-fluid requisition-page">

    

    {{-- =====================================================
         HEADER
         ====================================================== --}}

    @include(
        'admin.finance.budgets.partials.header'
    )


    {{-- =====================================================
         WORKFLOW
         ====================================================== --}}

    @include(
        'admin.finance.budgets.partials.workflow'
    )


    {{-- =====================================================
         STATE / NOTICES
         ====================================================== --}}

    @include(
        'admin.finance.budgets.partials.state'
    )


    {{-- =====================================================
         APPROVAL ACTIONS
         ====================================================== --}}

    @include(
        'admin.finance.budgets.partials.approval'
    )


    {{-- =====================================================
         FINANCIAL SUMMARY
         ====================================================== --}}

    @include(
        'admin.finance.budgets.partials.financial-summary'
    )


    {{-- =====================================================
         BUDGET DETAILS
         ====================================================== --}}

    @include(
        'admin.finance.budgets.partials.budget-details'
    )


    {{-- =====================================================
         APPROVAL HISTORY
         ====================================================== --}}

    @include(
        'admin.finance.budgets.partials.history'
    )


    {{-- =====================================================
         BOTTOM ACTIONS
         ====================================================== --}}

    @include(
        'admin.finance.budgets.partials.bottom-actions'
    )

</div>


{{-- =========================================================
     MODALS
     ========================================================= --}}

@include(
    'admin.finance.budgets.partials.modals'
)

@endsection