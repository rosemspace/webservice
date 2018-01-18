<?php

return [
    'db' => [
        'driver'    => getenv('DB_DRIVER'),
        'host'      => getenv('DB_HOST'),
        'database'  => getenv('DB_NAME'),
        'username'  => getenv('DB_USERNAME'),
        'password'  => getenv('DB_PASSWORD'),
        'charset'   => getenv('DB_CHARSET'),
        'collation' => getenv('DB_COLLATION'),
        'prefix'    => getenv('DB_PREFIX'),
    ],
];
