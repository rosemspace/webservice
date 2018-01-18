<?php

namespace Rosem\Kernel;

use Interop\Container\ServiceProviderInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class KernelServiceProvider implements ServiceProviderInterface
{
    /**
     * Returns a list of all container entries registered by this service provider.
     * - the key is the entry name
     * - the value is a callable that will return the entry, aka the **factory**
     * Factories have the following signature:
     *        function(\Psr\Container\ContainerInterface $container)
     *
     * @return callable[]
     */
    public function getFactories()
    {
        return [
            ServerRequestInterface::class => [self::class, 'createRequest'],
            \Zend\Diactoros\Server::class => function (ContainerInterface $container) {
                return new \Zend\Diactoros\Server(
                    function () {},
                    $container->get(ServerRequestInterface::class),
                    $container->get(ResponseInterface::class)
                );
            },
            ResponseInterface::class => function () {
                return new \Zend\Diactoros\Response();
            },
            \Analogue\ORM\Analogue::class => function (ContainerInterface $container) {
                return new \Analogue\ORM\Analogue($container->get('db'));
            },
            \TrueStandards\GraphQL\GraphInterface::class => function (ContainerInterface $container) {
                return new \True\GraphQL\Graph($container);
            }
        ];
    }

    public function createRequest()
    {
        return \Zend\Diactoros\ServerRequestFactory::fromGlobals(
            $_SERVER,
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES
        );
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
     * - the entry to be extended. If the entry to be extended does not exist and the parameter is nullable, `null` will be passed.
     *
     * @return callable[]
     */
    public function getExtensions()
    {
        return [];
    }
}
