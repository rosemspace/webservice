<?php
#!/usr/bin/env php
//todo move shebang to top

if (version_compare('7.4.0', PHP_VERSION, '>')) {
    fwrite(
        STDERR,
        sprintf(
            'This version of Rosem application console is supported PHP 7.4.0 and higher.' . PHP_EOL .
            'You are using PHP %s (%s).' . PHP_EOL,
            PHP_VERSION,
            PHP_BINARY
        )
    );

    die(1);
}

if (PHP_SAPI !== 'cli') {
    fwrite(
        STDERR,
        'Rosem application console must be run as a CLI application'
    );

    die(1);
}

if (!ini_get('date.timezone')) {
    ini_set('date.timezone', 'UTC');
}

$vendorDirTail = DIRECTORY_SEPARATOR . 'vendor';
$autoloadFileTail = DIRECTORY_SEPARATOR . 'autoload.php';
$autoloadFile = null;
$autoloadFiles = [
    // ./
    __DIR__ . "$vendorDirTail$autoloadFileTail",
    // ./bin
    dirname(__DIR__) . "$vendorDirTail$autoloadFileTail",
    // ./vendor/rosem/app/bin
    dirname(__DIR__, 3) . $autoloadFileTail,
    // ./vendor/rosem/app/Resource/bin
    dirname(__DIR__, 4) . $autoloadFileTail,
    // src/Rosem/Component/App/bin
    // todo: remove it
    dirname(__DIR__, 6) . "$vendorDirTail$autoloadFileTail",
];

foreach ($autoloadFiles as $file) {
    if (file_exists($file)) {
        $autoloadFile = $file;

        break;
    }
}

unset($vendorDirTail, $autoloadFileTail, $autoloadFiles);

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

unset($autoloadFile);
