<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\Store;
use App\Models\StoreItem;
use App\Models\StoreLpo;
use App\Models\StoreReceipt;
use App\Models\Supplier;
use App\Services\StoreReceiptPostingService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class StoreReceiptController extends Controller
{
    public function index()
    {
        $receipts = StoreReceipt::with([
            'store',
            'receivedBy',
            'supplier',
            'lpo',
        ])->latest()->get();

        return view('admin.stores.receipts.index', compact('receipts'));
    }

    public function create()
    {
        $stores = Store::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $suppliers = Supplier::query()
            ->orderBy('name')
            ->get();

        $donors = Donor::query()
            ->orderBy('name')
            ->get();

        $donations = Donation::query()
            ->with([
                'donor:id,donor_number,name',
                'items.storeItem.unit',
                'items.variant',
            ])
            ->where('type', 'IN_KIND')
            ->orderByDesc('donation_date')
            ->orderByDesc('id')
            ->get();

        $lpos = StoreLpo::query()
            ->with([
                'supplier:id,supplier_code,name',
                'store:id,name',
            ])
            ->withCount('items')
            ->whereIn('status', [
                'APPROVED',
                'PARTIALLY_RECEIVED',
            ])
            ->orderByDesc('lpo_date')
            ->orderByDesc('id')
            ->get();

        return view('admin.stores.receipts.create', compact(
            'stores',
            'suppliers',
            'donors',
            'donations',
            'lpos'
        ));
    }

public function store(Request $request)
{
    $validated = $request->validate([
        'store_id' => [
            'required',
            'integer',
            'exists:stores,id',
        ],

        'received_date' => [
            'required',
            'date',
        ],
    ]);

    $receipt = DB::transaction(function () use ($validated) {

        return StoreReceipt::create([
            'receipt_number' => $this->generateReceiptNumber(),

            'store_id' => $validated['store_id'],

            /*
             * source_type is required by the database.
             * The actual source will be selected/changed
             * on the Edit page before the receipt is posted.
             */
            'source_type' => 'PURCHASE',

            'received_date' => $validated['received_date'],

            'received_by' => auth()->id(),

            'status' => 'DRAFT',

            'supplier_id' => null,
            'store_lpo_id' => null,
            'supplier_name' => null,
            'supplier_reference' => null,
            'donation_id' => null,
            'notes' => null,
        ]);
    });

    return redirect()
        ->route('admin.store-receipts.edit', $receipt)
        ->with(
            'success',
            'Store receipt created. Complete the receiving details below.'
        );
}

    public function show(StoreReceipt $storeReceipt)
    {
        $storeReceipt->load([
            'store',
            'receivedBy',
            'supplier',
            'lpo.supplier',
            'lpo.items.item.unit',
            'lpo.items.variant',
            'donation.donor',
            'donation.items.storeItem.unit',
            'donation.items.variant',
            'items.item.unit',
            'items.variant',
            'items.lpoItem',
        ]);

        $items = StoreItem::with([
            'unit',
            'variants' => function ($query) {
                $query->where('is_active', true)->orderBy('name');
            },
        ])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $itemVariants = [];

        foreach ($items as $item) {
            $itemVariants[$item->id] = [];

            foreach ($item->variants as $variant) {
                $itemVariants[$item->id][] = [
                    'id' => $variant->id,
                    'name' => $variant->name,
                    'code' => $variant->code,
                ];
            }
        }

        return view('admin.stores.receipts.show', compact(
            'storeReceipt',
            'items',
            'itemVariants'
        ));
    }

public function edit(StoreReceipt $storeReceipt)
{
    /*
    |--------------------------------------------------------------------------
    | Only draft receipts can be edited
    |--------------------------------------------------------------------------
    */

    if ($storeReceipt->status !== 'DRAFT') {
        return redirect()
            ->route('admin.store-receipts.show', $storeReceipt)
            ->with('error', 'Only draft store receipts can be edited.');
    }

    /*
    |--------------------------------------------------------------------------
    | Load the receipt and all related data
    |--------------------------------------------------------------------------
    */

    $storeReceipt->load([
        'store',
        'supplier',

        'lpo.supplier',
        'lpo.store',
        'lpo.items.item.unit',
        'lpo.items.variant',

        'donation.donor',
        'donation.items.storeItem.unit',
        'donation.items.variant',

        'items.item.unit',
        'items.variant',
        'items.lpoItem',
    ]);

    /*
    |--------------------------------------------------------------------------
    | Stores
    |--------------------------------------------------------------------------
    */

    $stores = Store::query()
        ->where('is_active', true)
        ->orderBy('name')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | Suppliers
    |--------------------------------------------------------------------------
    */

    $suppliers = Supplier::query()
        ->orderBy('name')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | Donors
    |--------------------------------------------------------------------------
    */

    $donors = Donor::query()
        ->orderBy('name')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | Donations
    |
    | Only IN_KIND donations that have NOT already been received into
    | Stores are shown.
    |
    | IMPORTANT:
    | The donation belonging to the current draft receipt is kept visible
    | so that the user can continue editing the draft.
    |--------------------------------------------------------------------------
    */

    $donations = Donation::query()
        ->with([
            'donor:id,donor_number,name',
            'items.storeItem.unit',
            'items.variant',
        ])
        ->where('type', 'IN_KIND')
        ->where(function ($query) use ($storeReceipt) {

            /*
             * Donation has never been used by a Store Receipt
             */
            $query->whereNotExists(function ($subQuery) {
                $subQuery
                    ->selectRaw('1')
                    ->from('store_receipts')
                    ->whereColumn(
                        'store_receipts.donation_id',
                        'donations.id'
                    );
            })

            /*
             * OR it belongs to the current draft receipt.
             *
             * This allows the current draft donation to remain
             * selectable while editing.
             */
            ->orWhere('donations.id', $storeReceipt->donation_id);
        })
        ->orderByDesc('donation_date')
        ->orderByDesc('id')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | LPOs
    |--------------------------------------------------------------------------
    */

    $lpos = StoreLpo::query()
        ->with([
            'supplier',
            'store',
            'items.item.unit',
            'items.variant',
        ])
        ->whereIn('status', [
            'APPROVED',
            'PARTIALLY_RECEIVED',
        ])
        ->orderByDesc('lpo_date')
        ->orderByDesc('id')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | Keep the current LPO available
    |
    | This is important if an LPO was previously selected on the receipt
    | but its status has subsequently changed.
    |--------------------------------------------------------------------------
    */

    if (
        $storeReceipt->lpo &&
        !$lpos->contains('id', $storeReceipt->store_lpo_id)
    ) {
        $lpos->push($storeReceipt->lpo);

        $lpos = $lpos
            ->sortByDesc('lpo_date')
            ->values();
    }

    /*
    |--------------------------------------------------------------------------
    | Keep current store available
    |--------------------------------------------------------------------------
    */

    if (
        $storeReceipt->store &&
        !$stores->contains('id', $storeReceipt->store_id)
    ) {
        $stores->push($storeReceipt->store);

        $stores = $stores
            ->sortBy('name')
            ->values();
    }

    /*
    |--------------------------------------------------------------------------
    | Keep current supplier available
    |--------------------------------------------------------------------------
    */

    if (
        $storeReceipt->supplier &&
        !$suppliers->contains('id', $storeReceipt->supplier_id)
    ) {
        $suppliers->push($storeReceipt->supplier);

        $suppliers = $suppliers
            ->sortBy('name')
            ->values();
    }

    /*
    |--------------------------------------------------------------------------
    | Store items and active variants
    |--------------------------------------------------------------------------
    */

    $items = StoreItem::query()
        ->with([
            'unit',
            'variants' => function ($query) {
                $query
                    ->where('is_active', true)
                    ->orderBy('name');
            },
        ])
        ->where('is_active', true)
        ->orderBy('name')
        ->get();

    /*
    |--------------------------------------------------------------------------
    | Prepare simple JSON-safe store item data
    |
    | This is used by JavaScript in edit.blade.php.
    |--------------------------------------------------------------------------
    */

    $itemData = $items
        ->map(function ($item) {
            return [
                'id' => (int) $item->id,

                'name' => (string) $item->name,

                'sku' => (string) ($item->sku ?? ''),

                'unit' => $item->unit
                    ? (string) (
                        $item->unit->name
                        ?? $item->unit->code
                        ?? ''
                    )
                    : '',

                'variants' => $item->variants
                    ->map(function ($variant) {
                        return [
                            'id' => (int) $variant->id,

                            'name' => (string) $variant->name,

                            'code' => (string) (
                                $variant->code ?? ''
                            ),
                        ];
                    })
                    ->values()
                    ->all(),
            ];
        })
        ->values()
        ->all();

    /*
    |--------------------------------------------------------------------------
    | Prepare LPO data
    |
    | This includes the LPO header information and its individual items.
    |--------------------------------------------------------------------------
    */

    $lpoData = $lpos
        ->map(function ($lpo) {
            return [
                'id' => (int) $lpo->id,

                'supplier_id' => $lpo->supplier_id
                    ? (int) $lpo->supplier_id
                    : null,

                'store_id' => $lpo->store_id
                    ? (int) $lpo->store_id
                    : null,

                'status' => (string) $lpo->status,

                'supplier_name' => (string) (
                    $lpo->supplier->name ?? ''
                ),

                'store_name' => (string) (
                    $lpo->store->name ?? ''
                ),

                'items' => $lpo->items
                    ->map(function ($lpoItem) {
                        return [
                            'id' => (int) $lpoItem->id,

                            'store_item_id' => $lpoItem->store_item_id
                                ? (int) $lpoItem->store_item_id
                                : null,

                            'variant_id' => $lpoItem->variant_id
                                ? (int) $lpoItem->variant_id
                                : null,

                            'quantity' => $lpoItem->getAttribute('quantity') !== null
                                ? (float) $lpoItem->getAttribute('quantity')
                                : null,
                        ];
                    })
                    ->values()
                    ->all(),
            ];
        })
        ->values()
        ->all();

    /*
    |--------------------------------------------------------------------------
    | Prepare existing receipt items
    |
    | These are the items currently saved against this draft receipt.
    |
    | We now also send the item name, SKU, unit and variant name directly
    | to the Blade. This makes the existing receipt rows reliable even
    | before JavaScript performs another item lookup.
    |--------------------------------------------------------------------------
    */

    $receiptItemData = $storeReceipt->items
        ->map(function ($receiptItem) {

            $storeItem = $receiptItem->item;
            $variant = $receiptItem->variant;

            return [
                'store_item_id' => $receiptItem->store_item_id
                    ? (int) $receiptItem->store_item_id
                    : null,

                'variant_id' => $receiptItem->variant_id
                    ? (int) $receiptItem->variant_id
                    : null,

                'quantity' => $receiptItem->quantity !== null
                    ? (float) $receiptItem->quantity
                    : 0,

                'notes' => (string) (
                    $receiptItem->notes ?? ''
                ),

                'lpo_item_id' => $receiptItem->lpo_item_id
                    ? (int) $receiptItem->lpo_item_id
                    : null,

                /*
                |--------------------------------------------------------------------------
                | Item information
                |--------------------------------------------------------------------------
                */

                'store_item_name' => $storeItem
                    ? (string) ($storeItem->name ?? '')
                    : '',

                'store_item_sku' => $storeItem
                    ? (string) ($storeItem->sku ?? '')
                    : '',

                'unit_name' => $storeItem && $storeItem->unit
                    ? (string) (
                        $storeItem->unit->name
                        ?? $storeItem->unit->code
                        ?? ''
                    )
                    : '',

                'variant_name' => $variant
                    ? (string) ($variant->name ?? '')
                    : '',
            ];
        })
        ->values()
        ->all();

    /*
    |--------------------------------------------------------------------------
    | Prepare donation data
    |
    | Each donation includes its donor information and all donation items.
    |
    | This allows the edit Blade to do:
    |
    | Donation selected
    |       ↓
    | Donation items loaded
    |       ↓
    | Goods received table populated
    |--------------------------------------------------------------------------
    */

    $donationData = $donations
        ->map(function ($donation) {
            return [
                'id' => (int) $donation->id,

                'donation_number' => (string) (
                    $donation->donation_number
                    ?? ('Donation #' . $donation->id)
                ),

                'donation_date' => $donation->donation_date
                    ? $donation->donation_date->format('Y-m-d')
                    : '',

                'donor_number' => (string) (
                    $donation->donor->donor_number ?? ''
                ),

                'donor_name' => (string) (
                    $donation->donor->name ?? ''
                ),

                'items' => $donation->items
                    ->map(function ($donationItem) {
                        return [
                            'id' => (int) $donationItem->id,

                            'store_item_id' => $donationItem->store_item_id
                                ? (int) $donationItem->store_item_id
                                : null,

                            'variant_id' => $donationItem->variant_id
                                ? (int) $donationItem->variant_id
                                : null,

                            /*
                            |--------------------------------------------------------------------------
                            | Free-text item description from the
                            | original donation record.
                            |--------------------------------------------------------------------------
                            */

                            'item' => (string) (
                                $donationItem->item ?? ''
                            ),

                            'quantity' => $donationItem->quantity !== null
                                ? (float) $donationItem->quantity
                                : 0,

                            'unit' => (string) (
                                $donationItem->unit ?? ''
                            ),

                            'condition' => (string) (
                                $donationItem->condition ?? ''
                            ),

                            'notes' => (string) (
                                $donationItem->notes ?? ''
                            ),

                            /*
                            |--------------------------------------------------------------------------
                            | Store item information
                            |--------------------------------------------------------------------------
                            */

                            'store_item_name' => $donationItem->storeItem
                                ? (string) $donationItem->storeItem->name
                                : '',

                            'variant_name' => $donationItem->variant
                                ? (string) $donationItem->variant->name
                                : '',
                        ];
                    })
                    ->values()
                    ->all(),
            ];
        })
        ->values()
        ->all();

    /*
    |--------------------------------------------------------------------------
    | Received date
    |--------------------------------------------------------------------------
    */

    $receivedDateValue = $storeReceipt->received_date
        ? date(
            'Y-m-d',
            strtotime((string) $storeReceipt->received_date)
        )
        : now()->format('Y-m-d');

    /*
    |--------------------------------------------------------------------------
    | Return edit view
    |--------------------------------------------------------------------------
    */

    return view('admin.stores.receipts.edit', compact(
        'storeReceipt',
        'stores',
        'suppliers',
        'donors',
        'donations',
        'lpos',
        'items',
        'itemData',
        'lpoData',
        'donationData',
        'receiptItemData',
        'receivedDateValue'
    ));
}

public function update(Request $request, StoreReceipt $storeReceipt)
{
    /*
    |--------------------------------------------------------------------------
    | Only draft receipts can be edited
    |--------------------------------------------------------------------------
    */
    if ($storeReceipt->status !== 'DRAFT') {
        return redirect()
            ->route('admin.store-receipts.show', $storeReceipt)
            ->with('error', 'Only draft store receipts can be edited.');
    }

    /*
    |--------------------------------------------------------------------------
    | Validate request
    |--------------------------------------------------------------------------
    */
    $validated = $request->validate([
        'store_id' => [
            'required',
            'integer',
            'exists:stores,id',
        ],

        'source_type' => [
            'required',
            Rule::in([
                'PURCHASE',
                'DONATION',
            ]),
        ],

        'supplier_id' => [
            'nullable',
            'integer',
            'exists:suppliers,id',
        ],

        'store_lpo_id' => [
            'nullable',
            'integer',
            'exists:store_lpos,id',
        ],

        'supplier_name' => [
            'nullable',
            'string',
            'max:255',
        ],

        'supplier_reference' => [
            'nullable',
            'string',
            'max:255',
        ],

        'donation_id' => [
            'nullable',
            'integer',
            Rule::exists('donations', 'id')->where(function ($query) {
                $query->where('type', 'IN_KIND');
            }),
        ],

        'received_date' => [
            'required',
            'date',
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

        'items.*.store_item_id' => [
            'required',
            'integer',
            'exists:store_items,id',
        ],

        'items.*.variant_id' => [
            'nullable',
            'integer',
        ],

        'items.*.quantity' => [
            'required',
            'numeric',
            'min:0.001',
        ],

        'items.*.notes' => [
            'nullable',
            'string',
            'max:2000',
        ],

        'items.*.lpo_item_id' => [
            'nullable',
            'integer',
        ],
    ]);

    /*
    |--------------------------------------------------------------------------
    | Variables
    |--------------------------------------------------------------------------
    */
    $lpo = null;
    $donation = null;

    /*
    |--------------------------------------------------------------------------
    | PURCHASE validation
    |--------------------------------------------------------------------------
    */
    if ($validated['source_type'] === 'PURCHASE') {

        /*
        |--------------------------------------------------------------------------
        | Supplier required
        |--------------------------------------------------------------------------
        */
        if (empty($validated['supplier_id'])) {
            return back()
                ->withInput()
                ->withErrors([
                    'supplier_id' =>
                        'A supplier must be selected for a purchase receipt.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | LPO required
        |--------------------------------------------------------------------------
        */
        if (empty($validated['store_lpo_id'])) {
            return back()
                ->withInput()
                ->withErrors([
                    'store_lpo_id' =>
                        'An approved LPO must be selected for a purchase receipt.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Load LPO and its items
        |--------------------------------------------------------------------------
        |
        | We need the receipt items because remaining_quantity is calculated
        | from POSTED StoreReceiptItems.
        |
        */
        $lpo = StoreLpo::with([
            'supplier',
            'items',
        ])->find($validated['store_lpo_id']);

        if (!$lpo) {
            return back()
                ->withInput()
                ->withErrors([
                    'store_lpo_id' =>
                        'The selected LPO could not be found.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Check LPO status
        |--------------------------------------------------------------------------
        |
        | APPROVED and PARTIALLY_RECEIVED LPOs can receive goods.
        |
        | The current LPO is also allowed if it is already attached to this
        | draft receipt, so the user can continue editing it.
        |
        */
        if (
            !in_array(
                $lpo->status,
                [
                    'APPROVED',
                    'PARTIALLY_RECEIVED',
                ],
                true
            )
            &&
            (int) $lpo->id !== (int) $storeReceipt->store_lpo_id
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'store_lpo_id' =>
                        'Only approved LPOs with outstanding items can be received.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Supplier must match LPO supplier
        |--------------------------------------------------------------------------
        */
        if (
            (int) $validated['supplier_id']
            !==
            (int) $lpo->supplier_id
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'supplier_id' =>
                        'The selected supplier does not match the supplier on the LPO.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Store must match LPO store
        |--------------------------------------------------------------------------
        */
        if (
            (int) $validated['store_id']
            !==
            (int) $lpo->store_id
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'store_id' =>
                        'The selected store does not match the store on the LPO.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Purchase receipts cannot have a donation
        |--------------------------------------------------------------------------
        */
        $validated['donation_id'] = null;

        /*
        |--------------------------------------------------------------------------
        | Use actual supplier name from LPO
        |--------------------------------------------------------------------------
        */
        $validated['supplier_name'] =
            $lpo->supplier->name ?? null;
    }

    /*
    |--------------------------------------------------------------------------
    | DONATION validation
    |--------------------------------------------------------------------------
    */
    if ($validated['source_type'] === 'DONATION') {

        /*
        |--------------------------------------------------------------------------
        | Donation required
        |--------------------------------------------------------------------------
        */
        if (empty($validated['donation_id'])) {
            return back()
                ->withInput()
                ->withErrors([
                    'donation_id' =>
                        'A donation must be selected for a donation receipt.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Load donation
        |--------------------------------------------------------------------------
        */
        $donation = Donation::with('items')->find(
            $validated['donation_id']
        );

        if (!$donation) {
            return back()
                ->withInput()
                ->withErrors([
                    'donation_id' =>
                        'The selected donation could not be found.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Prevent the same donation from being received twice
        |--------------------------------------------------------------------------
        |
        | The current draft receipt is excluded.
        |
        */
        $alreadyReceived = StoreReceipt::query()
            ->where('donation_id', $donation->id)
            ->where('id', '!=', $storeReceipt->id)
            ->exists();

        if ($alreadyReceived) {
            return back()
                ->withInput()
                ->withErrors([
                    'donation_id' =>
                        'This donation has already been received into Stores.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Donation receipts do not have purchase information
        |--------------------------------------------------------------------------
        */
        $validated['supplier_id'] = null;
        $validated['store_lpo_id'] = null;
        $validated['supplier_name'] = null;
        $validated['supplier_reference'] = null;
    }

    /*
    |--------------------------------------------------------------------------
    | Validate submitted items
    |--------------------------------------------------------------------------
    */
    $submittedItems = collect($validated['items'])
        ->filter(function ($item) {
            return !empty($item['store_item_id'])
                && isset($item['quantity'])
                && (float) $item['quantity'] > 0;
        })
        ->values();

    if ($submittedItems->isEmpty()) {
        return back()
            ->withInput()
            ->withErrors([
                'items' =>
                    'Add at least one received item before saving the receipt.',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Load submitted store items
    |--------------------------------------------------------------------------
    */
    $storeItemIds = $submittedItems
        ->pluck('store_item_id')
        ->map(fn ($id) => (int) $id)
        ->unique()
        ->values();

    $storeItems = StoreItem::query()
        ->with([
            'variants' => function ($query) {
                $query->where('is_active', true);
            },
        ])
        ->whereIn('id', $storeItemIds)
        ->where('is_active', true)
        ->get()
        ->keyBy('id');

    /*
    |--------------------------------------------------------------------------
    | Validate store items and variants
    |--------------------------------------------------------------------------
    */
    foreach ($submittedItems as $index => $item) {

        $storeItemId = (int) $item['store_item_id'];

        /*
        |--------------------------------------------------------------------------
        | Store item must exist and be active
        |--------------------------------------------------------------------------
        */
        if (!$storeItems->has($storeItemId)) {
            return back()
                ->withInput()
                ->withErrors([
                    "items.$index.store_item_id" =>
                        'The selected store item is not available.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Variant must belong to selected item
        |--------------------------------------------------------------------------
        */
        if (!empty($item['variant_id'])) {

            $variantExists = $storeItems[$storeItemId]
                ->variants
                ->contains(
                    'id',
                    (int) $item['variant_id']
                );

            if (!$variantExists) {
                return back()
                    ->withInput()
                    ->withErrors([
                        "items.$index.variant_id" =>
                            'The selected variant does not belong to the selected item.',
                    ]);
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | PURCHASE item validation
    |--------------------------------------------------------------------------
    */
    if ($validated['source_type'] === 'PURCHASE') {

        $lpoItems = $lpo->items->keyBy('id');

        foreach ($submittedItems as $index => $item) {

            /*
            |--------------------------------------------------------------------------
            | Every purchase item must be linked to an LPO item
            |--------------------------------------------------------------------------
            */
            if (empty($item['lpo_item_id'])) {
                return back()
                    ->withInput()
                    ->withErrors([
                        "items.$index.lpo_item_id" =>
                            'Each purchase receipt item must be linked to an LPO item.',
                    ]);
            }

            $lpoItemId = (int) $item['lpo_item_id'];

            /*
            |--------------------------------------------------------------------------
            | LPO item must belong to selected LPO
            |--------------------------------------------------------------------------
            */
            if (!$lpoItems->has($lpoItemId)) {
                return back()
                    ->withInput()
                    ->withErrors([
                        "items.$index.lpo_item_id" =>
                            'The selected LPO item does not belong to the selected LPO.',
                    ]);
            }

            $lpoItem = $lpoItems[$lpoItemId];

            /*
            |--------------------------------------------------------------------------
            | Store item must match LPO item
            |--------------------------------------------------------------------------
            */
            if (
                (int) $lpoItem->store_item_id
                !==
                (int) $item['store_item_id']
            ) {
                return back()
                    ->withInput()
                    ->withErrors([
                        "items.$index.store_item_id" =>
                            'The selected item does not match the selected LPO item.',
                    ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Variant must match LPO item
            |--------------------------------------------------------------------------
            */
            $submittedVariantId = !empty($item['variant_id'])
                ? (int) $item['variant_id']
                : null;

            $lpoVariantId = !empty($lpoItem->variant_id)
                ? (int) $lpoItem->variant_id
                : null;

            if ($submittedVariantId !== $lpoVariantId) {
                return back()
                    ->withInput()
                    ->withErrors([
                        "items.$index.variant_id" =>
                            'The selected variant does not match the LPO item.',
                    ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Partial LPO receiving
            |--------------------------------------------------------------------------
            |
            | IMPORTANT:
            |
            | ordered_quantity = what was originally ordered.
            |
            | received_quantity = quantity already received on POSTED
            | receipts only.
            |
            | remaining_quantity = ordered_quantity - received_quantity.
            |
            | Therefore a new draft receipt cannot exceed what is still
            | outstanding on the LPO.
            |
            */
            $orderedQuantity =
                (float) $lpoItem->ordered_quantity;

            $receivedQuantity =
                (float) $lpoItem->received_quantity;

            $remainingQuantity =
                (float) $lpoItem->remaining_quantity;

            $requestedReceiptQuantity =
                (float) $item['quantity'];

            /*
            |--------------------------------------------------------------------------
            | Prevent over-receiving
            |--------------------------------------------------------------------------
            */
            if (
                $requestedReceiptQuantity
                >
                $remainingQuantity
            ) {
                return back()
                    ->withInput()
                    ->withErrors([
                        "items.$index.quantity" =>
                            "The received quantity ({$requestedReceiptQuantity}) cannot exceed the remaining LPO quantity ({$remainingQuantity}). " .
                            "Ordered: {$orderedQuantity}; " .
                            "Already received: {$receivedQuantity}.",
                    ]);
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DONATION item validation
    |--------------------------------------------------------------------------
    |
    | The quantity received into Stores must match the quantity recorded
    | on the selected donation item.
    |--------------------------------------------------------------------------
    */
    if ($validated['source_type'] === 'DONATION') {

        foreach ($submittedItems as $index => $item) {

            $storeItemId = (int) $item['store_item_id'];

            $submittedVariantId = !empty($item['variant_id'])
                ? (int) $item['variant_id']
                : null;

            /*
            |--------------------------------------------------------------------------
            | Find matching donation item
            |--------------------------------------------------------------------------
            */
            $matchingDonationItem = $donation->items->first(
                function ($donationItem) use (
                    $storeItemId,
                    $submittedVariantId
                ) {

                    $donationStoreItemId =
                        (int) $donationItem->store_item_id;

                    $donationVariantId =
                        !empty($donationItem->variant_id)
                            ? (int) $donationItem->variant_id
                            : null;

                    return $donationStoreItemId === $storeItemId
                        && $donationVariantId === $submittedVariantId;
                }
            );

            /*
            |--------------------------------------------------------------------------
            | Item must exist on donation
            |--------------------------------------------------------------------------
            */
            if (!$matchingDonationItem) {
                return back()
                    ->withInput()
                    ->withErrors([
                        "items.$index.store_item_id" =>
                            'The selected item does not belong to the selected donation.',
                    ]);
            }

            /*
            |--------------------------------------------------------------------------
            | Donation quantity
            |--------------------------------------------------------------------------
            */
            $donationQuantity =
                (float) $matchingDonationItem->quantity;

            $receivedQuantity =
                (float) $item['quantity'];

            /*
            |--------------------------------------------------------------------------
            | Donation quantity must match
            |--------------------------------------------------------------------------
            */
            if (
                abs(
                    $receivedQuantity
                    -
                    $donationQuantity
                ) > 0.000001
            ) {
                return back()
                    ->withInput()
                    ->withErrors([
                        "items.$index.quantity" =>
                            "The received quantity ({$receivedQuantity}) must match the donation quantity ({$donationQuantity}).",
                    ]);
            }
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Save receipt and lines
    |--------------------------------------------------------------------------
    */
    DB::transaction(function () use (
        $storeReceipt,
        $validated,
        $submittedItems
    ) {

        /*
        |--------------------------------------------------------------------------
        | Update receipt header
        |--------------------------------------------------------------------------
        */
        $storeReceipt->update([
            'store_id' =>
                $validated['store_id'],

            'source_type' =>
                $validated['source_type'],

            'supplier_id' =>
                $validated['supplier_id'] ?? null,

            'store_lpo_id' =>
                $validated['store_lpo_id'] ?? null,

            'supplier_name' =>
                $validated['supplier_name'] ?? null,

            'supplier_reference' =>
                $validated['supplier_reference'] ?? null,

            'donation_id' =>
                $validated['donation_id'] ?? null,

            'received_date' =>
                $validated['received_date'],

            'notes' =>
                $validated['notes'] ?? null,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Replace receipt items
        |--------------------------------------------------------------------------
        */
        $storeReceipt->items()->delete();

        foreach ($submittedItems as $item) {

            $storeReceipt->items()->create([
                'store_item_id' =>
                    $item['store_item_id'],

                'variant_id' =>
                    !empty($item['variant_id'])
                        ? $item['variant_id']
                        : null,

                'quantity' =>
                    $item['quantity'],

                'notes' =>
                    $item['notes'] ?? null,

                'store_lpo_item_id' =>
                    $validated['source_type'] === 'PURCHASE'
                        ? ($item['lpo_item_id'] ?? null)
                        : null,
            ]);
        }
    });

    /*
    |--------------------------------------------------------------------------
    | Return to receipt
    |--------------------------------------------------------------------------
    */
    return redirect()
        ->route(
            'admin.store-receipts.show',
            $storeReceipt
        )
        ->with(
            'success',
            'Store receipt updated successfully.'
        );
}

    public function destroy(StoreReceipt $storeReceipt)
    {
        if ($storeReceipt->status !== 'DRAFT') {
            return redirect()
                ->route('admin.store-receipts.index')
                ->with('error', 'Only draft store receipts can be deleted.');
        }

        $storeReceipt->delete();

        return redirect()
            ->route('admin.store-receipts.index')
            ->with('success', 'Store receipt deleted successfully.');
    }

    public function post(
        StoreReceipt $storeReceipt,
        StoreReceiptPostingService $postingService
    ) {
        try {
            $postingService->post($storeReceipt);

            return redirect()
                ->route('admin.store-receipts.show', $storeReceipt)
                ->with(
                    'success',
                    'Store receipt posted successfully. Stock has been updated.'
                );
        } catch (\RuntimeException $e) {
            return redirect()
                ->route('admin.store-receipts.show', $storeReceipt)
                ->with('error', $e->getMessage());
        }
    }

    private function generateReceiptNumber(): string
    {
        do {
            $number = 'SR-' . now()->format('YmdHis');
            $number .= '-' . strtoupper(
                substr(bin2hex(random_bytes(3)), 0, 6)
            );
        } while (
            StoreReceipt::where('receipt_number', $number)->exists()
        );

        return $number;
    }
}