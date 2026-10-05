<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreItemVariant;
use App\Models\StoreLpoItem;
use App\Models\StoreReceipt;
use App\Models\StoreReceiptItem;
use Illuminate\Http\Request;

class StoreReceiptItemController extends Controller
{
    public function store(
        Request $request,
        StoreReceipt $storeReceipt
    ) {
        $this->ensureDraft($storeReceipt);

        /*
        |--------------------------------------------------------------------------
        | LPO RECEIPT
        |--------------------------------------------------------------------------
        */

        if ($storeReceipt->store_lpo_id) {

            $validated = $request->validate([
                'store_lpo_item_id' => [
                    'required',
                    'integer',
                    'exists:store_lpo_items,id',
                ],

                'quantity' => [
                    'required',
                    'numeric',
                    'gt:0',
                ],

                'notes' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],
            ]);

            $lpoItem = StoreLpoItem::with([
                'lpo',
                'item',
                'variant',
            ])->find($validated['store_lpo_item_id']);

            if (!$lpoItem) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'store_lpo_item_id' =>
                            'The selected LPO item could not be found.',
                    ]);
            }

            /*
             * Item must belong to this receipt's LPO.
             */
            if (
                (int) $lpoItem->store_lpo_id !==
                (int) $storeReceipt->store_lpo_id
            ) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'store_lpo_item_id' =>
                            'The selected item does not belong to this receipt LPO.',
                    ]);
            }

            /*
             * Calculate the quantity already received.
             */
            $alreadyReceived = StoreReceiptItem::where(
                'store_lpo_item_id',
                $lpoItem->id
            )->sum('quantity');

            $remaining = max(
                0,
                (float) $lpoItem->ordered_quantity -
                (float) $alreadyReceived
            );

            if ($remaining <= 0) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'quantity' =>
                            'This LPO item has already been fully received.',
                    ]);
            }

            if ((float) $validated['quantity'] > $remaining) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'quantity' =>
                            'You can receive a maximum of ' .
                            rtrim(rtrim(number_format($remaining, 3), '0'), '.') .
                            ' for this LPO item.',
                    ]);
            }

            /*
             * Prevent the same LPO line from being added twice
             * to the same draft receipt.
             */
            $existing = $storeReceipt->items()
                ->where(
                    'store_lpo_item_id',
                    $lpoItem->id
                )
                ->first();

            if ($existing) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'store_lpo_item_id' =>
                            'This LPO item is already on this receipt. Edit the existing line instead.',
                    ]);
            }

            $storeReceipt->items()->create([
                'store_lpo_item_id' => $lpoItem->id,
                'store_item_id' => $lpoItem->store_item_id,
                'variant_id' => $lpoItem->variant_id,
                'quantity' => $validated['quantity'],
                'notes' => $validated['notes'] ?? null,
            ]);

            return redirect()
                ->route(
                    'admin.store-receipts.show',
                    $storeReceipt
                )
                ->with(
                    'success',
                    'LPO item added to the receipt successfully.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | LEGACY / DONATION RECEIPT
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'store_item_id' => [
                'required',
                'integer',
                'exists:store_items,id',
            ],

            'variant_id' => [
                'nullable',
                'integer',
                'exists:store_item_variants,id',
            ],

            'quantity' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $this->validateVariant(
            $validated['store_item_id'],
            $validated['variant_id'] ?? null
        );

        $existing = $storeReceipt->items()
            ->where(
                'store_item_id',
                $validated['store_item_id']
            )
            ->when(
                array_key_exists('variant_id', $validated)
                    && $validated['variant_id'] !== null,
                function ($query) use ($validated) {
                    $query->where(
                        'variant_id',
                        $validated['variant_id']
                    );
                },
                function ($query) {
                    $query->whereNull('variant_id');
                }
            )
            ->first();

        if ($existing) {
            return back()
                ->withInput()
                ->withErrors([
                    'store_item_id' =>
                        'This item and variant combination is already on the receipt. Edit the existing line instead of adding it again.',
                ]);
        }

        $storeReceipt->items()->create([
            'store_item_id' => $validated['store_item_id'],
            'variant_id' => $validated['variant_id'] ?? null,
            'quantity' => $validated['quantity'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route(
                'admin.store-receipts.show',
                $storeReceipt
            )
            ->with(
                'success',
                'Received item added successfully.'
            );
    }

    public function update(
        Request $request,
        StoreReceipt $storeReceipt,
        StoreReceiptItem $storeReceiptItem
    ) {
        $this->ensureDraft($storeReceipt);

        $this->ensureItemBelongsToReceipt(
            $storeReceipt,
            $storeReceiptItem
        );

        /*
        |--------------------------------------------------------------------------
        | LPO RECEIPT
        |--------------------------------------------------------------------------
        */

        if ($storeReceipt->store_lpo_id) {

            $validated = $request->validate([
                'quantity' => [
                    'required',
                    'numeric',
                    'gt:0',
                ],

                'notes' => [
                    'nullable',
                    'string',
                    'max:2000',
                ],
            ]);

            if (!$storeReceiptItem->store_lpo_item_id) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'quantity' =>
                            'This receipt line is not linked to an LPO item.',
                    ]);
            }

            $lpoItem = StoreLpoItem::find(
                $storeReceiptItem->store_lpo_item_id
            );

            if (!$lpoItem) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'quantity' =>
                            'The linked LPO item could not be found.',
                    ]);
            }

            /*
             * Exclude the current receipt line from the
             * already-received calculation.
             */
            $alreadyReceived = StoreReceiptItem::where(
                'store_lpo_item_id',
                $lpoItem->id
            )
                ->where(
                    'id',
                    '!=',
                    $storeReceiptItem->id
                )
                ->sum('quantity');

            $remaining = max(
                0,
                (float) $lpoItem->ordered_quantity -
                (float) $alreadyReceived
            );

            if ((float) $validated['quantity'] > $remaining) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'quantity' =>
                            'You can receive a maximum of ' .
                            rtrim(rtrim(number_format($remaining, 3), '0'), '.') .
                            ' for this LPO item.',
                    ]);
            }

            $storeReceiptItem->update([
                'quantity' => $validated['quantity'],
                'notes' => $validated['notes'] ?? null,
            ]);

            return redirect()
                ->route(
                    'admin.store-receipts.show',
                    $storeReceipt
                )
                ->with(
                    'success',
                    'Received quantity updated successfully.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | LEGACY / DONATION RECEIPT
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'store_item_id' => [
                'required',
                'integer',
                'exists:store_items,id',
            ],

            'variant_id' => [
                'nullable',
                'integer',
                'exists:store_item_variants,id',
            ],

            'quantity' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $this->validateVariant(
            $validated['store_item_id'],
            $validated['variant_id'] ?? null
        );

        $duplicate = $storeReceipt->items()
            ->where(
                'id',
                '!=',
                $storeReceiptItem->id
            )
            ->where(
                'store_item_id',
                $validated['store_item_id']
            )
            ->when(
                array_key_exists('variant_id', $validated)
                    && $validated['variant_id'] !== null,
                function ($query) use ($validated) {
                    $query->where(
                        'variant_id',
                        $validated['variant_id']
                    );
                },
                function ($query) {
                    $query->whereNull('variant_id');
                }
            )
            ->exists();

        if ($duplicate) {
            return back()
                ->withInput()
                ->withErrors([
                    'store_item_id' =>
                        'This item and variant combination is already on the receipt.',
                ]);
        }

        $storeReceiptItem->update([
            'store_item_id' => $validated['store_item_id'],
            'variant_id' => $validated['variant_id'] ?? null,
            'quantity' => $validated['quantity'],
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route(
                'admin.store-receipts.show',
                $storeReceipt
            )
            ->with(
                'success',
                'Received item updated successfully.'
            );
    }

    public function destroy(
        StoreReceipt $storeReceipt,
        StoreReceiptItem $storeReceiptItem
    ) {
        $this->ensureDraft($storeReceipt);

        $this->ensureItemBelongsToReceipt(
            $storeReceipt,
            $storeReceiptItem
        );

        $storeReceiptItem->delete();

        return redirect()
            ->route(
                'admin.store-receipts.show',
                $storeReceipt
            )
            ->with(
                'success',
                'Received item removed successfully.'
            );
    }

    private function validateVariant(
        int $storeItemId,
        ?int $variantId
    ): void {
        if (!$variantId) {
            return;
        }

        $variantBelongsToItem = StoreItemVariant::where(
            'id',
            $variantId
        )
            ->where(
                'store_item_id',
                $storeItemId
            )
            ->exists();

        abort_unless(
            $variantBelongsToItem,
            422,
            'The selected variant does not belong to the selected item.'
        );
    }

    private function ensureDraft(StoreReceipt $storeReceipt): void
    {
        abort_unless(
            $storeReceipt->status === 'DRAFT',
            403,
            'Only draft store receipts can be modified.'
        );
    }

    private function ensureItemBelongsToReceipt(
        StoreReceipt $storeReceipt,
        StoreReceiptItem $storeReceiptItem
    ): void {
        abort_unless(
            $storeReceiptItem->store_receipt_id === $storeReceipt->id,
            404
        );
    }
}

