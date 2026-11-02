{{-- MONTHLY BUDGET HEADER --}}

<div class="requisition-page-header">

    <div>

        <div class="requisition-title-row">

            <a
                href="{{ route('admin.finance.budgets.index') }}"
                class="requisition-cancel-button"
                title="Back to budgets"
            >
                <i class="fas fa-arrow-left"></i>
            </a>

            <div>

                <h1 class="requisition-page-title">
                    Monthly Budget
                </h1>

                <div class="requisition-number">

                    {{ $budget->period->name ?? 'Budget Period' }}

                    @if($budget->created_at)
                        <span class="mx-1">•</span>
                        {{ $budget->created_at->format('d M Y H:i') }}
                    @endif

                </div>

            </div>

        </div>

    </div>


    <div class="requisition-page-actions">

        {{-- DETAILS --}}
            <div class="requisition-bottom-actions-left">

        <a href="{{ route('admin.finance.budgets.index') }}"
           class="requisition-cancel-button btn-sm">

            <i class="fas fa-arrow-left mr-1"></i>
            Back to Budgets

        </a>

    </div>

        <button
            type="button"
            class="requisition-add-button btn-outline-primary"
            data-bs-toggle="modal"
            data-bs-target="#detailsModal"
        >
            <i class="fas fa-info-circle me-1"></i>
            Details
        </button>


    </div>

</div>