<?php

namespace App\Http\Controllers\Admin\MpesaCollector;

use App\Http\Controllers\Controller;
use App\Models\MpesaCollectorMessage;
use App\Services\MpesaCollector\MpesaMessageProcessingService;
use Illuminate\Http\Request;

class MpesaCollectorMessageManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = MpesaCollectorMessage::with([
            'device',
            'transaction',
        ]);

        // Search by SMS content, sender or transaction receipt.
        if ($request->filled('q')) {
            $search = trim($request->q);

            $query->where(function ($q) use ($search) {
                $q->where('raw_message', 'LIKE', "%{$search}%")
                    ->orWhere('sender', 'LIKE', "%{$search}%")
                    ->orWhereHas('transaction', function ($transaction) use ($search) {
                        $transaction->where(
                            'receipt_number',
                            'LIKE',
                            "%{$search}%"
                        );
                    });
            });
        }

        // Filter by processing status.
        if ($request->filled('status')) {
            $query->where(
                'processing_status',
                $request->status
            );
        }

        // Filter by registered device.
        if ($request->filled('device_id')) {
            $query->where(
                'mpesa_collector_device_id',
                $request->device_id
            );
        }

       $messages = $query
    ->latest('uploaded_at')
    ->paginate(20);

$messages->appends($request->query());


        $counts = [
            'all' => MpesaCollectorMessage::count(),
            'pending' => MpesaCollectorMessage::where(
                'processing_status', 'pending'
            )->count(),
            'processed' => MpesaCollectorMessage::where(
                'processing_status', 'processed'
            )->count(),
            'unrecognized' => MpesaCollectorMessage::where(
                'processing_status', 'unrecognized'
            )->count(),
            'failed' => MpesaCollectorMessage::where(
                'processing_status', 'failed'
            )->count(),
        ];

        $devices = \App\Models\MpesaCollectorDevice::orderBy(
            'device_name'
        )->get(['id', 'device_id', 'device_name']);

        return view(
            'admin.mpesa-collector.messages',
            compact('messages', 'counts', 'devices')
        );
    }
    public function retry(
    $id,
    MpesaMessageProcessingService $processingService
) {
    $message = MpesaCollectorMessage::findOrFail($id);

    if (!in_array($message->processing_status, [
        'failed',
        'unrecognized',
    ], true)) {
        return redirect()
            ->route('admin.mpesa-collector.messages.index')
            ->with('error', 'Only failed or unrecognized messages can be retried.');
    }

    $processingService->retry($message);

    $message->refresh();

    if ($message->processing_status === 'processed') {
        return redirect()
            ->route('admin.mpesa-collector.messages.index')
            ->with('success', 'Message successfully reprocessed.');
    }

    return redirect()
        ->route('admin.mpesa-collector.messages.index')
        ->with('error', 'Message retry completed with status: ' . $message->processing_status);
}

public function retryAll(Request $request, MpesaMessageProcessingService $processingService)
{
    $query = MpesaCollectorMessage::query()
        ->whereIn('processing_status', ['failed', 'unrecognized']);

    // Respect the current search filter
    if ($request->filled('q')) {
        $search = trim($request->q);

        $query->where(function ($q) use ($search) {
            $q->where('raw_message', 'LIKE', "%{$search}%")
                ->orWhere('sender', 'LIKE', "%{$search}%")
                ->orWhereHas('transaction', function ($transaction) use ($search) {
                    $transaction->where('receipt_number', 'LIKE', "%{$search}%");
                });
        });
    }

    // Respect the selected device
    if ($request->filled('device_id')) {
        $query->where('mpesa_collector_device_id', $request->device_id);
    }

    $attempted = 0;

    $query->chunkById(100, function ($messages) use ($processingService, &$attempted) {
        foreach ($messages as $message) {
            $processingService->retry($message);
            $attempted++;
        }
    });

    return redirect()
        ->route('admin.mpesa-collector.messages.index', $request->only([
            'q',
            'status',
            'device_id',
        ]))
        ->with('success', "Retry completed. {$attempted} eligible messages were submitted for reprocessing.");
}

}