<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    /**
     * Display suppliers.
     */
    public function index(Request $request)
    {
        $query = Supplier::query()
            ->withCount('accountTransactions');

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('supplier_code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('contact_person', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('tax_pin', 'like', "%{$search}%");
            });
        }

        if ($request->status === 'active') {
            $query->where('is_active', true);
        }

        if ($request->status === 'inactive') {
            $query->where('is_active', false);
        }

        $suppliers = $query
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('admin.suppliers.index', compact('suppliers'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        return view('admin.suppliers.create');
    }

    /**
     * Store supplier.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'contact_person' => [
                'nullable',
                'string',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:50',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'tax_pin' => [
                'nullable',
                'string',
                'max:100',
            ],

            'payment_terms' => [
                'nullable',
                'string',
                'max:100',
            ],

            'credit_limit' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'opening_balance' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'notes' => [
                'nullable',
                'string',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $supplier = DB::transaction(function () use ($validated, $request) {
            return Supplier::create([
                'supplier_code' => $this->generateSupplierCode(),
                'name' => $validated['name'],
                'contact_person' => $validated['contact_person'] ?? null,
                'phone' => $validated['phone'] ?? null,
                'email' => $validated['email'] ?? null,
                'address' => $validated['address'] ?? null,
                'tax_pin' => $validated['tax_pin'] ?? null,
                'payment_terms' => $validated['payment_terms'] ?? null,
                'credit_limit' => $validated['credit_limit'] ?? 0,
                'opening_balance' => $validated['opening_balance'] ?? 0,
                'is_active' => $request->boolean('is_active', true),
                'notes' => $validated['notes'] ?? null,
            ]);
        });

        return redirect()
            ->route('admin.suppliers.show', $supplier)
            ->with('success', 'Supplier created successfully.');
    }

    /**
     * Display supplier.
     */
    public function show(Supplier $supplier)
    {
        $supplier->load([
            'accountTransactions' => function ($query) {
                $query
                    ->with('creator')
                    ->orderByDesc('transaction_date')
                    ->orderByDesc('id');
            },
        ]);

        $totalDebit = $supplier->accountTransactions->sum(
            fn ($transaction) => (float) $transaction->debit
        );

        $totalCredit = $supplier->accountTransactions->sum(
            fn ($transaction) => (float) $transaction->credit
        );

        $currentBalance = (float) $supplier->opening_balance
            + $totalDebit
            - $totalCredit;

        return view(
            'admin.suppliers.show',
            compact(
                'supplier',
                'totalDebit',
                'totalCredit',
                'currentBalance'
            )
        );
    }

    /**
     * Show edit form.
     */
    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    /**
     * Update supplier.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'contact_person' => [
                'nullable',
                'string',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:50',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'tax_pin' => [
                'nullable',
                'string',
                'max:100',
            ],

            'payment_terms' => [
                'nullable',
                'string',
                'max:100',
            ],

            'credit_limit' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'opening_balance' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'notes' => [
                'nullable',
                'string',
            ],

            'is_active' => [
                'nullable',
                'boolean',
            ],
        ]);

        $supplier->update([
            'name' => $validated['name'],
            'contact_person' => $validated['contact_person'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'address' => $validated['address'] ?? null,
            'tax_pin' => $validated['tax_pin'] ?? null,
            'payment_terms' => $validated['payment_terms'] ?? null,
            'credit_limit' => $validated['credit_limit'] ?? 0,
            'opening_balance' => $validated['opening_balance'] ?? 0,
            'is_active' => $request->boolean('is_active'),
            'notes' => $validated['notes'] ?? null,
        ]);

        return redirect()
            ->route('admin.suppliers.show', $supplier)
            ->with('success', 'Supplier updated successfully.');
    }

    /**
     * Activate supplier.
     */
    public function activate(Supplier $supplier)
    {
        $supplier->update([
            'is_active' => true,
        ]);

        return back()
            ->with('success', 'Supplier activated successfully.');
    }

    /**
     * Deactivate supplier.
     */
    public function deactivate(Supplier $supplier)
    {
        $supplier->update([
            'is_active' => false,
        ]);

        return back()
            ->with('success', 'Supplier deactivated successfully.');
    }

    /**
     * Delete supplier.
     *
     * Suppliers with account transactions are protected.
     */
    public function destroy(Supplier $supplier)
    {
        if ($supplier->accountTransactions()->exists()) {
            return back()
                ->with('error',
                    'This supplier cannot be deleted because supplier account transactions already exist. Deactivate the supplier instead.'
                );
        }

        /*
         * If your StoreLpo model/table already references suppliers,
         * prevent deletion when the supplier has been used by an LPO.
         */
        if (
            DB::table('store_lpos')
                ->where('supplier_id', $supplier->id)
                ->exists()
        ) {
            return back()
                ->with('error',
                    'This supplier cannot be deleted because it has already been used on an LPO. Deactivate the supplier instead.'
                );
        }

        $supplier->delete();

        return redirect()
            ->route('admin.suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }

    /**
     * Generate supplier code.
     */
    private function generateSupplierCode(): string
    {
        $lastSupplier = Supplier::query()
            ->orderByDesc('id')
            ->first();

        $nextNumber = $lastSupplier
            ? ((int) preg_replace('/[^0-9]/', '', $lastSupplier->supplier_code)) + 1
            : 1;

        do {
            $code = 'SUP-' . str_pad(
                $nextNumber,
                4,
                '0',
                STR_PAD_LEFT
            );

            $exists = Supplier::where(
                'supplier_code',
                $code
            )->exists();

            if ($exists) {
                $nextNumber++;
            }
        } while ($exists);

        return $code;
    }
}

