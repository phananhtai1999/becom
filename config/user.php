<?php

return [
    'default_role_uuid' => env('DEFAULT_ROLE_UUID', 3),
    'default_admin_role_uuid' => env('DEFAULT_ADMIN_ROLE_UUID', 1),
    'email_test' => 'sendemail@gmail.com',
    'invite_partner_timeout' => env('INVITE_PARTNER_TIMEOUT', 5)//day
];
