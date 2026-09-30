<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreItemCategory;
use Illuminate\Http\Request;

class StoreItemCategoryController extends Controller
{
    public function index()
    {
        $categories = StoreItemCategory::withCount('items')
            ->latest()
            ->get();

        return view('admin.stores.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.stores.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:store_item_categories,name',
            'description' => 'nullable|string|max:2000',
        ]);

        $validated['is_active'] = true;

        StoreItemCategory::create($validated);

        return redirect()
            ->route('admin.store-categories.index')
            ->with('success', 'Item category added successfully.');
    }

    public function show(StoreItemCategory $storeCategory)
    {
        $storeCategory->loadCount('items');

        return view('admin.stores.categories.show', compact('storeCategory'));
    }

    public function edit(StoreItemCategory $storeCategory)
    {
        return view('admin.stores.categories.edit', compact('storeCategory'));
    }

    public function update(Request $request, StoreItemCategory $storeCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:store_item_categories,name,' . $storeCategory->id,
            'description' => 'nullable|string|max:2000',
        ]);

        $storeCategory->update($validated);

        return redirect()
            ->route('admin.store-categories.index')
            ->with('success', 'Item category updated successfully.');
    }

    public function toggleStatus(StoreItemCategory $storeCategory)
    {
        $storeCategory->update([
            'is_active' => ! $storeCategory->is_active,
        ]);

        return redirect()
            ->route('admin.store-categories.index')
            ->with(
                'success',
                $storeCategory->is_active
                    ? 'Item category activated successfully.'
                    : 'Item category deactivated successfully.'
            );
    }

    public function destroy(StoreItemCategory $storeCategory)
    {
        if ($storeCategory->items()->exists()) {
            return redirect()
                ->route('admin.store-categories.index')
                ->with(
                    'error',
                    'This category cannot be deleted because it has store items. Deactivate it instead.'
                );
        }

        $storeCategory->delete();

        return redirect()
            ->route('admin.store-categories.index')
            ->with('success', 'Item category deleted successfully.');
    }
}