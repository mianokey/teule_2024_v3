<?php

namespace App\Mail;

use App\Models\Donation;
use App\Services\DonationReceiptPdf;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DonationThankYouMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Donation $donation
    ) {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thank You for Your Support to Teule Kenya',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.thank-you',
        );
    }

    public function attachments(): array
    {
        // Generate the official donor receipt for every donation.
        $receipt = app(DonationReceiptPdf::class)->generate(
            $this->donation,
            'donor'
        );

        return [
            Attachment::fromData(
                fn () => $receipt['content'],
                $receipt['filename']
            )->withMime('application/pdf'),
        ];
    }
}
