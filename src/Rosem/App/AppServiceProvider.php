<?php

namespace Rosem\App;

use Psr\Container\ContainerInterface;
use Rosem\Psr\{
    App\AppFactoryInterface, Container\ServiceProviderInterface, Environment\EnvironmentInterface, Environment\EnvironmentMode, Route\RouteCollectorInterface, Template\TemplateRendererInterface
};
use Rosem\Psr\Config\ConfigInterface;
use Psr\Http\Message\{
    ResponseFactoryInterface, ServerRequestFactoryInterface
};
use Rosem\Psr\Http\Server\MiddlewareDispatcherInterface;
use Rosem\App\Http\Server\{
    HomeRequestHandler, InternalServerErrorRequestHandler
};
use Rosem\Environment\Environment;
use Rosem\Http\Message\{
    ResponseFactory, ServerRequestFactory
};
use function dirname;

class AppServiceProvider implements ServiceProviderInterface
{
    use ConfigFileTrait;

    protected $baseDirectory;

    public function __construct()
    {
        $this->baseDirectory = dirname((PHP_SAPI !== 'cli-server' ? getcwd() : $_SERVER['DOCUMENT_ROOT']));
    }

    /**
     * Returns a list of all container entries registered by this service provider.
     * @return callable[]
     * @throws \InvalidArgumentException
     */
    public function getFactories(): array
    {
        return [
            'app.name' => function () {
                return 'Rosem';
            },
            'app.lang' => function () {
                return 'en';
            },
            'app.meta.charset' => function () {
                return 'utf-8';
            },
            'app.meta.title_prefix' => function () {
                return 'Rosem | ';
            },
            'app.meta.title' => function () {
                return 'Welcome';
            },
            'app.meta.title_suffix' => function () {
                return '';
            },
            'app.environment' => function () {
//                return EnvironmentMode::PRODUCTION;
                return EnvironmentMode::DEVELOPMENT;
            },
            AppFactoryInterface::class      => function () {
                return new AppFactory;
            },
            MiddlewareDispatcherInterface::class => function (ContainerInterface $container) {
//                $container->get(AppFactoryInterface::class)->create();
                return new App($container, $container->get(InternalServerErrorRequestHandler::class));
            },
            EnvironmentInterface::class     => function () {
                $env = new Environment($this->baseDirectory);
                $env->load();

                return $env;
            },
            ConfigInterface::class          => function (ContainerInterface $container) {
                $container->get(EnvironmentInterface::class)->load();

                return new \Rosem\Config\Config(self::getConfiguration($this->baseDirectory . '/config/app.php'));
            },
            ServerRequestFactoryInterface::class => [static::class, 'createServerRequestFactory'],
            ResponseFactoryInterface::class      => [static::class, 'createResponseFactory'],
            HomeRequestHandler::class            => function (ContainerInterface $container) {
                return new HomeRequestHandler(
                    $container->get(ResponseFactoryInterface::class),
                    $container->get(TemplateRendererInterface::class),
                    $container->get(ConfigInterface::class)
                );
            },
            InternalServerErrorRequestHandler::class => function (ContainerInterface $container) {
                return new InternalServerErrorRequestHandler(
                    $container->get(ResponseFactoryInterface::class),
                    $container->get(TemplateRendererInterface::class),
                    $container->get(ConfigInterface::class)
                );
            }
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
            TemplateRendererInterface::class => function (
                ContainerInterface $container,
                TemplateRendererInterface $renderer
            ) {
                $renderer->addPath(__DIR__ . '/resources/templates', 'app');
                $renderer->addGlobalData([
                    'appName'         => $container->get('app.name'),
                    'lang'            => strtolower($container->get('app.lang')),
                    'charset'         => strtolower($container->get('app.meta.charset')),
                    'appEnvironment'  => $container->get('app.environment'),
                    'metaTitlePrefix' => $container->get('app.meta.title_prefix'),
                    'metaTitleSuffix' => $container->get('app.meta.title_suffix'),
                ]);
            },
            RouteCollectorInterface::class => function (
                ContainerInterface $container,
                RouteCollectorInterface $routeCollector
            ) {
                $routeCollector->get('/{appRelativePath.*}', HomeRequestHandler::class);
            },
        ];
    }

    public function createServerRequestFactory()
    {
        return new ServerRequestFactory;
    }

    public function createResponseFactory()
    {
        return new ResponseFactory;
    }
}
