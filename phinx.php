<?php

(new \Dotenv\Dotenv(__DIR__))->load();

return [
    'paths' => [
        'migrations' => __DIR__ . '/app/*/*/etc/db/migrations',
        'seeds' => __DIR__ . '/app/*/*/etc/db/seeds',
    ],
    'environments' => [
        'default_migration_table' => 'schema_migrations_log',
        'default_database' => 'development',
        'development' => [
            'adapter' => getenv('DB_DRIVER'),
            'host' => getenv('DB_HOST'),
            'name' => getenv('DB_NAME'),
            'user' => getenv('DB_USERNAME'),
            'pass' => getenv('DB_PASSWORD'),
            'port' => getenv('DB_PORT'),
            'charset' => getenv('DB_CHARSET'),
        ],
    ],
    'version_order' => 'creation',
];
