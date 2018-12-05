<?php

return [
    'directory' => [
        'root'    => \dirname(getcwd()),
        'public'  => '${app.directories.root}/DIRECTORY_PUBLIC',
        'media'   => '${app.directories.root}/DIRECTORY_MEDIA',
        'temp'    => '${app.directories.root}/DIRECTORY_TEMP',
        'cache'   => '${app.directories.root}/DIRECTORY_CACHE',
        'log'     => '${app.directories.root}/DIRECTORY_LOG',
        'session' => '${app.directories.root}/DIRECTORY_SESSION',
        'upload'  => '${app.directories.root}/DIRECTORY_UPLOAD',
        'export'  => '${app.directories.root}/DIRECTORY_EXPORT',
    ],
    'app'        => [
        'name'      => 'APP_NAME',
        'env'       => 'APP_ENV',
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
        'uri'    => '/graphql',
        'schema' => 'default',
    ],
    'auth' => [
        'symmetricKey' => 'AUTH_SYMMETRIC_KEY',
        'http' => [
            'type' => 'digest',
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
