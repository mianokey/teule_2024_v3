<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StoreRequisition;
use App\Services\StoreFulfillmentService;
use Illuminate\Http\Request;
use RuntimeException;

class StoreFulfillmentController extends Controller
{
    public function store(
        Request $request,
        StoreRequisition $storeRequisition,
        StoreFulfillmentService $service
    ) {
        $validated = $request->validate([
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
                'min:0.001',
            ],

            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ]);

        try {
            $fulfillment = $service->fulfill(
                $storeRequisition,
                $validated['items'],
                auth()->id(),
                $validated['notes'] ?? null
            );

            return redirect()
                ->route(
                    'admin.stores.store-requisitions.show',
                    $storeRequisition
                )
                ->with(
                    'success',
                    'Fulfillment ' .
                    $fulfillment->transaction_number .
                    ' processed successfully.'
                );

        } catch (RuntimeException $e) {

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }
}