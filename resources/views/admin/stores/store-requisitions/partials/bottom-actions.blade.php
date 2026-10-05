<div class="requisition-bottom-actions">

    <div class="requisition-bottom-actions-left">

        <a
            href="{{ route(
                'admin.stores.store-requisitions.index'
            ) }}"
            class="btn btn-sm btn-outline-secondary"
        >
            <i class="fas fa-arrow-left me-1"></i>
            Back to Requisitions
        </a>

    </div>


    <div class="requisition-bottom-actions-right">

        @if($isEditable)

            <a
                href="{{ route(
                    'admin.stores.store-requisitions.edit',
                    $storeRequisition
                ) }}"
                class="btn btn-sm btn-primary"
            >
                <i class="fas fa-edit me-1"></i>
                Edit
            </a>

        @endif


        @if($latestFulfillment)

            <a
                href="{{ route(
                    'admin.stores.store-requisitions.fulfillments.receipt',
                    $latestFulfillment
                ) }}"
                class="btn btn-sm btn-outline-primary"
                target="_blank"
            >
                <i class="fas fa-file-pdf me-1"></i>
                Download Receipt
            </a>

        @endif

    </div>

</div>