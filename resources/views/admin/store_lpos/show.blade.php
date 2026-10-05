@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('assets/css/stores.css') }}">
@endpush

@section('content')

<div class="store-requisition-page">

    {{-- ============================================================
         PAGE HEADER
         ============================================================ --}}

    <div class="requisition-page-header">

        <div class="requisition-header-content">

            <div class="requisition-header-icon">
                <i class="fas fa-file-invoice"></i>
            </div>

            <div>

                <div class="requisition-breadcrumb">

                    <span>Stores</span>

                    <i class="fas fa-chevron-right"></i>

                    <span>LPOs</span>

                    <i class="fas fa-chevron-right"></i>

                    <span>View LPO</span>

                </div>

                <h1 class="requisition-page-title">

                    {{ $storeLpo->lpo_number }}

                </h1>

                <p class="requisition-page-subtitle">

                    View LPO details, approval stage and approval actions.

                </p>

            </div>

        </div>


        <div class="requisition-header-right">

            <a
                href="{{ route('admin.store-lpos.index') }}"
                class="requisition-cancel-button"
            >

                <i class="fas fa-arrow-left me-1"></i>

                Back

            </a>


            @if(in_array($storeLpo->status, ['DRAFT', 'RETURNED']))

                <a
                    href="{{ route(
                        'admin.store-lpos.edit',
                        $storeLpo
                    ) }}"
                    class="requisition-add-button"
                >

                    <i class="fas fa-edit me-1"></i>

                    Edit

                </a>

            @endif


            {{-- PDF --}}

            <a
                href="{{ route(
                    'admin.store-lpos.pdf',
                    $storeLpo
                ) }}"
                target="_blank"
                class="requisition-add-button"
            >

                <i class="fas fa-file-pdf me-1"></i>

                PDF

            </a>

        </div>

    </div>


    <x-message></x-message>


    {{-- ============================================================
         STATUS / APPROVAL STAGE
         ============================================================ --}}

    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">

                    <i class="fas fa-tasks"></i>

                </div>

                <div>

                    <h5>
                        Approval Status
                    </h5>

                    <p>
                        Current LPO status and approval stage.
                    </p>

                </div>

            </div>

        </div>


        <div class="requisition-section-body">

            <div class="row">

                {{-- STATUS --}}

                <div class="col-md-4">

                    <div class="requisition-detail-item">

                        <label class="requisition-detail-label">

                            LPO Status

                        </label>

                        <div>

                            <strong>

                                {{ strtoupper(
                                    $storeLpo->status
                                ) }}

                            </strong>

                        </div>

                    </div>

                </div>


                {{-- APPROVAL STAGE --}}

                <div class="col-md-4">

                    <div class="requisition-detail-item">

                        <label class="requisition-detail-label">

                            Approval Stage

                        </label>

                        <div>

                            <strong>

                                @if($storeLpo->approval_stage)

                                    @php

                                        $stageLabel = match (
                                            strtolower(
                                                (string)
                                                $storeLpo->approval_stage
                                            )
                                        ) {

                                            'hod',
                                            'pending_hod'
                                                => 'HOD',

                                            'management',
                                            'pending_management'
                                                => 'MANAGEMENT',

                                            default
                                                => strtoupper(
                                                    (string)
                                                    $storeLpo->approval_stage
                                                ),

                                        };

                                    @endphp

                                    {{ $stageLabel }}

                                @else

                                    Not Set

                                @endif

                            </strong>

                        </div>

                    </div>

                </div>


                {{-- INITIATOR --}}

                <div class="col-md-4">

                    <div class="requisition-detail-item">

                        <label class="requisition-detail-label">

                            Initiated By

                        </label>

                        <div>

                            <strong>

                                {{ $storeLpo->creator?->name ?? 'N/A' }}

                            </strong>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>



    {{-- ============================================================
         APPROVAL ACTIONS
         ============================================================ --}}

    @php

        $currentStage = strtolower(
            trim(
                (string) $storeLpo->approval_stage
            )
        );

        $currentStatus = strtoupper(
            trim(
                (string) $storeLpo->status
            )
        );


        /*
        |--------------------------------------------------------------------------
        | CURRENT APPROVAL STAGE
        |--------------------------------------------------------------------------
        */

        $isHodStage = in_array(
            $currentStage,
            [
                'hod',
                'pending_hod'
            ],
            true
        );


        $isManagementStage = in_array(
            $currentStage,
            [
                'management',
                'pending_management'
            ],
            true
        );


        /*
        |--------------------------------------------------------------------------
        | PENDING STATUS
        |--------------------------------------------------------------------------
        */

        $isPending = in_array(
            $currentStatus,
            [
                'PENDING',
                'PENDING_HOD',
                'PENDING_MANAGEMENT'
            ],
            true
        );


        /*
        |--------------------------------------------------------------------------
        | APPROVAL AUTHORITY
        |--------------------------------------------------------------------------
        |
        | IMPORTANT:
        |
        | The person who created the LPO may also be the HOD
        | or CM/DCM.
        |
        | Therefore we deliberately DO NOT check created_by here.
        |
        | Approval is determined by:
        |
        | 1. Current approval stage
        | 2. User permission
        |
        |--------------------------------------------------------------------------
        */

        $canApproveHod =
            $isPending
            &&
            $isHodStage
            &&
            (
                auth()->user()->can(
                    'APPROVE STORE LPO - HOD'
                )
                ||
                auth()->user()->can(
                    'APPROVE STORE LPO'
                )
            );


        $canApproveManagement =
            $isPending
            &&
            $isManagementStage
            &&
            (
                auth()->user()->can(
                    'APPROVE STORE LPO - MANAGEMENT'
                )
                ||
                auth()->user()->can(
                    'APPROVE STORE LPO'
                )
            );


        $canTakeAction =
            $canApproveHod
            ||
            $canApproveManagement;


        /*
        |--------------------------------------------------------------------------
        | DISPLAY STAGE
        |--------------------------------------------------------------------------
        */

        $approvalStageLabel = match ($currentStage) {

            'hod',
            'pending_hod'
                => 'HOD',

            'management',
            'pending_management'
                => 'MANAGEMENT',

            default
                => strtoupper(
                    (string)
                    $storeLpo->approval_stage
                ),

        };

    @endphp


    @if($isPending)

        <div class="requisition-section">

            <div class="requisition-section-header">

                <div class="requisition-section-heading">

                    <div class="requisition-section-icon">

                        <i class="fas fa-user-check"></i>

                    </div>

                    <div>

                        <h5>
                            Approval Actions
                        </h5>

                        <p>

                            @if($isHodStage)

                                This LPO is awaiting HOD approval.

                            @elseif($isManagementStage)

                                This LPO is awaiting Management approval.

                            @else

                                This LPO is awaiting approval.

                            @endif

                        </p>

                    </div>

                </div>

            </div>


            <div class="requisition-section-body">

                @if($canTakeAction)

                    <div class="requisition-notice requisition-notice-info mb-3">

                        <strong>

                            {{ $approvalStageLabel }}
                            Approval Required

                        </strong>

                        <div>

                            Review the LPO details and choose an action below.

                        </div>

                    </div>


                    <div class="requisition-bottom-actions">

                        <div class="requisition-bottom-actions-left">

                            <span>

                                Current Stage:

                                <strong>

                                    {{ $approvalStageLabel }}

                                </strong>

                            </span>

                        </div>


                        <div class="requisition-bottom-actions-right">


                            {{-- =================================================
                                 RETURN
                                 ================================================= --}}

                            <form
                                method="POST"
                                action="{{ route(
                                    'admin.store-lpos.approval',
                                    $storeLpo
                                ) }}"
                                style="display:inline-block;"
                            >

                                @csrf

                                <input
                                    type="hidden"
                                    name="action"
                                    value="return"
                                >

                                <button
                                    type="submit"
                                    class="requisition-cancel-button"
                                    onclick="return confirm(
                                        'Return this LPO to the initiator for correction?'
                                    );"
                                >

                                    <i class="fas fa-undo me-1"></i>

                                    Return

                                </button>

                            </form>


                            {{-- =================================================
                                 REJECT
                                 ================================================= --}}

                            <form
                                method="POST"
                                action="{{ route(
                                    'admin.store-lpos.approval',
                                    $storeLpo
                                ) }}"
                                style="display:inline-block;"
                            >

                                @csrf

                                <input
                                    type="hidden"
                                    name="action"
                                    value="reject"
                                >

                                <button
                                    type="submit"
                                    class="requisition-cancel-button"
                                    onclick="return confirm(
                                        'Reject this LPO? This action should only be used when the LPO should not proceed.'
                                    );"
                                >

                                    <i class="fas fa-times me-1"></i>

                                    Reject

                                </button>

                            </form>


                            {{-- =================================================
                                 APPROVE
                                 ================================================= --}}

                            <form
                                method="POST"
                                action="{{ route(
                                    'admin.store-lpos.approval',
                                    $storeLpo
                                ) }}"
                                style="display:inline-block;"
                            >

                                @csrf

                                <input
                                    type="hidden"
                                    name="action"
                                    value="approve"
                                >

                                <button
                                    type="submit"
                                    class="requisition-add-button"
                                    onclick="return confirm(
                                        'Approve this LPO?'
                                    );"
                                >

                                    <i class="fas fa-check me-1"></i>

                                    Approve

                                </button>

                            </form>

                        </div>

                    </div>


                @else

                    {{-- =================================================
                         USER DOES NOT HAVE APPROVAL PERMISSION
                         ================================================= --}}

                    <div class="requisition-notice requisition-notice-info">

                        <strong>

                            Awaiting
                            {{ $approvalStageLabel }}
                            Approval

                        </strong>

                        <div>

                            This LPO is currently awaiting an authorized
                            {{ $approvalStageLabel }} approver.

                        </div>

                    </div>


                    <div class="requisition-bottom-actions">

                        <div class="requisition-bottom-actions-left">

                            <span>

                                Current Stage:

                                <strong>

                                    {{ $approvalStageLabel }}

                                </strong>

                            </span>

                        </div>

                    </div>

                @endif

            </div>

        </div>

    @endif



    {{-- ============================================================
         LPO DETAILS
         ============================================================ --}}

    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">

                    <i class="fas fa-building"></i>

                </div>

                <div>

                    <h5>
                        LPO Details
                    </h5>

                    <p>
                        Supplier, store and purchase order information.
                    </p>

                </div>

            </div>

        </div>


        <div class="requisition-section-body">

            <div class="row">


                {{-- SUPPLIER --}}

                <div class="col-md-6">

                    <div class="requisition-detail-item">

                        <label class="requisition-detail-label">

                            Supplier

                        </label>

                        <div>

                            <strong>

                                {{ $storeLpo->supplier?->name ?? 'N/A' }}

                            </strong>

                        </div>

                    </div>

                </div>


                {{-- STORE --}}

                <div class="col-md-6">

                    <div class="requisition-detail-item">

                        <label class="requisition-detail-label">

                            Store

                        </label>

                        <div>

                            <strong>

                                {{ $storeLpo->store?->name ?? 'N/A' }}

                            </strong>

                        </div>

                    </div>

                </div>


                {{-- LPO DATE --}}

                <div class="col-md-6">

                    <div class="requisition-detail-item">

                        <label class="requisition-detail-label">

                            LPO Date

                        </label>

                        <div>

                            <strong>

                                {{
                                    $storeLpo->lpo_date?->format('d M Y')
                                    ?? 'N/A'
                                }}

                            </strong>

                        </div>

                    </div>

                </div>


                {{-- EXPECTED DELIVERY --}}

                <div class="col-md-6">

                    <div class="requisition-detail-item">

                        <label class="requisition-detail-label">

                            Expected Delivery Date

                        </label>

                        <div>

                            <strong>

                                {{
                                    $storeLpo->expected_delivery_date
                                        ?->format('d M Y')
                                    ?? 'N/A'
                                }}

                            </strong>

                        </div>

                    </div>

                </div>


                {{-- SUPPLIER CODE --}}

                @if(
                    isset($storeLpo->supplier)
                    &&
                    !empty($storeLpo->supplier->supplier_code)
                )

                    <div class="col-md-6">

                        <div class="requisition-detail-item">

                            <label class="requisition-detail-label">

                                Supplier Code

                            </label>

                            <div>

                                <strong>

                                    {{ $storeLpo->supplier->supplier_code }}

                                </strong>

                            </div>

                        </div>

                    </div>

                @endif


                {{-- LPO NUMBER --}}

                <div class="col-md-6">

                    <div class="requisition-detail-item">

                        <label class="requisition-detail-label">

                            LPO Number

                        </label>

                        <div>

                            <strong>

                                {{ $storeLpo->lpo_number }}

                            </strong>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>



    {{-- ============================================================
         ITEMS
         ============================================================ --}}

    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">

                    <i class="fas fa-boxes"></i>

                </div>

                <div>

                    <h5>
                        LPO Items
                    </h5>

                    <p>
                        Items included in this purchase order.
                    </p>

                </div>

            </div>


            @if($storeLpo->items->count())

                <span class="requisition-count-badge">

                    {{ $storeLpo->items->count() }}

                    {{
                        $storeLpo->items->count() === 1
                            ? 'Item'
                            : 'Items'
                    }}

                </span>

            @endif

        </div>


        <div class="requisition-section-body">

            @if($storeLpo->items->count())

                <div class="requisition-list-table-wrapper">

                    <table class="requisition-list-table">

                        <thead>

                            <tr>

                                <th>
                                    #
                                </th>

                                <th>
                                    Item
                                </th>

                                <th>
                                    Variant
                                </th>

                                <th>
                                    Description
                                </th>

                                <th>
                                    Quantity
                                </th>

                                <th>
                                    Unit Price
                                </th>

                                <th>
                                    Discount
                                </th>

                                <th>
                                    Tax
                                </th>

                                <th>
                                    Line Total
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach(
                                $storeLpo->items
                                as $index => $item
                            )

                                <tr>

                                    <td>

                                        {{ $index + 1 }}

                                    </td>


                                    <td>

                                        <strong>

                                            {{
                                                $item->item?->name
                                                ?? 'N/A'
                                            }}

                                        </strong>

                                    </td>


                                    <td>

                                        {{
                                            $item->variant?->name
                                            ?? '—'
                                        }}

                                    </td>


                                    <td>

                                        {{
                                            $item->description
                                            ?? '—'
                                        }}

                                    </td>


                                    <td>

                                        {{
                                            number_format(
                                                (float)
                                                $item->ordered_quantity,
                                                3
                                            )
                                        }}

                                    </td>


                                    <td>

                                        {{
                                            number_format(
                                                (float)
                                                $item->unit_price,
                                                2
                                            )
                                        }}

                                    </td>


                                    <td>

                                        {{
                                            number_format(
                                                (float)
                                                $item->discount,
                                                2
                                            )
                                        }}

                                    </td>


                                    <td>

                                        {{
                                            number_format(
                                                (float)
                                                $item->tax,
                                                2
                                            )
                                        }}

                                    </td>


                                    <td>

                                        <strong>

                                            {{
                                                number_format(
                                                    (float)
                                                    $item->line_total,
                                                    2
                                                )
                                            }}

                                        </strong>

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="requisition-table-empty">

                    <i class="fas fa-box-open"></i>

                    <p>
                        No items have been added to this LPO.
                    </p>

                </div>

            @endif

        </div>

    </div>



    {{-- ============================================================
         TOTALS
         ============================================================ --}}

    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">

                    <i class="fas fa-calculator"></i>

                </div>

                <div>

                    <h5>
                        LPO Totals
                    </h5>

                    <p>
                        Financial summary of this purchase order.
                    </p>

                </div>

            </div>

        </div>


        <div class="requisition-section-body">

            <div class="row">

                <div class="col-md-4">

                    <div class="requisition-detail-item">

                        <label class="requisition-detail-label">

                            Subtotal

                        </label>

                        <strong>

                            {{
                                number_format(
                                    (float)
                                    $storeLpo->subtotal,
                                    2
                                )
                            }}

                        </strong>

                    </div>

                </div>


                <div class="col-md-4">

                    <div class="requisition-detail-item">

                        <label class="requisition-detail-label">

                            Discount

                        </label>

                        <strong>

                            {{
                                number_format(
                                    (float)
                                    $storeLpo->discount,
                                    2
                                )
                            }}

                        </strong>

                    </div>

                </div>


                <div class="col-md-4">

                    <div class="requisition-detail-item">

                        <label class="requisition-detail-label">

                            Tax

                        </label>

                        <strong>

                            {{
                                number_format(
                                    (float)
                                    $storeLpo->tax,
                                    2
                                )
                            }}

                        </strong>

                    </div>

                </div>


                <div class="col-md-12">

                    <div class="requisition-detail-item">

                        <label class="requisition-detail-label">

                            Total

                        </label>

                        <strong>

                            {{
                                number_format(
                                    (float)
                                    $storeLpo->total,
                                    2
                                )
                            }}

                        </strong>

                    </div>

                </div>

            </div>

        </div>

    </div>



    {{-- ============================================================
         NOTES
         ============================================================ --}}

    @if($storeLpo->notes)

        <div class="requisition-section">

            <div class="requisition-section-header">

                <div class="requisition-section-heading">

                    <div class="requisition-section-icon">

                        <i class="fas fa-sticky-note"></i>

                    </div>

                    <div>

                        <h5>
                            Notes
                        </h5>

                        <p>
                            Additional information recorded on this LPO.
                        </p>

                    </div>

                </div>

            </div>


            <div class="requisition-section-body">

                <div class="requisition-detail-item">

                    {{ $storeLpo->notes }}

                </div>

            </div>

        </div>

    @endif



    {{-- ============================================================
         APPROVAL HISTORY
         ============================================================ --}}

    <div class="requisition-section">

        <div class="requisition-section-header">

            <div class="requisition-section-heading">

                <div class="requisition-section-icon">

                    <i class="fas fa-history"></i>

                </div>

                <div>

                    <h5>
                        Approval History
                    </h5>

                    <p>
                        Actions taken during the approval process.
                    </p>

                </div>

            </div>

        </div>


        <div class="requisition-section-body">

            @if($storeLpo->approvals->count())

                <div class="requisition-list-table-wrapper">

                    <table class="requisition-list-table">

                        <thead>

                            <tr>

                                <th>
                                    Stage
                                </th>

                                <th>
                                    Action
                                </th>

                                <th>
                                    User
                                </th>

                                <th>
                                    Comments
                                </th>

                                <th>
                                    Date
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                            @foreach(
                                $storeLpo->approvals
                                as $approval
                            )

                                <tr>

                                    <td>

                                        {{
                                            strtoupper(
                                                $approval->approval_stage
                                            )
                                        }}

                                    </td>


                                    <td>

                                        {{
                                            strtoupper(
                                                $approval->action
                                            )
                                        }}

                                    </td>


                                    <td>

                                        {{
                                            $approval->user?->name
                                            ?? 'N/A'
                                        }}

                                    </td>


                                    <td>

                                        {{
                                            $approval->comments
                                            ?? '—'
                                        }}

                                    </td>


                                    <td>

                                        {{
                                            $approval->acted_at
                                                ?->format(
                                                    'd M Y H:i'
                                                )
                                            ?? '—'
                                        }}

                                    </td>

                                </tr>

                            @endforeach

                        </tbody>

                    </table>

                </div>

            @else

                <div class="requisition-table-empty">

                    <i class="fas fa-history"></i>

                    <p>
                        No approval actions have been recorded yet.
                    </p>

                </div>

            @endif

        </div>

    </div>



    {{-- ============================================================
         BOTTOM ACTIONS
         ============================================================ --}}

    <div class="requisition-bottom-actions">

        <div class="requisition-bottom-actions-left">

            <a
                href="{{ route('admin.store-lpos.index') }}"
                class="requisition-cancel-button"
            >

                <i class="fas fa-arrow-left me-1"></i>

                Back to LPOs

            </a>

        </div>


        <div class="requisition-bottom-actions-right">

            @if(in_array(
                $storeLpo->status,
                [
                    'DRAFT',
                    'RETURNED'
                ]
            ))

                <a
                    href="{{ route(
                        'admin.store-lpos.edit',
                        $storeLpo
                    ) }}"
                    class="requisition-add-button"
                >

                    <i class="fas fa-edit me-1"></i>

                    Edit LPO

                </a>

            @endif


            <a
                href="{{ route(
                    'admin.store-lpos.pdf',
                    $storeLpo
                ) }}"
                target="_blank"
                class="requisition-add-button"
            >

                <i class="fas fa-file-pdf me-1"></i>

                Generate PDF

            </a>

        </div>

    </div>

</div>

@endsection

