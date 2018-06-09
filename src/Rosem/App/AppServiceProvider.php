<?php

namespace Rosem\App;

use Psr\Container\ContainerInterface;
use Psrnext\{
    App\AppFactoryInterface, Container\ServiceProviderInterface, Environment\EnvironmentInterface, Http\Server\MiddlewareProcessorInterface, Route\RouteCollectorInterface, ViewRenderer\ViewRendererInterface
};
use Psrnext\Config\ConfigInterface;
use Psrnext\Http\Factory\{
    ResponseFactoryInterface, ServerRequestFactoryInterface
};
use Rosem\App\Http\Server\HomeRequestHandler;
use Rosem\Environment\Environment;

class AppServiceProvider implements ServiceProviderInterface
{
    use ConfigFileTrait;

    /**
     * Returns a list of all container entries registered by this service provider.
     * @return callable[]
     * @throws \InvalidArgumentException
     */
    public function getFactories(): array
    {
        return [
            AppFactoryInterface::class           => function () {
                return new AppFactory;
            },
            MiddlewareProcessorInterface::class  => function (ContainerInterface $container) {
//                $container->get(AppFactoryInterface::class)->create();
                return new App($container);
            },
            EnvironmentInterface::class          => function () {
                $env = new Environment(getcwd() . '/..');
                $env->load();

                return $env;
            },
            ConfigInterface::class               => function (ContainerInterface $container) {
                $container->get(EnvironmentInterface::class)->load();

                return new \Rosem\Config\Config(self::getConfiguration(getcwd() . '/../config/app.php'));
            },
            ServerRequestFactoryInterface::class => [static::class, 'createServerRequestFactory'],
            ResponseFactoryInterface::class      => [static::class, 'createResponseFactory'],
            ViewRendererInterface::class         => [static::class, 'createViewRenderer'],
            HomeRequestHandler::class            => function (ContainerInterface $container) {
                return new HomeRequestHandler(
                    $container->get(ResponseFactoryInterface::class),
                    $container->get(ViewRendererInterface::class),
                    $container->get(ConfigInterface::class)
                );
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
            RouteCollectorInterface::class => function (
                ContainerInterface $container,
                RouteCollectorInterface $routeCollector
            ) {
                $routeCollector->get('/{path.*}', HomeRequestHandler::class);
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
                    'metaTitlePrefix' => $config->get('app.meta.title_prefix'),
                    'metaTitleSuffix' => $config->get('app.meta.title_suffix'),
                    'csrfToken'       => '4sWPhTlJAmt1IcyNq1FCyivsAVhHqjiDCKRXOgOQock=',
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

    public function createViewRenderer(ContainerInterface $container)
    {
        return new class (\League\Plates\Engine::create(
            $container->get(ConfigInterface::class)->get('app.paths.public'),
            'phtml'
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
