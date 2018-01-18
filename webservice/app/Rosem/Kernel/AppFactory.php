<?php

namespace Rosem\Kernel;

use Closure;
use Exception;
use Interop\Container\ServiceProviderInterface;
use Psr\Container\ContainerInterface;
use True\DI\ReflectionContainer;

class AppFactory
{
    /**
     * @var ServiceProviderInterface[]
     */
    protected static $serviceProviders = [];

    /**
     * @param string $serviceProvidersFileName
     *
     * @return ContainerInterface
     * @throws Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public static function create(string $serviceProvidersFileName) : ContainerInterface
    {
        $directories = new AppDirectories;
        $serviceProvidersFilePath = "{$directories->config()}/$serviceProvidersFileName";

        if (is_readable($serviceProvidersFilePath) && file_exists($serviceProvidersFilePath)) {
            $serviceProviders = require_once $serviceProvidersFilePath;

            if (is_array($serviceProviders)) {
                $app = new App;
                $app->delegate(new ReflectionContainer);

                // 1. In the first pass, the container calls the getFactories method of all service providers.
                foreach ($serviceProviders as $serviceProviderClass) {
                    if ($serviceProviderClass !== static::class) {
                        /** @var ServiceProviderInterface $serviceProvider */
                        static::$serviceProviders[] = $serviceProvider = new $serviceProviderClass;

                        foreach ($serviceProvider->getFactories() as $key => $factory) {
                            $app->share(
                                $key,
                                is_array($factory) ? function () use ($factory) {
                                    $serviceProvider = reset($factory);
                                    $method = next($factory);

                                    return (new $serviceProvider)->$method();
                                } : $factory
                            )->commit();
                        }
                    }
                }

                // 2. In the second pass, the container calls the getExtensions method of all service providers.
                foreach (static::$serviceProviders as $serviceProvider) {
                    foreach ($serviceProvider->getExtensions() as $key => $factory) {
                        $app->find($key)->withFunctionCall(
                            is_array($factory) ? Closure::fromCallable($factory) : $factory
                        )->commit();
                    }
                }

                return $app;
            } else {
                throw new Exception('Service providers config file is invalid');
            }
        } else {
            throw new Exception('Service providers config file does not exist or not readable');
        }
    }
}
