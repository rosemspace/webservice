<?php

namespace Rosem\App\Provider;

use Psr\Container\ContainerInterface;
use Psrnext\{
    App\AppFactoryInterface, App\AppInterface, Container\ServiceProviderInterface, Environment\EnvironmentInterface, Router\RouteCollectorInterface, Router\RouteDispatcherInterface, ViewRenderer\ViewRendererInterface
};
use Psrnext\Config\ConfigInterface;
use Psrnext\Http\Factory\{
    ResponseFactoryInterface, ServerRequestFactoryInterface
};
use Rosem\App\App;
use Rosem\App\AppFactory;
use Rosem\App\ConfigFileTrait;
use Rosem\App\Http\Controller\AppController;
use Rosem\Environment\Environment;
use Rosem\Http\Authentication\Middleware\BasicAuthenticationMiddleware;
use Rosem\Http\Authentication\Middleware\DigestAuthenticationMiddleware;

class AppServiceProvider implements ServiceProviderInterface
{
    use ConfigFileTrait;

    /**
     * Returns a list of all container entries registered by this service provider.
     *
     * @return callable[]
     */
    public function getFactories(): array
    {
        return [
            AppFactoryInterface::class => function () {
                return new AppFactory;
            },
            AppInterface::class => function (ContainerInterface $container) {
//                $container->get(AppFactoryInterface::class)->create();
                return new App($container);
            },
            EnvironmentInterface::class => function () {
                $env = new Environment(getcwd() . '/..');
                $env->load();

                return $env;
            },
            ConfigInterface::class => function (ContainerInterface $container) {
                $container->get(EnvironmentInterface::class)->load();

                return new \Rosem\Config\Config(self::getConfiguration(getcwd() . '/../config/app.php'));
            },
            \Rosem\App\Http\Middleware\RouteMiddleware::class => function (ContainerInterface $container) {
                return new \Rosem\App\Http\Middleware\RouteMiddleware(
                    $container->get(RouteDispatcherInterface::class),
                    $container->get(ResponseFactoryInterface::class)
                );
            },
            \Rosem\App\Http\Middleware\RequestHandlerMiddleware::class => function (ContainerInterface $container) {
                return new \Rosem\App\Http\Middleware\RequestHandlerMiddleware($container);
            },
            ServerRequestFactoryInterface::class => [static::class, 'createServerRequestFactory'],
            ResponseFactoryInterface::class      => [static::class, 'createResponseFactory'],
            RouteCollectorInterface::class       => [static::class, 'createRouteCollector'],
            RouteDispatcherInterface::class      => [static::class, 'createRouteDispatcher'],
            ViewRendererInterface::class         => [static::class, 'createViewRenderer'],
            AppController::class                 => function (ContainerInterface $container) {
                return new AppController(
                    $container->get(ResponseFactoryInterface::class),
                    $container->get(ViewRendererInterface::class),
                    $container->get(ConfigInterface::class)
                );
            },

            BasicAuthenticationMiddleware::class => function () {
                return new BasicAuthenticationMiddleware([
                    'roshe' => '1111',
                ]);
            },
            DigestAuthenticationMiddleware::class => function () {
                return new DigestAuthenticationMiddleware([
                    'roshe' => '1111',
                ]);
            },
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
            AppInterface::class => function (ContainerInterface $container, AppInterface $app) {
                $app->use(\Rosem\Http\Authentication\Middleware\DigestAuthenticationMiddleware::class);
                $app->use(\Rosem\App\Http\Middleware\RouteMiddleware::class);
                $app->use(\Rosem\App\Http\Middleware\RequestHandlerMiddleware::class);
            },
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
                $config = $container->get(ConfigInterface::class);
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
            $container->get(ConfigInterface::class)->get('app.paths.public'),
            'html'
        )) implements ViewRendererInterface
        {
            /**
             * @var \League\Plates\Engine
             * @uses \League\Plates\Engine::addFolder(string $path, string $alias)
             * @method addData(array $data)
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
