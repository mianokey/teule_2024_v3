<?php

namespace App\Services;

use App\Models\Donation;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\File;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use RuntimeException;

class DonationReceiptPdf
{
    public function generate(
        Donation $donation,
        string $receiptType = 'donor'
    ): array {
        $donation->loadMissing([
            'donor',
            'items',
            'receivedBy',
        ]);

        if (!in_array($receiptType, ['internal', 'donor'], true)) {
            $receiptType = 'donor';
        }

        $encodedDonationNumber = rtrim(
            strtr(
                base64_encode($donation->donation_number),
                '+/',
                '-_'
            ),
            '='
        );

        $verificationUrl = route('donations.verify', [
            'encodedDonationNumber' => $encodedDonationNumber,
        ]);

        $generatedAt = now();

        $qrDirectory = public_path('receipt-qr-temp');

        if (!is_dir($qrDirectory)) {
            if (!File::makeDirectory($qrDirectory, 0755, true, true)
                && !is_dir($qrDirectory)) {
                throw new RuntimeException(
                    'Unable to create the temporary receipt QR directory.'
                );
            }
        }

        $qrCodePath = $qrDirectory
            . DIRECTORY_SEPARATOR
            . 'receipt-'
            . bin2hex(random_bytes(16))
            . '.svg';

        try {
            $qrSvg = QrCode::format('svg')
                ->size(120)
                ->margin(2)
                ->errorCorrection('H')
                ->generate($verificationUrl);

            if (file_put_contents($qrCodePath, $qrSvg) === false) {
                throw new RuntimeException(
                    'Unable to generate the receipt QR code image.'
                );
            }

            $pdf = Pdf::loadView(
                'admin.donations.receipt',
                [
                    'donation' => $donation,
                    'generatedAt' => $generatedAt,
                    'receiptType' => $receiptType,
                    'verificationUrl' => $verificationUrl,
                    'qrCodePath' => $qrCodePath,
                ]
            );

            $pdf->setPaper('A4', 'portrait');

            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => false,
                'defaultFont' => 'DejaVu Sans',
                'chroot' => public_path(),
            ]);

            $typeLabel = $receiptType === 'donor'
                ? 'Donor'
                : 'Internal';

            $filename = 'Donation-Receipt-'
                . preg_replace(
                    '/[^A-Za-z0-9_-]/',
                    '-',
                    (string) $donation->donation_number
                )
                . '-' . $typeLabel . '.pdf';

            return [
                'content' => $pdf->output(),
                'filename' => $filename,
            ];
        } finally {
            if (is_file($qrCodePath)) {
                @unlink($qrCodePath);
            }
        }
    }
}
