<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Child;
use App\Models\Store;
use App\Models\StoreItem;
use App\Models\StoreItemVariant;
use App\Models\StoreRequisition;
use App\Models\StoreRequisitionApproval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class StoreRequisitionController extends Controller
{
    /**
     * Display requisitions.
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

        /*
         * Requesters see only their own requisitions.
         * Users with approval permission can see all requisitions.
         */
        if (!auth()->user()->can('APPROVE STORE REQUISITIONS')) {
            $query->where('requested_by', auth()->id());
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where(
                    'requisition_number',
                    'like',
                    '%' . $search . '%'
                )
                ->orWhere(
                    'department',
                    'like',
                    '%' . $search . '%'
                )
                ->orWhere(
                    'purpose',
                    'like',
                    '%' . $search . '%'
                );
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

        $children = Child::orderBy('name', 'asc')
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
                'stores'
            )
        );
    }


    /**
     * Show the edit requisition form.
     */
public function edit(StoreRequisition $storeRequisition)
{
    /*
    |--------------------------------------------------------------------------
    | ONLY THE REQUESTER CAN EDIT
    |--------------------------------------------------------------------------
    */

    if ((int) $storeRequisition->requested_by !== (int) auth()->id()) {
        abort(403);
    }


    /*
    |--------------------------------------------------------------------------
    | DRAFT AND RETURNED REQUISITIONS CAN BE EDITED
    |--------------------------------------------------------------------------
    */

    if (!in_array($storeRequisition->status, ['draft', 'returned'], true)) {
        return redirect()
            ->route(
                'admin.stores.store-requisitions.show',
                $storeRequisition
            )
            ->with(
                'error',
                'Only draft or returned requisitions can be edited.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | AVAILABLE STORE ITEMS
    |--------------------------------------------------------------------------
    */

    $items = StoreItem::with([
        'unit',
        'variants',
    ])
        ->where('is_active', true)
        ->orderBy('name')
        ->get();


    /*
    |--------------------------------------------------------------------------
    | CHILDREN
    |--------------------------------------------------------------------------
    */

    $children = Child::orderBy('name')->get();


    /*
    |--------------------------------------------------------------------------
    | STORES
    |--------------------------------------------------------------------------
    */

    $stores = Store::where('is_active', true)
        ->orderBy('name')
        ->get();


    /*
    |--------------------------------------------------------------------------
    | LOAD EXISTING REQUISITION DATA
    |--------------------------------------------------------------------------
    */

    $storeRequisition->load([
        'items.item',
        'items.variant',
        'items.children',
    ]);


    /*
    |--------------------------------------------------------------------------
    | EDIT VIEW
    |--------------------------------------------------------------------------
    */

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
    public function update(
        Request $request,
        StoreRequisition $storeRequisition
    ) {
        if (
            (int) $storeRequisition->requested_by !==
            (int) auth()->id()
        ) {
            abort(403);
        }

        if (
            !in_array(
                $storeRequisition->status,
                ['draft', 'returned'],
                true
            )
        ) {
            return redirect()
                ->route(
                    'admin.stores.store-requisitions.index'
                )
                ->with(
                    'error',
                    'Only draft or returned requisitions can be edited.'
                );
        }

        $validated = $request->validate([

            /*
             * ----------------------------------------------------------
             * Requisition routing
             * ----------------------------------------------------------
             */

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
                    fn () =>
                        $request->input('requisition_type') === 'TRANSFER'
                ),
                'different:source_store_id',
            ],

            /*
             * ----------------------------------------------------------
             * Basic requisition information
             * ----------------------------------------------------------
             */

            'department' => [
                'required',
                'string',
                'max:255',
            ],

            'purpose' => [
                'required',
                'string',
                'max:2000',
            ],

            'submission_notes' => [
                'nullable',
                'string',
                'max:2000',
            ],

            /*
             * ----------------------------------------------------------
             * Items
             * ----------------------------------------------------------
             */

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

                function (
                    $attribute,
                    $value,
                    $fail
                ) use ($request) {

                    if (!$value) {
                        return;
                    }

                    preg_match(
                        '/items\.(\d+)\.variant_id/',
                        $attribute,
                        $matches
                    );

                    if (!isset($matches[1])) {
                        return;
                    }

                    $index = $matches[1];

                    $storeItemId = $request->input(
                        "items.{$index}.store_item_id"
                    );

                    $belongsToItem =
                        StoreItemVariant::where(
                            'id',
                            $value
                        )
                        ->where(
                            'store_item_id',
                            $storeItemId
                        )
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

        DB::transaction(function () use (
            $validated,
            $storeRequisition
        ) {

            /*
             * ----------------------------------------------------------
             * Update requisition header
             * ----------------------------------------------------------
             */

            $storeRequisition->update([

                'department' =>
                    $validated['department'],

                'purpose' =>
                    $validated['purpose'],

                'submission_notes' =>
                    $validated['submission_notes'] ?? null,

                'requisition_type' =>
                    $validated['requisition_type'],

                'source_store_id' =>
                    $validated['source_store_id'],

                'destination_store_id' =>
                    $validated['requisition_type'] === 'TRANSFER'
                        ? (
                            $validated['destination_store_id']
                            ?? null
                        )
                        : null,
            ]);


            /*
             * ----------------------------------------------------------
             * Rebuild item lines
             *
             * This is safe because the requisition is still
             * draft/returned and has not been approved.
             * ----------------------------------------------------------
             */

            $storeRequisition->items()->delete();

            foreach ($validated['items'] as $item) {

                /*
                 * Double-check variant belongs to selected item.
                 */
                if (!empty($item['variant_id'])) {

                    $variantBelongsToItem =
                        StoreItemVariant::where(
                            'id',
                            $item['variant_id']
                        )
                        ->where(
                            'store_item_id',
                            $item['store_item_id']
                        )
                        ->exists();

                    if (!$variantBelongsToItem) {
                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'items' => [
                                'One of the selected variants does not belong to its selected item.',
                            ],
                        ]);
                    }
                }


                $requisitionItem =
                    $storeRequisition->items()->create([

                        'store_item_id' =>
                            $item['store_item_id'],

                        'variant_id' =>
                            $item['variant_id'] ?? null,

                        'requested_quantity' =>
                            $item['requested_quantity'],

                        /*
                         * Before final approval:
                         * approved = 0
                         * issued = 0
                         * outstanding = 0
                         */
                        'approved_quantity' => 0,

                        'issued_quantity' => 0,

                        'outstanding_quantity' => 0,

                        'notes' =>
                            $item['notes'] ?? null,
                    ]);


                /*
                 * Attach children where provided.
                 */
                if (!empty($item['child_ids'])) {
                    $requisitionItem->children()->sync(
                        $item['child_ids']
                    );
                }
            }
        });

        return redirect()
            ->route(
                'admin.stores.store-requisitions.index'
            )
            ->with(
                'success',
                'Requisition ' .
                $storeRequisition->requisition_number .
                ' updated successfully.'
            );
    }


    /**
     * Save a new requisition as a draft.
     *
     * No stock is checked or deducted here.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            /*
             * ----------------------------------------------------------
             * Requisition routing
             * ----------------------------------------------------------
             */

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
                    fn () =>
                        $request->input('requisition_type') === 'TRANSFER'
                ),
                'different:source_store_id',
            ],

            /*
             * ----------------------------------------------------------
             * Basic requisition information
             * ----------------------------------------------------------
             */

            'department' => [
                'required',
                'string',
                'max:255',
            ],

            'purpose' => [
                'required',
                'string',
                'max:2000',
            ],

            'submission_notes' => [
                'nullable',
                'string',
                'max:2000',
            ],

            /*
             * ----------------------------------------------------------
             * Items
             * ----------------------------------------------------------
             */

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

                function (
                    $attribute,
                    $value,
                    $fail
                ) use ($request) {

                    if (!$value) {
                        return;
                    }

                    preg_match(
                        '/items\.(\d+)\.variant_id/',
                        $attribute,
                        $matches
                    );

                    if (!isset($matches[1])) {
                        return;
                    }

                    $index = $matches[1];

                    $storeItemId = $request->input(
                        "items.{$index}.store_item_id"
                    );

                    $belongsToItem =
                        StoreItemVariant::where(
                            'id',
                            $value
                        )
                        ->where(
                            'store_item_id',
                            $storeItemId
                        )
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


        DB::transaction(function () use (
            $validated,
            &$requisition
        ) {

            /*
             * ----------------------------------------------------------
             * Create requisition header
             * ----------------------------------------------------------
             */

            $requisition =
                StoreRequisition::create([

                    'requisition_number' =>
                        $this->generateRequisitionNumber(),

                    'requested_by' =>
                        auth()->id(),

                    'department' =>
                        $validated['department'],

                    'purpose' =>
                        $validated['purpose'],

                    /*
                     * Requisition routing
                     */
                    'requisition_type' =>
                        $validated['requisition_type'],

                    'source_store_id' =>
                        $validated['source_store_id'],

                    'destination_store_id' =>
                        $validated['requisition_type'] === 'TRANSFER'
                            ? (
                                $validated['destination_store_id']
                                ?? null
                            )
                            : null,

                    /*
                     * Workflow
                     */
                    'status' => 'draft',

                    'approval_stage' => 'none',

                    'fulfillment_status' => 'not_issued',

                    'submission_notes' =>
                        $validated['submission_notes'] ?? null,
                ]);


            /*
             * ----------------------------------------------------------
             * Create requisition items
             * ----------------------------------------------------------
             */

            foreach ($validated['items'] as $item) {

                /*
                 * Double-check variant belongs to selected item.
                 */
                if (!empty($item['variant_id'])) {

                    $variantBelongsToItem =
                        StoreItemVariant::where(
                            'id',
                            $item['variant_id']
                        )
                        ->where(
                            'store_item_id',
                            $item['store_item_id']
                        )
                        ->exists();

                    if (!$variantBelongsToItem) {

                        throw \Illuminate\Validation\ValidationException::withMessages([
                            'items' => [
                                'One of the selected variants does not belong to its selected item.',
                            ],
                        ]);
                    }
                }


                $requisitionItem =
                    $requisition->items()->create([

                        'store_item_id' =>
                            $item['store_item_id'],

                        'variant_id' =>
                            $item['variant_id'] ?? null,

                        'requested_quantity' =>
                            $item['requested_quantity'],

                        /*
                         * IMPORTANT:
                         *
                         * Nothing is approved at draft stage.
                         */
                        'approved_quantity' => 0,

                        'issued_quantity' => 0,

                        'outstanding_quantity' => 0,

                        'notes' =>
                            $item['notes'] ?? null,
                    ]);


                /*
                 * Attach children where provided.
                 */
                if (!empty($item['child_ids'])) {
                    $requisitionItem->children()->sync(
                        $item['child_ids']
                    );
                }
            }
        });


        return redirect()
            ->route(
                'admin.stores.store-requisitions.create'
            )
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

            $number =
                'REQ-' .
                now()->format('Ymd') .
                '-' .
                strtoupper(
                    substr(
                        bin2hex(random_bytes(3)),
                        0,
                        6
                    )
                );

        } while (
            StoreRequisition::where(
                'requisition_number',
                $number
            )->exists()
        );

        return $number;
    }


    /**
     * Display a requisition.
     */
    public function show(StoreRequisition $storeRequisition)
    {
        $isRequester =
            $storeRequisition->requested_by === auth()->id();

        $canApprove =
            auth()->user()->can(
                'APPROVE STORE REQUISITIONS'
            );

        $canApproveHod =
            auth()->user()->can(
                'APPROVE STORE REQUISITIONS - HOD'
            );

        $canApproveManagement =
            auth()->user()->can(
                'APPROVE STORE REQUISITIONS - MANAGEMENT'
            );

        $canApproveStores =
            auth()->user()->can(
                'APPROVE STORE REQUISITIONS - STORES'
            );

        $canView =
            $isRequester ||
            $canApprove ||
            $canApproveHod ||
            $canApproveManagement ||
            $canApproveStores;

        if (!$canView) {
            abort(403);
        }

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
            compact('storeRequisition')
        );
    }


    /**
     * Submit a requisition for approval.
     */
    public function submit(StoreRequisition $storeRequisition)
    {
        if (
            $storeRequisition->requested_by !==
            auth()->id()
        ) {
            abort(403);
        }

        if (
            !in_array(
                $storeRequisition->status,
                ['draft', 'returned'],
                true
            )
        ) {
            return redirect()
                ->route(
                    'admin.stores.store-requisitions.show',
                    $storeRequisition
                )
                ->with(
                    'error',
                    'Only draft or returned requisitions can be submitted.'
                );
        }

        if ($storeRequisition->items()->count() === 0) {

            return redirect()
                ->route(
                    'admin.stores.store-requisitions.show',
                    $storeRequisition
                )
                ->with(
                    'error',
                    'A requisition must contain at least one item before it can be submitted.'
                );
        }

        /*
         * Make sure routing information exists before submission.
         */
        if (!$storeRequisition->source_store_id) {

            return redirect()
                ->route(
                    'admin.stores.store-requisitions.edit',
                    $storeRequisition
                )
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
                ->route(
                    'admin.stores.store-requisitions.edit',
                    $storeRequisition
                )
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

        return redirect()
            ->route(
                'admin.stores.store-requisitions.show',
                $storeRequisition
            )
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
        $validated = $request->validate([
            'comments' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        $stage =
            $storeRequisition->approval_stage;


        /*
         * --------------------------------------------------------------
         * Approval permissions by stage
         * --------------------------------------------------------------
         */

        $permissionByStage = [

            'hod' =>
                'APPROVE STORE REQUISITIONS - HOD',

            'management' =>
                'APPROVE STORE REQUISITIONS - MANAGEMENT',

            'stores' =>
                'APPROVE STORE REQUISITIONS - STORES',
        ];


        /*
         * --------------------------------------------------------------
         * Make sure requisition is at a valid approval stage
         * --------------------------------------------------------------
         */

        if (!isset($permissionByStage[$stage])) {

            return redirect()
                ->route(
                    'admin.stores.store-requisitions.show',
                    $storeRequisition
                )
                ->with(
                    'error',
                    'This requisition is not currently awaiting approval.'
                );
        }


        /*
         * --------------------------------------------------------------
         * Check permission for CURRENT stage
         * --------------------------------------------------------------
         */

        $requiredPermission =
            $permissionByStage[$stage];

        abort_unless(
            \Illuminate\Support\Facades\Gate::forUser(
                auth()->user()
            )->allows($requiredPermission),
            403
        );


        /*
         * --------------------------------------------------------------
         * Record approval and move to next stage
         * --------------------------------------------------------------
         */

        DB::transaction(function () use (
            $storeRequisition,
            $validated,
            $stage
        ) {

            /*
             * Record approval.
             */
            StoreRequisitionApproval::create([

                'store_requisition_id' =>
                    $storeRequisition->id,

                'approved_by' =>
                    auth()->id(),

                'approval_level' =>
                    $stage,

                'decision' =>
                    'approved',

                'comments' =>
                    $validated['comments'] ?? null,

                'decided_at' =>
                    now(),
            ]);


            /*
             * HOD → MANAGEMENT
             */
            if ($stage === 'hod') {

                $storeRequisition->update([
                    'status' => 'pending',
                    'approval_stage' => 'management',
                ]);
            }


            /*
             * MANAGEMENT → STORES
             */
            elseif ($stage === 'management') {

                $storeRequisition->update([
                    'status' => 'pending',
                    'approval_stage' => 'stores',
                ]);
            }


            /*
             * STORES → FULLY APPROVED
             */
            elseif ($stage === 'stores') {

                $storeRequisition->load('items');


                /*
                 * Only now do approved quantities become available.
                 */
                foreach (
                    $storeRequisition->items
                    as $item
                ) {

                    $requestedQuantity =
                        (float) $item->requested_quantity;

                    $issuedQuantity =
                        (float) $item->issued_quantity;

                    $outstandingQuantity =
                        max(
                            0,
                            $requestedQuantity -
                            $issuedQuantity
                        );

                    $item->update([

                        'approved_quantity' =>
                            $requestedQuantity,

                        'outstanding_quantity' =>
                            $outstandingQuantity,
                    ]);
                }


                /*
                 * Mark requisition fully approved.
                 */
                $storeRequisition->update([

                    'status' =>
                        'approved',

                    'approval_stage' =>
                        'approved',

                    'approved_at' =>
                        now(),

                    'fulfillment_status' =>
                        'not_issued',
                ]);
            }
        });


        /*
         * --------------------------------------------------------------
         * Success message
         * --------------------------------------------------------------
         */

        $message = match ($stage) {

            'hod' =>
                'HOD approval recorded. The requisition is now awaiting CM/DCM approval.',

            'management' =>
                'Management approval recorded. The requisition is now awaiting Stores review.',

            'stores' =>
                'Stores approval recorded. The requisition is now fully approved and ready for physical fulfillment.',

            default =>
                'Requisition approved.',
        };


        return redirect()
            ->route(
                'admin.stores.store-requisitions.show',
                $storeRequisition
            )
            ->with(
                'success',
                $message
            );
    }


    /**
     * Reject a pending requisition.
     */
    public function reject(
        Request $request,
        StoreRequisition $storeRequisition
    ) {
        abort_unless(
            auth()->user()->can(
                'APPROVE STORE REQUISITIONS'
            ),
            403
        );


        $validated = $request->validate([
            'comments' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);


        /*
         * Submitted requisitions are now represented by
         * status = pending.
         */
        if ($storeRequisition->status !== 'pending') {

            return redirect()
                ->route(
                    'admin.stores.store-requisitions.show',
                    $storeRequisition
                )
                ->with(
                    'error',
                    'Only pending requisitions can be rejected.'
                );
        }


        DB::transaction(function () use (
            $storeRequisition,
            $validated
        ) {

            StoreRequisitionApproval::create([

                'store_requisition_id' =>
                    $storeRequisition->id,

                'approved_by' =>
                    auth()->id(),

                'approval_level' =>
                    $storeRequisition->approval_stage,

                'decision' =>
                    'rejected',

                'comments' =>
                    $validated['comments'],

                'decided_at' =>
                    now(),
            ]);


            $storeRequisition->update([

                'status' =>
                    'rejected',

                'approval_stage' =>
                    'rejected',
            ]);
        });


        return redirect()
            ->route(
                'admin.stores.store-requisitions.show',
                $storeRequisition
            )
            ->with(
                'success',
                'Requisition ' .
                $storeRequisition->requisition_number .
                ' rejected.'
            );
    }


    /**
     * Send a pending requisition back to the requester.
     */
    public function sendBack(
        Request $request,
        StoreRequisition $storeRequisition
    ) {
        abort_unless(
            auth()->user()->can(
                'APPROVE STORE REQUISITIONS'
            ),
            403
        );


        $validated = $request->validate([
            'comments' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);


        /*
         * Submitted requisitions are now represented by
         * status = pending.
         */
        if ($storeRequisition->status !== 'pending') {

            return redirect()
                ->route(
                    'admin.stores.store-requisitions.show',
                    $storeRequisition
                )
                ->with(
                    'error',
                    'Only pending requisitions can be sent back for edits.'
                );
        }


        DB::transaction(function () use (
            $storeRequisition,
            $validated
        ) {

            StoreRequisitionApproval::create([

                'store_requisition_id' =>
                    $storeRequisition->id,

                'approved_by' =>
                    auth()->id(),

                'approval_level' =>
                    $storeRequisition->approval_stage,

                'decision' =>
                    'returned',

                'comments' =>
                    $validated['comments'],

                'decided_at' =>
                    now(),
            ]);


            $storeRequisition->update([

                'status' =>
                    'returned',

                'approval_stage' =>
                    'returned',
            ]);
        });


        return redirect()
            ->route(
                'admin.stores.store-requisitions.show',
                $storeRequisition
            )
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
    public function destroy(
        StoreRequisition $storeRequisition
    ) {
        /*
         * Only the person who created the requisition
         * can delete it.
         */
        if (
            (int) $storeRequisition->requested_by !==
            (int) auth()->id()
        ) {
            abort(403);
        }


        /*
         * Only drafts can be deleted.
         */
        if ($storeRequisition->status !== 'draft') {

            return redirect()
                ->route(
                    'admin.stores.store-requisitions.index'
                )
                ->with(
                    'error',
                    'Only draft requisitions can be deleted.'
                );
        }


        $requisitionNumber =
            $storeRequisition->requisition_number;


        DB::transaction(function () use (
            $storeRequisition
        ) {

            /*
             * Delete requisition items first.
             */
            $storeRequisition->items()->delete();

            /*
             * Delete the requisition.
             */
            $storeRequisition->delete();
        });


        return redirect()
            ->route(
                'admin.stores.store-requisitions.index'
            )
            ->with(
                'success',
                "Draft requisition {$requisitionNumber} was deleted successfully."
            );
    }

    public function cancel(Request $request, StoreRequisition $storeRequisition)
{
    $request->validate([
        'cancellation_reason' => ['required', 'string', 'max:1000'],
    ]);

    if (
        in_array($storeRequisition->status, ['cancelled', 'closed', 'completed'], true)
    ) {
        return back()->with('error', 'This requisition can no longer be cancelled.');
    }

    $storeRequisition->update([
        'status' => 'cancelled',
        'submission_notes' => trim(
            ($storeRequisition->submission_notes ? $storeRequisition->submission_notes . "\n\n" : '') .
            'Cancellation reason: ' . $request->cancellation_reason
        ),
    ]);

    return redirect()
        ->route('admin.stores.store-requisitions.show', $storeRequisition)
        ->with('success', 'Store requisition cancelled successfully.');
}


}

