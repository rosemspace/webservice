<?php

namespace Rosem\Admin;

use Psr\Container\ContainerInterface;
use Psrnext\Http\Factory\ResponseFactoryInterface;
use Psrnext\ViewRenderer\ViewRendererInterface;
use Rosem\Admin\Http\Server\AdminRequestHandler;
use Rosem\Admin\Http\Server\LoginRequestHandler;
use Psrnext\Config\ConfigInterface;
use Psrnext\Container\ServiceProviderInterface;
use Psrnext\Route\RouteCollectorInterface;
use Rosem\Authentication\Http\Server\AuthenticationMiddleware;

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
            'admin.uri' => function () {
                return '/admin';
            },
            'admin.loginUri' => function (ContainerInterface $container) {
                return $container->get('admin.uri') . '/login';
            },
            'AdminAuthentication' => function (ContainerInterface $container) {
                return new AuthenticationMiddleware(
                    $container->get(ResponseFactoryInterface::class),
                    $container->get('auth.userPasswordGetter'),
                    $container->get('admin.loginUri')
                );
            },
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
                $adminUri = '/' . trim($container->get('admin.uri'), '/');
                $loginUri = '/' . trim($container->get('admin.loginUri'), '/');
                $routeCollector->get($loginUri, LoginRequestHandler::class);
                $routeCollector->post($loginUri, LoginRequestHandler::class)
                    ->setMiddleware('AdminAuthentication');
                $routeCollector->get($adminUri . '{adminRelativePath:.*}', AdminRequestHandler::class)
                    ->setMiddleware('AdminAuthentication');
            },
            ViewRendererInterface::class => function (
                ContainerInterface $container,
                ViewRendererInterface $renderer
            ) {
                $renderer->addPathAlias(__DIR__ . '/resources/templates', 'admin');
            }
        ];
    }
}
