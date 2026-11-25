<?php

namespace App\Services\MpesaCollector;

use App\Models\MpesaCollectorMessage;
use App\Models\MpesaCollectorTransaction;
use Illuminate\Support\Facades\DB;
use Throwable;

class MpesaMessageProcessingService
{
    protected MpesaMessageParser $parser;

    public function __construct(MpesaMessageParser $parser)
    {
        $this->parser = $parser;
    }

    /**
     * Process one stored M-Pesa SMS.
     *
     * Returns: processed, unrecognized or failed.
     */
    public function process(MpesaCollectorMessage $message): string
    {
        try {
            // Avoid processing an SMS that already has a transaction.
            if ($message->transaction()->exists()) {
                $message->update([
                    'processing_status' => 'processed',
                    'processing_error' => null,
                    'processed_at' => now(),
                ]);

                return 'processed';
            }

            $parsed = $this->parser->parse($message->raw_message);

            // Preserve messages that are not recognized.
            if ($parsed === null) {
                $message->update([
                    'processing_status' => 'unrecognized',
                    'processing_error' => null,
                    'processed_at' => now(),
                ]);

                return 'unrecognized';
            }

            DB::transaction(function () use ($message, $parsed) {

                MpesaCollectorTransaction::updateOrCreate(
                    [
                        'mpesa_collector_message_id' => $message->id,
                    ],
                    $parsed
                );

                $message->update([
                    'processing_status' => 'processed',
                    'processing_error' => null,
                    'processed_at' => now(),
                ]);
            });

            return 'processed';

        } catch (Throwable $exception) {

            $message->update([
                'processing_status' => 'failed',
                'processing_error' => mb_substr(
                    $exception->getMessage(),
                    0,
                    2000
                ),
                'processed_at' => null,
            ]);

            report($exception);

            return 'failed';
        }
    }

    /**
     * Retry a previously failed or unrecognized message.
     */
    public function retry(MpesaCollectorMessage $message): string
    {
        $message->update([
            'processing_status' => 'pending',
            'processing_error' => null,
            'processed_at' => null,
        ]);

        return $this->process($message);
    }

    /**
     * Process a collection of pending messages.
     */
    public function processPending(int $limit = 100): array
    {
        $messages = MpesaCollectorMessage::where(
            'processing_status',
            'pending'
        )
            ->orderBy('id')
            ->limit($limit)
            ->get();

        $summary = [
            'processed' => 0,
            'unrecognized' => 0,
            'failed' => 0,
        ];

        foreach ($messages as $message) {
            $status = $this->process($message);

            $summary[$status]++;
        }

        return $summary;
    }
}