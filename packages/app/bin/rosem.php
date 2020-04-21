#!/usr/bin/env php
<?php

const APP_NAME = 'Rosem application console';
const PHP_VERSION_SUPPORTED = '7.4.0';

// Check is PHP version is supported
if (version_compare(PHP_VERSION_SUPPORTED, PHP_VERSION, '>')) {
    fwrite(
        STDERR,
        sprintf(
            'This version of ' . APP_NAME . ' is supported PHP ' . PHP_VERSION_SUPPORTED .
            ' and higher.' . PHP_EOL . 'You are using PHP %s (%s).' . PHP_EOL,
            PHP_VERSION,
            PHP_BINARY
        )
    );

    die(1);
}

// Check if this file run as CLI application
if (PHP_SAPI !== 'cli') {
    fwrite(
        STDERR,
        APP_NAME . ' must be run as a CLI application.' . PHP_EOL
    );

    die(1);
}

if (!ini_get('date.timezone')) {
    ini_set('date.timezone', 'UTC');
}

// Require an autoload file
require_once __DIR__ . '/findAutoloadFile.php';

$autoloadFile = findAutoloadFile(__DIR__, 6);

if ($autoloadFile === null) {
    fwrite(
        STDERR,
        'You need to set up the project dependencies using Composer:' . PHP_EOL . PHP_EOL .
        '    composer install' . PHP_EOL . PHP_EOL .
        'You can learn all about Composer on https://getcomposer.org/.' . PHP_EOL
    );

    die(1);
}

require_once $autoloadFile;
require_once dirname($autoloadFile, 2) . DIRECTORY_SEPARATOR . 'bootstrap.php';

unset($autoloadFile);
