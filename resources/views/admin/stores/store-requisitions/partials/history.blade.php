<div class="requisition-history-grid">

    {{-- =====================================================
         APPROVAL HISTORY
    ====================================================== --}}

    <div class="requisition-history-card">

        <div class="requisition-history-card-title">

            <span>
                <i class="fas fa-user-check me-1"></i>
                Approval History
            </span>

            <button
                type="button"
                class="btn btn-sm btn-outline-primary"
                data-bs-toggle="modal"
                data-bs-target="#approvalHistoryModal"
            >
                View
            </button>

        </div>


        <div class="requisition-history-card-text">

            @if(
                $storeRequisition->approvals &&
                $storeRequisition->approvals->count()
            )

                {{ $storeRequisition->approvals->count() }}
                approval action(s) recorded.

            @else

                No approval actions recorded yet.

            @endif

        </div>

    </div>


    {{-- =====================================================
         FULFILLMENT HISTORY
    ====================================================== --}}

    <div class="requisition-history-card">

        <div class="requisition-history-card-title">

            <span>
                <i class="fas fa-box-open me-1"></i>
                Physical Fulfillment
            </span>

            @if(
                $storeRequisition->fulfillments &&
                $storeRequisition->fulfillments->count()
            )

                <button
                    type="button"
                    class="btn btn-sm btn-outline-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#fulfillmentHistoryModal"
                >
                    View
                </button>

            @endif

        </div>


        <div class="requisition-history-card-text">

            @if(
                $storeRequisition->fulfillments &&
                $storeRequisition->fulfillments->count()
            )

                {{ $storeRequisition->fulfillments->count() }}
                physical transaction(s) recorded.

            @else

                No physical fulfillment has been recorded.

            @endif

        </div>

    </div>

</div>