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
    ]
];
