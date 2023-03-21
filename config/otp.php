<?php


return [
    'expired_time' => env('OTP_EXPIRED_TIME', '5'),//minutes
    'blocked_time' => env('OTP_BLOCKED_TIME', '1'),//hours
    'refresh_time' => env('OTP_REFRESH_TIME', '90'),//seconds
    'refresh_count' => env('OTP_REFRESH_COUNT', '3'),
    'wrong_count' => env('OTP_WRONG_COUNT', '5'),
];
