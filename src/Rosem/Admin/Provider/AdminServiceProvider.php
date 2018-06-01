<?php

namespace Rosem\Admin\Provider;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psrnext\Http\Factory\ResponseFactoryInterface;
use Psrnext\ViewRenderer\ViewRendererInterface;
use Rosem\Admin\Http\Server\AdminRequestHandler;
use Rosem\Admin\Http\Server\LoginRequestHandler;
use Psrnext\Config\ConfigInterface;
use Psrnext\Container\ServiceProviderInterface;
use Psrnext\Route\RouteCollectorInterface;
use Rosem\App\MiddlewareRequestHandler;
use Rosem\Authentication\Middleware\BearerAuthenticationMiddleware;

class AdminServiceProvider implements ServiceProviderInterface
{
    /**
     * Returns a list of all container entries registered by this service provider.
     *
     * @return callable[]
     */
    public function getFactories(): array
    {
        return [
            AdminRequestHandler::class => function (ContainerInterface $container) {
                return new AdminRequestHandler(
                    $container->get(ResponseFactoryInterface::class),
                    $container->get(ViewRendererInterface::class),
                    $container->get(ConfigInterface::class)
                );
            },
            LoginRequestHandler::class => function (ContainerInterface $container) {
                return new LoginRequestHandler(
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
     */
    public function getExtensions(): array
    {
        return [
            RouteCollectorInterface::class => function (
                ContainerInterface $container,
                RouteCollectorInterface $routeCollector
            ) {
                $adminUri = '/' . $container->get(ConfigInterface::class)->get('admin.uri', 'admin');
                $routeCollector->get($adminUri . '{path:.*}', AdminRequestHandler::class)
                    ->setMiddleware(BearerAuthenticationMiddleware::class);
                $routeCollector->get($adminUri . '/login', LoginRequestHandler::class);
//                $routeCollector->post(
//                    '/admin/login',
//                    function (ServerRequestInterface $request, RequestHandlerInterface $next) use ($container) {
//                        $controller = new LoginAction(
//                            $container->get(ResponseFactoryInterface::class),
//                            $container->get(ViewRendererInterface::class),
//                            $container->get(ConfigInterface::class)
//                        );
//
//                        $requestHandler = new class ($controller) implements RequestHandlerInterface
//                        {
//                            protected $controller;
//                            public function __construct(LoginAction $controller)
//                            {
//                                $this->controller = $controller;
//                            }
//
//                            public function handle(ServerRequestInterface $request): ResponseInterface
//                            {
//                                return $this->controller->index()->withStatus(302)->withHeader(
//                                    'Location',
//                                    '/admin'
//                                );
//                            }
//                        };
//
//                        return $container->get(BearerAuthenticationMiddleware::class)->process($request, $requestHandler);
//                    }
//                );
//                $routeCollector->get(
//                    "/{$container->get(ConfigInterface::class)->get('admin.uri', 'admin')}[/{any:.*}]",
//                    function (ServerRequestInterface $request, RequestHandlerInterface $next) use ($container) {
//                        $controller = new AdminController(
//                            $container->get(ResponseFactoryInterface::class),
//                            $container->get(ViewRendererInterface::class),
//                            $container->get(ConfigInterface::class)
//                        );
//
//                        $requestHandler = new class ($controller) implements RequestHandlerInterface
//                        {
//                            protected $controller;
//                            public function __construct($controller)
//                            {
//                                $this->controller = $controller;
//                            }
//
//                            public function handle(ServerRequestInterface $request): ResponseInterface
//                            {
//                                return $this->controller->index();
//                            }
//                        };
//
//                        return $container->get(BearerAuthenticationMiddleware::class)->process($request, $requestHandler);
//                    }
//                );
            },
        ];
    }
}
