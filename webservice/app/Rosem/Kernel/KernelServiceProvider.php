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
use TrueStd\View\ViewInterface;

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

    public function createView(ContainerInterface $container)
    {
//        $container->get(AppConfigInterface::class)->get('webservice.paths.');

        return new class (new \League\Plates\Engine([
            'base_dir' => $container->get(AppConfigInterface::class)->get('app.paths.root')
        ])) implements ViewInterface {
            /**
             * @var \League\Plates\Engine
             */
            private $engine;

            public function __construct(\League\Plates\Engine $engine)
            {
                $this->engine = $engine;
                // TODO: make as glob
                $this->engine->addFolder('assets', __DIR__ . '/../../../public');
                $this->engine->addFolder('Rosem\Kernel', __DIR__ . '/View');
                $this->engine->addFolder('Rosem\Admin', __DIR__ . '/../Admin/View');
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
        };
    }
}
