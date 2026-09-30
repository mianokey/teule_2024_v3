@extends('layouts.admin')

@section('content')

<div class="container-fluid">


{{-- PAGE HEADER --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h4 class="mb-1">Store Receipt</h4>
        <div class="text-muted">
            {{ $storeReceipt->receipt_number }}
        </div>
    </div>

    <div class="d-flex gap-2">

        @if($storeReceipt->status === 'DRAFT')

    <a href="{{ route('admin.store-receipts.edit', $storeReceipt) }}"
       class="btn btn-outline-primary">
        Edit Receipt
    </a>

    @if($storeReceipt->items->count())

        <form method="POST"
              action="{{ route('admin.store-receipts.post', $storeReceipt) }}"
              class="d-inline"
              onsubmit="return confirm('Post this receipt? This will increase the store stock and cannot be undone from this screen.');">

            @csrf

            <button type="submit"
                    class="btn btn-success">

                <i class="fas fa-check"></i>
                Post Receipt

            </button>

        </form>

    @endif

@endif


        <a href="{{ route('admin.store-receipts.index') }}"
           class="btn btn-outline-secondary">
            Back to Receipts
        </a>

    </div>
</div>


{{-- MESSAGES --}}
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="alert alert-danger">
        <strong>Please correct the following:</strong>

        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


{{-- RECEIPT DETAILS --}}
<div class="card mb-4">

    <div class="card-header">
        <h6 class="mb-0">Receipt Details</h6>
    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-3 mb-3">
                <small class="text-muted d-block">
                    Receipt Number
                </small>

                <strong>
                    {{ $storeReceipt->receipt_number }}
                </strong>
            </div>


            <div class="col-md-3 mb-3">
                <small class="text-muted d-block">
                    Store
                </small>

                <strong>
                    {{ $storeReceipt->store->name ?? '—' }}
                </strong>
            </div>


            <div class="col-md-3 mb-3">
                <small class="text-muted d-block">
                    Source
                </small>

                @if($storeReceipt->source_type === 'PURCHASE')

                    <span class="badge bg-primary">
                        PURCHASE
                    </span>

                @else

                    <span class="badge bg-success">
                        DONATION
                    </span>

                @endif
            </div>


            <div class="col-md-3 mb-3">
                <small class="text-muted d-block">
                    Status
                </small>

                @if($storeReceipt->status === 'DRAFT')

                    <span class="badge bg-warning text-dark">
                        DRAFT
                    </span>

                @elseif($storeReceipt->status === 'POSTED')

                    <span class="badge bg-success">
                        POSTED
                    </span>

                @else

                    <span class="badge bg-secondary">
                        {{ $storeReceipt->status }}
                    </span>

                @endif
            </div>


            <div class="col-md-3 mb-3">
                <small class="text-muted d-block">
                    Received Date
                </small>

                {{ optional($storeReceipt->received_date)->format('d M Y') }}
            </div>


            <div class="col-md-3 mb-3">
                <small class="text-muted d-block">
                    Received By
                </small>

                {{ $storeReceipt->receivedBy->name ?? '—' }}
            </div>


            @if($storeReceipt->source_type === 'PURCHASE')

                <div class="col-md-3 mb-3">

                    <small class="text-muted d-block">
                        Supplier
                    </small>

                    {{ $storeReceipt->supplier_name ?: '—' }}

                </div>


                <div class="col-md-3 mb-3">

                    <small class="text-muted d-block">
                        Supplier Reference
                    </small>

                    {{ $storeReceipt->supplier_reference ?: '—' }}

                </div>

            @else

                <div class="col-md-3 mb-3">

                    <small class="text-muted d-block">
                        Donation
                    </small>

                    @if($storeReceipt->donation)

                        Donation #{{ $storeReceipt->donation->id }}

                    @else

                        —

                    @endif

                </div>

            @endif


            @if($storeReceipt->notes)

                <div class="col-12">

                    <small class="text-muted d-block">
                        Notes
                    </small>

                    <div>
                        {{ $storeReceipt->notes }}
                    </div>

                </div>

            @endif

        </div>

    </div>

</div>


{{-- GOODS RECEIVED --}}
<div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">

        <h6 class="mb-0">
            Goods Received
        </h6>

        @if($storeReceipt->status === 'DRAFT')

            <button type="button"
                    class="btn btn-sm btn-primary"
                    data-bs-toggle="modal"
                    data-bs-target="#addReceiptItemModal">

                <i class="fas fa-plus"></i>

                Add Item

            </button>

        @endif

    </div>


    <div class="card-body">

        @if($storeReceipt->items->count())

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead>

                        <tr>

                            <th>
                                Item
                            </th>

                            <th>
                                Variant
                            </th>

                            <th>
                                Unit
                            </th>

                            <th>
                                Quantity
                            </th>

                            <th>
                                Notes
                            </th>

                            @if($storeReceipt->status === 'DRAFT')

                                <th>
                                    Actions
                                </th>

                            @endif

                        </tr>

                    </thead>


                    <tbody>

                        @foreach($storeReceipt->items as $receiptItem)

                            <tr>

                                <td>
                                    <strong>
                                        {{ $receiptItem->item->name ?? '—' }}
                                    </strong>
                                </td>


                                <td>

                                    @if($receiptItem->variant)

                                        {{ $receiptItem->variant->name }}

                                    @else

                                        <span class="text-muted">
                                            No Variant
                                        </span>

                                    @endif

                                </td>


                                <td>

                                    {{ $receiptItem->item->unit->code
                                        ?? $receiptItem->item->unit->name
                                        ?? '—' }}

                                </td>


                                <td>

                                    {{ number_format(
                                        (float) $receiptItem->quantity,
                                        3
                                    ) }}

                                </td>


                                <td>

                                    {{ $receiptItem->notes ?: '—' }}

                                </td>


                                @if($storeReceipt->status === 'DRAFT')

                                    <td>

                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editReceiptItemModal{{ $receiptItem->id }}">

                                            Edit

                                        </button>


                                        <form method="POST"
                                              action="{{ route(
                                                  'admin.store-receipt-items.destroy',
                                                  [
                                                      'storeReceipt' => $storeReceipt,
                                                      'storeReceiptItem' => $receiptItem
                                                  ]
                                              ) }}"
                                              class="d-inline"
                                              onsubmit="return confirm('Remove this item from the receipt?');">

                                            @csrf

                                            @method('DELETE')

                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-danger">

                                                Delete

                                            </button>

                                        </form>

                                    </td>

                                @endif

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>

        @else

            <div class="text-center py-5">

                <div class="mb-3 text-muted">

                    <i class="fas fa-box-open fa-3x"></i>

                </div>

                <h6>
                    No Goods Added Yet
                </h6>

                <p class="text-muted mb-3">
                    Add the items that were physically received into this store.
                </p>


                @if($storeReceipt->status === 'DRAFT')

                    <button type="button"
                            class="btn btn-primary"
                            data-bs-toggle="modal"
                            data-bs-target="#addReceiptItemModal">

                        <i class="fas fa-plus"></i>

                        Add First Item

                    </button>

                @endif

            </div>

        @endif

    </div>

</div>



</div>

{{-- ADD RECEIPT ITEM MODAL --}}

@if($storeReceipt->status === 'DRAFT')

<div class="modal fade"
     id="addReceiptItemModal"
     tabindex="-1"
     aria-hidden="true">


<div class="modal-dialog">

    <div class="modal-content">

        <form method="POST"
              action="{{ route(
                  'admin.store-receipt-items.store',
                  $storeReceipt
              ) }}">

            @csrf


            <div class="modal-header">

                <h5 class="modal-title">
                    Add Received Item
                </h5>

                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>

            </div>


            <div class="modal-body">


                {{-- ITEM --}}

                <div class="mb-3">

                    <label for="store_item_id"
                           class="form-label">

                        Item
                        <span class="text-danger">*</span>

                    </label>


                    <select name="store_item_id"
                            id="store_item_id"
                            class="form-select"
                            required>

                        <option value="">
                            Select Item
                        </option>


                        @foreach($items as $item)

                            <option value="{{ $item->id }}">

                                {{ $item->name }}

                                @if($item->sku)
                                    — {{ $item->sku }}
                                @endif

                            </option>

                        @endforeach

                    </select>

                </div>



                {{-- VARIANT --}}

                <div class="mb-3">

                    <label for="variant_id"
                           class="form-label">

                        Variant

                    </label>


                    <select name="variant_id"
                            id="variant_id"
                            class="form-select">

                        <option value="">
                            No Variant
                        </option>

                    </select>


                    <small class="text-muted">
                        Select a variant only when the item has one.
                    </small>

                </div>



                {{-- QUANTITY --}}

                <div class="mb-3">

                    <label for="quantity"
                           class="form-label">

                        Quantity
                        <span class="text-danger">*</span>

                    </label>


                    <input type="number"
                           name="quantity"
                           id="quantity"
                           class="form-control"
                           min="0.001"
                           step="0.001"
                           required>

                </div>



                {{-- NOTES --}}

                <div class="mb-3">

                    <label for="notes"
                           class="form-label">

                        Notes

                    </label>


                    <textarea name="notes"
                              id="notes"
                              class="form-control"
                              rows="3"
                              maxlength="2000"></textarea>

                </div>

            </div>



            <div class="modal-footer">

                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                    Cancel

                </button>


                <button type="submit"
                        class="btn btn-primary">

                    Add Item

                </button>

            </div>

        </form>

    </div>

</div>

</div>

{{-- EDIT RECEIPT ITEM MODALS --}}

@foreach($storeReceipt->items as $receiptItem)

<div class="modal fade"
     id="editReceiptItemModal{{ $receiptItem->id }}"
     tabindex="-1"
     aria-hidden="true">

<div class="modal-dialog">

    <div class="modal-content">

        <form method="POST"
              action="{{ route(
                  'admin.store-receipt-items.update',
                  [
                      'storeReceipt' => $storeReceipt,
                      'storeReceiptItem' => $receiptItem
                  ]
              ) }}">

            @csrf

            @method('PUT')


            <div class="modal-header">

                <h5 class="modal-title">
                    Edit Received Item
                </h5>


                <button type="button"
                        class="btn-close"
                        data-bs-dismiss="modal">
                </button>

            </div>


            <div class="modal-body">


                {{-- ITEM --}}

                <div class="mb-3">

                    <label class="form-label">

                        Item
                        <span class="text-danger">*</span>

                    </label>


                    <select name="store_item_id"
                            class="form-select edit-item-select"
                            data-variant-target="editVariant{{ $receiptItem->id }}"
                            required>

                        @foreach($items as $item)

                            <option value="{{ $item->id }}"
                                @if($receiptItem->store_item_id == $item->id)
                                    selected
                                @endif>

                                {{ $item->name }}

                                @if($item->sku)
                                    — {{ $item->sku }}
                                @endif

                            </option>

                        @endforeach

                    </select>

                </div>



                {{-- VARIANT --}}

                <div class="mb-3">

                    <label class="form-label">
                        Variant
                    </label>


                    <select name="variant_id"
                            id="editVariant{{ $receiptItem->id }}"
                            class="form-select"
                            data-current-variant="{{ $receiptItem->variant_id }}">

                        <option value="">
                            No Variant
                        </option>

                    </select>

                </div>



                {{-- QUANTITY --}}

                <div class="mb-3">

                    <label class="form-label">

                        Quantity
                        <span class="text-danger">*</span>

                    </label>


                    <input type="number"
                           name="quantity"
                           class="form-control"
                           min="0.001"
                           step="0.001"
                           value="{{ $receiptItem->quantity }}"
                           required>

                </div>



                {{-- NOTES --}}

                <div class="mb-3">

                    <label class="form-label">
                        Notes
                    </label>


                    <textarea name="notes"
                              class="form-control"
                              rows="3"
                              maxlength="2000">{{ $receiptItem->notes }}</textarea>

                </div>

            </div>



            <div class="modal-footer">

                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">

                    Cancel

                </button>


                <button type="submit"
                        class="btn btn-primary">

                    Save Changes

                </button>

            </div>

        </form>

    </div>

</div>

</div>

@endforeach

@endif

{{-- VARIANT JAVASCRIPT --}}

@if($storeReceipt->status === 'DRAFT')

<script>

document.addEventListener('DOMContentLoaded', function () {

    const itemVariants = {!! json_encode($itemVariants) !!};


    function populateVariants(
        itemSelect,
        variantSelect,
        selectedVariantId = null
    ) {

        variantSelect.innerHTML = '';


        const noVariant = document.createElement('option');

        noVariant.value = '';

        noVariant.textContent = 'No Variant';

        variantSelect.appendChild(noVariant);


        const itemId = itemSelect.value;


        if (!itemId) {
            return;
        }


        const variants = itemVariants[itemId] || [];


        variants.forEach(function (variant) {

            const option =
                document.createElement('option');


            option.value = variant.id;


            option.textContent =
                variant.name +
                (variant.code
                    ? ' — ' + variant.code
                    : '');


            if (
                selectedVariantId !== null &&
                String(selectedVariantId) ===
                String(variant.id)
            ) {

                option.selected = true;

            }


            variantSelect.appendChild(option);

        });

    }



    /*
     * ADD ITEM
     */

    const addItemSelect =
        document.getElementById('store_item_id');


    const addVariantSelect =
        document.getElementById('variant_id');


    if (
        addItemSelect &&
        addVariantSelect
    ) {

        addItemSelect.addEventListener(
            'change',
            function () {

                populateVariants(
                    addItemSelect,
                    addVariantSelect
                );

            }
        );

    }



    /*
     * EDIT ITEMS
     */

    document
        .querySelectorAll('.edit-item-select')
        .forEach(function (itemSelect) {

            const variantTarget =
                itemSelect.dataset.variantTarget;


            const variantSelect =
                document.getElementById(
                    variantTarget
                );


            if (!variantSelect) {
                return;
            }


            const currentVariantId =
                variantSelect.dataset.currentVariant;


            populateVariants(
                itemSelect,
                variantSelect,
                currentVariantId
            );


            itemSelect.addEventListener(
                'change',
                function () {

                    populateVariants(
                        itemSelect,
                        variantSelect
                    );

                }
            );

        });

});

</script>

@endif

@endsection
