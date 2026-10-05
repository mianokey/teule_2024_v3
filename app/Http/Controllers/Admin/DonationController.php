<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\DonationItem;
use App\Models\Donor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\DonationThankYouService;
use App\Models\StoreItem;
use App\Models\StoreItemVariant;

class DonationController extends Controller
{
    /**
     * Display all donations.
     */
    public function index()
    {
        $donations = Donation::with([
            'donor',
            'receivedBy',
        ])
            ->latest('donation_date')
            ->latest('id')
            ->get();

        return view('admin.donations.index', compact('donations'));
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
            'source'            => 'nullable|in:manual,mpesa,bank,other',
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

        $donation->load('items');

        return view(
            'admin.donations.edit',
            compact('donation', 'donors')
        );
    }

    /**
     * Update a donation.
     */
    public function update(Request $request, Donation $donation)
    {
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
            'items.*.quantity'        => 'nullable|numeric|min:0',
            'items.*.unit'            => 'nullable|string|max:100',
            'items.*.estimated_value' => 'nullable|numeric|min:0',
            'items.*.condition'       => 'nullable|string|max:100',
            'items.*.notes'           => 'nullable|string|max:1000',
        ]);

        /*
    |--------------------------------------------------------------------------
    | Cash
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

            $validated['source'] =
                $validated['source'] ?? 'manual';
        }

        /*
    |--------------------------------------------------------------------------
    | In-Kind
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

            /*
        |--------------------------------------------------------------------------
        | Calculate total estimated value on the server.
        |--------------------------------------------------------------------------
        */

            $totalEstimatedValue = collect($items)->sum(function ($item) {
                return (float) ($item['estimated_value'] ?? 0);
            });

            $validated['amount'] = $totalEstimatedValue;

            // In-kind donations are manually received for now.
            $validated['source'] = 'manual';

            // These don't apply to in-kind donations.
            $validated['reference'] = null;
            $validated['payment_reference'] = null;
        }

        DB::transaction(function () use ($validated, $donation) {

            $items = $validated['items'] ?? [];

            unset($validated['items']);

            $donation->update($validated);

            /*
        |--------------------------------------------------------------------------
        | Replace existing in-kind items.
        |--------------------------------------------------------------------------
        */

            $donation->items()->delete();

            foreach ($items as $item) {

                $item['donation_id'] = $donation->id;

                DonationItem::create($item);
            }
        });

        return redirect()
            ->route('admin.donations.show', $donation)
            ->with('success', 'Donation updated successfully.');
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


    public function receipt(\App\Models\Donation $donation)
    {
        $donation->load([
            'donor',
            'items',
            'receivedBy',
        ]);

        /*
    |--------------------------------------------------------------------------
    | Receipt Type
    |--------------------------------------------------------------------------
    | internal = includes estimated values for in-kind donations
    | donor    = excludes estimated values for in-kind donations
    |--------------------------------------------------------------------------
    */

        $receiptType = request('type', 'internal');

        // Only allow the two supported receipt types
        if (!in_array($receiptType, ['internal', 'donor'], true)) {
            $receiptType = 'internal';
        }

        $generatedAt = now();

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView(
            'admin.donations.receipt',
            [
                'donation'   => $donation,
                'generatedAt' => $generatedAt,
                'receiptType' => $receiptType,
            ]
        );

        /*
    |--------------------------------------------------------------------------
    | Filename
    |--------------------------------------------------------------------------
    */

        $typeLabel = $receiptType === 'donor'
            ? 'Donor'
            : 'Internal';

        $filename = 'Donation-Receipt-' .
            $donation->donation_number .
            '-' .
            $typeLabel .
            '.pdf';

        return $pdf->download($filename);
    }
}
