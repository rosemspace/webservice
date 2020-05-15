<?php
#!/usr/bin/env php

$autoloadPaths = [
    __DIR__ . '/vendor/autoload.php',
    __DIR__ . '/../../autoload.php',
    __DIR__ . '/../../../vendor/autoload.php',
];

foreach ($autoloadPaths as $autoloadPath) {
    if (file_exists($autoloadPath)) {
        require_once $autoloadPath;
        break;
    }
}

use Rosem\Component\Encryption\Console\KeyGenerateCommand;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new KeyGenerateCommand());

$application->run();
