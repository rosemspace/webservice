<?php

namespace Rosem\Kernel;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psrnext\{
    App\AppConfigInterface,
    Container\ServiceProviderInterface,
    RouteCollector\RouteCollectorInterface,
    RouteCollector\RouteDispatcherInterface,
    ViewRenderer\ViewRendererInterface
};
use Psrnext\Http\Factory\{
    ResponseFactoryInterface, ServerRequestFactoryInterface
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
            RouteCollectorInterface::class       => [static::class, 'createRouteCollector'],
            RouteDispatcherInterface::class      => [static::class, 'createRouteDispatcher'],
            ViewRendererInterface::class         => [static::class, 'createViewRenderer'],

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
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function getExtensions() : array
    {
        return [
            RouteCollectorInterface::class => function (
                ContainerInterface $container,
                RouteCollectorInterface $routeCollector
            ) {
                $routeCollector->get(
                    '/{uri-path:.*}',
                    function (
                        ServerRequestInterface $request,
                        RequestHandlerInterface $nextHandler
                    ) use ($container) : ResponseInterface {
                        $appConfig = $container->get(AppConfigInterface::class);
                        $request = $request->withAttribute('view-data', [
                            '__template__'  => 'Rosem\Kernel::templates/main',
                            'metaTitle' => $appConfig->get(
                                'app.meta.title',
                                $appConfig->get('app.name', 'Rosem')
                            ),
                        ]);

                        return $nextHandler->handle($request);
                    }
                );
            },
            ViewRendererInterface::class   => function (
                ContainerInterface $container,
                ViewRendererInterface $view
            ) {
                $appConfig = $container->get(AppConfigInterface::class);
                $view->addData([
                    'lang'            => $appConfig->get('app.lang'),
                    'charset'         => $appConfig->get('app.meta.charset'),
                    'appName'         => $appConfig->get('app.name'),
                    'appEnv'          => $appConfig->get('app.env'),
                    'metaTitlePrefix' => $appConfig->get('app.meta.titlePrefix'),
                    'metaTitleSuffix' => $appConfig->get('app.meta.titleSuffix'),
                    'polyfills'       => $appConfig->get('app.polyfills'),
                ]);
                $view->addPathAlias(__DIR__ . '/resources/views', __NAMESPACE__);
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
     * @return \Rosem\RouteCollector\RouteCollector
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function createRouteCollector()
    {
        return new \Rosem\RouteCollector\RouteCollector(
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
        return new \Rosem\RouteCollector\RouteDispatcher(
            new \FastRoute\Dispatcher\GroupCountBased($container->get(RouteCollectorInterface::class)->getData())
        );
    }

    public function createViewRenderer(ContainerInterface $container)
    {
        return new class (\League\Plates\Engine::create(
            $container->get(AppConfigInterface::class)->get('app.paths.root')
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
            public function render(string $templateName, array $data = [], array $attributes = []) : string
            {
                return $this->engine->render($templateName, $data, $attributes);
            }

            public function addPathAlias(string $path, string $alias) : void
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
