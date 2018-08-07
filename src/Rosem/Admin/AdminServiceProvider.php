<?php

namespace Rosem\Admin;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Rosem\Admin\Http\Server\{
    AdminRequestHandler,
    LoginRequestHandler};
use Rosem\Authentication\Http\Server\AuthenticationMiddleware;
use Rosem\Psr\Container\ServiceProviderInterface;
use Rosem\Psr\Environment\EnvironmentInterface;
use Rosem\Psr\Http\Server\MiddlewareDispatcherInterface;
use Rosem\Psr\Route\RouteCollectorInterface;
use Rosem\Psr\Template\TemplateRendererInterface;

class AdminServiceProvider implements ServiceProviderInterface
{
    public const CONFIG_USER_IDENTITY = 'admin.user.identity';

    public const CONFIG_USER_PASSWORD = 'admin.user.password';

    public const CONFIG_USER_RESOLVER_PASSWORD = 'admin.user.resolver.password';

    public const CONFIG_URI_LOGGED_IN = 'admin.uri.loggedIn';

    public const CONFIG_URI_LOGIN = 'admin.uri.login';

    /**
     * Returns a list of all container entries registered by this service provider.
     *
     * @return callable[]
     */
    public function getFactories(): array
    {
        return [
            static::CONFIG_USER_IDENTITY => function (ContainerInterface $container) {
                return $container->get(EnvironmentInterface::class)->get('ADMIN_IDENTITY');
            },
            static::CONFIG_USER_PASSWORD => function (ContainerInterface $container) {
                return $container->get(EnvironmentInterface::class)->get('ADMIN_PASSWORD');
            },
            static::CONFIG_USER_RESOLVER_PASSWORD => function (ContainerInterface $container) {
                return function (string $userIdentity) use (&$container): ?string {
                    return [
                               $container->get(static::CONFIG_USER_IDENTITY) =>
                                   $container->get(static::CONFIG_USER_PASSWORD),
                           ][$userIdentity] ?? null;
                };
            },
            'admin.meta.title_prefix' => function (ContainerInterface $container) {
                return ($container->has('app.name') ? $container->get('app.name') . ' ' : '') . 'Admin | ';
            },
            'admin.meta.title' => function () {
                return 'Welcome';
            },
            'admin.meta.title_suffix' => function () {
                return '';
            },
            static::CONFIG_URI_LOGGED_IN => function () {
                return '/admin';
            },
            static::CONFIG_URI_LOGIN => function (ContainerInterface $container) {
                return '/' . trim($container->get(static::CONFIG_URI_LOGGED_IN), '/') . '/login';
            },
            AdminRequestHandler::class => function (ContainerInterface $container) {
                return new AdminRequestHandler(
                    $container->get(ResponseFactoryInterface::class),
                    $container->get(TemplateRendererInterface::class)
                );
            },
            LoginRequestHandler::class => function (ContainerInterface $container) {
                return new LoginRequestHandler(
                    $container->get(ResponseFactoryInterface::class),
                    $container->get(TemplateRendererInterface::class)
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
                $loggedInUri = '/' . trim($container->get(static::CONFIG_URI_LOGGED_IN), '/');
                $loginUri = '/' . trim($container->get(static::CONFIG_URI_LOGIN), '/');
                $adminAuthenticationMiddlewareExtension = function (
                    MiddlewareDispatcherInterface $dispatcher,
                    ContainerInterface $container
                ) use (&$loggedInUri, &$loginUri): void {
                    $dispatcher->add($container->get(AuthenticationMiddleware::class)
                        ->withPasswordResolver($container->get(static::CONFIG_USER_RESOLVER_PASSWORD))
                        ->withLoggedInUri($loggedInUri)
                        ->withLoginUri($loginUri)
                    );
                };
                $routeCollector->get($loginUri, LoginRequestHandler::class)
                    ->middleware($adminAuthenticationMiddlewareExtension);
                $routeCollector->post($loginUri, LoginRequestHandler::class)
                    ->middleware($adminAuthenticationMiddlewareExtension);
                $routeCollector->get($loggedInUri . '{adminRelativePath:.*}', AdminRequestHandler::class)
                    ->middleware($adminAuthenticationMiddlewareExtension);
            },
            TemplateRendererInterface::class => function (
                ContainerInterface $container,
                TemplateRendererInterface $renderer
            ) {
                $renderer->addPath(__DIR__ . '/resources/templates', 'admin');
                $adminData = [
                    'metaTitlePrefix' => $container->get('admin.meta.title_prefix'),
                    'metaTitle' => $container->get('admin.meta.title'),
                    'metaTitleSuffix' => $container->get('admin.meta.title_suffix'),
                ];
                $renderer->addTemplateData('admin::index', $adminData);
                $renderer->addTemplateData('admin::login', $adminData);
            },
        ];
    }
}
