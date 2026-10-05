{{-- =====================================================
     DRAFT
===================================================== --}}

@if($isDraft)

    <div class="requisition-state-panel">

        <div class="requisition-state-main">

            <div class="requisition-state-icon">
                <i class="fas fa-pencil-alt"></i>
            </div>

            <div class="requisition-state-text">

                <strong>
                    Draft requisition
                </strong>

                <span>
                    This requisition has not yet been submitted for approval.
                </span>

            </div>

        </div>


        <div class="requisition-state-actions">

            @if($isRequester)

                <a
                    href="{{ route(
                        'admin.stores.store-requisitions.edit',
                        $storeRequisition
                    ) }}"
                    class="btn btn-sm btn-outline-primary"
                >
                    <i class="fas fa-edit me-1"></i>
                    Edit
                </a>


                <form
                    method="POST"
                    action="{{ route(
                        'admin.stores.store-requisitions.submit',
                        $storeRequisition
                    ) }}"
                    class="d-inline"
                >

                    @csrf

                    <button
                        type="submit"
                        class="btn btn-sm btn-primary"
                        onclick="return confirm(
                            'Submit this requisition for approval?'
                        )"
                    >
                        <i class="fas fa-paper-plane me-1"></i>
                        Submit for Approval
                    </button>

                </form>

            @endif

        </div>

    </div>


{{-- =====================================================
     RETURNED
===================================================== --}}

@elseif($isReturned)

    <div class="requisition-notice requisition-notice-info">

        <i class="fas fa-undo"></i>

        <div class="flex-grow-1">

            <strong>
                Requisition returned for correction.
            </strong>

            @if($returnApproval ?? null)

                <div class="mt-1">
                    {{ $returnApproval->comments
                        ?? $returnApproval->comment
                        ?? 'Please review and correct the requisition.' }}
                </div>

            @else

                <div class="mt-1">
                    Please review the requisition and make the required corrections.
                </div>

            @endif

        </div>


        @if($isRequester)

            <a
                href="{{ route(
                    'admin.stores.store-requisitions.edit',
                    $storeRequisition
                ) }}"
                class="btn btn-sm btn-primary"
            >
                <i class="fas fa-edit me-1"></i>
                Edit & Resubmit
            </a>

        @endif

    </div>


{{-- =====================================================
     PENDING
===================================================== --}}

@elseif($isPending && !$showApprovalActions)

    <div class="requisition-state-panel">

        <div class="requisition-state-main">

            <div class="requisition-state-icon">
                <i class="fas fa-hourglass-half"></i>
            </div>

            <div class="requisition-state-text">

                <strong>
                    Awaiting {{ $stageLabel }}
                </strong>

                <span>
                    The requisition is currently waiting for the next approval action.
                </span>

            </div>

        </div>

    </div>


{{-- =====================================================
     APPROVED
===================================================== --}}

@elseif($isApproved && $hasOutstanding && !$isClosed)

    <div class="requisition-state-panel">

        <div class="requisition-state-main">

            <div
                class="requisition-state-icon"
                style="background:#f0fdf4;color:#198754;"
            >
                <i class="fas fa-box-open"></i>
            </div>

            <div class="requisition-state-text">

                <strong>
                    Approved — ready for physical fulfillment
                </strong>

                <span>
                    {{ number_format($totalOutstanding, 3) }}
                    approved quantity remains outstanding.
                </span>

            </div>

        </div>


        <div class="requisition-state-actions">

            <button
                type="button"
                class="btn btn-sm btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#fulfillmentModal"
            >
                <i class="fas fa-box-open me-1"></i>
                Fulfill & Close
            </button>

        </div>

    </div>


{{-- =====================================================
     CLOSED
===================================================== --}}

@elseif($isClosed)

    <div class="requisition-notice requisition-notice-success">

        <i class="fas fa-check-circle"></i>

        <div>

            <strong>
                Requisition closed.
            </strong>

            <div class="mt-1">

                Physical fulfillment has been completed.

                @if($totalOutstanding > 0)

                    Any remaining balance must be handled through
                    a new requisition.

                @endif

            </div>

        </div>

    </div>


{{-- =====================================================
     REJECTED
===================================================== --}}

@elseif($isRejected)

    <div class="requisition-notice requisition-notice-danger">

        <i class="fas fa-times-circle"></i>

        <div>

            <strong>
                Requisition rejected.
            </strong>

            <div class="mt-1">
                This requisition is no longer in the approval workflow.
            </div>

        </div>

    </div>


{{-- =====================================================
     CANCELLED
===================================================== --}}

@elseif($isCancelled)

    <div class="requisition-notice requisition-notice-danger">

        <i class="fas fa-ban"></i>

        <div>

            <strong>
                Requisition cancelled.
            </strong>

            <div class="mt-1">
                This requisition is no longer active.
            </div>

        </div>

    </div>

@endif