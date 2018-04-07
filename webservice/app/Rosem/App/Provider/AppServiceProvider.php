<?php

namespace Rosem\App\Provider;

use Psr\Container\ContainerInterface;
use Psrnext\{
    App\AppConfigInterface,
    Container\ServiceProviderInterface,
    Router\RouteCollectorInterface,
    Router\RouteDispatcherInterface,
    ViewRenderer\ViewRendererInterface
};
use Psrnext\Http\Factory\{
    ResponseFactoryInterface, ServerRequestFactoryInterface
};
use Rosem\App\Http\Controller\AppController;

class AppServiceProvider implements ServiceProviderInterface
{
    /**
     * Returns a list of all container entries registered by this service provider.
     *
     * @return callable[]
     */
    public function getFactories(): array
    {
        return [
            ServerRequestFactoryInterface::class => [static::class, 'createServerRequestFactory'],
            ResponseFactoryInterface::class      => [static::class, 'createResponseFactory'],
            RouteCollectorInterface::class       => [static::class, 'createRouteCollector'],
            RouteDispatcherInterface::class      => [static::class, 'createRouteDispatcher'],
            ViewRendererInterface::class         => [static::class, 'createViewRenderer'],
            AppController::class                 => function (ContainerInterface $container) {
                return new AppController(
                    $container->get(ResponseFactoryInterface::class),
                    $container->get(ViewRendererInterface::class),
                    $container->get(AppConfigInterface::class)
                );
            },

//            \TrueStandards\GraphQL\GraphInterface::class => function (ContainerInterface $container) {
//                return new \True\GraphQL\Graph($container);
//            },
        ];
    }

    /**
     * Returns a list of all container entries extended by this service provider.
     *
     * @return callable[]
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function getExtensions(): array
    {
        return [
            RouteCollectorInterface::class => function (
                ContainerInterface $container,
                RouteCollectorInterface $routeCollector
            ) {
                $routeCollector->get(
                    '/{uri-path:.*}',
                    [AppController::class, 'index']
                );
            },
            ViewRendererInterface::class   => function (
                ContainerInterface $container,
                ViewRendererInterface $view
            ) {
                $config = $container->get(AppConfigInterface::class);
                $view->addData([
                    'lang'            => $config->get('app.lang'),
                    'charset'         => $config->get('app.meta.charset'),
                    'appName'         => $config->get('app.name'),
                    'appEnv'          => $config->get('app.env'),
                    'metaTitlePrefix' => $config->get('app.meta.titlePrefix'),
                    'metaTitleSuffix' => $config->get('app.meta.titleSuffix'),
                    'polyfills'       => $config->get('app.polyfills'),
                ]);
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

    /**
     * @return \Rosem\Router\RouteCollector
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function createRouteCollector()
    {
        return new \Rosem\Router\RouteCollector(
            new \FastRoute\RouteParser\Std,
            new \FastRoute\DataGenerator\GroupCountBased
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
        return new \Rosem\Router\RouteDispatcher(
            new \FastRoute\Dispatcher\GroupCountBased($container->get(RouteCollectorInterface::class)->getData())
        );
    }

    public function createViewRenderer(ContainerInterface $container)
    {
        return new class (\League\Plates\Engine::create(
            $container->get(AppConfigInterface::class)->get('client.paths.public'),
            'html'
        )) implements ViewRendererInterface
        {
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
            public function render(string $templateName, array $data = [], array $attributes = []): string
            {
                return $this->engine->render($templateName, $data, $attributes);
            }

            public function addPathAlias(string $path, string $alias): void
            {
                $this->engine->addFolder($alias, $path);
            }

            public function addData(array $data): void
            {
                $this->engine->addData($data);
            }
        };
    }
}
