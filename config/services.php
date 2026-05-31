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

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'model'   => env('OPENAI_MODEL', 'gpt-4o'),
    ],

    'firebase' => [
        'api_key'              => env('FIREBASE_API_KEY'),
        'auth_domain'          => env('FIREBASE_AUTH_DOMAIN'),
        'project_id'           => env('FIREBASE_PROJECT_ID'),
        'storage_bucket'       => env('FIREBASE_STORAGE_BUCKET'),
        'messaging_sender_id'  => env('FIREBASE_MESSAGING_SENDER_ID'),
        'app_id'               => env('FIREBASE_APP_ID'),
        'vapid_key'            => env('FIREBASE_VAPID_KEY'),
        'credentials'          => env('FIREBASE_CREDENTIALS', storage_path('app/firebase-credentials.json')),
    ],

    /*
    | Mizan platform-level billing gateway (charges OFFICES for their subscriptions).
    | This is separate from the per-office payment_gateways (which charge each
    | office's own clients). Keys belong to the Mizan platform itself.
    */
    'platform_billing' => [
        'gateway' => env('PLATFORM_BILLING_GATEWAY', 'paymob'), // paymob | stripe
        'paymob' => [
            'api_key'        => env('PLATFORM_PAYMOB_API_KEY'),
            'integration_id' => env('PLATFORM_PAYMOB_INTEGRATION_ID'),
            'iframe_id'      => env('PLATFORM_PAYMOB_IFRAME_ID'),
            'hmac_secret'    => env('PLATFORM_PAYMOB_HMAC_SECRET'),
        ],
        'stripe' => [
            'secret_key'     => env('PLATFORM_STRIPE_SECRET_KEY'),
            'webhook_secret' => env('PLATFORM_STRIPE_WEBHOOK_SECRET'),
        ],
    ],

];
