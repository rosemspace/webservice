<?php

namespace Rosem\Kernel;

use Psr\Container\ContainerInterface;
use Psrnext\App\AppConfigInterface;
use Psrnext\Container\ServiceProviderInterface;
use Psrnext\Http\Factory\{
    MiddlewareFactoryInterface, ResponseFactoryInterface, ServerRequestFactoryInterface
};
use Psrnext\RouteCollector\{
    RouteCollectorInterface, RouteDispatcherInterface
};
use Psrnext\View\ViewInterface;

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
            RouteDispatcherInterface::class      => [static::class, 'createRouteDispatcher'],
            ViewInterface::class                 => [static::class, 'createView'],

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
            RouteCollectorInterface::class => function (
                ContainerInterface $container,
                RouteCollectorInterface $routeCollector
            ) {
                $routeCollector->get('/{relativePath:.*}', [
                    \Rosem\Kernel\Controller\MainController::class,
                    'index',
                ]);
            },
            ViewInterface::class => function (ContainerInterface $container, ViewInterface $view) {
                $appConfig = $container->get(AppConfigInterface::class);
                $view->addData([
                    'lang' => $appConfig->get('app.lang'),
                    'charset' => $appConfig->get('app.meta.charset'),
                ]);
                $view->addDirectoryAlias('Rosem.Kernel', __DIR__ . '/View');
            },
        ];
    }

    public function createServerRequestFactory()
    {
        return new \Rosem\Http\Factory\ServerRequestFactory;
    }

    public function createResponseFactory()
    {
        return new \Rosem\Http\Factory\ResponseFactory;
    }

    public function createMiddlewareFactory(ContainerInterface $container)
    {
        return new \Rosem\Http\Factory\MiddlewareFactory($container);
    }

    /**
     * @param ContainerInterface $container
     *
     * @return \Rosem\RouteCollector\RouteCollector
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function createRouteCollector(ContainerInterface $container)
    {
        return new \Rosem\RouteCollector\RouteCollector(
            new \FastRoute\RouteParser\Std,
            new \Rosem\RouteCollector\RouteDataGenerator(
                $container->get(AppConfigInterface::class)
                    ->get(
                        'kernel.route.data_generator',
                        \Rosem\RouteCollector\RouteDataGenerator::DRIVER_GROUP_COUNT
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
    public function createRouteDispatcher(ContainerInterface $container)
    {
        return new \Rosem\RouteCollector\RouteDispatcher(
            $container->get(RouteCollectorInterface::class)->getData(),
            $container->get(AppConfigInterface::class)
                ->get(
                    'kernel.route.dispatcher',
                    \Rosem\RouteCollector\RouteDispatcher::DRIVER_GROUP_COUNT
                )
        );
    }

    public function createView(ContainerInterface $container)
    {
        return new class (\League\Plates\Engine::create(
            $container->get(AppConfigInterface::class)->get('app.paths.root')
        )) implements ViewInterface {
            /**
             * @var \League\Plates\Engine
             */
            private $engine;

            public function __construct(\League\Plates\Engine $engine)
            {
                $this->engine = $engine;
//                $this->engine->register(new \League\Plates\Extension\Asset(BASEDIR . '/public'));
            }

            /**
             * Create a new template and render it.
             *
             * @param  string $templateName
             * @param  array  $data
             * @param array   $attributes
             *
             * @return string
             */
            public function render(string $templateName, array $data = [], array $attributes = []) : string
            {
                return $this->engine->render($templateName, $data, $attributes);
            }

            public function addDirectoryAlias(string $alias, string $path) : void
            {
                $this->engine->addFolder($alias, $path);
            }
            
            public function addData(array $data) : void
            {
                $this->engine->addData($data);
            }
        };
    }
}
