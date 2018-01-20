<?php

namespace Rosem\Kernel;

use Closure;
use Exception;
use Interop\Container\ServiceProviderInterface;
use True\DI\ReflectionContainer;
use TrueStd\Application\{AppFactoryInterface, AppInterface};

class AppFactory implements AppFactoryInterface
{
    use ConfigTrait;

    /**
     * @param string $serviceProvidersFilePath
     *
     * @return AppInterface
     * @throws Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public static function create(string $serviceProvidersFilePath) : AppInterface
    {
        $serviceProviders = self::getConfiguration($serviceProvidersFilePath);
        $app = new App;
        $app->delegate(new ReflectionContainer);
        /** @var ServiceProviderInterface[] $serviceProviderInstances */
        $serviceProviderInstances = [];

        // 1. In the first pass, the container calls the getFactories method of all service providers.
        foreach ($serviceProviders as $serviceProviderClass) {
            if (
                is_string($serviceProviderClass) &&
                class_exists($serviceProviderClass) &&
                $serviceProviderClass !== static::class
            ) {
                $serviceProvider = new $serviceProviderClass;

                if ($serviceProvider instanceof ServiceProviderInterface) {
                    $serviceProviderInstances[] = $serviceProvider;

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
                } else {
                    throw new Exception(
                        "The service provider $serviceProviderClass should implement " .
                        ServiceProviderInterface::class
                    );
                }
            } else {
                throw new Exception(
                    'An item of service providers configuration should be a string' .
                    'that represents service provider class which implements ' .
                    ServiceProviderInterface::class . ", got $serviceProviderClass");
            }
        }

        // 2. In the second pass, the container calls the getExtensions method of all service providers.
        foreach ($serviceProviderInstances as $serviceProvider) {
            foreach ($serviceProvider->getExtensions() as $key => $factory) {
                $app->find($key)->withFunctionCall(
                    is_array($factory) ? Closure::fromCallable($factory) : $factory
                )->commit();
            }
        }

        return $app;
    }
}
