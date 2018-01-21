<?php

namespace TrueStd\Application;

use Psr\Container\ContainerInterface;
use TrueStd\Container\ServiceProviderInterface;
use TrueStd\Http\Server\MiddlewareInterface;

interface AppInterface extends ContainerInterface
{
    public function addServiceProvider(ServiceProviderInterface $serviceProvider);

    public function addServiceProviders(string $serviceProvidersConfigFilePath);

    public function addMiddleware(MiddlewareInterface $middleware);

    public function addMiddlewareLayers(string $serviceProvidersConfigFilePath);

    public function boot(string $appConfigFilePath);
}
