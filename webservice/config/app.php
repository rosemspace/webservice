<?php

return [
    'app'        => [
        'name'      => getenv('APP_NAME'),
        'env'       => getenv('APP_ENV', 'development'),
        'lang'      => 'en-US',
        'paths'     => [
            'root' => getenv('APP_DIR'),
        ],
        'meta'      => [
            'charset'     => 'utf-8',
            'titlePrefix' => '${app.name} | ',
            'title'       => 'Welcome',
            'titleSuffix' => '',
        ],
        'polyfills' => [
            'Promise',
            'Object.assign',
            'Object.values',
            'Array.prototype.find',
            'Array.prototype.findIndex',
            'Array.prototype.includes',
            'String.prototype.includes',
            'String.prototype.startsWith',
            'String.prototype.endsWith',
        ],
    ],
    'webservice' => [
        'paths' => [
            'root'   => getenv('WEBSERVICE_ROOT_PATH'),
            'public' => getenv('WEBSERVICE_PUBLIC_PATH'),
        ],
    ],
    'client'     => [
        'paths' => [
            'root'   => getenv('CLIENT_ROOT_PATH'),
            'public' => getenv('CLIENT_PUBLIC_PATH'),
        ],
    ],
    'db'     => [
        'driver'    => getenv('DATABASE_DRIVER', 'mysql'),
        'host'      => getenv('DATABASE_HOST', 'localhost'),
        'dbname'    => getenv('DATABASE_NAME', 'rosem'),
        'username'  => getenv('DATABASE_USERNAME', 'root'),
        'password'  => getenv('DATABASE_PASSWORD', ''),
        'port'      => getenv('DATABASE_PORT'),
        'charset'   => getenv('DATABASE_CHARSET', 'utf-8'),
        'engine'    => getenv('DATABASE_ENGINE'),
        'collation' => getenv('DATABASE_COLLATION', 'utf8_unicode_ci'),
        'prefix'    => getenv('DATABASE_PREFIX', ''),
    ],
    'backOffice'      => [
        'uri'              => 'bo',
        'meta'             => [
            'titlePrefix' => '${app.name} back-office | ',
            'title'       => 'Dashboard',
            'titleSuffix' => '',
        ],
        'username'         => getenv('ADMIN_USERNAME', 'admin'),
        'password'         => getenv('ADMIN_PASSWORD', 'admin'),
        'session_lifetime' => getenv('ADMIN_SESSION_LIFETIME', 3000),
    ],
    'blog'       => [
        'uri' => 'blog',
    ],
];
