@php

    $budgetStages = [
        'finance' => 'Finance',
        'dcm'     => 'DCM',
        'cm'      => 'Centre Manager',
    ];

    $stageKeys = array_keys($budgetStages);

    $currentStageIndex = array_search(
        $approvalStage,
        $stageKeys,
        true
    );

    $isWorkflowComplete = $isApproved;

@endphp

<div class="requisition-workflow">

    <div class="requisition-workflow-track">

        @foreach($budgetStages as $key => $label)

            @php

                $stageIndex = array_search(
                    $key,
                    $stageKeys,
                    true
                );

                $completed = $isWorkflowComplete ||
                    (
                        $isPending &&
                        $currentStageIndex !== false &&
                        $stageIndex < $currentStageIndex
                    );

                $active = $isPending &&
                    $approvalStage === $key;

                if ($isReturned && $approvalStage === $key) {
                    $active = true;
                }

            @endphp

            <div class="workflow-step
                {{ $completed ? 'completed' : '' }}
                {{ $active ? 'active' : '' }}">

                <div class="workflow-node">

                    @if($completed)
                        <i class="fas fa-check"></i>
                    @else
                        {{ $stageIndex + 1 }}
                    @endif

                </div>

                <span class="workflow-label">
                    {{ $label }}
                </span>

            </div>

            @if(!$loop->last)

                @php
                    $nextStageIndex = $stageIndex + 1;

                    $lineCompleted = $isWorkflowComplete ||
                        (
                            $isPending &&
                            $currentStageIndex !== false &&
                            $nextStageIndex <= $currentStageIndex
                        );
                @endphp

                <div class="workflow-line
                    {{ $lineCompleted ? 'completed' : '' }}">
                </div>

            @endif

        @endforeach

    </div>

</div>