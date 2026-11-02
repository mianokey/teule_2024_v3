<div class="requisition-card">

    <div class="requisition-card-header">

        <div>
            <div class="requisition-card-title">
                Approval History
            </div>

            <div class="requisition-card-subtitle">
                Record of budget approval decisions
            </div>
        </div>

        <span class="badge badge-light">
            {{ $approvals->count() }} {{ Str::plural('record', $approvals->count()) }}
        </span>

    </div>

    <div class="p-3">

        @if($approvals->isNotEmpty())

            <div class="timeline">

                @foreach($approvals as $approval)

                    @php

                        $decision = strtolower(
                            trim($approval->decision ?? '')
                        );

                        $decisionLabel = match($decision) {
                            'approved' => 'Approved',
                            'returned', 'sent_back' => 'Sent Back',
                            'rejected' => 'Rejected',
                            default => ucfirst(str_replace('_', ' ', $decision ?: 'Action')),
                        };

                    @endphp

                    <div class="timeline-item">

                        <div class="timeline-dot"></div>

                        <div class="timeline-title">

                            {{ $decisionLabel }}

                            <span class="text-muted font-weight-normal">
                                — {{ $approval->approver->name ?? 'Unknown User' }}
                            </span>

                        </div>

                        <div class="timeline-meta">

                            {{ ucfirst($approval->approval_stage ?? 'Approval') }}

                            @if($approval->acted_at)
                                <span class="mx-1">|</span>
                                {{ \Carbon\Carbon::parse($approval->acted_at)->format('d M Y, h:i A') }}
                            @endif

                        </div>


                        @if($approval->comments)

                            <div class="timeline-comment">
                                {{ $approval->comments }}
                            </div>

                        @endif


                        @if($approval->rejection_reason)

                            <div class="timeline-comment">
                                <strong>Reason:</strong>
                                {{ $approval->rejection_reason }}
                            </div>

                        @endif

                    </div>

                @endforeach

            </div>

        @else

            <div class="text-center text-muted py-3">

                <i class="fas fa-history fa-2x mb-2"></i>

                <div>No approval history available yet.</div>

            </div>

        @endif

    </div>

</div>