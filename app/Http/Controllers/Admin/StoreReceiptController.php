<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\StoreItem;
use App\Models\StoreReceipt;
use App\Services\StoreReceiptPostingService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class StoreReceiptController extends Controller
{
    public function index()
    {
        $receipts = StoreReceipt::with([
            'store',
            'receivedBy',
        ])
            ->latest()
            ->get();

        return view(
            'admin.stores.receipts.index',
            compact('receipts')
        );
    }

    public function create()
    {
        $stores = Store::where('is_active', true)
            ->orderBy('name')
            ->get();

        $items = StoreItem::with([
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
            'admin.stores.receipts.create',
            compact('stores', 'items')
        );
    }

    public function store(Request $request)
    {
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
                'exists:donations,id',
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
        ]);

        /*
         * Donation receipts must have a donation reference.
         */
        if (
            $validated['source_type'] === 'DONATION' &&
            empty($validated['donation_id'])
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'donation_id' =>
                        'A donation must be selected for a donation receipt.',
                ]);
        }

        /*
         * Purchase receipts should not be linked
         * to an existing donation.
         */
        if ($validated['source_type'] === 'PURCHASE') {
            $validated['donation_id'] = null;
        }

        /*
         * Generate a unique receipt number.
         */
        $validated['receipt_number'] = $this->generateReceiptNumber();

        /*
         * The person creating the receipt is recorded
         * as the receiving officer.
         */
        $validated['received_by'] = auth()->id();

        /*
         * New receipts start as DRAFT.
         *
         * IMPORTANT:
         * No stock is changed at this stage.
         */
        $validated['status'] = 'DRAFT';

        $receipt = StoreReceipt::create($validated);

        return redirect()
            ->route(
                'admin.store-receipts.show',
                $receipt
            )
            ->with(
                'success',
                'Store receipt created successfully. Add the items received before posting the receipt.'
            );
    }

public function show(StoreReceipt $storeReceipt)
{
    $storeReceipt->load([
        'store',
        'receivedBy',
        'donation',
        'items.item.unit',
        'items.variant',
    ]);

    $items = StoreItem::with([
        'unit',
        'variants' => function ($query) {
            $query->where('is_active', true)
                ->orderBy('name');
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

    return view(
        'admin.stores.receipts.show',
        compact('storeReceipt', 'items', 'itemVariants')
    );
}

    public function edit(StoreReceipt $storeReceipt)
    {
        /*
         * Only draft receipts can be edited.
         */
        if ($storeReceipt->status !== 'DRAFT') {
            return redirect()
                ->route(
                    'admin.store-receipts.show',
                    $storeReceipt
                )
                ->with(
                    'error',
                    'Only draft store receipts can be edited.'
                );
        }

        $stores = Store::where('is_active', true)
            ->orderBy('name')
            ->get();

        if (
            $storeReceipt->store &&
            !$stores->contains(
                'id',
                $storeReceipt->store_id
            )
        ) {
            $stores->push($storeReceipt->store);

            $stores = $stores
                ->sortBy('name')
                ->values();
        }

        return view(
            'admin.stores.receipts.edit',
            compact(
                'storeReceipt',
                'stores'
            )
        );
    }

    public function update(
        Request $request,
        StoreReceipt $storeReceipt
    ) {
        if ($storeReceipt->status !== 'DRAFT') {
            return redirect()
                ->route(
                    'admin.store-receipts.show',
                    $storeReceipt
                )
                ->with(
                    'error',
                    'Only draft store receipts can be edited.'
                );
        }

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
                'exists:donations,id',
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
        ]);

        if (
            $validated['source_type'] === 'DONATION' &&
            empty($validated['donation_id'])
        ) {
            return back()
                ->withInput()
                ->withErrors([
                    'donation_id' =>
                        'A donation must be selected for a donation receipt.',
                ]);
        }

        if ($validated['source_type'] === 'PURCHASE') {
            $validated['donation_id'] = null;
        }

        $storeReceipt->update($validated);

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
        /*
         * Never allow deletion after posting.
         */
        if ($storeReceipt->status !== 'DRAFT') {
            return redirect()
                ->route('admin.store-receipts.index')
                ->with(
                    'error',
                    'Only draft store receipts can be deleted.'
                );
        }

        $storeReceipt->delete();

        return redirect()
            ->route('admin.store-receipts.index')
            ->with(
                'success',
                'Store receipt deleted successfully.'
            );
    }

    private function generateReceiptNumber(): string
    {
        do {
            $number = 'SR-' . now()->format('YmdHis');

            /*
             * Add a random suffix to protect against
             * two receipts being created in the same second.
             */
            $number .= '-' . strtoupper(
                substr(
                    bin2hex(random_bytes(3)),
                    0,
                    6
                )
            );
        } while (
            StoreReceipt::where(
                'receipt_number',
                $number
            )->exists()
        );

        return $number;
    }

    public function post(
    StoreReceipt $storeReceipt,
    StoreReceiptPostingService $postingService
) {
    try {

        $postingService->post($storeReceipt);

        return redirect()
            ->route(
                'admin.store-receipts.show',
                $storeReceipt
            )
            ->with(
                'success',
                'Store receipt posted successfully. Stock has been updated.'
            );

    } catch (\RuntimeException $e) {

        return redirect()
            ->route(
                'admin.store-receipts.show',
                $storeReceipt
            )
            ->with(
                'error',
                $e->getMessage()
            );
    }
}

}