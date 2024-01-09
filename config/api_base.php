<?php

use App\Models\UserProfile;

return [
    'api_key' => env('API_KEY', ''),
    'token_key' => env('TOKEN_KEY', ''),
    'domain' => env('DOMAIN', 'http://127.0.0.1:8000'),
    'roles' => [
        'root_system_role' => env('ROLE_ROOT_SYSTEM', 'root_system'),
        'system_role' => env('ROLE_APP_SYSTEM', 'app_system'),
    ],
    'customer_profile_model' => UserProfile::class
];
