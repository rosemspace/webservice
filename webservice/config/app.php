<?php

return [
    'app' => [
        'env' => getenv('APP_ENV', 'development'),
    ],
    'kernel' => [
        'route' => [
            'data_generator' => getenv(
                'ROUTE_DATA_GENERATOR',
                \TrueCode\RouteCollector\RouteDataGenerator::DRIVER_GROUP_COUNT
            ),
            'dispatcher'     => getenv(
                'ROUTE_DISPATCHER',
                \TrueCode\RouteCollector\RouteDispatcher::DRIVER_GROUP_COUNT
            ),
        ],
        'database'    => [
            'paths'         => [
                'migrations' => __DIR__ . '/app/*/*/etc/db/migrations',
                'seeds'      => __DIR__ . '/app/*/*/etc/db/seeds',
            ],
            'environments'  => [
                'default_migration_table' => 'schema_migration_log',
                'default_database'        => 'development',
                'development'             => [
                    'driver'    => getenv('DATABASE_DRIVER', 'mysql'), // -> adapter
                    'host'      => getenv('DATABASE_HOST', 'localhost'), // +
                    'database'  => getenv('DATABASE_NAME', 'rosem'), // -> name
                    'username'  => getenv('DATABASE_USERNAME', 'root'), // -> user
                    'password'  => getenv('DATABASE_PASSWORD', ''), // -> pass
                    'port'      => getenv('DATABASE_PORT'),
                    'charset'   => getenv('DATABASE_CHARSET', 'utf-8'), // +
                    'engine'    => getenv('DATABASE_ENGINE'), // ???
                    'collation' => getenv('DATABASE_COLLATION', 'utf8_unicode_ci'), // ?
                    'prefix'    => getenv('DATABASE_PREFIX', ''), // ?
                ],
            ],
            'version_order' => 'creation',
        ],
    ],
    'admin'  => [
        'uri'              => 'admin',
        'username'         => getenv('ADMIN_USERNAME', 'admin'),
        'password'         => getenv('ADMIN_PASSWORD', 'admin'),
        'session_lifetime' => getenv('ADMIN_SESSION_LIFETIME'),
    ],
    'blog'   => [
        'uri' => 'blog',
    ],
];
