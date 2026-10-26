<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\Department;
use App\Models\Store;
use App\Models\StoreItem;
use App\Models\StoreItemVariant;
use App\Models\StoreRequisition;
use App\Models\StoreRequisitionApproval;
use App\Models\StoreStock;
use App\Models\User;
use App\Services\WorkflowNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Throwable;

class StoreRequisitionController extends Controller
{
    /**
     * Display requisitions.
     *
     * Requesters see their own requisitions.
     * Users with any stage-specific approval permission can see all.
     */
    public function index(Request $request)
    {
        $query = StoreRequisition::query()
            ->with([
                'requester:id,name',
                'items.item:id,name',
                'sourceStore:id,name',
                'destinationStore:id,name',
            ]);

        $canViewAllRequisitions =
            auth()->user()->can('APPROVE STORE REQUISITIONS - HOD') ||
            auth()->user()->can('APPROVE STORE REQUISITIONS - MANAGEMENT') ||
            auth()->user()->can('APPROVE STORE REQUISITIONS - STORES');

        if (!$canViewAllRequisitions) {
            $query->where('requested_by', auth()->id());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('requisition_number', 'like', '%' . $search . '%')
                    ->orWhere('department', 'like', '%' . $search . '%')
                    ->orWhere('purpose', 'like', '%' . $search . '%');
            });
        }

        $requisitions = $query
            ->latest('created_at')
            ->paginate(15)
            ->withQueryString();

        return view(
            'admin.stores.store-requisitions.index',
            compact('requisitions')
        );
    }

    /**
     * Show the create requisition form.
     */

public function create()
{
    $items = StoreItem::with([
        'variants',
        'unit',
    ])
        ->where('is_active', true)
        ->orderBy('name')
        ->get();

    // Load stock records for all stores and variants.
    $stockRows = StoreStock::query()
        ->select([
            'store_id',
            'store_item_id',
            'variant_id',
            'quantity',
        ])
        ->get();

    foreach ($items as $item) {

        // Base-item balances, excluding variant stock.
        $item->store_balances = $stockRows
            ->where('store_item_id', $item->id)
            ->whereNull('variant_id')
            ->groupBy('store_id')
            ->map(function ($rows) {
                return (float) $rows->sum('quantity');
            })
            ->all();

        // Keep the existing property for compatibility.
        $item->current_balance = (float) array_sum(
            $item->store_balances
        );

        // Give each variant its own per-store balance map.
        foreach ($item->variants as $variant) {

            $variant->store_balances = $stockRows
                ->where('store_item_id', $item->id)
                ->where('variant_id', $variant->id)
                ->groupBy('store_id')
                ->map(function ($rows) {
                    return (float) $rows->sum('quantity');
                })
                ->all();
        }
    }

    $children = Child::orderBy('name', 'asc')->get();

    $departments = Department::where('is_active', true)
        ->orderBy('name')
        ->get();

    $stores = Store::query()
        ->where('is_active', true)
        ->orderBy('name')
        ->get();

    return view(
        'admin.stores.store-requisitions.create',
        compact(
            'items',
            'children',
            'stores',
            'departments'
        )
    );
}


    /**
     * Show the edit requisition form.
     */

public function edit(StoreRequisition $storeRequisition)
{
    if ((int) $storeRequisition->requested_by !== (int) auth()->id()) {
        abort(403);
    }

    if (!in_array($storeRequisition->status, ['draft', 'returned'], true)) {
        return redirect()
            ->route('admin.stores.store-requisitions.show', $storeRequisition)
            ->with('error', 'Only draft or returned requisitions can be edited.');
    }

    $items = StoreItem::with([
        'unit',
        'variants',
    ])
        ->where('is_active', true)
        ->orderBy('name')
        ->get();

    $children = Child::orderBy('name')->get();

    $stores = Store::where('is_active', true)
        ->orderBy('name')
        ->get();

    $storeRequisition->load([
        'items.item.unit',
        'items.item.variants',
        'items.variant',
        'items.children',
    ]);

    return view(
        'admin.stores.store-requisitions.edit',
        compact(
            'storeRequisition',
            'items',
            'children',
            'stores'
        )
    );
}



    /**
     * Update an existing draft/returned requisition.
     */
    public function update(Request $request, StoreRequisition $storeRequisition)
    {
        if ((int) $storeRequisition->requested_by !== (int) auth()->id()) {
            abort(403);
        }

        if (!in_array($storeRequisition->status, ['draft', 'returned'], true)) {
            return redirect()
                ->route('admin.stores.store-requisitions.index')
                ->with('error', 'Only draft or returned requisitions can be edited.');
        }

        $validated = $request->validate([
            'requisition_type' => [
                'required',
                'in:ITEM,TRANSFER',
            ],

            'source_store_id' => [
                'required',
                'integer',
                'exists:stores,id',
            ],

            'destination_store_id' => [
                'nullable',
                'integer',
                'exists:stores,id',
                Rule::requiredIf(
                    fn() => $request->input('requisition_type') === 'TRANSFER'
                ),
                'different:source_store_id',
            ],

            'department' => [
                'required',
                'string',
                'max:255',
            ],

            'purpose' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'submission_notes' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
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

                function ($attribute, $value, $fail) use ($request) {
                    if (!$value) {
                        return;
                    }

                    preg_match('/items\.(\d+)\.variant_id/', $attribute, $matches);

                    if (!isset($matches[1])) {
                        return;
                    }

                    $index = $matches[1];

                    $storeItemId = $request->input(
                        "items.{$index}.store_item_id"
                    );

                    $belongsToItem = StoreItemVariant::where('id', $value)
                        ->where('store_item_id', $storeItemId)
                        ->exists();

                    if (!$belongsToItem) {
                        $fail(
                            'The selected variant does not belong to the selected item.'
                        );
                    }
                },
            ],

            'items.*.requested_quantity' => [
                'required',
                'numeric',
                'min:0.001',
            ],

            'items.*.notes' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'items.*.child_ids' => [
                'nullable',
                'array',
            ],

            'items.*.child_ids.*' => [
                'integer',
                'exists:children,id',
            ],
        ]);

        DB::transaction(function () use ($validated, $storeRequisition) {
            $storeRequisition->update([
                'department' => $validated['department'],
                'purpose' => $validated['purpose'],
                'submission_notes' => $validated['submission_notes'] ?? null,
                'requisition_type' => $validated['requisition_type'],
                'source_store_id' => $validated['source_store_id'],
                'status'=>'pending',
                'approval_stage'=>'hod',

                'destination_store_id' =>
                    $validated['requisition_type'] === 'TRANSFER'
                        ? ($validated['destination_store_id'] ?? null)
                        : null,
            ]);

            $storeRequisition->items()->delete();

            foreach ($validated['items'] as $item) {
                if (!empty($item['variant_id'])) {
                    $variantBelongsToItem = StoreItemVariant::where(
                        'id',
                        $item['variant_id']
                    )
                        ->where('store_item_id', $item['store_item_id'])
                        ->exists();

                    if (!$variantBelongsToItem) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'items' => [
                                'One of the selected variants does not belong to its selected item.',
                            ],
                        ]);
                    }
                }

                $requisitionItem = $storeRequisition->items()->create([
                    'store_item_id' => $item['store_item_id'],
                    'variant_id' => $item['variant_id'] ?? null,
                    'requested_quantity' => $item['requested_quantity'],
                    'approved_quantity' => 0,
                    'issued_quantity' => 0,
                    'outstanding_quantity' => 0,
                    'notes' => $item['notes'] ?? null,
                ]);

                if (!empty($item['child_ids'])) {
                    $requisitionItem->children()->sync($item['child_ids']);
                }
            }
        });

        return redirect()
            ->route('admin.stores.store-requisitions.index')
            ->with(
                'success',
                'Requisition ' .
                    $storeRequisition->requisition_number .
                    ' updated successfully.'
            );
    }

    /**
     * Save a new requisition as a draft.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'requisition_type' => [
                'required',
                'in:ITEM,TRANSFER',
            ],

            'source_store_id' => [
                'required',
                'integer',
                'exists:stores,id',
            ],

            'destination_store_id' => [
                'nullable',
                'integer',
                'exists:stores,id',
                Rule::requiredIf(
                    fn() => $request->input('requisition_type') === 'TRANSFER'
                ),
                'different:source_store_id',
            ],

            'department' => [
                'required',
                'string',
                'max:255',
            ],

            'purpose' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'submission_notes' => [
                'nullable',
                'string',
                'max:2000',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
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

                function ($attribute, $value, $fail) use ($request) {
                    if (!$value) {
                        return;
                    }

                    preg_match('/items\.(\d+)\.variant_id/', $attribute, $matches);

                    if (!isset($matches[1])) {
                        return;
                    }

                    $index = $matches[1];

                    $storeItemId = $request->input(
                        "items.{$index}.store_item_id"
                    );

                    $belongsToItem = StoreItemVariant::where('id', $value)
                        ->where('store_item_id', $storeItemId)
                        ->exists();

                    if (!$belongsToItem) {
                        $fail(
                            'The selected variant does not belong to the selected item.'
                        );
                    }
                },
            ],

            'items.*.requested_quantity' => [
                'required',
                'numeric',
                'min:0.001',
            ],

            'items.*.notes' => [
                'nullable',
                'string',
                'max:1000',
            ],

            'items.*.child_ids' => [
                'nullable',
                'array',
            ],

            'items.*.child_ids.*' => [
                'integer',
                'exists:children,id',
            ],
        ]);

        $requisition = null;

        DB::transaction(function () use ($validated, &$requisition) {
            $requisition = StoreRequisition::create([
                'requisition_number' => $this->generateRequisitionNumber(),
                'requested_by' => auth()->id(),
                'department' => $validated['department'],
                'purpose' => $validated['purpose'],
                'requisition_type' => $validated['requisition_type'],
                'source_store_id' => $validated['source_store_id'],

                'destination_store_id' =>
                    $validated['requisition_type'] === 'TRANSFER'
                        ? ($validated['destination_store_id'] ?? null)
                        : null,

                'status' => 'draft',
                'approval_stage' => 'none',
                'fulfillment_status' => 'not_issued',
                'submission_notes' => $validated['submission_notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                if (!empty($item['variant_id'])) {
                    $variantBelongsToItem = StoreItemVariant::where(
                        'id',
                        $item['variant_id']
                    )
                        ->where('store_item_id', $item['store_item_id'])
                        ->exists();

                    if (!$variantBelongsToItem) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'items' => [
                                'One of the selected variants does not belong to its selected item.',
                            ],
                        ]);
                    }
                }

                $requisitionItem = $requisition->items()->create([
                    'store_item_id' => $item['store_item_id'],
                    'variant_id' => $item['variant_id'] ?? null,
                    'requested_quantity' => $item['requested_quantity'],
                    'approved_quantity' => 0,
                    'issued_quantity' => 0,
                    'outstanding_quantity' => 0,
                    'notes' => $item['notes'] ?? null,
                ]);

                if (!empty($item['child_ids'])) {
                    $requisitionItem->children()->sync($item['child_ids']);
                }
            }
        });

        return redirect()
            ->route('admin.stores.store-requisitions.create')
            ->with(
                'success',
                'Requisition ' .
                    $requisition->requisition_number .
                    ' saved as draft successfully.'
            );
    }

    /**
     * Generate a unique requisition number.
     */
    protected function generateRequisitionNumber(): string
    {
        do {
            $number = 'REQ-' .
                now()->format('Ymd') .
                '-' .
                strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        } while (
            StoreRequisition::where('requisition_number', $number)->exists()
        );

        return $number;
    }

    /**
     * Get the permission required for the current approval stage.
     */
    protected function getApprovalPermissionForStage(
        StoreRequisition $storeRequisition
    ): ?string {
        return match (strtolower(trim($storeRequisition->approval_stage ?? ''))) {
            'hod' => 'APPROVE STORE REQUISITIONS - HOD',
            'management' => 'APPROVE STORE REQUISITIONS - MANAGEMENT',
            'stores' => 'APPROVE STORE REQUISITIONS - STORES',
            default => null,
        };
    }

    /**
     * Display a requisition.
     */
    public function show(StoreRequisition $storeRequisition)
    {
        $user = auth()->user();

        $isRequester =
            (int) $storeRequisition->requested_by === (int) $user->id;

        $canApproveHod = $user->can('APPROVE STORE REQUISITIONS - HOD');
        $canApproveManagement = $user->can('APPROVE STORE REQUISITIONS - MANAGEMENT');
        $canApproveStores = $user->can('APPROVE STORE REQUISITIONS - STORES');

        $approvalStage = strtolower(
            trim($storeRequisition->approval_stage ?? 'none')
        );

        $status = strtolower(trim($storeRequisition->status ?? 'draft'));

        $isPending = $status === 'pending';

        $canApproveCurrentStage = false;

        switch ($approvalStage) {
            case 'hod':
                $canApproveCurrentStage = $canApproveHod;
                break;

            case 'management':
                $canApproveCurrentStage = $canApproveManagement;
                break;

            case 'stores':
                $canApproveCurrentStage = $canApproveStores;
                break;

            default:
                $canApproveCurrentStage = false;
                break;
        }

        $showApprovalActions = $isPending && $canApproveCurrentStage;

        $canView =
            $isRequester ||
            $canApproveHod ||
            $canApproveManagement ||
            $canApproveStores;

        if (!$canView) {
            abort(403);
        }

        $stageLabel = match ($approvalStage) {
            'hod' => 'HOD Approval',
            'management' => 'Management Approval',
            'stores' => 'Stores Approval',
            'approved' => 'Fully Approved',
            'rejected' => 'Rejected',
            'returned' => 'Returned to Requester',
            default => 'Not Submitted',
        };

        $storeRequisition->load([
            'requester',
            'items.item.unit',
            'items.variant',
            'items.children',
            'approvals.approver',
            'sourceStore',
            'destinationStore',
            'fulfillments.processor',
            'fulfillments.sourceStore',
            'fulfillments.destinationStore',
            'fulfillments.items.item',
            'fulfillments.items.variant',
        ]);

        return view(
            'admin.stores.store-requisitions.show',
            compact(
                'storeRequisition',
                'showApprovalActions',
                'canApproveCurrentStage',
                'approvalStage',
                'stageLabel',
                'status',
                'isPending',
                'isRequester',
                'canApproveHod',
                'canApproveManagement',
                'canApproveStores'
            )
        );
    }

    /**
     * Submit a requisition for approval.
     */
    public function submit(StoreRequisition $storeRequisition)
    {
        if ((int) $storeRequisition->requested_by !== (int) auth()->id()) {
            abort(403);
        }

        if (!in_array($storeRequisition->status, ['draft', 'returned'], true)) {
            return redirect()
                ->route('admin.stores.store-requisitions.show', $storeRequisition)
                ->with(
                    'error',
                    'Only draft or returned requisitions can be submitted.'
                );
        }

        if ($storeRequisition->items()->count() === 0) {
            return redirect()
                ->route('admin.stores.store-requisitions.show', $storeRequisition)
                ->with(
                    'error',
                    'A requisition must contain at least one item before it can be submitted.'
                );
        }

        if (!$storeRequisition->source_store_id) {
            return redirect()
                ->route('admin.stores.store-requisitions.edit', $storeRequisition)
                ->with(
                    'error',
                    'Please select a source store before submitting the requisition.'
                );
        }

        if (
            $storeRequisition->requisition_type === 'TRANSFER' &&
            !$storeRequisition->destination_store_id
        ) {
            return redirect()
                ->route('admin.stores.store-requisitions.edit', $storeRequisition)
                ->with(
                    'error',
                    'Please select a destination store before submitting the transfer.'
                );
        }

        $storeRequisition->update([
            'status' => 'pending',
            'approval_stage' => 'hod',
            'submitted_at' => now(),
        ]);

        // Notify the HOD approvers after the requisition has been submitted.
        $this->notifyRequisitionStage(
            $storeRequisition,
            'APPROVE STORE REQUISITIONS - HOD',
            [
                'event' => 'store_requisition_submitted',
                'title' => 'Store Requisition Awaiting HOD Approval',
                'message' => 'Requisition ' .
                    $storeRequisition->requisition_number .
                    ' from ' . $storeRequisition->department .
                    ' has been submitted and is awaiting HOD approval.',
                'priority' => 'high',
            ]
        );

        return redirect()
            ->route('admin.stores.store-requisitions.show', $storeRequisition)
            ->with(
                'success',
                'Requisition ' .
                    $storeRequisition->requisition_number .
                    ' submitted successfully for approval.'
            );
    }

    /**
     * Approve the current approval stage.
     */
    public function approve(
        Request $request,
        StoreRequisition $storeRequisition
    ) {
        if ($storeRequisition->status !== 'pending') {
            return redirect()
                ->route('admin.stores.store-requisitions.show', $storeRequisition)
                ->with(
                    'error',
                    'This requisition is not currently awaiting approval.'
                );
        }

        $requiredPermission = $this->getApprovalPermissionForStage(
            $storeRequisition
        );

        if (!$requiredPermission) {
            return redirect()
                ->route('admin.stores.store-requisitions.show', $storeRequisition)
                ->with(
                    'error',
                    'This requisition is not currently awaiting approval.'
                );
        }

        abort_unless(auth()->user()->can($requiredPermission), 403);

        $validated = $request->validate([
            'comments' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $stage = strtolower(trim($storeRequisition->approval_stage));

        DB::transaction(function () use (
            $storeRequisition,
            $validated,
            $stage
        ) {
            StoreRequisitionApproval::create([
                'store_requisition_id' => $storeRequisition->id,
                'approved_by' => auth()->id(),
                'approval_level' => $stage,
                'decision' => 'approved',
                'comments' => $validated['comments'] ?? null,
                'decided_at' => now(),
            ]);

            if ($stage === 'hod') {
                $storeRequisition->update([
                    'status' => 'pending',
                    'approval_stage' => 'management',
                ]);
            } elseif ($stage === 'management') {
                $storeRequisition->update([
                    'status' => 'pending',
                    'approval_stage' => 'stores',
                ]);
            } elseif ($stage === 'stores') {
                $storeRequisition->load('items');

                foreach ($storeRequisition->items as $item) {
                    $requestedQuantity = (float) $item->requested_quantity;
                    $issuedQuantity = (float) $item->issued_quantity;

                    $outstandingQuantity = max(
                        0,
                        $requestedQuantity - $issuedQuantity
                    );

                    $item->update([
                        'approved_quantity' => $requestedQuantity,
                        'outstanding_quantity' => $outstandingQuantity,
                    ]);
                }

                $storeRequisition->update([
                    'status' => 'approved',
                    'approval_stage' => 'approved',
                    'approved_at' => now(),
                    'fulfillment_status' => 'not_issued',
                ]);
            }
        });

        // Notify the next stage, or the requester if approval is complete.
        $storeRequisition->refresh();

        if ($stage === 'hod') {
            $this->notifyRequisitionStage(
                $storeRequisition,
                'APPROVE STORE REQUISITIONS - MANAGEMENT',
                [
                    'event' => 'store_requisition_hod_approved',
                    'title' => 'Store Requisition Awaiting Management Approval',
                    'message' => 'Requisition ' .
                        $storeRequisition->requisition_number .
                        ' has passed HOD approval and is awaiting Management approval.',
                    'priority' => 'high',
                ]
            );
        } elseif ($stage === 'management') {
            $this->notifyRequisitionStage(
                $storeRequisition,
                'APPROVE STORE REQUISITIONS - STORES',
                [
                    'event' => 'store_requisition_management_approved',
                    'title' => 'Store Requisition Awaiting Stores Review',
                    'message' => 'Requisition ' .
                        $storeRequisition->requisition_number .
                        ' has passed Management approval and is awaiting Stores review.',
                    'priority' => 'high',
                ]
            );
        } elseif ($stage === 'stores') {
            $this->notifyRequisitionRequester(
                $storeRequisition,
                [
                    'event' => 'store_requisition_fully_approved',
                    'title' => 'Store Requisition Fully Approved',
                    'message' => 'Your requisition ' .
                        $storeRequisition->requisition_number .
                        ' has been fully approved and is ready for physical fulfillment.',
                    'priority' => 'high',
                ]
            );
        }

        $message = match ($stage) {
            'hod' =>
                'HOD approval recorded. The requisition is now awaiting CM/DCM approval.',
            'management' =>
                'Management approval recorded. The requisition is now awaiting Stores review.',
            'stores' =>
                'Stores approval recorded. The requisition is now fully approved and ready for physical fulfillment.',
            default => 'Requisition approved.',
        };

        return redirect()
            ->route('admin.stores.store-requisitions.show', $storeRequisition)
            ->with('success', $message);
    }

    /**
     * Reject a pending requisition.
     */
    public function reject(
        Request $request,
        StoreRequisition $storeRequisition
    ) {
        $requiredPermission = $this->getApprovalPermissionForStage(
            $storeRequisition
        );

        abort_unless(
            $requiredPermission && auth()->user()->can($requiredPermission),
            403
        );

        $validated = $request->validate([
            'comments' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);

        if ($storeRequisition->status !== 'pending') {
            return redirect()
                ->route('admin.stores.store-requisitions.show', $storeRequisition)
                ->with('error', 'Only pending requisitions can be rejected.');
        }

        DB::transaction(function () use ($storeRequisition, $validated) {
            StoreRequisitionApproval::create([
                'store_requisition_id' => $storeRequisition->id,
                'approved_by' => auth()->id(),
                'approval_level' => $storeRequisition->approval_stage,
                'decision' => 'rejected',
                'comments' => $validated['comments'],
                'decided_at' => now(),
            ]);

            $storeRequisition->update([
                'status' => 'rejected',
                'approval_stage' => 'rejected',
            ]);
        });

        // Notify the requester after the rejection has been saved.
        $this->notifyRequisitionRequester(
            $storeRequisition,
            [
                'event' => 'store_requisition_rejected',
                'title' => 'Store Requisition Rejected',
                'message' => 'Your requisition ' .
                    $storeRequisition->requisition_number .
                    ' has been rejected. Reason: ' .
                    $validated['comments'],
                'priority' => 'high',
            ]
        );

        return redirect()
            ->route('admin.stores.store-requisitions.show', $storeRequisition)
            ->with(
                'success',
                'Requisition ' .
                    $storeRequisition->requisition_number .
                    ' rejected.'
            );
    }

    /**
     * Send a pending requisition back to requester.
     */
    public function sendBack(
        Request $request,
        StoreRequisition $storeRequisition
    ) {
        $requiredPermission = $this->getApprovalPermissionForStage(
            $storeRequisition
        );

        abort_unless(
            $requiredPermission && auth()->user()->can($requiredPermission),
            403
        );

        $validated = $request->validate([
            'comments' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);

        if ($storeRequisition->status !== 'pending') {
            return redirect()
                ->route('admin.stores.store-requisitions.show', $storeRequisition)
                ->with(
                    'error',
                    'Only pending requisitions can be sent back for edits.'
                );
        }

        DB::transaction(function () use ($storeRequisition, $validated) {
            StoreRequisitionApproval::create([
                'store_requisition_id' => $storeRequisition->id,
                'approved_by' => auth()->id(),
                'approval_level' => $storeRequisition->approval_stage,
                'decision' => 'returned',
                'comments' => $validated['comments'],
                'decided_at' => now(),
            ]);

            $storeRequisition->update([
                'status' => 'returned',
                'approval_stage' => 'returned',
            ]);
        });

        // Notify the requester that action is required.
        $this->notifyRequisitionRequester(
            $storeRequisition,
            [
                'event' => 'store_requisition_returned',
                'title' => 'Store Requisition Returned for Editing',
                'message' => 'Your requisition ' .
                    $storeRequisition->requisition_number .
                    ' has been returned for edits. Comments: ' .
                    $validated['comments'],
                'priority' => 'high',
            ]
        );

        return redirect()
            ->route('admin.stores.store-requisitions.show', $storeRequisition)
            ->with(
                'success',
                'Requisition ' .
                    $storeRequisition->requisition_number .
                    ' has been sent back to the requester for edits.'
            );
    }

    /**
     * Delete a draft requisition.
     */
    public function destroy(StoreRequisition $storeRequisition)
    {
        if ((int) $storeRequisition->requested_by !== (int) auth()->id()) {
            abort(403);
        }

        if ($storeRequisition->status !== 'draft') {
            return redirect()
                ->route('admin.stores.store-requisitions.index')
                ->with('error', 'Only draft requisitions can be deleted.');
        }

        $requisitionNumber = $storeRequisition->requisition_number;

        DB::transaction(function () use ($storeRequisition) {
            $storeRequisition->items()->delete();
            $storeRequisition->delete();
        });

        return redirect()
            ->route('admin.stores.store-requisitions.index')
            ->with(
                'success',
                "Draft requisition {$requisitionNumber} was deleted successfully."
            );
    }

    /**
     * Cancel a requisition.
     */

public function cancel(
    Request $request,
    StoreRequisition $storeRequisition
) {
    $validated = $request->validate([
        'cancellation_reason' => [
            'required',
            'string',
            'max:1000',
        ],
    ]);

    if (in_array(
        $storeRequisition->status,
        ['cancelled', 'closed', 'completed'],
        true
    )) {
        return back()->with(
            'error',
            'This requisition can no longer be cancelled.'
        );
    }

    // Capture the approval stage before changing the status.
    $previousStagePermission = $this->getApprovalPermissionForStage(
        $storeRequisition
    );

    // Preserve the existing notes and append the cancellation reason.
    $existingNotes = trim($storeRequisition->submission_notes ?? '');
    $reason = trim($validated['cancellation_reason']);

    $updatedNotes = $existingNotes !== ''
        ? $existingNotes . "\n\nCancellation reason: " . $reason
        : 'Cancellation reason: ' . $reason;

    // Explicitly update and save the model.
    $storeRequisition->status = 'cancelled';
    $storeRequisition->submission_notes = $updatedNotes;
    $storeRequisition->save();

    // Reload the saved record from the database.
    $storeRequisition->refresh();

    // Verify that the status was actually persisted.
    if ($storeRequisition->status !== 'cancelled') {
        return back()->with(
            'error',
            'The cancellation status could not be saved. Please check the database status column.'
        );
    }

    // Notify approvers at the stage where the requisition was cancelled.
    if ($previousStagePermission) {
        $this->notifyRequisitionStage(
            $storeRequisition,
            $previousStagePermission,
            [
                'event' => 'store_requisition_cancelled',
                'title' => 'Store Requisition Cancelled',
                'message' => 'Requisition ' .
                    $storeRequisition->requisition_number .
                    ' has been cancelled. Reason: ' .
                    $reason,
                'priority' => 'normal',
            ],
            false
        );
    }

    return redirect()
        ->route(
            'admin.stores.store-requisitions.show',
            $storeRequisition
        )
        ->with(
            'success',
            'Store requisition cancelled successfully.'
        );
}




    /*
    |--------------------------------------------------------------------------
    | NOTIFICATION HELPERS
    |--------------------------------------------------------------------------
    |
    | These helpers use the existing WorkflowNotificationService.
    | Notifications use database and webpush channels.
    | Failures are logged so they do not interrupt the requisition workflow.
    |
    */

    /**
     * Notify the requester about a requisition event.
     */
    protected function notifyRequisitionRequester(
        StoreRequisition $storeRequisition,
        array $payload
    ): void {
        $this->sendRequisitionNotification(
            $storeRequisition,
            $payload,
            [(int) $storeRequisition->requested_by]
        );
    }

    /**
     * Notify the requester and users holding a particular stage permission.
     *
     * Set $includeRequester to false when only the stage approvers
     * should be notified.
     */
    protected function notifyRequisitionStage(
        StoreRequisition $storeRequisition,
        string $permission,
        array $payload,
        bool $includeRequester = true
    ): void {
        $recipientIds = User::permission($permission)
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();

        if ($includeRequester) {
            $recipientIds[] = (int) $storeRequisition->requested_by;
        }

        $this->sendRequisitionNotification(
            $storeRequisition,
            $payload,
            $recipientIds
        );
    }

    /**
     * Send a notification to unique recipients.
     */
    protected function sendRequisitionNotification(
        StoreRequisition $storeRequisition,
        array $payload,
        array $recipientIds
    ): void {
        $recipientIds = array_values(array_unique(array_filter(
            array_map('intval', $recipientIds),
            fn($id) => $id > 0
        )));

        if (empty($recipientIds)) {
            return;
        }

        $payload = array_merge([
            'url' => route(
                'admin.stores.store-requisitions.show',
                $storeRequisition
            ),

            'icon' => 'clipboard-check',
            'priority' => 'normal',
            'created_by' => auth()->id(),

            'metadata' => [
                'requisition_id' => $storeRequisition->id,
                'requisition_number' => $storeRequisition->requisition_number,
                'status' => $storeRequisition->status,
                'approval_stage' => $storeRequisition->approval_stage,
            ],
        ], $payload);

        try {
            app(WorkflowNotificationService::class)->send(
                $payload,
                $recipientIds,
                ['database', 'webpush']
            );
        } catch (Throwable $exception) {
            Log::warning('Store requisition notification failed.', [
                'requisition_id' => $storeRequisition->id,
                'requisition_number' => $storeRequisition->requisition_number,
                'recipient_ids' => $recipientIds,
                'event' => $payload['event'] ?? 'store_requisition',
                'error' => $exception->getMessage(),
            ]);
        }
    }
}