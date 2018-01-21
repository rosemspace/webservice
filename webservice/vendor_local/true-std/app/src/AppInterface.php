<?php

namespace TrueStd\Application;

use Psr\Container\ContainerInterface;

interface AppInterface extends ContainerInterface
{
    public function addServiceProviders(string $serviceProvidersConfigFilePath);

    public function addMiddleware() : AppInterface;

    public function boot(string $appConfigFilePath);
}
