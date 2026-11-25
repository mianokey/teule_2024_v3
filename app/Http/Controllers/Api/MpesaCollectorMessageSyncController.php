<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MpesaCollectorMessage;
use App\Models\MpesaCollectorSyncLog;
use App\Services\MpesaCollector\MpesaMessageProcessingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class MpesaCollectorMessageSyncController extends Controller
{
    public function sync(
        Request $request,
        MpesaMessageProcessingService $processingService
    ) {
        $validated = $request->validate([
            'messages' => ['required', 'array', 'min:1', 'max:100'],
            'messages.*.sender' => ['required', 'string', 'max:100'],
            'messages.*.raw_message' => ['required', 'string'],
            'messages.*.message_received_at' => ['required', 'date'],
            'messages.*.metadata' => ['nullable', 'array'],
            'messages.*.client_id' => ['nullable', 'string', 'max:100'],
        ]);

        $device = $request->attributes->get(
            'mpesa_collector_device'
        );

        $startedAt = now();

        $syncLog = MpesaCollectorSyncLog::create([
            'mpesa_collector_device_id' => $device->id,
            'sync_reference' => (string) Str::uuid(),
            'messages_received' => count($validated['messages']),
            'sync_status' => 'processing',
            'started_at' => $startedAt,
        ]);

        $results = [];

        $summary = [
            'received' => count($validated['messages']),
            'saved' => 0,
            'duplicates' => 0,
            'processed' => 0,
            'unrecognized' => 0,
            'processing_failed' => 0,
        ];

        try {
            foreach ($validated['messages'] as $message) {

                $sender = trim($message['sender']);
                $rawMessage = $message['raw_message'];
                $receivedAt = $message['message_received_at'];

                // Generate a stable hash for duplicate prevention.
                $messageHash = hash(
                    'sha256',
                    $sender . '|' . $receivedAt . '|' . $rawMessage
                );

                $inserted = DB::table('mpesa_collector_messages')
                    ->insertOrIgnore([
                        'mpesa_collector_device_id' => $device->id,
                        'sender' => $sender,
                        'raw_message' => $rawMessage,
                        'message_received_at' => $receivedAt,
                        'message_hash' => $messageHash,
                        'processing_status' => 'pending',
                        'metadata' => isset($message['metadata'])
                            ? json_encode($message['metadata'])
                            : null,
                        'uploaded_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                if ($inserted > 0) {

                    $summary['saved']++;

                    $storedMessage = MpesaCollectorMessage::where(
                        'mpesa_collector_device_id',
                        $device->id
                    )
                        ->where('message_hash', $messageHash)
                        ->firstOrFail();

                    // Process the newly saved SMS immediately.
                    $processingStatus = $processingService->process(
                        $storedMessage
                    );

                    if ($processingStatus === 'processed') {
                        $summary['processed']++;
                    } elseif ($processingStatus === 'unrecognized') {
                        $summary['unrecognized']++;
                    } else {
                        $summary['processing_failed']++;
                    }

                    $status = 'saved';

                } else {

                    // Confirm that the ignored insert was actually a duplicate.
                    $existingMessage = MpesaCollectorMessage::where(
                        'mpesa_collector_device_id',
                        $device->id
                    )
                        ->where('message_hash', $messageHash)
                        ->first();

                    if ($existingMessage) {
                        $summary['duplicates']++;

                        $processingStatus = $existingMessage->processing_status;

                        $status = 'duplicate';
                    } else {
                        $summary['processing_failed']++;

                        $processingStatus = 'failed';

                        $status = 'failed';
                    }
                }

                $results[] = [
                    'client_id' => $message['client_id'] ?? null,
                    'message_hash' => $messageHash,
                    'status' => $status,
                    'processing_status' => $processingStatus,
                ];
            }

            // Update the device's synchronization timestamps.
            $device->forceFill([
                'last_sync_at' => now(),
                'last_seen_at' => now(),
            ])->save();

            $hasFailures = $summary['processing_failed'] > 0;

            $syncLog->update([
                'messages_saved' => $summary['saved'],
                'duplicates' => $summary['duplicates'],
                'messages_processed' => $summary['processed'],
                'messages_unrecognized' => $summary['unrecognized'],
                'messages_failed' => $summary['processing_failed'],
                'sync_status' => $hasFailures
                    ? 'partial'
                    : 'completed',
                'completed_at' => now(),
                'metadata' => [
                    'results' => $results,
                ],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Message synchronization completed.',
                'sync_reference' => $syncLog->sync_reference,
                'summary' => $summary,
                'results' => $results,
            ], 200);

        } catch (Throwable $exception) {

            report($exception);

            $syncLog->update([
                'messages_saved' => $summary['saved'],
                'duplicates' => $summary['duplicates'],
                'messages_processed' => $summary['processed'],
                'messages_unrecognized' => $summary['unrecognized'],
                'messages_failed' => $summary['processing_failed'],
                'sync_status' => 'failed',
                'error_message' => Str::limit(
                    $exception->getMessage(),
                    5000
                ),
                'completed_at' => now(),
                'metadata' => [
                    'results' => $results,
                ],
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Synchronization encountered an error.',
                'sync_reference' => $syncLog->sync_reference,
                'summary' => $summary,
                'results' => $results,
            ], 500);
        }
    }
}
