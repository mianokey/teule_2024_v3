<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'onfon' => [
        'api_url' => env(
            'ONFON_API_URL',
            'https://api.onfonmedia.co.ke/v1/sms/SendBulkSMS'
        ),

        'api_key' => env('ONFON_API_KEY'),
        'client_id' => env('ONFON_CLIENT_ID'),
        'access_key' => env('ONFON_ACCESS_KEY'),
        'sender_id' => env('ONFON_SENDER_ID'),
    ],

    'mpesa' => [
        'environment' => env('MPESA_ENVIRONMENT', 'sandbox'),
        'consumer_key' => env('MPESA_CONSUMER_KEY'),
        'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
        'shortcode' => env('MPESA_SHORTCODE'),
        'passkey' => env('MPESA_PASSKEY'),

        'review_reminder_phones' => array_filter(
            array_map(
                'trim',
                explode(',', env('MPESA_REVIEW_REMINDER_PHONES', ''))
            )
        ),
    ],

];
