<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\StoreItem;
use App\Models\StoreItemCategory;
use App\Models\StoreStock;
use App\Models\StoreStockMovement;
use App\Models\StoreUnit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StoreItemController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        $items = StoreItem::with([
            'category',
            'unit',
            'stocks.store',
        ])
            ->latest()
            ->get();

        return view(
            'admin.stores.items.index',
            compact('items')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        $categories = StoreItemCategory::where(
            'is_active',
            true
        )
            ->orderBy('name')
            ->get();

        $units = StoreUnit::where(
            'is_active',
            true
        )
            ->orderBy('name')
            ->get();

        return view(
            'admin.stores.items.create',
            compact(
                'categories',
                'units'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | VALIDATE
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([

            'category_id' => [
                'required',
                'integer',
                'exists:store_item_categories,id',
            ],

            'unit_id' => [
                'required',
                'integer',
                'exists:store_units,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'sku' => [
                'nullable',
                'string',
                'max:100',
                'unique:store_items,sku',
            ],

            'item_type' => [
                'required',
                Rule::in([
                    'CONSUMABLE',
                    'RETURNABLE',
                    'ASSET',
                ]),
            ],

            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'reorder_level' => [
                'required',
                'numeric',
                'min:0',
            ],

            'opening_stock' => [
                'required',
                'numeric',
                'min:0',
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | NORMALIZE SKU
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['sku'])) {

            $validated['sku'] =
                strtoupper(
                    trim($validated['sku'])
                );

        }


        /*
        |--------------------------------------------------------------------------
        | OPENING STOCK
        |--------------------------------------------------------------------------
        */

        $openingStock =
            (float) $validated['opening_stock'];


        /*
        |--------------------------------------------------------------------------
        | REMOVE opening_stock FROM STORE ITEMS DATA
        |--------------------------------------------------------------------------
        |
        | opening_stock is not a StoreItem column.
        |
        */

        unset(
            $validated['opening_stock']
        );


        /*
        |--------------------------------------------------------------------------
        | CREATE ITEM + STOCK
        |--------------------------------------------------------------------------
        */

        DB::transaction(function () use (
            $validated,
            $openingStock
        ) {

            /*
            |--------------------------------------------------------------------------
            | CREATE STORE ITEM
            |--------------------------------------------------------------------------
            */

            $validated['is_active'] = true;

            $storeItem = StoreItem::create(
                $validated
            );


            /*
            |--------------------------------------------------------------------------
            | GET MAIN STORE
            |--------------------------------------------------------------------------
            */

$mainStore = Store::query()
    ->where(function ($query) {
        $query->whereRaw(
            'LOWER(name) LIKE ?',
            ['%main%']
        )
        ->orWhereRaw(
            'LOWER(code) LIKE ?',
            ['%main%']
        );
    })
    ->first();

if (!$mainStore) {
    throw new \RuntimeException(
        'Main Store was not found. Please create a store with "MAIN" in its name or code before adding store items.'
    );
}


            /*
            |--------------------------------------------------------------------------
            | CREATE STOCK RECORD
            |--------------------------------------------------------------------------
            |
            | Your stock structure uses:
            |
            | store_id
            | store_item_id
            | variant_id
            | quantity
            | last_movement_at
            |
            */

            $stock = StoreStock::create([

                'store_id' =>
                    $mainStore->id,

                'store_item_id' =>
                    $storeItem->id,

                'variant_id' =>
                    null,

                'quantity' =>
                    $openingStock,

                'last_movement_at' =>
                    now(),

            ]);


            /*
            |--------------------------------------------------------------------------
            | CREATE OPENING STOCK MOVEMENT
            |--------------------------------------------------------------------------
            |
            | We use RECEIPT because your existing stock movement
            | workflow already uses RECEIPT for incoming stock.
            |
            */

            if ($openingStock > 0) {

                StoreStockMovement::create([

                    'store_id' =>
                        $mainStore->id,

                    'store_item_id' =>
                        $storeItem->id,

                    'variant_id' =>
                        null,

                    'movement_type' =>
                        'RECEIPT',

                    'quantity' =>
                        $openingStock,

                    'balance_after' =>
                        $openingStock,

                    'reference_type' =>
                        StoreItem::class,

                    'reference_id' =>
                        $storeItem->id,

                    'created_by' =>
                        auth()->id(),

                    'notes' =>
                        'Opening stock entered when store item was created.',

                ]);

            }

        });


        /*
        |--------------------------------------------------------------------------
        | REDIRECT
        |--------------------------------------------------------------------------
        */

        return redirect()

            ->route(
                'admin.store-items.index'
            )

            ->with(
                'success',
                'Store item added successfully with opening stock.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show(StoreItem $storeItem)
    {
        $storeItem->load([

            'category',

            'unit',

            'stocks.store',

            'variants',

        ]);

        return view(
            'admin.stores.items.show',
            compact('storeItem')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(StoreItem $storeItem)
    {
        $categories = StoreItemCategory::where(
            'is_active',
            true
        )
            ->orderBy('name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Always include current category
        |--------------------------------------------------------------------------
        */

        if (
            $storeItem->category &&
            !$categories->contains(
                'id',
                $storeItem->category_id
            )
        ) {

            $categories->push(
                $storeItem->category
            );

            $categories =
                $categories
                    ->sortBy('name')
                    ->values();

        }


        /*
        |--------------------------------------------------------------------------
        | Units
        |--------------------------------------------------------------------------
        */

        $units = StoreUnit::where(
            'is_active',
            true
        )
            ->orderBy('name')
            ->get();


        /*
        |--------------------------------------------------------------------------
        | Always include current unit
        |--------------------------------------------------------------------------
        */

        if (
            $storeItem->unit &&
            !$units->contains(
                'id',
                $storeItem->unit_id
            )
        ) {

            $units->push(
                $storeItem->unit
            );

            $units =
                $units
                    ->sortBy('name')
                    ->values();

        }


        return view(
            'admin.stores.items.edit',
            compact(
                'storeItem',
                'categories',
                'units'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        StoreItem $storeItem
    ) {

        $validated = $request->validate([

            'category_id' => [
                'required',
                'integer',
                'exists:store_item_categories,id',
            ],

            'unit_id' => [
                'required',
                'integer',
                'exists:store_units,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'sku' => [
                'nullable',
                'string',
                'max:100',

                Rule::unique(
                    'store_items',
                    'sku'
                )->ignore(
                    $storeItem->id
                ),
            ],

            'item_type' => [
                'required',
                Rule::in([
                    'CONSUMABLE',
                    'RETURNABLE',
                    'ASSET',
                ]),
            ],

            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'reorder_level' => [
                'required',
                'numeric',
                'min:0',
            ],

        ]);


        /*
        |--------------------------------------------------------------------------
        | NORMALIZE SKU
        |--------------------------------------------------------------------------
        */

        if (!empty($validated['sku'])) {

            $validated['sku'] =
                strtoupper(
                    trim($validated['sku'])
                );

        }


        /*
        |--------------------------------------------------------------------------
        | UPDATE ITEM
        |--------------------------------------------------------------------------
        */

        $storeItem->update(
            $validated
        );


        return redirect()

            ->route(
                'admin.store-items.show',
                $storeItem
            )

            ->with(
                'success',
                'Store item updated successfully.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | TOGGLE STATUS
    |--------------------------------------------------------------------------
    */

    public function toggleStatus(
        StoreItem $storeItem
    ) {

        $storeItem->update([

            'is_active' =>
                !$storeItem->is_active,

        ]);


        return redirect()

            ->route(
                'admin.store-items.index'
            )

            ->with(

                'success',

                $storeItem->is_active

                    ? 'Store item activated successfully.'

                    : 'Store item deactivated successfully.'

            );
    }


    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */

    public function destroy(
        StoreItem $storeItem
    ) {

        /*
        |--------------------------------------------------------------------------
        | DO NOT DELETE ITEMS WITH STOCK RECORDS
        |--------------------------------------------------------------------------
        */

        if (
            $storeItem
                ->stocks()
                ->exists()
        ) {

            return redirect()

                ->route(
                    'admin.store-items.index'
                )

                ->with(

                    'error',

                    'This item cannot be deleted because it has stock records. Deactivate it instead.'

                );

        }


        /*
        |--------------------------------------------------------------------------
        | DO NOT DELETE ITEMS WITH STOCK MOVEMENTS
        |--------------------------------------------------------------------------
        */

        if (
            $storeItem
                ->stockMovements()
                ->exists()
        ) {

            return redirect()

                ->route(
                    'admin.store-items.index'
                )

                ->with(

                    'error',

                    'This item cannot be deleted because it has stock movement records. Deactivate it instead.'

                );

        }


        /*
        |--------------------------------------------------------------------------
        | DELETE
        |--------------------------------------------------------------------------
        */

        $storeItem->delete();


        return redirect()

            ->route(
                'admin.store-items.index'
            )

            ->with(
                'success',
                'Store item deleted successfully.'
            );
    }
}