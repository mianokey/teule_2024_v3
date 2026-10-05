<div class="requisition-page-header">

    <div>

        <div class="requisition-title-row">

            <a
                href="{{ route('admin.stores.store-requisitions.index') }}"
                class="btn btn-sm btn-outline-secondary"
                title="Back to requisitions"
            >
                <i class="fas fa-arrow-left"></i>
            </a>

            <div>

                <h1 class="requisition-page-title">
                    Store Requisition
                </h1>

                <div class="requisition-number">

                    {{ $storeRequisition->requisition_number }}

                    @if($storeRequisition->created_at)
                        <span class="mx-1">•</span>
                        {{ $storeRequisition->created_at->format('d M Y H:i') }}
                    @endif

                </div>

            </div>

        </div>

    </div>


    <div class="requisition-page-actions">

        <button
            type="button"
            class="btn btn-sm btn-outline-primary"
            data-bs-toggle="modal"
            data-bs-target="#detailsModal"
        >
            <i class="fas fa-info-circle me-1"></i>
            Details
        </button>


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
                Receipt
            </a>

        @endif


        <span class="requisition-status {{ $statusClass }}">
            {{ $statusLabel }}
        </span>

    </div>

</div>