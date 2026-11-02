@if($showApprovalActions)

    <div class="modal fade"
         id="sendBackModal"
         tabindex="-1"
         role="dialog"
         aria-labelledby="sendBackModalLabel"
         aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered"
             role="document">

            <div class="modal-content">

                <form method="POST"
                      action="{{ route('admin.finance.budgets.send-back', $budget->id) }}">

                    @csrf

                    <div class="modal-header">

                        <h5 class="modal-title"
                            id="sendBackModalLabel">

                            <i class="fas fa-undo text-warning mr-2"></i>
                            Send Budget Back

                        </h5>

                        <button type="button"
                                class="close"
                                data-dismiss="modal"
                                aria-label="Close">

                            <span aria-hidden="true">&times;</span>

                        </button>

                    </div>


                    <div class="modal-body">

                        <p class="text-muted small">
                            Provide a clear reason why this budget is being
                            returned for adjustment.
                        </p>

                        <div class="form-group">

                            <label for="return_reason">
                                Reason for Returning
                                <span class="text-danger">*</span>
                            </label>

                            <textarea
                                name="return_reason"
                                id="return_reason"
                                class="form-control"
                                rows="4"
                                required
                                maxlength="2000"
                                placeholder="Explain the adjustments required...">{{ old('return_reason') }}</textarea>

                        </div>

                    </div>


                    <div class="modal-footer">

                        <button type="button"
                                class="btn btn-light btn-sm"
                                data-dismiss="modal">

                            Cancel

                        </button>

                        <button type="submit"
                                class="btn btn-warning btn-sm">

                            <i class="fas fa-undo mr-1"></i>
                            Send Back

                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>
@endif

@if($showApprovalActions)

    <div class="modal fade"
         id="rejectModal"
         tabindex="-1"
         role="dialog"
         aria-labelledby="rejectModalLabel"
         aria-hidden="true">

        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

                <form method="POST"
                      action="{{ route('admin.finance.budgets.reject', $budget->id) }}">

                    @csrf

                    <div class="modal-header">
                        <h5 class="modal-title" id="rejectModalLabel">
                            <i class="fas fa-times-circle text-danger mr-2"></i>
                            Reject Monthly Budget
                        </h5>

                        <button type="button"
                                class="close"
                                data-dismiss="modal"
                                aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <p class="text-muted small">
                            Please provide a clear reason for rejecting this budget.
                            This action will end its approval process.
                        </p>

                        <div class="form-group">
                            <label for="rejection_reason">
                                Rejection Reason
                                <span class="text-danger">*</span>
                            </label>

                            <textarea
                                name="rejection_reason"
                                id="rejection_reason"
                                class="form-control"
                                rows="4"
                                maxlength="2000"
                                required
                                placeholder="Explain why this budget is being rejected...">{{ old('rejection_reason') }}</textarea>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button"
                                class="btn btn-light btn-sm"
                                data-dismiss="modal">
                            Cancel
                        </button>

                        <button type="submit"
                                class="btn btn-danger btn-sm">
                            <i class="fas fa-times mr-1"></i>
                            Confirm Rejection
                        </button>
                    </div>

                </form>

            </div>
        </div>
    </div>

@endif
