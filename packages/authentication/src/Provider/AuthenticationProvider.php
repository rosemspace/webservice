<?php

declare(strict_types=1);

namespace Rosem\Component\Authentication\Provider;

use Dflydev\FigCookies\Modifier\SameSite;
use Dflydev\FigCookies\SetCookie;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use PSR7Sessions\Storageless\Http\SessionMiddleware;
use Rosem\Component\Authentication\Middleware\AuthenticationMiddleware;
use Rosem\Contract\Authentication\UserFactoryInterface;
use Rosem\Contract\Container\ServiceProviderInterface;
use Rosem\Contract\Hash\HasherInterface;
use Rosem\Contract\Http\Server\GroupMiddlewareInterface;
use Rosem\Contract\Template\TemplateRendererInterface;

class AuthenticationProvider implements ServiceProviderInterface
{
    public const CONFIG_SYMMETRIC_KEY = 'auth.symmetricKey';

    public const CONFIG_USER_PASSWORD_RESOLVER = 'auth.user.passwordResolver';

    public const CONFIG_PARAMETER_IDENTITY = 'auth.parameter.identity';

    public const CONFIG_PARAMETER_PASSWORD = 'auth.parameter.password';

    public const CONFIG_URI_LOGIN = 'auth.uri.login';

    public const CONFIG_URI_LOGGED_IN = 'auth.uri.loggedIn';

    public function getFactories(): array
    {
        return [
            static::CONFIG_SYMMETRIC_KEY => static fn () => null,
            static::CONFIG_USER_PASSWORD_RESOLVER => static function (): callable {
                return static function (string $username): ?string {
                    return ['admin' => 'admin'][$username] ?? null;
                };
            },
            static::CONFIG_PARAMETER_IDENTITY => static fn (): string => 'username',
            static::CONFIG_PARAMETER_PASSWORD => static fn (): string => 'password',
            static::CONFIG_URI_LOGIN => static fn (): string => '/login',
            static::CONFIG_URI_LOGGED_IN => static fn (): string => '/',
            SessionMiddleware::class => [static::class, 'createSessionMiddleware'],
            AuthenticationMiddleware::class => [static::class, 'createAuthenticationMiddleware'],
        ];
    }

    public function getExtensions(): array
    {
        return [
            TemplateRendererInterface::class => static function (
                ContainerInterface $container,
                TemplateRendererInterface $renderer
            ): void {
                $renderer->addGlobalData([
                    'csrfToken' => '4sWPhTlJAmt1IcyNq1FCyivsAVhHqjiDCKRXOgOQock=',
                ]);
            },
            GroupMiddlewareInterface::class => static function (
                ContainerInterface $container,
                GroupMiddlewareInterface $middlewareCollector
            ): void {
                $middlewareCollector->addMiddleware($container->get(SessionMiddleware::class));
            },
        ];
    }

    public function createSessionMiddleware(ContainerInterface $container): SessionMiddleware
    {
        // TODO: add validation for symmetric key
        $symmetricKey = $container->get(static::CONFIG_SYMMETRIC_KEY);
        $sessionCookieName = 'session';
        // 20 minutes
        // TODO env - ADMIN_SESSION_LIFETIME
        $sessionCookieExpirationTime = 20 * 60;
        $secureCookie = false;

        if (PHP_SAPI !== 'cli-server') {
            $sessionCookieName = "__Secure-${sessionCookieName}";
            $secureCookie = true;
        }

        return new SessionMiddleware(
            new Sha256(),
            $symmetricKey,
            $symmetricKey,
            SetCookie::create($sessionCookieName)
                ->withSecure($secureCookie)
                ->withHttpOnly(true)
                ->withSameSite(SameSite::lax())
                // todo: use config
                ->withPath('/admin'),
            new Parser(),
            $sessionCookieExpirationTime,
            new SystemClock()
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
