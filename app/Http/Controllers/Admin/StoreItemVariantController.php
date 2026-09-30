<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreItem;
use App\Models\StoreItemVariant;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class StoreItemVariantController extends Controller
{
    public function index(StoreItem $storeItem)
    {
        $variants = $storeItem->variants()
            ->withCount('stocks')
            ->latest()
            ->get();

        return view(
            'admin.stores.items.variants.index',
            compact('storeItem', 'variants')
        );
    }

    public function create(StoreItem $storeItem)
    {
        return view(
            'admin.stores.items.variants.create',
            compact('storeItem')
        );
    }

    public function store(Request $request, StoreItem $storeItem)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('store_item_variants', 'name')
                    ->where('store_item_id', $storeItem->id),
            ],

            'code' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('store_item_variants', 'code')
                    ->where('store_item_id', $storeItem->id),
            ],

            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        if (!empty($validated['code'])) {
            $validated['code'] = strtoupper(trim($validated['code']));
        }

        $validated['store_item_id'] = $storeItem->id;
        $validated['is_active'] = true;

        StoreItemVariant::create($validated);

        return redirect()
            ->route('admin.store-item-variants.index', $storeItem)
            ->with('success', 'Item variant added successfully.');
    }

    public function show(StoreItem $storeItem, StoreItemVariant $variant)
    {
        $this->ensureVariantBelongsToItem($storeItem, $variant);

        $variant->load([
            'stocks.store',
        ]);

        return view(
            'admin.stores.items.variants.show',
            compact('storeItem', 'variant')
        );
    }

    public function edit(StoreItem $storeItem, StoreItemVariant $variant)
    {
        $this->ensureVariantBelongsToItem($storeItem, $variant);

        return view(
            'admin.stores.items.variants.edit',
            compact('storeItem', 'variant')
        );
    }

    public function update(
        Request $request,
        StoreItem $storeItem,
        StoreItemVariant $variant
    ) {
        $this->ensureVariantBelongsToItem($storeItem, $variant);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('store_item_variants', 'name')
                    ->where('store_item_id', $storeItem->id)
                    ->ignore($variant->id),
            ],

            'code' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('store_item_variants', 'code')
                    ->where('store_item_id', $storeItem->id)
                    ->ignore($variant->id),
            ],

            'description' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        if (!empty($validated['code'])) {
            $validated['code'] = strtoupper(trim($validated['code']));
        }

        $variant->update($validated);

        return redirect()
            ->route('admin.store-item-variants.show', [
                $storeItem,
                $variant,
            ])
            ->with('success', 'Item variant updated successfully.');
    }

    public function toggleStatus(
        StoreItem $storeItem,
        StoreItemVariant $variant
    ) {
        $this->ensureVariantBelongsToItem($storeItem, $variant);

        $variant->update([
            'is_active' => ! $variant->is_active,
        ]);

        return redirect()
            ->route('admin.store-item-variants.index', $storeItem)
            ->with(
                'success',
                $variant->is_active
                    ? 'Item variant activated successfully.'
                    : 'Item variant deactivated successfully.'
            );
    }

    public function destroy(
        StoreItem $storeItem,
        StoreItemVariant $variant
    ) {
        $this->ensureVariantBelongsToItem($storeItem, $variant);

        if ($variant->stocks()->exists()) {
            return redirect()
                ->route('admin.store-item-variants.index', $storeItem)
                ->with(
                    'error',
                    'This variant cannot be deleted because it has stock records. Deactivate it instead.'
                );
        }

        if ($variant->stockMovements()->exists()) {
            return redirect()
                ->route('admin.store-item-variants.index', $storeItem)
                ->with(
                    'error',
                    'This variant cannot be deleted because it has stock movement records. Deactivate it instead.'
                );
        }

        $variant->delete();

        return redirect()
            ->route('admin.store-item-variants.index', $storeItem)
            ->with('success', 'Item variant deleted successfully.');
    }

    private function ensureVariantBelongsToItem(
        StoreItem $storeItem,
        StoreItemVariant $variant
    ): void {
        abort_unless(
            $variant->store_item_id === $storeItem->id,
            404
        );
    }
}