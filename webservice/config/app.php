<?php

return [
    'kernel' => [
        'route' => [
            'dataGenerator' => \TrueCode\RouteCollector\RouteDataGenerator::DRIVER_GROUP_COUNT,
            'dispatcher' => \TrueCode\RouteCollector\RouteDispatcher::DRIVER_GROUP_COUNT,
        ],
        'db' => [
            'driver'    => getenv('DB_DRIVER', 'mysql'),
            'host'      => getenv('DB_HOST', 'localhost'),
            'database'  => getenv('DB_NAME'),
            'username'  => getenv('DB_USERNAME', 'root'),
            'password'  => getenv('DB_PASSWORD', ''),
            'charset'   => getenv('DB_CHARSET', 'utf-8'),
            'collation' => getenv('DB_COLLATION', 'utf8_unicode_ci'),
            'prefix'    => getenv('DB_PREFIX', ''),
        ],
    ],
    'admin' => [
        'uri'      => 'admin',
        'username' => getenv('ADMIN_USERNAME', 'admin'),
        'password' => getenv('ADMIN_PASSWORD', 'admin'),
    ],
    'blog' => [
        'route' => 'blog',
    ],
];
