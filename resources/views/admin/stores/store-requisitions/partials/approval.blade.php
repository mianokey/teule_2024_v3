{{-- REQUISITION APPROVAL PANEL --}}
@if($showApprovalActions)

    <div class="requisition-approval-panel">

        <div class="row align-items-center g-2">

            {{-- LEFT: TITLE AND STATUS --}}
            <div class="col-md-6">

                <div class="requisition-approval-panel-title mb-1">
                    <i class="fas fa-user-check me-1"></i>

                    @if($canApproveCurrentStage)
                        Action Required: {{ $stageLabel }}
                    @else
                        Awaiting {{ $stageLabel }} Approval
                    @endif
                </div>

                <div class="small">
                    <strong>Status:</strong>
                    {{ $storeRequisition->status }}

                    <span class="mx-2">|</span>

                    <strong>Stage:</strong>
                    {{ $stageLabel }}
                </div>

                @unless($canApproveCurrentStage)
                    <div class="small mt-1">
                        Awaiting an authorised {{ $stageLabel }} approver.
                    </div>
                @endunless

            </div>

            {{-- RIGHT: ACTION BUTTONS --}}
            <div class="col-md-6 text-md-end">

                @if($canApproveCurrentStage)

                    <div class="requisition-approval-actions justify-content-md-end">

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
                                onclick="return confirm('Approve this requisition?')"
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

                @endif

            </div>

        </div>

    </div>

@endif