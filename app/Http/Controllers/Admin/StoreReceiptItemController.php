<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreItemVariant;
use App\Models\StoreReceipt;
use App\Models\StoreReceiptItem;
use Illuminate\Http\Request;

class StoreReceiptItemController extends Controller
{
    public function store(Request $request, StoreReceipt $storeReceipt)
    {
        $this->ensureDraft($storeReceipt);

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

        /*
         * If a variant was supplied, make absolutely sure
         * that it belongs to the selected store item.
         */
        if (!empty($validated['variant_id'])) {

            $variantBelongsToItem = StoreItemVariant::where(
                'id',
                $validated['variant_id']
            )
                ->where(
                    'store_item_id',
                    $validated['store_item_id']
                )
                ->exists();

            if (!$variantBelongsToItem) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'variant_id' =>
                            'The selected variant does not belong to the selected item.',
                    ]);
            }
        }

        /*
         * Prevent the same item + variant combination
         * from being accidentally added twice.
         */
        $existing = $storeReceipt->items()
            ->where('store_item_id', $validated['store_item_id'])
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

        /*
         * Validate variant → item relationship.
         */
        if (!empty($validated['variant_id'])) {

            $variantBelongsToItem = StoreItemVariant::where(
                'id',
                $validated['variant_id']
            )
                ->where(
                    'store_item_id',
                    $validated['store_item_id']
                )
                ->exists();

            if (!$variantBelongsToItem) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'variant_id' =>
                            'The selected variant does not belong to the selected item.',
                    ]);
            }
        }

        /*
         * Prevent duplicate item + variant combinations
         * when changing an existing line.
         */
        $duplicate = $storeReceipt->items()
            ->where('id', '!=', $storeReceiptItem->id)
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