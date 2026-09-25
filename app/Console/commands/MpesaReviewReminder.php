<?php

namespace App\Console\Commands;

use App\Models\MpesaTransaction;
use App\Services\OnfonSmsService;
use Illuminate\Console\Command;
use Throwable;

class MpesaReviewReminder extends Command
{
    protected $signature = 'mpesa:review-reminder';

    protected $description = 'Send scheduled reminders for M-Pesa transactions pending review';

    public function handle(OnfonSmsService $onfonSmsService): int
    {
        $phoneNumbers = config(
            'services.mpesa.review_reminder_phones',
            []
        );

        if (empty($phoneNumbers)) {
            $this->error(
                'No M-Pesa review reminder phone numbers are configured.'
            );

            return self::FAILURE;
        }

        $now = now();

        /*
         * FIRST REMINDER
         *
         * Transaction has been pending for at least 24 hours
         * and has never received the first reminder.
         */
        $firstReminderTransactions = MpesaTransaction::query()
            ->where('status', 'pending_review')
            ->where('received_at', '<=', $now->copy()->subHours(24))
            ->whereNull('review_reminder_1_sent_at')
            ->orderBy('received_at')
            ->get();

        foreach ($firstReminderTransactions as $transaction) {
            $this->sendReminder(
                $onfonSmsService,
                $phoneNumbers,
                'Teule Kenya M-Pesa Alert: Transaction '
                    . $transaction->transaction_id
                    . ' for KES '
                    . number_format((float) $transaction->amount, 2)
                    . ' has been pending review for more than 24 hours. Please log in and review it.'
            );

            $transaction->update([
                'review_reminder_1_sent_at' => $now,
            ]);
        }

        /*
         * SECOND REMINDER
         *
         * First reminder was sent at least 12 hours ago,
         * but the transaction is still pending review.
         *
         * We only send this during the 10:00 AM hour.
         */
        if ((int) $now->format('H') === 10) {

            $secondReminderTransactions = MpesaTransaction::query()
                ->where('status', 'pending_review')
                ->whereNotNull('review_reminder_1_sent_at')
                ->where(
                    'review_reminder_1_sent_at',
                    '<=',
                    $now->copy()->subHours(12)
                )
                ->whereNull('review_reminder_2_sent_at')
                ->orderBy('received_at')
                ->get();

            foreach ($secondReminderTransactions as $transaction) {
                $this->sendReminder(
                    $onfonSmsService,
                    $phoneNumbers,
                    'Teule Kenya M-Pesa Follow-up: Transaction '
                        . $transaction->transaction_id
                        . ' for KES '
                        . number_format((float) $transaction->amount, 2)
                        . ' is still pending review. Please log in and review it.'
                );

                $transaction->update([
                    'review_reminder_2_sent_at' => $now,
                ]);
            }
        }

        if (
            $firstReminderTransactions->isEmpty()
            && !isset($secondReminderTransactions)
        ) {
            $this->info('No M-Pesa review reminders are due.');
        }

        return self::SUCCESS;
    }

    private function sendReminder(
        OnfonSmsService $onfonSmsService,
        array $phoneNumbers,
        string $message
    ): void {
        foreach ($phoneNumbers as $phoneNumber) {
            try {
                $result = $onfonSmsService->send(
                    $phoneNumber,
                    $message
                );

                $this->info(
                    'Reminder sent to ' . $phoneNumber
                );

                if (!empty($result['message_id'])) {
                    $this->line(
                        'Provider reference: '
                        . $result['message_id']
                    );
                }
            } catch (Throwable $e) {
                $this->error(
                    'Failed to send reminder to '
                    . $phoneNumber
                    . ': '
                    . $e->getMessage()
                );
            }
        }
    }
}