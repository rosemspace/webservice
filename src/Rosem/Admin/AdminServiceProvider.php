<?php

namespace Rosem\Admin;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Rosem\Admin\Http\Server\AdminRequestHandler;
use Rosem\Admin\Http\Server\LoginRequestHandler;
use Rosem\Authentication\AuthenticationServiceProvider;
use Rosem\Authentication\Http\Server\AuthenticationMiddleware;
use Rosem\Psr\Authentication\UserFactoryInterface;
use Rosem\Psr\Config\ConfigInterface;
use Rosem\Psr\Container\ServiceProviderInterface;
use Rosem\Psr\Route\RouteCollectorInterface;
use Rosem\Psr\Template\TemplateRendererInterface;

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
            'admin.meta.title_prefix' => function (ContainerInterface $container) {
                return ($container->has('app.name') ? $container->get('app.name') . ' ' : '') . 'Admin | ';
            },
            'admin.meta.title' => function () {
                return 'Welcome';
            },
            'admin.meta.title_suffix' => function () {
                return '';
            },
            'admin.uri.loggedIn' => function () {
                return '/admin';
            },
            'admin.uri.login' => function (ContainerInterface $container) {
                return '/' . trim($container->get('admin.uri.loggedIn'), '/') . '/login';
            },
            AuthenticationMiddleware::class . '.admin' => function (ContainerInterface $container) {
                return new AuthenticationMiddleware(
                    $container->get(ResponseFactoryInterface::class),
                    $container->get(UserFactoryInterface::class),
                    $container->get(AuthenticationServiceProvider::CONFIG_USER_RESOLVER_PASSWORD),
                    'username',
                    'password',
                    '/' . trim($container->get('admin.uri.login'), '/'),
                    '/' . trim($container->get('admin.uri.loggedIn'), '/')
                );
            },
            AdminRequestHandler::class => function (ContainerInterface $container) {
                return new AdminRequestHandler(
                    $container->get(ResponseFactoryInterface::class),
                    $container->get(TemplateRendererInterface::class),
                    $container->get(ConfigInterface::class)
                );
            },
            LoginRequestHandler::class => function (ContainerInterface $container) {
                return new LoginRequestHandler(
                    $container->get(ResponseFactoryInterface::class),
                    $container->get(TemplateRendererInterface::class),
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
                $adminUri = '/' . trim($container->get('admin.uri.loggedIn'), '/');
                $loginUri = '/' . trim($container->get('admin.uri.login'), '/');
                $routeCollector->get($loginUri, LoginRequestHandler::class)
                    ->addMiddleware(AuthenticationMiddleware::class . '.admin');
                $routeCollector->post($loginUri, LoginRequestHandler::class)
                    ->addMiddleware(AuthenticationMiddleware::class . '.admin');
                $routeCollector->get($adminUri . '{adminRelativePath:.*}', AdminRequestHandler::class)
                    ->addMiddleware(AuthenticationMiddleware::class . '.admin');
            },
            TemplateRendererInterface::class => function (
                ContainerInterface $container,
                TemplateRendererInterface $renderer
            ) {
                $renderer->addPath(__DIR__ . '/resources/templates', 'admin');
                $adminData = [
                    'metaTitlePrefix' => $container->get('admin.meta.title_prefix'),
                    'metaTitle'       => $container->get('admin.meta.title'),
                    'metaTitleSuffix' => $container->get('admin.meta.title_suffix'),
                ];
                $renderer->addTemplateData('admin::index', $adminData);
                $renderer->addTemplateData('admin::login', $adminData);
            }
        ];
    }
}
