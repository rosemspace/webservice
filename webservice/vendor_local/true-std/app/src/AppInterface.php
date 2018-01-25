<?php

namespace TrueStd\App;

use Psr\Container\ContainerInterface;

interface AppInterface extends ContainerInterface
{
    public function loadServiceProviders(string $serviceProvidersConfigFilePath);

    public function loadMiddlewares(string $serviceProvidersConfigFilePath);

    public function boot(string $appConfigFilePath);
}
