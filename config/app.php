<?php

return [
    'app'        => [
        'name'      => getenv('APP_NAME'),
        'env'       => getenv('APP_ENV', 'development'),
        'lang'      => 'en-US',
        'paths'     => [
            'root'   => getenv('APP_ROOT_PATH'),
            'public' => getenv('APP_PUBLIC_PATH'),
        ],
        'meta'      => [
            'charset'      => 'utf-8',
            'title_prefix' => '${app.name} | ',
            'title'        => 'Welcome',
            'title_suffix' => '',
        ],
    ],
    'database'   => [
        'driver'    => getenv('DATABASE_DRIVER', 'mysql'),
        'host'      => getenv('DATABASE_HOST', 'localhost'),
        'name'      => getenv('DATABASE_NAME', 'rosem'),
        'username'  => getenv('DATABASE_USERNAME', 'root'),
        'password'  => getenv('DATABASE_PASSWORD', ''),
        'port'      => getenv('DATABASE_PORT'),
        'charset'   => getenv('DATABASE_CHARSET', 'utf-8'),
        'engine'    => getenv('DATABASE_ENGINE'),
        'collation' => getenv('DATABASE_COLLATION', 'utf8_unicode_ci'),
        'prefix'    => getenv('DATABASE_PREFIX', ''),
    ],
    'graphql'    => [
        'uri'    => '/graphql',
        'schema' => 'default',
    ],
    'authentication' => [
        'http' => [
            'type' => 'digest',
        ],
    ],
    'admin'      => [
        'uri'              => [
            'loggedIn' => 'admin',
        ],
        'meta'             => [
            'title_prefix' => '${app.name} Admin | ',
            'title'        => 'Dashboard',
            'title_suffix' => '',
        ],
        'username'         => getenv('ADMIN_USERNAME', 'admin'),
        'password'         => getenv('ADMIN_PASSWORD', 'admin'),
        'session_lifetime' => getenv('ADMIN_SESSION_LIFETIME', 3000),
    ],
];
