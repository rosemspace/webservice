<?php

declare(strict_types=1);

namespace Rosem\Component\App;

use function function_exists;

if (! function_exists('throwError')) {
    function throwError(string $message, int $code = 1): void
    {
        fwrite(STDERR, PHP_EOL . "\e[31m${message}\e[0m" . PHP_EOL);

        die($code);
    }
}
