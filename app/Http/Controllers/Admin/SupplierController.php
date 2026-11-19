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
     * Supplier categories used throughout the supplier module.
     */
    private function categories(): array
    {
        return [
            'food_groceries' => 'Food & Groceries',
            'stationery' => 'Stationery & Learning Materials',
            'construction' => 'Construction & Building Materials',
            'maintenance' => 'Repairs & Maintenance',
            'ict_electronics' => 'ICT & Electronics',
            'furniture_equipment' => 'Furniture & Equipment',
            'medical_hygiene' => 'Medical & Hygiene Supplies',
            'transport' => 'Transport & Vehicle Services',
            'professional_services' => 'Professional Services',
            'utilities' => 'Utilities',
            'other' => 'Other',
        ];
    }

    /**
     * Supplier validation rules.
     */
    private function supplierRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],

            'category' => [
                'required',
                'string',
                Rule::in(array_keys($this->categories())),
            ],

            'contact_person' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string'],
            'tax_pin' => ['nullable', 'string', 'max:100'],
            'payment_terms' => ['nullable', 'string', 'max:100'],
            'credit_limit' => ['nullable', 'numeric', 'min:0'],
            'opening_balance' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Display suppliers.
     */
    public function index(Request $request)
    {
        $query = Supplier::query()
            ->withCount('accountTransactions');

        // General search.
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

        // Category filter.
        if (
            $request->filled('category') &&
            array_key_exists($request->category, $this->categories())
        ) {
            $query->where('category', $request->category);
        }

        // Status filter.
        if ($request->status === 'active') {
            $query->where('is_active', true);
        } elseif ($request->status === 'inactive') {
            $query->where('is_active', false);
        }

        // Payment terms filter.
        if ($request->filled('payment_terms')) {
            $paymentTerms = trim($request->payment_terms);

            $query->where(
                'payment_terms',
                'like',
                "%{$paymentTerms}%"
            );
        }

        $suppliers = $query
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        $categories = $this->categories();

        return view(
            'admin.suppliers.index',
            compact('suppliers', 'categories')
        );
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $categories = $this->categories();

        return view('admin.suppliers.create', compact('categories'));
    }

    /**
     * Store supplier.
     */
    public function store(Request $request)
    {
        $validated = $request->validate($this->supplierRules());

        $supplier = DB::transaction(function () use ($validated, $request) {
            return Supplier::create([
                'supplier_code' => $this->generateSupplierCode(),
                'name' => $validated['name'],
                'category' => $validated['category'],
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
        $categories = $this->categories();

        return view(
            'admin.suppliers.edit',
            compact('supplier', 'categories')
        );
    }

    /**
     * Update supplier.
     */
    public function update(Request $request, Supplier $supplier)
    {
        $validated = $request->validate($this->supplierRules());

        $supplier->update([
            'name' => $validated['name'],
            'category' => $validated['category'],
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
        $supplier->update(['is_active' => true]);

        return back()->with('success', 'Supplier activated successfully.');
    }

    /**
     * Deactivate supplier.
     */
    public function deactivate(Supplier $supplier)
    {
        $supplier->update(['is_active' => false]);

        return back()->with('success', 'Supplier deactivated successfully.');
    }

    /**
     * Delete supplier.
     *
     * Suppliers with account transactions or LPO references are protected.
     */
    public function destroy(Supplier $supplier)
    {
        if ($supplier->accountTransactions()->exists()) {
            return back()->with(
                'error',
                'This supplier cannot be deleted because supplier account transactions already exist. Deactivate the supplier instead.'
            );
        }

        if (
            DB::table('store_lpos')
                ->where('supplier_id', $supplier->id)
                ->exists()
        ) {
            return back()->with(
                'error',
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

            $exists = Supplier::where('supplier_code', $code)->exists();

            if ($exists) {
                $nextNumber++;
            }
        } while ($exists);

        return $code;
    }
}
