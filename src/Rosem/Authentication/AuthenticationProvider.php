<?php

namespace Rosem\Authentication;

use Psr\Container\ContainerInterface;
use PSR7Sessions\Storageless\Http\SessionMiddleware;
use Rosem\Psr\Container\ServiceProviderInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Rosem\Psr\Template\TemplateRendererInterface;
use Rosem\Authentication\Http\Server\AuthenticationMiddleware;
use Rosem\Psr\Http\Server\MiddlewareQueueInterface;

class AuthenticationProvider implements ServiceProviderInterface
{
    public const CONFIG_SYMMETRIC_KEY = 'auth.symmetricKey';

    public const CONFIG_USER_RESOLVER_PASSWORD = 'auth.user.resolver.password';

    public const CONFIG_USER_RESOLVER_ROLES = 'auth.user.resolver.roles';

    public const CONFIG_USER_RESOLVER_DETAILS = 'auth.user.resolver.details';

    public const CONFIG_URI_LOGIN = 'auth.uri.login';

    /**
     * Returns a list of all container entries registered by this service provider.
     * - the key is the entry name
     * - the value is a callable that will return the entry, aka the **factory**
     * Factories have the following signature:
     *        function(\Psr\Container\ContainerInterface $container)
     * @return callable[]
     */
    public function getFactories(): array
    {
        return [
            static::CONFIG_SYMMETRIC_KEY => function () {
                return 'mBC5v1sOKVvbdEitdSBenu59nfNfhwkedkJVNabosTw=';
            },
            static::CONFIG_USER_RESOLVER_PASSWORD => function () {
                return function (string $username): ?string {
                    return ['roshe' => '1234'][$username] ?? null;
                };
            },
            static::CONFIG_USER_RESOLVER_ROLES => function () {
                return function (string $username) {
                    return ['admin'];
                };
            },
            static::CONFIG_USER_RESOLVER_DETAILS => function () {
                return function (string $username) {
                    return ['username' => $username];
                };
            },
            static::CONFIG_URI_LOGIN => function () {
                return '/login';
            },
            SessionMiddleware::class => function (ContainerInterface $container) {
//                return SessionMiddleware::fromSymmetricKeyDefaults(
//                    $container->get(static::CONFIG_SYMMETRIC_KEY),
//                    20 * 60 // 20 minutes
//                );

                $symmetricKey = $container->get(static::CONFIG_SYMMETRIC_KEY);

                return new SessionMiddleware(
                    new \Lcobucci\JWT\Signer\Hmac\Sha256(),
                    $symmetricKey,
                    $symmetricKey,
                    \Dflydev\FigCookies\SetCookie::create('session')
                        ->withSecure(PHP_SAPI !== 'cli-server')
                        ->withHttpOnly(true)
                        ->withPath('/'),
                    new \Lcobucci\JWT\Parser(),
                    20 * 60, // 20 minutes,
                    new \Lcobucci\Clock\SystemClock()
                );
            },
            AuthenticationMiddleware::class => [static::class, 'createAuthenticationMiddleware'],
        ];
    }

    /**
     * Returns a list of all container entries extended by this service provider.
     * - the key is the entry name
     * - the value is a callable that will return the modified entry
     * Callables have the following signature:
     *        function(Psr\Container\ContainerInterface $container, $previous)
     *     or function(Psr\Container\ContainerInterface $container, $previous = null)
     * About factories parameters:
     * - the container (instance of `Psr\Container\ContainerInterface`)
     * - the entry to be extended. If the entry to be extended does not exist and the parameter is nullable, `null`
     * will be passed.
     * @return callable[]
     */
    public function getExtensions(): array
    {
        return [
            TemplateRendererInterface::class => function (
                ContainerInterface $container,
                TemplateRendererInterface $renderer
            ) {
                $renderer->addGlobalData([
                    'csrfToken' => '4sWPhTlJAmt1IcyNq1FCyivsAVhHqjiDCKRXOgOQock=',
                ]);
            },
            MiddlewareQueueInterface::class => function (
                ContainerInterface $container,
                MiddlewareQueueInterface $middlewareQueue
            ) {
                $middlewareQueue->add(SessionMiddleware::class);
            },
        ];
    }

    public function createAuthenticationMiddleware(ContainerInterface $container): AuthenticationMiddleware
    {
        return new AuthenticationMiddleware(
            $container->get(ResponseFactoryInterface::class),
            $container->get(static::CONFIG_USER_RESOLVER_PASSWORD),
            $container->get(static::CONFIG_USER_RESOLVER_ROLES),
            $container->get(static::CONFIG_USER_RESOLVER_DETAILS),
            $container->get(static::CONFIG_URI_LOGIN)
        );
    }
}
