<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class MpesaService
{
    /**
     * Get a Daraja OAuth access token.
     */
    public function getAccessToken(): string
    {
        $response = Http::withBasicAuth(
            config('services.mpesa.consumer_key'),
            config('services.mpesa.consumer_secret')
        )->get(
            'https://sandbox.safaricom.co.ke/oauth/v1/generate',
            [
                'grant_type' => 'client_credentials',
            ]
        );

        if ($response->failed()) {
            throw new RuntimeException(
                'Unable to authenticate with M-Pesa: '
                . $response->body()
            );
        }

        $accessToken = $response->json('access_token');

        if (!$accessToken) {
            throw new RuntimeException(
                'M-Pesa authentication succeeded but no access token was returned.'
            );
        }

        return $accessToken;
    }

    /**
     * Register C2B validation and confirmation URLs with Daraja.
     */
    public function registerC2BUrls(
        string $confirmationUrl,
        string $validationUrl
    ): array {
        $accessToken = $this->getAccessToken();

        $response = Http::withToken($accessToken)
            ->post(
                'https://sandbox.safaricom.co.ke/mpesa/c2b/v1/registerurl',
                [
                    'ShortCode' => config('services.mpesa.shortcode'),
                    'ResponseType' => 'Completed',
                    'ConfirmationURL' => $confirmationUrl,
                    'ValidationURL' => $validationUrl,
                ]
            );

        if ($response->failed()) {
            throw new RuntimeException(
                'Unable to register M-Pesa C2B URLs: '
                . $response->body()
            );
        }

        return $response->json();
    }
}