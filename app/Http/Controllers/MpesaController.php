<?php

namespace App\Http\Controllers;

use App\Models\MpesaTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class MpesaController extends Controller
{
    /**
     * Receive M-Pesa C2B validation requests.
     *
     * For now, we accept the transaction.
     * The actual transaction will be stored by the
     * confirmation callback.
     */
    public function validation(Request $request): JsonResponse
    {
        Log::info('M-Pesa C2B validation callback received.', [
            'payload' => $request->all(),
        ]);

        return response()->json([
            'ResultCode' => 0,
            'ResultDesc' => 'Accepted',
        ]);
    }

    /**
     * Receive M-Pesa C2B confirmation requests.
     *
     * Every transaction is stored for staff review.
     * No donation is automatically confirmed here.
     * No thank-you message is automatically sent here.
     */
    public function confirmation(Request $request): JsonResponse
    {
        $data = $request->all();

        Log::info('M-Pesa C2B confirmation callback received.', [
            'payload' => $data,
        ]);

        try {
            $transactionId = $data['TransID'] ?? null;

            if (!$transactionId) {
                Log::warning(
                    'M-Pesa confirmation received without TransID.',
                    [
                        'payload' => $data,
                    ]
                );

                return response()->json([
                    'ResultCode' => 1,
                    'ResultDesc' => 'Missing transaction ID',
                ]);
            }

            /*
             * Prevent duplicate processing if Safaricom
             * sends the same transaction more than once.
             */
            $transaction = MpesaTransaction::firstOrCreate(
                [
                    'transaction_id' => $transactionId,
                ],
                [
                    'transaction_type' => $data['TransactionType'] ?? null,
                    'trans_time' => $data['TransTime'] ?? null,
                    'amount' => $data['TransAmount'] ?? null,
                    'business_short_code' => $data['BusinessShortCode'] ?? null,
                    'bill_ref_number' => $data['BillRefNumber'] ?? null,
                    'invoice_number' => $data['InvoiceNumber'] ?? null,
                    'phone_number' => $data['MSISDN'] ?? null,
                    'first_name' => $data['FirstName'] ?? null,
                    'middle_name' => $data['MiddleName'] ?? null,
                    'last_name' => $data['LastName'] ?? null,
                    'org_account_balance' => $data['OrgAccountBalance'] ?? null,
                    'third_party_trans_id' => $data['ThirdPartyTransID'] ?? null,
                    'classification' => 'unclassified',
                    'status' => 'pending_review',
                    'raw_payload' => $data,
                    'received_at' => now(),
                ]
            );

            Log::info('M-Pesa transaction stored.', [
                'mpesa_transaction_id' => $transaction->id,
                'transaction_id' => $transaction->transaction_id,
                'was_recently_created' => $transaction->wasRecentlyCreated,
            ]);

            return response()->json([
                'ResultCode' => 0,
                'ResultDesc' => 'Accepted',
            ]);
        } catch (Throwable $e) {
            Log::error('M-Pesa confirmation processing failed.', [
                'error' => $e->getMessage(),
                'payload' => $data,
            ]);

            /*
             * Return a valid response to Safaricom while keeping
             * the error in our application log for investigation.
             */
            return response()->json([
                'ResultCode' => 1,
                'ResultDesc' => 'Transaction processing failed',
            ]);
        }
    }
}