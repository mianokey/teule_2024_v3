<div class="requisition-card">

    <div class="requisition-card-header">

        <div>
            <div class="requisition-card-title">
                Budget Details
            </div>

            <div class="requisition-card-subtitle">
                Budget information and submission details
            </div>
        </div>

    </div>

    <div class="p-3">

        <div class="requisition-details-grid">

            <div class="requisition-detail-item">

                <span class="requisition-detail-label">
                    Budget Period
                </span>

                <span class="requisition-detail-value">
                    {{ $budget->period->name ?? '—' }}
                </span>

            </div>


            <div class="requisition-detail-item">

                <span class="requisition-detail-label">
                    Department
                </span>

                <span class="requisition-detail-value">
                    {{ $budget->costCentre->department->name ?? '—' }}
                </span>

            </div>


            <div class="requisition-detail-item">

                <span class="requisition-detail-label">
                    Cost Centre
                </span>

                <span class="requisition-detail-value">
                    {{ $budget->costCentre->name ?? '—' }}
                </span>

            </div>


            <div class="requisition-detail-item">

                <span class="requisition-detail-label">
                    Prepared By
                </span>

                <span class="requisition-detail-value">
                    {{ $budget->creator->name ?? '—' }}
                </span>

            </div>


            <div class="requisition-detail-item">

                <span class="requisition-detail-label">
                    Submitted By
                </span>

                <span class="requisition-detail-value">
                    {{ $budget->submitter->name ?? '—' }}
                </span>

            </div>


            <div class="requisition-detail-item">

                <span class="requisition-detail-label">
                    Submitted On
                </span>

                <span class="requisition-detail-value">
                    {{ $budget->submitted_at
                        ? \Carbon\Carbon::parse($budget->submitted_at)->format('d M Y, h:i A')
                        : '—' }}
                </span>

            </div>


            <div class="requisition-detail-item">

                <span class="requisition-detail-label">
                    Approved By
                </span>

                <span class="requisition-detail-value">
                    {{ $budget->approver->name ?? '—' }}
                </span>

            </div>


            <div class="requisition-detail-item">

                <span class="requisition-detail-label">
                    Approved On
                </span>

                <span class="requisition-detail-value">
                    {{ $budget->approved_at
                        ? \Carbon\Carbon::parse($budget->approved_at)->format('d M Y, h:i A')
                        : '—' }}
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

        </div>


        @if($budget->notes)

            <div class="mt-3">

                <span class="requisition-detail-label">
                    Budget Notes
                </span>

                <div class="requisition-detail-item">
                    {{ $budget->notes }}
                </div>

            </div>

        @endif

    </div>

</div>