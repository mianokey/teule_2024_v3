<?php

namespace App\Services;

use App\Models\StoreFulfillment;
use App\Models\StoreFulfillmentItem;
use App\Models\StoreRequisition;
use App\Models\StoreRequisitionItem;
use App\Models\StoreStock;
use App\Models\StoreStockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StoreFulfillmentService
{
    /**
     * Fulfill an approved requisition.
     *
     * IMPORTANT BUSINESS RULE:
     * A requisition can only be physically fulfilled ONCE.
     *
     * If the full approved quantity cannot be issued because the
     * recorded system stock is insufficient, the requisition is still
     * closed after the fulfillment.
     *
     * Any remaining approved quantity is carried forward into a NEW
     * requisition which starts again at HOD approval.
     */
    public function fulfill(
        StoreRequisition $storeRequisition,
        array $items,
        int $userId,
        ?string $notes = null,
        string $mode = 'available'
    ): StoreFulfillment {
        return DB::transaction(function () use (
            $storeRequisition,
            $items,
            $userId,
            $notes,
            $mode
        ) {
            /*
             * ---------------------------------------------------------
             * 1. LOCK THE REQUISITION
             * ---------------------------------------------------------
             */
            $requisition = StoreRequisition::query()
                ->whereKey($storeRequisition->id)
                ->lockForUpdate()
                ->firstOrFail();

            /*
             * ---------------------------------------------------------
             * 2. REQUISITION MUST BE FULLY APPROVED
             * ---------------------------------------------------------
             */
            if (
                strtolower((string) $requisition->status) !== 'approved' ||
                strtolower((string) $requisition->approval_stage) !== 'approved'
            ) {
                throw ValidationException::withMessages([
                    'requisition' =>
                        'This requisition is not fully approved and cannot be fulfilled.',
                ]);
            }

            /*
             * ---------------------------------------------------------
             * 3. ONE-TIME FULFILLMENT RULE
             * ---------------------------------------------------------
             *
             * Once a fulfillment exists, this requisition has already
             * been physically processed and must never be fulfilled again.
             */
            $existingFulfillment = StoreFulfillment::query()
                ->where('store_requisition_id', $requisition->id)
                ->lockForUpdate()
                ->exists();

            if ($existingFulfillment) {
                throw ValidationException::withMessages([
                    'requisition' =>
                        'This requisition has already been physically fulfilled and cannot be fulfilled again. Any remaining balance was carried forward to a new requisition.',
                ]);
            }

            /*
             * ---------------------------------------------------------
             * 4. VALIDATE MODE
             * ---------------------------------------------------------
             */
            if (!in_array($mode, ['available', 'all_or_nothing'], true)) {
                throw ValidationException::withMessages([
                    'mode' => 'Invalid fulfillment mode.',
                ]);
            }

            /*
             * ---------------------------------------------------------
             * 5. VALIDATE REQUISITION TYPE / STORE
             * ---------------------------------------------------------
             */
            $requisitionType = strtoupper(
                (string) ($requisition->requisition_type ?? 'ITEM')
            );

            if (!in_array($requisitionType, ['ITEM', 'TRANSFER'], true)) {
                throw ValidationException::withMessages([
                    'requisition' =>
                        'This requisition has an invalid requisition type.',
                ]);
            }

            if (!$requisition->source_store_id) {
                throw ValidationException::withMessages([
                    'requisition' =>
                        'No source store has been assigned to this requisition.',
                ]);
            }

            if (
                $requisitionType === 'TRANSFER' &&
                !$requisition->destination_store_id
            ) {
                throw ValidationException::withMessages([
                    'requisition' =>
                        'No destination store has been assigned to this transfer requisition.',
                ]);
            }

            /*
             * ---------------------------------------------------------
             * 6. NORMALISE REQUESTED LINES
             * ---------------------------------------------------------
             */
            $requestedLines = [];

            foreach ($items as $line) {
                $requisitionItemId = (int) (
                    $line['requisition_item_id'] ?? 0
                );

                $quantity = (float) ($line['quantity'] ?? 0);

                if ($requisitionItemId <= 0) {
                    continue;
                }

                if ($quantity <= 0) {
                    continue;
                }

                /*
                 * If the same requisition item appears more than once,
                 * combine the quantities rather than processing it twice.
                 */
                if (!isset($requestedLines[$requisitionItemId])) {
                    $requestedLines[$requisitionItemId] = 0;
                }

                $requestedLines[$requisitionItemId] += $quantity;
            }

            if (empty($requestedLines)) {
                throw ValidationException::withMessages([
                    'items' =>
                        'Please select at least one item to fulfill.',
                ]);
            }

            /*
             * ---------------------------------------------------------
             * 7. LOCK THE REQUISITION ITEMS
             * ---------------------------------------------------------
             */
            $requisitionItems = StoreRequisitionItem::query()
                ->where('store_requisition_id', $requisition->id)
                ->whereIn('id', array_keys($requestedLines))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if (
                $requisitionItems->count() !== count($requestedLines)
            ) {
                throw ValidationException::withMessages([
                    'items' =>
                        'One or more selected requisition items could not be found on this requisition.',
                ]);
            }

            /*
             * ---------------------------------------------------------
             * 8. PREPARE FULFILLMENT LINES
             * ---------------------------------------------------------
             */
            $fulfillmentLines = [];

            foreach (
                $requestedLines as $requisitionItemId => $requestedQuantity
            ) {
                /** @var StoreRequisitionItem $requisitionItem */
                $requisitionItem = $requisitionItems->get(
                    $requisitionItemId
                );

                /*
                 * Approved quantity is the maximum quantity that the
                 * requisition authorises Stores to issue.
                 */
                $approvedQuantity = (float) (
                    $requisitionItem->approved_quantity ?? 0
                );

                /*
                 * Use issued_quantity as the authoritative historical
                 * amount already issued.
                 */
                $issuedQuantity = (float) (
                    $requisitionItem->issued_quantity ?? 0
                );

                /*
                 * Calculate outstanding from approved - issued rather
                 * than trusting a potentially stale stored balance.
                 */
                $outstandingQuantity = max(
                    0,
                    $approvedQuantity - $issuedQuantity
                );

                if ($outstandingQuantity <= 0) {
                    throw ValidationException::withMessages([
                        'items' =>
                            'The selected item has no outstanding approved quantity.',
                    ]);
                }

                /*
                 * Never allow Stores to issue more than the remaining
                 * approved quantity.
                 */
                if ($requestedQuantity > $outstandingQuantity) {
                    throw ValidationException::withMessages([
                        'items' =>
                            'The quantity requested for item #' .
                            $requisitionItem->id .
                            ' exceeds the remaining approved quantity of ' .
                            $this->formatQuantity($outstandingQuantity) .
                            '.',
                    ]);
                }

                /*
                 * -----------------------------------------------------
                 * LOCK SYSTEM STOCK
                 * -----------------------------------------------------
                 *
                 * We use the SYSTEM stock balance here.
                 *
                 * Physical stock discrepancies are handled separately
                 * through stock adjustment.
                 */
                $stock = $this->lockStock(
                    $requisition->source_store_id,
                    $requisitionItem->store_item_id,
                    $requisitionItem->variant_id
                );

                $availableQuantity = (float) (
                    $stock->quantity ?? 0
                );

                /*
                 * ALL OR NOTHING:
                 *
                 * The selected line must have enough recorded system
                 * stock to issue the requested amount.
                 */
                if (
                    $mode === 'all_or_nothing' &&
                    $availableQuantity < $requestedQuantity
                ) {
                    throw ValidationException::withMessages([
                        'items' =>
                            'Insufficient recorded system stock for item #' .
                            $requisitionItem->id .
                            '. Available: ' .
                            $this->formatQuantity($availableQuantity) .
                            ', requested: ' .
                            $this->formatQuantity($requestedQuantity) .
                            '.',
                    ]);
                }

                /*
                 * AVAILABLE MODE:
                 *
                 * Issue whatever is actually available in the system,
                 * up to the requested amount.
                 */
                $actualQuantity = min(
                    $requestedQuantity,
                    $availableQuantity,
                    $outstandingQuantity
                );

                /*
                 * If there is no stock for this particular line, simply
                 * do not create a fulfillment line for it.
                 *
                 * Another selected line may still be fulfilled.
                 */
                if ($actualQuantity <= 0) {
                    continue;
                }

                $fulfillmentLines[] = [
                    'requisition_item' => $requisitionItem,
                    'stock' => $stock,
                    'requested_quantity' => $requestedQuantity,
                    'available_quantity' => $availableQuantity,
                    'actual_quantity' => $actualQuantity,
                ];
            }

            /*
             * ---------------------------------------------------------
             * 9. NOTHING CAN BE ISSUED
             * ---------------------------------------------------------
             */
            if (empty($fulfillmentLines)) {
                throw ValidationException::withMessages([
                    'items' =>
                        'No stock is currently available for the selected items. No fulfillment was processed.',
                ]);
            }

            /*
             * ---------------------------------------------------------
             * 10. CREATE FULFILLMENT HEADER
             * ---------------------------------------------------------
             */
            $fulfillment = StoreFulfillment::create([
                'store_requisition_id' => $requisition->id,
                'transaction_number' => $this->generateTransactionNumber(),
                'transaction_type' =>
                    $requisitionType === 'TRANSFER'
                        ? 'TRANSFER'
                        : 'ISSUE',
                'source_store_id' => $requisition->source_store_id,
                'destination_store_id' =>
                    $requisition->destination_store_id,
                'processed_by' => $userId,
                'notes' => $notes,
            ]);

            /*
             * ---------------------------------------------------------
             * 11. PROCESS EACH PHYSICAL ISSUE
             * ---------------------------------------------------------
             */
            foreach ($fulfillmentLines as $line) {
                /** @var StoreRequisitionItem $requisitionItem */
                $requisitionItem = $line['requisition_item'];

                /** @var StoreStock $stock */
                $stock = $line['stock'];

                $actualQuantity = (float) $line['actual_quantity'];

                /*
                 * Re-lock stock immediately before modifying it.
                 *
                 * This protects against another stock transaction being
                 * processed concurrently.
                 */
                $stock = $this->lockStock(
                    $requisition->source_store_id,
                    $requisitionItem->store_item_id,
                    $requisitionItem->variant_id
                );

                $currentStock = (float) ($stock->quantity ?? 0);

                /*
                 * Stock could have changed between preparation and
                 * processing. Never issue more than the current locked
                 * balance.
                 */
                if ($actualQuantity > $currentStock) {
                    if ($mode === 'all_or_nothing') {
                        throw ValidationException::withMessages([
                            'items' =>
                                'System stock changed while processing the fulfillment. Please refresh and try again.',
                        ]);
                    }

                    $actualQuantity = $currentStock;
                }

                if ($actualQuantity <= 0) {
                    continue;
                }

                /*
                 * -----------------------------------------------------
                 * DECREASE SOURCE STOCK
                 * -----------------------------------------------------
                 */
                $newStockBalance =
                    $currentStock - $actualQuantity;

                /*
                 * Avoid tiny floating point residue.
                 */
                if (abs($newStockBalance) < 0.000001) {
                    $newStockBalance = 0;
                }

                $stock->quantity = $newStockBalance;
                $stock->last_movement_at = now();
                $stock->save();

                /*
                 * -----------------------------------------------------
                 * CREATE SOURCE STOCK MOVEMENT
                 * -----------------------------------------------------
                 */
                if ($requisitionType === 'TRANSFER') {
                    $sourceMovementType = 'TRANSFER_OUT';
                } else {
                    $sourceMovementType = 'ISSUE';
                }

                StoreStockMovement::create([
                    'store_id' => $requisition->source_store_id,
                    'store_item_id' => $requisitionItem->store_item_id,
                    'variant_id' => $requisitionItem->variant_id,
                    'movement_type' => $sourceMovementType,
                    'quantity' => $actualQuantity,
                    'balance_after' => $newStockBalance,
                    'reference_type' => StoreFulfillment::class,
                    'reference_id' => $fulfillment->id,
                    'created_by' => $userId,
                    'notes' =>
                        'Stock issued against requisition ' .
                        $requisition->requisition_number .
                        ' / fulfillment ' .
                        $fulfillment->transaction_number,
                ]);

                /*
                 * -----------------------------------------------------
                 * TRANSFER DESTINATION STOCK
                 * -----------------------------------------------------
                 */
                if ($requisitionType === 'TRANSFER') {
                    $destinationStock = $this->lockOrCreateStock(
                        $requisition->destination_store_id,
                        $requisitionItem->store_item_id,
                        $requisitionItem->variant_id
                    );

                    $destinationCurrent =
                        (float) ($destinationStock->quantity ?? 0);

                    $destinationNew =
                        $destinationCurrent + $actualQuantity;

                    $destinationStock->quantity =
                        $destinationNew;

                    $destinationStock->last_movement_at = now();
                    $destinationStock->save();

                    StoreStockMovement::create([
                        'store_id' =>
                            $requisition->destination_store_id,
                        'store_item_id' =>
                            $requisitionItem->store_item_id,
                        'variant_id' =>
                            $requisitionItem->variant_id,
                        'movement_type' => 'TRANSFER_IN',
                        'quantity' => $actualQuantity,
                        'balance_after' => $destinationNew,
                        'reference_type' =>
                            StoreFulfillment::class,
                        'reference_id' => $fulfillment->id,
                        'created_by' => $userId,
                        'notes' =>
                            'Stock received from requisition ' .
                            $requisition->requisition_number .
                            ' / fulfillment ' .
                            $fulfillment->transaction_number,
                    ]);
                }

                /*
                 * -----------------------------------------------------
                 * CREATE FULFILLMENT ITEM
                 * -----------------------------------------------------
                 */
                StoreFulfillmentItem::create([
                    'store_fulfillment_id' => $fulfillment->id,
                    'store_requisition_item_id' =>
                        $requisitionItem->id,
                    'store_item_id' =>
                        $requisitionItem->store_item_id,
                    'variant_id' =>
                        $requisitionItem->variant_id,
                    'quantity' => $actualQuantity,
                ]);

                /*
                 * -----------------------------------------------------
                 * UPDATE REQUISITION ITEM
                 * -----------------------------------------------------
                 *
                 * Use direct assignment + save instead of mass update.
                 * This guarantees the values are persisted even if the
                 * model's $fillable list is restrictive.
                 */
                $approvedQuantity = (float) (
                    $requisitionItem->approved_quantity ?? 0
                );

                $oldIssuedQuantity = (float) (
                    $requisitionItem->issued_quantity ?? 0
                );

                $newIssuedQuantity =
                    $oldIssuedQuantity + $actualQuantity;

                /*
                 * Never allow issued quantity to exceed approved quantity.
                 */
                if ($newIssuedQuantity > $approvedQuantity) {
                    $newIssuedQuantity = $approvedQuantity;
                }

                $newOutstandingQuantity = max(
                    0,
                    $approvedQuantity - $newIssuedQuantity
                );

                $requisitionItem->issued_quantity =
                    $newIssuedQuantity;

                $requisitionItem->outstanding_quantity =
                    $newOutstandingQuantity;

                $requisitionItem->save();

                /*
                 * Refresh to ensure the model reflects what is actually
                 * stored in the database.
                 */
                $requisitionItem->refresh();
            }

            /*
             * ---------------------------------------------------------
             * 12. MAKE SURE SOMETHING WAS ACTUALLY ISSUED
             * ---------------------------------------------------------
             */
            $fulfillmentItemCount = StoreFulfillmentItem::query()
                ->where('store_fulfillment_id', $fulfillment->id)
                ->count();

            if ($fulfillmentItemCount === 0) {
                throw ValidationException::withMessages([
                    'items' =>
                        'No stock was issued. The fulfillment was not completed.',
                ]);
            }

            /*
             * ---------------------------------------------------------
             * 13. RELOAD ALL REQUISITION ITEMS
             * ---------------------------------------------------------
             *
             * Do NOT use only the selected items here.
             *
             * The requisition must be evaluated as a whole when deciding
             * whether a balance needs to be carried forward.
             */
            $allRequisitionItems = StoreRequisitionItem::query()
                ->where('store_requisition_id', $requisition->id)
                ->lockForUpdate()
                ->get();

            $hasOutstandingBalance = false;

            foreach ($allRequisitionItems as $requisitionItem) {
                $approvedQuantity = (float) (
                    $requisitionItem->approved_quantity ?? 0
                );

                $issuedQuantity = (float) (
                    $requisitionItem->issued_quantity ?? 0
                );

                $outstandingQuantity = max(
                    0,
                    $approvedQuantity - $issuedQuantity
                );

                /*
                 * Keep the stored balance synchronized.
                 */
                if (
                    (float) $requisitionItem->outstanding_quantity
                    !== $outstandingQuantity
                ) {
                    $requisitionItem->outstanding_quantity =
                        $outstandingQuantity;

                    $requisitionItem->save();
                }

                if ($outstandingQuantity > 0) {
                    $hasOutstandingBalance = true;
                }
            }

            /*
             * ---------------------------------------------------------
             * 14. CLOSE THE ORIGINAL REQUISITION
             * ---------------------------------------------------------
             *
             * THIS HAPPENS AFTER THE FIRST SUCCESSFUL PHYSICAL ISSUE.
             *
             * The original requisition can NEVER be used again.
             */
            $requisition->status = 'closed';

            $requisition->fulfillment_status =
                $hasOutstandingBalance
                    ? 'closed_with_balance'
                    : 'fully_issued';

            $requisition->completed_at = now();
            $requisition->save();

            /*
             * ---------------------------------------------------------
             * 15. CREATE NEW REQUISITION FOR ANY BALANCE
             * ---------------------------------------------------------
             */
            if ($hasOutstandingBalance) {
                $balanceRequisition =
                    $this->createBalanceRequisition(
                        $requisition,
                        $allRequisitionItems,
                        $userId
                    );

                /*
                 * Add the new requisition reference to the fulfillment
                 * notes so the audit trail clearly shows where the
                 * remaining quantity went.
                 */
                $existingNotes = trim(
                    (string) $fulfillment->notes
                );

                $carryForwardNote =
                    'Outstanding balance carried forward to new requisition ' .
                    $balanceRequisition->requisition_number .
                    '.';

                $fulfillment->notes =
                    $existingNotes !== ''
                        ? $existingNotes . "\n" . $carryForwardNote
                        : $carryForwardNote;

                $fulfillment->save();
            }

            /*
             * ---------------------------------------------------------
             * 16. RETURN COMPLETE FULFILLMENT
             * ---------------------------------------------------------
             */
            return $fulfillment->fresh([
                'requisition',
                'sourceStore',
                'destinationStore',
                'processor',
                'items.requisitionItem',
                'items.storeItem',
                'items.variant',
            ]);
        });
    }

    /**
     * Cancel an approved requisition before any fulfillment has occurred.
     */
    public function cancel(
        StoreRequisition $storeRequisition,
        int $userId,
        ?string $reason = null
    ): StoreRequisition {
        return DB::transaction(function () use (
            $storeRequisition,
            $userId,
            $reason
        ) {
            $requisition = StoreRequisition::query()
                ->whereKey($storeRequisition->id)
                ->lockForUpdate()
                ->firstOrFail();

            if (
                strtolower((string) $requisition->status) !== 'approved' ||
                strtolower((string) $requisition->approval_stage) !== 'approved'
            ) {
                throw ValidationException::withMessages([
                    'requisition' =>
                        'Only fully approved requisitions can be cancelled.',
                ]);
            }

            /*
             * A requisition with a fulfillment can never be cancelled.
             */
            $hasFulfillment = StoreFulfillment::query()
                ->where('store_requisition_id', $requisition->id)
                ->exists();

            if ($hasFulfillment) {
                throw ValidationException::withMessages([
                    'requisition' =>
                        'This requisition has already been physically fulfilled and cannot be cancelled.',
                ]);
            }

            $requisition->status = 'cancelled';
            $requisition->fulfillment_status = 'cancelled';
            $requisition->completed_at = now();

            $existingNotes = trim(
                (string) $requisition->submission_notes
            );

            $cancelNote =
                'Cancelled by user #' .
                $userId .
                ($reason
                    ? '. Reason: ' . trim($reason)
                    : '.');

            $requisition->submission_notes =
                $existingNotes !== ''
                    ? $existingNotes . "\n" . $cancelNote
                    : $cancelNote;

            $requisition->save();

            return $requisition->fresh([
                'items',
                'requester',
                'sourceStore',
                'destinationStore',
            ]);
        });
    }

    /**
     * Create a brand-new requisition containing the unfulfilled balance.
     *
     * The new requisition starts from HOD again.
     */
    protected function createBalanceRequisition(
        StoreRequisition $originalRequisition,
        $originalItems,
        int $userId
    ): StoreRequisition {
        $newRequisition = StoreRequisition::create([
            'requisition_number' =>
                $this->generateRequisitionNumber(),

            'requisition_type' =>
                $originalRequisition->requisition_type,

            'source_store_id' =>
                $originalRequisition->source_store_id,

            'destination_store_id' =>
                $originalRequisition->destination_store_id,

            'requested_by' =>
                $originalRequisition->requested_by,

            'child_id' =>
                $originalRequisition->child_id,

            'department' =>
                $originalRequisition->department,

            'purpose' =>
                $originalRequisition->purpose,

            /*
             * New requisition must enter the normal approval workflow.
             */
            'status' => 'pending',

            'approval_stage' => 'hod',

            /*
             * IMPORTANT:
             * The database does not allow fulfillment_status to be NULL.
             *
             * This is a NEW request which has not been issued yet.
             */
            'fulfillment_status' => 'not_issued',

            'submission_notes' =>
                'Automatically created from requisition ' .
                $originalRequisition->requisition_number .
                ' to carry forward quantities not issued during fulfillment.',

            'submitted_at' => now(),

            'approved_at' => null,

            'completed_at' => null,
        ]);

        foreach ($originalItems as $originalItem) {
            $approvedQuantity = (float) (
                $originalItem->approved_quantity ?? 0
            );

            $issuedQuantity = (float) (
                $originalItem->issued_quantity ?? 0
            );

            $balance = max(
                0,
                $approvedQuantity - $issuedQuantity
            );

            if ($balance <= 0) {
                continue;
            }

            StoreRequisitionItem::create([
                'store_requisition_id' =>
                    $newRequisition->id,

                'store_item_id' =>
                    $originalItem->store_item_id,

                'variant_id' =>
                    $originalItem->variant_id,

                /*
                 * The remaining quantity becomes a NEW REQUEST.
                 */
                'requested_quantity' => $balance,

                /*
                 * It has NOT yet been approved.
                 */
                'approved_quantity' => 0,

                'issued_quantity' => 0,

                'outstanding_quantity' => 0,

                'notes' =>
                    'Balance carried forward from requisition ' .
                    $originalRequisition->requisition_number .
                    '. Requires normal approval.',
            ]);
        }

        return $newRequisition->fresh([
            'items',
            'requester',
            'sourceStore',
            'destinationStore',
        ]);
    }

    /**
     * Lock existing stock.
     *
     * Physical stock discrepancies are NOT corrected here.
     * They must be handled through the stock adjustment workflow.
     */
    protected function lockStock(
        int $storeId,
        int $storeItemId,
        $variantId = null
    ): StoreStock {
        $query = StoreStock::query()
            ->where('store_id', $storeId)
            ->where('store_item_id', $storeItemId);

        if ($variantId === null) {
            $query->whereNull('variant_id');
        } else {
            $query->where('variant_id', $variantId);
        }

        $stock = $query
            ->lockForUpdate()
            ->first();

        if (!$stock) {
            throw ValidationException::withMessages([
                'stock' =>
                    'No stock record exists for the selected item in the source store.',
            ]);
        }

        return $stock;
    }

    /**
     * Lock destination stock or create it if it does not exist.
     */
    protected function lockOrCreateStock(
        int $storeId,
        int $storeItemId,
        $variantId = null
    ): StoreStock {
        $query = StoreStock::query()
            ->where('store_id', $storeId)
            ->where('store_item_id', $storeItemId);

        if ($variantId === null) {
            $query->whereNull('variant_id');
        } else {
            $query->where('variant_id', $variantId);
        }

        $stock = $query
            ->lockForUpdate()
            ->first();

        if ($stock) {
            return $stock;
        }

        return StoreStock::create([
            'store_id' => $storeId,
            'store_item_id' => $storeItemId,
            'variant_id' => $variantId,
            'quantity' => 0,
            'last_movement_at' => now(),
        ]);
    }

    /**
     * Generate fulfillment transaction number.
     *
     * Format:
     * FUL-YYYYMMDD-XXXXXX
     */
    protected function generateTransactionNumber(): string
    {
        do {
            $number =
                'FUL-' .
                now()->format('Ymd') .
                '-' .
                strtoupper(
                    substr(bin2hex(random_bytes(3)), 0, 6)
                );

            $exists = StoreFulfillment::query()
                ->where('transaction_number', $number)
                ->exists();

        } while ($exists);

        return $number;
    }

    /**
     * Generate requisition number.
     *
     * Format:
     * REQ-YYYYMMDD-XXXXXX
     */
    protected function generateRequisitionNumber(): string
    {
        do {
            $number =
                'REQ-' .
                now()->format('Ymd') .
                '-' .
                strtoupper(
                    substr(bin2hex(random_bytes(3)), 0, 6)
                );

            $exists = StoreRequisition::query()
                ->where('requisition_number', $number)
                ->exists();

        } while ($exists);

        return $number;
    }

    /**
     * Format quantities without unnecessary trailing zeroes.
     */
    protected function formatQuantity(float $quantity): string
    {
        if (abs($quantity - round($quantity)) < 0.000001) {
            return (string) (int) round($quantity);
        }

        return rtrim(
            rtrim(
                number_format($quantity, 3, '.', ''),
                '0'
            ),
            '.'
        );
    }
}

