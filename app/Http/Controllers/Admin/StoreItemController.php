<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreItem;
use App\Models\StoreItemCategory;
use App\Models\StoreUnit;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StoreItemController extends Controller
{
    public function index()
    {
        $items = StoreItem::with([
            'category',
            'unit',
            'stocks.store',
        ])
            ->latest()
            ->get();

        return view('admin.stores.items.index', compact('items'));
    }

    public function create()
    {
        $categories = StoreItemCategory::where('is_active', true)
            ->orderBy('name')
            ->get();

        $units = StoreUnit::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.stores.items.create', compact(
            'categories',
            'units'
        ));
    }

    public function store(Request $request)
    {
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
        ]);

        if (!empty($validated['sku'])) {
            $validated['sku'] = strtoupper(trim($validated['sku']));
        }

        $validated['is_active'] = true;

        StoreItem::create($validated);

        return redirect()
            ->route('admin.store-items.index')
            ->with('success', 'Store item added successfully.');
    }

public function show(StoreItem $storeItem)
{
    $storeItem->load([
        'category',
        'unit',
        'stocks.store',
        'variants',
    ]);

    return view('admin.stores.items.show', compact('storeItem'));
}

    public function edit(StoreItem $storeItem)
    {
        $categories = StoreItemCategory::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Always include the item's current category
        if (
            $storeItem->category &&
            !$categories->contains('id', $storeItem->category_id)
        ) {
            $categories->push($storeItem->category);
            $categories = $categories->sortBy('name')->values();
        }

        $units = StoreUnit::where('is_active', true)
            ->orderBy('name')
            ->get();

        // Always include the item's current unit
        if (
            $storeItem->unit &&
            !$units->contains('id', $storeItem->unit_id)
        ) {
            $units->push($storeItem->unit);
            $units = $units->sortBy('name')->values();
        }

        return view('admin.stores.items.edit', compact(
            'storeItem',
            'categories',
            'units'
        ));
    }

    public function update(Request $request, StoreItem $storeItem)
    {
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
                'unique:store_items,sku,' . $storeItem->id,
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

        if (!empty($validated['sku'])) {
            $validated['sku'] = strtoupper(trim($validated['sku']));
        }

        $storeItem->update($validated);

        return redirect()
            ->route('admin.store-items.show', $storeItem)
            ->with('success', 'Store item updated successfully.');
    }

    public function toggleStatus(StoreItem $storeItem)
    {
        $storeItem->update([
            'is_active' => ! $storeItem->is_active,
        ]);

        return redirect()
            ->route('admin.store-items.index')
            ->with(
                'success',
                $storeItem->is_active
                    ? 'Store item activated successfully.'
                    : 'Store item deactivated successfully.'
            );
    }

    public function destroy(StoreItem $storeItem)
    {
        if ($storeItem->stocks()->exists()) {
            return redirect()
                ->route('admin.store-items.index')
                ->with(
                    'error',
                    'This item cannot be deleted because it has stock records. Deactivate it instead.'
                );
        }

        if ($storeItem->stockMovements()->exists()) {
            return redirect()
                ->route('admin.store-items.index')
                ->with(
                    'error',
                    'This item cannot be deleted because it has stock movement records. Deactivate it instead.'
                );
        }

        $storeItem->delete();

        return redirect()
            ->route('admin.store-items.index')
            ->with('success', 'Store item deleted successfully.');
    }
}