<?php

namespace App\Console\Commands;

use App\Models\DonationCommunication;
use App\Services\DonationCommunicationSender;
use Illuminate\Console\Command;

class SendPendingDonationCommunications extends Command
{
    protected $signature = 'donations:send-pending';

    protected $description =
        'Send scheduled pending donation communications';

    public function handle(
        DonationCommunicationSender $sender
    ): int {
        $communications = DonationCommunication::query()
            ->where('status', 'pending')
            ->where(function ($query) {
                $query
                    ->whereNull('scheduled_at')
                    ->orWhere(
                        'scheduled_at',
                        '<=',
                        now()
                    );
            })
            ->orderBy('id')
            ->limit(100)
            ->get();

        if ($communications->isEmpty()) {
            $this->info('No pending donation communications.');

            return self::SUCCESS;
        }

        foreach ($communications as $communication) {
            $sender->send($communication);
        }

        $this->info(
            'Processed '
            . $communications->count()
            . ' communication(s).'
        );

        return self::SUCCESS;
    }
}