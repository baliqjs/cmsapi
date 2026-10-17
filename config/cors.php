<?php

return [
    'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'register', 'logout', 'me', 'storage/*'],

    'allowed_methods' => ['*'],

    // Mengizinkan semua origin (localhost, IP lokal 192.168.x.x untuk HP, Flutter, dll)
    'allowed_origins' => ['*'],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,
];
