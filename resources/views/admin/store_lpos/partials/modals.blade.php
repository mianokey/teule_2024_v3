
{{-- RETURN LPO MODAL --}}
<div
    class="modal fade"
    id="lpoReturnModal"
    tabindex="-1"
    aria-labelledby="lpoReturnModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form
                method="POST"
                action="{{ route('admin.store-lpos.approval', $storeLpo) }}"
            >
                @csrf

                <input type="hidden" name="action" value="return">

                <div class="modal-header">
                    <h5 class="modal-title" id="lpoReturnModalLabel">
                        Return LPO for Correction
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>

                <div class="modal-body">
                    <p>
                        Please provide the reason this LPO is being returned.
                    </p>

                    <label for="lpoReturnComments" class="form-label">
                        Comments <span class="text-danger">*</span>
                    </label>

                    <textarea
                        name="comments"
                        id="lpoReturnComments"
                        class="form-control"
                        rows="4"
                        required
                        placeholder="Explain the corrections required..."
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

                    <button type="submit" class="btn btn-warning">
                        Return LPO
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- REJECT LPO MODAL --}}
<div
    class="modal fade"
    id="lpoRejectModal"
    tabindex="-1"
    aria-labelledby="lpoRejectModalLabel"
    aria-hidden="true"
>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form
                method="POST"
                action="{{ route('admin.store-lpos.approval', $storeLpo) }}"
            >
                @csrf

                <input type="hidden" name="action" value="reject">

                <div class="modal-header">
                    <h5 class="modal-title" id="lpoRejectModalLabel">
                        Reject LPO
                    </h5>

                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>

                <div class="modal-body">
                    <p>
                        Please provide a reason for rejecting this LPO.
                    </p>

                    <label for="lpoRejectComments" class="form-label">
                        Reason for Rejection
                        <span class="text-danger">*</span>
                    </label>

                    <textarea
                        name="comments"
                        id="lpoRejectComments"
                        class="form-control"
                        rows="4"
                        required
                        placeholder="Explain why this LPO is being rejected..."
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

                    <button type="submit" class="btn btn-danger">
                        Reject LPO
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
