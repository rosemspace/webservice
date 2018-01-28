<?php

return [
    'app'        => [
        'name'  => getenv('APP_NAME'),
        'env'   => getenv('APP_ENV', 'development'),
        'paths' => [
            'root' => getenv('APP_DIR'),
        ],
        'meta'  => [
            'charset'     => 'utf-8',
            'titlePrefix' => '${app.name} | ',
            'title'       => 'Welcome',
            'titleSuffix' => '',
        ],
    ],
    'webservice' => [
        'paths' => [
            'root'   => getenv('WEBSERVICE_DIR'),
            'public' => getenv('WEBSERVICE_PUBLIC_DIR'),
        ],
    ],
    'client'     => [
        'paths' => [
            'root'   => getenv('CLIENT_DIR'),
            'public' => getenv('CLIENT_PUBLIC_DIR'),
        ],
    ],
    'kernel'     => [
        'route'    => [
            'data_generator' => getenv('ROUTE_DATA_GENERATOR', 'group_count'),
            'dispatcher'     => getenv('ROUTE_DISPATCHER', 'group_count'),
        ],
        'database' => [
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
    'admin'      => [
        'uri'              => 'admin',
        'meta'             => [
            'titlePrefix' => 'Admin | ',
            'title'       => '${app.name} dashboard',
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
