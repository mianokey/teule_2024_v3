<div class="requisition-bottom-actions">

    <div class="requisition-bottom-actions-left">

        <a href="{{ route('admin.finance.budgets.index') }}"
           class="requisition-cancel-button btn-sm">

            <i class="fas fa-arrow-left mr-1"></i>
            Back to Budgets

        </a>

    </div>


    <div class="requisition-bottom-actions-right">

        @if($isEditable)

            <a href="{{ route('admin.finance.budgets.edit', $budget->id) }}"
               class="requisition-add-button btn-sm">

                <i class="fas fa-edit mr-1"></i>
                Edit Budget

            </a>

        @endif

    </div>

</div>