<?php

namespace TrueStd\Application;

use Psr\Container\ContainerInterface;

interface AppInterface extends ContainerInterface
{
    public function boot(string $configFilePath);
}
