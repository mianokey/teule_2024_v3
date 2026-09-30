<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreUnit;
use Illuminate\Http\Request;

class StoreUnitController extends Controller
{
    public function index()
    {
        $units = StoreUnit::withCount('items')
            ->latest()
            ->get();

        return view('admin.stores.units.index', compact('units'));
    }

    public function create()
    {
        return view('admin.stores.units.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:store_units,name',
            'code' => 'required|string|max:20|unique:store_units,code',
            'description' => 'nullable|string|max:2000',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));
        $validated['is_active'] = true;

        StoreUnit::create($validated);

        return redirect()
            ->route('admin.store-units.index')
            ->with('success', 'Store unit added successfully.');
    }

    public function show(StoreUnit $storeUnit)
    {
        $storeUnit->loadCount('items');

        return view('admin.stores.units.show', compact('storeUnit'));
    }

    public function edit(StoreUnit $storeUnit)
    {
        return view('admin.stores.units.edit', compact('storeUnit'));
    }

    public function update(Request $request, StoreUnit $storeUnit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:store_units,name,' . $storeUnit->id,
            'code' => 'required|string|max:20|unique:store_units,code,' . $storeUnit->id,
            'description' => 'nullable|string|max:2000',
        ]);

        $validated['code'] = strtoupper(trim($validated['code']));

        $storeUnit->update($validated);

        return redirect()
            ->route('admin.store-units.index')
            ->with('success', 'Store unit updated successfully.');
    }

    public function toggleStatus(StoreUnit $storeUnit)
    {
        $storeUnit->update([
            'is_active' => ! $storeUnit->is_active,
        ]);

        return redirect()
            ->route('admin.store-units.index')
            ->with(
                'success',
                $storeUnit->is_active
                    ? 'Store unit activated successfully.'
                    : 'Store unit deactivated successfully.'
            );
    }

    public function destroy(StoreUnit $storeUnit)
    {
        if ($storeUnit->items()->exists()) {
            return redirect()
                ->route('admin.store-units.index')
                ->with(
                    'error',
                    'This unit cannot be deleted because it is being used by store items. Deactivate it instead.'
                );
        }

        $storeUnit->delete();

        return redirect()
            ->route('admin.store-units.index')
            ->with('success', 'Store unit deleted successfully.');
    }
}