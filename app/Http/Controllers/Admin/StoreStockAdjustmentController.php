<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\StoreItem;
use App\Models\StoreStock;
use App\Models\StoreStockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class StoreStockAdjustmentController extends Controller
{
    /**
     * Show stock adjustment form.
     */
    public function create()
    {
        abort_unless(
            auth()->user()->can('ADJUST STORE STOCK'),
            403
        );

        /*
        |--------------------------------------------------------------------------
        | ACTIVE STORE ITEMS
        |--------------------------------------------------------------------------
        |
        | Load variants together with each item.
        | JavaScript will use this data to populate the variant
        | dropdown ONLY after an item has been selected.
        |
        */

        $storeItems = StoreItem::query()
            ->where('is_active', true)
            ->with([
                'unit',
                'variants' => function ($query) {
                    $query
                        ->where('is_active', true)
                        ->orderBy('name');
                },
            ])
            ->orderBy('name')
            ->get();

        /*
        |--------------------------------------------------------------------------
        | ACTIVE STORES
        |--------------------------------------------------------------------------
        */

        $stores = Store::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'admin.stores.items.stock-adjustment',
            compact(
                'storeItems',
                'stores'
            )
        );
    }


    /**
     * Apply stock adjustment.
     */
    public function store(Request $request)
    {
        abort_unless(
            auth()->user()->can('ADJUST STORE STOCK'),
            403
        );

        /*
        |--------------------------------------------------------------------------
        | BASIC VALIDATION
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'store_item_id' => [
                'required',
                'integer',
                'exists:store_items,id',
            ],

            'store_id' => [
                'required',
                'integer',
                'exists:stores,id',
            ],

            'variant_id' => [
                'nullable',
                'integer',
                'exists:store_item_variants,id',
            ],

            'variation' => [
                'required',
                'numeric',
            ],

            'reason' => [
                'required',
                'string',
                'max:255',
            ],

            'notes' => [
                'required',
                'string',
                'max:2000',
            ],
        ], [
            'store_item_id.required' =>
                'Please select a store item.',

            'store_id.required' =>
                'Please select a store.',

            'variant_id.exists' =>
                'The selected variant is invalid.',

            'variation.required' =>
                'Please enter the stock variation.',

            'variation.numeric' =>
                'The stock variation must be a number.',

            'reason.required' =>
                'Please select a reason for the stock variation.',

            'notes.required' =>
                'Please provide an explanation for the stock variation.',
        ]);


        $variation =
            (float) $validated['variation'];


        /*
        |--------------------------------------------------------------------------
        | ZERO ADJUSTMENT
        |--------------------------------------------------------------------------
        */

        if ($variation == 0) {

            throw ValidationException::withMessages([
                'variation' =>
                    'The stock variation cannot be zero.',
            ]);

        }


        /*
        |--------------------------------------------------------------------------
        | GET ITEM
        |--------------------------------------------------------------------------
        */

        $storeItem = StoreItem::query()
            ->with([
                'unit',
                'variants',
            ])
            ->findOrFail(
                $validated['store_item_id']
            );


        /*
        |--------------------------------------------------------------------------
        | VERIFY VARIANT BELONGS TO SELECTED ITEM
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['variant_id'])) {

            $variantBelongsToItem =
                $storeItem
                    ->variants()
                    ->whereKey(
                        $validated['variant_id']
                    )
                    ->exists();

            if (!$variantBelongsToItem) {

                throw ValidationException::withMessages([
                    'variant_id' =>
                        'The selected variant does not belong to the selected store item.',
                ]);

            }

        }


        /*
        |--------------------------------------------------------------------------
        | FIND STORE STOCK
        |--------------------------------------------------------------------------
        |
        | We adjust the stock row belonging to:
        |
        | Store
        | Item
        | Variant
        |
        */

        DB::transaction(function () use (
            $validated,
            $variation,
            $storeItem
        ) {

            $stockQuery = StoreStock::query()
                ->where(
                    'store_id',
                    $validated['store_id']
                )
                ->where(
                    'store_item_id',
                    $validated['store_item_id']
                );


            /*
            |--------------------------------------------------------------------------
            | VARIANT
            |--------------------------------------------------------------------------
            */

            if (
                array_key_exists(
                    'variant_id',
                    $validated
                )
                &&
                !empty($validated['variant_id'])
            ) {

                $stockQuery->where(
                    'variant_id',
                    $validated['variant_id']
                );

            } else {

                $stockQuery->whereNull(
                    'variant_id'
                );

            }


            /*
            |--------------------------------------------------------------------------
            | LOCK STOCK ROW
            |--------------------------------------------------------------------------
            */

            $stock = $stockQuery
                ->lockForUpdate()
                ->first();


            /*
            |--------------------------------------------------------------------------
            | STOCK RECORD MUST EXIST
            |--------------------------------------------------------------------------
            */

            if (!$stock) {

                throw ValidationException::withMessages([
                    'store_item_id' =>
                        'No stock record exists for the selected item, store and variant. Stock must exist before it can be adjusted.',
                ]);

            }


            /*
            |--------------------------------------------------------------------------
            | CURRENT BALANCE
            |--------------------------------------------------------------------------
            */

            $currentStock =
                (float) $stock->quantity;


            /*
            |--------------------------------------------------------------------------
            | NEW BALANCE
            |--------------------------------------------------------------------------
            */

            $newStock =
                $currentStock + $variation;


            /*
            |--------------------------------------------------------------------------
            | PREVENT NEGATIVE STOCK
            |--------------------------------------------------------------------------
            */

            if ($newStock < 0) {

                throw ValidationException::withMessages([
                    'variation' =>
                        'This adjustment would make the stock balance negative. Current stock is '
                        . number_format(
                            $currentStock,
                            3
                        )
                        . '.',
                ]);

            }


            /*
            |--------------------------------------------------------------------------
            | UPDATE STOCK BALANCE
            |--------------------------------------------------------------------------
            */

            $stock->forceFill([
                'quantity' =>
                    $newStock,

                'last_movement_at' =>
                    now(),
            ])->save();


            /*
            |--------------------------------------------------------------------------
            | MOVEMENT NOTES
            |--------------------------------------------------------------------------
            */

            $movementNotes =
                'Reason: '
                . $validated['reason']
                . PHP_EOL
                . 'Explanation: '
                . $validated['notes'];


            /*
            |--------------------------------------------------------------------------
            | CREATE STOCK MOVEMENT
            |--------------------------------------------------------------------------
            */

            StoreStockMovement::forceCreate([

                'store_id' =>
                    $stock->store_id,

                'store_item_id' =>
                    $stock->store_item_id,

                'variant_id' =>
                    $stock->variant_id,

                'movement_type' =>
                    'ADJUSTMENT',

                'quantity' =>
                    $variation,

                'balance_after' =>
                    $newStock,

                'reference_type' =>
                    StoreStock::class,

                'reference_id' =>
                    $stock->id,

                'created_by' =>
                    auth()->id(),

                'notes' =>
                    $movementNotes,

            ]);

        });


        /*
        |--------------------------------------------------------------------------
        | SUCCESS
        |--------------------------------------------------------------------------
        */

        return redirect()
            ->route(
                'admin.store-stock-adjustments.create'
            )
            ->with(
                'success',
                'Stock adjusted successfully.'
            );
    }
}