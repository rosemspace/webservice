<?php

namespace Rosem\Authentication;

use Psr\Container\ContainerInterface;
use PSR7Sessions\Storageless\Http\SessionMiddleware;
use Psrnext\Container\ServiceProviderInterface;
use Psrnext\Http\Factory\ResponseFactoryInterface;
use Psrnext\Http\Server\MiddlewareQueueInterface;
use Psrnext\Template\TemplateRendererInterface;
use Rosem\Authentication\Http\Server\AuthenticationMiddleware;
use Rosem\Http\Server\LazyFactoryMiddleware;

class AuthenticationProvider implements ServiceProviderInterface
{
    public const CONFIG_SYMMETRIC_KEY = 'auth.symmetricKey';

    public const CONFIG_USER_PASSWORD_GETTER = 'auth.userPasswordGetter';

    public const CONFIG_URI = 'auth.uri';

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
//                return null;
                return 'mBC5v1sOKVvbdEitdSBenu59nfNfhwkedkJVNabosTw=';
            },
            static::CONFIG_USER_PASSWORD_GETTER => function () {
//                return null;
                return function (string $username): ?string {
                    return ['roshe' => '1234'][$username] ?? null;
                };
            },
            static::CONFIG_URI => function () {
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
            AuthenticationMiddleware::class => function (ContainerInterface $container) {
                return new LazyFactoryMiddleware($container, [static::class, 'createAuthenticationMiddleware']);
            },
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
                MiddlewareQueueInterface $middlewareDispatcher
            ) {
                $middlewareDispatcher->use($container->get(SessionMiddleware::class));
            },
        ];
    }

    public function createAuthenticationMiddleware(ContainerInterface $container): AuthenticationMiddleware
    {
        return new AuthenticationMiddleware(
            $container->get(ResponseFactoryInterface::class),
            $container->get(static::CONFIG_USER_PASSWORD_GETTER),
            $container->get(static::CONFIG_URI)
        );
    }
}
