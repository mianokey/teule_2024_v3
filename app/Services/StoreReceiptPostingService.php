<?php

namespace App\Services;

use App\Models\StoreReceipt;
use App\Models\StoreStock;
use App\Models\StoreStockMovement;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StoreReceiptPostingService
{
    public function post(StoreReceipt $storeReceipt): StoreReceipt
    {
        return DB::transaction(function () use ($storeReceipt) {

            /*
             * Lock the receipt itself.
             * This prevents two users from posting the same receipt
             * simultaneously.
             */
            $storeReceipt = StoreReceipt::query()
                ->lockForUpdate()
                ->with([
                    'items.item',
                    'items.variant',
                ])
                ->findOrFail($storeReceipt->id);


            /*
             * A receipt can only be posted once.
             */
            if ($storeReceipt->status !== 'DRAFT') {
                throw new RuntimeException(
                    'Only draft store receipts can be posted.'
                );
            }


            /*
             * A receipt without items cannot be posted.
             */
            if ($storeReceipt->items->isEmpty()) {
                throw new RuntimeException(
                    'The receipt cannot be posted because no items have been added.'
                );
            }


            /*
             * Process every received item.
             */
            foreach ($storeReceipt->items as $receiptItem) {

                $storeId = $storeReceipt->store_id;
                $itemId = $receiptItem->store_item_id;
                $variantId = $receiptItem->variant_id;

                $quantity = (float) $receiptItem->quantity;


                if ($quantity <= 0) {
                    throw new RuntimeException(
                        'Receipt item quantity must be greater than zero.'
                    );
                }


                /*
                 * The store row is locked before checking/creating
                 * the stock row. This also protects the case where
                 * the stock row does not yet exist.
                 */
                DB::table('stores')
                    ->where('id', $storeId)
                    ->lockForUpdate()
                    ->first();


                /*
                 * Find the stock record for:
                 *
                 * Store + Item + Variant
                 *
                 * NULL variant means the generic item itself.
                 */
                $stockQuery = StoreStock::query()
                    ->where('store_id', $storeId)
                    ->where('store_item_id', $itemId);

                if ($variantId === null) {

                    $stockQuery->whereNull('variant_id');

                } else {

                    $stockQuery->where(
                        'variant_id',
                        $variantId
                    );

                }


                /*
                 * Lock the existing stock row if it exists.
                 */
                $stock = $stockQuery
                    ->lockForUpdate()
                    ->first();


                /*
                 * Create the stock row if this item/variant
                 * has never been received into this store.
                 */
                if (!$stock) {

                    $stock = StoreStock::create([
                        'store_id' => $storeId,
                        'store_item_id' => $itemId,
                        'variant_id' => $variantId,
                        'quantity' => 0,
                        'last_movement_at' => null,
                    ]);

                }


                /*
                 * Calculate the new balance.
                 */
                $newBalance =
                    (float) $stock->quantity +
                    $quantity;


                /*
                 * Update current stock.
                 */
                $stock->update([
                    'quantity' => $newBalance,
                    'last_movement_at' => now(),
                ]);


                /*
                 * Create the stock ledger entry.
                 */
                StoreStockMovement::create([
                    'store_id' => $storeId,
                    'store_item_id' => $itemId,
                    'variant_id' => $variantId,
                    'movement_type' => 'RECEIPT',
                    'quantity' => $quantity,
                    'balance_after' => $newBalance,
                    'reference_type' => StoreReceipt::class,
                    'reference_id' => $storeReceipt->id,
                    'created_by' => auth()->id(),
                    'notes' => 'Stock received through receipt ' .
                        $storeReceipt->receipt_number,
                ]);

            }


            /*
             * Only after every item has successfully updated,
             * mark the receipt as POSTED.
             */
            $storeReceipt->update([
                'status' => 'POSTED',
            ]);


            return $storeReceipt->fresh([
                'store',
                'receivedBy',
                'items.item',
                'items.variant',
            ]);

        });
    }
}