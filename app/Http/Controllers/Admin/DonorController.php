<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donor;
use Illuminate\Http\Request;

class DonorController extends Controller
{
    /**
     * Display all donors.
     */
    public function index()
    {
        $donors = Donor::withCount('donations')
            ->latest()
            ->get();

        return view('admin.donors.index', compact('donors'));
    }

    /**
     * Show the form for creating a donor.
     */
    public function create()
    {
        return view('admin.donors.create');
    }

    /**
     * Store a new donor.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'phone'        => 'nullable|string|max:50',
            'email'        => 'nullable|email|max:255',
            'organization' => 'nullable|string|max:255',
            'address'      => 'nullable|string|max:1000',
            'notes'        => 'nullable|string|max:2000',
        ]);

        $nextId = (Donor::max('id') ?? 0) + 1;

        $validated['donor_number'] =
            'DON-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);

        Donor::create($validated);

        return redirect()
            ->route('admin.donors.index')
            ->with('success', 'Donor added successfully.');
    }

    /**
     * Display a donor and their donation history.
     */
public function show(Donor $donor)
{
    $donor->load([
        'donations' => function ($query) {
            $query->latest('donation_date');
        },

        'communications' => function ($query) {
            $query->latest();
        },
    ]);

    return view('admin.donors.show', compact('donor'));
}

    /**
     * Show the form for editing a donor.
     */
    public function edit(Donor $donor)
    {
        return view('admin.donors.edit', compact('donor'));
    }

    /**
     * Update a donor.
     */
    public function update(Request $request, Donor $donor)
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'phone'        => 'nullable|string|max:50',
            'email'        => 'nullable|email|max:255',
            'organization' => 'nullable|string|max:255',
            'address'      => 'nullable|string|max:1000',
            'notes'        => 'nullable|string|max:2000',
        ]);

        $donor->update($validated);

        return redirect()
            ->route('admin.donors.show', $donor)
            ->with('success', 'Donor updated successfully.');
    }
}
