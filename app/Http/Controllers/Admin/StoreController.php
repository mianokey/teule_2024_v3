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
        'name' => [
            'required',
            'string',
            'max:255',
        ],

        'code' => [
            'required',
            'string',
            'max:100',
        ],

        // KEEP YOUR OTHER EXISTING VALIDATION RULES HERE
    ]);

    /*
    |--------------------------------------------------------------------------
    | NORMALIZE VALUES
    |--------------------------------------------------------------------------
    */

    $validated['name'] = trim($validated['name']);
    $validated['code'] = strtoupper(trim($validated['code']));

    /*
    |--------------------------------------------------------------------------
    | MAIN STORE PROTECTION
    |--------------------------------------------------------------------------
    |
    | A store is considered a MAIN STORE if "main" appears anywhere
    | in either its name OR its code.
    |
    | Examples:
    |
    | TLA- MAIN STORE
    | MAIN STORE
    | TLA-MAIN
    | MAIN
    |
    */

    $isMainStore = str_contains(
        strtolower($validated['name']),
        'main'
    ) || str_contains(
        strtolower($validated['code']),
        'main'
    );

    if ($isMainStore) {

        $mainStoreExists = Store::query()
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
            ->exists();

        if ($mainStoreExists) {
            return back()
                ->withInput()
                ->withErrors([
                    'name' =>
                        'A Main Store already exists. Only one store may contain "MAIN" in its name or code.',
                ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE STORE
    |--------------------------------------------------------------------------
    */

    Store::create($validated);

    return redirect()
        ->route('admin.stores.index')
        ->with(
            'success',
            'Store created successfully.'
        );
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
        'name' => [
            'required',
            'string',
            'max:255',
        ],

        'code' => [
            'required',
            'string',
            'max:100',
        ],

        // KEEP YOUR OTHER EXISTING VALIDATION RULES HERE
    ]);

    /*
    |--------------------------------------------------------------------------
    | NORMALIZE VALUES
    |--------------------------------------------------------------------------
    */

    $validated['name'] = trim($validated['name']);
    $validated['code'] = strtoupper(trim($validated['code']));

    /*
    |--------------------------------------------------------------------------
    | MAIN STORE PROTECTION
    |--------------------------------------------------------------------------
    */

    $isMainStore = str_contains(
        strtolower($validated['name']),
        'main'
    ) || str_contains(
        strtolower($validated['code']),
        'main'
    );

    if ($isMainStore) {

        $anotherMainStoreExists = Store::query()
            ->where('id', '!=', $store->id)
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
            ->exists();

        if ($anotherMainStoreExists) {
            return back()
                ->withInput()
                ->withErrors([
                    'name' =>
                        'Another Main Store already exists. Only one store may contain "MAIN" in its name or code.',
                ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE STORE
    |--------------------------------------------------------------------------
    */

    $store->update($validated);

    return redirect()
        ->route('admin.stores.index')
        ->with(
            'success',
            'Store updated successfully.'
        );
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