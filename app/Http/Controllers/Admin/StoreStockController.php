<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\StoreItem;
use App\Models\StoreStock;
use App\Models\StoreStockMovement;
use Illuminate\Http\Request;

class StoreStockController extends Controller
{
    /**
     * Display current stock balances.
     */
    public function index(Request $request)
    {
        $stores = Store::where('is_active', true)
            ->orderBy('name')
            ->get();

        $items = StoreItem::with('unit')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $stocks = StoreStock::with([
            'store',
            'item.unit',
            'variant',
        ])
            ->whereHas('store', function ($query) {
                $query->where('is_active', true);
            })
            ->whereHas('item', function ($query) {
                $query->where('is_active', true);
            })
            ->when($request->filled('store_id'), function ($query) use ($request) {
                $query->where('store_id', $request->store_id);
            })
            ->when($request->filled('item_id'), function ($query) use ($request) {
                $query->where('store_item_id', $request->item_id);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;

                $query->whereHas('item', function ($itemQuery) use ($search) {
                    $itemQuery->where('name', 'like', "%{$search}%");
                });
            })
            ->orderBy('store_id')
            ->orderBy('store_item_id')
            ->orderBy('variant_id')
            ->paginate(25)
            ->withQueryString();

        return view(
            'admin.stores.stock.index',
            compact(
                'stocks',
                'stores',
                'items'
            )
        );
    }

    /**
     * Return the stock movement ledger for one stock line.
     */
    public function ledger(StoreStock $storeStock)
    {
        $storeStock->load([
            'store',
            'item.unit',
            'variant',
        ]);

$movements = StoreStockMovement::with([
    'creator',
])
            ->where('store_id', $storeStock->store_id)
            ->where('store_item_id', $storeStock->store_item_id)
            ->when(
                $storeStock->variant_id === null,
                function ($query) {
                    $query->whereNull('variant_id');
                },
                function ($query) use ($storeStock) {
                    $query->where('variant_id', $storeStock->variant_id);
                }
            )
            ->orderByDesc('created_at')
            ->paginate(25);

        return response()->json([
            'stock' => [
                'id' => $storeStock->id,
                'store' => $storeStock->store->name,
                'item' => $storeStock->item->name,
                'variant' => $storeStock->variant?->name,
                'unit' => $storeStock->item->unit->code,
                'quantity' => $storeStock->quantity,
                'last_movement_at' => $storeStock->last_movement_at?->format('d M Y H:i'),
            ],

            'movements' => $movements->map(function ($movement) {
                return [
                    'date' => $movement->created_at?->format('d M Y H:i'),
                    'type' => $movement->movement_type,
                    'quantity' => $movement->quantity,
                    'balance_after' => $movement->balance_after,
                    'reference_type' => $movement->reference_type
                        ? class_basename($movement->reference_type)
                        : null,
                    'reference_id' => $movement->reference_id,
                    'created_by' => $movement->creator?->name,
                    'notes' => $movement->notes,
                ];
            })->values(),

            'pagination' => [
                'current_page' => $movements->currentPage(),
                'last_page' => $movements->lastPage(),
                'total' => $movements->total(),
            ],
        ]);
    }
}