<div class="requisition-card">

    <div class="requisition-card-header">

        <div>

            <div class="requisition-card-title">

                <i class="fas fa-boxes me-1"></i>

                Requested Items

            </div>

            <div class="requisition-card-subtitle">

                {{ $storeRequisition->items->count() }}
                {{ $storeRequisition->items->count() === 1 ? 'item' : 'items' }}

            </div>

        </div>


        @if($isApproved)

            <span class="stores-badge {{ $fulfillmentBadge }}">
                {{ $fulfillmentLabel }}
            </span>

        @endif

    </div>


    <div class="table-responsive">

        <table class="table requisition-table">

            <thead>

                <tr>

                    <th style="width:40px;">
                        #
                    </th>

                    <th>
                        Item
                    </th>

                    <th>
                        Variant
                    </th>

                    <th class="text-end">
                        Requested
                    </th>

                    @if($isApproved || $isClosed)

                        <th class="text-end">
                            Approved
                        </th>

                        <th class="text-end">
                            Issued
                        </th>

                        <th class="text-end">
                            Balance
                        </th>

                    @endif

                    @if($isApproved)

                        <th class="text-end">
                            Stock
                        </th>

                    @endif

                </tr>

            </thead>


            <tbody>

            @forelse($storeRequisition->items as $item)

                @php

                    $requested =
                        (float) $item->requested_quantity;

                    $approved =
                        (float) $item->approved_quantity;

                    $issued =
                        (float) $item->issued_quantity;

                    $outstanding = max(
                        0,
                        $approved - $issued
                    );

                    $stockKey =
                        $item->store_item_id
                        . '|'
                        . (
                            $item->variant_id
                            ?? 'null'
                        );

                    $availableStock =
                        (float) optional(
                            $sourceStockByKey->get($stockKey)
                        )->quantity;

                    $stockExists =
                        $sourceStockByKey->has($stockKey);

                @endphp


                <tr>

                    <td>
                        {{ $loop->iteration }}
                    </td>


                    <td>

                        <span class="item-name">

                            {{ optional($item->item)->name
                                ?? 'Unknown Item' }}

                        </span>

                    </td>


                    <td>

                        @if($item->variant)

                            <span class="item-variant">

                                {{ $item->variant->name
                                    ?? $item->variant->label
                                    ?? '—' }}

                            </span>

                        @else

                            <span class="text-muted">
                                —
                            </span>

                        @endif

                    </td>


                    <td class="text-end">

                        <span class="quantity-value">

                            {{ number_format(
                                $requested,
                                3
                            ) }}

                        </span>

                    </td>


                    @if($isApproved || $isClosed)

                        <td class="text-end">

                            <span class="quantity-value">

                                {{ number_format(
                                    $approved,
                                    3
                                ) }}

                            </span>

                        </td>


                        <td class="text-end">

                            <span class="quantity-value">

                                {{ number_format(
                                    $issued,
                                    3
                                ) }}

                            </span>

                        </td>


                        <td class="text-end">

                            <span
                                class="
                                    quantity-value
                                    {{ $outstanding > 0
                                        ? 'quantity-balance'
                                        : 'quantity-zero' }}
                                "
                            >

                                {{ number_format(
                                    $outstanding,
                                    3
                                ) }}

                            </span>

                        </td>

                    @endif


                    @if($isApproved)

                        <td class="text-end">

                            @if($stockExists)

                                @if($availableStock >= $outstanding)

                                    <span class="stock-available">

                                        {{ number_format(
                                            $availableStock,
                                            3
                                        ) }}

                                    </span>

                                @elseif($availableStock > 0)

                                    <span class="stock-insufficient">

                                        {{ number_format(
                                            $availableStock,
                                            3
                                        ) }}

                                    </span>

                                @else

                                    <span class="text-muted">
                                        0.000
                                    </span>

                                @endif

                            @else

                                <span class="text-muted">
                                    0.000
                                </span>

                            @endif

                        </td>

                    @endif

                </tr>

            @empty

                <tr>

                    <td
                        colspan="8"
                        class="text-center text-muted py-4"
                    >
                        No items found.

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>


    {{-- =====================================================
         SUMMARY
    ====================================================== --}}

    <div class="requisition-summary-strip">

        <div class="requisition-summary-item">

            <span class="requisition-summary-label">
                Requested
            </span>

            <span class="requisition-summary-value">
                {{ number_format($totalRequested, 3) }}
            </span>

        </div>


        <div class="requisition-summary-item">

            <span class="requisition-summary-label">
                Approved
            </span>

            <span class="requisition-summary-value">

                @if($isApproved || $isClosed)

                    {{ number_format($totalApproved, 3) }}

                @else

                    —

                @endif

            </span>

        </div>


        <div class="requisition-summary-item">

            <span class="requisition-summary-label">
                Issued
            </span>

            <span class="requisition-summary-value">
                {{ number_format($totalIssued, 3) }}
            </span>

        </div>


        <div class="requisition-summary-item">

            <span class="requisition-summary-label">
                Outstanding
            </span>

            <span
                class="requisition-summary-value
                    {{ $totalOutstanding > 0
                        ? 'text-warning'
                        : 'text-success' }}"
            >

                @if($isApproved || $isClosed)

                    {{ number_format(
                        $totalOutstanding,
                        3
                    ) }}

                @else

                    —

                @endif

            </span>

        </div>

    </div>

</div>