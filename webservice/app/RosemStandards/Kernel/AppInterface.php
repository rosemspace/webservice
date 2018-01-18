<?php

namespace RosemStandards\Kernel;

use Psr\Container\ContainerInterface;

interface AppInterface extends ContainerInterface
{
    public static function boot(string $configFileName);
}
