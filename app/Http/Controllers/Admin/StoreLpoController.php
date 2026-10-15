<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\StoreItem;
use App\Models\StoreItemVariant;
use App\Models\StoreLpo;
use App\Models\StoreLpoApproval;
use App\Models\Supplier;
use App\Models\User;
use App\Services\WorkflowNotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
            ->with(['supplier', 'store', 'creator'])
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

        $lpo = DB::transaction(function () use ($validated) {
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

            $lpo = StoreLpo::create([
                'lpo_number' => $this->generateLpoNumber(),
                'supplier_id' => $validated['supplier_id'],
                'store_id' => $validated['store_id'],
                'lpo_date' => $validated['lpo_date'],
                'expected_delivery_date' =>
                    $validated['expected_delivery_date'] ?? null,
                'status' => 'DRAFT',
                'approval_stage' => 'hod',
                'subtotal' => $subtotal,
                'tax' => $headerTax,
                'discount' => $headerDiscount,
                'total' => $total,
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

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
                $unitPrice = (float) $item['unit_price'];
                $discount = (float) ($item['discount'] ?? 0);
                $tax = (float) ($item['tax'] ?? 0);

                $lineTotal = max(
                    0,
                    ($quantity * $unitPrice) - $discount + $tax
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

            return $lpo;
        });

        // Draft creation intentionally does not notify approvers.

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

        $validated = $this->validateLpo($request, $storeLpo);

        DB::transaction(function () use ($validated, $storeLpo) {
            $storeLpo->load('items');

            $existingReceived = [];

            foreach ($storeLpo->items as $existingItem) {
                $existingReceived[$existingItem->id] =
                    (float) $existingItem->received_quantity;
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
                'expected_delivery_date' =>
                    $validated['expected_delivery_date'] ?? null,
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
                $unitPrice = (float) $item['unit_price'];
                $discount = (float) ($item['discount'] ?? 0);
                $tax = (float) ($item['tax'] ?? 0);

                $description = $item['description'] ?? null;

                if (!$description) {
                    $description = $variant
                        ? $storeItem->name . ' - ' . $variant->name
                        : $storeItem->name;
                }

                $lineData = [
                    'store_item_id' => $storeItem->id,
                    'variant_id' => $variant?->id,
                    'description' => $description,
                    'ordered_quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'discount' => $discount,
                    'tax' => $tax,
                    'line_total' => max(
                        0,
                        ($quantity * $unitPrice) - $discount + $tax
                    ),
                    'notes' => $item['notes'] ?? null,
                ];

                $lpoItem = null;

                if (!empty($item['id'])) {
                    $lpoItem = $storeLpo->items()
                        ->where('id', $item['id'])
                        ->first();
                }

                if ($lpoItem) {
                    $received = $existingReceived[$lpoItem->id] ?? 0;

                    if ($quantity < $received) {
                        abort(
                            422,
                            'Ordered quantity cannot be less than the quantity already received for an LPO item.'
                        );
                    }

                    $lpoItem->update($lineData);
                    $submittedItemIds[] = $lpoItem->id;
                } else {
                    $newItem = $storeLpo->items()->create($lineData);
                    $submittedItemIds[] = $newItem->id;
                }
            }

            foreach ($storeLpo->items()->get() as $existingItem) {
                if (!in_array(
                    $existingItem->id,
                    $submittedItemIds,
                    true
                )) {
                    if ((float) $existingItem->received_quantity > 0) {
                        abort(
                            422,
                            'An LPO item that has already been received cannot be removed.'
                        );
                    }

                    $existingItem->delete();
                }
            }

            if ($storeLpo->status === 'RETURNED') {
                $storeLpo->update([
                    'status' => 'DRAFT',
                    'approval_stage' => 'hod',
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

    $storeLpo->refresh();

    // Notify users authorised to approve at HOD stage.
    $this->notifyLpoUsers(
        $storeLpo,
        ['APPROVE STORE LPO - HOD'],
        'store_lpo_submitted',
        'LPO Awaiting HOD Approval',
        'LPO ' . $storeLpo->lpo_number .
            ' has been submitted and requires HOD approval.'
    );

    return back()->with(
        'success',
        'LPO submitted for HOD approval. The authorised HOD approvers have been notified.'
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
            Rule::in(['approve', 'return', 'reject']),
        ],
        'comments' => [
            'required_if:action,return,reject',
            'nullable',
            'string',
            'min:3',
            'max:2000',
        ],
    ], [
        'comments.required_if' =>
            'Please provide an explanation when returning or rejecting an LPO.',
        'comments.min' =>
            'The explanation must contain at least 3 characters.',
    ]);

    $stage = strtoupper(trim(
        (string) $storeLpo->approval_stage
    ));

    $status = strtoupper(trim(
        (string) $storeLpo->status
    ));

    if (
        $status !== 'PENDING' ||
        !in_array($stage, ['HOD', 'MANAGEMENT'], true)
    ) {
        return back()->with(
            'error',
            'This LPO is not currently awaiting approval.'
        );
    }

    if (!$this->canApproveStage($stage)) {
        abort(
            403,
            'You are not authorised to approve this LPO at the current stage.'
        );
    }

    $action = $validated['action'];
    $comments = trim((string) ($validated['comments'] ?? ''));

    DB::transaction(function () use (
        $storeLpo,
        $stage,
        $action,
        $comments
    ) {
        // Lock the LPO to prevent simultaneous approval actions.
        $lpo = StoreLpo::query()
            ->whereKey($storeLpo->id)
            ->lockForUpdate()
            ->firstOrFail();

        if (
            strtoupper(trim((string) $lpo->status)) !== 'PENDING' ||
            strtoupper(trim((string) $lpo->approval_stage)) !== $stage
        ) {
            abort(
                422,
                'The LPO stage has changed. Refresh the page and try again.'
            );
        }

        StoreLpoApproval::create([
            'store_lpo_id' => $lpo->id,
            'user_id' => auth()->id(),
            'approval_stage' => strtolower($stage),
            'action' => $action,
            'comments' => $comments !== '' ? $comments : null,
            'acted_at' => now(),
        ]);

        if ($action === 'return') {
            $lpo->update([
                'status' => 'RETURNED',
                'approval_stage' => strtolower($stage),
                'approved_by' => null,
                'approved_at' => null,
            ]);

            return;
        }

        if ($action === 'reject') {
            $lpo->update([
                'status' => 'REJECTED',
                'approval_stage' => strtolower($stage),
                'approved_by' => null,
                'approved_at' => null,
            ]);

            return;
        }

        if ($stage === 'HOD') {
            $lpo->update([
                'status' => 'PENDING',
                'approval_stage' => 'MANAGEMENT',
            ]);

            return;
        }

        $lpo->update([
            'status' => 'APPROVED',
            'approval_stage' => 'COMPLETED',
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
    });

    // Reload the saved workflow state before building notifications.
    $storeLpo->refresh();
    $storeLpo->loadMissing('store');

    if ($action === 'return') {
        $this->notifyLpoCreator(
            $storeLpo,
            'store_lpo_returned',
            'LPO Returned for Correction',
            'LPO ' . $storeLpo->lpo_number .
                ' was returned at the ' . $stage .
                ' stage. Reason: ' . $comments
        );
    } elseif ($action === 'reject') {
        $this->notifyLpoCreator(
            $storeLpo,
            'store_lpo_rejected',
            'LPO Rejected',
            'LPO ' . $storeLpo->lpo_number .
                ' was rejected at the ' . $stage .
                ' stage. Reason: ' . $comments
        );
    } elseif ($action === 'approve' && $stage === 'HOD') {
        // Notify management approvers because they are next.
        $this->notifyLpoUsers(
            $storeLpo,
            ['APPROVE STORE LPO - MANAGEMENT'],
            'store_lpo_hod_approved',
            'LPO Awaiting Management Approval',
            'LPO ' . $storeLpo->lpo_number .
                ' has been approved by HOD and requires management approval.'
        );

        // Notify the LPO initiator.
        $this->notifyLpoCreator(
            $storeLpo,
            'store_lpo_hod_approved',
            'LPO Approved by HOD',
            'LPO ' . $storeLpo->lpo_number .
                ' has passed HOD approval and has been forwarded to management.'
        );
    } elseif ($action === 'approve' && $stage === 'MANAGEMENT') {
        // Notify the initiator of final approval.
        $this->notifyLpoCreator(
            $storeLpo,
            'store_lpo_approved',
            'LPO Fully Approved',
            'LPO ' . $storeLpo->lpo_number .
                ' has been approved by management and is ready for receiving.'
        );

        // Notify users authorised to receive into this particular store.
        if ($storeLpo->store) {
            $receivingPermission = 'RECEIVE ITEMS TO ' .
                strtoupper(trim($storeLpo->store->name));

            $this->notifyLpoUsers(
                $storeLpo,
                [$receivingPermission],
                'store_lpo_ready_for_receiving',
                'Approved LPO Ready for Receiving',
                'LPO ' . $storeLpo->lpo_number .
                    ' has been approved. Items can now be received into ' .
                    $storeLpo->store->name . '.'
            );
        }
    }

    $message = match ($action) {
        'approve' => $stage === 'HOD'
            ? 'LPO approved by HOD and forwarded for management approval.'
            : 'LPO approved by management and is now available for receiving.',
        'return' => 'LPO returned for correction. The explanation has been recorded.',
        'reject' => 'LPO rejected. The explanation has been recorded.',
        default => 'LPO approval action completed.',
    };

    return back()->with('success', $message);
}


    /*
    |--------------------------------------------------------------------------
    | WORKFLOW NOTIFICATION HELPERS
    |--------------------------------------------------------------------------
    */

  
protected function notifyLpoUsers(
    StoreLpo $storeLpo,
    array $permissions,
    string $event,
    string $title,
    string $message
): void {
    try {
        // Find users with any of the specified permissions.
        $recipientIds = User::permission($permissions)
            ->where('id', '!=', auth()->id())
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        if (empty($recipientIds)) {
            Log::warning('No authorised recipients found for LPO notification.', [
                'lpo_id' => $storeLpo->id,
                'lpo_number' => $storeLpo->lpo_number,
                'event' => $event,
                'permissions' => $permissions,
            ]);

            return;
        }

        $sent = app(WorkflowNotificationService::class)->send(
            [
                'event' => $event,
                'title' => $title,
                'message' => $message,
                'url' => route('admin.store-lpos.show', $storeLpo),
                'icon' => asset('icons/teule-logo.png'),
                'priority' => 'normal',
                'metadata' => [
                    'store_lpo_id' => $storeLpo->id,
                    'lpo_number' => $storeLpo->lpo_number,
                    'status' => $storeLpo->status,
                    'approval_stage' => $storeLpo->approval_stage,
                ],
                'created_by' => auth()->id(),
            ],
            $recipientIds,
            ['database', 'webpush']
        );

        Log::info('LPO notification sent to authorised users.', [
            'lpo_id' => $storeLpo->id,
            'event' => $event,
            'permissions' => $permissions,
            'recipient_ids' => $recipientIds,
            'recipient_count' => $sent,
        ]);
    } catch (\Throwable $exception) {
        Log::error('Store LPO notification failed.', [
            'lpo_id' => $storeLpo->id,
            'event' => $event,
            'permissions' => $permissions,
            'error' => $exception->getMessage(),
        ]);
    }
}

protected function notifyLpoCreator(
    StoreLpo $storeLpo,
    string $event,
    string $title,
    string $message
): void {
    try {
        $creatorId = (int) $storeLpo->created_by;

        // Do not send an alert to the person performing the action.
        if (!$creatorId || $creatorId === (int) auth()->id()) {
            return;
        }

        $creatorExists = User::query()
            ->whereKey($creatorId)
            ->exists();

        if (!$creatorExists) {
            Log::warning('LPO creator not found for notification.', [
                'lpo_id' => $storeLpo->id,
                'creator_id' => $creatorId,
                'event' => $event,
            ]);

            return;
        }

        $sent = app(WorkflowNotificationService::class)->send(
            [
                'event' => $event,
                'title' => $title,
                'message' => $message,
                'url' => route('admin.store-lpos.show', $storeLpo),
                'icon' => asset('icons/teule-logo.png'),
                'priority' => 'normal',
                'metadata' => [
                    'store_lpo_id' => $storeLpo->id,
                    'lpo_number' => $storeLpo->lpo_number,
                    'status' => $storeLpo->status,
                    'approval_stage' => $storeLpo->approval_stage,
                ],
                'created_by' => auth()->id(),
            ],
            [$creatorId],
            ['database', 'webpush']
        );

        Log::info('LPO creator notification sent.', [
            'lpo_id' => $storeLpo->id,
            'event' => $event,
            'creator_id' => $creatorId,
            'recipient_count' => $sent,
        ]);
    } catch (\Throwable $exception) {
        Log::error('Store LPO creator notification failed.', [
            'lpo_id' => $storeLpo->id,
            'creator_id' => $storeLpo->created_by,
            'event' => $event,
            'error' => $exception->getMessage(),
        ]);
    }
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
        return $user->can('APPROVE STORE LPO - HOD');
    }

    if ($stage === 'MANAGEMENT') {
        return $user->can('APPROVE STORE LPO - MANAGEMENT');
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
        if (!in_array(
            $storeLpo->status,
            ['DRAFT', 'RETURNED'],
            true
        )) {
            return false;
        }

        $storeLpo->loadMissing('items');

        foreach ($storeLpo->items as $item) {
            if ((float) $item->received_quantity > 0) {
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

        $storeLpo->loadMissing('items');

        foreach ($storeLpo->items as $item) {
            if ((float) $item->received_quantity > 0) {
                return false;
            }
        }

        return true;
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

        return sprintf('LPO-%s-%04d', $year, $nextNumber);
    }

    /*
    |--------------------------------------------------------------------------
    | PDF
    |--------------------------------------------------------------------------
    */

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

        $generatedAt = now();

        $pdf = Pdf::loadView(
            'admin.store_lpos.printlpo',
            [
                'storeLpo' => $storeLpo,
                'generatedAt' => $generatedAt,
            ]
        );

        $pdf->setPaper('A4', 'portrait');

        $filename = 'LPO-' . $storeLpo->lpo_number . '.pdf';

        return $pdf->download($filename);
    }
}