<?php

namespace App\Services;

use App\Mail\DonationThankYouMail;
use App\Models\DonationCommunication;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class DonationCommunicationSender
{
    public function __construct(
        protected OnfonSmsService $onfonSmsService
    ) {
    }

    /**
     * Send a pending communication.
     */
    public function send(DonationCommunication $communication): bool
    {
        if ($communication->status !== 'pending') {
            return false;
        }

        if (
            $communication->scheduled_at &&
            $communication->scheduled_at->isFuture()
        ) {
            return false;
        }

        if ($communication->channel === 'sms') {
            return $this->sendSms($communication);
        }

        if ($communication->channel === 'email') {
            return $this->sendEmail($communication);
        }

        return false;
    }

    /**
     * Send an SMS communication.
     */
    protected function sendSms(
        DonationCommunication $communication
    ): bool {
        try {
            $communication->update([
                'status' => 'sending',
                'error_message' => null,
            ]);

            $result = $this->onfonSmsService->send(
                $communication->recipient,
                $communication->message
            );

            $communication->update([
                'status' => 'sent',
                'sent_at' => now(),
                'provider_reference' => $result['message_id'],
                'error_message' => null,
            ]);

            return true;
        } catch (Throwable $e) {
            Log::error(
                'Donation SMS failed.',
                [
                    'communication_id' => $communication->id,
                    'recipient' => $communication->recipient,
                    'error' => $e->getMessage(),
                ]
            );

            $communication->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Send an email communication.
     */
    protected function sendEmail(
        DonationCommunication $communication
    ): bool {
        try {
            $communication->update([
                'status' => 'sending',
                'error_message' => null,
            ]);

            $communication->loadMissing('donation');

            Mail::to($communication->recipient)
                ->send(
                    new DonationThankYouMail(
                        $communication->donation
                    )
                );

            $communication->update([
                'status' => 'sent',
                'sent_at' => now(),
                'error_message' => null,
            ]);

            return true;
        } catch (Throwable $e) {
            Log::error(
                'Donation email failed.',
                [
                    'communication_id' => $communication->id,
                    'recipient' => $communication->recipient,
                    'error' => $e->getMessage(),
                ]
            );

            $communication->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return false;
        }
    }
}