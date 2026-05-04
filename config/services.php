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
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // ─── Stripe ──────────────────────────────────────────────────────────
    'stripe' => [
        'key'            => env('STRIPE_KEY'),
        'secret'         => env('STRIPE_SECRET'),
        'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
    ],

    // ─── Orange Money Guinea ─────────────────────────────────────────────
    'orange_money' => [
        'env'             => env('ORANGE_MONEY_ENV', 'sandbox'),
        'base_url'        => env('ORANGE_MONEY_BASE_URL', 'https://api.orange.com/orange-money-webpay/gn/v1'),
        'client_id'       => env('ORANGE_MONEY_CLIENT_ID'),
        'client_secret'   => env('ORANGE_MONEY_CLIENT_SECRET'),
        'merchant_key'    => env('ORANGE_MONEY_MERCHANT_KEY'),
        'merchant_number' => env('ORANGE_MONEY_MERCHANT_NUMBER'),
        'notify_url'      => env('APP_URL') . '/orange-money/callback',
        'return_url'      => env('APP_URL') . '/user/dashboard',
        'cancel_url'      => env('APP_URL') . '/user/dashboard',
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
