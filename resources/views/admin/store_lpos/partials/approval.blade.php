
@php
    $currentStage = strtolower(trim((string) $storeLpo->approval_stage));
    $currentStatus = strtoupper(trim((string) $storeLpo->status));

    $isHodStage = in_array($currentStage, ['hod', 'pending_hod'], true);
    $isManagementStage = in_array($currentStage, ['management', 'pending_management'], true);

    $isPending = in_array(
        $currentStatus,
        ['PENDING', 'PENDING_HOD', 'PENDING_MANAGEMENT'],
        true
    );

    $canApproveHod = $isPending
        && $isHodStage
        && (
            auth()->user()->can('APPROVE STORE LPO - HOD')
            || auth()->user()->can('APPROVE STORE LPO')
        );

    $canApproveManagement = $isPending
        && $isManagementStage
        && (
            auth()->user()->can('APPROVE STORE LPO - MANAGEMENT')
            || auth()->user()->can('APPROVE STORE LPO')
        );

    $canTakeAction = $canApproveHod || $canApproveManagement;

    $approvalStageLabel = match ($currentStage) {
        'hod', 'pending_hod' => 'HOD',
        'management', 'pending_management' => 'MANAGEMENT',
        default => strtoupper((string) $storeLpo->approval_stage),
    };
@endphp

@if($isPending)
    <div class="requisition-approval-panel">
        <div class="row align-items-center g-2">

            <div class="col-md-6">
                <div class="requisition-approval-panel-title mb-1">
                    <i class="fas fa-user-check me-1"></i>

                    @if($canTakeAction)
                        Action Required: {{ $approvalStageLabel }}
                    @else
                        Awaiting {{ $approvalStageLabel }} Approval
                    @endif
                </div>

                <div class="small">
                    <strong>Status:</strong> {{ $storeLpo->status }}
                    <span class="mx-2">|</span>
                    <strong>Stage:</strong> {{ $approvalStageLabel }}
                </div>

                @unless($canTakeAction)
                    <div class="small mt-1">
                        Awaiting an authorised {{ $approvalStageLabel }} approver.
                    </div>
                @endunless
            </div>

            <div class="col-md-6 text-md-end">
                @if($canTakeAction)
                    <div class="requisition-approval-actions justify-content-md-end">

                        <form
                            method="POST"
                            action="{{ route('admin.store-lpos.approval', $storeLpo) }}"
                            class="d-inline"
                        >
                            @csrf
                            <input type="hidden" name="action" value="approve">

                            <button
                                type="submit"
                                class="btn btn-sm btn-success"
                                onclick="return confirm('Approve this LPO?')"
                            >
                                <i class="fas fa-check me-1"></i>
                                Approve
                            </button>
                        </form>

                        <button
                            type="button"
                            class="btn btn-sm btn-warning"
                            data-bs-toggle="modal"
                            data-bs-target="#lpoReturnModal"
                        >
                            <i class="fas fa-undo me-1"></i>
                            Return
                        </button>

                        <button
                            type="button"
                            class="btn btn-sm btn-danger"
                            data-bs-toggle="modal"
                            data-bs-target="#lpoRejectModal"
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
