#!/usr/bin/env php
<?php
/**
 * Copyright © Rosem, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

if (PHP_SAPI !== 'cli') {
    echo 'bin/rosem.php must be run as a CLI application';

    exit(1);
}

//try {
//} catch (\Exception $e) {
//    echo 'Autoload error: ' . $e->getMessage();
//
//    exit(1);
//}

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../bootstrap.php';
