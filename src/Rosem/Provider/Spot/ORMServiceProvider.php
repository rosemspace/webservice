<?php

namespace Rosem\Provider\Spot;

use Psr\Container\ContainerInterface;
use Rosem\Contract\Container\ServiceProviderInterface;
use Rosem\Contract\Environment\EnvironmentInterface;

class ORMServiceProvider implements ServiceProviderInterface
{
    /**
     * Returns a list of all container entries registered by this service provider.
     * - the key is the entry name
     * - the value is a callable that will return the entry, aka the **factory**
     * Factories have the following signature:
     *        function(\Psr\Container\ContainerInterface $container)
     * @return callable[]
     */
    public function getFactories(): array
    {
        return [
            \Spot\Locator::class => function (ContainerInterface $container) {
                $config = new \Spot\Config();
                $environment = $container->get(EnvironmentInterface::class);
                $driver = $container->get(EnvironmentInterface::class)->get('DATABASE_DRIVER');
                /** @noinspection PhpParamsInspection */
                $config->addConnection($driver, [
                    'dbname' => $environment->get('DATABASE_NAME'),
                    'user' => $environment->get('DATABASE_USERNAME'),
                    'password' => $environment->get('DATABASE_PASSWORD'),
                    'host' => $environment->get('DATABASE_HOST'),
                    'driver' => $driver,
                ]);

                return new \Spot\Locator($config);
            }
        ];
    }

    /**
     * Returns a list of all container entries extended by this service provider.
     * - the key is the entry name
     * - the value is a callable that will return the modified entry
     * Callables have the following signature:
     *        function(Psr\Container\ContainerInterface $container, $previous)
     *     or function(Psr\Container\ContainerInterface $container, $previous = null)
     * About factories parameters:
     * - the container (instance of `Psr\Container\ContainerInterface`)
     * - the entry to be extended. If the entry to be extended does not exist and the parameter is nullable, `null`
     * will be passed.
     * @return callable[]
     */
    public function getExtensions(): array
    {
        return [];
    }
}
