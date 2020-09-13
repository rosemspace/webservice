If you want to use several implementations of a single interface, for example logging you have to create aggregate implementation based on SplStack


## Files and directories naming conventions

- filename - C:\\Users\\file.php
- filepath (dirname) - C:\\Users
- directory - Users
- file - file.php

To add to a route a request handler with middlewares:

```php
<?php
namespace CustomModule;

use Psr\Container\ContainerInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Fig\Http\Message\RequestMethodInterface as RequestMethod;
use Rosem\Contract\Route\HttpRouteCollectorInterface;
use Rosem\Component\Http\Server\DefferedRequestHandler;
use Rosem\Component\Http\Server\GroupMiddleware;

class CustomServiceProvider implements \Rosem\Contract\Container\ServiceProviderInterface
{
    public function getExtensions(): array
    {
        return [
            HttpRouteCollectorInterface::class => static function (
                ContainerInterface $container,
                HttpRouteCollectorInterface $routeCollector
            ): void {
                $routeCollector->addRoute(
                    [RequestMethod::METHOD_GET, RequestMethod::METHOD_POST],
                    'custom/uri',
                    static fn(): RequestHandlerInterface =>
                        (new GroupMiddleware($container->get(CustomRequestHandler::class)))
                            ->addMiddleware($container->get(CustomeMiddleware1::class))
                            ->addMiddleware($container->get(CustomeMiddleware2::class))
                );
            }
        ];
    }
}
```

```php
$httpRouteCollection->addRoute(
    RequestMethod::GET | RequestMethod::POST,
    '/admin',
    RequestHandler::withMiddleware(
        Middleware::group(
            Middleware::defer($container, ClientSessionMiddleware::class)
                ->then(fn($session) => $session->withCookie('backend')),
            new AuthenticationMiddleware(),
        ),
        RequestHandler::defer($container, LoginRequestHandler::class)
    )
);
```
