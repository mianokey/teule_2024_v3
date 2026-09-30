<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index()
    {
        $stores = Store::withCount('stocks')
            ->latest()
            ->get();

        return view('admin.stores.index', compact('stores'));
    }

    public function create()
    {
        return view('admin.stores.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:stores,name',
            'code' => 'required|string|max:50|unique:stores,code',
            'description' => 'nullable|string|max:2000',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['is_active'] = true;

        Store::create($validated);

        return redirect()
            ->route('admin.stores.index')
            ->with('success', 'Store added successfully.');
    }

    public function show(Store $store)
    {
        $store->loadCount('stocks');

        return view('admin.stores.show', compact('store'));
    }

    public function edit(Store $store)
    {
        return view('admin.stores.edit', compact('store'));
    }

    public function update(Request $request, Store $store)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:stores,name,' . $store->id,
            'code' => 'required|string|max:50|unique:stores,code,' . $store->id,
            'description' => 'nullable|string|max:2000',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));

        $store->update($validated);

        return redirect()
            ->route('admin.stores.index')
            ->with('success', 'Store updated successfully.');
    }

    public function toggleStatus(Store $store)
    {
        $store->update([
            'is_active' => ! $store->is_active,
        ]);

        return redirect()
            ->route('admin.stores.index')
            ->with(
                'success',
                $store->is_active
                    ? 'Store activated successfully.'
                    : 'Store deactivated successfully.'
            );
    }

    public function destroy(Store $store)
    {
        if ($store->stocks()->exists()) {
            return redirect()
                ->route('admin.stores.index')
                ->with(
                    'error',
                    'This store cannot be deleted because it has stock records. Deactivate it instead.'
                );
        }

        if ($store->stockMovements()->exists()) {
            return redirect()
                ->route('admin.stores.index')
                ->with(
                    'error',
                    'This store cannot be deleted because it has stock movement records. Deactivate it instead.'
                );
        }

        $store->delete();

        return redirect()
            ->route('admin.stores.index')
            ->with('success', 'Store deleted successfully.');
    }
}