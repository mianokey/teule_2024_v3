<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Donor;
use App\Models\MpesaTransaction;
use App\Services\DonationThankYouService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Throwable;

class MpesaTransactionController extends Controller
{
    /**
     * Display M-Pesa transactions awaiting staff review.
     */
    public function index()
    {
        $transactions = MpesaTransaction::with([
            'donor',
            'reviewedBy',
        ])
            ->latest('received_at')
            ->latest('id')
            ->get();

        return view(
            'admin.mpesa_transactions.index',
            compact('transactions')
        );
    }

    /**
     * Display a single M-Pesa transaction.
     */
public function show(MpesaTransaction $mpesaTransaction)
{
    $mpesaTransaction->load([
        'donor',
        'donation',
        'reviewedBy',
    ]);

    $donors = Donor::orderBy('name')->get();

    $matchedDonor = null;

    /*
     * 1. If the transaction already has a donor, keep that.
     */
    if ($mpesaTransaction->donor) {

        $matchedDonor = $mpesaTransaction->donor;

    } elseif ($mpesaTransaction->phone_number) {

        /*
         * 2. Safaricom sends the MSISDN as a SHA-256 hash.
         *
         * We therefore normalize each donor phone number to:
         *
         *     254XXXXXXXXX
         *
         * then hash it and compare it with the value
         * received from Safaricom.
         */

        $safaricomHash = strtolower(
            trim($mpesaTransaction->phone_number)
        );

        foreach ($donors as $donor) {

            if (!$donor->phone) {
                continue;
            }

            // Remove spaces, +, -, brackets, etc.
            $normalizedPhone = preg_replace(
                '/\D+/',
                '',
                $donor->phone
            );

            // Convert Kenyan 07XXXXXXXX / 01XXXXXXXX
            // to 2547XXXXXXXX / 2541XXXXXXXX
            if (str_starts_with($normalizedPhone, '0')) {

                $normalizedPhone =
                    '254' . substr($normalizedPhone, 1);

            }

            // If stored as 254XXXXXXXXX, leave it unchanged.
            // Hash the normalized number.
            $donorHash = hash(
                'sha256',
                $normalizedPhone
            );

            if (hash_equals($safaricomHash, $donorHash)) {

                $matchedDonor = $donor;

                break;
            }
        }
    }

    return view(
        'admin.mpesa_transactions.show',
        compact(
            'mpesaTransaction',
            'donors',
            'matchedDonor'
        )
    );
}

    /**
     * Confirm and classify an M-Pesa transaction.
     */
    public function confirm(
        Request $request,
        MpesaTransaction $mpesaTransaction,
        DonationThankYouService $thankYouService
    ) {
        if ($mpesaTransaction->status !== 'pending_review') {
            return redirect()
                ->route(
                    'admin.mpesa-transactions.show',
                    $mpesaTransaction
                )
                ->with(
                    'error',
                    'This transaction has already been reviewed.'
                );
        }

        $validated = $request->validate([
            'classification' => [
                'required',
                Rule::in([
                    'donation',
                    'payment',
                    'refund',
                    'other',
                    'unclassified',
                ]),
            ],

            'donor_id' => [
                'nullable',
                'integer',
                'exists:donors,id',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ]);

        try {
            DB::transaction(function () use (
                $mpesaTransaction,
                $validated,
                $thankYouService
            ) {
                $donation = null;

                /*
                 * Only a transaction classified as a donation
                 * becomes a Donation record.
                 */
                if ($validated['classification'] === 'donation') {

                    $donationDate = now();

                    if ($mpesaTransaction->trans_time) {
                        try {
                            $donationDate = Carbon::createFromFormat(
                                'YmdHis',
                                $mpesaTransaction->trans_time
                            );
                        } catch (Throwable $e) {
                            // Keep current date if the M-Pesa
                            // transaction time cannot be parsed.
                        }
                    }

/*
 * Generate the next donation number before creating
 * the donation because donation_number is required
 * by the database.
 */
$nextDonationId = (Donation::max('id') ?? 0) + 1;

$donationNumber = 'DON-' . str_pad(
    $nextDonationId,
    6,
    '0',
    STR_PAD_LEFT
);

$donation = Donation::create([
    'donation_number' => $donationNumber,

    'donor_id' => $validated['donor_id'] ?? null,

    'type' => 'cash',

    'source' => 'mpesa',

    'classification' => 'donation',

    'amount' => $mpesaTransaction->amount,

    'currency' => 'KES',

    'donation_date' => $donationDate,

    'purpose' => $mpesaTransaction->bill_ref_number,

    'reference' => $mpesaTransaction->transaction_id,

    'payment_reference' => $mpesaTransaction->transaction_id,

    'description' =>
        'M-Pesa Paybill donation. Transaction ID: '
        . $mpesaTransaction->transaction_id,

    'notes' => $validated['notes'] ?? null,

    'received_by' => auth()->id(),
]);

                    /*
                     * Generate the normal Teule donation number
                     * using the newly-created donation ID.
                     */
                    $donation->update([
                        'donation_number' =>
                            'DON-' . str_pad(
                                $donation->id,
                                6,
                                '0',
                                STR_PAD_LEFT
                            ),
                    ]);

                    /*
                     * Link the M-Pesa transaction to the
                     * newly-created donation.
                     */
                    $mpesaTransaction->donation_id = $donation->id;
                }

                /*
                 * Update the M-Pesa transaction review information.
                 */
                $mpesaTransaction->update([
                    'donor_id' => $validated['donor_id'] ?? null,

                    'classification' =>
                        $validated['classification'],

                    'status' => 'confirmed',

                    'reviewed_by' => auth()->id(),

                    'reviewed_at' => now(),

                    'notes' => $validated['notes'] ?? null,

                    'donation_id' => $donation?->id,
                ]);

                /*
                 * Schedule the existing thank-you workflow
                 * only for actual donations.
                 *
                 * This preserves the existing 30-second delay
                 * and SMS/email behaviour.
                 */
                if ($donation) {
                    $thankYouService->createForDonation(
                        $donation
                    );
                }
            });

            return redirect()
                ->route(
                    'admin.mpesa-transactions.show',
                    $mpesaTransaction
                )
                ->with(
                    'success',
                    'M-Pesa transaction confirmed successfully.'
                );

        } catch (Throwable $e) {

            report($e);

            return redirect()
                ->route(
                    'admin.mpesa-transactions.show',
                    $mpesaTransaction
                )
                ->with(
                    'error',
                    'The M-Pesa transaction could not be confirmed. '
                    . 'No changes were saved.'
                );
        }
    }

    /**
     * Reject an M-Pesa transaction.
     */
    public function reject(
        MpesaTransaction $mpesaTransaction
    ) {
        if ($mpesaTransaction->status !== 'pending_review') {
            return redirect()
                ->route(
                    'admin.mpesa-transactions.show',
                    $mpesaTransaction
                )
                ->with(
                    'error',
                    'This transaction has already been reviewed.'
                );
        }

        $mpesaTransaction->update([
            'status' => 'rejected',

            'reviewed_by' => auth()->id(),

            'reviewed_at' => now(),
        ]);

        return redirect()
            ->route(
                'admin.mpesa-transactions.show',
                $mpesaTransaction
            )
            ->with(
                'success',
                'M-Pesa transaction rejected successfully.'
            );
    }
}