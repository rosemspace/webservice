<?php

namespace TrueStd\Application;

use Psr\Container\ContainerInterface;
use TrueStd\Container\ServiceProviderInterface;
use TrueStd\Http\Server\MiddlewareInterface;

interface AppInterface extends ContainerInterface
{
    public function addServiceProvider(ServiceProviderInterface $serviceProvider);

//    public function addServiceProviders(array $serviceProvidersConfig);

    public function addServiceProvidersFromFile(string $serviceProvidersConfigFilePath);

    public function addMiddleware(MiddlewareInterface $middleware);

//    public function addMiddlewareLayers(array $serviceProvidersConfig);

    public function addMiddlewareLayersFromFile(string $serviceProvidersConfigFilePath);

    public function boot(string $appConfigFilePath);
}
