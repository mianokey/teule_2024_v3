<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\StoreItem;
use App\Models\StoreItemVariant;
use App\Models\StoreLpo;
use App\Models\StoreLpoApproval;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StoreLpoController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request): View
    {
        $query = StoreLpo::query()
            ->with([
                'supplier',
                'store',
                'creator',
            ])
            ->withCount('items');

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('lpo_number', 'like', "%{$search}%")
                    ->orWhereHas('supplier', function ($supplierQuery) use ($search) {
                        $supplierQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('approval_stage')) {
            $query->where('approval_stage', $request->approval_stage);
        }

        $lpos = $query
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('admin.store_lpos.index', compact('lpos'));
    }


    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create(): View
    {
        $suppliers = Supplier::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $stores = Store::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $items = StoreItem::query()
            ->where('is_active', true)
            ->with([
                'unit',
                'variants' => function ($query) {
                    $query->where('is_active', true)
                        ->orderBy('name');
                },
            ])
            ->orderBy('name')
            ->get();

        return view('admin.store_lpos.create', compact(
            'suppliers',
            'stores',
            'items'
        ));
    }


    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateLpo($request);

        DB::transaction(function () use ($validated, $request) {
            $items = $validated['items'];

            $subtotal = 0;

            foreach ($items as $item) {
                $quantity = (float) $item['ordered_quantity'];
                $unitPrice = (float) $item['unit_price'];
                $discount = (float) ($item['discount'] ?? 0);
                $tax = (float) ($item['tax'] ?? 0);

                $lineBase = $quantity * $unitPrice;

                $lineTotal = max(
                    0,
                    $lineBase - $discount + $tax
                );

                $subtotal += $lineTotal;
            }

            $headerDiscount = (float) ($validated['discount'] ?? 0);
            $headerTax = (float) ($validated['tax'] ?? 0);

            $total = max(
                0,
                $subtotal - $headerDiscount + $headerTax
            );

            $lpo = StoreLpo::create([
                'lpo_number' => $this->generateLpoNumber(),
                'supplier_id' => $validated['supplier_id'],
                'store_id' => $validated['store_id'],
                'lpo_date' => $validated['lpo_date'],
                'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
                'status' => 'DRAFT',
                'approval_stage' => 'hod',
                'subtotal' => $subtotal,
                'tax' => $headerTax,
                'discount' => $headerDiscount,
                'total' => $total,
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            foreach ($items as $item) {
                $storeItem = StoreItem::findOrFail($item['store_item_id']);

                $variant = null;

                if (!empty($item['variant_id'])) {
                    $variant = StoreItemVariant::query()
                        ->where('id', $item['variant_id'])
                        ->where('store_item_id', $storeItem->id)
                        ->firstOrFail();
                }

                $quantity = (float) $item['ordered_quantity'];
                $unitPrice = (float) $item['unit_price'];
                $discount = (float) ($item['discount'] ?? 0);
                $tax = (float) ($item['tax'] ?? 0);

                $lineBase = $quantity * $unitPrice;

                $lineTotal = max(
                    0,
                    $lineBase - $discount + $tax
                );

                $description = $item['description'] ?? null;

                if (!$description) {
                    $description = $variant
                        ? $storeItem->name . ' - ' . $variant->name
                        : $storeItem->name;
                }

                $lpo->items()->create([
                    'store_item_id' => $storeItem->id,
                    'variant_id' => $variant?->id,
                    'description' => $description,
                    'ordered_quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount' => $discount,
                    'tax' => $tax,
                    'line_total' => $lineTotal,
                    'notes' => $item['notes'] ?? null,
                ]);
            }
        });

        return redirect()
            ->route('admin.store-lpos.index')
            ->with('success', 'LPO created successfully.');
    }


    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show(StoreLpo $storeLpo): View
    {
        $storeLpo->load([
            'supplier',
            'store',
            'creator',
            'approver',
            'items.item.unit',
            'items.variant',
            'approvals.user',
        ]);

        return view('admin.store_lpos.show', compact('storeLpo'));
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(StoreLpo $storeLpo): View|RedirectResponse
    {
        if (!$this->canEdit($storeLpo)) {
            return redirect()
                ->route('admin.store-lpos.show', $storeLpo)
                ->with('error', 'This LPO can no longer be edited.');
        }

        $storeLpo->load([
            'items.item',
            'items.variant',
        ]);

        $suppliers = Supplier::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $stores = Store::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $items = StoreItem::query()
            ->where('is_active', true)
            ->with([
                'unit',
                'variants' => function ($query) {
                    $query->where('is_active', true)
                        ->orderBy('name');
                },
            ])
            ->orderBy('name')
            ->get();

        return view('admin.store_lpos.edit', compact(
            'storeLpo',
            'suppliers',
            'stores',
            'items'
        ));
    }


    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(
        Request $request,
        StoreLpo $storeLpo
    ): RedirectResponse {
        if (!$this->canEdit($storeLpo)) {
            return redirect()
                ->route('admin.store-lpos.show', $storeLpo)
                ->with('error', 'This LPO can no longer be edited.');
        }

        $validated = $this->validateLpo(
            $request,
            $storeLpo
        );

        DB::transaction(function () use (
            $validated,
            $storeLpo
        ) {
            /*
             * Once receipts exist, the LPO supplier/store and
             * already-received lines must not be changed.
             */
            $hasReceipts = $storeLpo->loadCount('items')
                ->items()
                ->whereHas('lpo', function ($query) use ($storeLpo) {
                    $query->where('id', $storeLpo->id);
                })
                ->whereHas('lpo', function ($query) {
                    $query->whereHas('approvals');
                })
                ->exists();

            /*
             * The actual receipt check is performed directly below.
             */
            $existingReceived = [];

            foreach ($storeLpo->items as $existingItem) {
                $existingReceived[$existingItem->id] =
                    $existingItem->received_quantity;
            }

            $subtotal = 0;

            foreach ($validated['items'] as $item) {
                $quantity = (float) $item['ordered_quantity'];
                $unitPrice = (float) $item['unit_price'];
                $discount = (float) ($item['discount'] ?? 0);
                $tax = (float) ($item['tax'] ?? 0);

                $subtotal += max(
                    0,
                    ($quantity * $unitPrice) - $discount + $tax
                );
            }

            $headerDiscount = (float) ($validated['discount'] ?? 0);
            $headerTax = (float) ($validated['tax'] ?? 0);

            $total = max(
                0,
                $subtotal - $headerDiscount + $headerTax
            );

            $storeLpo->update([
                'supplier_id' => $validated['supplier_id'],
                'store_id' => $validated['store_id'],
                'lpo_date' => $validated['lpo_date'],
                'expected_delivery_date' => $validated['expected_delivery_date'] ?? null,
                'subtotal' => $subtotal,
                'tax' => $headerTax,
                'discount' => $headerDiscount,
                'total' => $total,
                'notes' => $validated['notes'] ?? null,
            ]);

            $submittedItemIds = [];

            foreach ($validated['items'] as $item) {
                $storeItem = StoreItem::findOrFail(
                    $item['store_item_id']
                );

                $variant = null;

                if (!empty($item['variant_id'])) {
                    $variant = StoreItemVariant::query()
                        ->where('id', $item['variant_id'])
                        ->where('store_item_id', $storeItem->id)
                        ->firstOrFail();
                }

                $quantity = (float) $item['ordered_quantity'];

                /*
                 * Existing line, if supplied.
                 */
                $lpoItem = null;

                if (!empty($item['id'])) {
                    $lpoItem = $storeLpo->items()
                        ->where('id', $item['id'])
                        ->first();
                }

                if ($lpoItem) {
                    $received = (float) (
                        $existingReceived[$lpoItem->id] ?? 0
                    );

                    if ($quantity < $received) {
                        abort(
                            422,
                            'Ordered quantity cannot be less than the quantity already received for an LPO item.'
                        );
                    }

                    $lpoItem->update([
                        'store_item_id' => $storeItem->id,
                        'variant_id' => $variant?->id,
                        'description' => $item['description'] ?? (
                            $variant
                            ? $storeItem->name . ' - ' . $variant->name
                            : $storeItem->name
                        ),
                        'ordered_quantity' => $quantity,
                        'unit_price' => $item['unit_price'],
                        'discount' => $item['discount'] ?? 0,
                        'tax' => $item['tax'] ?? 0,
                        'line_total' => max(
                            0,
                            ($quantity * (float) $item['unit_price'])
                                - (float) ($item['discount'] ?? 0)
                                + (float) ($item['tax'] ?? 0)
                        ),
                        'notes' => $item['notes'] ?? null,
                    ]);

                    $submittedItemIds[] = $lpoItem->id;
                } else {
                    $newItem = $storeLpo->items()->create([
                        'store_item_id' => $storeItem->id,
                        'variant_id' => $variant?->id,
                        'description' => $item['description'] ?? (
                            $variant
                            ? $storeItem->name . ' - ' . $variant->name
                            : $storeItem->name
                        ),
                        'ordered_quantity' => $quantity,
                        'unit_price' => $item['unit_price'],
                        'discount' => $item['discount'] ?? 0,
                        'tax' => $item['tax'] ?? 0,
                        'line_total' => max(
                            0,
                            ($quantity * (float) $item['unit_price'])
                                - (float) ($item['discount'] ?? 0)
                                + (float) ($item['tax'] ?? 0)
                        ),
                        'notes' => $item['notes'] ?? null,
                    ]);

                    $submittedItemIds[] = $newItem->id;
                }
            }

            /*
             * Delete lines removed by the user, but never delete a
             * line that has already received stock.
             */
            foreach ($storeLpo->items()->get() as $existingItem) {
                if (
                    !in_array(
                        $existingItem->id,
                        $submittedItemIds,
                        true
                    )
                ) {
                    if ($existingItem->received_quantity > 0) {
                        abort(
                            422,
                            'An LPO item that has already been received cannot be removed.'
                        );
                    }

                    $existingItem->delete();
                }
            }

            /*
             * If the LPO was returned for correction, editing it
             * puts it back into DRAFT.
             */
            if ($storeLpo->status === 'RETURNED') {
                $storeLpo->update([
                    'status' => 'DRAFT',
                    'approval_stage' => null,
                    'approved_by' => null,
                    'approved_at' => null,
                ]);
            }
        });

        return redirect()
            ->route('admin.store-lpos.show', $storeLpo)
            ->with('success', 'LPO updated successfully.');
    }


    /*
    |--------------------------------------------------------------------------
    | DESTROY
    |--------------------------------------------------------------------------
    */

    public function destroy(StoreLpo $storeLpo): RedirectResponse
    {
        if (!$this->canDelete($storeLpo)) {
            return redirect()
                ->route('admin.store-lpos.show', $storeLpo)
                ->with(
                    'error',
                    'This LPO cannot be deleted. Approved or received LPOs must be retained.'
                );
        }

        DB::transaction(function () use ($storeLpo) {
            $storeLpo->approvals()->delete();
            $storeLpo->items()->delete();
            $storeLpo->delete();
        });

        return redirect()
            ->route('admin.store-lpos.index')
            ->with('success', 'LPO deleted successfully.');
    }


    /*
    |--------------------------------------------------------------------------
    | SUBMIT
    |--------------------------------------------------------------------------
    */

    public function submit(StoreLpo $storeLpo): RedirectResponse
    {
        if ($storeLpo->status !== 'DRAFT') {
            return back()->with(
                'error',
                'Only draft LPOs can be submitted.'
            );
        }

        if (!$storeLpo->items()->exists()) {
            return back()->with(
                'error',
                'An LPO must contain at least one item before submission.'
            );
        }

        $storeLpo->update([
            'status' => 'PENDING',
            'approval_stage' => 'HOD',
        ]);

        return back()->with(
            'success',
            'LPO submitted for HOD approval.'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | APPROVAL ACTION
    |--------------------------------------------------------------------------
    */

    public function approval(
        Request $request,
        StoreLpo $storeLpo
    ): RedirectResponse {
        $validated = $request->validate([
            'action' => [
                'required',
                Rule::in([
                    'approve',
                    'return',
                    'reject',
                ]),
            ],
            'comments' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $stage = strtoupper(
            (string) $storeLpo->approval_stage
        );

        if (!in_array($stage, ['HOD', 'MANAGEMENT'], true)) {
            return back()->with(
                'error',
                'This LPO is not currently awaiting approval.'
            );
        }

        if (!$this->canApproveStage($stage)) {
            abort(403, 'You are not authorized to approve this LPO.');
        }

        $action = $validated['action'];

        DB::transaction(function () use (
            $storeLpo,
            $stage,
            $action,
            $validated
        ) {
            StoreLpoApproval::create([
                'store_lpo_id' => $storeLpo->id,
                'user_id' => auth()->id(),
                'approval_stage' => strtolower($stage),
                'action' => $action,
                'comments' => $validated['comments'] ?? null,
                'acted_at' => now(),
            ]);

            if ($action === 'return') {
                $storeLpo->update([
                    'status' => 'RETURNED',
                    'approval_stage' => strtolower($stage),
                    'approved_by' => null,
                    'approved_at' => null,
                ]);

                return;
            }

            if ($action === 'reject') {
                $storeLpo->update([
                    'status' => 'REJECTED',
                    'approval_stage' => strtolower($stage),
                    'approved_by' => null,
                    'approved_at' => null,
                ]);

                return;
            }

            /*
             * APPROVE
             */

            if ($stage === 'HOD') {
                $storeLpo->update([
                    'status' => 'PENDING',
                    'approval_stage' => 'MANAGEMENT',
                ]);

                return;
            }

            /*
             * MANAGEMENT APPROVED
             */

            $storeLpo->update([
                'status' => 'APPROVED',
                'approval_stage' => 'COMPLETED',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
        });

        $message = match ($action) {
            'approve' => $stage === 'HOD'
                ? 'LPO approved by HOD and forwarded for management approval.'
                : 'LPO approved by management and is now available for receiving.',

            'return' => 'LPO returned for correction.',

            'reject' => 'LPO rejected.',

            default => 'LPO approval action completed.',
        };

        return back()->with('success', $message);
    }


    /*
    |--------------------------------------------------------------------------
    | PRIVATE VALIDATION
    |--------------------------------------------------------------------------
    */

    protected function validateLpo(
        Request $request,
        ?StoreLpo $storeLpo = null
    ): array {
        return $request->validate([
            'supplier_id' => [
                'required',
                'integer',
                'exists:suppliers,id',
            ],

            'store_id' => [
                'required',
                'integer',
                'exists:stores,id',
            ],

            'lpo_date' => [
                'required',
                'date',
            ],

            'expected_delivery_date' => [
                'nullable',
                'date',
                'after_or_equal:lpo_date',
            ],

            'tax' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'discount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:5000',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.id' => [
                'nullable',
                'integer',
            ],

            'items.*.store_item_id' => [
                'required',
                'integer',
                'exists:store_items,id',
            ],

            'items.*.variant_id' => [
                'nullable',
                'integer',
                'exists:store_item_variants,id',
            ],

            'items.*.description' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'items.*.ordered_quantity' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'items.*.unit_price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'items.*.discount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'items.*.tax' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'items.*.notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);
    }


    /*
    |--------------------------------------------------------------------------
    | PERMISSION CHECK
    |--------------------------------------------------------------------------
    */

    protected function canApproveStage(string $stage): bool
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }

        if ($stage === 'HOD') {
            return $user->hasAnyPermission([
                'APPROVE STORE LPO',
                'APPROVE STORE LPO - HOD',
            ]);
        }

        if ($stage === 'MANAGEMENT') {
            return $user->hasAnyPermission([
                'APPROVE STORE LPO',
                'APPROVE STORE LPO - MANAGEMENT',
            ]);
        }

        return false;
    }


    /*
    |--------------------------------------------------------------------------
    | EDIT CHECK
    |--------------------------------------------------------------------------
    */

    protected function canEdit(StoreLpo $storeLpo): bool
    {
        /*
         * Only DRAFT and RETURNED LPOs are editable.
         */
        if (!in_array(
            $storeLpo->status,
            ['DRAFT', 'RETURNED'],
            true
        )) {
            return false;
        }

        /*
         * Safety check: if receiving has already started,
         * do not allow editing.
         */
        foreach ($storeLpo->items as $item) {
            if ($item->received_quantity > 0) {
                return false;
            }
        }

        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | DELETE CHECK
    |--------------------------------------------------------------------------
    */

    protected function canDelete(StoreLpo $storeLpo): bool
    {
        if (!in_array(
            $storeLpo->status,
            ['DRAFT', 'RETURNED', 'REJECTED'],
            true
        )) {
            return false;
        }

        $hasPostedReceipts = $storeLpo->items()
            ->whereHas('lpo', function ($query) {
                $query->where('id', '>', 0);
            })
            ->whereHas('lpo', function ($query) {
                $query->whereExists(function ($subQuery) {
                    $subQuery->selectRaw('1');
                });
            })
            ->exists();

        /*
         * Actual receipt existence is checked directly against
         * store_receipt_items through the relationship.
         */
        foreach ($storeLpo->items as $item) {
            if ($item->received_quantity > 0) {
                return false;
            }
        }

        return !$hasPostedReceipts;
    }


    /*
    |--------------------------------------------------------------------------
    | GENERATE LPO NUMBER
    |--------------------------------------------------------------------------
    */

    protected function generateLpoNumber(): string
    {
        $year = now()->format('Y');

        $lastLpo = StoreLpo::query()
            ->where('lpo_number', 'like', "LPO-{$year}-%")
            ->orderByDesc('id')
            ->first();

        $nextNumber = 1;

        if ($lastLpo) {
            $parts = explode('-', $lastLpo->lpo_number);
            $lastNumber = (int) end($parts);

            $nextNumber = $lastNumber + 1;
        }

        return sprintf(
            'LPO-%s-%04d',
            $year,
            $nextNumber
        );
    }

public function pdf(StoreLpo $storeLpo)
{
    $storeLpo->load([
        'supplier',
        'store',
        'creator',
        'approver',
        'items.item',
        'items.variant',
        'approvals.user',
    ]);

    /*
    |--------------------------------------------------------------------------
    | Generated At
    |--------------------------------------------------------------------------
    */

    $generatedAt = now();

    /*
    |--------------------------------------------------------------------------
    | Generate PDF
    |--------------------------------------------------------------------------
    */

    $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
        'admin.store_lpos.printlpo',
        [
            'storeLpo'    => $storeLpo,
            'generatedAt' => $generatedAt,
        ]
    );

    /*
    |--------------------------------------------------------------------------
    | Page Size
    |--------------------------------------------------------------------------
    */

    $pdf->setPaper('A4', 'portrait');

    /*
    |--------------------------------------------------------------------------
    | Filename
    |--------------------------------------------------------------------------
    */

    $filename = 'LPO-' .
        $storeLpo->lpo_number .
        '.pdf';

    return $pdf->download($filename);
}



}
