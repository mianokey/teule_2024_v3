<?php

namespace App\Services;

use App\Models\Donation;
use App\Models\DonationCommunication;
use Illuminate\Support\Facades\Auth;

class DonationThankYouService
{
    /**
     * Create thank-you communication records for a confirmed donation.
     *
     * By default both SMS and email are created when the donor
     * has the corresponding contact information.
     *
     * $channels can be:
     * ['sms']
     * ['email']
     * ['sms', 'email']
     */
    public function createForDonation(
        Donation $donation,
        int $delaySeconds = 30,
        array $channels = ['sms', 'email']
    ): void {
        if ($donation->classification !== 'donation') {
            return;
        }

        $donation->loadMissing('donor');

        if (!$donation->donor) {
            return;
        }

        $donor = $donation->donor;

        $scheduledAt = now()->addSeconds($delaySeconds);

        if (
            in_array('sms', $channels, true)
            && $donor->phone
        ) {
            $this->createCommunication(
                donation: $donation,
                donor: $donor,
                channel: 'sms',
                recipient: $donor->phone,
                subject: null,
                message: $this->smsMessage($donation),
                scheduledAt: $scheduledAt
            );
        }

        if (
            in_array('email', $channels, true)
            && $donor->email
        ) {
            $this->createCommunication(
                donation: $donation,
                donor: $donor,
                channel: 'email',
                recipient: $donor->email,
                subject: 'Thank You for Your Support to Teule Kenya',
                message: $this->emailMessage($donation),
                scheduledAt: $scheduledAt
            );
        }
    }

    protected function createCommunication(
        Donation $donation,
        $donor,
        string $channel,
        string $recipient,
        ?string $subject,
        string $message,
        $scheduledAt = null
    ): DonationCommunication {
        return DonationCommunication::create([
            'donation_id' => $donation->id,
            'donor_id' => $donor->id,
            'sent_by' => Auth::id(),
            'channel' => $channel,
            'type' => 'thank_you',
            'recipient' => $recipient,
            'subject' => $subject,
            'message' => $message,
            'status' => 'pending',
            'scheduled_at' => $scheduledAt,
        ]);
    }

    protected function smsMessage(Donation $donation): string
    {
        $donorName = $donation->donor?->name ?? 'Friend';

        if ($donation->type === 'cash') {
            $amount = number_format(
                (float) $donation->amount,
                2
            );

            return "Dear {$donorName}, thank you for your generous gift of "
                . "{$donation->currency} {$amount} to Teule Kenya. "
                . "Your support helps us care for and empower vulnerable "
                . "children and families. God bless you.";
        }

        return "Dear {$donorName}, thank you for your generous support to "
            . "Teule Kenya. Your gift helps us care for and empower "
            . "vulnerable children and families. God bless you.";
    }

    protected function emailMessage(Donation $donation): string
    {
        $donorName = $donation->donor?->name ?? 'Friend';

        if ($donation->type === 'cash') {
            $amount = number_format(
                (float) $donation->amount,
                2
            );

            return "Dear {$donorName},\n\n"
                . "Thank you very much for your generous gift of "
                . "{$donation->currency} {$amount} to Teule Kenya.\n\n"
                . "Your support enables us to care for vulnerable children "
                . "and families through education, child care, family "
                . "empowerment, discipleship, and community outreach.\n\n"
                . "We are grateful for your partnership and support. "
                . "May God bless you abundantly.\n\n"
                . "With gratitude,\n"
                . "Teule Kenya";
        }

        return "Dear {$donorName},\n\n"
            . "Thank you very much for your generous support to Teule Kenya.\n\n"
            . "Your gift enables us to care for vulnerable children and "
            . "families through our programs and community outreach.\n\n"
            . "We are grateful for your partnership and support. "
            . "May God bless you abundantly.\n\n"
            . "With gratitude,\n"
            . "Teule Kenya";
    }
}