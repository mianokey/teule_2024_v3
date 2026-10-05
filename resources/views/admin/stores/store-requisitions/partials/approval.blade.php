@if($showApprovalActions)

    <div class="requisition-approval-panel">

        <div class="requisition-approval-panel-title">

            <i class="fas fa-user-check me-1"></i>

            Action Required:
            {{ $stageLabel }}

        </div>


        <div class="requisition-approval-actions">

            {{-- APPROVE --}}

            <form
                method="POST"
                action="{{ route(
                    'admin.stores.store-requisitions.approve',
                    $storeRequisition
                ) }}"
                class="d-inline"
            >

                @csrf

                <button
                    type="submit"
                    class="btn btn-sm btn-success"
                    onclick="return confirm(
                        'Approve this requisition?'
                    )"
                >
                    <i class="fas fa-check me-1"></i>
                    Approve
                </button>

            </form>


            {{-- SEND BACK --}}

            <button
                type="button"
                class="btn btn-sm btn-warning"
                data-bs-toggle="modal"
                data-bs-target="#sendBackModal"
            >
                <i class="fas fa-undo me-1"></i>
                Send Back
            </button>


            {{-- REJECT --}}

            <button
                type="button"
                class="btn btn-sm btn-danger"
                data-bs-toggle="modal"
                data-bs-target="#rejectModal"
            >
                <i class="fas fa-times me-1"></i>
                Reject
            </button>

        </div>

    </div>

@endif