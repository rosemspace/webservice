<?php

namespace Rosem\Component\Authentication\Provider;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use PSR7Sessions\Storageless\Http\SessionMiddleware;
use Rosem\Component\Authentication\Middleware\AuthenticationMiddleware;
use Rosem\Contract\Authentication\UserFactoryInterface;
use Rosem\Contract\Container\ServiceProviderInterface;
use Rosem\Contract\Hash\HasherInterface;
use Rosem\Contract\Http\Server\MiddlewareCollectorInterface;
use Rosem\Contract\Template\TemplateRendererInterface;

class AuthenticationProvider implements ServiceProviderInterface
{
    public const CONFIG_SYMMETRIC_KEY = 'auth.symmetricKey';

    public const CONFIG_USER_PASSWORD_RESOLVER = 'auth.user.passwordResolver';

    public const CONFIG_PARAMETER_IDENTITY = 'auth.parameter.identity';

    public const CONFIG_PARAMETER_PASSWORD = 'auth.parameter.password';

    public const CONFIG_URI_LOGIN = 'auth.uri.login';

    public const CONFIG_URI_LOGGED_IN = 'auth.uri.loggedIn';

    /**
     * @inheritdoc
     */
    public function getFactories(): array
    {
        return [
            static::CONFIG_SYMMETRIC_KEY => static fn() => null,
            static::CONFIG_USER_PASSWORD_RESOLVER => static function (): callable {
                return static function (string $username): ?string {
                    return ['admin' => 'admin'][$username] ?? null;
                };
            },
            static::CONFIG_PARAMETER_IDENTITY => static fn(): string => 'username',
            static::CONFIG_PARAMETER_PASSWORD => static fn(): string => 'password',
            static::CONFIG_URI_LOGIN => static fn(): string => '/login',
            static::CONFIG_URI_LOGGED_IN => static fn(): string => '/',
            SessionMiddleware::class => [static::class, 'createSessionMiddleware'],
            AuthenticationMiddleware::class => [static::class, 'createAuthenticationMiddleware'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function getExtensions(): array
    {
        return [
            TemplateRendererInterface::class => static function (
                ContainerInterface $container,
                TemplateRendererInterface $renderer
            ) {
                $renderer->addGlobalData(
                    [
                        'csrfToken' => '4sWPhTlJAmt1IcyNq1FCyivsAVhHqjiDCKRXOgOQock=',
                    ]
                );
            },
            MiddlewareCollectorInterface::class => static function (
                ContainerInterface $container,
                MiddlewareCollectorInterface $middlewareCollector
            ) {
                $middlewareCollector->addDeferredMiddleware(SessionMiddleware::class);
            },
        ];
    }

    public function createSessionMiddleware(ContainerInterface $container): SessionMiddleware
    {
//        return SessionMiddleware::fromSymmetricKeyDefaults(
//            $container->get(static::CONFIG_SYMMETRIC_KEY),
//            20 * 60 // 20 minutes
//        );

        // TODO: add validation for symmetric key
        $symmetricKey = $container->get(static::CONFIG_SYMMETRIC_KEY);

        return new SessionMiddleware(
            new \Lcobucci\JWT\Signer\Hmac\Sha256(),
            $symmetricKey,
            $symmetricKey,
            \Dflydev\FigCookies\SetCookie::create('session')
                ->withSecure(PHP_SAPI !== 'cli-server')
                ->withHttpOnly(true)
                ->withPath('/admin'), // todo: use config
            new \Lcobucci\JWT\Parser(),
            20 * 60, // 20 minutes,
            new \Lcobucci\Clock\SystemClock()
        );
    }

    public function createAuthenticationMiddleware(ContainerInterface $container): AuthenticationMiddleware
    {
        return new AuthenticationMiddleware(
            $container->get(ResponseFactoryInterface::class),
            $container->get(UserFactoryInterface::class),
            $container->get(HasherInterface::class),
            $container->get(static::CONFIG_USER_PASSWORD_RESOLVER),
            $container->get(static::CONFIG_PARAMETER_IDENTITY),
            $container->get(static::CONFIG_PARAMETER_PASSWORD),
            $container->get(static::CONFIG_URI_LOGIN),
            $container->get(static::CONFIG_URI_LOGGED_IN)
        );
    }
}
