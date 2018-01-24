<?php

namespace Rosem\Kernel\ServiceProvider;

use Psr\Container\ContainerInterface;
use TrueStd\Container\ServiceProviderInterface;
use TrueStd\Http\Factory\MiddlewareFactoryInterface;
use TrueStd\Http\Factory\ResponseFactoryInterface;
use TrueStd\Http\Factory\ServerRequestFactoryInterface;

class HttpFactoryServiceProvider implements ServiceProviderInterface
{
    /**
     * Returns a list of all container entries registered by this service provider.
     *
     * @return callable[]
     */
    public function getFactories() : array
    {
        return [
            ServerRequestFactoryInterface::class => [static::class, 'createServerRequestFactory'],
            ResponseFactoryInterface::class      => [static::class, 'createResponseFactory'],
            MiddlewareFactoryInterface::class    => [static::class, 'createMiddlewareFactory'],

            \Analogue\ORM\Analogue::class                => function (ContainerInterface $container) {
                return new \Analogue\ORM\Analogue($container->get('kernel')['db']);
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
        return new \TrueCode\Http\Factory\ServerRequestFactory;
    }

    public function createResponseFactory()
    {
        return new \TrueCode\Http\Factory\ResponseFactory;
    }

    public function createMiddlewareFactory(ContainerInterface $container) {
        return new \TrueCode\Http\Factory\MiddlewareFactory($container);
    }
}
