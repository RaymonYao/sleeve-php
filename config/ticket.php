<?php
return [
    'jwt' => [
        'iss' => 'dd-ticket',
        'expire_second' => 3600 * 24,   //过期时间
        'signing_key' => env('JWT_SIGNING_KEY', 'dt-ticket-9f4ffabac44dca8812320b2cc7fd2628')
    ],
    'base_url' => env('APP_URL')
];