<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreFulfillment;
use App\Models\StoreRequisition;
use App\Models\StoreRequisitionItem;
use App\Models\StoreStock;
use App\Services\StoreFulfillmentService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StoreFulfillmentController extends Controller
{
    /**
     * ================================================================
     * STORE FULFILLMENT
     * ================================================================
     */
public function store(
    Request $request,
    StoreRequisition $storeRequisition,
    StoreFulfillmentService $service
) {
    try {
        $validated = $request->validate([
            'mode' => [
                'required',
                'in:available,all_or_nothing',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'items' => [
                'required',
                'array',
                'min:1',
            ],
            'items.*.requisition_item_id' => [
                'required',
                'integer',
            ],
            'items.*.quantity' => [
                'required',
                'numeric',
                'gt:0',
            ],
        ]);

        $items = [];

        foreach ($validated['items'] as $line) {
            $requisitionItemId = (int) $line['requisition_item_id'];
            $quantity = (float) $line['quantity'];

            if ($quantity <= 0) {
                continue;
            }

            $items[] = [
                'requisition_item_id' => $requisitionItemId,
                'quantity' => $quantity,
            ];
        }

        if (empty($items)) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Please select at least one item to fulfill.');
        }

        $fulfillment = $service->fulfill(
            $storeRequisition,
            $items,
            (int) Auth::id(),
            $validated['notes'] ?? null,
            $validated['mode']
        );

        $message =
            'Fulfillment ' .
            $fulfillment->transaction_number .
            ' was processed successfully.';

        if (
            $fulfillment->notes &&
            str_contains(
                $fulfillment->notes,
                'Outstanding balance carried forward'
            )
        ) {
            $message .=
                ' Outstanding balances were carried forward to a new requisition.';
        }

        return redirect()
            ->back()
            ->with('success', $message);

    } catch (\Throwable $e) {

        /*
         * TEMPORARY DIAGNOSTIC RESPONSE
         *
         * This deliberately exposes the exact exception so we can
         * identify the failing operation.
         */
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => collect($e->getTrace())->take(8)->map(function ($trace) {
                return [
                    'file' => $trace['file'] ?? null,
                    'line' => $trace['line'] ?? null,
                    'function' => $trace['function'] ?? null,
                    'class' => $trace['class'] ?? null,
                ];
            })->values(),
        ], 500);
    }
}

    /**
     * ================================================================
     * CANCEL REQUISITION
     * ================================================================
     */
    public function cancel(
        Request $request,
        StoreRequisition $storeRequisition,
        StoreFulfillmentService $service
    ) {
        $validated = $request->validate([
            'reason' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        try {
            $service->cancel(
                $storeRequisition,
                (int) Auth::id(),
                $validated['reason'] ?? null
            );

            return redirect()
                ->back()
                ->with(
                    'success',
                    'Requisition ' .
                    $storeRequisition->requisition_number .
                    ' was cancelled successfully.'
                );

        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }

    /**
     * ================================================================
     * DOWNLOAD FULFILLMENT RECEIPT
     * ================================================================
     */
    public function receipt(
        StoreFulfillment $fulfillment
    ) {
        /**
         * ============================================================
         * LOAD ALL REQUIRED RELATIONSHIPS
         * ============================================================
         */
        $fulfillment->load([
            'requisition.requester',
            'requisition.sourceStore',
            'requisition.destinationStore',

            'requisition.items.item',
            'requisition.items.variant',

            'items.requisitionItem',
            'items.requisitionItem.item',
            'items.requisitionItem.variant',

            'items.item',
            'items.variant',

            'processor',
            'sourceStore',
            'destinationStore',
        ]);

        $requisition = $fulfillment->requisition;

        /**
         * ============================================================
         * GET ALL FULFILLMENTS IN ORDER
         * ============================================================
         */
        $allFulfillments = $requisition
            ->fulfillments()
            ->with('items')
            ->orderBy('created_at')
            ->orderBy('id')
            ->get();

        /**
         * ============================================================
         * CALCULATE PREVIOUSLY ISSUED
         * ============================================================
         */
        $previousIssuedByItem = [];

        foreach ($allFulfillments as $existingFulfillment) {
            /**
             * Stop once we reach the current fulfillment.
             *
             * This means the current transaction is NOT included
             * in "Previously Issued".
             */
            if (
                (int) $existingFulfillment->id ===
                (int) $fulfillment->id
            ) {
                break;
            }

            foreach ($existingFulfillment->items as $existingItem) {
                $key = (int) $existingItem->store_requisition_item_id;

                $previousIssuedByItem[$key] =
                    ($previousIssuedByItem[$key] ?? 0) +
                    (float) $existingItem->quantity;
            }
        }

        /**
         * ============================================================
         * PREPARE RECEIPT ITEMS
         * ============================================================
         */
        $receiptItems = $fulfillment->items->map(
            function ($fulfillmentItem) use ($previousIssuedByItem) {

                $requisitionItem =
                    $fulfillmentItem->requisitionItem;

                $approved = (float) (
                    $requisitionItem->approved_quantity ?? 0
                );

                $previouslyIssued = (float) (
                    $previousIssuedByItem[
                        $requisitionItem->id
                    ] ?? 0
                );

                $issuedNow = (float) $fulfillmentItem->quantity;

                $balance = max(
                    0,
                    $approved -
                    $previouslyIssued -
                    $issuedNow
                );

                $itemModel = $requisitionItem->item;

                $unit =
                    data_get(
                        $itemModel,
                        'unit.name'
                    )
                    ??
                    data_get(
                        $itemModel,
                        'unit'
                    )
                    ??
                    '—';

                return [
                    'fulfillment_item' => $fulfillmentItem,

                    'requisition_item' => $requisitionItem,

                    'approved' => $approved,

                    'previously_issued' => $previouslyIssued,

                    'issued_now' => $issuedNow,

                    'balance' => $balance,

                    'unit' => $unit,
                ];
            }
        );

        /**
         * ============================================================
         * CALCULATE RECEIPT TOTALS
         * ============================================================
         */
        $totalIssuedNow = $receiptItems->sum(
            function ($row) {
                return (float) (
                    $row['issued_now'] ?? 0
                );
            }
        );

        $totalBalance = $receiptItems->sum(
            function ($row) {
                return (float) (
                    $row['balance'] ?? 0
                );
            }
        );

        /**
         * ============================================================
         * RECEIPT GENERATION TIMESTAMP
         * ============================================================
         */
        $generatedAt = now();

        /**
         * ============================================================
         * GENERATE PDF
         * ============================================================
         */
        $pdf = Pdf::loadView(
            'admin.stores.store-requisitions.fulfillment-receipt',
            [
                'fulfillment' => $fulfillment,
                'requisition' => $requisition,
                'receiptItems' => $receiptItems,

                'totalIssuedNow' => $totalIssuedNow,
                'totalBalance' => $totalBalance,

                'generatedAt' => $generatedAt,
            ]
        )
        ->setPaper(
            'a4',
            'portrait'
        );

        return $pdf->download(
            $fulfillment->transaction_number .
            '-receipt.pdf'
        );
    }
}