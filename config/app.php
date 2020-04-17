<?php

return [
    'providers' => include __DIR__ . '/providers.php',
    'directory' => [
        'public'  => '${root}/DIRECTORY_PUBLIC',
        'media'   => '${root}/DIRECTORY_MEDIA',
        'temp'    => '${root}/DIRECTORY_TEMP',
        'cache'   => '${root}/DIRECTORY_CACHE',
        'log'     => '${root}/DIRECTORY_LOG',
        'session' => '${root}/DIRECTORY_SESSION',
        'upload'  => '${root}/DIRECTORY_UPLOAD',
        'export'  => '${root}/DIRECTORY_EXPORT',
    ],
    'app'        => [
        'name'      => 'APP_NAME',
        'env'       => 'APP_ENV',
        'debug'     => 'APP_DEBUG',
        'lang'      => 'en-US',
        'meta'      => [
            'charset'      => 'utf-8',
            'titlePrefix'  => '${app.name} | ',
            'title'        => 'Welcome',
            'titleSuffix'  => '',
        ],
    ],
    'database'   => [
        'driver'    => 'DATABASE_DRIVER',
        'host'      => 'DATABASE_HOST',
        'name'      => 'DATABASE_NAME',
        'username'  => 'DATABASE_USERNAME',
        'password'  => 'DATABASE_PASSWORD',
        'port'      => 'DATABASE_PORT',
        'charset'   => 'DATABASE_CHARSET',
        'engine'    => 'DATABASE_ENGINE',
        'collation' => 'DATABASE_COLLATION',
        'prefix'    => 'DATABASE_PREFIX',
    ],
    'graphql'    => [
        'debug'  => '${app.debug}',
        'uri'    => '/graphql',
        'schema' => 'default',
    ],
    'auth' => [
        'symmetricKey' => 'AUTH_SYMMETRIC_KEY',
        'http' => [
            'type' => 'digest',
            'realm' => 'AUTH_HTTP_REALM',
            'user' => [
                'list' => [
                    'admin' => 'admin',
                ],
            ],
        ],
    ],
    'admin'      => [
        'uri'              => [
            'loggedIn' => 'admin',
        ],
        'meta'             => [
            'titlePrefix' => '${app.name} Admin | ',
            'title'        => 'Dashboard',
            'titleSuffix'  => '',
        ],
        'user' => [
            'identity' => 'ADMIN_USER_IDENTITY',
            'password' => 'ADMIN_USER_PASSWORD',
        ],
        'session' => [
            'lifetime' => 'ADMIN_SESSION_LIFETIME'
        ],
    ],
];
