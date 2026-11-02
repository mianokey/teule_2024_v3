<div class="requisition-card">

    <div class="requisition-card-header">

        <div>
            <div class="requisition-card-title">
                Financial Summary
            </div>

            <div class="requisition-card-subtitle">
                Monthly budget allocation overview
            </div>
        </div>

    </div>

    <div class="requisition-summary-strip">

        <div class="requisition-summary-item">

            <span class="requisition-summary-label">
                Requested Amount
            </span>

            <span class="requisition-summary-value">
                KSh {{ number_format($requestedAmount, 2) }}
            </span>

        </div>


        <div class="requisition-summary-item">

            <span class="requisition-summary-label">
                Carried Forward
            </span>

            <span class="requisition-summary-value">
                KSh {{ number_format($carriedForwardAmount, 2) }}
            </span>

        </div>


        <div class="requisition-summary-item">

            <span class="requisition-summary-label">
                Total Requested
            </span>

            <span class="requisition-summary-value">
                KSh {{ number_format($totalRequested, 2) }}
            </span>

        </div>


        <div class="requisition-summary-item">

            <span class="requisition-summary-label">
                Approved Amount
            </span>

            <span class="requisition-summary-value">
                KSh {{ number_format($approvedAmount, 2) }}
            </span>

        </div>

    </div>

</div>