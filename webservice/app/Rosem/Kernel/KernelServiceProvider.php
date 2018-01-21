<?php

namespace Rosem\Kernel;

use TrueStd\Container\ServiceProviderInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\{
    ServerRequestInterface, ResponseInterface
};
use TrueStd\Http\Factory\ResponseFactoryInterface;
use TrueStd\Http\Factory\ServerRequestFactoryInterface;

class KernelServiceProvider implements ServiceProviderInterface
{
    /**
     * Returns a list of all container entries registered by this service provider.
     *
     * @return callable[]
     */
    public function getFactories() : array
    {
        return [
            ServerRequestInterface::class        => [static::class, 'createRequest'],
            ResponseInterface::class             => [static::class, 'createResponse'],
            ServerRequestFactoryInterface::class => [static::class, 'createServerRequestFactory'],
            ResponseFactoryInterface::class      => [static::class, 'createResponseFactory'],

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
     *
     * @return callable[]
     */
    public function getExtensions() : array
    {
        return [];
    }


    public function createServerRequestFactory()
    {
        return new \Http\Factory\Diactoros\ServerRequestFactory;
    }

    public function createResponseFactory()
    {
        return new \Http\Factory\Diactoros\ResponseFactory;
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

    /**
     * @param ContainerInterface $container
     *
     * @return ResponseInterface
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function createResponse(ContainerInterface $container)
    {
        return $container->get(ResponseFactoryInterface::class)->createResponse(500);
    }
}
