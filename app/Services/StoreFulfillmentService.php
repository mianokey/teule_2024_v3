<?php

namespace App\Services;

use App\Models\StoreFulfillment;
use App\Models\StoreFulfillmentItem;
use App\Models\StoreRequisition;
use App\Models\StoreRequisitionItem;
use App\Models\StoreStock;
use App\Models\StoreStockMovement;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StoreFulfillmentService
{
    public function fulfill(
        StoreRequisition $requisition,
        array $items,
        int $userId,
        ?string $notes = null
    ): StoreFulfillment {
        return DB::transaction(function () use (
            $requisition,
            $items,
            $userId,
            $notes
        ) {

            $requisition = StoreRequisition::query()
                ->lockForUpdate()
                ->findOrFail($requisition->id);

            if (
                $requisition->status !== 'approved' ||
                $requisition->approval_stage !== 'approved'
            ) {
                throw new RuntimeException(
                    'Only fully approved requisitions can be fulfilled.'
                );
            }

            if (
                !in_array(
                    $requisition->requisition_type,
                    ['ITEM', 'TRANSFER'],
                    true
                )
            ) {
                throw new RuntimeException(
                    'Invalid requisition type.'
                );
            }

            if (!$requisition->source_store_id) {
                throw new RuntimeException(
                    'The requisition does not have a source store.'
                );
            }

            if (
                $requisition->requisition_type === 'TRANSFER' &&
                !$requisition->destination_store_id
            ) {
                throw new RuntimeException(
                    'A transfer requisition must have a destination store.'
                );
            }

            if (
                $requisition->requisition_type === 'TRANSFER' &&
                $requisition->source_store_id ===
                $requisition->destination_store_id
            ) {
                throw new RuntimeException(
                    'Source and destination stores cannot be the same.'
                );
            }

            /*
             * Lock stores in a consistent order.
             */
            $storeIds = [
                (int) $requisition->source_store_id,
            ];

            if ($requisition->requisition_type === 'TRANSFER') {
                $storeIds[] =
                    (int) $requisition->destination_store_id;
            }

            $storeIds = array_unique($storeIds);
            sort($storeIds);

            foreach ($storeIds as $storeId) {
                DB::table('stores')
                    ->where('id', $storeId)
                    ->lockForUpdate()
                    ->first();
            }

            /*
             * Make sure every submitted requisition item actually
             * belongs to this requisition.
             */
            $itemIds = array_map(
                'intval',
                array_column($items, 'requisition_item_id')
            );

            if (
                count($itemIds) !==
                count(array_unique($itemIds))
            ) {
                throw new RuntimeException(
                    'The same requisition item cannot appear more than once in one fulfillment.'
                );
            }

            $requisitionItems = StoreRequisitionItem::query()
                ->where(
                    'store_requisition_id',
                    $requisition->id
                )
                ->whereIn('id', $itemIds)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if (
                $requisitionItems->count() !==
                count($itemIds)
            ) {
                throw new RuntimeException(
                    'One or more requisition items could not be found.'
                );
            }

            /*
             * Validate quantities before touching stock.
             */
            foreach ($items as $line) {

                $requisitionItemId =
                    (int) $line['requisition_item_id'];

                $quantity =
                    (float) $line['quantity'];

                if ($quantity <= 0) {
                    throw new RuntimeException(
                        'Fulfillment quantity must be greater than zero.'
                    );
                }

                $requisitionItem =
                    $requisitionItems->get(
                        $requisitionItemId
                    );

                $approved =
                    (float) $requisitionItem->approved_quantity;

                $issued =
                    (float) $requisitionItem->issued_quantity;

                $outstanding =
                    max(
                        0,
                        $approved - $issued
                    );

                if ($approved <= 0) {
                    throw new RuntimeException(
                        'The item has no approved quantity.'
                    );
                }

                if ($quantity > ($outstanding + 0.000001)) {
                    throw new RuntimeException(
                        'Fulfillment quantity for requisition item ID ' .
                        $requisitionItemId .
                        ' exceeds the outstanding approved quantity of ' .
                        number_format($outstanding, 3) .
                        '.'
                    );
                }
            }

            /*
             * Create the physical fulfillment transaction.
             */
            $transactionNumber =
                $this->generateTransactionNumber();

            $fulfillment = StoreFulfillment::create([
                'store_requisition_id' =>
                    $requisition->id,

                'transaction_number' =>
                    $transactionNumber,

                'transaction_type' =>
                    $requisition->requisition_type,

                'source_store_id' =>
                    $requisition->source_store_id,

                'destination_store_id' =>
                    $requisition->requisition_type === 'TRANSFER'
                        ? $requisition->destination_store_id
                        : null,

                'processed_by' =>
                    $userId,

                'notes' =>
                    $notes,
            ]);

            /*
             * Process each physical line.
             */
            foreach ($items as $line) {

                $requisitionItem =
                    $requisitionItems->get(
                        (int) $line['requisition_item_id']
                    );

                $quantity =
                    (float) $line['quantity'];

                $itemId =
                    (int) $requisitionItem->store_item_id;

                $variantId =
                    $requisitionItem->variant_id
                        ? (int) $requisitionItem->variant_id
                        : null;

                /*
                 * SOURCE STOCK
                 */
                $sourceStock =
                    $this->lockStock(
                        (int) $requisition->source_store_id,
                        $itemId,
                        $variantId
                    );

                $sourceBalance =
                    (float) $sourceStock->quantity;

                if ($sourceBalance < ($quantity - 0.000001)) {
                    throw new RuntimeException(
                        'Insufficient stock for item ID ' .
                        $itemId .
                        '. Available: ' .
                        number_format($sourceBalance, 3) .
                        ', requested: ' .
                        number_format($quantity, 3) .
                        '.'
                    );
                }

                $newSourceBalance =
                    $sourceBalance - $quantity;

                $sourceStock->update([
                    'quantity' =>
                        $newSourceBalance,

                    'last_movement_at' =>
                        now(),
                ]);

                /*
                 * ITEM = consumption/issue.
                 */
                if ($requisition->requisition_type === 'ITEM') {

                    StoreStockMovement::create([
                        'store_id' =>
                            $requisition->source_store_id,

                        'store_item_id' =>
                            $itemId,

                        'variant_id' =>
                            $variantId,

                        'movement_type' =>
                            'ISSUE',

                        'quantity' =>
                            $quantity,

                        'balance_after' =>
                            $newSourceBalance,

                        'reference_type' =>
                            StoreFulfillment::class,

                        'reference_id' =>
                            $fulfillment->id,

                        'created_by' =>
                            $userId,

                        'notes' =>
                            'Stock issued through fulfillment ' .
                            $transactionNumber .
                            ' for requisition ' .
                            $requisition->requisition_number,
                    ]);
                }

                /*
                 * TRANSFER = remove from source and add to destination.
                 */
                else {

                    StoreStockMovement::create([
                        'store_id' =>
                            $requisition->source_store_id,

                        'store_item_id' =>
                            $itemId,

                        'variant_id' =>
                            $variantId,

                        'movement_type' =>
                            'TRANSFER_OUT',

                        'quantity' =>
                            $quantity,

                        'balance_after' =>
                            $newSourceBalance,

                        'reference_type' =>
                            StoreFulfillment::class,

                        'reference_id' =>
                            $fulfillment->id,

                        'created_by' =>
                            $userId,

                        'notes' =>
                            'Transfer out through fulfillment ' .
                            $transactionNumber .
                            ' for requisition ' .
                            $requisition->requisition_number,
                    ]);

                    /*
                     * Destination stock.
                     *
                     * Unlike the source, this stock record may not
                     * exist yet because this may be the first time
                     * the destination store receives this item.
                     */
                    $destinationStock =
                        $this->lockOrCreateStock(
                            (int) $requisition->destination_store_id,
                            $itemId,
                            $variantId
                        );

                    $destinationBalance =
                        (float) $destinationStock->quantity;

                    $newDestinationBalance =
                        $destinationBalance + $quantity;

                    $destinationStock->update([
                        'quantity' =>
                            $newDestinationBalance,

                        'last_movement_at' =>
                            now(),
                    ]);

                    StoreStockMovement::create([
                        'store_id' =>
                            $requisition->destination_store_id,

                        'store_item_id' =>
                            $itemId,

                        'variant_id' =>
                            $variantId,

                        'movement_type' =>
                            'TRANSFER_IN',

                        'quantity' =>
                            $quantity,

                        'balance_after' =>
                            $newDestinationBalance,

                        'reference_type' =>
                            StoreFulfillment::class,

                        'reference_id' =>
                            $fulfillment->id,

                        'created_by' =>
                            $userId,

                        'notes' =>
                            'Transfer in through fulfillment ' .
                            $transactionNumber .
                            ' for requisition ' .
                            $requisition->requisition_number,
                    ]);
                }

                /*
                 * Physical fulfillment record.
                 */
                StoreFulfillmentItem::create([
                    'store_fulfillment_id' =>
                        $fulfillment->id,

                    'store_requisition_item_id' =>
                        $requisitionItem->id,

                    'store_item_id' =>
                        $itemId,

                    'variant_id' =>
                        $variantId,

                    'quantity' =>
                        $quantity,
                ]);

                /*
                 * Update requisition fulfillment quantities.
                 */
                $newIssued =
                    (float) $requisitionItem->issued_quantity +
                    $quantity;

                $approved =
                    (float) $requisitionItem->approved_quantity;

                $newOutstanding =
                    max(
                        0,
                        $approved - $newIssued
                    );

                $requisitionItem->update([
                    'issued_quantity' =>
                        $newIssued,

                    'outstanding_quantity' =>
                        $newOutstanding,
                ]);
            }

            /*
             * Check whether the whole requisition is now fulfilled.
             */
            $remaining = StoreRequisitionItem::query()
    ->where(
        'store_requisition_id',
        $requisition->id
    )
    ->where(
        'outstanding_quantity',
        '>',
        0
    )
    ->exists();

$hasIssued = StoreRequisitionItem::query()
    ->where(
        'store_requisition_id',
        $requisition->id
    )
    ->where(
        'issued_quantity',
        '>',
        0
    )
    ->exists();

if (!$remaining) {

    $requisition->update([
        'fulfillment_status' => 'fully_issued',
        'completed_at' => now(),
    ]);

} elseif ($hasIssued) {

    $requisition->update([
        'fulfillment_status' => 'partially_issued',
    ]);

} else {

    $requisition->update([
        'fulfillment_status' => 'not_issued',
    ]);
}

            return $fulfillment->fresh([
                'requisition',
                'sourceStore',
                'destinationStore',
                'processor',
                'items.item',
                'items.variant',
            ]);
        });
    }

    /**
     * Lock an existing stock row.
     */
    protected function lockStock(
        int $storeId,
        int $itemId,
        ?int $variantId
    ): StoreStock {
        $query = StoreStock::query()
            ->where('store_id', $storeId)
            ->where('store_item_id', $itemId);

        if ($variantId === null) {
            $query->whereNull('variant_id');
        } else {
            $query->where(
                'variant_id',
                $variantId
            );
        }

        $stock = $query
            ->lockForUpdate()
            ->first();

        if (!$stock) {
            throw new RuntimeException(
                'No stock record exists for item ID ' .
                $itemId .
                ' in store ID ' .
                $storeId .
                '.'
            );
        }

        return $stock;
    }

    /**
     * Lock an existing stock row or create it for a destination store.
     */
    protected function lockOrCreateStock(
        int $storeId,
        int $itemId,
        ?int $variantId
    ): StoreStock {
        $query = StoreStock::query()
            ->where('store_id', $storeId)
            ->where('store_item_id', $itemId);

        if ($variantId === null) {
            $query->whereNull('variant_id');
        } else {
            $query->where(
                'variant_id',
                $variantId
            );
        }

        $stock = $query
            ->lockForUpdate()
            ->first();

        if ($stock) {
            return $stock;
        }

        return StoreStock::create([
            'store_id' =>
                $storeId,

            'store_item_id' =>
                $itemId,

            'variant_id' =>
                $variantId,

            'quantity' =>
                0,

            'last_movement_at' =>
                null,
        ]);
    }

    /**
     * Generate a unique fulfillment transaction number.
     */
    protected function generateTransactionNumber(): string
    {
        do {
            $number =
                'FUL-' .
                now()->format('Ymd') .
                '-' .
                strtoupper(
                    substr(
                        bin2hex(random_bytes(3)),
                        0,
                        6
                    )
                );

        } while (
            StoreFulfillment::where(
                'transaction_number',
                $number
            )->exists()
        );

        return $number;
    }
}