<?php
/**
 * PayPal Setting & API Credentials
 * Created by Raza Mehdi <srmk@outlook.com>.
 */

return [

    'momo' => [
        'access_key' => env('MOMO_ACCESS_KEY', ''),
        'secret_key' => env('MOMO_SECRET_KEY', ''),
        'partner_code' => env('MOMO_PARTNER_CODE', ''),
        "endpoint_create_order" => env('MOMO_ENDPOINT_CREATE_ORDER', ''),
        "endpoint_query_status" => env('MOMO_ENDPOINT_QUERY_STATUS', ''),
    ],
    'paypal' => [
        'mode' => env('PAYPAL_MODE', 'sandbox'), // Can only be 'sandbox' Or 'live'. If empty or invalid, 'live' will be used.
        'sandbox' => [
            'client_id' => env('PAYPAL_SANDBOX_CLIENT_ID', ''),
            'client_secret' => env('PAYPAL_SANDBOX_CLIENT_SECRET', ''),
            'app_id' => 'APP-80W284485P519543T',
        ],
        'live' => [
            'client_id' => env('PAYPAL_LIVE_CLIENT_ID', ''),
            'client_secret' => env('PAYPAL_LIVE_CLIENT_SECRET', ''),
            'app_id' => env('PAYPAL_LIVE_APP_ID', ''),
        ],

        'payment_action' => env('PAYPAL_PAYMENT_ACTION', 'Sale'), // Can only be 'Sale', 'Authorization' or 'Order'
        'currency' => env('PAYPAL_CURRENCY', 'USD'),
        'notify_url' => env('PAYPAL_NOTIFY_URL', ''), // Change this accordingly for your application.
        'locale' => env('PAYPAL_LOCALE', 'en_US'), // force gateway language  i.e. it_IT, es_ES, en_US ... (for express paypal only)
        'validate_ssl' => env('PAYPAL_VALIDATE_SSL', true), // Validate SSL when creating api client.
    ],
    'stripe' => [
        'client_secret' => env('STRIPE_SECRET_KEY', ''),
        'endpoint_secret' => env('STRIPE_ENDPOINT_SECRET_KEY', ''),
    ],
];