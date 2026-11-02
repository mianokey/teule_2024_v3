<div class="requisition-state-panel">

    <div class="requisition-state-main">

        <div class="requisition-state-icon">

            @if($isDraft)
                <i class="fas fa-file-alt"></i>
            @elseif($isReturned)
                <i class="fas fa-undo"></i>
            @elseif($isPending)
                <i class="fas fa-clock"></i>
            @elseif($isApproved)
                <i class="fas fa-check-circle"></i>
            @elseif($isRejected)
                <i class="fas fa-times-circle"></i>
            @else
                <i class="fas fa-info-circle"></i>
            @endif

        </div>

        <div class="requisition-state-text">

            <strong>{{ $statusLabel }}</strong>

            <span>

                @if($isDraft)

                    This budget is still a draft.

                @elseif($isReturned)

                    This budget was returned for adjustment.

                @elseif($isPending)

                    Awaiting {{ $stageLabel }}.

                @elseif($isApproved)

                    This budget has completed all approval stages.

                @elseif($isRejected)

                    This budget has been rejected.

                @else

                    Current budget status.

                @endif

            </span>

        </div>

    </div>

    <div class="requisition-state-actions">

        @if(
            $isEditable &&
            ($isDraft || $isReturned)
        )

            <form method="POST"
                  action="{{ route('admin.finance.budgets.submit', $budget->id) }}"
                  onsubmit="return confirm('Submit this budget for approval?');">

                @csrf

                <button type="submit"
                        class="requisition-add-button btn-sm">

                    <i class="fas fa-paper-plane mr-1"></i>
                    Submit for Approval

                </button>

            </form>

        @endif

    </div>

</div>


@if($isReturned && $budget->return_reason)

    <div class="requisition-notice requisition-notice-warning">

        <i class="fas fa-exclamation-circle"></i>

        <div>
            <strong>Reason for Return</strong>

            <div>
                {{ $budget->return_reason }}
            </div>
        </div>

    </div>

@endif


@if($isPeriodClosed)

    <div class="requisition-notice requisition-notice-info">

        <i class="fas fa-lock"></i>

        <div>
            <strong>Budget Period Closed</strong>

            <div>
                This budget period is closed. Editing and submission are unavailable.
            </div>
        </div>

    </div>

@endif