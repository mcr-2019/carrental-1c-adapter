<?php

return [
    'defaultConnection' => 'default',

    'connections' => [
        'default' => [
            'wsdl_base_url' => env('CARRENTAL_DEFAULT_CONNECTION_WSDL_URL'),
            'login'         => env('CARRENTAL_DEFAULT_CONNECTION_LOGIN'),
            'password'      => env('CARRENTAL_DEFAULT_CONNECTION_PASSWORD'),
        ],
    ],

    'cache' => [
        'driver' => env('CARRENTAL_CACHE_DRIVER', 'array'),
        'minutes' => 30,
    ],
];
