<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class OnfonSmsService
{
    /**
     * Send one SMS through Onfon Media.
     *
     * @return array{
     *     success: bool,
     *     message_id: ?string,
     *     response: mixed
     * }
     */
    public function send(
        string $phone,
        string $message
    ): array {
        $phone = $this->normalizeKenyanNumber($phone);

        $response = Http::timeout(15)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'AccessKey' => config('services.onfon.access_key'),
            ])
            ->post(
                config('services.onfon.api_url'),
                [
                    'SenderId' => config('services.onfon.sender_id'),

                    'IsUnicode' => true,

                    'IsFlash' => false,

                    'ApiKey' => config('services.onfon.api_key'),

                    'ClientId' => config('services.onfon.client_id'),

                    'MessageParameters' => [
                        [
                            'Number' => $phone,
                            'Text' => $message,
                        ],
                    ],
                ]
            );

        if (!$response->successful()) {
            throw new RuntimeException(
                'Onfon HTTP error: '
                . $response->status()
                . ' - '
                . $response->body()
            );
        }

        $data = $response->json();

        if (($data['ErrorCode'] ?? null) != 0) {
            throw new RuntimeException(
                'Onfon error: '
                . ($data['ErrorDescription'] ?? 'Unknown error')
            );
        }

        $messageId = $data['Data'][0]['MessageId'] ?? null;

        if (!$messageId) {
            throw new RuntimeException(
                'Onfon accepted the request but did not return a MessageId.'
            );
        }

        return [
            'success' => true,
            'message_id' => $messageId,
            'response' => $data,
        ];
    }

    /**
     * Convert common Kenyan formats to 2547XXXXXXXX.
     */
    protected function normalizeKenyanNumber(string $phone): string
    {
        $phone = preg_replace('/\s+/', '', trim($phone));

        if (str_starts_with($phone, '+254')) {
            return substr($phone, 1);
        }

        if (str_starts_with($phone, '254')) {
            return $phone;
        }

        if (str_starts_with($phone, '0')) {
            return '254' . substr($phone, 1);
        }

        throw new RuntimeException(
            "Invalid Kenyan phone number: {$phone}"
        );
    }
}