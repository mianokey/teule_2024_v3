{{-- =========================================================
     DETAILS MODAL
========================================================= --}}

<div
    class="modal fade"
    id="detailsModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    <i class="fas fa-info-circle me-2"></i>
                    Requisition Details
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <div class="modal-body">

                <div class="requisition-details-grid">

                    <div class="requisition-detail-item">

                        <span class="requisition-detail-label">
                            Requisition Number
                        </span>

                        <span class="requisition-detail-value">
                            {{ $storeRequisition->requisition_number }}
                        </span>

                    </div>


                    <div class="requisition-detail-item">

                        <span class="requisition-detail-label">
                            Requested By
                        </span>

                        <span class="requisition-detail-value">
                            {{ optional(
                                $storeRequisition->requester
                            )->name ?? '—' }}
                        </span>

                    </div>


                    <div class="requisition-detail-item">

                        <span class="requisition-detail-label">
                            Department
                        </span>

                        <span class="requisition-detail-value">
                            {{ $storeRequisition->department ?? '—' }}
                        </span>

                    </div>


                    <div class="requisition-detail-item">

                        <span class="requisition-detail-label">
                            Requisition Type
                        </span>

                        <span class="requisition-detail-value">

                            {{ ucfirst(
                                strtolower(
                                    $storeRequisition->requisition_type
                                    ?? '—'
                                )
                            ) }}

                        </span>

                    </div>


                    <div class="requisition-detail-item">

                        <span class="requisition-detail-label">
                            Source Store
                        </span>

                        <span class="requisition-detail-value">

                            {{ optional(
                                $storeRequisition->sourceStore
                            )->name ?? '—' }}

                        </span>

                    </div>


                    <div class="requisition-detail-item">

                        <span class="requisition-detail-label">
                            Destination Store
                        </span>

                        <span class="requisition-detail-value">

                            {{ optional(
                                $storeRequisition->destinationStore
                            )->name ?? '—' }}

                        </span>

                    </div>


                    <div class="requisition-detail-item">

                        <span class="requisition-detail-label">
                            Approval Stage
                        </span>

                        <span class="requisition-detail-value">
                            {{ $stageLabel }}
                        </span>

                    </div>


                    <div class="requisition-detail-item">

                        <span class="requisition-detail-label">
                            Created
                        </span>

                        <span class="requisition-detail-value">

                            {{ $storeRequisition->created_at
                                ? $storeRequisition->created_at->format(
                                    'd M Y H:i'
                                )
                                : '—' }}

                        </span>

                    </div>

                </div>


                @if($storeRequisition->purpose)

                    <div class="mt-3">

                        <span class="requisition-detail-label">
                            Purpose
                        </span>

                        <div class="p-2 bg-light border rounded small">
                            {!! nl2br(e($storeRequisition->purpose)) !!}
                        </div>

                    </div>

                @endif


                @if($storeRequisition->submission_notes)

                    <div class="mt-3">

                        <span class="requisition-detail-label">
                            Notes
                        </span>

                        <div class="p-2 bg-light border rounded small">
                            {!! nl2br(e($storeRequisition->submission_notes)) !!}
                        </div>

                    </div>

                @endif

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     APPROVAL HISTORY MODAL
========================================================= --}}

<div
    class="modal fade"
    id="approvalHistoryModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-lg modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    <i class="fas fa-history me-2"></i>
                    Approval History
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <div class="modal-body">

                @if(
                    $storeRequisition->approvals &&
                    $storeRequisition->approvals->count()
                )

                    <div class="timeline">

                        @foreach(
                            $storeRequisition->approvals
                            ->sortByDesc(function ($approval) {

                                return $approval->decided_at
                                    ?? $approval->created_at;

                            })
                            as $approval
                        )

                            <div class="timeline-item">

                                <span class="timeline-dot"></span>

                                <div class="timeline-title">

                                    {{ ucfirst(
                                        $approval->decision
                                        ?? 'Decision'
                                    ) }}

                                </div>


                                <div class="timeline-meta">

                                    {{ optional(
                                        $approval->approver
                                    )->name ?? 'System User' }}

                                    @if($approval->decided_at)

                                        •
                                        {{ $approval->decided_at->format(
                                            'd M Y H:i'
                                        ) }}

                                    @elseif($approval->created_at)

                                        •
                                        {{ $approval->created_at->format(
                                            'd M Y H:i'
                                        ) }}

                                    @endif

                                </div>


                                @if(
                                    $approval->comments
                                    ?? $approval->comment
                                )

                                    <div class="timeline-comment">

                                        {{
                                            $approval->comments
                                            ?? $approval->comment
                                        }}

                                    </div>

                                @endif

                            </div>

                        @endforeach

                    </div>

                @else

                    <div class="text-center text-muted py-4">

                        No approval history available.

                    </div>

                @endif

            </div>

        </div>

    </div>

</div>


{{-- =========================================================
     SEND BACK MODAL
========================================================= --}}

@if($showApprovalActions)

<div
    class="modal fade"
    id="sendBackModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <form
            method="POST"
            action="{{ route(
                'admin.stores.store-requisitions.send-back',
                $storeRequisition
            ) }}"
            class="modal-content"
        >

            @csrf

            <div class="modal-header">

                <h5 class="modal-title">
                    Send Requisition Back
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <div class="modal-body">

                <label class="form-label">
                    Reason
                </label>

                <textarea
                    name="comments"
                    class="form-control"
                    rows="4"
                    required
                    placeholder="Explain what needs to be corrected..."
                ></textarea>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="btn btn-warning"
                >
                    <i class="fas fa-undo me-1"></i>
                    Send Back
                </button>

            </div>

        </form>

    </div>

</div>


{{-- =========================================================
     REJECT MODAL
========================================================= --}}

<div
    class="modal fade"
    id="rejectModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-dialog-centered">

        <form
            method="POST"
            action="{{ route(
                'admin.stores.store-requisitions.reject',
                $storeRequisition
            ) }}"
            class="modal-content"
        >

            @csrf

            <div class="modal-header">

                <h5 class="modal-title">
                    Reject Requisition
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <div class="modal-body">

                <div class="alert alert-danger">

                    This action will reject the requisition.
                    Please provide a reason.

                </div>


                <label class="form-label">
                    Rejection Reason
                </label>

                <textarea
                    name="comments"
                    class="form-control"
                    rows="4"
                    required
                    placeholder="Enter rejection reason..."
                ></textarea>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="btn btn-danger"
                >
                    <i class="fas fa-times me-1"></i>
                    Reject Requisition
                </button>

            </div>

        </form>

    </div>

</div>

@endif


{{-- =========================================================
     FULFILLMENT HISTORY
========================================================= --}}

@if(
    $storeRequisition->fulfillments &&
    $storeRequisition->fulfillments->count()
)

<div
    class="modal fade"
    id="fulfillmentHistoryModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-xl modal-dialog-centered">

        <div class="modal-content">

            <div class="modal-header">

                <h5 class="modal-title">
                    <i class="fas fa-box-open me-2"></i>
                    Physical Fulfillment History
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <div class="modal-body">

                <div class="table-responsive">

                    <table class="table table-sm requisition-table">

                        <thead>

                            <tr>

                                <th>
                                    Transaction
                                </th>

                                <th>
                                    Date
                                </th>

                                <th>
                                    Processed By
                                </th>

                                <th>
                                    Type
                                </th>

                                <th class="text-end">
                                    Items
                                </th>

                                <th class="text-end">
                                    Quantity
                                </th>

                                <th>
                                    Receipt
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                        @foreach(
                            $storeRequisition->fulfillments
                            ->sortByDesc(function ($fulfillment) {

                                return $fulfillment->created_at
                                    ? $fulfillment->created_at->timestamp
                                    : $fulfillment->id;

                            })
                            as $fulfillment
                        )

                            @php

                                $fulfillmentQuantity =
                                    $fulfillment->items
                                        ? $fulfillment->items->sum(
                                            'quantity'
                                        )
                                        : 0;

                            @endphp


                            <tr>

                                <td>
                                    <strong>
                                        {{ $fulfillment->transaction_number }}
                                    </strong>
                                </td>


                                <td>

                                    {{ $fulfillment->created_at
                                        ? $fulfillment->created_at->format(
                                            'd M Y H:i'
                                        )
                                        : '—' }}

                                </td>


                                <td>

                                    {{ optional(
                                        $fulfillment->processor
                                    )->name ?? '—' }}

                                </td>


                                <td>

                                    {{ strtoupper(
                                        $fulfillment->transaction_type
                                        ?? '—'
                                    ) }}

                                </td>


                                <td class="text-end">

                                    {{ $fulfillment->items
                                        ? $fulfillment->items->count()
                                        : 0 }}

                                </td>


                                <td class="text-end">

                                    {{ number_format(
                                        $fulfillmentQuantity,
                                        3
                                    ) }}

                                </td>


                                <td>

                                    <a
                                        href="{{ route(
                                            'admin.stores.store-requisitions.fulfillments.receipt',
                                            $fulfillment
                                        ) }}"
                                        class="btn btn-sm btn-outline-primary"
                                        target="_blank"
                                    >
                                        <i class="fas fa-file-pdf"></i>
                                    </a>

                                </td>

                            </tr>

                        @endforeach

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>

</div>

@endif


{{-- =========================================================
     PHYSICAL FULFILLMENT MODAL
========================================================= --}}

@if($isApproved && $hasOutstanding && !$isClosed)

<div
    class="modal fade"
    id="fulfillmentModal"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">

        <form
            method="POST"
            action="{{ route(
                'admin.stores.store-requisitions.fulfill',
                $storeRequisition
            ) }}"
            class="modal-content"
            id="fulfillmentForm"
        >
        <input type="hidden" name="mode" value="available">

            @csrf


            <div class="modal-header">

                <div>

                    <h5 class="modal-title mb-0">

                        <i class="fas fa-box-open me-2"></i>
                        Physical Fulfillment

                    </h5>

                    <small class="text-muted">

                        {{ $storeRequisition->requisition_number }}

                    </small>

                </div>


                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <div class="modal-body">

                <div class="fulfillment-rules mb-3">

                    <strong>
                        Fulfillment rules
                    </strong>

                    <ul class="mb-0 mt-1 ps-3">

                        <li>
                            Issue only up to the outstanding approved quantity.
                        </li>

                        <li>
                            Issue only what is available in the source store.
                        </li>

                        <li>
                            Partial quantities are allowed.
                        </li>

                        <li>
                            Processing this transaction closes the current requisition.
                        </li>

                        <li>
                            Any remaining balance requires a new requisition.
                        </li>

                    </ul>

                </div>


                <div class="table-responsive">

                    <table class="table table-sm table-bordered requisition-table">

                        <thead>

                            <tr>

                                <th>
                                    Item
                                </th>

                                <th>
                                    Variant
                                </th>

                                <th class="text-end">
                                    Approved
                                </th>

                                <th class="text-end">
                                    Issued
                                </th>

                                <th class="text-end">
                                    Balance
                                </th>

                                <th class="text-end">
                                    Stock
                                </th>

                                <th style="width:150px;">
                                    Issue Now
                                </th>

                            </tr>

                        </thead>


                        <tbody>

                        @foreach(
                            $storeRequisition->items as $item
                        )

                            @php

                                $approved =
                                    (float) $item->approved_quantity;

                                $issued =
                                    (float) $item->issued_quantity;

                                $outstanding = max(
                                    0,
                                    $approved - $issued
                                );

                                $stockKey =
                                    $item->store_item_id
                                    . '|'
                                    . (
                                        $item->variant_id
                                        ?? 'null'
                                    );

                                $availableStock =
                                    (float) optional(
                                        $sourceStockByKey->get(
                                            $stockKey
                                        )
                                    )->quantity;

                                $stockExists =
                                    $sourceStockByKey->has(
                                        $stockKey
                                    );

                                $maximumIssue =
                                    min(
                                        $outstanding,
                                        $availableStock
                                    );

                            @endphp


                            @if($outstanding <= 0)

                                @continue

                            @endif


                            <tr
                                class="{{
                                    $availableStock < $outstanding
                                        ? 'fulfillment-unavailable-row'
                                        : ''
                                }}"
                            >

                                <td>

                                    <span class="item-name">
                                        {{ optional(
                                            $item->item
                                        )->name ?? 'Unknown Item' }}
                                    </span>

                                </td>


                                <td>

                                    {{ $item->variant
                                        ? (
                                            $item->variant->name
                                            ?? $item->variant->label
                                            ?? '—'
                                        )
                                        : '—' }}

                                </td>


                                <td class="text-end">

                                    {{ number_format(
                                        $approved,
                                        3
                                    ) }}

                                </td>


                                <td class="text-end">

                                    {{ number_format(
                                        $issued,
                                        3
                                    ) }}

                                </td>


                                <td class="text-end">

                                    <strong class="text-warning">

                                        {{ number_format(
                                            $outstanding,
                                            3
                                        ) }}

                                    </strong>

                                </td>


                                <td class="text-end">

                                    @if($stockExists)

                                        <strong
                                            class="{{
                                                $availableStock >= $outstanding
                                                    ? 'stock-available'
                                                    : 'stock-insufficient'
                                            }}"
                                        >

                                            {{ number_format(
                                                $availableStock,
                                                3
                                            ) }}

                                        </strong>

                                    @else

                                        <span class="text-muted">
                                            0.000
                                        </span>

                                    @endif

                                </td>


                                <td>

                                    <div class="fulfillment-input">

                                        <input
                                            type="hidden"
                                            name="items[{{ $loop->index }}][requisition_item_id]"
                                            value="{{ $item->id }}"
                                            class="fulfillment-requisition-item"
                                        >


                                        <input
                                            type="number"
                                            name="items[{{ $loop->index }}][quantity]"
                                            class="form-control form-control-sm fulfillment-quantity"
                                            value="0"
                                            min="0"
                                            max="{{ $maximumIssue }}"
                                            step="0.001"
                                            data-outstanding="{{ $outstanding }}"
                                            data-available="{{ $availableStock }}"
                                            data-maximum="{{ $maximumIssue }}"
                                        >


                                        <div class="fulfillment-stock-label">

                                            @if($maximumIssue > 0)

                                                <button
                                                    type="button"
                                                    class="btn btn-link btn-sm p-0 use-maximum-quantity"
                                                    data-target-max="{{ $maximumIssue }}"
                                                >
                                                    Use max
                                                </button>

                                                @if($availableStock < $outstanding)

                                                    <span class="text-warning ms-1">
                                                        Stock limited
                                                    </span>

                                                @endif

                                            @else

                                                <span class="text-danger">
                                                    No stock available
                                                </span>

                                            @endif

                                        </div>

                                    </div>

                                </td>

                            </tr>

                        @endforeach

                        </tbody>

                    </table>

                </div>


                <div class="mt-3">

                    <label class="form-label">
                        Fulfillment Notes
                    </label>

                    <textarea
                        name="notes"
                        class="form-control form-control-sm"
                        rows="2"
                        placeholder="Optional notes about this physical fulfillment..."
                    ></textarea>

                </div>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-sm btn-secondary"
                    data-bs-dismiss="modal"
                >
                    Cancel
                </button>


                <button
                    type="submit"
                    class="btn btn-sm btn-primary"
                    id="fulfillmentSubmitButton"
                >
                    <i class="fas fa-check me-1"></i>
                    Process & Close
                </button>

            </div>

        </form>

    </div>

</div>

@endif


<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | USE MAXIMUM
    |--------------------------------------------------------------------------
    */

    document
        .querySelectorAll('.use-maximum-quantity')
        .forEach(function (button) {

            button.addEventListener('click', function () {

                const cell =
                    button.closest('td');

                if (!cell) {
                    return;
                }

                const input =
                    cell.querySelector(
                        '.fulfillment-quantity'
                    );

                if (!input) {
                    return;
                }

                input.value =
                    button.dataset.targetMax || 0;

            });

        });


    /*
    |--------------------------------------------------------------------------
    | FULFILLMENT SUBMISSION
    |--------------------------------------------------------------------------
    */

    const fulfillmentForm =
        document.getElementById(
            'fulfillmentForm'
        );

    if (!fulfillmentForm) {
        return;
    }


    fulfillmentForm.addEventListener(
        'submit',
        function (event) {

            const quantityInputs =
                fulfillmentForm.querySelectorAll(
                    '.fulfillment-quantity'
                );

            let hasQuantity = false;
            let valid = true;


            quantityInputs.forEach(
                function (input) {

                    const value =
                        parseFloat(
                            input.value || 0
                        );

                    const outstanding =
                        parseFloat(
                            input.dataset.outstanding || 0
                        );

                    const available =
                        parseFloat(
                            input.dataset.available || 0
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | ZERO = DO NOT SUBMIT THIS ITEM
                    |--------------------------------------------------------------------------
                    */

                    if (value <= 0) {

                        input.disabled = true;

                        const hidden =
                            input
                                .closest('td')
                                ?.querySelector(
                                    '.fulfillment-requisition-item'
                                );

                        if (hidden) {
                            hidden.disabled = true;
                        }

                        return;
                    }


                    hasQuantity = true;


                    /*
                    |--------------------------------------------------------------------------
                    | OUTSTANDING CHECK
                    |--------------------------------------------------------------------------
                    */

                    if (
                        value >
                        outstanding + 0.000001
                    ) {

                        alert(
                            'The issue quantity cannot exceed the outstanding approved quantity.'
                        );

                        input.focus();

                        valid = false;

                        return;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | STOCK CHECK
                    |--------------------------------------------------------------------------
                    */

                    if (
                        value >
                        available + 0.000001
                    ) {

                        alert(
                            'The issue quantity cannot exceed the available stock.'
                        );

                        input.focus();

                        valid = false;

                        return;
                    }

                }
            );


            if (!valid) {

                event.preventDefault();

                return;
            }


            if (!hasQuantity) {

                event.preventDefault();

                alert(
                    'Please enter at least one quantity to fulfill.'
                );

                return;
            }


            /*
            |--------------------------------------------------------------------------
            | FINAL CONFIRMATION
            |--------------------------------------------------------------------------
            */

            const confirmed =
                confirm(
                    'Process this physical fulfillment and close this requisition? Any remaining balance will need to be handled through a new requisition.'
                );


            if (!confirmed) {

                event.preventDefault();

                return;
            }


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

});
</script>