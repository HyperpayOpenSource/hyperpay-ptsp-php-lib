<?php

return [
    // Environment: 'production' or 'sandbox'
    'environment' => env('PAYMENT_LINK_ENVIRONMENT', 'production'),
    
    'basic_auth_username' => env('PAYMENT_LINK_BASIC_AUTH_USERNAME', ''),
    'basic_auth_password' => env('PAYMENT_LINK_BASIC_AUTH_PASSWORD', ''),
    
    // Timeout in seconds
    'timeout' => env('PAYMENT_LINK_TIMEOUT', 15),
];

