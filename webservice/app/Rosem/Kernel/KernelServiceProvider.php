<?php

namespace Rosem\Kernel;

use Psr\Container\ContainerInterface;
use TrueStd\App\AppConfigInterface;
use TrueStd\Container\ServiceProviderInterface;
use TrueStd\Http\Factory\{
    MiddlewareFactoryInterface, ResponseFactoryInterface, ServerRequestFactoryInterface
};
use TrueStd\RouteCollector\{
    RouteCollectorInterface, RouteDispatcherInterface
};

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
            ServerRequestFactoryInterface::class => [static::class, 'createServerRequestFactory'],
            ResponseFactoryInterface::class      => [static::class, 'createResponseFactory'],
            MiddlewareFactoryInterface::class    => [static::class, 'createMiddlewareFactory'],
            RouteCollectorInterface::class       => [static::class, 'createRouteCollector'],
            RouteDispatcherInterface::class      => [static::class, 'createSimpleRouteDispatcher'],

            \Analogue\ORM\Analogue::class                => function (ContainerInterface $container) {
                return new \Analogue\ORM\Analogue(
                    $container->get(AppConfigInterface::class)->get('kernel.database.environments.development')
                );
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
        return [
            RouteCollectorInterface::class => function (RouteCollectorInterface $r) {
                $r->get('/{relativePath:.*}', [
                    \Rosem\Kernel\Controller\MainController::class,
                    'index',
                ]);
            },
        ];
    }

    public function createServerRequestFactory()
    {
        return new \TrueCode\Http\Factory\ServerRequestFactory;
    }

    public function createResponseFactory()
    {
        return new \TrueCode\Http\Factory\ResponseFactory;
    }

    public function createMiddlewareFactory(ContainerInterface $container)
    {
        return new \TrueCode\Http\Factory\MiddlewareFactory($container);
    }

    /**
     * @param ContainerInterface $container
     *
     * @return \TrueCode\RouteCollector\RouteCollector
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function createRouteCollector(ContainerInterface $container)
    {
        return new \TrueCode\RouteCollector\RouteCollector(
            new \FastRoute\RouteParser\Std,
            new \TrueCode\RouteCollector\RouteDataGenerator(
                $container->get(AppConfigInterface::class)
                    ->get(
                        'kernel.route.data_generator',
                        \TrueCode\RouteCollector\RouteDataGenerator::DRIVER_GROUP_COUNT
                    )
            )
        );
    }

    /**
     * @param ContainerInterface $container
     *
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function createSimpleRouteDispatcher(ContainerInterface $container)
    {
        return new \TrueCode\RouteCollector\RouteDispatcher(
            $container->get(RouteCollectorInterface::class)->getData(),
            $container->get(AppConfigInterface::class)
                ->get(
                    'kernel.route.dispatcher',
                    \TrueCode\RouteCollector\RouteDispatcher::DRIVER_GROUP_COUNT
                )
        );
    }
}
