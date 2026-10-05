@php

    $workflowSteps = [
        'draft' => 'Draft',
        'hod' => 'HOD',
        'management' => 'Management',
        'stores' => 'Stores',
        'fulfillment' => 'Fulfillment',
        'closed' => 'Closed',
    ];

    /*
    |--------------------------------------------------------------------------
    | Determine current workflow position
    |--------------------------------------------------------------------------
    */

    if ($isDraft) {

        $workflowPosition = 0;

    } elseif ($isReturned) {

        $workflowPosition = match ($approvalStage) {
            'hod' => 1,
            'management' => 2,
            'stores' => 3,
            default => 1,
        };

    } elseif ($isPending) {

        $workflowPosition = match ($approvalStage) {
            'hod' => 1,
            'management' => 2,
            'stores' => 3,
            default => 1,
        };

    } elseif ($isApproved) {

        $workflowPosition = 4;

    } elseif ($isClosed) {

        $workflowPosition = 5;

    } else {

        $workflowPosition = 0;
    }

@endphp


<div class="requisition-workflow">

    <div class="requisition-workflow-track">

        @foreach($workflowSteps as $key => $label)

            @php
                $position = $loop->index;

                $completed =
                    $position < $workflowPosition;

                $active =
                    $position === $workflowPosition;
            @endphp


            <div
                class="
                    workflow-step
                    {{ $completed ? 'completed' : '' }}
                    {{ $active ? 'active' : '' }}
                "
            >

                <div class="workflow-node">

                    @if($completed)

                        <i class="fas fa-check"></i>

                    @else

                        {{ $position + 1 }}

                    @endif

                </div>


                <span class="workflow-label">
                    {{ $label }}
                </span>

            </div>


            @if(!$loop->last)

                <div
                    class="
                        workflow-line
                        {{ $position < $workflowPosition
                            ? 'completed'
                            : '' }}
                    "
                ></div>

            @endif

        @endforeach

    </div>

</div>