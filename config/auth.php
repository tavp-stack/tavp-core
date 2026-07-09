<?php

// Authentication configuration. OTP-first per decision 10.11.

return [
    // Primary login channel: 'otp' (email/SMS/WhatsApp). Password is
    // an optional fallback only.
    'primary_method' => 'otp',

    'otp' => [
        'ttl_minutes' => env('OTP_TTL_MINUTES', 5),
        'max_attempts' => env('OTP_MAX_ATTEMPTS', 5),
        'channels' => ['email', 'sms', 'whatsapp'],
    ],

    'jwt' => [
        'secret' => env('JWT_SECRET', ''),
        'access_ttl_minutes' => 15,
        'refresh_ttl_days' => 30,
    ],
];
