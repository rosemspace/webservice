<?php

return [
    'app'        => [
        'name'      => 'APP_NAME',
        'env'       => 'APP_ENV',
        'lang'      => 'en-US',
        'paths'     => [
            'root'   => 'APP_ROOT_PATH',
            'public' => 'APP_PUBLIC_PATH',
        ],
        'meta'      => [
            'charset'      => 'utf-8',
            'title_prefix' => '${app.name} | ',
            'title'        => 'Welcome',
            'title_suffix' => '',
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
        'user' => [
            'identity' => 'ADMIN_USER_IDENTITY',
            'password' => 'ADMIN_USER_PASSWORD',
        ],
        'session' => [
            'lifetime' => 'ADMIN_SESSION_LIFETIME'
        ],
    ],
];
