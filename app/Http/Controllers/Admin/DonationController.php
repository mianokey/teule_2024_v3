<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\DonationItem;
use App\Models\Donor;
use App\Models\ReportAuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\DonationThankYouService;
use App\Models\StoreItem;
use App\Models\StoreItemVariant;
use App\Models\StoreReceipt;
use App\Models\StoreStock;
use App\Models\StoreStockMovement;
use App\Services\Reports\ReportExportService;
use App\Services\WorkflowNotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;

class DonationController extends Controller
{
    /**
     * Display all donations.
     */
    


public function index(Request $request)
{
    $request->validate([
        'search' => ['nullable', 'string', 'max:255'],
        'date_from' => ['nullable', 'date'],
        'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        'donor_id' => ['nullable', 'integer', 'exists:donors,id'],
        'type' => ['nullable', Rule::in(['cash', 'IN_KIND'])],
        'classification' => [
            'nullable',
            Rule::in([
                'donation',
                'payment',
                'refund',
                'other',
                'unclassified',
            ]),
        ],
        'source' => ['nullable', 'string', 'max:255'],
        'receiving_status' => [
            'nullable',
            Rule::in([
                'received',
                'draft',
                'not_received',
                'not_applicable',
            ]),
        ],
        'amount_min' => ['nullable', 'numeric', 'min:0'],
        'amount_max' => ['nullable', 'numeric', 'min:0', 'gte:amount_min'],
        'currency' => ['nullable', 'string', 'max:10'],
    ]);

    $postedReceipt = DB::table('store_receipts')
        ->selectRaw('COUNT(*)')
        ->whereColumn('store_receipts.donation_id', 'donations.id')
        ->where('store_receipts.source_type', 'DONATION')
        ->where('store_receipts.status', 'POSTED');

    $draftReceipt = DB::table('store_receipts')
        ->selectRaw('COUNT(*)')
        ->whereColumn('store_receipts.donation_id', 'donations.id')
        ->where('store_receipts.source_type', 'DONATION')
        ->where('store_receipts.status', 'DRAFT');

    $query = Donation::with([
        'donor',
        'receivedBy',
    ])
        ->addSelect([
            'posted_store_receipts_count' => $postedReceipt,
            'draft_store_receipts_count' => $draftReceipt,
        ]);

    // General search
    if ($request->filled('search')) {
        $search = trim($request->input('search'));

        $query->where(function ($q) use ($search) {
            $q->where('donation_number', 'like', "%{$search}%")
                ->orWhere('purpose', 'like', "%{$search}%")
                ->orWhere('source', 'like', "%{$search}%")
                ->orWhere('type', 'like', "%{$search}%")
                ->orWhere('classification', 'like', "%{$search}%")
                ->orWhere('currency', 'like', "%{$search}%")
                ->orWhereHas('donor', function ($donorQuery) use ($search) {
                    $donorQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('donor_number', 'like', "%{$search}%");
                });
        });
    }

    // Donation date range
    $query
        ->when(
            $request->filled('date_from'),
            fn ($q) => $q->whereDate(
                'donation_date',
                '>=',
                $request->input('date_from')
            )
        )
        ->when(
            $request->filled('date_to'),
            fn ($q) => $q->whereDate(
                'donation_date',
                '<=',
                $request->input('date_to')
            )
        );

    // Donor
    $query->when(
        $request->filled('donor_id'),
        fn ($q) => $q->where('donor_id', $request->input('donor_id'))
    );

    // Cash or in-kind
    $query->when(
        $request->filled('type'),
        fn ($q) => $q->where(
            'type',
            $request->input('type') === 'cash' ? 'cash' : 'IN_KIND'
        )
    );

    // Classification
    $query->when(
        $request->filled('classification'),
        function ($q) use ($request) {
            $classification = $request->input('classification');

            if ($classification === 'unclassified') {
                $q->where(function ($subQuery) {
                    $subQuery->whereNull('classification')
                        ->orWhere('classification', '');
                });
            } else {
                $q->where('classification', $classification);
            }
        }
    );

    // Source
    $query->when(
        $request->filled('source'),
        fn ($q) => $q->where('source', $request->input('source'))
    );

    // Amount range
    $query
        ->when(
            $request->filled('amount_min'),
            fn ($q) => $q->where(
                'amount',
                '>=',
                $request->input('amount_min')
            )
        )
        ->when(
            $request->filled('amount_max'),
            fn ($q) => $q->where(
                'amount',
                '<=',
                $request->input('amount_max')
            )
        );

    // Currency
    $query->when(
        $request->filled('currency'),
        fn ($q) => $q->where('currency', $request->input('currency'))
    );

    // Store receiving status
    $query->when(
        $request->filled('receiving_status'),
        function ($q) use ($request) {
            $status = $request->input('receiving_status');

            $hasPostedReceipt = function ($subQuery) {
                $subQuery->selectRaw('1')
                    ->from('store_receipts')
                    ->whereColumn('store_receipts.donation_id', 'donations.id')
                    ->where('store_receipts.source_type', 'DONATION')
                    ->where('store_receipts.status', 'POSTED');
            };

            $hasDraftReceipt = function ($subQuery) {
                $subQuery->selectRaw('1')
                    ->from('store_receipts')
                    ->whereColumn('store_receipts.donation_id', 'donations.id')
                    ->where('store_receipts.source_type', 'DONATION')
                    ->where('store_receipts.status', 'DRAFT');
            };

            if ($status === 'received') {
                $q->whereExists($hasPostedReceipt);
            } elseif ($status === 'draft') {
                $q->whereNotExists($hasPostedReceipt)
                    ->whereExists($hasDraftReceipt);
            } elseif ($status === 'not_received') {
                $q->whereNotExists($hasPostedReceipt)
                    ->whereNotExists($hasDraftReceipt)
                    ->whereRaw("UPPER(type) = ?", ['IN_KIND']);
            } elseif ($status === 'not_applicable') {
                $q->whereRaw("UPPER(type) != ?", ['IN_KIND']);
            }
        }
    );

    $donations = $query
        ->latest('donation_date')
        ->latest('id')
        ->paginate(10)
        ->withQueryString();

    $donors = Donor::orderBy('name')->get();

    $sources = Donation::whereNotNull('source')
        ->where('source', '!=', '')
        ->distinct()
        ->orderBy('source')
        ->pluck('source');

    $currencies = Donation::whereNotNull('currency')
        ->where('currency', '!=', '')
        ->distinct()
        ->orderBy('currency')
        ->pluck('currency');

    return view('admin.donations.index', compact(
        'donations',
        'donors',
        'sources',
        'currencies'
    ));
}




    /**
     * Show the form for creating a donation.
     */
    public function create()
    {
        $donors = Donor::orderBy('name')->get();

        $storeItems = StoreItem::with([
            'unit',
            'variants' => function ($query) {
                $query->where('is_active', true)
                    ->orderBy('name');
            },
        ])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view(
            'admin.donations.create',
            compact(
                'donors',
                'storeItems'
            )
        );
    }


    /**
     * Store a new donation.
     */
    public function store(
        Request $request,
        DonationThankYouService $thankYouService
    ) {
        $validated = $request->validate([

            'donor_id'          => 'nullable|exists:donors,id',
            'type'              => 'required|in:cash,in_kind',
            'classification'    => 'required|in:donation,payment,refund,other,unclassified',
            'source'            => 'nullable|in:manual,mpesa,bank,paypal,other',
            'amount'            => 'nullable|numeric|min:0',
            'currency'          => 'required|string|size:3',
            'donation_date'     => 'required|date',
            'purpose'           => 'nullable|string|max:255',
            'reference'         => 'nullable|string|max:255',
            'payment_reference' => 'nullable|string|max:255',
            'description'       => 'nullable|string|max:2000',
            'notes'             => 'nullable|string|max:2000',

            'items.*.store_item_id' => 'required_with:items|exists:store_items,id',
            'items.*.variant_id' => 'nullable|exists:store_item_variants,id',
            'items.*.quantity' => 'required_with:items|numeric|min:0.001',
            'items.*.estimated_value' => 'nullable|numeric|min:0',
            'items.*.condition' => 'nullable|string|max:100',
            'items.*.notes' => 'nullable|string|max:1000',
            'items'                   => 'nullable|array',
            'items.*.item'            => 'required_with:items|string|max:255',
            'items.*.unit'            => 'nullable|string|max:100',
        ]);

        if ($validated['type'] === 'cash') {
            if (
                $validated['classification'] === 'donation' &&
                ($validated['amount'] ?? 0) <= 0
            ) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'amount' => 'Please enter the cash donation amount.',
                    ]);
            }

            $validated['source'] = $validated['source'] ?? 'manual';
        }

if ($validated['type'] === 'in_kind') {

    $items = $validated['items'] ?? [];

    if (count($items) === 0) {

        return back()
            ->withInput()
            ->withErrors([
                'items' =>
                    'Please add at least one item for an in-kind donation.',
            ]);
    }

    foreach ($items as $index => &$item) {

        $storeItem = StoreItem::with('unit')
            ->where('id', $item['store_item_id'])
            ->where('is_active', true)
            ->first();

        if (!$storeItem) {

            return back()
                ->withInput()
                ->withErrors([
                    "items.$index.store_item_id" =>
                        'The selected store item is no longer active.',
                ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Validate Variant
        |--------------------------------------------------------------------------
        */

        if (!empty($item['variant_id'])) {

            $variantExists = $storeItem->variants()
                ->where('id', $item['variant_id'])
                ->where('is_active', true)
                ->exists();

            if (!$variantExists) {

                return back()
                    ->withInput()
                    ->withErrors([
                        "items.$index.variant_id" =>
                            'The selected variant does not belong to the selected store item.',
                    ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Preserve Existing Donation Item Fields
        |--------------------------------------------------------------------------
        |
        | Existing receipts/reports can continue using item and unit.
        |
        */

        $item['item'] = $storeItem->name;

        $item['unit'] =
            $storeItem->unit->code
            ?? $storeItem->unit->name
            ?? null;
    }

    unset($item);

    $totalEstimatedValue = collect($items)->sum(function ($item) {
        return (float) ($item['estimated_value'] ?? 0);
    });

    $validated['source'] = 'manual';
    $validated['amount'] = $totalEstimatedValue;
    $validated['reference'] = null;
    $validated['payment_reference'] = null;

    $validated['items'] = $items;
}
        $donation = DB::transaction(function () use (
            $validated,
            $thankYouService
        ) {
            $nextId = (Donation::max('id') ?? 0) + 1;

            $validated['donation_number'] =
                'DON-' . str_pad($nextId, 6, '0', STR_PAD_LEFT);

            $validated['received_by'] = Auth::id();

            $items = $validated['items'] ?? [];

            unset($validated['items']);

            $donation = Donation::create($validated);

            foreach ($items as $item) {
                $item['donation_id'] = $donation->id;

                DonationItem::create($item);
            }

            $donation->load('donor');

            $thankYouService->createForDonation($donation);

            return $donation;
        });

        return redirect()
            ->route('admin.donations.show', $donation)
            ->with(
                'success',
                'Donation recorded successfully. Thank-you messages have been scheduled.'
            );
    }


    /**
     * Display a donation.
     */
    public function show(Donation $donation)
    {
        $donation->load([
            'donor',
            'items',
            'receivedBy',
        ]);

        return view('admin.donations.show', compact('donation'));
    }

    /**
     * Show the form for editing a donation.
     */
    public function edit(Donation $donation)
    {
        $donors = Donor::orderBy('name')->get();
        
        $storeItems = StoreItem::with([
            'unit',
            'variants' => function ($query) {
                $query->where('is_active', true)
                    ->orderBy('name');
            },
        ])
            ->where('is_active', true)
            ->orderBy('name')
            ->get();


        $donation->load('items');

        return view(
            'admin.donations.edit',
            compact('donation', 'donors', 'storeItems')
        );
    }

    /**
     * Update a donation.
     */
    public function update(
    Request $request,
    Donation $donation,
    WorkflowNotificationService $notificationService
) {
    $validated = $request->validate([
        'donor_id'          => 'nullable|exists:donors,id',
        'type'              => 'required|in:cash,in_kind',
        'classification'    => 'required|in:donation,payment,refund,other,unclassified',
        'source'            => 'nullable|in:manual,mpesa,bank,other',
        'amount'            => 'nullable|numeric|min:0',
        'currency'          => 'required|string|size:3',
        'donation_date'     => 'required|date',
        'purpose'           => 'nullable|string|max:255',
        'reference'         => 'nullable|string|max:255',
        'payment_reference' => 'nullable|string|max:255',
        'description'       => 'nullable|string|max:2000',
        'notes'             => 'nullable|string|max:2000',

        'items'                   => 'nullable|array',
        'items.*.item'            => 'required_with:items|string|max:255',
        'items.*.store_item_id'   => 'nullable|integer|exists:store_items,id',
        'items.*.variant_id'      => 'nullable|integer|exists:store_item_variants,id',
        'items.*.quantity'        => 'nullable|numeric|min:0',
        'items.*.unit'            => 'nullable|string|max:100',
        'items.*.estimated_value' => 'nullable|numeric|min:0',
        'items.*.condition'       => 'nullable|string|max:100',
        'items.*.notes'           => 'nullable|string|max:1000',
    ]);

    /*
    |--------------------------------------------------------------------------
    | Cash donations
    |--------------------------------------------------------------------------
    */

    if ($validated['type'] === 'cash') {
        if (
            $validated['classification'] === 'donation' &&
            ($validated['amount'] ?? 0) <= 0
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'amount' => 'Please enter the cash donation amount.',
                ]);
        }

        $validated['source'] = $validated['source'] ?? 'manual';
    }

    /*
    |--------------------------------------------------------------------------
    | In-kind donations
    |--------------------------------------------------------------------------
    */

    if ($validated['type'] === 'in_kind') {
        $items = $validated['items'] ?? [];

        if (count($items) === 0) {
            return back()
                ->withInput()
                ->withErrors([
                    'items' => 'Please add at least one item for an in-kind donation.',
                ]);
        }

        $validated['amount'] = collect($items)->sum(
            fn ($item) => (float) ($item['estimated_value'] ?? 0)
        );

        $validated['source'] = 'manual';
        $validated['reference'] = null;
        $validated['payment_reference'] = null;
    }

    $items = $validated['items'] ?? [];
    unset($validated['items']);

    /*
    |--------------------------------------------------------------------------
    | Update donation and reconcile any posted donation receipt
    |--------------------------------------------------------------------------
    */

    $result = DB::transaction(function () use (
        $validated,
        $items,
        $donation
    ) {
        // Lock the donation while updating its items.
        $lockedDonation = Donation::query()
            ->whereKey($donation->id)
            ->lockForUpdate()
            ->firstOrFail();

        // Preserve the existing items before replacing them.
        $oldItems = $lockedDonation->items()->get();

        // Only a POSTED donation receipt has affected store stock.
        $receipt = StoreReceipt::query()
            ->where('donation_id', $lockedDonation->id)
            ->where('source_type', 'DONATION')
            ->where('status', 'POSTED')
            ->lockForUpdate()
            ->first();

        $adjustments = [];

        if ($receipt) {
            /*
             * A received in-kind donation cannot be converted to cash
             * through this edit because that would require reversing
             * the donation's inventory completely.
             */
            if ($validated['type'] !== 'in_kind') {
                throw ValidationException::withMessages([
                    'type' => 'This donation has already been received into Stores. It cannot be changed to cash through this edit.',
                ]);
            }

            /*
             * Both the existing and new donation items must map to
             * actual inventory items so that stock is never adjusted
             * using free-text descriptions.
             */
            foreach ($oldItems as $oldItem) {
                if (
                    empty($oldItem->store_item_id) ||
                    $oldItem->quantity === null
                ) {
                    throw ValidationException::withMessages([
                        'items' => 'An existing donation item is not mapped to a store item. Correct its inventory mapping before editing this received donation.',
                    ]);
                }
            }

            foreach ($items as $index => $item) {
                if (empty($item['store_item_id'])) {
                    throw ValidationException::withMessages([
                        "items.$index.store_item_id" =>
                            'Select the store item for every item in this received donation.',
                    ]);
                }

                if (!array_key_exists('quantity', $item) ||
                    $item['quantity'] === null) {
                    throw ValidationException::withMessages([
                        "items.$index.quantity" =>
                            'Enter a quantity for every item in this received donation.',
                    ]);
                }

                // Confirm that the selected variant belongs to the item.
                if (!empty($item['variant_id'])) {
                    $variantBelongsToItem = DB::table('store_item_variants')
                        ->where('id', $item['variant_id'])
                        ->where('store_item_id', $item['store_item_id'])
                        ->exists();

                    if (!$variantBelongsToItem) {
                        throw ValidationException::withMessages([
                            "items.$index.variant_id" =>
                                'The selected variant does not belong to the selected store item.',
                        ]);
                    }
                }
            }

            /*
             * Aggregate quantities by store item and variant.
             * This also handles duplicate lines for the same inventory
             * item without producing incorrect stock differences.
             */
            $aggregateItems = function ($rows) {
                $totals = [];

                foreach ($rows as $row) {
                    $itemId = (int) (
                        is_array($row)
                            ? $row['store_item_id']
                            : $row->store_item_id
                    );

                    $rawVariantId = is_array($row)
                        ? ($row['variant_id'] ?? null)
                        : $row->variant_id;

                    $variantId = !empty($rawVariantId)
                        ? (int) $rawVariantId
                        : null;

                    $rawQuantity = is_array($row)
                        ? ($row['quantity'] ?? 0)
                        : $row->quantity;

                    $quantity = (float) $rawQuantity;

                    $key = $itemId . ':' . ($variantId ?? 'null');

                    if (!isset($totals[$key])) {
                        $totals[$key] = [
                            'store_item_id' => $itemId,
                            'variant_id' => $variantId,
                            'quantity' => 0.0,
                        ];
                    }

                    $totals[$key]['quantity'] += $quantity;
                }

                return $totals;
            };

            $oldTotals = $aggregateItems($oldItems);
            $newTotals = $aggregateItems($items);

            $keys = array_unique(array_merge(
                array_keys($oldTotals),
                array_keys($newTotals)
            ));

            // Process stock rows in a consistent order.
            sort($keys);

            foreach ($keys as $key) {
                $oldLine = $oldTotals[$key] ?? null;
                $newLine = $newTotals[$key] ?? null;

                $oldQuantity = $oldLine['quantity'] ?? 0.0;
                $newQuantity = $newLine['quantity'] ?? 0.0;

                $variation = round(
                    $newQuantity - $oldQuantity,
                    3
                );

                if (abs($variation) < 0.0005) {
                    continue;
                }

                $line = $newLine ?? $oldLine;

                $stockQuery = StoreStock::query()
                    ->where('store_id', $receipt->store_id)
                    ->where('store_item_id', $line['store_item_id']);

                if ($line['variant_id'] === null) {
                    $stockQuery->whereNull('variant_id');
                } else {
                    $stockQuery->where(
                        'variant_id',
                        $line['variant_id']
                    );
                }

                $stock = $stockQuery
                    ->lockForUpdate()
                    ->first();

                /*
                 * Positive adjustments may create a missing stock row.
                 * Negative adjustments must act on an existing row.
                 */
                if (!$stock && $variation < 0) {
                    throw ValidationException::withMessages([
                        'items' =>
                            'Cannot reduce stock because the corresponding stock record is missing. No changes have been saved.',
                    ]);
                }

                if (!$stock) {
                    $stock = StoreStock::create([
                        'store_id' => $receipt->store_id,
                        'store_item_id' => $line['store_item_id'],
                        'variant_id' => $line['variant_id'],
                        'quantity' => 0,
                        'last_movement_at' => null,
                    ]);

                    // Lock the newly created row before adjusting it.
                    $stock = StoreStock::query()
                        ->whereKey($stock->id)
                        ->lockForUpdate()
                        ->firstOrFail();
                }

                $currentStock = (float) $stock->quantity;
                $newStock = round(
                    $currentStock + $variation,
                    3
                );

                if ($newStock < -0.0005) {
                    throw ValidationException::withMessages([
                        'items' =>
                            'This edit would make stock negative for store item ID '
                            . $line['store_item_id']
                            . '. Current balance: '
                            . number_format($currentStock, 3)
                            . '. Required adjustment: '
                            . number_format($variation, 3)
                            . '. No changes have been saved.',
                    ]);
                }

                if ($newStock < 0) {
                    $newStock = 0;
                }

                $stock->forceFill([
                    'quantity' => $newStock,
                    'last_movement_at' => now(),
                ])->save();

                $movementNotes =
                    'Donation edited. Donation ID: '
                    . $lockedDonation->id
                    . '. Receipt ID: '
                    . $receipt->id
                    . '. Previous donation quantity: '
                    . number_format($oldQuantity, 3)
                    . '. Updated donation quantity: '
                    . number_format($newQuantity, 3)
                    . '. Stock variation: '
                    . number_format($variation, 3)
                    . '.';

                StoreStockMovement::forceCreate([
                    'store_id' => $stock->store_id,
                    'store_item_id' => $stock->store_item_id,
                    'variant_id' => $stock->variant_id,
                    'movement_type' => 'ADJUSTMENT',
                    'quantity' => $variation,
                    'balance_after' => $newStock,
                    'reference_type' => StoreStock::class,
                    'reference_id' => $stock->id,
                    'created_by' => auth()->id(),
                    'notes' => $movementNotes,
                ]);

                $adjustments[] = [
                    'store_item_id' => $line['store_item_id'],
                    'variant_id' => $line['variant_id'],
                    'previous_quantity' => $oldQuantity,
                    'updated_quantity' => $newQuantity,
                    'variation' => $variation,
                    'balance_after' => $newStock,
                ];
            }
        }

        /*
         * Save the donation and replace its item records only after
         * stock reconciliation has passed all checks.
         */
        $lockedDonation->update($validated);

        $lockedDonation->items()->delete();

        foreach ($items as $item) {
            $item['donation_id'] = $lockedDonation->id;

            DonationItem::create($item);
        }

        return [
            'donation_id' => $lockedDonation->id,
            'receipt_id' => $receipt?->id,
            'adjustments' => $adjustments,
        ];
    });

    /*
    |--------------------------------------------------------------------------
    | Notify Stores after the database transaction commits
    |--------------------------------------------------------------------------
    */

    if (
        $result['receipt_id'] !== null &&
        count($result['adjustments']) > 0
    ) {
        try {
            $itemCount = count($result['adjustments']);

            $notificationService->sendToPermission(
                [
                    'event' => 'donation.stock_adjusted',
                    'title' => 'Received Donation Modified',
                    'message' =>
                        'Donation #' . $result['donation_id']
                        . ' was modified after receipt into Stores. '
                        . $itemCount
                        . ' inventory item(s) had their stock adjusted.',
                    'url' => route(
                        'admin.donations.show',
                        $result['donation_id']
                    ),
                    'icon' => 'package',
                    'priority' => 'high',
                    'metadata' => [
                        'donation_id' => $result['donation_id'],
                        'receipt_id' => $result['receipt_id'],
                        'adjustments' => $result['adjustments'],
                    ],
                    'created_by' => auth()->id(),
                ],
                'MANAGE STORE STOCK',
                ['database', 'webpush']
            );
        } catch (\Throwable $exception) {
            // Do not undo a committed stock reconciliation if
            // notification delivery encounters an error.
            report($exception);
        }
    }

    return redirect()
        ->route('admin.donations.show', $donation)
        ->with(
            'success',
            count($result['adjustments']) > 0
                ? 'Donation updated successfully. Store stock has been reconciled and the Stores team notified.'
                : 'Donation updated successfully.'
        );
}


    public function resendThankYou(
        Request $request,
        Donation $donation,
        DonationThankYouService $thankYouService
    ) {
        $validated = $request->validate([
            'channel' => 'required|in:sms,email,both',
        ]);

        $channels = match ($validated['channel']) {
            'sms' => ['sms'],
            'email' => ['email'],
            'both' => ['sms', 'email'],
        };

        $donation->load('donor');

        if (!$donation->donor) {
            return back()->withErrors([
                'communication' =>
                'This donation does not have a donor attached to it.',
            ]);
        }

        if (
            in_array('sms', $channels, true)
            && !$donation->donor->phone
        ) {
            if ($validated['channel'] === 'sms') {
                return back()->withErrors([
                    'communication' =>
                    'This donor does not have a phone number.',
                ]);
            }
        }

        if (
            in_array('email', $channels, true)
            && !$donation->donor->email
        ) {
            if ($validated['channel'] === 'email') {
                return back()->withErrors([
                    'communication' =>
                    'This donor does not have an email address.',
                ]);
            }
        }

        $thankYouService->createForDonation(
            donation: $donation,
            delaySeconds: 30,
            channels: $channels
        );

        $channelName = match ($validated['channel']) {
            'sms' => 'SMS',
            'email' => 'email',
            'both' => 'SMS and email',
        };

        return back()->with(
            'success',
            "Thank-you {$channelName} message scheduled. "
                . 'You have 30 seconds to cancel it.'
        );
    }



/**
 * Generate a donation receipt PDF.
 */

public function receipt(Donation $donation)
{
    $donation->load([
        'donor',
        'items',
        'receivedBy',
    ]);

    // Supported receipt types: internal and donor.
    $receiptType = request('type', 'internal');

    if (!in_array($receiptType, ['internal', 'donor'], true)) {
        $receiptType = 'internal';
    }

    // Encode the donation receipt number for the verification URL.
    $encodedDonationNumber = rtrim(
        strtr(
            base64_encode($donation->donation_number),
            '+/',
            '-_'
        ),
        '='
    );

    // Generate the public verification URL.
    $verificationUrl = route('donations.verify', [
        'encodedDonationNumber' => $encodedDonationNumber,
    ]);

    $generatedAt = now();

    // Generate a unique temporary QR code SVG file.
    $qrDirectory = public_path('receipt-qr-temp');

    if (!is_dir($qrDirectory) && !mkdir($qrDirectory, 0755, true) && !is_dir($qrDirectory)) {
        throw new \RuntimeException(
            'Unable to create the temporary receipt QR directory.'
        );
    }

    $qrCodePath = $qrDirectory
        . DIRECTORY_SEPARATOR
        . 'receipt-'
        . bin2hex(random_bytes(16))
        . '.svg';

    $qrSvg = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('svg')
        ->size(120)
        ->margin(2)
        ->errorCorrection('H')
        ->generate($verificationUrl);

    if (file_put_contents($qrCodePath, $qrSvg) === false) {
        throw new \RuntimeException(
            'Unable to generate the receipt QR code image.'
        );
    }

    // Determine the receipt filename.
    $typeLabel = $receiptType === 'donor'
        ? 'Donor'
        : 'Internal';

    $filename = 'Donation-Receipt-'
        . $donation->donation_number
        . '-'
        . $typeLabel
        . '.pdf';

    try {
        // Render the receipt using Dompdf.
$pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
    'admin.donations.receipt',
    [
        'donation' => $donation,
        'generatedAt' => $generatedAt,
        'receiptType' => $receiptType,
        'verificationUrl' => $verificationUrl,
        'qrCodePath' => $qrCodePath,
    ]
);


        $pdf->setPaper('A4', 'portrait');

        $pdf->setOptions([
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
            'defaultFont' => 'DejaVu Sans',
            'chroot' => public_path(),
        ]);

        // Render the PDF before deleting the QR image.
        $pdfContent = $pdf->output();

    } finally {
        // Remove the temporary QR file after rendering.
        if (is_file($qrCodePath)) {
            unlink($qrCodePath);
        }
    }

$timestamp = now()->format('Ymd_His');

$filename = pathinfo($filename, PATHINFO_FILENAME)
    . '-'
    . $timestamp
    . '.pdf';

return response($pdfContent, 200, [
    'Content-Type' => 'application/pdf',
    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
]);

}



/**
 * Public donation receipt verification.
 *
 * Every visit requires mobile verification before showing details.
 */
public function verifyReceipt(string $encodedDonationNumber)
{
    $donationNumber = $this->decodeDonationNumber(
        $encodedDonationNumber
    );

    $donation = \App\Models\Donation::with([
        'donor',
        'items',
    ])->where(
        'donation_number',
        $donationNumber
    )->firstOrFail();

    return view('admin.donations.verify', [
        'donation' => $donation,
        'verified' => false,
        'encodedDonationNumber' => $encodedDonationNumber,
    ]);
}


/**
 * Confirm the donor's registered mobile number.
 */
public function confirmReceipt(
    \Illuminate\Http\Request $request,
    string $encodedDonationNumber
) {
    $request->validate([
        'mobile' => ['required', 'string', 'max:25'],
    ]);

    $donationNumber = $this->decodeDonationNumber(
        $encodedDonationNumber
    );

    $donation = \App\Models\Donation::with([
        'donor',
        'items',
    ])->where(
        'donation_number',
        $donationNumber
    )->firstOrFail();

    $donor = $donation->donor;

    // Check common donor mobile-number column names.
    $registeredMobile = $donor?->phone
        ?? $donor?->phone_number
        ?? $donor?->mobile
        ?? $donor?->mobile_number;

    if (
        !$registeredMobile ||
        $this->normalizeMobileNumber($request->input('mobile'))
            !== $this->normalizeMobileNumber($registeredMobile)
    ) {
        return redirect()
            ->route('donations.verify', [
                'encodedDonationNumber' => $encodedDonationNumber,
            ])
            ->withInput()
            ->with(
                'verification_error',
                'The mobile number does not match the number registered for this donation. Please check and try again.'
            );
    }

    // Only a successful mobile-number match reveals the details.
    return view('admin.donations.verify', [
        'donation' => $donation,
        'verified' => true,
        'encodedDonationNumber' => $encodedDonationNumber,
    ]);
}


/**
 * Decode and validate the URL-safe receipt number.
 */
private function decodeDonationNumber(
    string $encodedDonationNumber
): string {
    $base64 = strtr(
        $encodedDonationNumber,
        '-_',
        '+/'
    );

    $base64 .= str_repeat(
        '=',
        (4 - strlen($base64) % 4) % 4
    );

    $donationNumber = base64_decode($base64, true);

    if (
        $donationNumber === false ||
        !preg_match('/^DON-\d+$/', $donationNumber)
    ) {
        abort(404);
    }

    return $donationNumber;
}


/**
 * Normalize Kenyan mobile numbers to allow common formats:
 * 0712345678, 254712345678, and 712345678.
 */
private function normalizeMobileNumber(string $number): string
{
    $number = preg_replace('/\D+/', '', $number);

    if (str_starts_with($number, '254') && strlen($number) === 12) {
        return '0' . substr($number, 3);
    }

    if (
        strlen($number) === 9 &&
        preg_match('/^[17]/', $number)
    ) {
        return '0' . $number;
    }

    return $number;
}


public function export(
    Request $request,
    string $format,
    ReportExportService $exporter
) {
    abort_unless(in_array($format, ['pdf', 'xlsx', 'csv'], true), 404);

    $request->validate([
        'search' => ['nullable', 'string', 'max:255'],
        'date_from' => ['nullable', 'date'],
        'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        'donor_id' => ['nullable', 'integer', 'exists:donors,id'],
        'type' => ['nullable', Rule::in(['cash', 'IN_KIND'])],
        'classification' => [
            'nullable',
            Rule::in([
                'donation',
                'payment',
                'refund',
                'other',
                'unclassified',
            ]),
        ],
        'source' => ['nullable', 'string', 'max:255'],
        'receiving_status' => [
            'nullable',
            Rule::in([
                'received',
                'draft',
                'not_received',
                'not_applicable',
            ]),
        ],
        'amount_min' => ['nullable', 'numeric', 'min:0'],
        'amount_max' => ['nullable', 'numeric', 'min:0', 'gte:amount_min'],
        'currency' => ['nullable', 'string', 'max:10'],
    ]);

    $query = Donation::with('donor')
        ->addSelect([
            'posted_store_receipts_count' => DB::table('store_receipts')
                ->selectRaw('COUNT(*)')
                ->whereColumn('store_receipts.donation_id', 'donations.id')
                ->where('store_receipts.source_type', 'DONATION')
                ->where('store_receipts.status', 'POSTED'),

            'draft_store_receipts_count' => DB::table('store_receipts')
                ->selectRaw('COUNT(*)')
                ->whereColumn('store_receipts.donation_id', 'donations.id')
                ->where('store_receipts.source_type', 'DONATION')
                ->where('store_receipts.status', 'DRAFT'),
        ]);

    if ($request->filled('search')) {
        $search = trim($request->input('search'));

        $query->where(function ($q) use ($search) {
            $q->where('donation_number', 'like', "%{$search}%")
                ->orWhere('purpose', 'like', "%{$search}%")
                ->orWhere('source', 'like', "%{$search}%")
                ->orWhere('type', 'like', "%{$search}%")
                ->orWhere('classification', 'like', "%{$search}%")
                ->orWhere('currency', 'like', "%{$search}%")
                ->orWhereHas('donor', function ($donorQuery) use ($search) {
                    $donorQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('donor_number', 'like', "%{$search}%");
                });
        });
    }

    $query
        ->when(
            $request->filled('date_from'),
            fn ($q) => $q->whereDate(
                'donation_date',
                '>=',
                $request->input('date_from')
            )
        )
        ->when(
            $request->filled('date_to'),
            fn ($q) => $q->whereDate(
                'donation_date',
                '<=',
                $request->input('date_to')
            )
        )
        ->when(
            $request->filled('donor_id'),
            fn ($q) => $q->where('donor_id', $request->input('donor_id'))
        )
        ->when(
            $request->filled('type'),
            fn ($q) => $q->where(
                'type',
                $request->input('type') === 'cash' ? 'cash' : 'IN_KIND'
            )
        )
        ->when(
            $request->filled('classification'),
            function ($q) use ($request) {
                $classification = $request->input('classification');

                if ($classification === 'unclassified') {
                    $q->where(function ($subQuery) {
                        $subQuery->whereNull('classification')
                            ->orWhere('classification', '');
                    });
                } else {
                    $q->where('classification', $classification);
                }
            }
        )
        ->when(
            $request->filled('source'),
            fn ($q) => $q->where('source', $request->input('source'))
        )
        ->when(
            $request->filled('amount_min'),
            fn ($q) => $q->where('amount', '>=', $request->input('amount_min'))
        )
        ->when(
            $request->filled('amount_max'),
            fn ($q) => $q->where('amount', '<=', $request->input('amount_max'))
        )
        ->when(
            $request->filled('currency'),
            fn ($q) => $q->where('currency', $request->input('currency'))
        );

    if ($request->filled('receiving_status')) {
        $status = $request->input('receiving_status');

        $posted = fn ($subQuery) => $subQuery
            ->selectRaw('1')
            ->from('store_receipts')
            ->whereColumn('store_receipts.donation_id', 'donations.id')
            ->where('store_receipts.source_type', 'DONATION')
            ->where('store_receipts.status', 'POSTED');

        $draft = fn ($subQuery) => $subQuery
            ->selectRaw('1')
            ->from('store_receipts')
            ->whereColumn('store_receipts.donation_id', 'donations.id')
            ->where('store_receipts.source_type', 'DONATION')
            ->where('store_receipts.status', 'DRAFT');

        if ($status === 'received') {
            $query->whereExists($posted);
        } elseif ($status === 'draft') {
            $query->whereNotExists($posted)->whereExists($draft);
        } elseif ($status === 'not_received') {
            $query->whereNotExists($posted)
                ->whereNotExists($draft)
                ->whereRaw('UPPER(type) = ?', ['IN_KIND']);
        } elseif ($status === 'not_applicable') {
            $query->whereRaw('UPPER(type) != ?', ['IN_KIND']);
        }
    }

    $donations = $query
        ->latest('donation_date')
        ->latest('id')
        ->get();

    $rows = $donations->map(function ($donation) {
        $type = strtoupper((string) $donation->type);

        if ($type !== 'IN_KIND') {
            $receivingStatus = 'Not applicable';
        } elseif (($donation->posted_store_receipts_count ?? 0) > 0) {
            $receivingStatus = 'Received in stores';
        } elseif (($donation->draft_store_receipts_count ?? 0) > 0) {
            $receivingStatus = 'Receipt in Draft';
        } else {
            $receivingStatus = 'Not Received';
        }

        return [
            'Donation Number' => $donation->donation_number,
            'Date' => $donation->donation_date?->format('Y-m-d'),
            'Donor' => $donation->donor?->name ?? 'Anonymous',
            'Donor Number' => $donation->donor?->donor_number ?? '—',
            'Type' => $donation->type,
            'Amount' => $donation->amount,
            'Currency' => $donation->currency,
            'Purpose' => $donation->purpose ?? '—',
            'Source' => $donation->source ?? '—',
            'Classification' => $donation->classification ?? 'Unclassified',
            'Store Receiving Status' => $receivingStatus,
        ];
    });

    $headings = [
        'Donation Number',
        'Date',
        'Donor',
        'Donor Number',
        'Type',
        'Amount',
        'Currency',
        'Purpose',
        'Source',
        'Classification',
        'Store Receiving Status',
    ];

    $filters = $request->only([
        'search',
        'date_from',
        'date_to',
        'donor_id',
        'type',
        'classification',
        'source',
        'receiving_status',
        'amount_min',
        'amount_max',
        'currency',
    ]);

    ReportAuditLog::create([
        'user_id' => $request->user()?->id,
        'report_key' => 'donations.records',
        'report_title' => 'Donations Report',
        'format' => $format,
        'filters' => $filters,
        'date_from' => $request->input('date_from'),
        'date_to' => $request->input('date_to'),
        'row_count' => $rows->count(),
    ]);

    $filename = 'teule-donations-' . now()->format('Ymd-His');

    if ($format === 'pdf') {
        return Pdf::loadView('admin.donations.reports.pdf', [
            'title' => 'Donations Report',
            'headings' => $headings,
            'rows' => $rows,
            'generatedAt' => now(),
            'filters' => $filters,
        ])->setPaper('a4', 'landscape')
            ->download($filename . '.pdf');
    }

    return $exporter->download(
        $headings,
        $rows->map(fn ($row) => array_values($row))->all(),
        $filename,
        $format
    );
}




}
