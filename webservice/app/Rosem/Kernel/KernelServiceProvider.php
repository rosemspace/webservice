<?php

namespace Rosem\Kernel;

use Http\Factory\Diactoros\ServerRequestFactory;
use Interop\Container\ServiceProviderInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\{
    ServerRequestInterface, ResponseInterface
};
use TrueStd\Http\Factory\ServerRequestFactoryInterface;
use Zend\Diactoros\Response;

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
            ServerRequestInterface::class        => [static::class, 'createRequest'],
            ResponseInterface::class             => [static::class, 'createResponse'],
            ServerRequestFactoryInterface::class => [static::class, 'createServerRequestFactory'],

            \Zend\Diactoros\Server::class                => function (ContainerInterface $container) {
                return new \Zend\Diactoros\Server(
                    function () {
                    },
                    $container->get(ServerRequestInterface::class),
                    $container->get(ResponseInterface::class)
                );
            },
            \Analogue\ORM\Analogue::class                => function (ContainerInterface $container) {
                return new \Analogue\ORM\Analogue($container->get('db'));
            },
            \TrueStandards\GraphQL\GraphInterface::class => function (ContainerInterface $container) {
                return new \True\GraphQL\Graph($container);
            },
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
     * - the entry to be extended. If the entry to be extended does not exist and the parameter is nullable, `null` will be passed.
     *
     * @return callable[]
     */
    public function getExtensions()
    {
        return [];
    }


    public function createServerRequestFactory()
    {
        return new ServerRequestFactory;
    }

    public function createResponse()
    {
        return new Response();
    }

    /**
     * @param ContainerInterface $container
     *
     * @return ServerRequestInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function createRequest(ContainerInterface $container)
    {
        return $container->get(ServerRequestFactoryInterface::class)->createServerRequestFromArray($_SERVER)
            ->withQueryParams($_GET)
            ->withParsedBody($_POST)
            ->withCookieParams($_COOKIE)
            ->withUploadedFiles($_FILES);
    }
}
