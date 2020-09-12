<?php

namespace Rosem\Component\App;

use function function_exists;

if (!function_exists('throwError')) {
    function throwError(string $message, int $code = 1)
    {
        fwrite(STDERR, PHP_EOL . "\e[31m$message\e[0m" . PHP_EOL);

        die($code);
    }
}
